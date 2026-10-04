<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    /**
     * Customer Types list.
     */
    public const CUSTOMER_TYPES = [
        'Distributor',
        'Wholesaler',
        'Retailer',
        'Direct Client',
        'Exporter'
    ];

    /**
     * Display a listing of customers with filtering, search, and KPI statistics.
     */
    public function index(Request $request)
    {
        $query = Customer::with('company');

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('gstin', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('customer_type', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Customer Type Filter
        if ($type = $request->input('type')) {
            if ($type !== 'all') {
                $query->where('customer_type', $type);
            }
        }

        // City Filter
        if ($city = $request->input('city')) {
            if ($city !== 'all') {
                $query->where('city', $city);
            }
        }

        // Sorting
        $customers = $query->orderBy('status', 'asc')
                           ->orderBy('name', 'asc')
                           ->paginate(10)
                           ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'             => Customer::count(),
            'active'            => Customer::where('status', 'active')->count(),
            'total_receivables' => Customer::sum('current_balance'),
            'total_credit_pool' => Customer::sum('credit_limit'),
            'cities_count'      => Customer::distinct('city')->whereNotNull('city')->count('city'),
        ];

        // Available cities for filter dropdown
        $cities = Customer::distinct('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city')
            ->pluck('city');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'   => true,
                'customers' => $customers,
                'stats'     => $stats,
            ]);
        }

        return view('admin.masters.customer.index', [
            'pageTitle' => 'Customer Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-customer',
            'customers' => $customers,
            'stats'     => $stats,
            'cities'    => $cities,
            'types'     => self::CUSTOMER_TYPES,
            'filters'   => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
                'type'   => $request->input('type', 'all'),
                'city'   => $request->input('city', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new customer.
     */
    public function create()
    {
        $suggestedCode = Customer::generateUniqueCode('CST');
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.customer.create', [
            'pageTitle'     => 'Add New Customer - VIKAS UDHYOG ERP',
            'pageCode'      => 'master-customer',
            'suggestedCode' => $suggestedCode,
            'companies'     => $companies,
            'types'         => self::CUSTOMER_TYPES,
        ]);
    }

    /**
     * Generate an intelligent unique customer code via AJAX.
     */
    public function generateCode(Request $request)
    {
        $prefix = $request->query('prefix', 'CST');
        $excludeId = $request->query('exclude_id') ? (int)$request->query('exclude_id') : null;

        $code = Customer::generateUniqueCode($prefix, $excludeId);

        return response()->json([
            'success' => true,
            'code'    => $code,
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:30|unique:customers,code',
            'name'            => 'required|string|max:150',
            'customer_type'   => 'required|string|max:50',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:15',
            'credit_limit'    => 'nullable|numeric|min:0',
            'payment_terms'   => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:25',
            'bank_branch'     => 'nullable|string|max:100',
            'company_id'      => 'nullable|exists:companies,id',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        // No status field in form: all new records default to active
        $validated['status'] = 'active';
        $validated['credit_limit'] = $validated['credit_limit'] ?? 300000.00;
        $validated['payment_terms'] = $validated['payment_terms'] ?: '30 Days';
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;
        $validated['current_balance'] = $validated['opening_balance'];

        if (!empty($validated['gstin'])) {
            $validated['gstin'] = strtoupper(trim($validated['gstin']));
        }
        if (!empty($validated['pan'])) {
            $validated['pan'] = strtoupper(trim($validated['pan']));
        }

        $customer = Customer::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Customer account "' . $customer->name . ' (' . $customer->code . ')" registered successfully.',
                'customer' => $customer,
            ]);
        }

        return redirect()
            ->route('admin.masters.customer')
            ->with('success', 'Customer account "' . $customer->name . ' (' . $customer->code . ')" registered successfully.');
    }

    /**
     * Display the specified customer profile.
     */
    public function show(Request $request, Customer $customer)
    {
        $customer->load('company');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'customer' => $customer,
            ]);
        }

        return view('admin.masters.customer.show', [
            'pageTitle' => $customer->name . ' (' . $customer->code . ') - Customer Profile',
            'pageCode'  => 'master-customer',
            'customer'  => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified customer.
     */
    public function edit(Customer $customer)
    {
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.customer.edit', [
            'pageTitle' => 'Edit Customer: ' . $customer->name . ' - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-customer',
            'customer'  => $customer,
            'companies' => $companies,
            'types'     => self::CUSTOMER_TYPES,
        ]);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:30', Rule::unique('customers', 'code')->ignore($customer->id)],
            'name'            => 'required|string|max:150',
            'customer_type'   => 'required|string|max:50',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'gstin'           => 'nullable|string|max:20',
            'pan'             => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:15',
            'credit_limit'    => 'nullable|numeric|min:0',
            'payment_terms'   => 'nullable|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric|min:0',
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
        if (isset($validated['credit_limit'])) {
            $validated['credit_limit'] = (float)$validated['credit_limit'];
        }
        if (isset($validated['opening_balance'])) {
            $validated['opening_balance'] = (float)$validated['opening_balance'];
        }

        // Adjust current balance if opening balance changed and current balance was not explicitly sent
        if (isset($validated['current_balance'])) {
            $validated['current_balance'] = (float)$validated['current_balance'];
        } elseif (isset($validated['opening_balance'])) {
            $diff = (float)$validated['opening_balance'] - (float)$customer->opening_balance;
            $validated['current_balance'] = max(0, (float)$customer->current_balance + $diff);
        }

        $customer->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'message'  => 'Customer profile for "' . $customer->name . '" updated successfully.',
                'customer' => $customer,
            ]);
        }

        return redirect()
            ->route('admin.masters.customer')
            ->with('success', 'Customer profile for "' . $customer->name . ' (' . $customer->code . ')" updated successfully.');
    }

    /**
     * Soft delete the specified customer from storage.
     */
    public function destroy(Request $request, Customer $customer)
    {
        $customerName = $customer->name;
        $customerCode = $customer->code;
        $customer->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Customer account "' . $customerName . ' (' . $customerCode . ')" has been archived.',
            ]);
        }

        return redirect()
            ->route('admin.masters.customer')
            ->with('success', 'Customer account "' . $customerName . ' (' . $customerCode . ')" has been archived.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Customer $customer)
    {
        $newStatus = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Status changed to ' . ucfirst($newStatus) . '.',
            ]);
        }

        return back()->with('success', 'Customer status updated to ' . ucfirst($newStatus) . '.');
    }
}
