<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * Standard ERP Chart of Accounts Groups.
     */
    public const ACCOUNT_GROUPS = [
        'Bank Accounts',
        'Cash in Hand',
        'Direct Incomes',
        'Indirect Incomes',
        'Direct Expenses',
        'Indirect Expenses',
        'Current Assets',
        'Current Liabilities',
        'Duties & Taxes',
        'Fixed Assets',
        'Capital & Reserves',
    ];

    /**
     * Display a listing of chart of accounts ledgers, bank & cash accounts.
     */
    public function index(Request $request)
    {
        $query = Account::with('company');

        // Search Filter
        if ($search = trim((string)$request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('bank_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhere('upi_id', 'like', "%{$search}%")
                  ->orWhere('account_group', 'like', "%{$search}%");
            });
        }

        // Status Filter
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Account Group Filter
        if ($group = $request->input('group')) {
            if ($group !== 'all') {
                $query->where('account_group', $group);
            }
        }

        $accounts = $query->orderBy('account_group', 'asc')
                          ->orderBy('name', 'asc')
                          ->paginate(10)
                          ->withQueryString();

        // 4 KPI Summary Statistics
        $stats = [
            'total'              => Account::count(),
            'active'             => Account::where('status', 'active')->count(),
            'total_bank_balance' => Account::where('account_group', 'Bank Accounts')->sum('current_balance'),
            'total_cash_balance' => Account::where('account_group', 'Cash in Hand')->sum('current_balance'),
        ];

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'accounts' => $accounts,
                'stats'    => $stats,
            ]);
        }

        return view('admin.masters.account.index', [
            'pageTitle' => 'Account Master - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-account',
            'accounts'  => $accounts,
            'stats'     => $stats,
            'groups'    => self::ACCOUNT_GROUPS,
            'filters'   => [
                'search' => $request->input('search', ''),
                'status' => $request->input('status', 'all'),
                'group'  => $request->input('group', 'all'),
            ]
        ]);
    }

    /**
     * Show the form for creating a new account ledger.
     */
    public function create()
    {
        $suggestedCode = Account::generateUniqueCode('ACC');
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.account.create', [
            'pageTitle'     => 'Add New Account - VIKAS UDHYOG ERP',
            'pageCode'      => 'master-account',
            'suggestedCode' => $suggestedCode,
            'companies'     => $companies,
            'groups'        => self::ACCOUNT_GROUPS,
        ]);
    }

    /**
     * AJAX endpoint to generate intelligent code based on account group.
     */
    public function generateCode(Request $request)
    {
        $group = $request->query('group', '');
        $prefix = match ($group) {
            'Bank Accounts'                         => 'BNK',
            'Cash in Hand'                          => 'CSH',
            'Direct Expenses', 'Indirect Expenses'  => 'EXP',
            'Direct Incomes', 'Indirect Incomes'    => 'INC',
            'Duties & Taxes'                        => 'TAX',
            default                                 => 'ACC',
        };

        $excludeId = $request->query('exclude_id') ? (int)$request->query('exclude_id') : null;
        $code = Account::generateUniqueCode($prefix, $excludeId);

        return response()->json([
            'success' => true,
            'code'    => $code,
            'prefix'  => $prefix,
        ]);
    }

    /**
     * Store a newly created account in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'            => 'required|string|max:30|unique:accounts,code',
            'name'            => 'required|string|max:150',
            'account_group'   => 'required|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'balance_type'    => 'required|in:debit,credit',
            'company_id'      => 'nullable|exists:companies,id',
            'bank_name'       => 'nullable|string|max:100',
            'account_number'  => 'nullable|string|max:50',
            'ifsc_code'       => 'nullable|string|max:20',
            'branch_name'     => 'nullable|string|max:100',
            'upi_id'          => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['ifsc_code'])) {
            $validated['ifsc_code'] = strtoupper(trim($validated['ifsc_code']));
        }

        // Automatic default status per ERP guidelines: No status field in forms
        $validated['status'] = 'active';
        $validated['opening_balance'] = (float)($validated['opening_balance'] ?? 0.00);
        $validated['current_balance'] = $validated['opening_balance'];

        $account = Account::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account ledger "' . $account->name . ' (' . $account->code . ')" registered successfully.',
                'account' => $account,
            ]);
        }

        return redirect()
            ->route('admin.masters.account')
            ->with('success', 'Account ledger "' . $account->name . ' (' . $account->code . ')" registered successfully.');
    }

    /**
     * Display the specified account dossier.
     */
    public function show(Request $request, Account $account)
    {
        $account->load('company');

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'account' => $account,
            ]);
        }

        return view('admin.masters.account.show', [
            'pageTitle' => $account->name . ' (' . $account->code . ') - Account Dossier',
            'pageCode'  => 'master-account',
            'account'   => $account,
        ]);
    }

    /**
     * Show the form for editing the specified account.
     */
    public function edit(Account $account)
    {
        $companies = Company::active()->orderBy('name')->get();

        return view('admin.masters.account.edit', [
            'pageTitle' => 'Edit Account: ' . $account->name . ' - VIKAS UDHYOG ERP',
            'pageCode'  => 'master-account',
            'account'   => $account,
            'companies' => $companies,
            'groups'    => self::ACCOUNT_GROUPS,
        ]);
    }

    /**
     * Update the specified account in storage.
     */
    public function update(Request $request, Account $account)
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:30', Rule::unique('accounts', 'code')->ignore($account->id)],
            'name'            => 'required|string|max:150',
            'account_group'   => 'required|string|max:50',
            'opening_balance' => 'nullable|numeric|min:0',
            'current_balance' => 'nullable|numeric',
            'balance_type'    => 'required|in:debit,credit',
            'company_id'      => 'nullable|exists:companies,id',
            'bank_name'       => 'nullable|string|max:100',
            'account_number'  => 'nullable|string|max:50',
            'ifsc_code'       => 'nullable|string|max:20',
            'branch_name'     => 'nullable|string|max:100',
            'upi_id'          => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        if (!empty($validated['ifsc_code'])) {
            $validated['ifsc_code'] = strtoupper(trim($validated['ifsc_code']));
        }

        if (isset($validated['current_balance'])) {
            $validated['current_balance'] = (float)$validated['current_balance'];
        } elseif (isset($validated['opening_balance'])) {
            $diff = (float)$validated['opening_balance'] - (float)$account->opening_balance;
            $validated['current_balance'] = (float)$account->current_balance + $diff;
        }

        $account->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account profile for "' . $account->name . '" updated successfully.',
                'account' => $account,
            ]);
        }

        return redirect()
            ->route('admin.masters.account')
            ->with('success', 'Account profile for "' . $account->name . ' (' . $account->code . ')" updated successfully.');
    }

    /**
     * Soft delete the specified account from storage.
     */
    public function destroy(Request $request, Account $account)
    {
        $accountName = $account->name;
        $accountCode = $account->code;
        $account->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Account ledger "' . $accountName . ' (' . $accountCode . ')" has been archived.',
            ]);
        }

        return redirect()
            ->route('admin.masters.account')
            ->with('success', 'Account ledger "' . $accountName . ' (' . $accountCode . ')" has been archived.');
    }

    /**
     * Toggle active/inactive status.
     */
    public function toggleStatus(Request $request, Account $account)
    {
        $newStatus = $account->status === 'active' ? 'inactive' : 'active';
        $account->update(['status' => $newStatus]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status'  => $newStatus,
                'message' => 'Status changed to ' . ucfirst($newStatus) . '.',
            ]);
        }

        return back()->with('success', 'Account status updated to ' . ucfirst($newStatus) . '.');
    }
}
