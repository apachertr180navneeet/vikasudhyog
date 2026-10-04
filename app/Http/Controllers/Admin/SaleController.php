<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SaleController extends Controller
{
    /**
     * Display a listing of sales.
     */
    public function index(Request $request)
    {
        $query = Sale::with(['customer', 'broker', 'items.item']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('sale_no', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Customer Filter
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Date Range Filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('sale_date', '<=', $endDate);
        }

        // Bill Type Filter
        if ($billType = $request->input('bill_type')) {
            $query->where('bill_type', $billType);
        }

        // Order
        $sales = $query->orderBy('sale_date', 'desc')
                       ->orderBy('id', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        // KPIs
        $totalSales = Sale::count();
        $completedCount = Sale::whereIn('status', ['dispatched', 'completed'])->count();
        $totalBillAmount = Sale::where('status', '!=', 'cancelled')->sum('bill_total');
        $totalUBAmount = Sale::where('status', '!=', 'cancelled')->sum('under_billing_total');
        $totalGrandAmount = Sale::where('status', '!=', 'cancelled')->sum('grand_total');
        $totalPaidAmount = Sale::where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalPendingAmount = max(0, (float)$totalGrandAmount - (float)$totalPaidAmount);

        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.sales.index', compact(
            'sales',
            'totalSales',
            'completedCount',
            'totalBillAmount',
            'totalUBAmount',
            'totalGrandAmount',
            'totalPaidAmount',
            'totalPendingAmount',
            'customers'
        ));
    }

    /**
     * Show the form for creating a new sales entry.
     */
    public function create()
    {
        $nextSaleNo = Sale::generateNextSaleNo();
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::with('unitRelation')->where('status', 'active')->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('admin.transactions.sales.create', compact(
            'nextSaleNo',
            'customers',
            'brokers',
            'items',
            'units'
        ));
    }

    /**
     * Store a newly created sales entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sale_no' => 'required|string|max:30|unique:sales,sale_no',
            'bill_type' => 'nullable|string|in:with_bill,without_bill',
            'invoice_no' => 'nullable|string|max:50',
            'sale_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
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
            'items.*.actual_rate' => 'nullable|numeric|min:0',
            'items.*.bill_rate' => 'required|numeric|min:0',
            'items.*.ub_rate' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        // CRITICAL: Stock Check before initiating transaction
        $requiredQuantities = [];
        foreach ($validated['items'] as $itemRow) {
            $itemId = $itemRow['item_id'];
            $qty = (float) $itemRow['quantity'];
            $requiredQuantities[$itemId] = ($requiredQuantities[$itemId] ?? 0) + $qty;
        }

        foreach ($requiredQuantities as $itemId => $reqQty) {
            $item = Item::find($itemId);
            if (!$item) {
                return back()->withInput()->with('error', "Item not found in master catalog.");
            }
            if ((float)$item->current_stock < $reqQty) {
                return back()->withInput()->with(
                    'error',
                    "Insufficient inward stock for '{$item->name}' ({$item->code})! Available stock: " . number_format($item->current_stock, 2) . " {$item->unit}, Requested outward: " . number_format($reqQty, 2) . " {$item->unit}. You cannot sell more than available inward stock."
                );
            }
        }

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
                $ubRate = isset($itemRow['ub_rate']) && $itemRow['ub_rate'] !== null && $itemRow['ub_rate'] !== '' ? (float) $itemRow['ub_rate'] : 0.0;
                $actualRate = isset($itemRow['actual_rate']) && $itemRow['actual_rate'] !== null && $itemRow['actual_rate'] !== '' ? (float) $itemRow['actual_rate'] : ($billRate + $ubRate);
                $gstPercent = isset($itemRow['gst_percent']) && $itemRow['gst_percent'] !== null && $itemRow['gst_percent'] !== '' ? (float) $itemRow['gst_percent'] : 5.0;

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

            $sale = Sale::create([
                'sale_no' => $validated['sale_no'],
                'bill_type' => $validated['bill_type'] ?? 'with_bill',
                'invoice_no' => $validated['invoice_no'] ?? null,
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
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
                'status' => 'dispatched',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $sItem) {
                $sItem['sale_id'] = $sale->id;
                SaleItem::create($sItem);

                // Deduct outward stock from inventory
                $item = Item::find($sItem['item_id']);
                if ($item) {
                    $item->decrement('current_stock', $sItem['quantity']);
                }
            }

            // Update Customer Balance (Receivable increased by Grand Total)
            $customer = Customer::find($validated['customer_id']);
            if ($customer) {
                $customer->increment('current_balance', $grandTotal);
            }

            DB::commit();

            return redirect()->route('admin.transactions.sales-entry')
                ->with('success', "Sales Entry #{$sale->sale_no} created successfully and outward stock deducted from inventory.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating sales entry: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified sale entry.
     */
    public function show(Sale $sale)
    {
        $sale->load(['customer', 'broker', 'company', 'items.item.unitRelation']);
        return view('admin.transactions.sales.show', compact('sale'));
    }

    /**
     * Show the form for editing the specified sale entry.
     */
    public function edit(Sale $sale)
    {
        $sale->load(['customer', 'broker', 'items.item.unitRelation']);
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::with('unitRelation')->where('status', 'active')->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('admin.transactions.sales.edit', compact(
            'sale',
            'customers',
            'brokers',
            'items',
            'units'
        ));
    }

    /**
     * Update the specified sale entry in storage.
     */
    public function update(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'bill_type' => 'nullable|string|in:with_bill,without_bill',
            'invoice_no' => 'nullable|string|max:50',
            'sale_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
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
            'items.*.actual_rate' => 'nullable|numeric|min:0',
            'items.*.bill_rate' => 'required|numeric|min:0',
            'items.*.ub_rate' => 'nullable|numeric|min:0',
            'items.*.gst_percent' => 'nullable|numeric|min:0',
        ]);

        // CRITICAL: Stock Check taking into account previously deducted stock for this sale
        $oldItemsMap = [];
        foreach ($sale->items as $oldItem) {
            $oldItemsMap[$oldItem->item_id] = ($oldItemsMap[$oldItem->item_id] ?? 0) + (float)$oldItem->quantity;
        }

        $newStockNeeds = [];
        foreach ($validated['items'] as $itemRow) {
            $itemId = $itemRow['item_id'];
            $qty = (float) $itemRow['quantity'];
            $newStockNeeds[$itemId] = ($newStockNeeds[$itemId] ?? 0) + $qty;
        }

        foreach ($newStockNeeds as $itemId => $reqQty) {
            $item = Item::find($itemId);
            if (!$item) {
                return back()->withInput()->with('error', "Item not found in master catalog.");
            }
            $previouslyDeducted = $oldItemsMap[$itemId] ?? 0;
            $availableStockWithRevert = (float)$item->current_stock + $previouslyDeducted;

            if ($availableStockWithRevert < $reqQty) {
                return back()->withInput()->with(
                    'error',
                    "Insufficient inward stock for '{$item->name}' ({$item->code})! Available stock: " . number_format($availableStockWithRevert, 2) . " {$item->unit}, Requested outward: " . number_format($reqQty, 2) . " {$item->unit}."
                );
            }
        }

        DB::beginTransaction();
        try {
            // Revert previously deducted stock and customer balance
            foreach ($sale->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $item->increment('current_stock', $oldItem->quantity);
                }
            }
            if ($oldCustomer = Customer::find($sale->customer_id)) {
                $oldCustomer->decrement('current_balance', $sale->grand_total);
            }

            $subtotal = 0;
            $taxAmount = 0;
            $underBillingTotal = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $billRate = (float) $itemRow['bill_rate'];
                $ubRate = isset($itemRow['ub_rate']) && $itemRow['ub_rate'] !== null && $itemRow['ub_rate'] !== '' ? (float) $itemRow['ub_rate'] : 0.0;
                $actualRate = isset($itemRow['actual_rate']) && $itemRow['actual_rate'] !== null && $itemRow['actual_rate'] !== '' ? (float) $itemRow['actual_rate'] : ($billRate + $ubRate);
                $gstPercent = isset($itemRow['gst_percent']) && $itemRow['gst_percent'] !== null && $itemRow['gst_percent'] !== '' ? (float) $itemRow['gst_percent'] : 5.0;

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

            $sale->update([
                'bill_type' => $validated['bill_type'] ?? $sale->bill_type ?? 'with_bill',
                'invoice_no' => $validated['invoice_no'] ?? null,
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
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
                'paid_amount' => $validated['paid_amount'] ?? $sale->paid_amount,
                'payment_status' => $validated['payment_status'] ?? $sale->payment_status,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Replace line items
            $sale->items()->delete();
            foreach ($preparedItems as $sItem) {
                $sItem['sale_id'] = $sale->id;
                SaleItem::create($sItem);

                // Deduct updated outward stock
                $item = Item::find($sItem['item_id']);
                if ($item) {
                    $item->decrement('current_stock', $sItem['quantity']);
                }
            }

            // Apply new Customer balance
            $newCustomer = Customer::find($validated['customer_id']);
            if ($newCustomer) {
                $newCustomer->increment('current_balance', $grandTotal);
            }

            DB::commit();

            return redirect()->route('admin.transactions.sales-entry')
                ->with('success', "Sales Entry #{$sale->sale_no} updated successfully and inventory stock adjusted.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating sales entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified sale entry from storage.
     */
    public function destroy(Sale $sale)
    {
        DB::beginTransaction();
        try {
            // Restore inventory stock
            if ($sale->status !== 'cancelled') {
                foreach ($sale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->increment('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($sale->customer_id)) {
                    $customer->decrement('current_balance', $sale->grand_total);
                }
            }

            $sale->items()->delete();
            $sale->delete();

            DB::commit();

            return redirect()->route('admin.transactions.sales-entry')
                ->with('success', "Sales Entry #{$sale->sale_no} deleted successfully and outward stock restored to inventory.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting sales entry: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of a sales entry.
     */
    public function toggleStatus(Sale $sale)
    {
        DB::beginTransaction();
        try {
            if ($sale->status === 'cancelled') {
                // Uncancelling / Redispatching: Verify stock first!
                foreach ($sale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if (!$item || (float)$item->current_stock < (float)$sItem->quantity) {
                        return back()->with(
                            'error',
                            "Cannot re-dispatch: Insufficient stock for '{$item->name}'. Available: {$item->current_stock} {$item->unit}, Needed: {$sItem->quantity} {$item->unit}."
                        );
                    }
                }

                // Deduct outward stock again
                foreach ($sale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->decrement('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($sale->customer_id)) {
                    $customer->increment('current_balance', $sale->grand_total);
                }
                $sale->update(['status' => 'dispatched']);
                $msg = "Sales entry #{$sale->sale_no} re-dispatched and outward stock deducted.";
            } else {
                // Cancelling: Restore outward stock back to inventory
                foreach ($sale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->increment('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($sale->customer_id)) {
                    $customer->decrement('current_balance', $sale->grand_total);
                }
                $sale->update(['status' => 'cancelled']);
                $msg = "Sales entry #{$sale->sale_no} marked as cancelled and stock restored to inventory.";
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error toggling sales entry status: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to generate next sale number.
     */
    public function generateCode()
    {
        return response()->json([
            'code' => Sale::generateNextSaleNo()
        ]);
    }
}
