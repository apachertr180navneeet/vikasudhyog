<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransporterController extends Controller
{
    /**
     * Display a listing of transporters with search, filter, and KPI statistics.
     */
    public function index(Request $request)
    {
        $query = Transporter::with('company');

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('transporter_id_gstin', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // City Filter
        if ($city = $request->input('city')) {
            if ($city !== 'all') {
                $query->where('city', $city);
            }
        }

        // Sorting
        $transporters = $query->orderBy('is_active', 'desc')
                              ->orderBy('name', 'asc')
                              ->paginate(10)
                              ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'        => Transporter::count(),
            'active'       => Transporter::where('is_active', true)->count(),
            'inactive'     => Transporter::where('is_active', false)->count(),
            'cities_count' => Transporter::distinct('city')->whereNotNull('city')->count('city'),
        ];

        $cities = Transporter::whereNotNull('city')->distinct()->pluck('city')->sort();

        return view('admin.masters.transporter.index', compact('transporters', 'stats', 'cities'));
    }

    /**
     * Show the form for creating a new transporter.
     */
    public function create()
    {
        return view('admin.masters.transporter.create');
    }

    /**
     * Store a newly created transporter in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:150',
            'transporter_id_gstin' => 'nullable|string|max:50',
            'phone'                => 'nullable|string|max:30',
            'city'                 => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:500',
        ]);

        $company = Company::first();

        $transporter = Transporter::create([
            'company_id'           => $company ? $company->id : null,
            'name'                 => $validated['name'],
            'transporter_id_gstin' => $validated['transporter_id_gstin'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'city'                 => $validated['city'] ?? null,
            'notes'                => $validated['notes'] ?? null,
            'is_active'            => true,
        ]);

        return redirect()->route('admin.masters.transporter')
            ->with('success', "Transporter '{$transporter->name}' created successfully.");
    }

    /**
     * Fast AJAX store for inline transporter addition in transactions.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:150',
            'transporter_id_gstin' => 'nullable|string|max:50',
            'phone'                => 'nullable|string|max:30',
            'city'                 => 'nullable|string|max:100',
        ]);

        $company = Company::first();

        $transporter = Transporter::create([
            'company_id'           => $company ? $company->id : null,
            'name'                 => $validated['name'],
            'transporter_id_gstin' => $validated['transporter_id_gstin'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'city'                 => $validated['city'] ?? null,
            'is_active'            => true,
        ]);

        return response()->json([
            'success'     => true,
            'transporter' => $transporter,
            'message'     => 'Transporter added successfully.',
        ]);
    }

    /**
     * Display the specified transporter.
     */
    public function show(Transporter $transporter)
    {
        return view('admin.masters.transporter.show', compact('transporter'));
    }

    /**
     * Show the form for editing the specified transporter.
     */
    public function edit(Transporter $transporter)
    {
        return view('admin.masters.transporter.edit', compact('transporter'));
    }

    /**
     * Update the specified transporter in storage.
     */
    public function update(Request $request, Transporter $transporter)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:150',
            'transporter_id_gstin' => 'nullable|string|max:50',
            'phone'                => 'nullable|string|max:30',
            'city'                 => 'nullable|string|max:100',
            'notes'                => 'nullable|string|max:500',
        ]);

        $transporter->update([
            'name'                 => $validated['name'],
            'transporter_id_gstin' => $validated['transporter_id_gstin'] ?? null,
            'phone'                => $validated['phone'] ?? null,
            'city'                 => $validated['city'] ?? null,
            'notes'                => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.masters.transporter')
            ->with('success', "Transporter '{$transporter->name}' updated successfully.");
    }

    /**
     * Remove the specified transporter from storage.
     */
    public function destroy(Transporter $transporter)
    {
        $name = $transporter->name;
        $transporter->delete();

        return redirect()->route('admin.masters.transporter')
            ->with('success', "Transporter '{$name}' archived successfully.");
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Transporter $transporter)
    {
        $transporter->is_active = !$transporter->is_active;
        $transporter->save();

        $statusLabel = $transporter->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.masters.transporter')
            ->with('success', "Transporter '{$transporter->name}' {$statusLabel} successfully.");
    }
}
