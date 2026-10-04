<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Unit;
use App\Models\WBSale;
use App\Models\WBSaleItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WBSaleController extends Controller
{
    /**
     * Display a listing of WB sales.
     */
    public function index(Request $request)
    {
        $query = WBSale::with(['customer', 'broker', 'items.item']);

        // Search
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('slip_no', 'like', "%{$search}%")
                  ->orWhere('invoice_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
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

        // Date Filter
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('entry_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('entry_date', '<=', $endDate);
        }

        // Paginate
        $wbSales = $query->orderBy('entry_date', 'desc')
                         ->orderBy('id', 'desc')
                         ->paginate(10)
                         ->withQueryString();

        // KPIs
        $totalSlips = WBSale::count();
        $completedCount = WBSale::whereIn('status', ['dispatched', 'completed'])->count();
        $totalNetWeight = WBSale::where('status', '!=', 'cancelled')->sum('net_weight');
        $totalAmount = WBSale::where('status', '!=', 'cancelled')->sum('total_amount');
        $totalPaidAmount = WBSale::where('status', '!=', 'cancelled')->sum('paid_amount');
        $totalPendingAmount = max(0, (float)$totalAmount - (float)$totalPaidAmount);

        $customers = Customer::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.wb-sales.index', compact(
            'wbSales',
            'totalSlips',
            'completedCount',
            'totalNetWeight',
            'totalAmount',
            'totalPaidAmount',
            'totalPendingAmount',
            'customers'
        ));
    }

    /**
     * Show the form for creating a new WB sales entry.
     */
    public function create()
    {
        $nextSlipNo = WBSale::generateNextSlipNo();
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('admin.transactions.wb-sales.create', compact(
            'nextSlipNo',
            'customers',
            'brokers',
            'items',
            'units'
        ));
    }

    /**
     * Store a newly created WB sales entry in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'slip_no' => 'required|string|max:30|unique:wb_sales,slip_no',
            'bill_type' => 'nullable|string|in:with_bill,without_bill',
            'invoice_no' => 'nullable|string|max:50',
            'entry_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'vehicle_no' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:25',
            'payment_terms' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string|in:unpaid,paid',
            'payment_mode' => 'nullable|string|max:50',
            'gross_weight' => 'nullable|numeric|min:0',
            'tare_weight' => 'nullable|numeric|min:0',
            'deduction_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'nullable|string|max:50',
            'items.*.hsn_code' => 'nullable|string|max:20',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
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
                    "Insufficient inward stock for '{$item->name}' ({$item->code})! Available stock: " . number_format($item->current_stock, 2) . " {$item->unit}, Requested outward: " . number_format($reqQty, 2) . " {$item->unit}. Outward dispatch cannot exceed inward purchase stock."
                );
            }
        }

        DB::beginTransaction();
        try {
            $company = Company::first();

            // Calculate Net Weight
            $gross = (float) ($validated['gross_weight'] ?? 0);
            $tare = (float) ($validated['tare_weight'] ?? 0);
            $deduction = (float) ($validated['deduction_weight'] ?? 0);
            $netWeight = max(0, $gross - $tare - $deduction);

            // Calculate Total from line items
            $totalAmount = 0;
            $preparedItems = [];
            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $rate = (float) $itemRow['rate'];
                $amt = $qty * $rate;
                $totalAmount += $amt;

                $preparedItems[] = [
                    'item_id' => $itemRow['item_id'],
                    'batch_no' => $itemRow['batch_no'] ?? null,
                    'hsn_code' => $itemRow['hsn_code'] ?? null,
                    'unit' => $itemRow['unit'] ?? 'KG',
                    'quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $amt,
                    'notes' => $itemRow['notes'] ?? null,
                ];
            }

            $wbSale = WBSale::create([
                'slip_no' => $validated['slip_no'],
                'bill_type' => $validated['bill_type'] ?? 'without_bill',
                'invoice_no' => $validated['invoice_no'] ?? null,
                'entry_date' => $validated['entry_date'],
                'customer_id' => $validated['customer_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? '30 Days',
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'deduction_weight' => $deduction,
                'net_weight' => $netWeight,
                'total_amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'] ?? 0.00,
                'payment_status' => $validated['payment_status'] ?? 'unpaid',
                'payment_mode' => $validated['payment_mode'] ?? 'Cash',
                'company_id' => $company ? $company->id : null,
                'status' => 'dispatched',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($preparedItems as $pItem) {
                $pItem['wb_sale_id'] = $wbSale->id;
                WBSaleItem::create($pItem);

                // Deduct outward stock from inventory
                $item = Item::find($pItem['item_id']);
                if ($item) {
                    $item->decrement('current_stock', $pItem['quantity']);
                }
            }

            // Update Customer Balance
            $customer = Customer::find($validated['customer_id']);
            if ($customer) {
                $customer->increment('current_balance', $totalAmount);
            }

            DB::commit();

            return redirect()->route('admin.transactions.wb-sales-entry')
                ->with('success', "WB Sales Entry #{$wbSale->slip_no} created successfully and outward stock deducted.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating WB sales entry: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified WB sales entry.
     */
    public function show(WBSale $wbSale)
    {
        $wbSale->load(['customer', 'broker', 'company', 'items.item']);
        return view('admin.transactions.wb-sales.show', compact('wbSale'));
    }

    /**
     * Show the form for editing the specified WB sales entry.
     */
    public function edit(WBSale $wbSale)
    {
        $wbSale->load(['customer', 'broker', 'items.item']);
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('admin.transactions.wb-sales.edit', compact(
            'wbSale',
            'customers',
            'brokers',
            'items',
            'units'
        ));
    }

    /**
     * Update the specified WB sales entry in storage.
     */
    public function update(Request $request, WBSale $wbSale)
    {
        $validated = $request->validate([
            'bill_type' => 'nullable|string|in:with_bill,without_bill',
            'invoice_no' => 'nullable|string|max:50',
            'entry_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'vehicle_no' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:25',
            'payment_terms' => 'nullable|string|max:50',
            'payment_status' => 'nullable|string|in:unpaid,paid',
            'payment_mode' => 'nullable|string|max:50',
            'gross_weight' => 'nullable|numeric|min:0',
            'tare_weight' => 'nullable|numeric|min:0',
            'deduction_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.batch_no' => 'nullable|string|max:50',
            'items.*.hsn_code' => 'nullable|string|max:20',
            'items.*.unit' => 'required|string|max:20',
            'items.*.quantity' => 'required|numeric|min:0.001',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:255',
        ]);

        // Stock check considering existing items
        $oldItemsMap = [];
        foreach ($wbSale->items as $oldItem) {
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
            // Revert previous stock and customer balance
            foreach ($wbSale->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $item->increment('current_stock', $oldItem->quantity);
                }
            }
            if ($oldCustomer = Customer::find($wbSale->customer_id)) {
                $oldCustomer->decrement('current_balance', $wbSale->total_amount);
            }

            // Calculate Net Weight
            $gross = (float) ($validated['gross_weight'] ?? 0);
            $tare = (float) ($validated['tare_weight'] ?? 0);
            $deduction = (float) ($validated['deduction_weight'] ?? 0);
            $netWeight = max(0, $gross - $tare - $deduction);

            // Recalculate Total from items
            $totalAmount = 0;
            $preparedItems = [];
            foreach ($validated['items'] as $itemRow) {
                $qty = (float) $itemRow['quantity'];
                $rate = (float) $itemRow['rate'];
                $amt = $qty * $rate;
                $totalAmount += $amt;

                $preparedItems[] = [
                    'item_id' => $itemRow['item_id'],
                    'batch_no' => $itemRow['batch_no'] ?? null,
                    'hsn_code' => $itemRow['hsn_code'] ?? null,
                    'unit' => $itemRow['unit'] ?? 'KG',
                    'quantity' => $qty,
                    'rate' => $rate,
                    'amount' => $amt,
                    'notes' => $itemRow['notes'] ?? null,
                ];
            }

            $wbSale->update([
                'bill_type' => $validated['bill_type'] ?? $wbSale->bill_type ?? 'without_bill',
                'invoice_no' => $validated['invoice_no'] ?? null,
                'entry_date' => $validated['entry_date'],
                'customer_id' => $validated['customer_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'driver_name' => $validated['driver_name'] ?? null,
                'driver_phone' => $validated['driver_phone'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? '30 Days',
                'gross_weight' => $gross,
                'tare_weight' => $tare,
                'deduction_weight' => $deduction,
                'net_weight' => $netWeight,
                'total_amount' => $totalAmount,
                'paid_amount' => $validated['paid_amount'] ?? $wbSale->paid_amount,
                'payment_status' => $validated['payment_status'] ?? $wbSale->payment_status,
                'payment_mode' => $validated['payment_mode'] ?? 'Cash',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Replace line items
            $wbSale->items()->delete();
            foreach ($preparedItems as $pItem) {
                $pItem['wb_sale_id'] = $wbSale->id;
                WBSaleItem::create($pItem);

                // Deduct updated outward stock
                $item = Item::find($pItem['item_id']);
                if ($item) {
                    $item->decrement('current_stock', $pItem['quantity']);
                }
            }

            // Apply new Customer balance
            $newCustomer = Customer::find($validated['customer_id']);
            if ($newCustomer) {
                $newCustomer->increment('current_balance', $totalAmount);
            }

            DB::commit();

            return redirect()->route('admin.transactions.wb-sales-entry')
                ->with('success', "WB Sales Entry #{$wbSale->slip_no} updated successfully and inventory adjusted.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating WB sales entry: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified WB sales entry from storage.
     */
    public function destroy(WBSale $wbSale)
    {
        DB::beginTransaction();
        try {
            // Restore inventory stock
            if ($wbSale->status !== 'cancelled') {
                foreach ($wbSale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->increment('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($wbSale->customer_id)) {
                    $customer->decrement('current_balance', $wbSale->total_amount);
                }
            }

            $wbSale->items()->delete();
            $wbSale->delete();

            DB::commit();

            return redirect()->route('admin.transactions.wb-sales-entry')
                ->with('success', "WB Sales Entry #{$wbSale->slip_no} deleted successfully and outward stock restored.");
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error deleting WB sales entry: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the status of a WB sales entry.
     */
    public function toggleStatus(WBSale $wbSale)
    {
        DB::beginTransaction();
        try {
            if ($wbSale->status === 'cancelled') {
                // Redispatching: Verify stock first!
                foreach ($wbSale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if (!$item || (float)$item->current_stock < (float)$sItem->quantity) {
                        return back()->with(
                            'error',
                            "Cannot re-dispatch: Insufficient stock for '{$item->name}'. Available: {$item->current_stock} {$item->unit}, Needed: {$sItem->quantity} {$item->unit}."
                        );
                    }
                }

                foreach ($wbSale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->decrement('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($wbSale->customer_id)) {
                    $customer->increment('current_balance', $wbSale->total_amount);
                }
                $wbSale->update(['status' => 'dispatched']);
                $msg = "WB Sales entry #{$wbSale->slip_no} re-dispatched and outward stock deducted.";
            } else {
                // Cancelling: Restore stock
                foreach ($wbSale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->increment('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($wbSale->customer_id)) {
                    $customer->decrement('current_balance', $wbSale->total_amount);
                }
                $wbSale->update(['status' => 'cancelled']);
                $msg = "WB Sales entry #{$wbSale->slip_no} marked as cancelled and stock restored to inventory.";
            }

            DB::commit();
            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error toggling WB sales entry status: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to generate next slip number.
     */
    public function generateCode()
    {
        return response()->json([
            'code' => WBSale::generateNextSlipNo()
        ]);
    }
}
