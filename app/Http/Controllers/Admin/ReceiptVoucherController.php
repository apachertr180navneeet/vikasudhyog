<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Customer;
use App\Models\ReceiptVoucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceiptVoucherController extends Controller
{
    /**
     * Standard list of default income sources.
     */
    public const INCOME_SOURCES = [
        'Scrap & Waste Sale',
        'Bank Interest',
        'Rental Income',
        'Commission Received',
        'Freight Recovery',
        'Discount Received',
        'Other Miscellaneous Income',
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
     * Display a listing of receipt vouchers.
     */
    public function index(Request $request)
    {
        $query = ReceiptVoucher::with(['customer', 'account']);

        // Search
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('against_invoice', 'like', "%{$search}%")
                  ->orWhere('income_source', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Receipt Type filter
        if ($type = $request->input('receipt_type')) {
            $query->where('receipt_type', $type);
        }

        // Payment Mode filter
        if ($mode = $request->input('payment_mode')) {
            $query->where('payment_mode', $mode);
        }

        // Customer filter
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Receiving Account filter
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
        $totalCount = ReceiptVoucher::count();
        $totalCollected = ReceiptVoucher::where('status', 'active')->sum('amount');
        $customerCollections = ReceiptVoucher::where('status', 'active')->where('receipt_type', 'Customer')->sum('amount');
        $directIncomes = ReceiptVoucher::where('status', 'active')->where('receipt_type', 'Income')->sum('amount');

        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 'active')->orderBy('name')->get();

        return view('admin.transactions.receipt-vouchers.index', compact(
            'vouchers',
            'totalCount',
            'totalCollected',
            'customerCollections',
            'directIncomes',
            'customers',
            'accounts'
        ));
    }

    /**
     * Show the form for creating a new receipt voucher.
     */
    public function create()
    {
        $nextVoucherNo = ReceiptVoucher::generateNextVoucherNo();
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 'active')->orderBy('name')->get();
        $incomeSources = self::INCOME_SOURCES;
        $paymentModes = self::PAYMENT_MODES;

        return view('admin.transactions.receipt-vouchers.create', compact(
            'nextVoucherNo',
            'customers',
            'accounts',
            'incomeSources',
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
            'code'    => ReceiptVoucher::generateNextVoucherNo(),
        ]);
    }

    /**
     * Store a newly created receipt voucher in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'voucher_no'      => 'required|string|max:50|unique:receipt_vouchers,voucher_no',
            'voucher_date'    => 'required|date',
            'receipt_type'    => 'required|in:Customer,Income',
            'customer_id'     => 'required_if:receipt_type,Customer|nullable|exists:customers,id',
            'income_source'   => 'required_if:receipt_type,Income|nullable|string|max:150',
            'account_id'      => 'nullable|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode'    => 'required|string|max:30',
            'reference_no'    => 'nullable|string|max:100',
            'reference_date'  => 'nullable|date',
            'against_invoice' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // Rule 4: New records always default to 'active' automatically on backend
            $validated['status'] = 'active';
            $validated['created_by'] = Auth::id();

            // Clear opposite field based on receipt_type
            if ($validated['receipt_type'] === 'Customer') {
                $validated['income_source'] = null;
            } else {
                $validated['customer_id'] = null;
            }

            $voucher = ReceiptVoucher::create($validated);

            // Deduct customer balance (collection reduces customer debt)
            if ($voucher->receipt_type === 'Customer' && $voucher->customer_id) {
                Customer::where('id', $voucher->customer_id)->decrement('current_balance', $voucher->amount);
            }

            // Increase receiving bank or cash account balance
            if ($voucher->account_id) {
                Account::where('id', $voucher->account_id)->increment('current_balance', $voucher->amount);
            }

            return redirect()
                ->route('admin.transactions.receipt-voucher')
                ->with('success', "Receipt Voucher '{$voucher->voucher_no}' recorded successfully!");
        });
    }

    /**
     * Display the specified receipt voucher.
     */
    public function show(ReceiptVoucher $receiptVoucher)
    {
        $receiptVoucher->load(['customer', 'account', 'creator']);

        return view('admin.transactions.receipt-vouchers.show', compact('receiptVoucher'));
    }

    /**
     * Show the form for editing the specified receipt voucher.
     */
    public function edit(ReceiptVoucher $receiptVoucher)
    {
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $accounts = Account::where('status', 'active')->orderBy('name')->get();
        $incomeSources = self::INCOME_SOURCES;
        $paymentModes = self::PAYMENT_MODES;

        return view('admin.transactions.receipt-vouchers.edit', compact(
            'receiptVoucher',
            'customers',
            'accounts',
            'incomeSources',
            'paymentModes'
        ));
    }

    /**
     * Update the specified receipt voucher in storage.
     */
    public function update(Request $request, ReceiptVoucher $receiptVoucher)
    {
        $validated = $request->validate([
            'voucher_no'      => 'required|string|max:50|unique:receipt_vouchers,voucher_no,' . $receiptVoucher->id,
            'voucher_date'    => 'required|date',
            'receipt_type'    => 'required|in:Customer,Income',
            'customer_id'     => 'required_if:receipt_type,Customer|nullable|exists:customers,id',
            'income_source'   => 'required_if:receipt_type,Income|nullable|string|max:150',
            'account_id'      => 'nullable|exists:accounts,id',
            'amount'          => 'required|numeric|min:0.01',
            'payment_mode'    => 'required|string|max:30',
            'reference_no'    => 'nullable|string|max:100',
            'reference_date'  => 'nullable|date',
            'against_invoice' => 'nullable|string|max:100',
            'notes'           => 'nullable|string|max:1000',
        ]);

        return DB::transaction(function () use ($validated, $receiptVoucher) {
            // Reverse previous balance adjustments if voucher was active
            if ($receiptVoucher->status === 'active') {
                if ($receiptVoucher->receipt_type === 'Customer' && $receiptVoucher->customer_id) {
                    Customer::where('id', $receiptVoucher->customer_id)->increment('current_balance', $receiptVoucher->amount);
                }
                if ($receiptVoucher->account_id) {
                    Account::where('id', $receiptVoucher->account_id)->decrement('current_balance', $receiptVoucher->amount);
                }
            }

            // Clear opposite field
            if ($validated['receipt_type'] === 'Customer') {
                $validated['income_source'] = null;
            } else {
                $validated['customer_id'] = null;
            }

            $receiptVoucher->update($validated);

            // Apply new balance adjustments if voucher remains active
            if ($receiptVoucher->status === 'active') {
                if ($receiptVoucher->receipt_type === 'Customer' && $receiptVoucher->customer_id) {
                    Customer::where('id', $receiptVoucher->customer_id)->decrement('current_balance', $receiptVoucher->amount);
                }
                if ($receiptVoucher->account_id) {
                    Account::where('id', $receiptVoucher->account_id)->increment('current_balance', $receiptVoucher->amount);
                }
            }

            return redirect()
                ->route('admin.transactions.receipt-voucher')
                ->with('success', "Receipt Voucher '{$receiptVoucher->voucher_no}' updated successfully!");
        });
    }

    /**
     * Toggle receipt voucher status between active and cancelled.
     */
    public function toggleStatus(ReceiptVoucher $receiptVoucher)
    {
        return DB::transaction(function () use ($receiptVoucher) {
            if ($receiptVoucher->status === 'active') {
                // Cancel voucher -> restore customer balance and revert bank account
                if ($receiptVoucher->receipt_type === 'Customer' && $receiptVoucher->customer_id) {
                    Customer::where('id', $receiptVoucher->customer_id)->increment('current_balance', $receiptVoucher->amount);
                }
                if ($receiptVoucher->account_id) {
                    Account::where('id', $receiptVoucher->account_id)->decrement('current_balance', $receiptVoucher->amount);
                }

                $receiptVoucher->update(['status' => 'cancelled']);
                $message = "Receipt Voucher '{$receiptVoucher->voucher_no}' has been cancelled and balances restored.";
            } else {
                // Reactivate voucher -> re-apply customer deduction and account deposit
                if ($receiptVoucher->receipt_type === 'Customer' && $receiptVoucher->customer_id) {
                    Customer::where('id', $receiptVoucher->customer_id)->decrement('current_balance', $receiptVoucher->amount);
                }
                if ($receiptVoucher->account_id) {
                    Account::where('id', $receiptVoucher->account_id)->increment('current_balance', $receiptVoucher->amount);
                }

                $receiptVoucher->update(['status' => 'active']);
                $message = "Receipt Voucher '{$receiptVoucher->voucher_no}' has been reactivated.";
            }

            return redirect()->back()->with('success', $message);
        });
    }

    /**
     * Remove the specified receipt voucher from storage.
     */
    public function destroy(ReceiptVoucher $receiptVoucher)
    {
        return DB::transaction(function () use ($receiptVoucher) {
            // Revert balances if deleting an active voucher
            if ($receiptVoucher->status === 'active') {
                if ($receiptVoucher->receipt_type === 'Customer' && $receiptVoucher->customer_id) {
                    Customer::where('id', $receiptVoucher->customer_id)->increment('current_balance', $receiptVoucher->amount);
                }
                if ($receiptVoucher->account_id) {
                    Account::where('id', $receiptVoucher->account_id)->decrement('current_balance', $receiptVoucher->amount);
                }
            }

            $voucherNo = $receiptVoucher->voucher_no;
            $receiptVoucher->delete();

            return redirect()
                ->route('admin.transactions.receipt-voucher')
                ->with('success', "Receipt Voucher '{$voucherNo}' has been archived successfully!");
        });
    }
}
