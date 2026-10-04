<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UnitController extends Controller
{
    /**
     * Standard GST Unique Quantity Codes (UQC).
     */
    public const UQC_CODES = [
        'KGS' => 'KGS - KILOGRAMS',
        'GMS' => 'GMS - GRAMS',
        'QTL' => 'QTL - QUINTAL',
        'MTR' => 'MTR - METERS',
        'CMS' => 'CMS - CENTIMETERS',
        'BGS' => 'BGS - BAGS',
        'BOX' => 'BOX - BOXES',
        'PAC' => 'PAC - PACKETS',
        'NOS' => 'NOS - NUMBERS / PIECES',
        'LTR' => 'LTR - LITRES',
        'MLT' => 'MLT - MILLILITRES',
        'TON' => 'TON - METRIC TON',
    ];

    /**
     * Display a listing of measurement units with conversion relation statistics.
     */
    public function index(Request $request)
    {
        $query = Unit::with('baseUnit');

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('uqc_code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Unit Type Filter (Base Unit vs Derived Relation Unit)
        if ($type = $request->input('type')) {
            if ($type === 'base') {
                $query->where(function ($q) {
                    $q->where('is_base_unit', true)
                      ->orWhereNull('base_unit_id');
                });
            } elseif ($type === 'derived') {
                $query->where('is_base_unit', false)
                      ->whereNotNull('base_unit_id');
            }
        }

        $units = $query->orderBy('is_base_unit', 'desc')
                       ->orderBy('name', 'asc')
                       ->paginate(10)
                       ->withQueryString();

        // 4 KPI Statistical Counters
        $stats = [
            'total'          => Unit::count(),
            'active'         => Unit::where('status', 'active')->count(),
            'base_units'     => Unit::baseUnits()->count(),
            'derived_units'  => Unit::where('is_base_unit', false)->whereNotNull('base_unit_id')->count(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'units'   => $units,
                'stats'   => $stats,
            ]);
        }

        return view('admin.masters.unit.index', [
            'pageTitle' => 'Unit Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-unit',
            'units'     => $units,
            'stats'     => $stats,
            'filters'   => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
                'type'   => $request->input('type', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new measurement unit with relation logic.
     */
    public function create()
    {
        $baseUnits = Unit::baseUnits()->active()->orderBy('name')->get();

        return view('admin.masters.unit.create', [
            'pageTitle' => 'Add New Unit - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-unit',
            'baseUnits' => $baseUnits,
            'uqcCodes'  => self::UQC_CODES,
        ]);
    }

    /**
     * Store a newly created measurement unit in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'code'              => 'required|string|max:20|unique:units,code',
            'uqc_code'          => 'nullable|string|max:20',
            'decimal_places'    => 'required|integer|min:0|max:4',
            'base_unit_id'      => 'nullable|exists:units,id',
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'operator'          => 'nullable|in:/,*',
            'description'       => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['uqc_code'])) {
            $validated['uqc_code'] = strtoupper(trim($validated['uqc_code']));
        }

        // Automatic default status per ERP guidelines: No status field in forms
        $validated['status'] = 'active';

        // Unit Relation Logic: If base_unit_id is selected, this is a derived unit
        if (!empty($validated['base_unit_id'])) {
            $validated['is_base_unit'] = false;
            $validated['conversion_factor'] = (float)($validated['conversion_factor'] ?? 1.0000);
            $validated['operator'] = $validated['operator'] ?? '/';
        } else {
            $validated['is_base_unit'] = true;
            $validated['base_unit_id'] = null;
            $validated['conversion_factor'] = 1.0000;
            $validated['operator'] = '/';
        }

        $unit = Unit::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Measurement unit "' . $unit->name . ' (' . $unit->code . ')" registered successfully.',
                'unit'    => $unit,
            ]);
        }

        return redirect()
            ->route('admin.masters.unit')
            ->with('success', 'Measurement unit "' . $unit->name . ' (' . $unit->code . ')" registered successfully.');
    }

    /**
     * Display the specified unit dossier with live conversion calculator.
     */
    public function show(Request $request, Unit $unit)
    {
        $unit->load(['baseUnit', 'subUnits']);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'unit'    => $unit,
            ]);
        }

        return view('admin.masters.unit.show', [
            'pageTitle' => $unit->name . ' (' . $unit->code . ') - Unit Dossier',
            'pageCode'  => 'master-unit',
            'unit'      => $unit,
        ]);
    }

    /**
     * Show the form for editing the specified unit.
     */
    public function edit(Unit $unit)
    {
        // Prevent setting itself as base unit to avoid circular relation
        $baseUnits = Unit::where('id', '!=', $unit->id)
                         ->active()
                         ->orderBy('name')
                         ->get();

        return view('admin.masters.unit.edit', [
            'pageTitle' => 'Edit Unit: ' . $unit->name . ' - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-unit',
            'unit'      => $unit,
            'baseUnits' => $baseUnits,
            'uqcCodes'  => self::UQC_CODES,
        ]);
    }

    /**
     * Update the specified unit in storage.
     */
    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:100',
            'code'              => ['required', 'string', 'max:20', Rule::unique('units', 'code')->ignore($unit->id)],
            'uqc_code'          => 'nullable|string|max:20',
            'decimal_places'    => 'required|integer|min:0|max:4',
            'base_unit_id'      => ['nullable', 'exists:units,id', Rule::notIn([$unit->id])],
            'conversion_factor' => 'nullable|numeric|min:0.0001',
            'operator'          => 'nullable|in:/,*',
            'description'       => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['uqc_code'])) {
            $validated['uqc_code'] = strtoupper(trim($validated['uqc_code']));
        }

        if (!empty($validated['base_unit_id'])) {
            $validated['is_base_unit'] = false;
            $validated['conversion_factor'] = (float)($validated['conversion_factor'] ?? 1.0000);
            $validated['operator'] = $validated['operator'] ?? '/';
        } else {
            $validated['is_base_unit'] = true;
            $validated['base_unit_id'] = null;
            $validated['conversion_factor'] = 1.0000;
            $validated['operator'] = '/';
        }

        $unit->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Unit profile for "' . $unit->name . ' (' . $unit->code . ')" updated successfully.',
                'unit'    => $unit,
            ]);
        }

        return redirect()
            ->route('admin.masters.unit')
            ->with('success', 'Unit profile for "' . $unit->name . ' (' . $unit->code . ')" updated successfully.');
    }

    /**
     * Soft delete the specified unit from storage.
     */
    public function destroy(Request $request, Unit $unit)
    {
        $unitName = $unit->name;
        $unitCode = $unit->code;
        $unit->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Measurement unit "' . $unitName . ' (' . $unitCode . ')" has been archived.',
            ]);
        }

        return redirect()
            ->route('admin.masters.unit')
            ->with('success', 'Measurement unit "' . $unitName . ' (' . $unitCode . ')" has been archived.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Unit $unit)
    {
        $newStatus = $unit->status === 'active' ? 'inactive' : 'active';
        $unit->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Status changed to ' . ucfirst($newStatus) . '.',
            ]);
        }

        return back()->with('success', 'Unit status updated to ' . ucfirst($newStatus) . '.');
    }
}
