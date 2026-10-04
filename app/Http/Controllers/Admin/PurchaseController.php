<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    /**
     * Display a listing of purchases.
     */
    public function index(Request $request)
    {
        $query = Purchase::with(['vendor', 'broker', 'items.item']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('purchase_no', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Vendor Filter
        if ($vendorId = $request->input('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        // Date Range Filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('invoice_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('invoice_date', '<=', $endDate);
        }

        // Order
        $purchases = $query->orderBy('invoice_date', 'desc')
                           ->orderBy('id', 'desc')
                           ->paginate(10)
                           ->withQueryString();

        // KPIs
        $totalPurchases = Purchase::count();
        $completedCount = Purchase::whereIn('status', ['received', 'completed'])->count();
        $totalBillAmount = Purchase::where('status', '!=', 'cancelled')->sum('bill_total');
        $totalUBAmount = Purchase::where('status', '!=', 'cancelled')->sum('under_billing_total');
        $totalGrandAmount = Purchase::where('status', '!=', 'cancelled')->sum('grand_total');

        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.purchase.index', compact(
            'purchases',
            'totalPurchases',
            'completedCount',
            'totalBillAmount',
            'totalUBAmount',
            'totalGrandAmount',
            'vendors'
        ));
    }

    /**
     * Show the form for creating a new purchase entry.
     */
    public function create()
    {
        $nextPurchaseNo = Purchase::generateNextPurchaseNo();
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.purchase.create', compact(
            'nextPurchaseNo',
            'vendors',
            'brokers',
            'items'
        ));
    }

    /**
     * Store a newly created purchase entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_no' => 'required|string|max:30|unique:purchases,purchase_no',
            'invoice_no' => 'nullable|string|max:50',
            'invoice_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'vehicle_no' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string|in:unpaid,partial,paid',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'nullable|string|max:50',
            'items.*.hsn_code' => 'nullable|string|max:20',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.actual_rate' => 'required|numeric|min:0',
            'items.*.bill_rate' => 'required|numeric|min:0',
            'items.*.ub_rate' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $company = Company::first();

            $subtotal = 0;
            $taxAmount = 0;
            $underBillingTotal = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $billRate = (float) $itemRow['bill_rate'];
                $actualRate = (float) $itemRow['actual_rate'];
                $ubRate = isset($itemRow['ub_rate']) ? (float) $itemRow['ub_rate'] : max(0, $actualRate - $billRate);
                $gstPercent = isset($itemRow['gst_percent']) ? (float) $itemRow['gst_percent'] : 5.0;

                $lineSub = $qty * $billRate;
                $lineTax = $lineSub * ($gstPercent / 100);
                $lineUB = $qty * $ubRate;
                $lineBillAmt = $lineSub + $lineTax;
                $lineTotal = $lineBillAmt + $lineUB;

                $subtotal += $lineSub;
                $taxAmount += $lineTax;
                $underBillingTotal += $lineUB;

                $preparedItems[] = [
                    'item_id' => $itemRow['item_id'],
                    'batch_no' => $itemRow['batch_no'] ?? null,
                    'hsn_code' => $itemRow['hsn_code'] ?? null,
                    'unit' => $itemRow['unit'] ?? 'KG',
                    'quantity' => $qty,
                    'actual_rate' => $actualRate,
                    'bill_rate' => $billRate,
                    'ub_rate' => $ubRate,
                    'gst_percent' => $gstPercent,
                    'tax_amount' => $lineTax,
                    'bill_amount' => $lineBillAmt,
                    'under_amount' => $lineUB,
                    'total_amount' => $lineTotal,
                ];
            }

            $billTotal = $subtotal + $taxAmount;
            $grandTotal = $billTotal + $underBillingTotal;

            $purchase = Purchase::create([
                'purchase_no' => $validated['purchase_no'],
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'vendor_id' => $validated['vendor_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? '30 Days',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'bill_total' => $billTotal,
                'under_billing_total' => $underBillingTotal,
                'round_off' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => $validated['paid_amount'] ?? 0.00,
                'payment_status' => $validated['payment_status'] ?? 'unpaid',
                'company_id' => $company ? $company->id : null,
                'status' => 'received', // Auto-default to received
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $pItem) {
                $pItem['purchase_id'] = $purchase->id;
                PurchaseItem::create($pItem);

                // Increase Item stock
                $item = Item::find($pItem['item_id']);
                if ($item) {
                    $item->increment('current_stock', $pItem['quantity']);
                }
            }

            // Update Vendor Balance (Outstanding)
            $vendor = Vendor::find($validated['vendor_id']);
            if ($vendor) {
                $vendor->increment('current_balance', $grandTotal);
            }

            DB::commit();

            return redirect()->route('admin.transactions.purchase-entry')
                ->with('success', "Purchase Entry #{$purchase->purchase_no} created successfully and inventory stock updated.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error saving purchase entry: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified purchase entry.
     */
    public function show(Purchase $purchase)
    {
        $purchase->load(['vendor', 'broker', 'company', 'items.item']);
        return view('admin.transactions.purchase.show', compact('purchase'));
    }

    /**
     * Show the form for editing the specified purchase entry.
     */
    public function edit(Purchase $purchase)
    {
        $purchase->load(['vendor', 'broker', 'items.item']);
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.purchase.edit', compact(
            'purchase',
            'vendors',
            'brokers',
            'items'
        ));
    }

    /**
     * Update the specified purchase entry in storage.
     */
    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'invoice_no' => 'nullable|string|max:50',
            'invoice_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'vehicle_no' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string|in:unpaid,partial,paid',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'nullable|string|max:50',
            'items.*.hsn_code' => 'nullable|string|max:20',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.actual_rate' => 'required|numeric|min:0',
            'items.*.bill_rate' => 'required|numeric|min:0',
            'items.*.ub_rate' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Revert previous stock and vendor balance
            foreach ($purchase->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $item->decrement('current_stock', $oldItem->quantity);
                }
            }
            if ($oldVendor = Vendor::find($purchase->vendor_id)) {
                $oldVendor->decrement('current_balance', $purchase->grand_total);
            }

            $subtotal = 0;
            $taxAmount = 0;
            $underBillingTotal = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $billRate = (float) $itemRow['bill_rate'];
                $actualRate = (float) $itemRow['actual_rate'];
                $ubRate = isset($itemRow['ub_rate']) ? (float) $itemRow['ub_rate'] : max(0, $actualRate - $billRate);
                $gstPercent = isset($itemRow['gst_percent']) ? (float) $itemRow['gst_percent'] : 5.0;

                $lineSub = $qty * $billRate;
                $lineTax = $lineSub * ($gstPercent / 100);
                $lineUB = $qty * $ubRate;
                $lineBillAmt = $lineSub + $lineTax;
                $lineTotal = $lineBillAmt + $lineUB;

                $subtotal += $lineSub;
                $taxAmount += $lineTax;
                $underBillingTotal += $lineUB;

                $preparedItems[] = [
                    'item_id' => $itemRow['item_id'],
                    'batch_no' => $itemRow['batch_no'] ?? null,
                    'hsn_code' => $itemRow['hsn_code'] ?? null,
                    'unit' => $itemRow['unit'] ?? 'KG',
                    'quantity' => $qty,
                    'actual_rate' => $actualRate,
                    'bill_rate' => $billRate,
                    'ub_rate' => $ubRate,
                    'gst_percent' => $gstPercent,
                    'tax_amount' => $lineTax,
                    'bill_amount' => $lineBillAmt,
                    'under_amount' => $lineUB,
                    'total_amount' => $lineTotal,
                ];
            }

            $billTotal = $subtotal + $taxAmount;
            $grandTotal = $billTotal + $underBillingTotal;

            $purchase->update([
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_date' => $validated['invoice_date'],
                'vendor_id' => $validated['vendor_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? '30 Days',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'bill_total' => $billTotal,
                'under_billing_total' => $underBillingTotal,
                'round_off' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => $validated['paid_amount'] ?? $purchase->paid_amount,
                'payment_status' => $validated['payment_status'] ?? $purchase->payment_status,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Replace line items
            $purchase->items()->delete();
            foreach ($preparedItems as $pItem) {
                $pItem['purchase_id'] = $purchase->id;
                PurchaseItem::create($pItem);

                // Increment updated stock
                $item = Item::find($pItem['item_id']);
                if ($item) {
                    $item->increment('current_stock', $pItem['quantity']);
                }
            }

            // Apply to new or current vendor
            $vendor = Vendor::find($validated['vendor_id']);
            if ($vendor) {
                $vendor->increment('current_balance', $grandTotal);
            }

            DB::commit();

            return redirect()->route('admin.transactions.purchase-entry')
                ->with('success', "Purchase Entry #{$purchase->purchase_no} updated successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating purchase entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified purchase entry from storage.
     */
    public function destroy(Purchase $purchase)
    {
        DB::beginTransaction();
        try {
            // Revert stock & vendor balance
            foreach ($purchase->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $item->decrement('current_stock', $oldItem->quantity);
                }
            }
            if ($vendor = Vendor::find($purchase->vendor_id)) {
                $vendor->decrement('current_balance', $purchase->grand_total);
            }

            $purchase->items()->delete();
            $purchase->delete();

            DB::commit();

            return redirect()->route('admin.transactions.purchase-entry')
                ->with('success', "Purchase Entry #{$purchase->purchase_no} deleted successfully and stock reverted.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting purchase entry: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status between received and completed.
     */
    public function toggleStatus(Purchase $purchase)
    {
        $newStatus = $purchase->status === 'completed' ? 'received' : 'completed';
        $purchase->update(['status' => $newStatus]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "Purchase status updated to " . ucfirst($newStatus)
            ]);
        }

        return back()->with('success', "Purchase status updated to " . ucfirst($newStatus));
    }

    /**
     * Generate next purchase code JSON.
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Purchase::generateNextPurchaseNo()
        ]);
    }
}
