<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
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

    public function itemLedger()
    {
        return view('admin.inventory.item-ledger', [
            'pageTitle' => 'Item Ledger - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-ledger'
        ]);
    }

    public function stockAdjustment()
    {
        return view('admin.inventory.stock-adjustment', [
            'pageTitle' => 'Stock Adjustment - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-adjustment'
        ]);
    }

    public function lowStockAlert()
    {
        return view('admin.inventory.low-stock-alert', [
            'pageTitle' => 'Low Stock Alert - VIKAS UDHYOG ERP',
            'pageCode'  => 'inv-low-stock'
        ]);
    }
}
