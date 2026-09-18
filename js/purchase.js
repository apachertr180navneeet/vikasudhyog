/* ==========================================================================
   VIKAS UDHYOG ERP - Purchase & Purchase Order Controller
   ========================================================================== */

const Transactions = {
    render(viewId) {
        if (viewId === 'txn-purchase') this.renderPurchaseEntry();
        if (viewId === 'txn-wb-purchase') this.renderWBPurchaseEntry();
        if (viewId === 'txn-sales-order') this.renderSalesOrder();
        if (viewId === 'txn-wb-sales') this.renderWBSalesEntry();
        if (viewId === 'txn-order-dispatch') this.renderOrderDispatch();
        if (viewId === 'txn-sales-invoice') this.renderSalesInvoice();
        if (viewId === 'txn-receipt') this.renderReceipt();
        if (viewId === 'txn-payment') this.renderPayment();
    },

    // 1. Purchase Entry
    renderPurchaseEntry() {
        const purchases = db.getAll('PURCHASES');
        const container = document.getElementById('purchase-table-body');
        if (!container) return;

        let html = '';
        purchases.forEach(p => {
            const billSub = p.subtotal || 0;
            const gst = p.gst || 0;
            const underBilling = p.underBillingTotal || 0;
            const grandTotal = p.total || (billSub + gst + underBilling);

            html += `
                <tr>
                    <td><strong>${p.invNo}</strong></td>
                    <td>${p.date}</td>
                    <td><strong>${p.vendorName}</strong>${p.brokerName ? `<div style="font-size:0.78rem; color:var(--text-muted);"><i class="fa-solid fa-handshake"></i> ${p.brokerName}</div>` : ''}</td>
                    <td>₹${billSub.toLocaleString('en-IN')}</td>
                    <td>₹${gst.toLocaleString('en-IN')}</td>
                    <td><span style="color:#d97706; font-weight:600;">₹${underBilling.toLocaleString('en-IN')}</span></td>
                    <td><strong>₹${grandTotal.toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge ${p.status === 'Paid' ? 'badge-success' : 'badge-warning'}">${p.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action view" onclick="Transactions.printInvoice('${p.id}', 'purchase')"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;

        // Populate Vendor Dropdown in Modal
        const vendorSelect = document.getElementById('pur-vendor');
        if (vendorSelect) {
            const vendors = db.getAll('VENDORS');
            let vHtml = `<option value="">Select Vendor...</option>`;
            vendors.forEach(v => vHtml += `<option value="${v.id}">${v.name}</option>`);
            vendorSelect.innerHTML = vHtml;
        }

        // Populate Broker Dropdown in Modal
        const brokerSelect = document.getElementById('pur-broker');
        if (brokerSelect) {
            const brokers = db.getAll('BROKERS');
            let bHtml = `<option value="">Select Broker (Optional)...</option>`;
            brokers.forEach(b => bHtml += `<option value="${b.id}">${b.name} (${b.city || 'Sojat'} - ${b.commissionRate}%)</option>`);
            brokerSelect.innerHTML = bHtml;
        }

        this.initPurchaseItemRow();
    },

    getUnitOptions(selectedUnit = 'KG') {
        const units = ['KG', 'GRAM', 'BAG', 'BOX', 'PCS', 'PACKET', 'LITER'];
        return units.map(u => `<option value="${u}" ${u === selectedUnit ? 'selected' : ''}>${u}</option>`).join('');
    },

    updatePurchaseRowNumbers() {
        document.querySelectorAll('#purchase-items-body tr').forEach((tr, index) => {
            const sno = tr.querySelector('.pur-sno');
            if (sno) sno.innerText = index + 1;
        });
    },

    initPurchaseItemRow() {
        const tbody = document.getElementById('purchase-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (Code: ${i.code})</option>`);

        tbody.innerHTML = `
            <tr>
                <td style="text-align:center;"><span class="pur-sno" style="font-weight:600;">1</span></td>
                <td><select class="form-control pur-item-select" onchange="Transactions.onPurchaseItemChange(this)">${optionsHtml}</select></td>
                <td><input type="text" class="form-control pur-batch" value="B-2026-09"></td>
                <td><input type="text" class="form-control pur-hsn" value="1404"></td>
                <td><input type="number" class="form-control pur-gst" value="18" oninput="Transactions.calcPurchaseRow(this)"></td>
                <td><select class="form-control pur-unit">${this.getUnitOptions('KG')}</select></td>
                <td><input type="number" class="form-control pur-qty" value="100" min="0" step="any" oninput="Transactions.calcPurchaseRow(this)"></td>
                <td><input type="number" class="form-control pur-actual-rate" value="120" step="any" oninput="Transactions.calcPurchaseRow(this, 'actual')"></td>
                <td><input type="number" class="form-control pur-bill-rate" value="60" step="any" oninput="Transactions.calcPurchaseRow(this, 'bill')"></td>
                <td><input type="number" class="form-control pur-ub-rate" value="60" step="any" oninput="Transactions.calcPurchaseRow(this, 'ub')"></td>
                <td><input type="number" class="form-control pur-bill-amt" value="7080.00" readonly></td>
                <td><input type="number" class="form-control pur-under-amt" value="6000.00" readonly></td>
                <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updatePurchaseRowNumbers(); Transactions.calcPurchaseTotals();"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `;
        this.updatePurchaseRowNumbers();
        this.calcPurchaseTotals();
    },

    addPurchaseRow() {
        const tbody = document.getElementById('purchase-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (${i.code})</option>`);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align:center;"><span class="pur-sno" style="font-weight:600;">1</span></td>
            <td><select class="form-control pur-item-select" onchange="Transactions.onPurchaseItemChange(this)">${optionsHtml}</select></td>
            <td><input type="text" class="form-control pur-batch" value="B-2026-09"></td>
            <td><input type="text" class="form-control pur-hsn" value="1404"></td>
            <td><input type="number" class="form-control pur-gst" value="18" oninput="Transactions.calcPurchaseRow(this)"></td>
            <td><select class="form-control pur-unit">${this.getUnitOptions('KG')}</select></td>
            <td><input type="number" class="form-control pur-qty" value="10" min="0" step="any" oninput="Transactions.calcPurchaseRow(this)"></td>
            <td><input type="number" class="form-control pur-actual-rate" value="100" step="any" oninput="Transactions.calcPurchaseRow(this, 'actual')"></td>
            <td><input type="number" class="form-control pur-bill-rate" value="50" step="any" oninput="Transactions.calcPurchaseRow(this, 'bill')"></td>
            <td><input type="number" class="form-control pur-ub-rate" value="50" step="any" oninput="Transactions.calcPurchaseRow(this, 'ub')"></td>
            <td><input type="number" class="form-control pur-bill-amt" value="590.00" readonly></td>
            <td><input type="number" class="form-control pur-under-amt" value="500.00" readonly></td>
            <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updatePurchaseRowNumbers(); Transactions.calcPurchaseTotals();"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        this.updatePurchaseRowNumbers();
        this.calcPurchaseTotals();
    },

    onPurchaseItemChange(selectEl) {
        const tr = selectEl.closest('tr');
        const items = db.getAll('ITEMS');
        const item = items.find(i => i.id === selectEl.value);

        if (item) {
            tr.querySelector('.pur-hsn').value = item.hsn || '1404';
            tr.querySelector('.pur-gst').value = item.gst || 18;
            tr.querySelector('.pur-unit').value = item.unit || 'KG';
            const actualRate = item.purchaseRate || 120;
            const billRate = item.billRate || Math.round(actualRate * 0.5);
            const ubRate = Math.max(0, actualRate - billRate);

            tr.querySelector('.pur-actual-rate').value = actualRate;
            tr.querySelector('.pur-bill-rate').value = billRate;
            tr.querySelector('.pur-ub-rate').value = ubRate;
            this.calcPurchaseRow(selectEl);
        }
    },

    calcPurchaseRow(inputEl, source) {
        const tr = inputEl.closest('tr');
        const netWeight = parseFloat(tr.querySelector('.pur-qty').value) || 0;
        let actualRate = parseFloat(tr.querySelector('.pur-actual-rate').value) || 0;
        let billRate = parseFloat(tr.querySelector('.pur-bill-rate').value) || 0;
        let ubRate = parseFloat(tr.querySelector('.pur-ub-rate').value) || 0;
        const gst = parseFloat(tr.querySelector('.pur-gst').value) || 0;

        if (source === 'actual' || source === 'bill') {
            ubRate = Math.max(0, actualRate - billRate);
            tr.querySelector('.pur-ub-rate').value = ubRate.toFixed(2);
        } else if (source === 'ub') {
            actualRate = billRate + ubRate;
            tr.querySelector('.pur-actual-rate').value = actualRate.toFixed(2);
        }

        const billSub = netWeight * billRate;
        const billTax = billSub * (gst / 100);
        const billAmt = billSub + billTax;
        const underAmt = netWeight * ubRate;

        tr.querySelector('.pur-bill-amt').value = billAmt.toFixed(2);
        tr.querySelector('.pur-under-amt').value = underAmt.toFixed(2);

        this.calcPurchaseTotals();
    },

    calcPurchaseTotals() {
        let subtotal = 0;
        let totalGst = 0;
        let totalUnderBilling = 0;

        document.querySelectorAll('#purchase-items-body tr').forEach(tr => {
            const netWeight = parseFloat(tr.querySelector('.pur-qty').value) || 0;
            const billRate = parseFloat(tr.querySelector('.pur-bill-rate').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.pur-ub-rate').value) || 0;
            const gst = parseFloat(tr.querySelector('.pur-gst').value) || 0;

            const lineSub = netWeight * billRate;
            const lineGst = lineSub * (gst / 100);
            const lineUnder = netWeight * ubRate;

            subtotal += lineSub;
            totalGst += lineGst;
            totalUnderBilling += lineUnder;
        });

        const billTotal = subtotal + totalGst;
        const grandTotal = billTotal + totalUnderBilling;

        const subEl = document.getElementById('pur-summary-subtotal');
        const gstEl = document.getElementById('pur-summary-gst');
        const billTotEl = document.getElementById('pur-summary-billtotal');
        const underEl = document.getElementById('pur-summary-underbilling');
        const grandEl = document.getElementById('pur-summary-grandtotal');

        if (subEl) subEl.innerText = `₹${subtotal.toFixed(2)}`;
        if (gstEl) gstEl.innerText = `₹${totalGst.toFixed(2)}`;
        if (billTotEl) billTotEl.innerText = `₹${billTotal.toFixed(2)}`;
        if (underEl) underEl.innerText = `₹${totalUnderBilling.toFixed(2)}`;
        if (grandEl) grandEl.innerText = `₹${Math.round(grandTotal).toLocaleString('en-IN')}`;
    },

    savePurchase() {
        const vendorSelect = document.getElementById('pur-vendor');
        const vendorId = vendorSelect.value;
        const invNo = document.getElementById('pur-inv-no').value || 'PUR-' + Date.now().toString().substr(-5);
        const date = document.getElementById('pur-date').value || new Date().toISOString().split('T')[0];

        if (!vendorId) return App.showToast('Please select a Vendor', 'danger');

        const vendor = db.getAll('VENDORS').find(v => v.id === vendorId);
        const lineItems = [];
        let subtotal = 0;
        let totalGst = 0;
        let totalUnderBilling = 0;

        document.querySelectorAll('#purchase-items-body tr').forEach(tr => {
            const select = tr.querySelector('.pur-item-select');
            const itemId = select.value;
            const netWeight = parseFloat(tr.querySelector('.pur-qty').value) || 0;
            const actualRate = parseFloat(tr.querySelector('.pur-actual-rate').value) || 0;
            const billRate = parseFloat(tr.querySelector('.pur-bill-rate').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.pur-ub-rate').value) || 0;
            const gst = parseFloat(tr.querySelector('.pur-gst').value) || 0;
            const hsn = tr.querySelector('.pur-hsn').value || '1404';
            const unit = tr.querySelector('.pur-unit').value || 'KG';
            const batch = tr.querySelector('.pur-batch').value;

            if (itemId && netWeight > 0) {
                const itemObj = db.getAll('ITEMS').find(i => i.id === itemId);
                const lineSub = netWeight * billRate;
                const lineGst = lineSub * (gst / 100);
                const lineUnder = netWeight * ubRate;

                subtotal += lineSub;
                totalGst += lineGst;
                totalUnderBilling += lineUnder;

                lineItems.push({
                    itemId,
                    itemName: itemObj ? itemObj.name : 'Herbal Product',
                    batch,
                    hsn,
                    gst,
                    unit,
                    qty: netWeight,
                    netWeight,
                    actualRate,
                    billRate,
                    ubRate,
                    rate: billRate,
                    billAmount: lineSub + lineGst,
                    underAmount: lineUnder,
                    amount: lineSub + lineGst + lineUnder
                });

                // Increase Stock in database
                db.adjustStock(itemId, netWeight);
            }
        });

        if (lineItems.length === 0) return App.showToast('Please add at least one product item', 'danger');

        const total = Math.round(subtotal + totalGst + totalUnderBilling);

        const brokerSelect = document.getElementById('pur-broker');
        const brokerId = brokerSelect ? brokerSelect.value : '';
        const brokerObj = brokerId ? db.getAll('BROKERS').find(b => b.id === brokerId) : null;

        const orderTypeSelect = document.getElementById('pur-order-type');
        const orderType = orderTypeSelect ? orderTypeSelect.value : 'Medium';

        const purchaseRecord = {
            invNo,
            vendorId,
            vendorName: vendor ? vendor.name : 'Supplier',
            brokerId: brokerId || '',
            brokerName: brokerObj ? brokerObj.name : '',
            orderType,
            date,
            warehouse: 'Main Factory Storage',
            subtotal,
            gst: totalGst,
            billTotal: subtotal + totalGst,
            underBillingTotal: totalUnderBilling,
            total,
            status: 'Pending',
            items: lineItems
        };

        db.saveItem('PURCHASES', purchaseRecord);
        // Increase vendor outstanding balance
        if (vendor) db.updateVendorBalance(vendor.name, total);

        App.showToast('Purchase entry saved with Under Billing!', 'success');
        App.closeModal('modal-purchase');
        this.renderPurchaseEntry();
    },

    // 1b. WB Purchase Entry (Without Bill)
    renderWBPurchaseEntry() {
        const wbPurchases = db.getAll('WB_PURCHASES');
        const container = document.getElementById('wb-purchase-table-body');
        if (!container) return;

        let html = '';
        wbPurchases.forEach(p => {
            const grandTotal = p.totalAmount || 0;
            const netWeight = p.totalWeight || 0;

            html += `
                <tr>
                    <td><strong>${p.slipNo}</strong></td>
                    <td>${p.date}</td>
                    <td><strong>${p.vendorName}</strong>${p.brokerName ? `<div style="font-size:0.78rem; color:var(--text-muted);"><i class="fa-solid fa-handshake"></i> ${p.brokerName}</div>` : ''}</td>
                    <td><strong>${netWeight} KG</strong></td>
                    <td><span style="color:#d97706; font-weight:700;">₹${grandTotal.toLocaleString('en-IN')}</span></td>
                    <td><span class="badge ${p.status === 'Completed' ? 'badge-success' : 'badge-warning'}">${p.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action view" onclick="Transactions.printInvoice('${p.id}', 'purchase')"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;

        // Populate Vendor Dropdown in WB Modal
        const vendorSelect = document.getElementById('wbp-vendor');
        if (vendorSelect) {
            const vendors = db.getAll('VENDORS');
            let vHtml = `<option value="">Select Vendor...</option>`;
            vendors.forEach(v => vHtml += `<option value="${v.id}">${v.name}</option>`);
            vendorSelect.innerHTML = vHtml;
        }

        // Populate Broker Dropdown in WB Modal
        const brokerSelect = document.getElementById('wbp-broker');
        if (brokerSelect) {
            const brokers = db.getAll('BROKERS');
            let bHtml = `<option value="">Select Broker (Optional)...</option>`;
            brokers.forEach(b => bHtml += `<option value="${b.id}">${b.name} (${b.city || 'Sojat'} - ${b.commissionRate}%)</option>`);
            brokerSelect.innerHTML = bHtml;
        }

        this.initWBPurchaseItemRow();
    },

    updateWBPurchaseRowNumbers() {
        document.querySelectorAll('#wbp-items-body tr').forEach((tr, index) => {
            const sno = tr.querySelector('.wbp-sno');
            if (sno) sno.innerText = index + 1;
        });
    },

    initWBPurchaseItemRow() {
        const tbody = document.getElementById('wbp-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (Code: ${i.code})</option>`);

        tbody.innerHTML = `
            <tr>
                <td style="text-align:center;"><span class="wbp-sno" style="font-weight:600;">1</span></td>
                <td><select class="form-control wbp-item-select" onchange="Transactions.onWBPurchaseItemChange(this)">${optionsHtml}</select><input type="hidden" class="wbp-hsn" value="1404"></td>
                <td><input type="text" class="form-control wbp-batch" value="B-2026-09"></td>
                <td><input type="number" class="form-control wbp-ub-rate" value="60" step="any" oninput="Transactions.calcWBPurchaseRow(this)"></td>
                <td><select class="form-control wbp-unit">${this.getUnitOptions('KG')}</select></td>
                <td><input type="number" class="form-control wbp-qty" value="100" min="0" step="any" oninput="Transactions.calcWBPurchaseRow(this)"></td>
                <td><input type="number" class="form-control wbp-under-amt" value="6000.00" readonly></td>
                <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateWBPurchaseRowNumbers(); Transactions.calcWBPurchaseTotals();"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `;
        this.updateWBPurchaseRowNumbers();
        this.calcWBPurchaseTotals();
    },

    addWBPurchaseRow() {
        const tbody = document.getElementById('wbp-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (${i.code})</option>`);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align:center;"><span class="wbp-sno" style="font-weight:600;">1</span></td>
            <td><select class="form-control wbp-item-select" onchange="Transactions.onWBPurchaseItemChange(this)">${optionsHtml}</select><input type="hidden" class="wbp-hsn" value="1404"></td>
            <td><input type="text" class="form-control wbp-batch" value="B-2026-09"></td>
            <td><input type="number" class="form-control wbp-ub-rate" value="50" step="any" oninput="Transactions.calcWBPurchaseRow(this)"></td>
            <td><select class="form-control wbp-unit">${this.getUnitOptions('KG')}</select></td>
            <td><input type="number" class="form-control wbp-qty" value="10" min="0" step="any" oninput="Transactions.calcWBPurchaseRow(this)"></td>
            <td><input type="number" class="form-control wbp-under-amt" value="500.00" readonly></td>
            <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateWBPurchaseRowNumbers(); Transactions.calcWBPurchaseTotals();"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        this.updateWBPurchaseRowNumbers();
        this.calcWBPurchaseTotals();
    },

    onWBPurchaseItemChange(selectEl) {
        const tr = selectEl.closest('tr');
        const items = db.getAll('ITEMS');
        const item = items.find(i => i.id === selectEl.value);

        if (item) {
            tr.querySelector('.wbp-hsn').value = item.hsn || '1404';
            tr.querySelector('.wbp-unit').value = item.unit || 'KG';
            const actualRate = item.purchaseRate || 120;
            const ubRate = item.billRate || Math.round(actualRate * 0.5);
            tr.querySelector('.wbp-ub-rate').value = ubRate;
            this.calcWBPurchaseRow(selectEl);
        }
    },

    calcWBPurchaseRow(inputEl) {
        const tr = inputEl.closest('tr');
        const netWeight = parseFloat(tr.querySelector('.wbp-qty').value) || 0;
        const ubRate = parseFloat(tr.querySelector('.wbp-ub-rate').value) || 0;
        const underAmt = netWeight * ubRate;

        tr.querySelector('.wbp-under-amt').value = underAmt.toFixed(2);
        this.calcWBPurchaseTotals();
    },

    calcWBPurchaseTotals() {
        let totalWeight = 0;
        let totalAmount = 0;

        document.querySelectorAll('#wbp-items-body tr').forEach(tr => {
            const netWeight = parseFloat(tr.querySelector('.wbp-qty').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.wbp-ub-rate').value) || 0;
            totalWeight += netWeight;
            totalAmount += (netWeight * ubRate);
        });

        const grandEl = document.getElementById('wbp-summary-total');
        if (grandEl) grandEl.innerText = `₹${Math.round(totalAmount).toLocaleString('en-IN')}`;
    },

    saveWBPurchase() {
        const vendorSelect = document.getElementById('wbp-vendor');
        const vendorId = vendorSelect ? vendorSelect.value : '';
        const slipNo = document.getElementById('wbp-slip-no').value || 'WBP-' + Date.now().toString().substr(-5);
        const date = document.getElementById('wbp-date').value || new Date().toISOString().split('T')[0];

        if (!vendorId) return App.showToast('Please select a Vendor', 'danger');

        const vendor = db.getAll('VENDORS').find(v => v.id === vendorId);
        const lineItems = [];
        let totalWeight = 0;
        let totalAmount = 0;

        document.querySelectorAll('#wbp-items-body tr').forEach(tr => {
            const select = tr.querySelector('.wbp-item-select');
            const itemId = select.value;
            const netWeight = parseFloat(tr.querySelector('.wbp-qty').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.wbp-ub-rate').value) || 0;
            const hsn = tr.querySelector('.wbp-hsn').value || '1404';
            const unit = tr.querySelector('.wbp-unit').value || 'KG';
            const batch = tr.querySelector('.wbp-batch').value;

            if (itemId && netWeight > 0) {
                const itemObj = db.getAll('ITEMS').find(i => i.id === itemId);
                const lineUnder = netWeight * ubRate;

                totalWeight += netWeight;
                totalAmount += lineUnder;

                lineItems.push({
                    itemId,
                    itemName: itemObj ? itemObj.name : 'Herbal Product',
                    batch,
                    hsn,
                    unit,
                    qty: netWeight,
                    netWeight,
                    ubRate,
                    underAmount: lineUnder,
                    amount: lineUnder
                });

                // Increase Stock in database (WB Entry)
                db.adjustStock(itemId, netWeight);
            }
        });

        if (lineItems.length === 0) return App.showToast('Please add at least one product item', 'danger');

        const wbpBrokerSelect = document.getElementById('wbp-broker');
        const brokerId = wbpBrokerSelect ? wbpBrokerSelect.value : '';
        const brokerObj = brokerId ? db.getAll('BROKERS').find(b => b.id === brokerId) : null;

        const wbpOrderTypeSelect = document.getElementById('wbp-order-type');
        const orderType = wbpOrderTypeSelect ? wbpOrderTypeSelect.value : 'Medium';

        const wbRecord = {
            slipNo,
            vendorId,
            vendorName: vendor ? vendor.name : 'Supplier',
            brokerId: brokerId || '',
            brokerName: brokerObj ? brokerObj.name : '',
            orderType,
            date,
            totalWeight,
            totalAmount: Math.round(totalAmount),
            status: 'Recorded',
            items: lineItems
        };

        db.saveItem('WB_PURCHASES', wbRecord);
        if (vendor) db.updateVendorBalance(vendor.name, totalAmount);

        App.showToast(`WB Purchase Entry ${slipNo} saved & stock increased!`, 'success');
        App.closeModal('modal-wb-purchase');
        this.renderWBPurchaseEntry();
    }
};
