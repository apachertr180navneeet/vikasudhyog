<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\PaymentVoucher;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentVoucherController extends Controller
{
    /**
     * Standard list of default operational expense heads.
     */
    public const EXPENSE_HEADS = [
        'Freight & Transportation',
        'Electricity & Power Bill',
        'Staff Salary & Wages',
        'Office & Factory Rent',
        'Maintenance & Repairs',
        'Packaging Material & Bags',
        'Tea, Water & Refreshments',
        'Printing & Stationery',
        'Fuel & Vehicle Expenses',
        'Other Operational Expenses',
    ];

    /**
     * Payment modes available.
     */
    public const PAYMENT_MODES = [
        'Cash',
        'Bank Transfer',
        'NEFT',
        'RTGS',
        'Cheque',
        'UPI',
        'Demand Draft',
        'Other',
    ];

    /**
     * Display a listing of payment vouchers.
     */
    public function index(Request $request)
    {
        $query = PaymentVoucher::with(['vendor', 'account']);

        // Search
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('against_invoice', 'like', "%{$search}%")
                  ->orWhere('expense_head', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Payment Type filter
        if ($type = $request->input('payment_type')) {
            $query->where('payment_type', $type);
        }

        // Payment Mode filter
        if ($mode = $request->input('payment_mode')) {
            $query->where('payment_mode', $mode);
        }

        // Vendor filter
        if ($vendorId = $request->input('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        // Paying Account filter
        if ($accountId = $request->input('account_id')) {
            $query->where('account_id', $accountId);
        }

        // Status filter
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Date Range filters
        if ($startDate = $request->input('start_date')) {
            $query->whereDate('voucher_date', '>=', $startDate);
        }
        if ($endDate = $request->input('end_date')) {
            $query->whereDate('voucher_date', '<=', $endDate);
        }

        $vouchers = $query->orderBy('voucher_date', 'desc')
                          ->orderBy('id', 'desc')
                          ->paginate(10)
                          ->withQueryString();

        // KPIs calculation
        $totalCount = PaymentVoucher::count();
        $totalDisbursed = PaymentVoucher::where('status', 'active')->sum('amount');
        $vendorDisbursements = PaymentVoucher::where('status', 'active')->where('payment_type', 'Vendor')->sum('amount');
        $directExpenses = PaymentVoucher::where('status', 'active')->where('payment_type', 'Expense')->sum('amount');

        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.payment-vouchers.index', compact(
            'vouchers',
            'totalCount',
            'totalDisbursed',
            'vendorDisbursements',
            'directExpenses',
            'vendors',
            'accounts'
        ));
    }

    /**
     * Show the form for creating a new payment voucher.
     */
    public function create()
    {
        $nextVoucherNo = PaymentVoucher::generateNextVoucherNo();
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 'active')->orderBy('name')->get();
        $expenseHeads = self::EXPENSE_HEADS;
        $paymentModes = self::PAYMENT_MODES;

        return view('admin.transactions.payment-vouchers.create', compact(
            'nextVoucherNo',
            'vendors',
            'accounts',
            'expenseHeads',
            'paymentModes'
        ));
    }

    /**
     * Generate next sequential voucher code via AJAX.
     */
    public function generateCode()
    {
        return response()->json([
            'success' => true,
            'code'    => PaymentVoucher::generateNextVoucherNo(),
        ]);
    }

    /**
     * Store a newly created payment voucher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_no'      => 'required|string|max:50|unique:payment_vouchers,voucher_no',
            'voucher_date'    => 'required|date',
            'payment_type'    => 'required|in:Vendor,Expense',
            'vendor_id'       => 'required_if:payment_type,Vendor|nullable|exists:vendors,id',
            'expense_head'    => 'required_if:payment_type,Expense|nullable|string|max:150',
            'account_id'      => 'nullable|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode'    => 'required|string|max:30',
            'reference_no'    => 'nullable|string|max:100',
            'reference_date'  => 'nullable|date',
            'against_invoice' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated) {
            // Rule 4: New records always default to 'active' automatically on backend
            $validated['status'] = 'active';
            $validated['created_by'] = Auth::id();

            // Clear opposite field based on payment_type
            if ($validated['payment_type'] === 'Vendor') {
                $validated['expense_head'] = null;
            } else {
                $validated['vendor_id'] = null;
            }

            $voucher = PaymentVoucher::create($validated);

            // Deduct vendor balance (payment reduces company payable debt to vendor)
            if ($voucher->payment_type === 'Vendor' && $voucher->vendor_id) {
                Vendor::where('id', $voucher->vendor_id)->decrement('current_balance', $voucher->amount);
            }

            // Deduct paying bank or cash account balance (funds disbursed)
            if ($voucher->account_id) {
                Account::where('id', $voucher->account_id)->decrement('current_balance', $voucher->amount);
            }

            return redirect()
                ->route('admin.transactions.payment-voucher')
                ->with('success', "Payment Voucher '{$voucher->voucher_no}' recorded successfully!");
        });
    }

    /**
     * Display the specified payment voucher.
     */
    public function show(PaymentVoucher $paymentVoucher)
    {
        $paymentVoucher->load(['vendor', 'account', 'creator']);

        return view('admin.transactions.payment-vouchers.show', compact('paymentVoucher'));
    }

    /**
     * Show the form for editing the specified payment voucher.
     */
    public function edit(PaymentVoucher $paymentVoucher)
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 'active')->orderBy('name')->get();
        $expenseHeads = self::EXPENSE_HEADS;
        $paymentModes = self::PAYMENT_MODES;

        return view('admin.transactions.payment-vouchers.edit', compact(
            'paymentVoucher',
            'vendors',
            'accounts',
            'expenseHeads',
            'paymentModes'
        ));
    }

    /**
     * Update the specified payment voucher in storage.
     */
    public function update(Request $request, PaymentVoucher $paymentVoucher)
    {
        $validated = $request->validate([
            'voucher_no'      => 'required|string|max:50|unique:payment_vouchers,voucher_no,' . $paymentVoucher->id,
            'voucher_date'    => 'required|date',
            'payment_type'    => 'required|in:Vendor,Expense',
            'vendor_id'       => 'required_if:payment_type,Vendor|nullable|exists:vendors,id',
            'expense_head'    => 'required_if:payment_type,Expense|nullable|string|max:150',
            'account_id'      => 'nullable|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode'    => 'required|string|max:30',
            'reference_no'    => 'nullable|string|max:100',
            'reference_date'  => 'nullable|date',
            'against_invoice' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $paymentVoucher) {
            // Reverse previous balance adjustments if voucher was active
            if ($paymentVoucher->status === 'active') {
                if ($paymentVoucher->payment_type === 'Vendor' && $paymentVoucher->vendor_id) {
                    Vendor::where('id', $paymentVoucher->vendor_id)->increment('current_balance', $paymentVoucher->amount);
                }
                if ($paymentVoucher->account_id) {
                    Account::where('id', $paymentVoucher->account_id)->increment('current_balance', $paymentVoucher->amount);
                }
            }

            // Clear opposite field
            if ($validated['payment_type'] === 'Vendor') {
                $validated['expense_head'] = null;
            } else {
                $validated['vendor_id'] = null;
            }

            $paymentVoucher->update($validated);

            // Apply new balance adjustments if voucher remains active
            if ($paymentVoucher->status === 'active') {
                if ($paymentVoucher->payment_type === 'Vendor' && $paymentVoucher->vendor_id) {
                    Vendor::where('id', $paymentVoucher->vendor_id)->decrement('current_balance', $paymentVoucher->amount);
                }
                if ($paymentVoucher->account_id) {
                    Account::where('id', $paymentVoucher->account_id)->decrement('current_balance', $paymentVoucher->amount);
                }
            }

            return redirect()
                ->route('admin.transactions.payment-voucher')
                ->with('success', "Payment Voucher '{$paymentVoucher->voucher_no}' updated successfully!");
        });
    }

    /**
     * Toggle payment voucher status between active and cancelled.
     */
    public function toggleStatus(PaymentVoucher $paymentVoucher)
    {
        return DB::transaction(function () use ($paymentVoucher) {
            if ($paymentVoucher->status === 'active') {
                // Cancel voucher -> restore vendor payable debt and refund bank account
                if ($paymentVoucher->payment_type === 'Vendor' && $paymentVoucher->vendor_id) {
                    Vendor::where('id', $paymentVoucher->vendor_id)->increment('current_balance', $paymentVoucher->amount);
                }
                if ($paymentVoucher->account_id) {
                    Account::where('id', $paymentVoucher->account_id)->increment('current_balance', $paymentVoucher->amount);
                }

                $paymentVoucher->update(['status' => 'cancelled']);
                $message = "Payment Voucher '{$paymentVoucher->voucher_no}' has been cancelled and balances restored.";
            } else {
                // Reactivate voucher -> re-apply vendor deduction and account outflow
                if ($paymentVoucher->payment_type === 'Vendor' && $paymentVoucher->vendor_id) {
                    Vendor::where('id', $paymentVoucher->vendor_id)->decrement('current_balance', $paymentVoucher->amount);
                }
                if ($paymentVoucher->account_id) {
                    Account::where('id', $paymentVoucher->account_id)->decrement('current_balance', $paymentVoucher->amount);
                }

                $paymentVoucher->update(['status' => 'active']);
                $message = "Payment Voucher '{$paymentVoucher->voucher_no}' has been reactivated.";
            }

            return redirect()->back()->with('success', $message);
        });
    }

    /**
     * Remove the specified payment voucher from storage.
     */
    public function destroy(PaymentVoucher $paymentVoucher)
    {
        return DB::transaction(function () use ($paymentVoucher) {
            // Revert balances if deleting an active voucher
            if ($paymentVoucher->status === 'active') {
                if ($paymentVoucher->payment_type === 'Vendor' && $paymentVoucher->vendor_id) {
                    Vendor::where('id', $paymentVoucher->vendor_id)->increment('current_balance', $paymentVoucher->amount);
                }
                if ($paymentVoucher->account_id) {
                    Account::where('id', $paymentVoucher->account_id)->increment('current_balance', $paymentVoucher->amount);
                }
            }

            $voucherNo = $paymentVoucher->voucher_no;
            $paymentVoucher->delete();

            return redirect()
                ->route('admin.transactions.payment-voucher')
                ->with('success', "Payment Voucher '{$voucherNo}' has been archived successfully!");
        });
    }
}
