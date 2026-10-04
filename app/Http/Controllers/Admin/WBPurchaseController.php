<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\WBPurchase;
use App\Models\WBPurchaseItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WBPurchaseController extends Controller
{
    /**
     * Display a listing of WB purchases.
     */
    public function index(Request $request)
    {
        $query = WBPurchase::with(['vendor', 'broker', 'items.item']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('slip_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
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

        // Date Filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('entry_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('entry_date', '<=', $endDate);
        }

        // Paginate
        $wbPurchases = $query->orderBy('entry_date', 'desc')
                             ->orderBy('id', 'desc')
                             ->paginate(10)
                             ->withQueryString();

        // KPIs
        $totalSlips = WBPurchase::count();
        $completedCount = WBPurchase::whereIn('status', ['received', 'completed'])->count();
        $totalNetWeight = WBPurchase::where('status', '!=', 'cancelled')->sum('net_weight');
        $totalAmount = WBPurchase::where('status', '!=', 'cancelled')->sum('total_amount');

        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.wb-purchase.index', compact(
            'wbPurchases',
            'totalSlips',
            'completedCount',
            'totalNetWeight',
            'totalAmount',
            'vendors'
        ));
    }

    /**
     * Show the form for creating a new WB purchase entry.
     */
    public function create()
    {
        $nextSlipNo = WBPurchase::generateNextSlipNo();
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.wb-purchase.create', compact(
            'nextSlipNo',
            'vendors',
            'brokers',
            'items'
        ));
    }

    /**
     * Store a newly created WB purchase entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slip_no' => 'required|string|max:30|unique:wb_purchases,slip_no',
            'entry_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'vehicle_no' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:25',
            'gross_weight' => 'nullable|numeric|min:0',
            'tare_weight' => 'nullable|numeric|min:0',
            'deduction_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|string|in:unpaid,paid',
            'payment_mode' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'nullable|string|max:50',
            'items.*.hsn_code' => 'nullable|string|max:20',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $company = Company::first();

            $calcNetWeight = 0;
            $calcTotalAmount = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $rate = (float) $itemRow['rate'];
                $lineAmt = $qty * $rate;

                $calcNetWeight += $qty;
                $calcTotalAmount += $lineAmt;

                $preparedItems[] = [
                    'item_id' => $itemRow['item_id'],
                    'batch_no' => $itemRow['batch_no'] ?? null,
                    'hsn_code' => $itemRow['hsn_code'] ?? null,
                    'unit' => $itemRow['unit'] ?? 'KG',
                    'quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $lineAmt,
                    'notes' => $itemRow['notes'] ?? null,
                ];
            }

            // If manual weighbridge weights provided
            $gross = (float) ($validated['gross_weight'] ?? 0);
            $tare = (float) ($validated['tare_weight'] ?? 0);
            $deduction = (float) ($validated['deduction_weight'] ?? 0);
            $wbNet = max(0, $gross - $tare - $deduction);

            $finalNetWeight = $wbNet > 0 ? $wbNet : $calcNetWeight;

            $wbPurchase = WBPurchase::create([
                'slip_no' => $validated['slip_no'],
                'entry_date' => $validated['entry_date'],
                'vendor_id' => $validated['vendor_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'deduction_weight' => $deduction,
                'net_weight' => $finalNetWeight,
                'total_amount' => $calcTotalAmount,
                'payment_status' => $validated['payment_status'] ?? 'unpaid',
                'payment_mode' => $validated['payment_mode'] ?? 'Cash',
                'company_id' => $company ? $company->id : null,
                'status' => 'received', // Auto-default to received
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $pItem) {
                $pItem['wb_purchase_id'] = $wbPurchase->id;
                WBPurchaseItem::create($pItem);

                // Increment stock
                $item = Item::find($pItem['item_id']);
                if ($item) {
                    $item->increment('current_stock', $pItem['quantity']);
                }
            }

            // Vendor balance increment
            $vendor = Vendor::find($validated['vendor_id']);
            if ($vendor) {
                $vendor->increment('current_balance', $calcTotalAmount);
            }

            DB::commit();

            return redirect()->route('admin.transactions.wb-purchase-entry')
                ->with('success', "WB Purchase Entry Slip #{$wbPurchase->slip_no} created successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error saving WB purchase entry: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified WB purchase entry.
     */
    public function show(WBPurchase $wbPurchase)
    {
        $wbPurchase->load(['vendor', 'broker', 'company', 'items.item']);
        return view('admin.transactions.wb-purchase.show', compact('wbPurchase'));
    }

    /**
     * Show the form for editing the specified WB purchase entry.
     */
    public function edit(WBPurchase $wbPurchase)
    {
        $wbPurchase->load(['vendor', 'broker', 'items.item']);
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.wb-purchase.edit', compact(
            'wbPurchase',
            'vendors',
            'brokers',
            'items'
        ));
    }

    /**
     * Update the specified WB purchase entry in storage.
     */
    public function update(Request $request, WBPurchase $wbPurchase)
    {
        $validated = $request->validate([
            'entry_date' => 'required|date',
            'vendor_id' => 'required|exists:vendors,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'vehicle_no' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:25',
            'gross_weight' => 'nullable|numeric|min:0',
            'tare_weight' => 'nullable|numeric|min:0',
            'deduction_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|string|in:unpaid,paid',
            'payment_mode' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'nullable|string|max:50',
            'items.*.hsn_code' => 'nullable|string|max:20',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Revert stock & vendor balance
            foreach ($wbPurchase->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $item->decrement('current_stock', $oldItem->quantity);
                }
            }
            if ($oldVendor = Vendor::find($wbPurchase->vendor_id)) {
                $oldVendor->decrement('current_balance', $wbPurchase->total_amount);
            }

            $calcNetWeight = 0;
            $calcTotalAmount = 0;
            $preparedItems = [];

            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $rate = (float) $itemRow['rate'];
                $lineAmt = $qty * $rate;

                $calcNetWeight += $qty;
                $calcTotalAmount += $lineAmt;

                $preparedItems[] = [
                    'item_id' => $itemRow['item_id'],
                    'batch_no' => $itemRow['batch_no'] ?? null,
                    'hsn_code' => $itemRow['hsn_code'] ?? null,
                    'unit' => $itemRow['unit'] ?? 'KG',
                    'quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $lineAmt,
                    'notes' => $itemRow['notes'] ?? null,
                ];
            }

            $gross = (float) ($validated['gross_weight'] ?? 0);
            $tare = (float) ($validated['tare_weight'] ?? 0);
            $deduction = (float) ($validated['deduction_weight'] ?? 0);
            $wbNet = max(0, $gross - $tare - $deduction);

            $finalNetWeight = $wbNet > 0 ? $wbNet : $calcNetWeight;

            $wbPurchase->update([
                'entry_date' => $validated['entry_date'],
                'vendor_id' => $validated['vendor_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'deduction_weight' => $deduction,
                'net_weight' => $finalNetWeight,
                'total_amount' => $calcTotalAmount,
                'payment_status' => $validated['payment_status'] ?? $wbPurchase->payment_status,
                'payment_mode' => $validated['payment_mode'] ?? $wbPurchase->payment_mode,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Replace items
            $wbPurchase->items()->delete();
            foreach ($preparedItems as $pItem) {
                $pItem['wb_purchase_id'] = $wbPurchase->id;
                WBPurchaseItem::create($pItem);

                $item = Item::find($pItem['item_id']);
                if ($item) {
                    $item->increment('current_stock', $pItem['quantity']);
                }
            }

            $vendor = Vendor::find($validated['vendor_id']);
            if ($vendor) {
                $vendor->increment('current_balance', $calcTotalAmount);
            }

            DB::commit();

            return redirect()->route('admin.transactions.wb-purchase-entry')
                ->with('success', "WB Purchase Entry Slip #{$wbPurchase->slip_no} updated successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating WB purchase entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified WB purchase entry from storage.
     */
    public function destroy(WBPurchase $wbPurchase)
    {
        DB::beginTransaction();
        try {
            foreach ($wbPurchase->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $item->decrement('current_stock', $oldItem->quantity);
                }
            }
            if ($vendor = Vendor::find($wbPurchase->vendor_id)) {
                $vendor->decrement('current_balance', $wbPurchase->total_amount);
            }

            $wbPurchase->items()->delete();
            $wbPurchase->delete();

            DB::commit();

            return redirect()->route('admin.transactions.wb-purchase-entry')
                ->with('success', "WB Purchase Entry Slip #{$wbPurchase->slip_no} deleted successfully.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting WB purchase entry: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status between received and completed.
     */
    public function toggleStatus(WBPurchase $wbPurchase)
    {
        $newStatus = $wbPurchase->status === 'completed' ? 'received' : 'completed';
        $wbPurchase->update(['status' => $newStatus]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $newStatus,
                'message' => "WB Purchase status updated to " . ucfirst($newStatus)
            ]);
        }

        return back()->with('success', "WB Purchase status updated to " . ucfirst($newStatus));
    }

    /**
     * Generate next slip number JSON.
     */
    public function generateCode()
    {
        return response()->json([
            'code' => WBPurchase::generateNextSlipNo()
        ]);
    }
}
