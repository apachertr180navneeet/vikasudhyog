<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Company;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    /**
     * Item Categories.
     */
    public const CATEGORIES = [
        'Mehndi / Henna',
        'Herbal Powder',
        'Ayurvedic Raw Material',
        'Finished Product',
        'Packaging Material'
    ];

    /**
     * Common GST Tax Rates.
     */
    public const GST_RATES = [
        0.00,
        5.00,
        12.00,
        18.00,
        28.00
    ];

    /**
     * Display a listing of product items with search, filters & statistics.
     */
    public function index(Request $request)
    {
        $query = Item::with(['company', 'unitRelation']);

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('hsn_code', 'like', "%{$search}%")
                  ->orWhere('batch_no', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Category Filter
        if ($category = $request->input('category')) {
            if ($category !== 'all') {
                $query->where('category', $category);
            }
        }

        // Unit Filter
        if ($unit = $request->input('unit')) {
            if ($unit !== 'all') {
                $query->where(function ($q) use ($unit) {
                    $q->where('unit', $unit)
                      ->orWhere('unit_id', $unit);
                });
            }
        }

        // Stock Status Filter
        if ($stockStatus = $request->input('stock_status')) {
            if ($stockStatus === 'low_stock') {
                $query->whereColumn('current_stock', '<=', 'min_stock_alert');
            } elseif ($stockStatus === 'in_stock') {
                $query->whereColumn('current_stock', '>', 'min_stock_alert');
            }
        }

        // Sorting
        $items = $query->orderBy('status', 'asc')
                       ->orderBy('name', 'asc')
                       ->paginate(10)
                       ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'           => Item::count(),
            'active'          => Item::where('status', 'active')->count(),
            'total_valuation' => Item::selectRaw('SUM(current_stock * purchase_rate) as total')->value('total') ?? 0,
            'low_stock_count' => Item::lowStock()->count(),
        ];

        $units = Unit::active()->orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'items'   => $items,
                'stats'   => $stats,
            ]);
        }

        return view('admin.masters.item.index', [
            'pageTitle'  => 'Item Master - VIKAS UDHYOG ERP',
            'pageCode'   => 'master-item',
            'items'      => $items,
            'stats'      => $stats,
            'categories' => self::CATEGORIES,
            'units'      => $units,
            'filters'    => [
                'search'       => $request->input('search', ''),
                'status'       => $request->input('status', 'all'),
                'category'     => $request->input('category', 'all'),
                'unit'         => $request->input('unit', 'all'),
                'stock_status' => $request->input('stock_status', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new item.
     */
    public function create()
    {
        $suggestedCode = Item::generateUniqueCode('ITM');
        $companies = Company::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('admin.masters.item.create', [
            'pageTitle'     => 'Add New Item - VIKAS UDHYOG ERP',
            'pageCode'      => 'master-item',
            'suggestedCode' => $suggestedCode,
            'companies'     => $companies,
            'categories'    => self::CATEGORIES,
            'units'         => $units,
            'gstRates'      => self::GST_RATES,
        ]);
    }

    /**
     * Generate an intelligent unique item code via AJAX.
     */
    public function generateCode(Request $request)
    {
        $prefix = $request->query('prefix', 'ITM');
        $excludeId = $request->query('exclude_id') ? (int)$request->query('exclude_id') : null;

        $code = Item::generateUniqueCode($prefix, $excludeId);

        return response()->json([
            'success' => true,
            'code'    => $code,
        ]);
    }

    /**
     * Store a newly created item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:30|unique:items,code',
            'name'            => 'required|string|max:150',
            'category'        => 'required|string|max:50',
            'unit'            => 'nullable|string|max:20',
            'unit_id'         => 'nullable|exists:units,id',
            'hsn_code'        => 'nullable|string|max:20',
            'gst_rate'        => 'nullable|numeric|min:0|max:100',
            'purchase_rate'   => 'nullable|numeric|min:0',
            'sale_rate'       => 'nullable|numeric|min:0',
            'opening_stock'   => 'nullable|numeric|min:0',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'batch_no'        => 'nullable|string|max:50',
            'company_id'      => 'nullable|exists:companies,id',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['hsn_code'])) {
            $validated['hsn_code'] = strtoupper(trim($validated['hsn_code']));
        }

        // Resolve unit_id and unit code/name
        if (!empty($validated['unit_id'])) {
            $unitModel = Unit::find($validated['unit_id']);
            if ($unitModel) {
                $validated['unit'] = $unitModel->code ?: $unitModel->name;
            }
        } elseif (!empty($validated['unit'])) {
            $unitModel = Unit::where('code', $validated['unit'])
                ->orWhere('name', $validated['unit'])
                ->first();
            if ($unitModel) {
                $validated['unit_id'] = $unitModel->id;
                $validated['unit'] = $unitModel->code ?: $unitModel->name;
            }
        } else {
            $defaultUnit = Unit::active()->first();
            if ($defaultUnit) {
                $validated['unit_id'] = $defaultUnit->id;
                $validated['unit'] = $defaultUnit->code ?: $defaultUnit->name;
            } else {
                $validated['unit'] = 'KG';
            }
        }

        // Automatic defaults per guidelines: no status field in form
        $validated['status'] = 'active';
        $validated['gst_rate'] = $validated['gst_rate'] ?? 5.00;
        $validated['purchase_rate'] = $validated['purchase_rate'] ?? 0.00;
        $validated['sale_rate'] = $validated['sale_rate'] ?? 0.00;
        $validated['opening_stock'] = $validated['opening_stock'] ?? 0.00;
        $validated['current_stock'] = $validated['opening_stock'];
        $validated['min_stock_alert'] = $validated['min_stock_alert'] ?? 20.00;

        $item = Item::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product item "' . $item->name . ' (' . $item->code . ')" registered successfully.',
                'item'    => $item,
            ]);
        }

        return redirect()
            ->route('admin.masters.item')
            ->with('success', 'Product item "' . $item->name . ' (' . $item->code . ')" registered successfully.');
    }

    /**
     * Display the specified item dossier.
     */
    public function show(Request $request, Item $item)
    {
        $item->load(['company', 'unitRelation']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'item'    => $item,
            ]);
        }

        return view('admin.masters.item.show', [
            'pageTitle' => $item->name . ' (' . $item->code . ') - Item Dossier',
            'pageCode'  => 'master-item',
            'item'      => $item,
        ]);
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        $item->load('unitRelation');
        $companies = Company::active()->orderBy('name')->get();
        $units = Unit::active()->orderBy('name')->get();

        return view('admin.masters.item.edit', [
            'pageTitle'  => 'Edit Item: ' . $item->name . ' - VIKAS UDHYOG ERP',
            'pageCode'   => 'master-item',
            'item'       => $item,
            'companies'  => $companies,
            'categories' => self::CATEGORIES,
            'units'      => $units,
            'gstRates'   => self::GST_RATES,
        ]);
    }

    /**
     * Update the specified item in storage.
     */
    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:30', Rule::unique('items', 'code')->ignore($item->id)],
            'name'            => 'required|string|max:150',
            'category'        => 'required|string|max:50',
            'unit'            => 'nullable|string|max:20',
            'unit_id'         => 'nullable|exists:units,id',
            'hsn_code'        => 'nullable|string|max:20',
            'gst_rate'        => 'nullable|numeric|min:0|max:100',
            'purchase_rate'   => 'nullable|numeric|min:0',
            'sale_rate'       => 'nullable|numeric|min:0',
            'opening_stock'   => 'nullable|numeric|min:0',
            'current_stock'   => 'nullable|numeric|min:0',
            'min_stock_alert' => 'nullable|numeric|min:0',
            'batch_no'        => 'nullable|string|max:50',
            'company_id'      => 'nullable|exists:companies,id',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['hsn_code'])) {
            $validated['hsn_code'] = strtoupper(trim($validated['hsn_code']));
        }

        // Resolve unit_id and unit code/name
        if (!empty($validated['unit_id'])) {
            $unitModel = Unit::find($validated['unit_id']);
            if ($unitModel) {
                $validated['unit'] = $unitModel->code ?: $unitModel->name;
            }
        } elseif (!empty($validated['unit'])) {
            $unitModel = Unit::where('code', $validated['unit'])
                ->orWhere('name', $validated['unit'])
                ->first();
            if ($unitModel) {
                $validated['unit_id'] = $unitModel->id;
                $validated['unit'] = $unitModel->code ?: $unitModel->name;
            }
        }

        if (isset($validated['current_stock'])) {
            $validated['current_stock'] = (float)$validated['current_stock'];
        } elseif (isset($validated['opening_stock'])) {
            $diff = (float)$validated['opening_stock'] - (float)$item->opening_stock;
            $validated['current_stock'] = max(0, (float)$item->current_stock + $diff);
        }

        $item->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item profile for "' . $item->name . '" updated successfully.',
                'item'    => $item,
            ]);
        }

        return redirect()
            ->route('admin.masters.item')
            ->with('success', 'Item profile for "' . $item->name . ' (' . $item->code . ')" updated successfully.');
    }

    /**
     * Soft delete the specified item from storage.
     */
    public function destroy(Request $request, Item $item)
    {
        $itemName = $item->name;
        $itemCode = $item->code;
        $item->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product item "' . $itemName . ' (' . $itemCode . ')" has been archived.',
            ]);
        }

        return redirect()
            ->route('admin.masters.item')
            ->with('success', 'Product item "' . $itemName . ' (' . $itemCode . ')" has been archived.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Item $item)
    {
        $newStatus = $item->status === 'active' ? 'inactive' : 'active';
        $item->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Status changed to ' . ucfirst($newStatus) . '.',
            ]);
        }

        return back()->with('success', 'Item status updated to ' . ucfirst($newStatus) . '.');
    }
}
