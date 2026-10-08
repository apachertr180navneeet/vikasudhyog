<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\WBPurchase;
use App\Models\WBPurchaseItem;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\WBSale;
use App\Models\WBSaleItem;
use App\Models\StockAdjustment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InventoryController extends Controller
{
    /**
     * Display current stock levels, inventory valuation, and reorder warnings.
     */
    public function stockOverview(Request $request)
    {
        $query = Item::with(['unitRelation', 'company']);

        // Search Filter
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('hsn_code', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Unit Filter
        if ($unit = $request->input('unit')) {
            $query->where('unit', $unit);
        }

        // Stock Level Status Filter
        if ($stockStatus = $request->input('stock_status')) {
            if ($stockStatus === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'min_stock_alert')
                      ->where('current_stock', '>', 0);
            } elseif ($stockStatus === 'out_of_stock') {
                $query->where('current_stock', '<=', 0);
            } elseif ($stockStatus === 'in_stock') {
                $query->whereColumn('current_stock', '>', 'min_stock_alert');
            }
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'name_asc');
        switch ($sortBy) {
            case 'stock_desc':
                $query->orderBy('current_stock', 'desc');
                break;
            case 'stock_asc':
                $query->orderBy('current_stock', 'asc');
                break;
            case 'value_desc':
                $query->orderByRaw('(current_stock * purchase_rate) DESC');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'name_asc':
            default:
                $query->orderBy('name', 'asc');
                break;
        }

        // CSV Export Trigger
        if ($request->has('export') && $request->input('export') === 'csv') {
            return $this->exportStockCsv($query->get());
        }

        $items = $query->paginate(10)->withQueryString();

        // 4-Card KPIs calculation
        $totalItems = Item::count();
        $totalStockQty = (float)Item::sum('current_stock');
        $totalStockValuation = (float)Item::selectRaw('SUM(current_stock * purchase_rate) as total_val')->value('total_val') ?? 0.0;
        $lowStockCount = Item::whereColumn('current_stock', '<=', 'min_stock_alert')->where('current_stock', '>', 0)->count();
        $outOfStockCount = Item::where('current_stock', '<=', 0)->count();

        // Filter dropdown lists
        $categories = Item::whereNotNull('category')->where('category', '!=', '')->distinct()->orderBy('category')->pluck('category');
        $units = Item::whereNotNull('unit')->where('unit', '!=', '')->distinct()->orderBy('unit')->pluck('unit');

        return view('admin.inventory.stock-overview', compact(
            'items',
            'totalItems',
            'totalStockQty',
            'totalStockValuation',
            'lowStockCount',
            'outOfStockCount',
            'categories',
            'units'
        ));
    }

    /**
     * Export current stock inventory to a clean CSV spreadsheet with UTF-8 BOM.
     */
    protected function exportStockCsv($items): StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="vikas_udhyog_stock_overview_' . date('Y_m_d_His') . '.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Item Code',
                'Item Name',
                'Category',
                'HSN Code',
                'Batch No',
                'Unit',
                'Current Stock',
                'Min Alert Level',
                'Purchase Rate (INR)',
                'Sale Rate (INR)',
                'Stock Valuation (INR)',
                'Stock Status',
            ]);

            foreach ($items as $item) {
                $status = 'In Stock';
                if ($item->current_stock <= 0) {
                    $status = 'Out of Stock';
                } elseif ($item->current_stock <= $item->min_stock_alert) {
                    $status = 'Low Stock Alert';
                }

                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->category ?? 'General',
                    $item->hsn_code ?? 'N/A',
                    $item->batch_no ?? 'N/A',
                    $item->unit,
                    number_format($item->current_stock, 2, '.', ''),
                    number_format($item->min_stock_alert, 2, '.', ''),
                    number_format($item->purchase_rate, 2, '.', ''),
                    number_format($item->sale_rate, 2, '.', ''),
                    number_format($item->current_stock * $item->purchase_rate, 2, '.', ''),
                    $status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the item-wise stock ledger and movement timeline (In / Out).
     */
    public function itemLedger(Request $request)
    {
        $allItems = Item::with('unitRelation')
            ->orderBy('name', 'asc')
            ->get();

        $selectedItemId = $request->input('item_id');
        $selectedItem = null;

        if ($selectedItemId) {
            $selectedItem = Item::with(['unitRelation', 'company'])->find($selectedItemId);
        }

        if (!$selectedItem && $allItems->isNotEmpty()) {
            $selectedItem = $allItems->first();
            $selectedItemId = $selectedItem->id;
        }

        if (!$selectedItem) {
            return view('admin.inventory.item-ledger', [
                'allItems'          => collect(),
                'selectedItem'      => null,
                'movements'         => collect(),
                'openingRow'        => null,
                'openingBalance'    => 0,
                'totalInQty'        => 0,
                'totalOutQty'       => 0,
                'closingBalance'    => 0,
                'totalInVal'        => 0,
                'totalOutVal'       => 0,
                'inCount'           => 0,
                'outCount'          => 0,
                'sortOrder'         => 'asc',
                'viewMode'          => 'table',
                'filters'           => $request->all(),
            ]);
        }

        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        $typeFilter = $request->input('type', 'all'); // all, inward, outward, purchase, wb_purchase, sale, wb_sale
        $sortOrder = $request->input('sort_order', 'asc'); // asc (chronological) or desc (newest first)
        $viewMode = $request->input('view_mode', 'table'); // table or timeline

        // Collect all raw movements for the selected item
        $rawMovements = [];

        // 1. Standard Purchases
        $purchaseItems = PurchaseItem::with(['purchase.vendor'])
            ->where('item_id', $selectedItem->id)
            ->whereHas('purchase', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        foreach ($purchaseItems as $pi) {
            $p = $pi->purchase;
            if ($p && $p->status !== 'cancelled') {
                $invDate = $p->invoice_date ? Carbon::parse($p->invoice_date)->format('Y-m-d') : $p->created_at->format('Y-m-d');
                $rawMovements[] = [
                    'id'          => 'pur_' . $pi->id,
                    'date'        => $invDate,
                    'datetime'    => $p->created_at,
                    'category'    => 'inward',
                    'type'        => 'purchase',
                    'type_label'  => 'Purchase Entry (Bill)',
                    'voucher_no'  => $p->purchase_no,
                    'invoice_no'  => $p->invoice_no,
                    'party_type'  => 'Vendor',
                    'party_name'  => $p->vendor ? $p->vendor->name : 'N/A',
                    'in_qty'      => (float)$pi->quantity,
                    'out_qty'     => 0.0,
                    'unit'        => $pi->unit ?: $selectedItem->unit,
                    'rate'        => (float)$pi->bill_rate,
                    'amount'      => (float)$pi->total_amount,
                    'url'         => route('admin.transactions.purchase-entry.show', $p->id),
                    'batch_no'    => $pi->batch_no,
                    'notes'       => $p->notes,
                ];
            }
        }

        // 2. WB Purchases
        $wbPurchaseItems = WBPurchaseItem::with(['wbPurchase.vendor'])
            ->where('item_id', $selectedItem->id)
            ->whereHas('wbPurchase', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        foreach ($wbPurchaseItems as $wpi) {
            $wbp = $wpi->wbPurchase;
            if ($wbp && $wbp->status !== 'cancelled') {
                $entryDate = $wbp->entry_date ? Carbon::parse($wbp->entry_date)->format('Y-m-d') : $wbp->created_at->format('Y-m-d');
                $rawMovements[] = [
                    'id'          => 'wb_pur_' . $wpi->id,
                    'date'        => $entryDate,
                    'datetime'    => $wbp->created_at,
                    'category'    => 'inward',
                    'type'        => 'wb_purchase',
                    'type_label'  => 'WB Purchase (Without Bill)',
                    'voucher_no'  => $wbp->slip_no,
                    'invoice_no'  => $wbp->invoice_no,
                    'party_type'  => 'Vendor',
                    'party_name'  => $wbp->vendor ? $wbp->vendor->name : 'N/A',
                    'in_qty'      => (float)$wpi->quantity,
                    'out_qty'     => 0.0,
                    'unit'        => $wpi->unit ?: $selectedItem->unit,
                    'rate'        => (float)$wpi->rate,
                    'amount'      => (float)$wpi->amount,
                    'url'         => route('admin.transactions.wb-purchase-entry.show', $wbp->id),
                    'batch_no'    => $wpi->batch_no,
                    'notes'       => $wbp->notes ?: $wpi->notes,
                ];
            }
        }

        // 3. Standard Sales
        $saleItems = SaleItem::with(['sale.customer'])
            ->where('item_id', $selectedItem->id)
            ->whereHas('sale', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        foreach ($saleItems as $si) {
            $s = $si->sale;
            if ($s && $s->status !== 'cancelled') {
                $saleDate = $s->sale_date ? Carbon::parse($s->sale_date)->format('Y-m-d') : $s->created_at->format('Y-m-d');
                $rawMovements[] = [
                    'id'          => 'sale_' . $si->id,
                    'date'        => $saleDate,
                    'datetime'    => $s->created_at,
                    'category'    => 'outward',
                    'type'        => 'sale',
                    'type_label'  => 'Sales Entry (Bill)',
                    'voucher_no'  => $s->sale_no,
                    'invoice_no'  => $s->invoice_no,
                    'party_type'  => 'Customer',
                    'party_name'  => $s->customer ? $s->customer->name : 'N/A',
                    'in_qty'      => 0.0,
                    'out_qty'     => (float)$si->quantity,
                    'unit'        => $si->unit ?: $selectedItem->unit,
                    'rate'        => (float)$si->bill_rate,
                    'amount'      => (float)$si->total_amount,
                    'url'         => route('admin.transactions.sales-entry.show', $s->id),
                    'batch_no'    => $si->batch_no,
                    'notes'       => $s->notes,
                ];
            }
        }

        // 4. WB Sales
        $wbSaleItems = WBSaleItem::with(['wbSale.customer'])
            ->where('item_id', $selectedItem->id)
            ->whereHas('wbSale', function ($q) {
                $q->whereNull('deleted_at');
            })
            ->get();

        foreach ($wbSaleItems as $wsi) {
            $wbs = $wsi->wbSale;
            if ($wbs && $wbs->status !== 'cancelled') {
                $entryDate = $wbs->entry_date ? Carbon::parse($wbs->entry_date)->format('Y-m-d') : $wbs->created_at->format('Y-m-d');
                $rawMovements[] = [
                    'id'          => 'wb_sale_' . $wsi->id,
                    'date'        => $entryDate,
                    'datetime'    => $wbs->created_at,
                    'category'    => 'outward',
                    'type'        => 'wb_sale',
                    'type_label'  => 'WB Sales (Without Bill)',
                    'voucher_no'  => $wbs->slip_no,
                    'invoice_no'  => $wbs->invoice_no,
                    'party_type'  => 'Customer',
                    'party_name'  => $wbs->customer ? $wbs->customer->name : 'N/A',
                    'in_qty'      => 0.0,
                    'out_qty'     => (float)$wsi->quantity,
                    'unit'        => $wsi->unit ?: $selectedItem->unit,
                    'rate'        => (float)$wsi->rate,
                    'amount'      => (float)$wsi->amount,
                    'url'         => route('admin.transactions.wb-sales-entry.show', $wbs->id),
                    'batch_no'    => $wsi->batch_no,
                    'notes'       => $wbs->notes ?: $wsi->notes,
                ];
            }
        }

        // 5. Stock Adjustments & Physical Audit Events
        $stockAdjustments = StockAdjustment::where('item_id', $selectedItem->id)
            ->whereNull('deleted_at')
            ->get();

        foreach ($stockAdjustments as $sa) {
            $adjDate = $sa->adjustment_date ? Carbon::parse($sa->adjustment_date)->format('Y-m-d') : $sa->created_at->format('Y-m-d');
            $isAdd = $sa->type === 'add';
            $rawMovements[] = [
                'id'          => 'adj_' . $sa->id,
                'date'        => $adjDate,
                'datetime'    => $sa->created_at,
                'category'    => $isAdd ? 'inward' : 'outward',
                'type'        => $isAdd ? 'stock_adjustment_add' : 'stock_adjustment_reduce',
                'type_label'  => $isAdd ? 'Stock Adjustment (+ Surplus)' : 'Stock Adjustment (- Wastage)',
                'voucher_no'  => $sa->adjustment_no,
                'invoice_no'  => null,
                'party_type'  => 'Audit',
                'party_name'  => $sa->reason ?: 'Physical Stock Audit',
                'in_qty'      => $isAdd ? (float)$sa->quantity : 0.0,
                'out_qty'     => $isAdd ? 0.0 : (float)$sa->quantity,
                'unit'        => $sa->unit ?: $selectedItem->unit,
                'rate'        => (float)$sa->rate,
                'amount'      => (float)$sa->total_value,
                'url'         => route('admin.inventory.stock-adjustment', ['search' => $sa->adjustment_no]),
                'batch_no'    => $selectedItem->batch_no,
                'notes'       => $sa->notes,
            ];
        }

        // Sort all raw movements strictly in chronological order
        usort($rawMovements, function ($a, $b) {
            if ($a['date'] === $b['date']) {
                return ($a['datetime'] ?? 0) <=> ($b['datetime'] ?? 0);
            }
            return strcmp($a['date'], $b['date']);
        });

        // Calculate Period Opening Balance if $fromDate is specified
        $initialStock = (float)$selectedItem->opening_stock;
        $periodOpeningBalance = $initialStock;

        if ($fromDate) {
            foreach ($rawMovements as $rm) {
                if ($rm['date'] < $fromDate) {
                    $periodOpeningBalance += ($rm['in_qty'] - $rm['out_qty']);
                }
            }
        }

        // Now compute running balance for transactions within the filter
        $runningBalance = $periodOpeningBalance;
        $filteredMovements = [];
        $totalInQty = 0;
        $totalOutQty = 0;
        $totalInVal = 0;
        $totalOutVal = 0;
        $inCount = 0;
        $outCount = 0;

        $search = trim($request->input('search', ''));

        foreach ($rawMovements as $rm) {
            // Check if before from_date
            if ($fromDate && $rm['date'] < $fromDate) {
                continue;
            }
            // Check if after to_date
            if ($toDate && $rm['date'] > $toDate) {
                continue;
            }

            // Update running balance sequentially
            $runningBalance += ($rm['in_qty'] - $rm['out_qty']);
            $rm['running_balance'] = $runningBalance;

            // Apply type filter
            $include = true;
            if ($typeFilter === 'inward' && $rm['in_qty'] <= 0) {
                $include = false;
            } elseif ($typeFilter === 'outward' && $rm['out_qty'] <= 0) {
                $include = false;
            } elseif ($typeFilter === 'purchase' && $rm['type'] !== 'purchase') {
                $include = false;
            } elseif ($typeFilter === 'wb_purchase' && $rm['type'] !== 'wb_purchase') {
                $include = false;
            } elseif ($typeFilter === 'sale' && $rm['type'] !== 'sale') {
                $include = false;
            } elseif ($typeFilter === 'wb_sale' && $rm['type'] !== 'wb_sale') {
                $include = false;
            }

            // Apply keyword search filter if specified
            if ($include && $search !== '') {
                $sLower = strtolower($search);
                $matches = str_contains(strtolower($rm['voucher_no']), $sLower)
                    || str_contains(strtolower($rm['invoice_no'] ?? ''), $sLower)
                    || str_contains(strtolower($rm['party_name']), $sLower)
                    || str_contains(strtolower($rm['type_label']), $sLower)
                    || str_contains(strtolower($rm['batch_no'] ?? ''), $sLower);
                if (!$matches) {
                    $include = false;
                }
            }

            if ($include) {
                $filteredMovements[] = $rm;
            }

            // Aggregates for the date range
            if ($rm['in_qty'] > 0) {
                $totalInQty += $rm['in_qty'];
                $totalInVal += $rm['amount'];
                $inCount++;
            }
            if ($rm['out_qty'] > 0) {
                $totalOutQty += $rm['out_qty'];
                $totalOutVal += $rm['amount'];
                $outCount++;
            }
        }

        $closingBalance = $runningBalance;

        // Create opening row
        $openingRow = [
            'id'              => 'opening_bal',
            'date'            => $fromDate ?: ($selectedItem->created_at ? $selectedItem->created_at->format('Y-m-d') : date('Y-m-d')),
            'datetime'        => null,
            'category'        => 'opening',
            'type'            => 'opening',
            'type_label'      => $fromDate ? 'Period Opening Balance' : 'Initial Opening Balance',
            'voucher_no'      => 'INITIAL-STOCK',
            'invoice_no'      => '-',
            'party_type'      => 'System',
            'party_name'      => 'Opening Balance Master',
            'in_qty'          => (float)$periodOpeningBalance,
            'out_qty'         => 0.0,
            'unit'            => $selectedItem->unit,
            'rate'            => (float)$selectedItem->purchase_rate,
            'amount'          => (float)($periodOpeningBalance * $selectedItem->purchase_rate),
            'running_balance' => (float)$periodOpeningBalance,
            'url'             => null,
            'batch_no'        => $selectedItem->batch_no,
            'notes'           => 'Initial stock setup in Item Master',
        ];

        // CSV Export handling
        if ($request->input('export') === 'csv') {
            return $this->exportItemLedgerCsv($selectedItem, $openingRow, $filteredMovements, $closingBalance);
        }

        // Handle sort order
        $displayMovements = $filteredMovements;
        if ($sortOrder === 'desc') {
            $displayMovements = array_reverse($filteredMovements);
        }

        return view('admin.inventory.item-ledger', [
            'allItems'          => $allItems,
            'selectedItem'      => $selectedItem,
            'movements'         => $displayMovements,
            'openingRow'        => $openingRow,
            'openingBalance'    => $periodOpeningBalance,
            'totalInQty'        => $totalInQty,
            'totalOutQty'       => $totalOutQty,
            'closingBalance'    => $closingBalance,
            'totalInVal'        => $totalInVal,
            'totalOutVal'       => $totalOutVal,
            'inCount'           => $inCount,
            'outCount'          => $outCount,
            'sortOrder'         => $sortOrder,
            'viewMode'          => $viewMode,
            'filters'           => $request->all(),
        ]);
    }

    /**
     * Export item stock ledger to a clean CSV spreadsheet with UTF-8 BOM.
     */
    protected function exportItemLedgerCsv($item, $openingRow, $movements, $closingBalance): StreamedResponse
    {
        $filename = "item_stock_ledger_{$item->code}_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($item, $openingRow, $movements, $closingBalance) {
            $file = fopen('php://output', 'w');
            // UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // File meta header
            fputcsv($file, ['VIKAS UDHYOG ERP - ITEM STOCK LEDGER']);
            fputcsv($file, ['Product:', $item->name, 'Code:', $item->code, 'Category:', $item->category, 'Unit:', $item->unit]);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            // Column Headers
            fputcsv($file, [
                'Date',
                'Transaction Type',
                'Voucher / Ref No',
                'Invoice No',
                'Party Type',
                'Party Name',
                'Inward Qty (' . $item->unit . ')',
                'Outward Qty (' . $item->unit . ')',
                'Running Balance (' . $item->unit . ')',
                'Rate (INR)',
                'Total Amount (INR)',
                'Remarks / Batch',
            ]);

            // Opening Row
            fputcsv($file, [
                $openingRow['date'],
                $openingRow['type_label'],
                $openingRow['voucher_no'],
                '-',
                $openingRow['party_type'],
                $openingRow['party_name'],
                number_format($openingRow['in_qty'], 3, '.', ''),
                '-',
                number_format($openingRow['running_balance'], 3, '.', ''),
                number_format($openingRow['rate'], 2, '.', ''),
                number_format($openingRow['amount'], 2, '.', ''),
                $openingRow['notes'],
            ]);

            // Transactions
            foreach ($movements as $m) {
                fputcsv($file, [
                    $m['date'],
                    $m['type_label'],
                    $m['voucher_no'],
                    $m['invoice_no'] ?: '-',
                    $m['party_type'],
                    $m['party_name'],
                    $m['in_qty'] > 0 ? number_format($m['in_qty'], 3, '.', '') : '-',
                    $m['out_qty'] > 0 ? number_format($m['out_qty'], 3, '.', '') : '-',
                    number_format($m['running_balance'], 3, '.', ''),
                    number_format($m['rate'], 2, '.', ''),
                    number_format($m['amount'], 2, '.', ''),
                    ($m['batch_no'] ? "Batch: {$m['batch_no']}; " : '') . ($m['notes'] ?: ''),
                ]);
            }

            fputcsv($file, []);
            fputcsv($file, ['Closing Balance:', number_format($closingBalance, 3, '.', '') . ' ' . $item->unit]);
            fputcsv($file, ['Closing Valuation:', 'INR ' . number_format($closingBalance * $item->purchase_rate, 2, '.', '')]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the Stock Adjustment & Physical Audit directory and reconciliation register.
     */
    public function stockAdjustment(Request $request)
    {
        $query = StockAdjustment::with(['item.unitRelation', 'user', 'company'])
            ->orderBy('id', 'desc');

        // Search Filter
        if ($search = trim($request->input('search', ''))) {
            $query->search($search);
        }

        // Item Filter
        if ($itemId = $request->input('item_id')) {
            $query->byItem($itemId);
        }

        // Type Filter (add / reduce)
        if ($type = $request->input('type')) {
            $query->byType($type);
        }

        // Date Range Filters
        if ($fromDate = $request->input('from_date')) {
            $query->whereDate('adjustment_date', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $query->whereDate('adjustment_date', '<=', $toDate);
        }

        // Export to CSV
        if ($request->input('export') === 'csv') {
            return $this->exportStockAdjustmentsCsv($query->get());
        }

        // KPI Statistics (across all adjustments)
        $baseQuery = StockAdjustment::query();
        if ($itemId = $request->input('item_id')) {
            $baseQuery->byItem($itemId);
        }
        if ($fromDate = $request->input('from_date')) {
            $baseQuery->whereDate('adjustment_date', '>=', $fromDate);
        }
        if ($toDate = $request->input('to_date')) {
            $baseQuery->whereDate('adjustment_date', '<=', $toDate);
        }

        $allAdjustments = $baseQuery->get();
        $totalAdjustments = $allAdjustments->count();
        $totalAddQty = (float)$allAdjustments->where('type', 'add')->sum('quantity');
        $totalReduceQty = (float)$allAdjustments->where('type', 'reduce')->sum('quantity');
        $addCount = $allAdjustments->where('type', 'add')->count();
        $reduceCount = $allAdjustments->where('type', 'reduce')->count();

        $netValuationImpact = 0;
        foreach ($allAdjustments as $adj) {
            if ($adj->type === 'add') {
                $netValuationImpact += (float)$adj->total_value;
            } else {
                $netValuationImpact -= (float)$adj->total_value;
            }
        }

        $stats = [
            'total'               => $totalAdjustments,
            'add_qty'             => $totalAddQty,
            'reduce_qty'          => $totalReduceQty,
            'add_count'           => $addCount,
            'reduce_count'        => $reduceCount,
            'net_valuation'       => $netValuationImpact,
        ];

        $adjustments = $query->paginate(15)->withQueryString();

        $allItems = Item::with('unitRelation')
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        return view('admin.inventory.stock-adjustments.index', [
            'adjustments' => $adjustments,
            'stats'       => $stats,
            'allItems'    => $allItems,
            'filters'     => $request->all(),
            'pageTitle'   => 'Stock Adjustment & Physical Audit - VIKAS UDHYOG ERP',
            'pageCode'    => 'inv-adjustment',
        ]);
    }

    /**
     * Show form for creating a new physical stock adjustment.
     */
    public function createStockAdjustment(Request $request)
    {
        $nextAdjustmentNo = StockAdjustment::generateAdjustmentNo();
        $allItems = Item::with('unitRelation')
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $selectedItemId = $request->input('item_id');
        $reasons = [
            'Physical Count Variance',
            'Damaged Packaging / Spillage',
            'Expired / Degraded Quality',
            'Sampling / Lab Quality Testing',
            'Found Unrecorded Surplus',
            'Supplier Shortage Reconciliation',
            'Transit Loss',
            'Correction of Data Entry Error',
            'Other'
        ];

        return view('admin.inventory.stock-adjustments.create', [
            'nextAdjustmentNo' => $nextAdjustmentNo,
            'allItems'         => $allItems,
            'selectedItemId'   => $selectedItemId,
            'reasons'          => $reasons,
            'pageTitle'        => 'New Stock Adjustment - VIKAS UDHYOG ERP',
            'pageCode'         => 'inv-adjustment',
        ]);
    }

    /**
     * Store a new stock adjustment and update physical inventory balances.
     */
    public function storeStockAdjustment(Request $request)
    {
        $validated = $request->validate([
            'adjustment_no'   => 'nullable|string|max:50|unique:stock_adjustments,adjustment_no',
            'item_id'         => 'required|exists:items,id',
            'adjustment_date' => 'required|date',
            'type'            => 'required|in:add,reduce',
            'quantity'        => 'required|numeric|min:0.01',
            'reason'          => 'required|string|max:150',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $adjustment = DB::transaction(function () use ($validated, $request) {
            $item = Item::lockForUpdate()->findOrFail($validated['item_id']);
            $previousStock = (float)$item->current_stock;
            $qty = (float)$validated['quantity'];

            // Compute new stock level
            if ($validated['type'] === 'add') {
                $newStock = $previousStock + $qty;
            } else {
                $newStock = $previousStock - $qty;
            }

            // Update item stock balance
            $item->current_stock = $newStock;
            $item->save();

            // Calculate valuation impact
            $rate = (float)$item->purchase_rate;
            $totalValue = $qty * $rate;
            $auditedBy = Auth::check() ? Auth::user()->name : 'Admin Auditor';
            $adjNo = !empty($validated['adjustment_no']) ? $validated['adjustment_no'] : StockAdjustment::generateAdjustmentNo();

            return StockAdjustment::create([
                'adjustment_no'   => $adjNo,
                'adjustment_date' => $validated['adjustment_date'],
                'item_id'         => $item->id,
                'type'            => $validated['type'],
                'quantity'        => $qty,
                'previous_stock'  => $previousStock,
                'new_stock'       => $newStock,
                'unit'            => $item->unit,
                'rate'            => $rate,
                'total_value'     => $totalValue,
                'reason'          => $validated['reason'],
                'notes'           => $validated['notes'] ?? null,
                'audited_by'      => $auditedBy,
                'user_id'         => Auth::id(),
                'company_id'      => $item->company_id ?: session('active_company_id'),
                'status'          => 'completed',
            ]);
        });

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => "Stock adjustment {$adjustment->adjustment_no} recorded successfully!",
                'adjustment' => $adjustment,
            ]);
        }

        return redirect()->route('admin.inventory.stock-adjustment')
            ->with('success', "Stock adjustment {$adjustment->adjustment_no} recorded successfully! Inventory balance updated.");
    }

    /**
     * Show 360-degree dossier of a stock adjustment record.
     */
    public function showStockAdjustment(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['item.unitRelation', 'user', 'company']);

        return view('admin.inventory.stock-adjustments.show', [
            'adjustment' => $stockAdjustment,
            'pageTitle'  => "Audit {$stockAdjustment->adjustment_no} Profile - VIKAS UDHYOG ERP",
            'pageCode'   => 'inv-adjustment',
        ]);
    }

    /**
     * Show form for editing an existing stock adjustment.
     */
    public function editStockAdjustment(StockAdjustment $stockAdjustment)
    {
        $stockAdjustment->load(['item.unitRelation', 'user', 'company']);
        $allItems = Item::with('unitRelation')
            ->where('status', 'active')
            ->orderBy('name', 'asc')
            ->get();

        $reasons = [
            'Physical Count Variance',
            'Damaged Packaging / Spillage',
            'Expired / Degraded Quality',
            'Sampling / Lab Quality Testing',
            'Found Unrecorded Surplus',
            'Supplier Shortage Reconciliation',
            'Transit Loss',
            'Correction of Data Entry Error',
            'Other'
        ];

        return view('admin.inventory.stock-adjustments.edit', [
            'adjustment' => $stockAdjustment,
            'allItems'   => $allItems,
            'reasons'    => $reasons,
            'pageTitle'  => "Edit Stock Adjustment {$stockAdjustment->adjustment_no} - VIKAS UDHYOG ERP",
            'pageCode'   => 'inv-adjustment',
        ]);
    }

    /**
     * Update an existing stock adjustment and re-adjust physical inventory.
     */
    public function updateStockAdjustment(Request $request, StockAdjustment $stockAdjustment)
    {
        $validated = $request->validate([
            'adjustment_no'   => 'required|string|max:50|unique:stock_adjustments,adjustment_no,' . $stockAdjustment->id,
            'item_id'         => 'required|exists:items,id',
            'adjustment_date' => 'required|date',
            'type'            => 'required|in:add,reduce',
            'quantity'        => 'required|numeric|min:0.01',
            'reason'          => 'required|string|max:150',
            'notes'           => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($validated, $stockAdjustment) {
            // 1. Revert previous adjustment on the original item
            $originalItem = Item::lockForUpdate()->findOrFail($stockAdjustment->item_id);
            if ($stockAdjustment->type === 'add') {
                $originalItem->current_stock -= (float)$stockAdjustment->quantity;
            } else {
                $originalItem->current_stock += (float)$stockAdjustment->quantity;
            }
            $originalItem->save();

            // 2. Apply new adjustment to target item (could be same or new item)
            $newItem = ($validated['item_id'] == $originalItem->id)
                ? $originalItem
                : Item::lockForUpdate()->findOrFail($validated['item_id']);

            $newPreviousStock = (float)$newItem->current_stock;
            $newQty = (float)$validated['quantity'];

            if ($validated['type'] === 'add') {
                $newStock = $newPreviousStock + $newQty;
            } else {
                $newStock = $newPreviousStock - $newQty;
            }

            $newItem->current_stock = $newStock;
            $newItem->save();

            // 3. Update adjustment valuation & details
            $rate = (float)$newItem->purchase_rate;
            $totalValue = $newQty * $rate;

            $stockAdjustment->update([
                'adjustment_no'   => $validated['adjustment_no'],
                'adjustment_date' => $validated['adjustment_date'],
                'item_id'         => $newItem->id,
                'type'            => $validated['type'],
                'quantity'        => $newQty,
                'previous_stock'  => $newPreviousStock,
                'new_stock'       => $newStock,
                'unit'            => $newItem->unit,
                'rate'            => $rate,
                'total_value'     => $totalValue,
                'reason'          => $validated['reason'],
                'notes'           => $validated['notes'] ?? null,
            ]);
        });

        return redirect()->route('admin.inventory.stock-adjustment')
            ->with('success', "Stock adjustment {$stockAdjustment->adjustment_no} updated successfully! Inventory balances reconciled.");
    }

    /**
     * Cancel / delete a stock adjustment and revert item stock balance.
     */
    public function destroyStockAdjustment(StockAdjustment $stockAdjustment)
    {
        DB::transaction(function () use ($stockAdjustment) {
            $item = Item::lockForUpdate()->findOrFail($stockAdjustment->item_id);

            // Revert stock change
            if ($stockAdjustment->type === 'add') {
                $item->current_stock -= (float)$stockAdjustment->quantity;
            } else {
                $item->current_stock += (float)$stockAdjustment->quantity;
            }

            $item->save();
            $stockAdjustment->delete();
        });

        return redirect()->route('admin.inventory.stock-adjustment')
            ->with('success', "Stock adjustment {$stockAdjustment->adjustment_no} cancelled and stock reverted successfully.");
    }

    /**
     * Export stock adjustments log to CSV spreadsheet.
     */
    protected function exportStockAdjustmentsCsv($adjustments): StreamedResponse
    {
        $filename = "stock_adjustments_audit_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($adjustments) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['VIKAS UDHYOG ERP - STOCK ADJUSTMENT & PHYSICAL AUDIT LOG']);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            fputcsv($file, [
                'Adjustment No',
                'Date',
                'Item Code',
                'Item Name',
                'Type',
                'Adjusted Quantity',
                'Unit',
                'Previous Stock',
                'New Stock',
                'Rate (INR)',
                'Valuation Impact (INR)',
                'Reason / Category',
                'Remarks / Notes',
                'Audited By',
                'Status',
            ]);

            foreach ($adjustments as $adj) {
                fputcsv($file, [
                    $adj->adjustment_no,
                    $adj->adjustment_date ? $adj->adjustment_date->format('Y-m-d') : '',
                    $adj->item ? $adj->item->code : 'N/A',
                    $adj->item ? $adj->item->name : 'N/A',
                    $adj->type === 'add' ? '+ Surplus / Inward' : '- Damage / Wastage',
                    ($adj->type === 'add' ? '+' : '-') . number_format($adj->quantity, 2, '.', ''),
                    $adj->unit,
                    number_format($adj->previous_stock, 2, '.', ''),
                    number_format($adj->new_stock, 2, '.', ''),
                    number_format($adj->rate, 2, '.', ''),
                    ($adj->type === 'add' ? '+' : '-') . number_format($adj->total_value, 2, '.', ''),
                    $adj->reason,
                    $adj->notes ?: '-',
                    $adj->audited_by ?: 'System',
                    $adj->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the Low Stock Alert Center & Reorder Requisition Dashboard.
     */
    public function lowStockAlert(Request $request)
    {
        $query = Item::with(['unitRelation', 'company'])
            ->where('status', 'active')
            ->whereColumn('current_stock', '<=', 'min_stock_alert');

        // Search Filter
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('hsn_code', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%");
            });
        }

        // Category Filter
        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        // Severity / Stock Health Filter
        $severity = $request->input('severity', 'all');
        if ($severity === 'out_of_stock') {
            $query->where('current_stock', '<=', 0);
        } elseif ($severity === 'low_stock') {
            $query->where('current_stock', '>', 0);
        } elseif ($severity === 'critical') {
            $query->whereRaw('current_stock <= (min_stock_alert * 0.5)');
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'deficit_desc');
        if ($sortBy === 'deficit_desc') {
            $query->orderByRaw('(min_stock_alert - current_stock) DESC');
        } elseif ($sortBy === 'stock_asc') {
            $query->orderBy('current_stock', 'asc');
        } elseif ($sortBy === 'name_asc') {
            $query->orderBy('name', 'asc');
        } elseif ($sortBy === 'cost_desc') {
            $query->orderByRaw('((min_stock_alert * 2 - current_stock) * purchase_rate) DESC');
        } else {
            $query->orderByRaw('(min_stock_alert - current_stock) DESC');
        }

        // Calculate KPI Metrics across all alert items
        $allItemsAlert = Item::where('status', 'active')
            ->whereColumn('current_stock', '<=', 'min_stock_alert')
            ->get();

        $totalAlertItems = $allItemsAlert->count();
        $outOfStockCount = $allItemsAlert->where('current_stock', '<=', 0)->count();
        $lowStockCount = $totalAlertItems - $outOfStockCount;

        $totalShortfallQty = 0;
        $totalSuggestedReorderQty = 0;
        $totalEstimatedCapital = 0;

        foreach ($allItemsAlert as $itm) {
            $cur = (float)$itm->current_stock;
            $min = (float)$itm->min_stock_alert;
            $rate = (float)$itm->purchase_rate;

            $shortfall = max(0, $min - $cur);
            $suggested = max($min, ($min * 2) - $cur);

            $totalShortfallQty += $shortfall;
            $totalSuggestedReorderQty += $suggested;
            $totalEstimatedCapital += ($suggested * $rate);
        }

        $stats = [
            'total_alerts'          => $totalAlertItems,
            'out_of_stock_count'    => $outOfStockCount,
            'low_stock_count'       => $lowStockCount,
            'total_shortfall_qty'   => $totalShortfallQty,
            'total_suggested_qty'   => $totalSuggestedReorderQty,
            'estimated_capital'     => $totalEstimatedCapital,
        ];

        // Export to CSV if requested
        if ($request->input('export') === 'csv') {
            return $this->exportLowStockCsv($query->get());
        }

        $items = $query->paginate(15)->withQueryString();

        // Attach last vendor details and reorder calculations
        foreach ($items as $item) {
            $lastPurchaseItem = PurchaseItem::with(['purchase.vendor'])
                ->where('item_id', $item->id)
                ->whereHas('purchase', function ($q) {
                    $q->whereNull('deleted_at')->where('status', '!=', 'cancelled');
                })
                ->latest()
                ->first();

            $item->last_vendor = $lastPurchaseItem && $lastPurchaseItem->purchase && $lastPurchaseItem->purchase->vendor
                ? $lastPurchaseItem->purchase->vendor
                : null;
            $item->last_rate = $lastPurchaseItem ? (float)$lastPurchaseItem->actual_rate : (float)$item->purchase_rate;
            $item->shortfall_qty = max(0, (float)$item->min_stock_alert - (float)$item->current_stock);
            $item->suggested_qty = max((float)$item->min_stock_alert, ((float)$item->min_stock_alert * 2) - (float)$item->current_stock);
            $item->reorder_cost = $item->suggested_qty * (float)$item->purchase_rate;

            $item->stock_pct = ($item->min_stock_alert > 0)
                ? min(100, max(0, round(($item->current_stock / $item->min_stock_alert) * 100)))
                : 0;
        }

        $categories = Item::distinct()->whereNotNull('category')->pluck('category')->sort()->values();

        return view('admin.inventory.low-stock-alert', [
            'items'       => $items,
            'stats'       => $stats,
            'categories'  => $categories,
            'filters'     => $request->all(),
            'pageTitle'   => 'Low Stock Alert Center - VIKAS UDHYOG ERP',
            'pageCode'    => 'inv-low-stock',
        ]);
    }

    /**
     * Export low stock items procurement requisition to CSV.
     */
    protected function exportLowStockCsv($items): StreamedResponse
    {
        $filename = "low_stock_reorder_requisition_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['VIKAS UDHYOG ERP - LOW STOCK PROCUREMENT REQUISITION']);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            fputcsv($file, [
                'Item Code',
                'Product Name',
                'Category',
                'Current Stock',
                'Min Stock Level',
                'Shortfall / Deficit',
                'Recommended Reorder Qty',
                'Unit',
                'Unit Purchase Rate (INR)',
                'Estimated Reorder Cost (INR)',
                'Urgency Status',
            ]);

            foreach ($items as $item) {
                $cur = (float)$item->current_stock;
                $min = (float)$item->min_stock_alert;
                $rate = (float)$item->purchase_rate;
                $shortfall = max(0, $min - $cur);
                $suggested = max($min, ($min * 2) - $cur);
                $status = $cur <= 0 ? 'CRITICAL - OUT OF STOCK' : ($cur <= ($min * 0.5) ? 'URGENT - BELOW 50% MIN' : 'WARNING - LOW STOCK');

                fputcsv($file, [
                    $item->code,
                    $item->name,
                    $item->category ?: 'General',
                    number_format($cur, 2, '.', ''),
                    number_format($min, 2, '.', ''),
                    number_format($shortfall, 2, '.', ''),
                    number_format($suggested, 2, '.', ''),
                    $item->unit,
                    number_format($rate, 2, '.', ''),
                    number_format($suggested * $rate, 2, '.', ''),
                    $status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
