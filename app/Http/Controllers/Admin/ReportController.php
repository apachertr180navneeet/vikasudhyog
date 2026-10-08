<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Broker;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Item;
use App\Models\PaymentVoucher;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\ReceiptVoucher;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Vendor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Display the comprehensive Purchase Report & Analytical Procurement Register.
     */
    public function purchaseReport(Request $request)
    {
        // 1. Determine active report mode: 'bills', 'items', 'vendors'
        $viewMode = $request->input('view_mode', 'bills');

        // 2. Base Query
        $query = Purchase::with(['vendor', 'broker', 'company', 'items.item'])
            ->whereNull('deleted_at');

        // Filter: Status (non-cancelled by default unless explicitly chosen)
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        } else {
            $query->where('status', '!=', 'cancelled');
        }

        // Filter: Bill Type
        if ($billType = $request->input('bill_type')) {
            if ($billType !== 'all') {
                $query->where('bill_type', $billType);
            }
        }

        // Filter: Vendor
        if ($vendorId = $request->input('vendor_id')) {
            $query->where('vendor_id', $vendorId);
        }

        // Filter: Broker
        if ($brokerId = $request->input('broker_id')) {
            $query->where('broker_id', $brokerId);
        }

        // Filter: Payment Status
        if ($paymentStatus = $request->input('payment_status')) {
            if ($paymentStatus !== 'all') {
                $query->where('payment_status', $paymentStatus);
            }
        }

        // Filter: Item SKU
        if ($itemId = $request->input('item_id')) {
            $query->whereHas('items', function ($iq) use ($itemId) {
                $iq->where('item_id', $itemId);
            });
        }

        // Date Presets & Custom Date Range
        $datePreset = $request->input('date_preset', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($datePreset !== 'all' && $datePreset !== 'custom') {
            $now = Carbon::now();
            if ($datePreset === 'today') {
                $fromDate = $now->toDateString();
                $toDate = $now->toDateString();
            } elseif ($datePreset === 'yesterday') {
                $fromDate = $now->copy()->subDay()->toDateString();
                $toDate = $now->copy()->subDay()->toDateString();
            } elseif ($datePreset === 'this_week') {
                $fromDate = $now->copy()->startOfWeek()->toDateString();
                $toDate = $now->copy()->endOfWeek()->toDateString();
            } elseif ($datePreset === 'this_month') {
                $fromDate = $now->copy()->startOfMonth()->toDateString();
                $toDate = $now->copy()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'last_month') {
                $fromDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
                $toDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'this_quarter') {
                $fromDate = $now->copy()->startOfQuarter()->toDateString();
                $toDate = $now->copy()->endOfQuarter()->toDateString();
            } elseif ($datePreset === 'this_fy') {
                $currentYear = $now->year;
                if ($now->month >= 4) {
                    $fromDate = Carbon::createFromDate($currentYear, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear + 1, 3, 31)->toDateString();
                } else {
                    $fromDate = Carbon::createFromDate($currentYear - 1, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear, 3, 31)->toDateString();
                }
            }
        }

        if ($fromDate) {
            $query->whereDate('invoice_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('invoice_date', '<=', $toDate);
        }

        // Keyword Search
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('purchase_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%")
                         ->orWhere('gstin', 'like', "%{$search}%");
                  });
            });
        }

        // CSV Export if requested
        if ($request->input('export') === 'csv') {
            return $this->exportPurchaseReportCsv($query, $viewMode, $fromDate, $toDate, $request->input('item_id'));
        }

        // 3. Compute KPI Metrics on all filtered purchases
        $allFilteredPurchases = (clone $query)->get();
        $totalInvoicesCount = $allFilteredPurchases->count();
        $totalTaxableSubtotal = (float)$allFilteredPurchases->sum('subtotal');
        $totalTaxAmount = (float)$allFilteredPurchases->sum('tax_amount');
        $totalGrandAmount = (float)$allFilteredPurchases->sum('grand_total');
        $totalPaidAmount = (float)$allFilteredPurchases->sum('paid_amount');
        $totalPendingBalance = max(0, $totalGrandAmount - $totalPaidAmount);

        $totalQuantityProcured = 0;
        foreach ($allFilteredPurchases as $pur) {
            $totalQuantityProcured += (float)$pur->items->sum('quantity');
        }

        $stats = [
            'total_invoices'       => $totalInvoicesCount,
            'total_taxable'        => $totalTaxableSubtotal,
            'total_tax'            => $totalTaxAmount,
            'total_grand'          => $totalGrandAmount,
            'total_paid'           => $totalPaidAmount,
            'total_pending'        => $totalPendingBalance,
            'total_quantity'       => $totalQuantityProcured,
        ];

        // 4. Data retrieval according to selected view mode
        $billsData = null;
        $itemsData = null;
        $vendorsData = null;

        if ($viewMode === 'bills') {
            $billsData = (clone $query)
                ->orderBy('invoice_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();
        } elseif ($viewMode === 'items') {
            $purchaseIds = $allFilteredPurchases->pluck('id')->toArray();
            $itemsData = PurchaseItem::with(['item.unitRelation', 'purchase.vendor'])
                ->whereIn('purchase_id', $purchaseIds)
                ->when($request->input('item_id'), function ($q, $itemId) {
                    $q->where('item_id', $itemId);
                })
                ->get()
                ->groupBy('item_id')
                ->map(function ($group) {
                    $first = $group->first();
                    $totalQty = (float)$group->sum('quantity');
                    $totalVal = (float)$group->sum('total_amount');
                    $avgRate = $totalQty > 0 ? ($totalVal / $totalQty) : 0;
                    $billsCount = $group->pluck('purchase_id')->unique()->count();

                    $vendorGroup = $group->groupBy(function ($pi) {
                        return $pi->purchase && $pi->purchase->vendor ? $pi->purchase->vendor->name : 'N/A';
                    })->sortByDesc(function ($vGroup) {
                        return $vGroup->sum('quantity');
                    });
                    $topSupplier = $vendorGroup->keys()->first() ?: 'N/A';

                    return (object)[
                        'item_id'       => $first->item_id,
                        'item_name'     => $first->item ? $first->item->name : 'Unknown Item',
                        'item_code'     => $first->item ? $first->item->code : '—',
                        'category'      => $first->item ? $first->item->category : 'General',
                        'unit'          => $first->unit ?: 'KG',
                        'total_qty'     => $totalQty,
                        'avg_rate'      => $avgRate,
                        'total_amount'  => $totalVal,
                        'bills_count'   => $billsCount,
                        'top_supplier'  => $topSupplier,
                    ];
                })
                ->sortByDesc('total_amount')
                ->values();
        } elseif ($viewMode === 'vendors') {
            $vendorsData = $allFilteredPurchases
                ->groupBy('vendor_id')
                ->map(function ($group) {
                    $first = $group->first();
                    $totalBills = $group->count();
                    $totalSpend = (float)$group->sum('grand_total');
                    $totalPaid = (float)$group->sum('paid_amount');
                    $pending = max(0, $totalSpend - $totalPaid);
                    $totalQty = 0;
                    foreach ($group as $p) {
                        $totalQty += (float)$p->items->sum('quantity');
                    }

                    return (object)[
                        'vendor_id'       => $first->vendor_id,
                        'vendor_name'     => $first->vendor ? $first->vendor->name : 'N/A',
                        'vendor_code'     => $first->vendor ? $first->vendor->code : '—',
                        'city'            => $first->vendor ? $first->vendor->city : '—',
                        'gstin'           => $first->vendor ? $first->vendor->gstin : '—',
                        'phone'           => $first->vendor ? $first->vendor->phone : '—',
                        'bills_count'     => $totalBills,
                        'total_qty'       => $totalQty,
                        'total_spend'     => $totalSpend,
                        'total_paid'      => $totalPaid,
                        'pending_balance' => $pending,
                    ];
                })
                ->sortByDesc('total_spend')
                ->values();
        }

        // 5. Filter Dropdown Options
        $vendors = Vendor::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.purchase-report', [
            'viewMode'      => $viewMode,
            'stats'         => $stats,
            'billsData'     => $billsData,
            'itemsData'     => $itemsData,
            'vendorsData'   => $vendorsData,
            'vendors'       => $vendors,
            'brokers'       => $brokers,
            'items'         => $items,
            'filters'       => [
                'date_preset'    => $datePreset,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'vendor_id'      => $request->input('vendor_id'),
                'broker_id'      => $request->input('broker_id'),
                'item_id'        => $request->input('item_id'),
                'bill_type'      => $request->input('bill_type', 'all'),
                'payment_status' => $request->input('payment_status', 'all'),
                'status'         => $request->input('status', 'all'),
                'search'         => $search,
            ],
            'pageTitle'     => 'Purchase Report - VIKAS UDHYOG ERP',
            'pageCode'      => 'rpt-purchase',
        ]);
    }

    /**
     * Stream CSV export for the purchase report based on active mode.
     */
    protected function exportPurchaseReportCsv($query, string $viewMode, $fromDate, $toDate, $itemId = null): StreamedResponse
    {
        $filename = "purchase_report_{$viewMode}_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query, $viewMode, $fromDate, $toDate, $itemId) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['VIKAS UDHYOG ERP - PROCUREMENT & PURCHASE REPORT']);
            fputcsv($file, ['Report Mode:', strtoupper($viewMode)]);
            fputcsv($file, ['Date Range:', ($fromDate ?: 'All Past') . ' to ' . ($toDate ?: 'Present')]);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            if ($viewMode === 'bills') {
                fputcsv($file, [
                    'Invoice Date',
                    'Invoice No',
                    'Purchase Docket No',
                    'Vendor Firm',
                    'City',
                    'GSTIN',
                    'Bill Type',
                    'Taxable Subtotal (INR)',
                    'GST Tax (INR)',
                    'Under Billing (INR)',
                    'Grand Total (INR)',
                    'Paid Amount (INR)',
                    'Pending Balance (INR)',
                    'Payment Status',
                    'Order Status',
                ]);

                $purchases = (clone $query)->orderBy('invoice_date', 'desc')->get();
                foreach ($purchases as $p) {
                    $pending = max(0, (float)$p->grand_total - (float)$p->paid_amount);
                    fputcsv($file, [
                        $p->invoice_date ? $p->invoice_date->format('Y-m-d') : '',
                        $p->invoice_no ?: '—',
                        $p->purchase_no,
                        $p->vendor ? $p->vendor->name : 'N/A',
                        $p->vendor ? $p->vendor->city : '—',
                        $p->vendor ? $p->vendor->gstin : '—',
                        $p->bill_type === 'with_bill' ? 'Tax Invoice' : 'Without Bill',
                        number_format($p->subtotal, 2, '.', ''),
                        number_format($p->tax_amount, 2, '.', ''),
                        number_format($p->under_billing_total, 2, '.', ''),
                        number_format($p->grand_total, 2, '.', ''),
                        number_format($p->paid_amount, 2, '.', ''),
                        number_format($pending, 2, '.', ''),
                        strtoupper($p->payment_status ?: 'unpaid'),
                        strtoupper($p->status ?: 'completed'),
                    ]);
                }
            } elseif ($viewMode === 'items') {
                fputcsv($file, [
                    'Item Code',
                    'Herbal Product Name',
                    'Category',
                    'Total Quantity Inwarded',
                    'Unit',
                    'Average Purchase Rate (INR)',
                    'Total Procurement Value (INR)',
                    'Invoices Count',
                    'Top Supplier',
                ]);

                $purchases = (clone $query)->get();
                $purchaseIds = $purchases->pluck('id')->toArray();
                $itemsData = PurchaseItem::with(['item', 'purchase.vendor'])
                    ->whereIn('purchase_id', $purchaseIds)
                    ->when($itemId, fn($q) => $q->where('item_id', $itemId))
                    ->get()
                    ->groupBy('item_id');

                foreach ($itemsData as $group) {
                    $first = $group->first();
                    $totalQty = (float)$group->sum('quantity');
                    $totalVal = (float)$group->sum('total_amount');
                    $avgRate = $totalQty > 0 ? ($totalVal / $totalQty) : 0;
                    $billsCount = $group->pluck('purchase_id')->unique()->count();

                    $vendorGroup = $group->groupBy(fn($pi) => $pi->purchase && $pi->purchase->vendor ? $pi->purchase->vendor->name : 'N/A')
                        ->sortByDesc(fn($vg) => $vg->sum('quantity'));
                    $topSupplier = $vendorGroup->keys()->first() ?: 'N/A';

                    fputcsv($file, [
                        $first->item ? $first->item->code : '—',
                        $first->item ? $first->item->name : 'N/A',
                        $first->item ? $first->item->category : 'General',
                        number_format($totalQty, 2, '.', ''),
                        $first->unit ?: 'KG',
                        number_format($avgRate, 2, '.', ''),
                        number_format($totalVal, 2, '.', ''),
                        $billsCount,
                        $topSupplier,
                    ]);
                }
            } elseif ($viewMode === 'vendors') {
                fputcsv($file, [
                    'Vendor Code',
                    'Vendor Firm Name',
                    'City',
                    'GSTIN',
                    'Phone',
                    'Total Inward Bills',
                    'Total Quantity (KG/Units)',
                    'Total Inward Spend (INR)',
                    'Total Paid (INR)',
                    'Pending Balance (INR)',
                ]);

                $purchases = (clone $query)->get()->groupBy('vendor_id');
                foreach ($purchases as $group) {
                    $first = $group->first();
                    $totalSpend = (float)$group->sum('grand_total');
                    $totalPaid = (float)$group->sum('paid_amount');
                    $pending = max(0, $totalSpend - $totalPaid);
                    $totalQty = 0;
                    foreach ($group as $p) {
                        $totalQty += (float)$p->items->sum('quantity');
                    }

                    fputcsv($file, [
                        $first->vendor ? $first->vendor->code : '—',
                        $first->vendor ? $first->vendor->name : 'N/A',
                        $first->vendor ? $first->vendor->city : '—',
                        $first->vendor ? $first->vendor->gstin : '—',
                        $first->vendor ? $first->vendor->phone : '—',
                        $group->count(),
                        number_format($totalQty, 2, '.', ''),
                        number_format($totalSpend, 2, '.', ''),
                        number_format($totalPaid, 2, '.', ''),
                        number_format($pending, 2, '.', ''),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the comprehensive Sales Report & Outward Dispatch Register.
     */
    public function salesReport(Request $request)
    {
        // 1. Determine active report mode: 'bills', 'items', 'customers'
        $viewMode = $request->input('view_mode', 'bills');

        // 2. Base Query
        $query = Sale::with(['customer', 'broker', 'company', 'items.item'])
            ->whereNull('deleted_at');

        // Filter: Status (non-cancelled by default unless explicitly chosen)
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        } else {
            $query->where('status', '!=', 'cancelled');
        }

        // Filter: Bill Type
        if ($billType = $request->input('bill_type')) {
            if ($billType !== 'all') {
                $query->where('bill_type', $billType);
            }
        }

        // Filter: Customer
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Filter: Broker
        if ($brokerId = $request->input('broker_id')) {
            $query->where('broker_id', $brokerId);
        }

        // Filter: Order Type
        if ($orderType = $request->input('order_type')) {
            if ($orderType !== 'all') {
                $query->where('order_type', $orderType);
            }
        }

        // Filter: Payment Status
        if ($paymentStatus = $request->input('payment_status')) {
            if ($paymentStatus !== 'all') {
                $query->where('payment_status', $paymentStatus);
            }
        }

        // Filter: Item SKU
        if ($itemId = $request->input('item_id')) {
            $query->whereHas('items', function ($iq) use ($itemId) {
                $iq->where('item_id', $itemId);
            });
        }

        // Date Presets & Custom Date Range
        $datePreset = $request->input('date_preset', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($datePreset !== 'all' && $datePreset !== 'custom') {
            $now = Carbon::now();
            if ($datePreset === 'today') {
                $fromDate = $now->toDateString();
                $toDate = $now->toDateString();
            } elseif ($datePreset === 'yesterday') {
                $fromDate = $now->copy()->subDay()->toDateString();
                $toDate = $now->copy()->subDay()->toDateString();
            } elseif ($datePreset === 'this_week') {
                $fromDate = $now->copy()->startOfWeek()->toDateString();
                $toDate = $now->copy()->endOfWeek()->toDateString();
            } elseif ($datePreset === 'this_month') {
                $fromDate = $now->copy()->startOfMonth()->toDateString();
                $toDate = $now->copy()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'last_month') {
                $fromDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
                $toDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'this_quarter') {
                $fromDate = $now->copy()->startOfQuarter()->toDateString();
                $toDate = $now->copy()->endOfQuarter()->toDateString();
            } elseif ($datePreset === 'this_fy') {
                $currentYear = $now->year;
                if ($now->month >= 4) {
                    $fromDate = Carbon::createFromDate($currentYear, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear + 1, 3, 31)->toDateString();
                } else {
                    $fromDate = Carbon::createFromDate($currentYear - 1, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear, 3, 31)->toDateString();
                }
            }
        }

        if ($fromDate) {
            $query->whereDate('sale_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('sale_date', '<=', $toDate);
        }

        // Keyword Search
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('sale_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%")
                         ->orWhere('gstin', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // CSV Export if requested
        if ($request->input('export') === 'csv') {
            return $this->exportSalesReportCsv($query, $viewMode, $fromDate, $toDate, $request->input('item_id'));
        }

        // 3. Compute KPI Metrics across all filtered sales
        $allFilteredSales = (clone $query)->get();
        $totalInvoicesCount = $allFilteredSales->count();
        $totalTaxableSubtotal = (float)$allFilteredSales->sum('subtotal');
        $totalTaxAmount = (float)$allFilteredSales->sum('tax_amount');
        $totalGrandAmount = (float)$allFilteredSales->sum('grand_total');
        $totalPaidAmount = (float)$allFilteredSales->sum('paid_amount');
        $totalPendingBalance = max(0, $totalGrandAmount - $totalPaidAmount);

        $totalQuantityDispatched = 0;
        foreach ($allFilteredSales as $sl) {
            $totalQuantityDispatched += (float)$sl->items->sum('quantity');
        }

        $stats = [
            'total_invoices'       => $totalInvoicesCount,
            'total_taxable'        => $totalTaxableSubtotal,
            'total_tax'            => $totalTaxAmount,
            'total_grand'          => $totalGrandAmount,
            'total_paid'           => $totalPaidAmount,
            'total_pending'        => $totalPendingBalance,
            'total_quantity'       => $totalQuantityDispatched,
        ];

        // 4. Data retrieval according to selected view mode
        $billsData = null;
        $itemsData = null;
        $customersData = null;

        if ($viewMode === 'bills') {
            $billsData = (clone $query)
                ->orderBy('sale_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();
        } elseif ($viewMode === 'items') {
            $saleIds = $allFilteredSales->pluck('id')->toArray();
            $itemsData = SaleItem::with(['item.unitRelation', 'sale.customer'])
                ->whereIn('sale_id', $saleIds)
                ->when($request->input('item_id'), function ($q, $itemId) {
                    $q->where('item_id', $itemId);
                })
                ->get()
                ->groupBy('item_id')
                ->map(function ($group) {
                    $first = $group->first();
                    $totalQty = (float)$group->sum('quantity');
                    $totalVal = (float)$group->sum('total_amount');
                    $avgRate = $totalQty > 0 ? ($totalVal / $totalQty) : 0;
                    $billsCount = $group->pluck('sale_id')->unique()->count();

                    $customerGroup = $group->groupBy(function ($si) {
                        return $si->sale && $si->sale->customer ? $si->sale->customer->name : 'N/A';
                    })->sortByDesc(function ($cGroup) {
                        return $cGroup->sum('quantity');
                    });
                    $topCustomer = $customerGroup->keys()->first() ?: 'N/A';

                    return (object)[
                        'item_id'       => $first->item_id,
                        'item_name'     => $first->item ? $first->item->name : 'Unknown Item',
                        'item_code'     => $first->item ? $first->item->code : '—',
                        'category'      => $first->item ? $first->item->category : 'General',
                        'unit'          => $first->unit ?: 'KG',
                        'total_qty'     => $totalQty,
                        'avg_rate'      => $avgRate,
                        'total_amount'  => $totalVal,
                        'bills_count'   => $billsCount,
                        'top_customer'  => $topCustomer,
                    ];
                })
                ->sortByDesc('total_amount')
                ->values();
        } elseif ($viewMode === 'customers') {
            $customersData = $allFilteredSales
                ->groupBy('customer_id')
                ->map(function ($group) {
                    $first = $group->first();
                    $totalBills = $group->count();
                    $totalSpend = (float)$group->sum('grand_total');
                    $totalPaid = (float)$group->sum('paid_amount');
                    $pending = max(0, $totalSpend - $totalPaid);
                    $totalQty = 0;
                    foreach ($group as $s) {
                        $totalQty += (float)$s->items->sum('quantity');
                    }

                    return (object)[
                        'customer_id'     => $first->customer_id,
                        'customer_name'   => $first->customer ? $first->customer->name : 'N/A',
                        'customer_code'   => $first->customer ? $first->customer->code : '—',
                        'city'            => $first->customer ? $first->customer->city : '—',
                        'gstin'           => $first->customer ? $first->customer->gstin : '—',
                        'phone'           => $first->customer ? $first->customer->phone : '—',
                        'bills_count'     => $totalBills,
                        'total_qty'       => $totalQty,
                        'total_spend'     => $totalSpend,
                        'total_paid'      => $totalPaid,
                        'pending_balance' => $pending,
                    ];
                })
                ->sortByDesc('total_spend')
                ->values();
        }

        // 5. Filter Dropdown Options
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.sales-report', [
            'viewMode'      => $viewMode,
            'stats'         => $stats,
            'billsData'     => $billsData,
            'itemsData'     => $itemsData,
            'customersData' => $customersData,
            'customers'     => $customers,
            'brokers'       => $brokers,
            'items'         => $items,
            'filters'       => [
                'date_preset'    => $datePreset,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'customer_id'    => $request->input('customer_id'),
                'broker_id'      => $request->input('broker_id'),
                'item_id'        => $request->input('item_id'),
                'bill_type'      => $request->input('bill_type', 'all'),
                'order_type'     => $request->input('order_type', 'all'),
                'payment_status' => $request->input('payment_status', 'all'),
                'status'         => $request->input('status', 'all'),
                'search'         => $search,
            ],
            'pageTitle'     => 'Sales Report - VIKAS UDHYOG ERP',
            'pageCode'      => 'rpt-sales',
        ]);
    }

    /**
     * Stream CSV export for the sales report based on active mode.
     */
    protected function exportSalesReportCsv($query, string $viewMode, $fromDate, $toDate, $itemId = null): StreamedResponse
    {
        $filename = "sales_report_{$viewMode}_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query, $viewMode, $fromDate, $toDate, $itemId) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['VIKAS UDHYOG ERP - SALES & OUTWARD REGISTER REPORT']);
            fputcsv($file, ['Report Mode:', strtoupper($viewMode)]);
            fputcsv($file, ['Date Range:', ($fromDate ?: 'All Past') . ' to ' . ($toDate ?: 'Present')]);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            if ($viewMode === 'bills') {
                fputcsv($file, [
                    'Sale Date',
                    'Invoice No',
                    'Sale Docket No',
                    'Customer Firm Name',
                    'Customer Code',
                    'City',
                    'GSTIN',
                    'Broker',
                    'Bill Type',
                    'Order Priority',
                    'Vehicle No',
                    'Taxable Subtotal (INR)',
                    'Output GST (INR)',
                    'Grand Total (INR)',
                    'Paid Amount (INR)',
                    'Pending Balance (INR)',
                    'Payment Status',
                    'Dispatch Status'
                ]);

                $sales = (clone $query)->orderBy('sale_date', 'desc')->get();
                foreach ($sales as $s) {
                    $pending = max(0, (float)$s->grand_total - (float)$s->paid_amount);
                    fputcsv($file, [
                        $s->sale_date ? $s->sale_date->format('d/m/Y') : '—',
                        $s->invoice_no ?: '—',
                        $s->sale_no ?: '—',
                        $s->customer ? $s->customer->name : 'N/A',
                        $s->customer ? $s->customer->code : '—',
                        $s->customer ? $s->customer->city : '—',
                        $s->customer ? $s->customer->gstin : '—',
                        $s->broker ? $s->broker->name : 'Direct',
                        ucwords(str_replace('_', ' ', $s->bill_type ?: 'with_bill')),
                        $s->order_type ?: 'Standard',
                        $s->vehicle_no ?: '—',
                        number_format((float)$s->subtotal, 2, '.', ''),
                        number_format((float)$s->tax_amount, 2, '.', ''),
                        number_format((float)$s->grand_total, 2, '.', ''),
                        number_format((float)$s->paid_amount, 2, '.', ''),
                        number_format($pending, 2, '.', ''),
                        strtoupper($s->payment_status ?: 'UNPAID'),
                        strtoupper($s->status ?: 'DISPATCHED'),
                    ]);
                }
            } elseif ($viewMode === 'items') {
                fputcsv($file, [
                    'Item SKU Code',
                    'Item / Product Name',
                    'Category',
                    'Total Quantity Sold',
                    'Unit',
                    'Weighted Avg Rate (INR)',
                    'Total Sales Turnover (INR)',
                    'Invoices Count',
                    'Top Purchasing Customer',
                ]);

                $saleIds = (clone $query)->pluck('id')->toArray();
                $itemsData = SaleItem::with(['item', 'sale.customer'])
                    ->whereIn('sale_id', $saleIds)
                    ->when($itemId, fn($q) => $q->where('item_id', $itemId))
                    ->get()
                    ->groupBy('item_id');

                foreach ($itemsData as $group) {
                    $first = $group->first();
                    $totalQty = (float)$group->sum('quantity');
                    $totalVal = (float)$group->sum('total_amount');
                    $avgRate = $totalQty > 0 ? ($totalVal / $totalQty) : 0;
                    $billsCount = $group->pluck('sale_id')->unique()->count();

                    $customerGroup = $group->groupBy(fn($si) => $si->sale && $si->sale->customer ? $si->sale->customer->name : 'N/A')
                        ->sortByDesc(fn($cg) => $cg->sum('quantity'));
                    $topCustomer = $customerGroup->keys()->first() ?: 'N/A';

                    fputcsv($file, [
                        $first->item ? $first->item->code : '—',
                        $first->item ? $first->item->name : 'N/A',
                        $first->item ? $first->item->category : 'General',
                        number_format($totalQty, 2, '.', ''),
                        $first->unit ?: 'KG',
                        number_format($avgRate, 2, '.', ''),
                        number_format($totalVal, 2, '.', ''),
                        $billsCount,
                        $topCustomer,
                    ]);
                }
            } elseif ($viewMode === 'customers') {
                fputcsv($file, [
                    'Customer Code',
                    'Customer Firm Name',
                    'City',
                    'GSTIN',
                    'Phone',
                    'Total Orders / Invoices',
                    'Total Quantity Sold (KG/Units)',
                    'Total Sales Turnover (INR)',
                    'Total Paid / Collected (INR)',
                    'Pending Balance Receivable (INR)',
                ]);

                $sales = (clone $query)->get()->groupBy('customer_id');
                foreach ($sales as $group) {
                    $first = $group->first();
                    $totalSpend = (float)$group->sum('grand_total');
                    $totalPaid = (float)$group->sum('paid_amount');
                    $pending = max(0, $totalSpend - $totalPaid);
                    $totalQty = 0;
                    foreach ($group as $s) {
                        $totalQty += (float)$s->items->sum('quantity');
                    }

                    fputcsv($file, [
                        $first->customer ? $first->customer->code : '—',
                        $first->customer ? $first->customer->name : 'N/A',
                        $first->customer ? $first->customer->city : '—',
                        $first->customer ? $first->customer->gstin : '—',
                        $first->customer ? $first->customer->phone : '—',
                        $group->count(),
                        number_format($totalQty, 2, '.', ''),
                        number_format($totalSpend, 2, '.', ''),
                        number_format($totalPaid, 2, '.', ''),
                        number_format($pending, 2, '.', ''),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the comprehensive Order Fulfillment & Dispatch Pipeline Report.
     */
    public function orderReport(Request $request)
    {
        // 1. Determine active report mode: 'orders', 'priority', 'items', 'customers'
        $viewMode = $request->input('view_mode', 'orders');

        // 2. Base Query
        $query = Sale::with(['customer', 'broker', 'company', 'items.item'])
            ->whereNull('deleted_at');

        // Filter: Order Fulfillment Status
        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        // Filter: Order Priority / Type
        if ($orderType = $request->input('order_type')) {
            if ($orderType !== 'all') {
                $query->where('order_type', $orderType);
            }
        }

        // Filter: Customer
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Filter: Broker
        if ($brokerId = $request->input('broker_id')) {
            $query->where('broker_id', $brokerId);
        }

        // Filter: Bill Type
        if ($billType = $request->input('bill_type')) {
            if ($billType !== 'all') {
                $query->where('bill_type', $billType);
            }
        }

        // Filter: Payment Status
        if ($paymentStatus = $request->input('payment_status')) {
            if ($paymentStatus !== 'all') {
                $query->where('payment_status', $paymentStatus);
            }
        }

        // Filter: Item SKU
        if ($itemId = $request->input('item_id')) {
            $query->whereHas('items', function ($iq) use ($itemId) {
                $iq->where('item_id', $itemId);
            });
        }

        // Date Presets & Custom Date Range
        $datePreset = $request->input('date_preset', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($datePreset !== 'all' && $datePreset !== 'custom') {
            $now = Carbon::now();
            if ($datePreset === 'today') {
                $fromDate = $now->toDateString();
                $toDate = $now->toDateString();
            } elseif ($datePreset === 'yesterday') {
                $fromDate = $now->copy()->subDay()->toDateString();
                $toDate = $now->copy()->subDay()->toDateString();
            } elseif ($datePreset === 'this_week') {
                $fromDate = $now->copy()->startOfWeek()->toDateString();
                $toDate = $now->copy()->endOfWeek()->toDateString();
            } elseif ($datePreset === 'this_month') {
                $fromDate = $now->copy()->startOfMonth()->toDateString();
                $toDate = $now->copy()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'last_month') {
                $fromDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
                $toDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'this_quarter') {
                $fromDate = $now->copy()->startOfQuarter()->toDateString();
                $toDate = $now->copy()->endOfQuarter()->toDateString();
            } elseif ($datePreset === 'this_fy') {
                $currentYear = $now->year;
                if ($now->month >= 4) {
                    $fromDate = Carbon::createFromDate($currentYear, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear + 1, 3, 31)->toDateString();
                } else {
                    $fromDate = Carbon::createFromDate($currentYear - 1, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear, 3, 31)->toDateString();
                }
            }
        }

        if ($fromDate) {
            $query->whereDate('sale_date', '>=', $fromDate);
        }
        if ($toDate) {
            $query->whereDate('sale_date', '<=', $toDate);
        }

        // Keyword Search
        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('sale_no', 'like', "%{$search}%")
                  ->orWhere('vehicle_no', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%")
                         ->orWhere('gstin', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // CSV Export if requested
        if ($request->input('export') === 'csv') {
            return $this->exportOrderReportCsv($query, $viewMode, $fromDate, $toDate, $request->input('item_id'));
        }

        // 3. Compute KPI Metrics across all filtered orders
        $allFilteredOrders = (clone $query)->get();
        $totalOrdersCount = $allFilteredOrders->count();
        $totalOrderValue = (float)$allFilteredOrders->sum('grand_total');

        $pendingCount = $allFilteredOrders->filter(function ($ord) {
            return in_array($ord->status, ['ordered', 'pending']);
        })->count();

        $dispatchedCount = $allFilteredOrders->filter(function ($ord) {
            return in_array($ord->status, ['dispatched', 'in_transit']);
        })->count();

        $completedCount = $allFilteredOrders->filter(function ($ord) {
            return in_array($ord->status, ['delivered', 'completed']);
        })->count();

        $cancelledCount = $allFilteredOrders->filter(function ($ord) {
            return $ord->status === 'cancelled';
        })->count();

        $totalOrderVolume = 0;
        foreach ($allFilteredOrders as $ord) {
            $totalOrderVolume += (float)$ord->items->sum('quantity');
        }

        $fulfillmentRate = ($totalOrdersCount - $cancelledCount) > 0 
            ? round(($completedCount / ($totalOrdersCount - $cancelledCount)) * 100, 1) 
            : 0;

        $stats = [
            'total_orders'      => $totalOrdersCount,
            'total_value'       => $totalOrderValue,
            'pending_count'     => $pendingCount,
            'dispatched_count'  => $dispatchedCount,
            'completed_count'   => $completedCount,
            'cancelled_count'   => $cancelledCount,
            'total_volume'      => $totalOrderVolume,
            'fulfillment_rate'  => $fulfillmentRate,
        ];

        // 4. Data retrieval according to selected view mode
        $ordersData = null;
        $priorityData = null;
        $itemsData = null;
        $customersData = null;

        if ($viewMode === 'orders') {
            $ordersData = (clone $query)
                ->orderBy('sale_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();
        } elseif ($viewMode === 'priority') {
            $priorityData = $allFilteredOrders
                ->groupBy(function ($ord) {
                    return $ord->order_type ?: 'Medium';
                })
                ->map(function ($group, $pName) {
                    $total = $group->count();
                    $val = (float)$group->sum('grand_total');
                    $qty = 0;
                    foreach ($group as $o) {
                        $qty += (float)$o->items->sum('quantity');
                    }
                    $comp = $group->filter(fn($o) => in_array($o->status, ['delivered', 'completed']))->count();
                    $disp = $group->filter(fn($o) => in_array($o->status, ['dispatched', 'in_transit']))->count();
                    $pend = $group->filter(fn($o) => in_array($o->status, ['ordered', 'pending']))->count();

                    return (object)[
                        'priority'        => $pName,
                        'total_orders'    => $total,
                        'total_volume'    => $qty,
                        'total_value'     => $val,
                        'completed_count' => $comp,
                        'dispatched_count'=> $disp,
                        'pending_count'   => $pend,
                        'completion_rate' => $total > 0 ? round(($comp / $total) * 100, 1) : 0,
                    ];
                })
                ->sortByDesc('total_value')
                ->values();
        } elseif ($viewMode === 'items') {
            $saleIds = $allFilteredOrders->pluck('id')->toArray();
            $itemsData = SaleItem::with(['item.unitRelation', 'sale.customer'])
                ->whereIn('sale_id', $saleIds)
                ->when($request->input('item_id'), function ($q, $itemId) {
                    $q->where('item_id', $itemId);
                })
                ->get()
                ->groupBy('item_id')
                ->map(function ($group) {
                    $first = $group->first();
                    $totalQty = (float)$group->sum('quantity');
                    $totalVal = (float)$group->sum('total_amount');
                    $avgRate = $totalQty > 0 ? ($totalVal / $totalQty) : 0;
                    $ordersCount = $group->pluck('sale_id')->unique()->count();

                    $customerGroup = $group->groupBy(function ($si) {
                        return $si->sale && $si->sale->customer ? $si->sale->customer->name : 'N/A';
                    })->sortByDesc(function ($cGroup) {
                        return $cGroup->sum('quantity');
                    });
                    $topCustomer = $customerGroup->keys()->first() ?: 'N/A';

                    return (object)[
                        'item_id'       => $first->item_id,
                        'item_name'     => $first->item ? $first->item->name : 'Unknown Item',
                        'item_code'     => $first->item ? $first->item->code : '—',
                        'category'      => $first->item ? $first->item->category : 'General',
                        'unit'          => $first->unit ?: 'KG',
                        'total_qty'     => $totalQty,
                        'avg_rate'      => $avgRate,
                        'total_amount'  => $totalVal,
                        'orders_count'  => $ordersCount,
                        'top_customer'  => $topCustomer,
                    ];
                })
                ->sortByDesc('total_amount')
                ->values();
        } elseif ($viewMode === 'customers') {
            $customersData = $allFilteredOrders
                ->groupBy('customer_id')
                ->map(function ($group) {
                    $first = $group->first();
                    $totalOrders = $group->count();
                    $totalVal = (float)$group->sum('grand_total');
                    $totalQty = 0;
                    foreach ($group as $s) {
                        $totalQty += (float)$s->items->sum('quantity');
                    }
                    $comp = $group->filter(fn($o) => in_array($o->status, ['delivered', 'completed']))->count();
                    $pend = $group->filter(fn($o) => in_array($o->status, ['ordered', 'pending', 'dispatched']))->count();

                    return (object)[
                        'customer_id'     => $first->customer_id,
                        'customer_name'   => $first->customer ? $first->customer->name : 'N/A',
                        'customer_code'   => $first->customer ? $first->customer->code : '—',
                        'city'            => $first->customer ? $first->customer->city : '—',
                        'orders_count'    => $totalOrders,
                        'total_qty'       => $totalQty,
                        'total_value'     => $totalVal,
                        'completed_count' => $comp,
                        'pending_count'   => $pend,
                    ];
                })
                ->sortByDesc('total_value')
                ->values();
        }

        // 5. Filter Dropdown Options
        $customers = Customer::where('status', 'active')->orderBy('name')->get();
        $brokers = Broker::where('status', 'active')->orderBy('name')->get();
        $items = Item::where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.order-report', [
            'viewMode'      => $viewMode,
            'stats'         => $stats,
            'ordersData'    => $ordersData,
            'priorityData'  => $priorityData,
            'itemsData'     => $itemsData,
            'customersData' => $customersData,
            'customers'     => $customers,
            'brokers'       => $brokers,
            'items'         => $items,
            'filters'       => [
                'date_preset'    => $datePreset,
                'from_date'      => $fromDate,
                'to_date'        => $toDate,
                'customer_id'    => $request->input('customer_id'),
                'broker_id'      => $request->input('broker_id'),
                'item_id'        => $request->input('item_id'),
                'bill_type'      => $request->input('bill_type', 'all'),
                'order_type'     => $request->input('order_type', 'all'),
                'payment_status' => $request->input('payment_status', 'all'),
                'status'         => $request->input('status', 'all'),
                'search'         => $search,
            ],
            'pageTitle'     => 'Order Report - VIKAS UDHYOG ERP',
            'pageCode'      => 'rpt-order',
        ]);
    }

    /**
     * Stream CSV export for the order report based on active mode.
     */
    protected function exportOrderReportCsv($query, string $viewMode, $fromDate, $toDate, $itemId = null): StreamedResponse
    {
        $filename = "order_report_{$viewMode}_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($query, $viewMode, $fromDate, $toDate, $itemId) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['VIKAS UDHYOG ERP - ORDER FULFILLMENT & DISPATCH PIPELINE REPORT']);
            fputcsv($file, ['Report Mode:', strtoupper($viewMode)]);
            fputcsv($file, ['Date Range:', ($fromDate ?: 'All Past') . ' to ' . ($toDate ?: 'Present')]);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            if ($viewMode === 'orders') {
                fputcsv($file, [
                    'Order Date',
                    'Order Docket No',
                    'Invoice No',
                    'Customer Firm Name',
                    'Customer Code',
                    'City',
                    'Broker',
                    'Order Priority',
                    'Vehicle No',
                    'Total Quantity',
                    'Order Value (INR)',
                    'Paid Amount (INR)',
                    'Fulfillment Status',
                    'Payment Status'
                ]);

                $orders = (clone $query)->orderBy('sale_date', 'desc')->get();
                foreach ($orders as $o) {
                    $qty = (float)$o->items->sum('quantity');
                    fputcsv($file, [
                        $o->sale_date ? $o->sale_date->format('d/m/Y') : '—',
                        $o->sale_no ?: '—',
                        $o->invoice_no ?: '—',
                        $o->customer ? $o->customer->name : 'N/A',
                        $o->customer ? $o->customer->code : '—',
                        $o->customer ? $o->customer->city : '—',
                        $o->broker ? $o->broker->name : 'Direct',
                        $o->order_type ?: 'Medium',
                        $o->vehicle_no ?: '—',
                        number_format($qty, 2, '.', ''),
                        number_format((float)$o->grand_total, 2, '.', ''),
                        number_format((float)$o->paid_amount, 2, '.', ''),
                        strtoupper($o->status ?: 'ORDERED'),
                        strtoupper($o->payment_status ?: 'UNPAID'),
                    ]);
                }
            } elseif ($viewMode === 'priority') {
                fputcsv($file, [
                    'Priority / Urgency Level',
                    'Total Orders Booked',
                    'Total Volume (KG/Units)',
                    'Total Value (INR)',
                    'Pending Orders',
                    'Dispatched Orders',
                    'Completed Orders',
                    'Fulfillment Rate (%)',
                ]);

                $orders = (clone $query)->get()->groupBy(fn($o) => $o->order_type ?: 'Medium');
                foreach ($orders as $pName => $group) {
                    $total = $group->count();
                    $val = (float)$group->sum('grand_total');
                    $qty = 0;
                    foreach ($group as $o) {
                        $qty += (float)$o->items->sum('quantity');
                    }
                    $comp = $group->filter(fn($o) => in_array($o->status, ['delivered', 'completed']))->count();
                    $disp = $group->filter(fn($o) => in_array($o->status, ['dispatched', 'in_transit']))->count();
                    $pend = $group->filter(fn($o) => in_array($o->status, ['ordered', 'pending']))->count();
                    $rate = $total > 0 ? round(($comp / $total) * 100, 1) : 0;

                    fputcsv($file, [
                        $pName,
                        $total,
                        number_format($qty, 2, '.', ''),
                        number_format($val, 2, '.', ''),
                        $pend,
                        $disp,
                        $comp,
                        $rate . '%',
                    ]);
                }
            } elseif ($viewMode === 'items') {
                fputcsv($file, [
                    'Item SKU Code',
                    'Item / Product Name',
                    'Category',
                    'Total Ordered Quantity',
                    'Unit',
                    'Weighted Avg Rate (INR)',
                    'Total Demand Value (INR)',
                    'Orders Count',
                    'Top Ordering Customer',
                ]);

                $saleIds = (clone $query)->pluck('id')->toArray();
                $itemsData = SaleItem::with(['item', 'sale.customer'])
                    ->whereIn('sale_id', $saleIds)
                    ->when($itemId, fn($q) => $q->where('item_id', $itemId))
                    ->get()
                    ->groupBy('item_id');

                foreach ($itemsData as $group) {
                    $first = $group->first();
                    $totalQty = (float)$group->sum('quantity');
                    $totalVal = (float)$group->sum('total_amount');
                    $avgRate = $totalQty > 0 ? ($totalVal / $totalQty) : 0;
                    $ordersCount = $group->pluck('sale_id')->unique()->count();

                    $customerGroup = $group->groupBy(fn($si) => $si->sale && $si->sale->customer ? $si->sale->customer->name : 'N/A')
                        ->sortByDesc(fn($cg) => $cg->sum('quantity'));
                    $topCustomer = $customerGroup->keys()->first() ?: 'N/A';

                    fputcsv($file, [
                        $first->item ? $first->item->code : '—',
                        $first->item ? $first->item->name : 'N/A',
                        $first->item ? $first->item->category : 'General',
                        number_format($totalQty, 2, '.', ''),
                        $first->unit ?: 'KG',
                        number_format($avgRate, 2, '.', ''),
                        number_format($totalVal, 2, '.', ''),
                        $ordersCount,
                        $topCustomer,
                    ]);
                }
            } elseif ($viewMode === 'customers') {
                fputcsv($file, [
                    'Customer Code',
                    'Customer Firm Name',
                    'City',
                    'Total Orders Placed',
                    'Total Quantity (KG/Units)',
                    'Total Order Value (INR)',
                    'Completed / Delivered Orders',
                    'Pending Orders',
                ]);

                $orders = (clone $query)->get()->groupBy('customer_id');
                foreach ($orders as $group) {
                    $first = $group->first();
                    $totalOrders = $group->count();
                    $totalVal = (float)$group->sum('grand_total');
                    $totalQty = 0;
                    foreach ($group as $s) {
                        $totalQty += (float)$s->items->sum('quantity');
                    }
                    $comp = $group->filter(fn($o) => in_array($o->status, ['delivered', 'completed']))->count();
                    $pend = $group->filter(fn($o) => in_array($o->status, ['ordered', 'pending', 'dispatched']))->count();

                    fputcsv($file, [
                        $first->customer ? $first->customer->code : '—',
                        $first->customer ? $first->customer->name : 'N/A',
                        $first->customer ? $first->customer->city : '—',
                        $totalOrders,
                        number_format($totalQty, 2, '.', ''),
                        number_format($totalVal, 2, '.', ''),
                        $comp,
                        $pend,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display the comprehensive Cash & Bank Register (Daybook, Inflow/Outflow).
     */
    public function cashBankRegister(Request $request)
    {
        // 1. Determine active report mode: 'daybook', 'receipts', 'payments', 'accounts'
        $viewMode = $request->input('view_mode', 'daybook');

        // Date Presets & Custom Date Range
        $datePreset = $request->input('date_preset', 'all');
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');

        if ($datePreset !== 'all' && $datePreset !== 'custom') {
            $now = Carbon::now();
            if ($datePreset === 'today') {
                $fromDate = $now->toDateString();
                $toDate = $now->toDateString();
            } elseif ($datePreset === 'yesterday') {
                $fromDate = $now->copy()->subDay()->toDateString();
                $toDate = $now->copy()->subDay()->toDateString();
            } elseif ($datePreset === 'this_week') {
                $fromDate = $now->copy()->startOfWeek()->toDateString();
                $toDate = $now->copy()->endOfWeek()->toDateString();
            } elseif ($datePreset === 'this_month') {
                $fromDate = $now->copy()->startOfMonth()->toDateString();
                $toDate = $now->copy()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'last_month') {
                $fromDate = $now->copy()->subMonth()->startOfMonth()->toDateString();
                $toDate = $now->copy()->subMonth()->endOfMonth()->toDateString();
            } elseif ($datePreset === 'this_quarter') {
                $fromDate = $now->copy()->startOfQuarter()->toDateString();
                $toDate = $now->copy()->endOfQuarter()->toDateString();
            } elseif ($datePreset === 'this_fy') {
                $currentYear = $now->year;
                if ($now->month >= 4) {
                    $fromDate = Carbon::createFromDate($currentYear, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear + 1, 3, 31)->toDateString();
                } else {
                    $fromDate = Carbon::createFromDate($currentYear - 1, 4, 1)->toDateString();
                    $toDate = Carbon::createFromDate($currentYear, 3, 31)->toDateString();
                }
            }
        }

        $accountId = $request->input('account_id');
        $paymentMode = $request->input('payment_mode');
        $search = trim($request->input('search', ''));

        // Base Receipts Query (Debit / Inflow)
        $receiptsQuery = ReceiptVoucher::with(['customer', 'account'])
            ->whereNull('deleted_at')
            ->where('status', 'active');

        // Base Payments Query (Credit / Outflow)
        $paymentsQuery = PaymentVoucher::with(['vendor', 'account'])
            ->whereNull('deleted_at')
            ->where('status', 'active');

        // Apply Date Filters
        if ($fromDate) {
            $receiptsQuery->whereDate('voucher_date', '>=', $fromDate);
            $paymentsQuery->whereDate('voucher_date', '>=', $fromDate);
        }
        if ($toDate) {
            $receiptsQuery->whereDate('voucher_date', '<=', $toDate);
            $paymentsQuery->whereDate('voucher_date', '<=', $toDate);
        }

        // Apply Account Filter
        if ($accountId) {
            $receiptsQuery->where('account_id', $accountId);
            $paymentsQuery->where('account_id', $accountId);
        }

        // Apply Payment Mode Filter
        if ($paymentMode && $paymentMode !== 'all') {
            $receiptsQuery->where('payment_mode', $paymentMode);
            $paymentsQuery->where('payment_mode', $paymentMode);
        }

        // Apply Search Keyword
        if ($search) {
            $receiptsQuery->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('against_invoice', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('income_source', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%");
                  });
            });

            $paymentsQuery->where(function ($q) use ($search) {
                $q->where('voucher_no', 'like', "%{$search}%")
                  ->orWhere('reference_no', 'like', "%{$search}%")
                  ->orWhere('against_invoice', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhere('expense_head', 'like', "%{$search}%")
                  ->orWhereHas('vendor', function ($vq) use ($search) {
                      $vq->where('name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%")
                         ->orWhere('city', 'like', "%{$search}%");
                  });
            });
        }

        // CSV Export if requested
        if ($request->input('export') === 'csv') {
            return $this->exportCashBankRegisterCsv($receiptsQuery, $paymentsQuery, $viewMode, $fromDate, $toDate, $accountId);
        }

        // Compute KPI Metrics across filtered transactions
        $allReceipts = (clone $receiptsQuery)->get();
        $allPayments = (clone $paymentsQuery)->get();

        $totalReceiptsCount = $allReceipts->count();
        $totalPaymentsCount = $allPayments->count();
        $totalReceiptsAmount = (float)$allReceipts->sum('amount');
        $totalPaymentsAmount = (float)$allPayments->sum('amount');
        $netCashMovement = $totalReceiptsAmount - $totalPaymentsAmount;

        $totalLiquidBalance = (float)Account::bankAndCash()->where('status', 'active')->sum('current_balance');

        $stats = [
            'total_receipts_amount' => $totalReceiptsAmount,
            'total_receipts_count'  => $totalReceiptsCount,
            'total_payments_amount' => $totalPaymentsAmount,
            'total_payments_count'  => $totalPaymentsCount,
            'net_cash_movement'     => $netCashMovement,
            'total_liquid_balance'  => $totalLiquidBalance,
        ];

        // Mode Data Resolution
        $daybookData = null;
        $receiptsData = null;
        $paymentsData = null;
        $accountsData = null;

        if ($viewMode === 'daybook') {
            $mappedReceipts = $allReceipts->map(function ($r) {
                $partyName = $r->receipt_type === 'Customer' && $r->customer ? $r->customer->name : ($r->income_source ?: 'Direct Inflow');
                return (object)[
                    'id'               => $r->id,
                    'type'             => 'Receipt',
                    'voucher_no'       => $r->voucher_no,
                    'voucher_date'     => $r->voucher_date,
                    'account_name'     => $r->account ? $r->account->name : 'Cash/Bank',
                    'party_name'       => $partyName,
                    'payment_mode'     => $r->payment_mode ?: 'Cash',
                    'debit'            => (float)$r->amount,
                    'credit'           => 0.00,
                    'reference_no'     => $r->reference_no ?: '—',
                    'against_invoice'  => $r->against_invoice ?: '—',
                    'notes'            => $r->notes,
                    'view_url'         => route('admin.transactions.receipt-voucher.show', $r->id),
                ];
            });

            $mappedPayments = $allPayments->map(function ($p) {
                $partyName = $p->payment_type === 'Vendor' && $p->vendor ? $p->vendor->name : ($p->expense_head ?: 'Direct Expense');
                return (object)[
                    'id'               => $p->id,
                    'type'             => 'Payment',
                    'voucher_no'       => $p->voucher_no,
                    'voucher_date'     => $p->voucher_date,
                    'account_name'     => $p->account ? $p->account->name : 'Cash/Bank',
                    'party_name'       => $partyName,
                    'payment_mode'     => $p->payment_mode ?: 'Cash',
                    'debit'            => 0.00,
                    'credit'           => (float)$p->amount,
                    'reference_no'     => $p->reference_no ?: '—',
                    'against_invoice'  => $p->against_invoice ?: '—',
                    'notes'            => $p->notes,
                    'view_url'         => route('admin.transactions.payment-voucher.show', $p->id),
                ];
            });

            $merged = $mappedReceipts->concat($mappedPayments)
                ->sortByDesc(function ($item) {
                    $ts = $item->voucher_date ? $item->voucher_date->timestamp : 0;
                    return sprintf('%012d_%010d', $ts, $item->id);
                })
                ->values();

            $currentPage = LengthAwarePaginator::resolveCurrentPage();
            $perPage = 15;
            $currentPageItems = $merged->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $daybookData = new LengthAwarePaginator(
                $currentPageItems,
                $merged->count(),
                $perPage,
                $currentPage,
                ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
            );
        } elseif ($viewMode === 'receipts') {
            $receiptsData = (clone $receiptsQuery)
                ->orderBy('voucher_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();
        } elseif ($viewMode === 'payments') {
            $paymentsData = (clone $paymentsQuery)
                ->orderBy('voucher_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();
        } elseif ($viewMode === 'accounts') {
            $bankAccounts = Account::bankAndCash()->where('status', 'active')->orderBy('account_group')->orderBy('name')->get();
            $accountsData = $bankAccounts->map(function ($acc) use ($allReceipts, $allPayments) {
                $accInflow = (float)$allReceipts->where('account_id', $acc->id)->sum('amount');
                $accOutflow = (float)$allPayments->where('account_id', $acc->id)->sum('amount');
                $netPeriod = $accInflow - $accOutflow;

                return (object)[
                    'id'              => $acc->id,
                    'name'            => $acc->name,
                    'code'            => $acc->code,
                    'group'           => $acc->account_group,
                    'bank_name'       => $acc->bank_name ?: '—',
                    'account_number'  => $acc->account_number ?: '—',
                    'ifsc_code'       => $acc->ifsc_code ?: '—',
                    'opening_balance' => (float)$acc->opening_balance,
                    'inflow'          => $accInflow,
                    'outflow'         => $accOutflow,
                    'net_change'      => $netPeriod,
                    'current_balance' => (float)$acc->current_balance,
                ];
            });
        }

        // Active Dropdown Options
        $accounts = Account::bankAndCash()->where('status', 'active')->orderBy('name')->get();

        return view('admin.reports.cash-bank-register', [
            'viewMode'      => $viewMode,
            'stats'         => $stats,
            'daybookData'   => $daybookData,
            'receiptsData'  => $receiptsData,
            'paymentsData'  => $paymentsData,
            'accountsData'  => $accountsData,
            'accounts'      => $accounts,
            'filters'       => [
                'date_preset'  => $datePreset,
                'from_date'    => $fromDate,
                'to_date'      => $toDate,
                'account_id'   => $accountId,
                'payment_mode' => $paymentMode ?: 'all',
                'search'       => $search,
            ],
            'pageTitle'     => 'Cash & Bank Register - VIKAS UDHYOG ERP',
            'pageCode'      => 'rpt-cash-reg',
        ]);
    }

    /**
     * Stream CSV export for the Cash & Bank Register.
     */
    protected function exportCashBankRegisterCsv($receiptsQuery, $paymentsQuery, string $viewMode, $fromDate, $toDate, $accountId = null): StreamedResponse
    {
        $filename = "cash_bank_register_{$viewMode}_" . date('Y_m_d_His') . ".csv";
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($receiptsQuery, $paymentsQuery, $viewMode, $fromDate, $toDate, $accountId) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM

            fputcsv($file, ['VIKAS UDHYOG ERP - CASH & BANK DAYBOOK REGISTER']);
            fputcsv($file, ['Report Mode:', strtoupper($viewMode)]);
            fputcsv($file, ['Date Range:', ($fromDate ?: 'All Past') . ' to ' . ($toDate ?: 'Present')]);
            fputcsv($file, ['Generated At:', date('d M Y, h:i A')]);
            fputcsv($file, []);

            if ($viewMode === 'daybook') {
                fputcsv($file, [
                    'Date',
                    'Voucher No',
                    'Transaction Type',
                    'Account Head',
                    'Particulars / Party Name',
                    'Payment Mode',
                    'Debit / Receipts (INR)',
                    'Credit / Payments (INR)',
                    'Reference No',
                    'Ref Bill / Invoice',
                    'Notes'
                ]);

                $allR = (clone $receiptsQuery)->get();
                $allP = (clone $paymentsQuery)->get();

                $mappedR = $allR->map(function ($r) {
                    $party = $r->receipt_type === 'Customer' && $r->customer ? $r->customer->name : ($r->income_source ?: 'Direct Inflow');
                    return (object)[
                        'date'     => $r->voucher_date ? $r->voucher_date->format('d/m/Y') : '—',
                        'raw_date' => $r->voucher_date,
                        'id'       => $r->id,
                        'v_no'     => $r->voucher_no,
                        'type'     => 'RECEIPT (Dr)',
                        'acc'      => $r->account ? $r->account->name : 'Cash/Bank',
                        'party'    => $party,
                        'mode'     => $r->payment_mode ?: 'Cash',
                        'dr'       => (float)$r->amount,
                        'cr'       => 0.00,
                        'ref'      => $r->reference_no ?: '—',
                        'inv'      => $r->against_invoice ?: '—',
                        'notes'    => $r->notes ?: '',
                    ];
                });

                $mappedP = $allP->map(function ($p) {
                    $party = $p->payment_type === 'Vendor' && $p->vendor ? $p->vendor->name : ($p->expense_head ?: 'Direct Expense');
                    return (object)[
                        'date'     => $p->voucher_date ? $p->voucher_date->format('d/m/Y') : '—',
                        'raw_date' => $p->voucher_date,
                        'id'       => $p->id,
                        'v_no'     => $p->voucher_no,
                        'type'     => 'PAYMENT (Cr)',
                        'acc'      => $p->account ? $p->account->name : 'Cash/Bank',
                        'party'    => $party,
                        'mode'     => $p->payment_mode ?: 'Cash',
                        'dr'       => 0.00,
                        'cr'       => (float)$p->amount,
                        'ref'      => $p->reference_no ?: '—',
                        'inv'      => $p->against_invoice ?: '—',
                        'notes'    => $p->notes ?: '',
                    ];
                });

                $merged = $mappedR->concat($mappedP)->sortByDesc(function ($item) {
                    $ts = $item->raw_date ? $item->raw_date->timestamp : 0;
                    return sprintf('%012d_%010d', $ts, $item->id);
                });

                foreach ($merged as $row) {
                    fputcsv($file, [
                        $row->date,
                        $row->v_no,
                        $row->type,
                        $row->acc,
                        $row->party,
                        $row->mode,
                        $row->dr > 0 ? number_format($row->dr, 2, '.', '') : '0.00',
                        $row->cr > 0 ? number_format($row->cr, 2, '.', '') : '0.00',
                        $row->ref,
                        $row->inv,
                        $row->notes,
                    ]);
                }
            } elseif ($viewMode === 'receipts') {
                fputcsv($file, [
                    'Receipt Date',
                    'Voucher No',
                    'Customer / Source',
                    'Receiving Account',
                    'Payment Mode',
                    'Amount Received (INR)',
                    'Reference No',
                    'Against Invoice',
                    'Notes'
                ]);

                $receipts = (clone $receiptsQuery)->orderBy('voucher_date', 'desc')->get();
                foreach ($receipts as $r) {
                    $party = $r->receipt_type === 'Customer' && $r->customer ? $r->customer->name : ($r->income_source ?: 'Direct Inflow');
                    fputcsv($file, [
                        $r->voucher_date ? $r->voucher_date->format('d/m/Y') : '—',
                        $r->voucher_no,
                        $party,
                        $r->account ? $r->account->name : 'Cash/Bank',
                        $r->payment_mode ?: 'Cash',
                        number_format((float)$r->amount, 2, '.', ''),
                        $r->reference_no ?: '—',
                        $r->against_invoice ?: '—',
                        $r->notes ?: '',
                    ]);
                }
            } elseif ($viewMode === 'payments') {
                fputcsv($file, [
                    'Payment Date',
                    'Voucher No',
                    'Vendor / Expense Head',
                    'Paid From Account',
                    'Payment Mode',
                    'Amount Paid (INR)',
                    'Reference No',
                    'Against Bill',
                    'Notes'
                ]);

                $payments = (clone $paymentsQuery)->orderBy('voucher_date', 'desc')->get();
                foreach ($payments as $p) {
                    $party = $p->payment_type === 'Vendor' && $p->vendor ? $p->vendor->name : ($p->expense_head ?: 'Direct Expense');
                    fputcsv($file, [
                        $p->voucher_date ? $p->voucher_date->format('d/m/Y') : '—',
                        $p->voucher_no,
                        $party,
                        $p->account ? $p->account->name : 'Cash/Bank',
                        $p->payment_mode ?: 'Cash',
                        number_format((float)$p->amount, 2, '.', ''),
                        $p->reference_no ?: '—',
                        $p->against_invoice ?: '—',
                        $p->notes ?: '',
                    ]);
                }
            } elseif ($viewMode === 'accounts') {
                fputcsv($file, [
                    'Account Code',
                    'Account Name',
                    'Group',
                    'Bank Name',
                    'Account Number',
                    'Opening Balance (INR)',
                    'Total Period Inflow (Dr)',
                    'Total Period Outflow (Cr)',
                    'Net Period Movement',
                    'Current Closing Balance (INR)'
                ]);

                $allR = (clone $receiptsQuery)->get();
                $allP = (clone $paymentsQuery)->get();
                $accounts = Account::bankAndCash()->where('status', 'active')->orderBy('account_group')->orderBy('name')->get();

                foreach ($accounts as $acc) {
                    $inflow = (float)$allR->where('account_id', $acc->id)->sum('amount');
                    $outflow = (float)$allP->where('account_id', $acc->id)->sum('amount');
                    $net = $inflow - $outflow;

                    fputcsv($file, [
                        $acc->code ?: '—',
                        $acc->name,
                        $acc->account_group,
                        $acc->bank_name ?: '—',
                        $acc->account_number ?: '—',
                        number_format((float)$acc->opening_balance, 2, '.', ''),
                        number_format($inflow, 2, '.', ''),
                        number_format($outflow, 2, '.', ''),
                        number_format($net, 2, '.', ''),
                        number_format((float)$acc->current_balance, 2, '.', ''),
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
