<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Transporter;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
            'sale_no' => 'required|string|max:50|unique:sales,sale_no',
            'bill_type' => 'nullable|string|in:with_bill,without_bill',
            'is_gst_inclusive' => 'nullable|boolean',
            'invoice_no' => 'nullable|string|max:50',
            'invoice_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'sale_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'transport_name' => 'nullable|string|max:150',
            'vehicle_no' => 'nullable|string|max:50',
            'lr_no' => 'nullable|string|max:50',
            'e_way_bill_no' => 'nullable|string|max:50',
            'transport_date' => 'nullable|date',
            'transport_notes' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:50',
            'freight_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|string|in:unpaid,partial,paid',
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

            // Handle file attachment
            $attachmentPath = null;
            if ($request->hasFile('invoice_attachment')) {
                $attachmentPath = $request->file('invoice_attachment')->store('attachments/invoices', 'public');
            }

            $transportName = $validated['transport_name'] ?? null;

            $isGstInclusive = $request->boolean('is_gst_inclusive');
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

                if ($isGstInclusive) {
                    $lineBillAmt = round($qty * $billRate, 2);
                    $lineSub = round($lineBillAmt / (1 + ($gstPercent / 100)), 2);
                    $lineTax = round($lineBillAmt - $lineSub, 2);
                } else {
                    $lineSub = round($qty * $billRate, 2);
                    $lineTax = round($lineSub * ($gstPercent / 100), 2);
                    $lineBillAmt = round($lineSub + $lineTax, 2);
                }

                $lineUB = round($qty * $ubRate, 2);
                $lineTotal = round($lineBillAmt + $lineUB, 2);

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

            $freightAmount = isset($validated['freight_amount']) && $validated['freight_amount'] !== null && $validated['freight_amount'] !== '' ? (float) $validated['freight_amount'] : 0.0;
            $billTotal = $subtotal + $taxAmount;
            $grandTotal = $billTotal + $underBillingTotal + $freightAmount;

            $advanceAmount = isset($validated['advance_amount']) ? (float)$validated['advance_amount'] : 0.0;
            $paidAmount = isset($validated['paid_amount']) ? (float)$validated['paid_amount'] : 0.0;
            if (($validated['payment_terms'] ?? '') === 'Advance' && $advanceAmount > 0) {
                $paidAmount = $advanceAmount;
            }

            $paymentStatus = $paidAmount <= 0 ? 'unpaid' : ($paidAmount >= $grandTotal ? 'paid' : 'partial');

            $sale = Sale::create([
                'sale_no' => $validated['sale_no'],
                'bill_type' => $validated['bill_type'] ?? 'with_bill',
                'is_gst_inclusive' => $isGstInclusive,
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_attachment' => $attachmentPath,
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'transporter_id' => null,
                'transport_name' => $transportName,
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'lr_no' => $validated['lr_no'] ?? null,
                'e_way_bill_no' => $validated['e_way_bill_no'] ?? null,
                'transport_date' => $validated['transport_date'] ?? null,
                'transport_notes' => $validated['transport_notes'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? '30 Days',
                'freight_amount' => $freightAmount,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'bill_total' => $billTotal,
                'under_billing_total' => $underBillingTotal,
                'round_off' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'advance_amount' => $advanceAmount,
                'payment_status' => $paymentStatus,
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
        $sale->load(['customer', 'broker', 'company', 'transporter', 'items.item.unitRelation']);
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
            'sale_no' => ['required', 'string', 'max:50', Rule::unique('sales', 'sale_no')->ignore($sale->id)],
            'bill_type' => 'nullable|string|in:with_bill,without_bill',
            'is_gst_inclusive' => 'nullable|boolean',
            'invoice_no' => 'nullable|string|max:50',
            'invoice_attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'remove_attachment' => 'nullable|boolean',
            'sale_date' => 'required|date',
            'customer_id' => 'required|exists:customers,id',
            'broker_id' => 'nullable|exists:brokers,id',
            'order_type' => 'required|string|max:50',
            'transport_name' => 'nullable|string|max:150',
            'vehicle_no' => 'nullable|string|max:50',
            'lr_no' => 'nullable|string|max:50',
            'e_way_bill_no' => 'nullable|string|max:50',
            'transport_date' => 'nullable|date',
            'transport_notes' => 'nullable|string',
            'payment_terms' => 'nullable|string|max:50',
            'freight_amount' => 'nullable|numeric|min:0',
            'advance_amount' => 'nullable|numeric|min:0',
            'paid_amount' => 'nullable|numeric|min:0',
            'payment_status' => 'nullable|string|in:unpaid,partial,paid',
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
            // Handle file attachment
            $attachmentPath = $sale->invoice_attachment;
            if ($request->hasFile('invoice_attachment')) {
                if (!empty($sale->invoice_attachment) && Storage::disk('public')->exists($sale->invoice_attachment)) {
                    Storage::disk('public')->delete($sale->invoice_attachment);
                }
                $attachmentPath = $request->file('invoice_attachment')->store('attachments/invoices', 'public');
            } elseif ($request->boolean('remove_attachment')) {
                if (!empty($sale->invoice_attachment) && Storage::disk('public')->exists($sale->invoice_attachment)) {
                    Storage::disk('public')->delete($sale->invoice_attachment);
                }
                $attachmentPath = null;
            }

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

            $transportName = $validated['transport_name'] ?? null;

            $isGstInclusive = $request->boolean('is_gst_inclusive');
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

                if ($isGstInclusive) {
                    $lineBillAmt = round($qty * $billRate, 2);
                    $lineSub = round($lineBillAmt / (1 + ($gstPercent / 100)), 2);
                    $lineTax = round($lineBillAmt - $lineSub, 2);
                } else {
                    $lineSub = round($qty * $billRate, 2);
                    $lineTax = round($lineSub * ($gstPercent / 100), 2);
                    $lineBillAmt = round($lineSub + $lineTax, 2);
                }

                $lineUB = round($qty * $ubRate, 2);
                $lineTotal = round($lineBillAmt + $lineUB, 2);

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

            $freightAmount = isset($validated['freight_amount']) && $validated['freight_amount'] !== null && $validated['freight_amount'] !== '' ? (float) $validated['freight_amount'] : 0.0;
            $billTotal = $subtotal + $taxAmount;
            $grandTotal = $billTotal + $underBillingTotal + $freightAmount;

            $advanceAmount = isset($validated['advance_amount']) ? (float)$validated['advance_amount'] : ($sale->advance_amount ?? 0.0);
            $paidAmount = isset($validated['paid_amount']) ? (float)$validated['paid_amount'] : $sale->paid_amount;
            if (($validated['payment_terms'] ?? '') === 'Advance' && isset($validated['advance_amount']) && $advanceAmount > 0) {
                $paidAmount = $advanceAmount;
            }

            $paymentStatus = $paidAmount <= 0 ? 'unpaid' : ($paidAmount >= $grandTotal ? 'paid' : 'partial');

            $sale->update([
                'sale_no' => $validated['sale_no'],
                'bill_type' => $validated['bill_type'] ?? $sale->bill_type ?? 'with_bill',
                'is_gst_inclusive' => $isGstInclusive,
                'invoice_no' => $validated['invoice_no'] ?? null,
                'invoice_attachment' => $attachmentPath,
                'sale_date' => $validated['sale_date'],
                'customer_id' => $validated['customer_id'],
                'broker_id' => $validated['broker_id'] ?? null,
                'order_type' => $validated['order_type'] ?? 'Medium',
                'transporter_id' => null,
                'transport_name' => $transportName,
                'vehicle_no' => $validated['vehicle_no'] ?? null,
                'lr_no' => $validated['lr_no'] ?? null,
                'e_way_bill_no' => $validated['e_way_bill_no'] ?? null,
                'transport_date' => $validated['transport_date'] ?? null,
                'transport_notes' => $validated['transport_notes'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? '30 Days',
                'freight_amount' => $freightAmount,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'bill_total' => $billTotal,
                'under_billing_total' => $underBillingTotal,
                'round_off' => 0.00,
                'grand_total' => $grandTotal,
                'paid_amount' => $paidAmount,
                'advance_amount' => $advanceAmount,
                'payment_status' => $paymentStatus,
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
     * Update the lifecycle status of a sales entry.
     * Allowed statuses: ordered, dispatched, delivered, completed, cancelled
     */
    public function updateStatus(Request $request, Sale $sale)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:ordered,dispatched,delivered,completed,cancelled'
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $sale->status;

        if ($newStatus === $oldStatus) {
            return back()->with('info', "Sales entry #{$sale->sale_no} is already {$newStatus}.");
        }

        DB::beginTransaction();
        try {
            // Case 1: Cancelling (revert outward stock & customer balance)
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
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
                $msg = "Sales entry #{$sale->sale_no} cancelled successfully. Outward stock has been restored to inventory.";
            }
            // Case 2: Re-activating from cancelled (verify stock availability & re-deduct)
            elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($sale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if (!$item || (float)$item->current_stock < (float)$sItem->quantity) {
                        return back()->with(
                            'error',
                            "Cannot activate order: Insufficient stock for '{$item->name}'. Available: {$item->current_stock} {$item->unit}, Needed: {$sItem->quantity} {$item->unit}."
                        );
                    }
                }
                foreach ($sale->items as $sItem) {
                    $item = Item::find($sItem->item_id);
                    if ($item) {
                        $item->decrement('current_stock', $sItem->quantity);
                    }
                }
                if ($customer = Customer::find($sale->customer_id)) {
                    $customer->increment('current_balance', $sale->grand_total);
                }
                $sale->update(['status' => $newStatus]);
                $msg = "Sales entry #{$sale->sale_no} status changed from Cancelled to " . ucfirst($newStatus) . " and stock re-allocated.";
            }
            // Case 3: Transitioning between active statuses (ordered <-> dispatched <-> delivered <-> completed)
            else {
                $sale->update(['status' => $newStatus]);
                $msg = "Sales entry #{$sale->sale_no} status successfully updated to " . ucfirst($newStatus) . ".";
            }

            DB::commit();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'status' => $newStatus,
                    'message' => $msg
                ]);
            }

            return back()->with('success', $msg);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating sales status: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status for quick action or test backwards-compatibility.
     */
    public function toggleStatus(Request $request, Sale $sale)
    {
        if ($request->filled('status')) {
            return $this->updateStatus($request, $sale);
        }

        // Toggle: if cancelled, re-activate to dispatched; otherwise cancel
        $nextStatus = ($sale->status === 'cancelled') ? 'dispatched' : 'cancelled';

        $request->merge(['status' => $nextStatus]);
        return $this->updateStatus($request, $sale);
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
