<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Broker;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BrokerController extends Controller
{
    /**
     * Display a listing of brokers with search, filter, and KPI statistics.
     */
    public function index(Request $request)
    {
        $query = Broker::with('company');

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('pan', 'like', "%{$search}%")
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
        $brokers = $query->orderBy('status', 'asc')
                         ->orderBy('name', 'asc')
                         ->paginate(10)
                         ->withQueryString();

        // Statistical KPI Counters
        $stats = [
            'total'                    => Broker::count(),
            'active'                   => Broker::where('status', 'active')->count(),
            'total_commission_payable' => Broker::sum('current_balance'),
            'cities_count'             => Broker::distinct('city')->whereNotNull('city')->count('city'),
        ];

        // Available cities for filter dropdown
        $cities = Broker::distinct('city')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->orderBy('city')
            ->pluck('city');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'brokers' => $brokers,
                'stats'   => $stats,
            ]);
        }

        return view('admin.masters.broker.index', [
            'pageTitle' => 'Broker Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-broker',
            'brokers'   => $brokers,
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
     * Show the form for creating a new broker.
     */
    public function create()
    {
        $suggestedCode = Broker::generateUniqueCode('BRK');
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.broker.create', [
            'pageTitle'     => 'Add New Broker - VIKAS UDHYOG ERP',
            'pageCode'      => 'master-broker',
            'suggestedCode' => $suggestedCode,
            'companies'     => $companies,
        ]);
    }

    /**
     * Generate an intelligent unique broker code via AJAX.
     */
    public function generateCode(Request $request)
    {
        $prefix = $request->query('prefix', 'BRK');
        $excludeId = $request->query('exclude_id') ? (int)$request->query('exclude_id') : null;

        $code = Broker::generateUniqueCode($prefix, $excludeId);

        return response()->json([
            'success' => true,
            'code'    => $code,
        ]);
    }

    /**
     * Store a newly created broker in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:30|unique:brokers,code',
            'name'            => 'required|string|max:150',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'brokerage_type'  => 'nullable|string|max:50',
            'pan'             => 'nullable|string|max:20',
            'gstin'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:15',
            'opening_balance' => 'nullable|numeric|min:0',
            'bank_name'       => 'nullable|string|max:100',
            'bank_account_no' => 'nullable|string|max:50',
            'bank_ifsc'       => 'nullable|string|max:25',
            'bank_branch'     => 'nullable|string|max:100',
            'company_id'      => 'nullable|exists:companies,id',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['status'] = 'active'; // Always defaults to active on creation
        $validated['opening_balance'] = $validated['opening_balance'] ?? 0.00;
        $validated['current_balance'] = $validated['opening_balance']; // Initial balance matches opening balance
        $validated['brokerage_type'] = $validated['brokerage_type'] ?? 'Percentage (%)';

        if (!empty($validated['pan'])) {
            $validated['pan'] = strtoupper(trim($validated['pan']));
        }
        if (!empty($validated['gstin'])) {
            $validated['gstin'] = strtoupper(trim($validated['gstin']));
        }

        $broker = Broker::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Broker account "' . $broker->name . ' (' . $broker->code . ')" created successfully.',
                'broker'  => $broker,
            ]);
        }

        return redirect()
            ->route('admin.masters.broker')
            ->with('success', 'Broker account "' . $broker->name . ' (' . $broker->code . ')" created successfully.');
    }

    /**
     * Display the specified broker profile.
     */
    public function show(Request $request, Broker $broker)
    {
        $broker->load('company');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'broker'  => $broker,
            ]);
        }

        return view('admin.masters.broker.show', [
            'pageTitle' => $broker->name . ' (' . $broker->code . ') - Broker Profile',
            'pageCode'  => 'master-broker',
            'broker'    => $broker,
        ]);
    }

    /**
     * Show the form for editing the specified broker.
     */
    public function edit(Broker $broker)
    {
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.broker.edit', [
            'pageTitle' => 'Edit Broker: ' . $broker->name . ' - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-broker',
            'broker'    => $broker,
            'companies' => $companies,
        ]);
    }

    /**
     * Update the specified broker in storage.
     */
    public function update(Request $request, Broker $broker)
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:30', Rule::unique('brokers', 'code')->ignore($broker->id)],
            'name'            => 'required|string|max:150',
            'contact_person'  => 'nullable|string|max:100',
            'phone'           => 'nullable|string|max:25',
            'email'           => 'nullable|email|max:150',
            'commission_rate' => 'required|numeric|min:0|max:100',
            'brokerage_type'  => 'nullable|string|max:50',
            'pan'             => 'nullable|string|max:20',
            'gstin'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:500',
            'city'            => 'nullable|string|max:100',
            'state'           => 'nullable|string|max:100',
            'pincode'         => 'nullable|string|max:15',
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
        $validated['brokerage_type'] = $validated['brokerage_type'] ?? 'Percentage (%)';

        if (!empty($validated['pan'])) {
            $validated['pan'] = strtoupper(trim($validated['pan']));
        }
        if (!empty($validated['gstin'])) {
            $validated['gstin'] = strtoupper(trim($validated['gstin']));
        }

        // Keep current balance in sync if explicitly edited or if opening balance adjusted
        if (isset($validated['current_balance'])) {
            $validated['current_balance'] = (float)$validated['current_balance'];
        } elseif (isset($validated['opening_balance']) && (float)$broker->opening_balance !== (float)$validated['opening_balance']) {
            $diff = (float)$validated['opening_balance'] - (float)$broker->opening_balance;
            $validated['current_balance'] = max(0, (float)$broker->current_balance + $diff);
        }

        $broker->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Broker account "' . $broker->name . ' (' . $broker->code . ')" updated successfully.',
                'broker'  => $broker,
            ]);
        }

        return redirect()
            ->route('admin.masters.broker')
            ->with('success', 'Broker account "' . $broker->name . ' (' . $broker->code . ')" updated successfully.');
    }

    /**
     * Toggle active/inactive status of the broker.
     */
    public function toggleStatus(Request $request, Broker $broker)
    {
        $broker->status = $broker->status === 'active' ? 'inactive' : 'active';
        $broker->save();

        $statusLabel = ucfirst($broker->status);
        $message = "Broker account '{$broker->name}' is now {$statusLabel}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $broker->status,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Remove the specified broker from storage (Soft Delete).
     */
    public function destroy(Request $request, Broker $broker)
    {
        $name = $broker->name;
        $code = $broker->code;

        $broker->delete();

        $message = "Broker '{$name} ({$code})' has been archived successfully.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message,
            ]);
        }

        return redirect()
            ->route('admin.masters.broker')
            ->with('success', $message);
    }
}
