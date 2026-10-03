<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VendorController extends Controller
{
    /**
     * Display a listing of vendors with filtering, search, and KPI statistics.
     */
    public function index(Request $request)
    {
        $query = Vendor::with('company');

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // City Filter
        if ($city = $request->input('city')) {
            if ($city !== 'all') {
                $query->where('city', $city);
            }
        }

        // Sorting
        $vendors = $query->orderBy('status', 'asc')
                         ->orderBy('name', 'asc')
                         ->paginate(10)
                         ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'         => Vendor::count(),
            'active'        => Vendor::where('status', 'active')->count(),
            'total_payable' => Vendor::sum('current_balance'),
            'cities_count'  => Vendor::distinct('city')->whereNotNull('city')->count('city'),
        ];

        // Available cities for quick filter
        $cities = Vendor::distinct('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city')
            ->pluck('city');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'vendors' => $vendors,
                'stats'   => $stats,
            ]);
        }

        return view('admin.masters.vendor.index', [
            'pageTitle' => 'Vendor Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-vendor',
            'vendors'   => $vendors,
            'stats'     => $stats,
            'cities'    => $cities,
            'filters'   => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
                'city'   => $request->input('city', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $suggestedCode = Vendor::generateUniqueCode('VND');
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.vendor.create', [
            'pageTitle'     => 'Add New Vendor - VIKAS UDHYOG ERP',
            'pageCode'      => 'master-vendor',
            'suggestedCode' => $suggestedCode,
            'companies'     => $companies,
        ]);
    }

    /**
     * Generate an intelligent unique vendor code via AJAX.
     */
    public function generateCode(Request $request)
    {
        $prefix = $request->query('prefix', 'VND');
        $excludeId = $request->query('exclude_id') ? (int)$request->query('exclude_id') : null;

        $code = Vendor::generateUniqueCode($prefix, $excludeId);

        return response()->json([
            'success' => true,
            'code'    => $code,
        ]);
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:30|unique:vendors,code',
            'name'            => 'required|string|max:150',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:15',
            'opening_balance' => 'nullable|numeric|min:0',
            'payment_terms'   => 'nullable|string|max:50',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:25',
            'bank_branch'     => 'nullable|string|max:100',
            'company_id'      => 'nullable|exists:companies,id',
            'status'          => 'nullable|in:active,inactive',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['status'] = $validated['status'] ?? 'active';
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;
        $validated['current_balance'] = $validated['opening_balance']; // Initial balance matches opening balance

        if (!empty($validated['gstin'])) {
            $validated['gstin'] = strtoupper(trim($validated['gstin']));
        }
        if (!empty($validated['pan'])) {
            $validated['pan'] = strtoupper(trim($validated['pan']));
        }

        $vendor = Vendor::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor account "' . $vendor->name . ' (' . $vendor->code . ')" created successfully.',
                'vendor'  => $vendor,
            ]);
        }

        return redirect()
            ->route('admin.masters.vendor')
            ->with('success', 'Vendor account "' . $vendor->name . ' (' . $vendor->code . ')" created successfully.');
    }

    /**
     * Display the specified vendor profile.
     */
    public function show(Request $request, Vendor $vendor)
    {
        $vendor->load('company');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'vendor'  => $vendor,
            ]);
        }

        return view('admin.masters.vendor.show', [
            'pageTitle' => $vendor->name . ' (' . $vendor->code . ') - Vendor Profile',
            'pageCode'  => 'master-vendor',
            'vendor'    => $vendor,
        ]);
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.vendor.edit', [
            'pageTitle' => 'Edit Vendor: ' . $vendor->name . ' - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-vendor',
            'vendor'    => $vendor,
            'companies' => $companies,
        ]);
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:30', Rule::unique('vendors', 'code')->ignore($vendor->id)],
            'name'            => 'required|string|max:150',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:15',
            'opening_balance' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
            'payment_terms'   => 'nullable|string|max:50',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:25',
            'bank_branch'     => 'nullable|string|max:100',
            'company_id'      => 'nullable|exists:companies,id',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['gstin'])) {
            $validated['gstin'] = strtoupper(trim($validated['gstin']));
        }
        if (!empty($validated['pan'])) {
            $validated['pan'] = strtoupper(trim($validated['pan']));
        }
        if (isset($validated['opening_balance'])) {
            $validated['opening_balance'] = (float)$validated['opening_balance'];
        }
        if (isset($validated['current_balance'])) {
            $validated['current_balance'] = (float)$validated['current_balance'];
        } elseif (isset($validated['opening_balance'])) {
            $diff = (float)$validated['opening_balance'] - (float)$vendor->opening_balance;
            $validated['current_balance'] = max(0, (float)$vendor->current_balance + $diff);
        }

        $vendor->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor details for "' . $vendor->name . '" updated successfully.',
                'vendor'  => $vendor,
            ]);
        }

        return redirect()
            ->route('admin.masters.vendor')
            ->with('success', 'Vendor profile for "' . $vendor->name . ' (' . $vendor->code . ')" updated successfully.');
    }

    /**
     * Soft delete the specified vendor from storage.
     */
    public function destroy(Request $request, Vendor $vendor)
    {
        $vendorName = $vendor->name;
        $vendorCode = $vendor->code;
        $vendor->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Vendor "' . $vendorName . ' (' . $vendorCode . ')" has been archived.',
            ]);
        }

        return redirect()
            ->route('admin.masters.vendor')
            ->with('success', 'Vendor account "' . $vendorName . ' (' . $vendorCode . ')" has been archived.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Vendor $vendor)
    {
        $newStatus = $vendor->status === 'active' ? 'inactive' : 'active';
        $vendor->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Status changed to ' . ucfirst($newStatus) . '.',
            ]);
        }

        return back()->with('success', 'Vendor status updated to ' . ucfirst($newStatus) . '.');
    }
}
