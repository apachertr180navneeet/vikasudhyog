<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies with filtering & stats.
     */
    public function index(Request $request)
    {
        $query = Company::query();

        // Search Filter
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('pan', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $companies = $query->orderBy('is_default', 'desc')
                           ->orderBy('created_at', 'desc')
                           ->paginate(10)
                           ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'     => Company::count(),
            'active'    => Company::where('status', 'active')->count(),
            'inactive'  => Company::where('status', 'inactive')->count(),
            'default'   => Company::where('is_default', true)->first(),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'companies' => $companies,
                'stats'     => $stats,
            ]);
        }

        return view('admin.masters.company.index', [
            'pageTitle'  => 'Company Master - VIKAS UDHYOG ERP',
            'pageCode'   => 'master-company',
            'companies'  => $companies,
            'stats'      => $stats,
            'filters'    => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('admin.masters.company.create', [
            'pageTitle' => 'Add New Company - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-company',
        ]);
    }

    /**
     * Generate a unique short code from company name via AJAX.
     */
    public function generateCode(Request $request)
    {
        $name = trim((string)$request->query('name', ''));
        $excludeId = $request->query('exclude_id') ? (int)$request->query('exclude_id') : null;

        if (empty($name)) {
            return response()->json([
                'success' => true,
                'code'    => '',
            ]);
        }

        $code = Company::generateUniqueCode($name, $excludeId);

        return response()->json([
            'success' => true,
            'code'    => $code,
        ]);
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => ['nullable', 'string', 'max:30', Rule::unique('companies', 'code')->whereNull('deleted_at')],
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:15',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:100',
            'website'         => 'nullable|string|max:150',
            'address'         => 'nullable|string',
            'city'            => 'required|string|max:60',
            'state'           => 'required|string|max:60',
            'pincode'         => 'nullable|string|max:15',
            'financial_year'  => 'nullable|string|max:20',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:25',
            'bank_branch'     => 'nullable|string|max:100',
            'tagline'         => 'nullable|string|max:255',
            'status'          => 'nullable|in:active,inactive',
            'is_default'      => 'nullable|boolean',
        ]);

        $validated['status'] = $validated['status'] ?? 'active';

        if (empty($validated['code'])) {
            $validated['code'] = Company::generateUniqueCode($validated['name']);
        } else {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $isDefault = $request->boolean('is_default');
        
        // If this is the first company, make it default automatically
        if (Company::count() === 0) {
            $isDefault = true;
        }

        $validated['is_default'] = false;
        $company = Company::create($validated);

        if ($isDefault) {
            $company->makeDefault();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company profile created successfully!',
                'company' => $company,
            ]);
        }

        return redirect()->route('admin.masters.company')
            ->with('success', 'Company profile "' . $company->name . '" created successfully.');
    }

    /**
     * Display the specified company profile.
     */
    public function show(Request $request, Company $company)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'company' => $company,
            ]);
        }

        return view('admin.masters.company.show', [
            'pageTitle' => $company->name . ' - Company Profile',
            'pageCode'  => 'master-company',
            'company'   => $company,
        ]);
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        return view('admin.masters.company.edit', [
            'pageTitle' => 'Edit ' . $company->name . ' - Company Master',
            'pageCode'  => 'master-company',
            'company'   => $company,
        ]);
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'code'            => ['nullable', 'string', 'max:30', Rule::unique('companies', 'code')->whereNull('deleted_at')->ignore($company->id)],
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:15',
            'phone'           => 'nullable|string|max:30',
            'email'           => 'nullable|email|max:100',
            'website'         => 'nullable|string|max:150',
            'address'         => 'nullable|string',
            'city'            => 'required|string|max:60',
            'state'           => 'required|string|max:60',
            'pincode'         => 'nullable|string|max:15',
            'financial_year'  => 'nullable|string|max:20',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:25',
            'bank_branch'     => 'nullable|string|max:100',
            'tagline'         => 'nullable|string|max:255',
            'status'          => 'nullable|in:active,inactive',
            'is_default'      => 'nullable|boolean',
        ]);

        if (!isset($validated['status'])) {
            unset($validated['status']);
        }

        if (empty($validated['code'])) {
            $validated['code'] = Company::generateUniqueCode($validated['name'], $company->id);
        } else {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $isDefault = $request->boolean('is_default');
        unset($validated['is_default']);

        $company->update($validated);

        if ($isDefault) {
            $company->makeDefault();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company profile updated successfully!',
                'company' => $company->fresh(),
            ]);
        }

        return redirect()->route('admin.masters.company')
            ->with('success', 'Company "' . $company->name . '" updated successfully.');
    }

    /**
     * Remove the specified company from storage.
     */
    public function destroy(Request $request, Company $company)
    {
        $companyName = $company->name;
        $wasDefault = $company->is_default;

        $company->delete();

        // If deleted company was default, assign default to first remaining active company
        if ($wasDefault) {
            $nextCompany = Company::where('status', 'active')->first() ?? Company::first();
            if ($nextCompany) {
                $nextCompany->makeDefault();
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company profile deleted successfully!',
            ]);
        }

        return redirect()->route('admin.masters.company')
            ->with('success', 'Company "' . $companyName . '" has been deleted.');
    }

    /**
     * Toggle company status (active/inactive).
     */
    public function toggleStatus(Request $request, Company $company)
    {
        $newStatus = $company->status === 'active' ? 'inactive' : 'active';
        $company->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Company status updated to ' . ucfirst($newStatus),
                'status'  => $newStatus,
            ]);
        }

        return back()->with('success', 'Company status changed to ' . ucfirst($newStatus));
    }

    /**
     * Set company as primary/default.
     */
    public function setDefault(Request $request, Company $company)
    {
        $company->makeDefault();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Set "' . $company->name . '" as primary active company.',
            ]);
        }

        return back()->with('success', 'Default company updated to "' . $company->name . '".');
    }
}
