/* ==========================================================================
   VIKAS UDHYOG ERP - Reports & Vouchers Controller
   ========================================================================== */

const Reports = {
    currentCashFilter: 'all',

    render(viewId) {
        if (viewId === 'rpt-ledger') this.renderItemLedgerReport();
        if (viewId === 'rpt-purchase') this.renderPurchaseReport();
        if (viewId === 'rpt-sales') this.renderSalesReport();
        if (viewId === 'rpt-order') this.renderOrderReport();
        if (viewId === 'rpt-cash-reg' || viewId === 'rpt-receipt-reg' || viewId === 'rpt-payment-reg') this.renderCashRegister();
    },

    // Purchase Report
    renderPurchaseReport() {
        const purchases = db.getAll('PURCHASES');
        const container = document.getElementById('rpt-purchase-body');
        if (!container) return;

        let totalAmount = 0;
        let totalGst = 0;
        let html = '';

        purchases.forEach(p => {
            totalAmount += (p.total || 0);
            totalGst += (p.gst || 0);

            html += `
                <tr>
                    <td>${p.date}</td>
                    <td><strong>${p.invNo}</strong></td>
                    <td>${p.vendorName}</td>
                    <td>₹${(p.subtotal || 0).toLocaleString('en-IN')}</td>
                    <td>₹${(p.gst || 0).toLocaleString('en-IN')}</td>
                    <td><strong>₹${(p.total || 0).toLocaleString('en-IN')}</strong></td>
                </tr>
            `;
        });
        container.innerHTML = html;

        document.getElementById('rpt-pur-total-val').innerText = `Total Purchase: ₹${totalAmount.toLocaleString('en-IN')} | Total GST: ₹${totalGst.toLocaleString('en-IN')}`;
    },

    // Sales Report
    renderSalesReport() {
        const sales = db.getAll('SALES_INVOICES');
        const container = document.getElementById('rpt-sales-body');
        if (!container) return;

        let totalAmount = 0;
        let totalGst = 0;
        let html = '';

        sales.forEach(s => {
            const gstSum = (s.cgst || 0) + (s.sgst || 0) + (s.igst || 0);
            totalAmount += (s.grandTotal || 0);
            totalGst += gstSum;

            html += `
                <tr>
                    <td>${s.invNo}</td>
                    <td>${s.date}</td>
                    <td><strong>${s.customerName}</strong></td>
                    <td>₹${(s.total || 0).toLocaleString('en-IN')}</td>
                    <td>₹${gstSum.toLocaleString('en-IN')}</td>
                    <td><strong>₹${(s.grandTotal || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge ${s.status === 'Paid' ? 'badge-success' : 'badge-warning'}">${s.status}</span></td>
                </tr>
            `;
        });
        container.innerHTML = html;

        document.getElementById('rpt-sales-total-val').innerText = `Total Sales: ₹${totalAmount.toLocaleString('en-IN')} | Total Tax: ₹${totalGst.toLocaleString('en-IN')}`;
    },

    // Order Report
    renderOrderReport() {
        const orders = db.getAll('SALES_ORDERS');
        const container = document.getElementById('rpt-order-body');
        if (!container) return;

        let html = '';
        orders.forEach(o => {
            html += `
                <tr>
                    <td><strong>${o.soNo}</strong></td>
                    <td>${o.date}</td>
                    <td>${o.customerName}</td>
                    <td>${o.delDate}</td>
                    <td><strong>₹${(o.total || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge badge-info">${o.status}</span></td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    // Cash & Bank Register (Combined Receipts & Payments)
    filterCashRegister(type, btnEl) {
        this.currentCashFilter = type;
        if (btnEl && btnEl.parentElement) {
            btnEl.parentElement.querySelectorAll('.cash-filter-btn').forEach(btn => btn.classList.remove('active'));
            btnEl.classList.add('active');
        }
        this.renderCashRegister();
    },

    renderCashRegister() {
        const container = document.getElementById('rpt-cash-body');
        if (!container) return;

        const receipts = (db.getAll('RECEIPTS') || []).map(r => ({
            id: r.id,
            voucherNo: r.rcpNo,
            date: r.date,
            partyName: r.customerName || 'N/A',
            mode: r.mode || 'Cash',
            debit: parseFloat(r.amount) || 0,
            credit: 0,
            ref: r.againstInv || '-',
            rawType: 'receipt'
        }));

        const payments = (db.getAll('PAYMENTS') || []).map(p => ({
            id: p.id,
            voucherNo: p.payNo,
            date: p.date,
            partyName: p.vendorName || 'N/A',
            mode: p.mode || 'Cash',
            debit: 0,
            credit: parseFloat(p.amount) || 0,
            ref: p.againstPur || '-',
            rawType: 'payment'
        }));

        let combined = [...receipts, ...payments];

        // Sort by date descending
        combined.sort((a, b) => new Date(b.date || 0) - new Date(a.date || 0));

        let filtered = combined;
        if (this.currentCashFilter === 'receipt') {
            filtered = combined.filter(item => item.rawType === 'receipt');
        } else if (this.currentCashFilter === 'payment') {
            filtered = combined.filter(item => item.rawType === 'payment');
        }

        let totalDebit = 0;
        let totalCredit = 0;
        let html = '';

        if (filtered.length === 0) {
            html = `<tr><td colspan="9" style="text-align:center; padding: 2rem; color: var(--text-muted);">No transactions found</td></tr>`;
        } else {
            filtered.forEach(item => {
                totalDebit += item.debit;
                totalCredit += item.credit;

                const typeBadge = item.rawType === 'receipt' 
                    ? `<span class="badge badge-success"><i class="fa-solid fa-arrow-down-left"></i> Receipt (Dr)</span>`
                    : `<span class="badge badge-danger"><i class="fa-solid fa-arrow-up-right"></i> Payment (Cr)</span>`;

                const debitStr = item.debit > 0 ? `<strong style="color: var(--status-success);">₹${item.debit.toLocaleString('en-IN')}</strong>` : '-';
                const creditStr = item.credit > 0 ? `<strong style="color: var(--status-danger);">₹${item.credit.toLocaleString('en-IN')}</strong>` : '-';

                html += `
                    <tr>
                        <td><strong>${item.voucherNo}</strong></td>
                        <td>${item.date}</td>
                        <td>${typeBadge}</td>
                        <td><strong>${item.partyName}</strong></td>
                        <td><span class="badge badge-info">${item.mode}</span></td>
                        <td>${debitStr}</td>
                        <td>${creditStr}</td>
                        <td>${item.ref}</td>
                        <td>
                            <button class="btn btn-sm btn-outline" onclick="Reports.printVoucher('${item.id}', '${item.rawType}')"><i class="fa-solid fa-print"></i> Voucher</button>
                        </td>
                    </tr>
                `;
            });
        }

        container.innerHTML = html;

        const totalEl = document.getElementById('rpt-cash-total-val');
        if (totalEl) {
            const netBalance = totalDebit - totalCredit;
            const netColor = netBalance >= 0 ? 'var(--status-success)' : 'var(--status-danger)';
            const netSign = netBalance >= 0 ? '+' : '';

            totalEl.innerHTML = `
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; background: var(--bg); padding: 0.75rem 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color); font-weight: 600;">
                    <div>Total Receipts (Dr): <span style="color: var(--status-success);">₹${totalDebit.toLocaleString('en-IN')}</span></div>
                    <div>Total Payments (Cr): <span style="color: var(--status-danger);">₹${totalCredit.toLocaleString('en-IN')}</span></div>
                    <div>Net Cash/Bank Balance: <span style="color: ${netColor};">${netSign}₹${netBalance.toLocaleString('en-IN')}</span></div>
                </div>
            `;
        }
    },

    printVoucher(id, type) {
        const v = type === 'receipt'
            ? db.getAll('RECEIPTS').find(item => item.id === id)
            : db.getAll('PAYMENTS').find(item => item.id === id);

        if (!v) return;

        const isReceipt = type === 'receipt';
        const title = isReceipt ? 'RECEIPT VOUCHER' : 'PAYMENT VOUCHER';
        const partyLabel = isReceipt ? 'Received From' : 'Paid To';
        const partyName = isReceipt ? v.customerName : v.vendorName;
        const voucherNo = isReceipt ? v.rcpNo : v.payNo;

        const printWin = window.open('', '_blank');
        printWin.document.write(`
            <html>
            <head>
                <title>${title} - ${voucherNo}</title>
                <style>
                    body { font-family: sans-serif; padding: 30px; border: 2px solid #183A1D; max-width: 650px; margin: 20px auto; }
                    .header { text-align: center; border-bottom: 2px dashed #6B8E23; padding-bottom: 15px; }
                    .header h2 { color: #183A1D; margin: 0; }
                    .voucher-title { background: #6B8E23; color: white; display: inline-block; padding: 4px 15px; margin-top: 10px; border-radius: 4px; font-weight: bold; }
                    .row { display: flex; justify-content: space-between; margin: 15px 0; font-size: 15px; }
                    .footer { margin-top: 50px; display: flex; justify-content: space-between; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h2>VIKAS UDHYOG</h2>
                    <p style="margin:2px;">Herbal Products Manufacturer & Supplier</p>
                    <span class="voucher-title">${title}</span>
                </div>
                <div class="row">
                    <div>Voucher No: <strong>${voucherNo}</strong></div>
                    <div>Date: <strong>${v.date}</strong></div>
                </div>
                <div style="margin: 20px 0; line-height: 2;">
                    <div>${partyLabel}: <strong>${partyName}</strong></div>
                    <div>Amount: <strong style="font-size: 18px;">₹${(v.amount || 0).toLocaleString('en-IN')}</strong></div>
                    <div>Payment Mode: <strong>${v.mode}</strong></div>
                    <div>Against Ref: <strong>${v.againstInv || v.againstPur || 'N/A'}</strong></div>
                    <div>Remarks: <i>${v.remarks || 'None'}</i></div>
                </div>
                <div class="footer">
                    <div>Receiver's Signature</div>
                    <div>Authorized Signatory<br><strong>VIKAS UDHYOG</strong></div>
                </div>
                <script>window.onload = function() { window.print(); }</script>
            </body>
            </html>
        `);
        printWin.document.close();
    }
};
