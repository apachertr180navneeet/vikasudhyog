/* ==========================================================================
   VIKAS UDHYOG ERP - Sales & Order Dispatch Controller
   ========================================================================== */

// Extend Transactions Object for Sales
Object.assign(Transactions, {
    // 3. Sales Order
    renderSalesOrder() {
        const orders = db.getAll('SALES_ORDERS');
        const container = document.getElementById('so-table-body');
        if (container) {
            let html = '';
            orders.forEach(o => {
                let statusBadge = 'badge-warning';
                if (o.status === 'Dispatched') statusBadge = 'badge-info';
                if (o.status === 'Completed') statusBadge = 'badge-success';

                const billAmt = o.billTotal || (o.total ? Math.round(o.total / 2) : 0);
                const underAmt = o.underBillingTotal || (o.total ? Math.round(o.total / 2) : 0);
                const grandTotal = o.total || (billAmt + underAmt);

                html += `
                    <tr>
                        <td><strong>${o.soNo}</strong></td>
                        <td>${o.date}</td>
                        <td><strong>${o.customerName}</strong>${o.brokerName ? `<div style="font-size:0.78rem; color:var(--text-muted);"><i class="fa-solid fa-handshake"></i> ${o.brokerName}</div>` : ''}</td>
                        <td>${o.delDate}</td>
                        <td>₹${billAmt.toLocaleString('en-IN')}</td>
                        <td><span style="color:#d97706; font-weight:600;">₹${underAmt.toLocaleString('en-IN')}</span></td>
                        <td><strong>₹${grandTotal.toLocaleString('en-IN')}</strong></td>
                        <td><span class="badge ${statusBadge}">${o.status}</span></td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-action edit" onclick="Transactions.openDispatchModal('${o.soNo}', '${o.customerName}')" title="Dispatch Order"><i class="fa-solid fa-truck"></i></button>
                            </div>
                        </td>
                    </tr>
                `;
            });
            container.innerHTML = html;
        }

        // Populate Customers
        const custSelect = document.getElementById('so-customer');
        if (custSelect) {
            const customers = db.getAll('CUSTOMERS');
            let cHtml = `<option value="">Select Customer...</option>`;
            customers.forEach(c => cHtml += `<option value="${c.id}">${c.name}</option>`);
            custSelect.innerHTML = cHtml;
        }

        // Populate Brokers
        const soBrokerSelect = document.getElementById('so-broker');
        if (soBrokerSelect) {
            const brokers = db.getAll('BROKERS');
            let bHtml = `<option value="">Select Broker (Optional)...</option>`;
            brokers.forEach(b => bHtml += `<option value="${b.id}">${b.name} (${b.city || 'Sojat'} - ${b.commissionRate}%)</option>`);
            soBrokerSelect.innerHTML = bHtml;
        }

        // Set default dates and SO number
        const soDate = document.getElementById('so-date');
        const soDelDate = document.getElementById('so-del-date');
        const soNoInput = document.getElementById('so-no');
        const today = new Date();
        const nextWeek = new Date();
        nextWeek.setDate(today.getDate() + 5);

        if (soDate && !soDate.value) soDate.value = today.toISOString().split('T')[0];
        if (soDelDate && !soDelDate.value) soDelDate.value = nextWeek.toISOString().split('T')[0];
        if (soNoInput) soNoInput.value = 'SO-' + (1020 + orders.length + 1);

        this.initSOItemRow();
    },

    getUnitOptions(selectedUnit = 'KG') {
        const units = ['KG', 'GRAM', 'BAG', 'BOX', 'PCS', 'PACKET', 'LITER'];
        return units.map(u => `<option value="${u}" ${u === selectedUnit ? 'selected' : ''}>${u}</option>`).join('');
    },

    updateSORowNumbers() {
        document.querySelectorAll('#so-items-body tr').forEach((tr, index) => {
            const sno = tr.querySelector('.so-sno');
            if (sno) sno.innerText = index + 1;
        });
    },

    initSOItemRow() {
        const tbody = document.getElementById('so-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (Stock: ${i.stock} ${i.unit})</option>`);

        tbody.innerHTML = `
            <tr>
                <td style="text-align:center;"><span class="so-sno" style="font-weight:600;">1</span></td>
                <td><select class="form-control so-item-select" onchange="Transactions.onSOItemChange(this)">${optionsHtml}</select></td>
                <td><input type="text" class="form-control so-batch" value="B-2026-08"></td>
                <td><input type="text" class="form-control so-hsn" value="1404"></td>
                <td><input type="number" class="form-control so-gst" value="18" oninput="Transactions.calcSORow(this)"></td>
                <td><select class="form-control so-unit">${this.getUnitOptions('KG')}</select></td>
                <td><input type="number" class="form-control so-qty" value="50" min="0" step="any" oninput="Transactions.calcSORow(this)"></td>
                <td><input type="number" class="form-control so-actual-rate" value="180" step="any" oninput="Transactions.calcSORow(this, 'actual')"></td>
                <td><input type="number" class="form-control so-bill-rate" value="90" step="any" oninput="Transactions.calcSORow(this, 'bill')"></td>
                <td><input type="number" class="form-control so-ub-rate" value="90" step="any" oninput="Transactions.calcSORow(this, 'ub')"></td>
                <td><input type="number" class="form-control so-bill-amt" value="5310.00" readonly></td>
                <td><input type="number" class="form-control so-under-amt" value="4500.00" readonly></td>
                <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateSORowNumbers(); Transactions.calcSOTotals();"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `;
        this.updateSORowNumbers();
        this.calcSOTotals();
    },

    addSORow() {
        const tbody = document.getElementById('so-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (${i.stock} ${i.unit})</option>`);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align:center;"><span class="so-sno" style="font-weight:600;">1</span></td>
            <td><select class="form-control so-item-select" onchange="Transactions.onSOItemChange(this)">${optionsHtml}</select></td>
            <td><input type="text" class="form-control so-batch" value="B-2026-08"></td>
            <td><input type="text" class="form-control so-hsn" value="1404"></td>
            <td><input type="number" class="form-control so-gst" value="18" oninput="Transactions.calcSORow(this)"></td>
            <td><select class="form-control so-unit">${this.getUnitOptions('KG')}</select></td>
            <td><input type="number" class="form-control so-qty" value="10" min="0" step="any" oninput="Transactions.calcSORow(this)"></td>
            <td><input type="number" class="form-control so-actual-rate" value="140" step="any" oninput="Transactions.calcSORow(this, 'actual')"></td>
            <td><input type="number" class="form-control so-bill-rate" value="70" step="any" oninput="Transactions.calcSORow(this, 'bill')"></td>
            <td><input type="number" class="form-control so-ub-rate" value="70" step="any" oninput="Transactions.calcSORow(this, 'ub')"></td>
            <td><input type="number" class="form-control so-bill-amt" value="826.00" readonly></td>
            <td><input type="number" class="form-control so-under-amt" value="700.00" readonly></td>
            <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateSORowNumbers(); Transactions.calcSOTotals();"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        this.updateSORowNumbers();
        this.calcSOTotals();
    },

    onSOItemChange(selectEl) {
        const tr = selectEl.closest('tr');
        const items = db.getAll('ITEMS');
        const item = items.find(i => i.id === selectEl.value);

        if (item) {
            tr.querySelector('.so-hsn').value = item.hsn || '1404';
            tr.querySelector('.so-gst').value = item.gst || 18;
            tr.querySelector('.so-unit').value = item.unit || 'KG';
            const actualRate = item.saleRate || 180;
            const billRate = item.billRate || Math.round(actualRate * 0.5);
            const ubRate = Math.max(0, actualRate - billRate);

            tr.querySelector('.so-actual-rate').value = actualRate;
            tr.querySelector('.so-bill-rate').value = billRate;
            tr.querySelector('.so-ub-rate').value = ubRate;
            this.calcSORow(selectEl);
        }
    },

    calcSORow(inputEl, source) {
        const tr = inputEl.closest('tr');
        const netWeight = parseFloat(tr.querySelector('.so-qty').value) || 0;
        let actualRate = parseFloat(tr.querySelector('.so-actual-rate').value) || 0;
        let billRate = parseFloat(tr.querySelector('.so-bill-rate').value) || 0;
        let ubRate = parseFloat(tr.querySelector('.so-ub-rate').value) || 0;
        const gst = parseFloat(tr.querySelector('.so-gst').value) || 0;

        if (source === 'actual' || source === 'bill') {
            ubRate = Math.max(0, actualRate - billRate);
            tr.querySelector('.so-ub-rate').value = ubRate.toFixed(2);
        } else if (source === 'ub') {
            actualRate = billRate + ubRate;
            tr.querySelector('.so-actual-rate').value = actualRate.toFixed(2);
        }

        const billSub = netWeight * billRate;
        const billTax = billSub * (gst / 100);
        const billAmt = billSub + billTax;
        const underAmt = netWeight * ubRate;

        tr.querySelector('.so-bill-amt').value = billAmt.toFixed(2);
        tr.querySelector('.so-under-amt').value = underAmt.toFixed(2);

        this.calcSOTotals();
    },

    calcSOTotals() {
        let totalBill = 0;
        let totalUnder = 0;

        document.querySelectorAll('#so-items-body tr').forEach(tr => {
            const billAmt = parseFloat(tr.querySelector('.so-bill-amt').value) || 0;
            const underAmt = parseFloat(tr.querySelector('.so-under-amt').value) || 0;
            totalBill += billAmt;
            totalUnder += underAmt;
        });

        const grandTotal = totalBill + totalUnder;

        const billEl = document.getElementById('so-summary-billing');
        const underEl = document.getElementById('so-summary-underbilling');
        const totalEl = document.getElementById('so-summary-total');

        if (billEl) billEl.innerText = `₹${Math.round(totalBill).toLocaleString('en-IN')}`;
        if (underEl) underEl.innerText = `₹${Math.round(totalUnder).toLocaleString('en-IN')}`;
        if (totalEl) totalEl.innerText = `₹${Math.round(grandTotal).toLocaleString('en-IN')}`;
    },

    saveSalesOrder(e) {
        if (e) e.preventDefault();
        const custSelect = document.getElementById('so-customer');
        const custId = custSelect ? custSelect.value : '';
        const custObj = db.getAll('CUSTOMERS').find(c => c.id === custId);
        const soNo = document.getElementById('so-no').value || 'SO-' + Date.now().toString().substr(-4);
        const date = document.getElementById('so-date').value || new Date().toISOString().split('T')[0];
        const delDate = document.getElementById('so-del-date').value || new Date().toISOString().split('T')[0];

        if (!custId) return App.showToast('Please select a Customer', 'danger');

        const lineItems = [];
        let totalBill = 0;
        let totalUnder = 0;

        document.querySelectorAll('#so-items-body tr').forEach(tr => {
            const select = tr.querySelector('.so-item-select');
            const itemId = select.value;
            const netWeight = parseFloat(tr.querySelector('.so-qty').value) || 0;
            const actualRate = parseFloat(tr.querySelector('.so-actual-rate').value) || 0;
            const billRate = parseFloat(tr.querySelector('.so-bill-rate').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.so-ub-rate').value) || 0;
            const gst = parseFloat(tr.querySelector('.so-gst').value) || 0;
            const hsn = tr.querySelector('.so-hsn').value || '1404';
            const unit = tr.querySelector('.so-unit').value || 'KG';
            const batch = tr.querySelector('.so-batch').value || 'B-2026-08';

            if (itemId && netWeight > 0) {
                const itemObj = db.getAll('ITEMS').find(i => i.id === itemId);
                const billSub = netWeight * billRate;
                const billTax = billSub * (gst / 100);
                const billAmt = billSub + billTax;
                const underAmt = netWeight * ubRate;
                const lineTotal = billAmt + underAmt;

                totalBill += billAmt;
                totalUnder += underAmt;

                lineItems.push({
                    itemId,
                    itemName: itemObj ? itemObj.name : 'Product Item',
                    batch,
                    hsn,
                    gst,
                    unit,
                    qty: netWeight,
                    netWeight,
                    actualRate,
                    billRate,
                    ubRate,
                    billAmount: billAmt,
                    underAmount: underAmt,
                    amount: lineTotal
                });
            }
        });

        if (lineItems.length === 0) return App.showToast('Please add at least one product item', 'danger');

        const soBrokerSelect = document.getElementById('so-broker');
        const brokerId = soBrokerSelect ? soBrokerSelect.value : '';
        const brokerObj = brokerId ? db.getAll('BROKERS').find(b => b.id === brokerId) : null;

        const soOrderTypeSelect = document.getElementById('so-order-type');
        const orderType = soOrderTypeSelect ? soOrderTypeSelect.value : 'Medium';

        const orderRecord = {
            soNo,
            customerId: custId,
            customerName: custObj ? custObj.name : 'Customer',
            brokerId: brokerId || '',
            brokerName: brokerObj ? brokerObj.name : '',
            orderType,
            date,
            delDate,
            billTotal: Math.round(totalBill),
            underBillingTotal: Math.round(totalUnder),
            total: Math.round(grandTotal),
            status: 'Pending',
            items: lineItems
        };

        db.saveItem('SALES_ORDERS', orderRecord);

        App.showToast(`Sales Entry ${soNo} created with Under Billing!`, 'success');
        App.closeModal('modal-sales-order');
        this.renderSalesOrder();
    },

    // 4. Order Dispatch Modal & Workflow
    renderOrderDispatch() {
        const dispatches = db.getAll('DISPATCHES');
        const container = document.getElementById('dispatch-table-body');
        if (!container) return;

        let html = '';
        dispatches.forEach(d => {
            html += `
                <tr>
                    <td><strong>${d.orderNo}</strong></td>
                    <td><strong>${d.customerName}</strong></td>
                    <td>${d.date}</td>
                    <td>${d.vehicleNo}</td>
                    <td>${d.driverName}</td>
                    <td>${d.transporter}</td>
                    <td><span class="badge badge-success">${d.status}</span></td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    openDispatchModal(orderNo, customerName) {
        document.getElementById('dsp-order-no').value = orderNo;
        document.getElementById('dsp-customer').value = customerName;
        App.openModal('modal-dispatch');
    },

    confirmDispatch() {
        const orderNo = document.getElementById('dsp-order-no').value;
        const customerName = document.getElementById('dsp-customer').value;
        const vehicleNo = document.getElementById('dsp-vehicle').value || 'RJ-19-GB-8842';
        const driverName = document.getElementById('dsp-driver').value || 'Ramesh Kumar';
        const transporter = document.getElementById('dsp-transporter').value || 'Vikas Express Cargo';

        const dispatchRecord = {
            orderNo,
            customerName,
            date: new Date().toISOString().split('T')[0],
            vehicleNo,
            driverName,
            transporter,
            status: 'Dispatched'
        };

        db.saveItem('DISPATCHES', dispatchRecord);

        // Update Sales Order Status to Dispatched
        const orders = db.getAll('SALES_ORDERS');
        const order = orders.find(o => o.soNo === orderNo);
        if (order) {
            order.status = 'Dispatched';
            db.set(STORAGE_KEYS.SALES_ORDERS, orders);
        }

        App.showToast(`Order ${orderNo} dispatched successfully!`, 'success');
        App.closeModal('modal-dispatch');
        this.renderSalesOrder();
        this.renderOrderDispatch();
    },

    // 5. GST Sales Invoice
    renderSalesInvoice() {
        const invoices = db.getAll('SALES_INVOICES');
        const container = document.getElementById('sales-invoice-table-body');
        if (!container) return;

        let html = '';
        invoices.forEach(inv => {
            const billSub = inv.total || 0;
            const gst = (inv.cgst || 0) + (inv.sgst || 0) + (inv.igst || 0);
            const underBilling = inv.underBillingTotal || 0;
            const grandTotal = inv.grandTotal || (billSub + gst + underBilling);

            html += `
                <tr>
                    <td><strong>${inv.invNo}</strong></td>
                    <td>${inv.date}</td>
                    <td><strong>${inv.customerName}</strong>${inv.brokerName ? `<div style="font-size:0.78rem; color:var(--text-muted);"><i class="fa-solid fa-handshake"></i> ${inv.brokerName}</div>` : ''}</td>
                    <td>₹${billSub.toLocaleString('en-IN')}</td>
                    <td>₹${gst.toLocaleString('en-IN')}</td>
                    <td><span style="color:#d97706; font-weight:600;">₹${underBilling.toLocaleString('en-IN')}</span></td>
                    <td><strong>₹${grandTotal.toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge ${inv.status === 'Paid' ? 'badge-success' : 'badge-warning'}">${inv.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action view" onclick="Transactions.printInvoice('${inv.id}', 'sales')" title="Print GST Invoice"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;

        // Populate Customers
        const custSelect = document.getElementById('si-customer');
        if (custSelect) {
            const customers = db.getAll('CUSTOMERS');
            let cHtml = `<option value="">Select Customer...</option>`;
            customers.forEach(c => cHtml += `<option value="${c.id}">${c.name}</option>`);
            custSelect.innerHTML = cHtml;
        }

        // Populate Brokers
        const siBrokerSelect = document.getElementById('si-broker');
        if (siBrokerSelect) {
            const brokers = db.getAll('BROKERS');
            let bHtml = `<option value="">Select Broker (Optional)...</option>`;
            brokers.forEach(b => bHtml += `<option value="${b.id}">${b.name} (${b.city || 'Sojat'} - ${b.commissionRate}%)</option>`);
            siBrokerSelect.innerHTML = bHtml;
        }

        this.initSalesInvoiceItemRow();
    },

    updateSalesRowNumbers() {
        document.querySelectorAll('#sales-items-body tr').forEach((tr, index) => {
            const sno = tr.querySelector('.si-sno');
            if (sno) sno.innerText = index + 1;
        });
    },

    initSalesInvoiceItemRow() {
        const tbody = document.getElementById('sales-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (Stock: ${i.stock} ${i.unit})</option>`);

        tbody.innerHTML = `
            <tr>
                <td style="text-align:center;"><span class="si-sno" style="font-weight:600;">1</span></td>
                <td><select class="form-control si-item-select" onchange="Transactions.onSalesItemChange(this)">${optionsHtml}</select></td>
                <td><input type="text" class="form-control si-batch" value="B-2026-08"></td>
                <td><input type="text" class="form-control si-hsn" value="1404"></td>
                <td><input type="number" class="form-control si-gst" value="18" oninput="Transactions.calcSalesRow(this)"></td>
                <td><select class="form-control si-unit">${this.getUnitOptions('KG')}</select></td>
                <td><input type="number" class="form-control si-qty" value="50" min="0" step="any" oninput="Transactions.calcSalesRow(this)"></td>
                <td><input type="number" class="form-control si-actual-rate" value="180" step="any" oninput="Transactions.calcSalesRow(this, 'actual')"></td>
                <td><input type="number" class="form-control si-bill-rate" value="90" step="any" oninput="Transactions.calcSalesRow(this, 'bill')"></td>
                <td><input type="number" class="form-control si-ub-rate" value="90" step="any" oninput="Transactions.calcSalesRow(this, 'ub')"></td>
                <td><input type="number" class="form-control si-bill-amt" value="5310.00" readonly></td>
                <td><input type="number" class="form-control si-under-amt" value="4500.00" readonly></td>
                <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateSalesRowNumbers(); Transactions.calcSalesTotals();"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `;
        this.updateSalesRowNumbers();
        this.calcSalesTotals();
    },

    addSalesRow() {
        const tbody = document.getElementById('sales-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (${i.stock} ${i.unit})</option>`);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align:center;"><span class="si-sno" style="font-weight:600;">1</span></td>
            <td><select class="form-control si-item-select" onchange="Transactions.onSalesItemChange(this)">${optionsHtml}</select></td>
            <td><input type="text" class="form-control si-batch" value="B-2026-08"></td>
            <td><input type="text" class="form-control si-hsn" value="1404"></td>
            <td><input type="number" class="form-control si-gst" value="18" oninput="Transactions.calcSalesRow(this)"></td>
            <td><select class="form-control si-unit">${this.getUnitOptions('KG')}</select></td>
            <td><input type="number" class="form-control si-qty" value="10" min="0" step="any" oninput="Transactions.calcSalesRow(this)"></td>
            <td><input type="number" class="form-control si-actual-rate" value="140" step="any" oninput="Transactions.calcSalesRow(this, 'actual')"></td>
            <td><input type="number" class="form-control si-bill-rate" value="70" step="any" oninput="Transactions.calcSalesRow(this, 'bill')"></td>
            <td><input type="number" class="form-control si-ub-rate" value="70" step="any" oninput="Transactions.calcSalesRow(this, 'ub')"></td>
            <td><input type="number" class="form-control si-bill-amt" value="826.00" readonly></td>
            <td><input type="number" class="form-control si-under-amt" value="700.00" readonly></td>
            <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateSalesRowNumbers(); Transactions.calcSalesTotals();"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        this.updateSalesRowNumbers();
        this.calcSalesTotals();
    },

    onSalesItemChange(selectEl) {
        const tr = selectEl.closest('tr');
        const items = db.getAll('ITEMS');
        const item = items.find(i => i.id === selectEl.value);

        if (item) {
            tr.querySelector('.si-hsn').value = item.hsn || '1404';
            tr.querySelector('.si-gst').value = item.gst || 18;
            tr.querySelector('.si-unit').value = item.unit || 'KG';
            const actualRate = item.saleRate || 180;
            const billRate = item.billRate || Math.round(actualRate * 0.5);
            const ubRate = Math.max(0, actualRate - billRate);

            tr.querySelector('.si-actual-rate').value = actualRate;
            tr.querySelector('.si-bill-rate').value = billRate;
            tr.querySelector('.si-ub-rate').value = ubRate;
            this.calcSalesRow(selectEl);
        }
    },

    calcSalesRow(inputEl, source) {
        const tr = inputEl.closest('tr');
        const netWeight = parseFloat(tr.querySelector('.si-qty').value) || 0;
        let actualRate = parseFloat(tr.querySelector('.si-actual-rate').value) || 0;
        let billRate = parseFloat(tr.querySelector('.si-bill-rate').value) || 0;
        let ubRate = parseFloat(tr.querySelector('.si-ub-rate').value) || 0;
        const gst = parseFloat(tr.querySelector('.si-gst').value) || 0;

        if (source === 'actual' || source === 'bill') {
            ubRate = Math.max(0, actualRate - billRate);
            tr.querySelector('.si-ub-rate').value = ubRate.toFixed(2);
        } else if (source === 'ub') {
            actualRate = billRate + ubRate;
            tr.querySelector('.si-actual-rate').value = actualRate.toFixed(2);
        }

        const billSub = netWeight * billRate;
        const billTax = billSub * (gst / 100);
        const billAmt = billSub + billTax;
        const underAmt = netWeight * ubRate;

        tr.querySelector('.si-bill-amt').value = billAmt.toFixed(2);
        tr.querySelector('.si-under-amt').value = underAmt.toFixed(2);

        this.calcSalesTotals();
    },

    calcSalesTotals() {
        let subtotal = 0;
        let totalGst = 0;
        let totalUnder = 0;

        document.querySelectorAll('#sales-items-body tr').forEach(tr => {
            const netWeight = parseFloat(tr.querySelector('.si-qty').value) || 0;
            const billRate = parseFloat(tr.querySelector('.si-bill-rate').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.si-ub-rate').value) || 0;
            const gst = parseFloat(tr.querySelector('.si-gst').value) || 0;

            const lineSub = netWeight * billRate;
            const lineGst = lineSub * (gst / 100);
            const lineUnder = netWeight * ubRate;

            subtotal += lineSub;
            totalGst += lineGst;
            totalUnder += lineUnder;
        });

        const cgst = totalGst / 2;
        const sgst = totalGst / 2;
        const billTotal = subtotal + totalGst;
        const grandTotal = Math.round(billTotal + totalUnder);

        const subEl = document.getElementById('si-summary-subtotal');
        const cgstEl = document.getElementById('si-summary-cgst');
        const sgstEl = document.getElementById('si-summary-sgst');
        const billTotEl = document.getElementById('si-summary-billtotal');
        const underEl = document.getElementById('si-summary-underbilling');
        const grandEl = document.getElementById('si-summary-grandtotal');

        if (subEl) subEl.innerText = `₹${subtotal.toFixed(2)}`;
        if (cgstEl) cgstEl.innerText = `₹${cgst.toFixed(2)}`;
        if (sgstEl) sgstEl.innerText = `₹${sgst.toFixed(2)}`;
        if (billTotEl) billTotEl.innerText = `₹${billTotal.toFixed(2)}`;
        if (underEl) underEl.innerText = `₹${totalUnder.toFixed(2)}`;
        if (grandEl) grandEl.innerText = `₹${grandTotal.toLocaleString('en-IN')}`;
    },

    saveSalesInvoice() {
        const custSelect = document.getElementById('si-customer');
        const custId = custSelect.value;
        const invNo = document.getElementById('si-inv-no').value || 'INV-' + Date.now().toString().substr(-4);
        const date = document.getElementById('si-date').value || new Date().toISOString().split('T')[0];

        if (!custId) return App.showToast('Please select a Customer', 'danger');

        const custObj = db.getAll('CUSTOMERS').find(c => c.id === custId);
        const lineItems = [];
        let subtotal = 0;
        let totalGst = 0;
        let totalUnder = 0;
        let insufficientStock = false;

        document.querySelectorAll('#sales-items-body tr').forEach(tr => {
            const select = tr.querySelector('.si-item-select');
            const itemId = select.value;
            const netWeight = parseFloat(tr.querySelector('.si-qty').value) || 0;
            const actualRate = parseFloat(tr.querySelector('.si-actual-rate').value) || 0;
            const billRate = parseFloat(tr.querySelector('.si-bill-rate').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.si-ub-rate').value) || 0;
            const gst = parseFloat(tr.querySelector('.si-gst').value) || 0;
            const hsn = tr.querySelector('.si-hsn').value || '1404';
            const unit = tr.querySelector('.si-unit').value || 'KG';
            const batch = tr.querySelector('.si-batch').value;

            if (itemId && netWeight > 0) {
                const itemObj = db.getAll('ITEMS').find(i => i.id === itemId);

                // Stock Validation for Total Net Weight
                if (itemObj && itemObj.stock < netWeight) {
                    insufficientStock = true;
                    App.showToast(`Insufficient stock for ${itemObj.name}. Required: ${netWeight}, Available: ${itemObj.stock}`, 'danger');
                    return;
                }

                const lineSub = netWeight * billRate;
                const lineGst = lineSub * (gst / 100);
                const lineUnder = netWeight * ubRate;

                subtotal += lineSub;
                totalGst += lineGst;
                totalUnder += lineUnder;

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

                // AUTOMATICALLY DEDUCT STOCK FROM LOCALSTORAGE
                db.adjustStock(itemId, -netWeight);
            }
        });

        if (insufficientStock) return;
        if (lineItems.length === 0) return App.showToast('Please add at least one product item', 'danger');

        const billTotal = subtotal + totalGst;
        const grandTotal = Math.round(billTotal + totalUnder);
        const cgst = totalGst / 2;
        const sgst = totalGst / 2;

        const siBrokerSelect = document.getElementById('si-broker');
        const brokerId = siBrokerSelect ? siBrokerSelect.value : '';
        const brokerObj = brokerId ? db.getAll('BROKERS').find(b => b.id === brokerId) : null;

        const siOrderTypeSelect = document.getElementById('si-order-type');
        const orderType = siOrderTypeSelect ? siOrderTypeSelect.value : 'Medium';

        const invoiceRecord = {
            invNo,
            customerId: custId,
            customerName: custObj ? custObj.name : 'Customer',
            brokerId: brokerId || '',
            brokerName: brokerObj ? brokerObj.name : '',
            orderType,
            date,
            total: subtotal,
            cgst,
            sgst,
            igst: 0,
            billTotal,
            underBillingTotal: totalUnder,
            grandTotal,
            status: 'Pending',
            items: lineItems
        };

        db.saveItem('SALES_INVOICES', invoiceRecord);
        // Increase customer balance
        if (custObj) db.updateCustomerBalance(custObj.name, grandTotal);

        App.showToast('Sales / Purchase Order created successfully!', 'success');
        App.closeModal('modal-sales-invoice');
        this.renderSalesInvoice();
    },

    // 6. Receipt Entry
    initReceiptModal() {
        const typeEl = document.getElementById('rcp-type');
        if (typeEl) typeEl.value = 'Customer';
        this.onReceiptTypeChange(typeEl);
    },

    onReceiptTypeChange(selectEl) {
        const type = selectEl ? selectEl.value : 'Customer';
        const partyLabel = document.getElementById('rcp-party-label');
        const custSelect = document.getElementById('rcp-customer');
        if (!custSelect) return;

        if (type === 'Income') {
            if (partyLabel) partyLabel.innerText = 'Income Source / Received From *';
            custSelect.innerHTML = `
                <option value="">Select Income Source...</option>
                <option value="Scrap & Waste Sale">Scrap & Waste Sale</option>
                <option value="Bank Interest">Bank Interest</option>
                <option value="Rental Income">Rental Income</option>
                <option value="Commission Received">Commission Received</option>
                <option value="Other Miscellaneous Income">Other Miscellaneous Income</option>
            `;
        } else {
            if (partyLabel) partyLabel.innerText = 'Customer *';
            const customers = db.getAll('CUSTOMERS');
            let opts = `<option value="">Select Customer...</option>`;
            customers.forEach(c => opts += `<option value="${c.name}">${c.name} (${c.city || ''})</option>`);
            custSelect.innerHTML = opts;
        }
    },

    renderReceipt() {
        const receipts = db.getAll('RECEIPTS');
        const container = document.getElementById('receipt-table-body');
        if (!container) return;

        let html = '';
        receipts.forEach(r => {
            const isIncome = r.receiptType === 'Income';
            const typeBadge = isIncome 
                ? `<span class="badge badge-warning" style="margin-left:6px; font-size:0.75rem;">Income</span>` 
                : `<span class="badge badge-success" style="margin-left:6px; font-size:0.75rem;">Customer</span>`;
            html += `
                <tr>
                    <td><strong>${r.rcpNo}</strong></td>
                    <td>${r.date}</td>
                    <td><strong>${r.customerName}</strong> ${typeBadge}</td>
                    <td><strong>₹${(r.amount || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge badge-info">${r.mode}</span></td>
                    <td>${r.againstInv || 'N/A'}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action view" onclick="Reports.printVoucher('${r.id}', 'receipt')"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveReceipt(e) {
        if (e) e.preventDefault();
        const typeEl = document.getElementById('rcp-type');
        const receiptType = typeEl ? typeEl.value : 'Customer';
        const partyName = document.getElementById('rcp-customer').value;

        const rcp = {
            rcpNo: document.getElementById('rcp-no').value || 'RCP-' + Math.floor(Math.random()*1000 + 100),
            date: document.getElementById('rcp-date').value || new Date().toISOString().split('T')[0],
            receiptType: receiptType,
            customerName: partyName,
            amount: parseFloat(document.getElementById('rcp-amount').value) || 0,
            mode: document.getElementById('rcp-mode').value,
            againstInv: document.getElementById('rcp-inv').value || 'N/A',
            remarks: document.getElementById('rcp-remarks').value
        };

        if (!rcp.customerName || rcp.amount <= 0) {
            return App.showToast(`Please select ${receiptType === 'Income' ? 'Income Source' : 'Customer'} and valid Amount`, 'danger');
        }

        db.saveItem('RECEIPTS', rcp);

        // Deduct customer balance only if it is a Customer Payment
        if (receiptType === 'Customer') {
            db.updateCustomerBalance(rcp.customerName, -rcp.amount);
        }

        App.showToast(`Receipt voucher (${receiptType === 'Income' ? 'Direct Income' : 'Customer Payment'}) recorded successfully!`, 'success');
        App.closeModal('modal-receipt');
        this.renderReceipt();
    },

    // 7. Payment Entry
    initPaymentModal() {
        const typeEl = document.getElementById('pay-type');
        if (typeEl) typeEl.value = 'Vendor';
        this.onPaymentTypeChange(typeEl);
    },

    onPaymentTypeChange(selectEl) {
        const type = selectEl ? selectEl.value : 'Vendor';
        const partyLabel = document.getElementById('pay-party-label');
        const vendorSelect = document.getElementById('pay-vendor');
        if (!vendorSelect) return;

        if (type === 'Expense') {
            if (partyLabel) partyLabel.innerText = 'Expense Head / Paid To *';
            vendorSelect.innerHTML = `
                <option value="">Select Expense Head...</option>
                <option value="Office & Factory Rent">Office & Factory Rent</option>
                <option value="Electricity & Power Bill">Electricity & Power Bill</option>
                <option value="Freight & Transportation">Freight & Transportation</option>
                <option value="Staff Salary & Wages">Staff Salary & Wages</option>
                <option value="Tea, Water & Refreshments">Tea, Water & Refreshments</option>
                <option value="Maintenance & Repairs">Maintenance & Repairs</option>
                <option value="Printing & Stationery">Printing & Stationery</option>
                <option value="Other Office Expenses">Other Office Expenses</option>
            `;
        } else {
            if (partyLabel) partyLabel.innerText = 'Vendor / Firm *';
            const vendors = db.getAll('VENDORS');
            let opts = `<option value="">Select Vendor / Firm...</option>`;
            vendors.forEach(v => opts += `<option value="${v.name}">${v.name} (${v.city || ''})</option>`);
            vendorSelect.innerHTML = opts;
        }
    },

    renderPayment() {
        const payments = db.getAll('PAYMENTS');
        const container = document.getElementById('payment-table-body');
        if (!container) return;

        let html = '';
        payments.forEach(p => {
            const isExpense = p.paymentType === 'Expense';
            const typeBadge = isExpense 
                ? `<span class="badge badge-warning" style="margin-left:6px; font-size:0.75rem;">Expense</span>` 
                : `<span class="badge badge-primary" style="margin-left:6px; font-size:0.75rem;">Vendor</span>`;
            html += `
                <tr>
                    <td><strong>${p.payNo}</strong></td>
                    <td>${p.date}</td>
                    <td><strong>${p.vendorName}</strong> ${typeBadge}</td>
                    <td><strong>₹${(p.amount || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge badge-info">${p.mode}</span></td>
                    <td>${p.againstPur || 'N/A'}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action view" onclick="Reports.printVoucher('${p.id}', 'payment')"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    savePayment(e) {
        if (e) e.preventDefault();
        const typeEl = document.getElementById('pay-type');
        const paymentType = typeEl ? typeEl.value : 'Vendor';
        const partyName = document.getElementById('pay-vendor').value;

        const pay = {
            payNo: document.getElementById('pay-no').value || 'PAY-' + Math.floor(Math.random()*1000 + 100),
            date: document.getElementById('pay-date').value || new Date().toISOString().split('T')[0],
            paymentType: paymentType,
            vendorName: partyName,
            amount: parseFloat(document.getElementById('pay-amount').value) || 0,
            mode: document.getElementById('pay-mode').value,
            againstPur: document.getElementById('pay-pur').value || 'N/A',
            remarks: document.getElementById('pay-remarks').value
        };

        if (!pay.vendorName || pay.amount <= 0) {
            return App.showToast(`Please select ${paymentType === 'Expense' ? 'Expense Head' : 'Vendor Firm'} and valid Amount`, 'danger');
        }

        db.saveItem('PAYMENTS', pay);

        // Deduct vendor balance only if it is a Vendor Payment
        if (paymentType === 'Vendor') {
            db.updateVendorBalance(pay.vendorName, -pay.amount);
        }

        App.showToast(`Payment voucher (${paymentType === 'Expense' ? 'Direct Expense' : 'Vendor Payment'}) recorded successfully!`, 'success');
        App.closeModal('modal-payment');
        this.renderPayment();
    },

    printInvoice(id, type) {
        const inv = type === 'sales' 
            ? db.getAll('SALES_INVOICES').find(i => i.id === id)
            : db.getAll('PURCHASES').find(i => i.id === id);

        if (!inv) return;

        const printWin = window.open('', '_blank');
        printWin.document.write(`
            <html>
            <head>
                <title>${type === 'sales' ? 'GST Sales Invoice' : 'Purchase Invoice'} - ${inv.invNo}</title>
                <style>
                    body { font-family: sans-serif; padding: 20px; color: #183A1D; }
                    .header { display: flex; justify-content: space-between; border-bottom: 2px solid #6B8E23; padding-bottom: 15px; }
                    .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    .table th, .table td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                    .table th { background: #EBF7D4; }
                </style>
            </head>
            <body>
                <div class="header">
                    <div>
                        <h2>VIKAS UDHYOG</h2>
                        <p>Sojat City, Pali, Rajasthan - 306104<br>GSTIN: 08ABCDE1234F1Z5</p>
                    </div>
                    <div style="text-align:right;">
                        <h3>${type === 'sales' ? 'TAX INVOICE' : 'PURCHASE RECORD'}</h3>
                        <p>Invoice No: <strong>${inv.invNo}</strong><br>Date: ${inv.date}</p>
                    </div>
                </div>
                <h4>Party Details: ${inv.customerName || inv.vendorName}</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Item</th>
                            <th>Batch No</th>
                            <th>HSN</th>
                            <th>GST %</th>
                            <th>Unit Type</th>
                            <th>Net Weight</th>
                            <th>Actual Rate</th>
                            <th>Bill Rate</th>
                            <th>U_B Rate</th>
                            <th>Bill Amt</th>
                            <th>U_B Amt</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${(inv.items || []).map((it, idx) => {
                            const actRate = it.actualRate || ((it.billRate || 0) + (it.ubRate || 0));
                            const ubR = it.ubRate !== undefined ? it.ubRate : Math.max(0, actRate - (it.billRate || 0));
                            return `
                                <tr>
                                    <td style="text-align:center;">${idx+1}</td>
                                    <td>${it.itemName}</td>
                                    <td>${it.batch || 'B-101'}</td>
                                    <td>${it.hsn || '1404'}</td>
                                    <td>${it.gst || 18}%</td>
                                    <td>${it.unit || 'KG'}</td>
                                    <td>${it.qty || it.netWeight || 0}</td>
                                    <td>₹${actRate}</td>
                                    <td>₹${it.billRate || it.rate || 0}</td>
                                    <td>₹${ubR}</td>
                                    <td>₹${(it.billAmount || (it.qty * (it.billRate || 0))).toLocaleString('en-IN')}</td>
                                    <td>₹${(it.underAmount || (it.qty * ubR)).toLocaleString('en-IN')}</td>
                                </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
                <h3 style="text-align:right; margin-top:20px;">Grand Total: ₹${(inv.grandTotal || inv.total).toLocaleString('en-IN')}</h3>
                <script>window.onload = function() { window.print(); }</script>
            </body>
            </html>
        `);
        printWin.document.close();
    },

    // 3b. WB Sales Entry (Without Bill)
    renderWBSalesEntry() {
        const wbSales = db.getAll('WB_SALES');
        const container = document.getElementById('wb-sales-table-body');
        if (!container) return;

        let html = '';
        wbSales.forEach(s => {
            const grandTotal = s.totalAmount || 0;
            const netWeight = s.totalWeight || 0;

            html += `
                <tr>
                    <td><strong>${s.slipNo}</strong></td>
                    <td>${s.date}</td>
                    <td><strong>${s.customerName}</strong>${s.brokerName ? `<div style="font-size:0.78rem; color:var(--text-muted);"><i class="fa-solid fa-handshake"></i> ${s.brokerName}</div>` : ''}</td>
                    <td><strong>${netWeight} KG</strong></td>
                    <td><span style="color:#d97706; font-weight:700;">₹${grandTotal.toLocaleString('en-IN')}</span></td>
                    <td><span class="badge ${s.status === 'Dispatched' ? 'badge-success' : 'badge-warning'}">${s.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action view" onclick="Transactions.printInvoice('${s.id}', 'sales')"><i class="fa-solid fa-print"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;

        // Populate Customers in WB Modal
        const custSelect = document.getElementById('wbs-customer');
        if (custSelect) {
            const customers = db.getAll('CUSTOMERS');
            let cHtml = `<option value="">Select Customer...</option>`;
            customers.forEach(c => cHtml += `<option value="${c.id}">${c.name}</option>`);
            custSelect.innerHTML = cHtml;
        }

        // Populate Brokers in WB Modal
        const brokerSelect = document.getElementById('wbs-broker');
        if (brokerSelect) {
            const brokers = db.getAll('BROKERS');
            let bHtml = `<option value="">Select Broker (Optional)...</option>`;
            brokers.forEach(b => bHtml += `<option value="${b.id}">${b.name} (${b.city || 'Sojat'} - ${b.commissionRate}%)</option>`);
            brokerSelect.innerHTML = bHtml;
        }

        this.initWBSalesItemRow();
    },

    updateWBSalesRowNumbers() {
        document.querySelectorAll('#wbs-items-body tr').forEach((tr, index) => {
            const sno = tr.querySelector('.wbs-sno');
            if (sno) sno.innerText = index + 1;
        });
    },

    initWBSalesItemRow() {
        const tbody = document.getElementById('wbs-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (Stock: ${i.stock} ${i.unit})</option>`);

        tbody.innerHTML = `
            <tr>
                <td style="text-align:center;"><span class="wbs-sno" style="font-weight:600;">1</span></td>
                <td><select class="form-control wbs-item-select" onchange="Transactions.onWBSalesItemChange(this)">${optionsHtml}</select><input type="hidden" class="wbs-hsn" value="1404"></td>
                <td><input type="text" class="form-control wbs-batch" value="B-2026-08"></td>
                <td><input type="number" class="form-control wbs-ub-rate" value="90" step="any" oninput="Transactions.calcWBSalesRow(this)"></td>
                <td><select class="form-control wbs-unit">${this.getUnitOptions('KG')}</select></td>
                <td><input type="number" class="form-control wbs-qty" value="50" min="0" step="any" oninput="Transactions.calcWBSalesRow(this)"></td>
                <td><input type="number" class="form-control wbs-under-amt" value="4500.00" readonly></td>
                <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateWBSalesRowNumbers(); Transactions.calcWBSalesTotals();"><i class="fa-solid fa-trash"></i></button></td>
            </tr>
        `;
        this.updateWBSalesRowNumbers();
        this.calcWBSalesTotals();
    },

    addWBSalesRow() {
        const tbody = document.getElementById('wbs-items-body');
        if (!tbody) return;

        const items = db.getAll('ITEMS');
        let optionsHtml = `<option value="">Select Product...</option>`;
        items.forEach(i => optionsHtml += `<option value="${i.id}">${i.name} (${i.stock} ${i.unit})</option>`);

        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td style="text-align:center;"><span class="wbs-sno" style="font-weight:600;">1</span></td>
            <td><select class="form-control wbs-item-select" onchange="Transactions.onWBSalesItemChange(this)">${optionsHtml}</select><input type="hidden" class="wbs-hsn" value="1404"></td>
            <td><input type="text" class="form-control wbs-batch" value="B-2026-08"></td>
            <td><input type="number" class="form-control wbs-ub-rate" value="70" step="any" oninput="Transactions.calcWBSalesRow(this)"></td>
            <td><select class="form-control wbs-unit">${this.getUnitOptions('KG')}</select></td>
            <td><input type="number" class="form-control wbs-qty" value="10" min="0" step="any" oninput="Transactions.calcWBSalesRow(this)"></td>
            <td><input type="number" class="form-control wbs-under-amt" value="700.00" readonly></td>
            <td><button type="button" class="btn-action delete" onclick="this.closest('tr').remove(); Transactions.updateWBSalesRowNumbers(); Transactions.calcWBSalesTotals();"><i class="fa-solid fa-trash"></i></button></td>
        `;
        tbody.appendChild(tr);
        this.updateWBSalesRowNumbers();
        this.calcWBSalesTotals();
    },

    onWBSalesItemChange(selectEl) {
        const tr = selectEl.closest('tr');
        const items = db.getAll('ITEMS');
        const item = items.find(i => i.id === selectEl.value);

        if (item) {
            tr.querySelector('.wbs-hsn').value = item.hsn || '1404';
            tr.querySelector('.wbs-unit').value = item.unit || 'KG';
            const actualRate = item.saleRate || 180;
            const ubRate = item.billRate || Math.round(actualRate * 0.5);
            tr.querySelector('.wbs-ub-rate').value = ubRate;
            this.calcWBSalesRow(selectEl);
        }
    },

    calcWBSalesRow(inputEl) {
        const tr = inputEl.closest('tr');
        const netWeight = parseFloat(tr.querySelector('.wbs-qty').value) || 0;
        const ubRate = parseFloat(tr.querySelector('.wbs-ub-rate').value) || 0;
        const underAmt = netWeight * ubRate;

        tr.querySelector('.wbs-under-amt').value = underAmt.toFixed(2);
        this.calcWBSalesTotals();
    },

    calcWBSalesTotals() {
        let totalWeight = 0;
        let totalAmount = 0;

        document.querySelectorAll('#wbs-items-body tr').forEach(tr => {
            const netWeight = parseFloat(tr.querySelector('.wbs-qty').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.wbs-ub-rate').value) || 0;
            totalWeight += netWeight;
            totalAmount += (netWeight * ubRate);
        });

        const grandEl = document.getElementById('wbs-summary-total');
        if (grandEl) grandEl.innerText = `₹${Math.round(totalAmount).toLocaleString('en-IN')}`;
    },

    saveWBSales() {
        const custSelect = document.getElementById('wbs-customer');
        const custId = custSelect ? custSelect.value : '';
        const slipNo = document.getElementById('wbs-slip-no').value || 'WBS-' + Date.now().toString().substr(-5);
        const date = document.getElementById('wbs-date').value || new Date().toISOString().split('T')[0];

        if (!custId) return App.showToast('Please select a Customer', 'danger');

        const custObj = db.getAll('CUSTOMERS').find(c => c.id === custId);
        const lineItems = [];
        let totalWeight = 0;
        let totalAmount = 0;
        let insufficientStock = false;

        document.querySelectorAll('#wbs-items-body tr').forEach(tr => {
            const select = tr.querySelector('.wbs-item-select');
            const itemId = select.value;
            const netWeight = parseFloat(tr.querySelector('.wbs-qty').value) || 0;
            const ubRate = parseFloat(tr.querySelector('.wbs-ub-rate').value) || 0;
            const hsn = tr.querySelector('.wbs-hsn').value || '1404';
            const unit = tr.querySelector('.wbs-unit').value || 'KG';
            const batch = tr.querySelector('.wbs-batch').value;

            if (itemId && netWeight > 0) {
                const itemObj = db.getAll('ITEMS').find(i => i.id === itemId);

                // Stock Validation
                if (itemObj && itemObj.stock < netWeight) {
                    insufficientStock = true;
                    App.showToast(`Insufficient stock for ${itemObj.name}. Required: ${netWeight}, Available: ${itemObj.stock}`, 'danger');
                    return;
                }

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

                // Deduct Stock in database (WB Entry)
                db.adjustStock(itemId, -netWeight);
            }
        });

        if (insufficientStock) return;
        if (lineItems.length === 0) return App.showToast('Please add at least one product item', 'danger');

        const wbsBrokerSelect = document.getElementById('wbs-broker');
        const brokerId = wbsBrokerSelect ? wbsBrokerSelect.value : '';
        const brokerObj = brokerId ? db.getAll('BROKERS').find(b => b.id === brokerId) : null;

        const wbsOrderTypeSelect = document.getElementById('wbs-order-type');
        const orderType = wbsOrderTypeSelect ? wbsOrderTypeSelect.value : 'Medium';

        const wbRecord = {
            slipNo,
            customerId: custId,
            customerName: custObj ? custObj.name : 'Customer',
            brokerId: brokerId || '',
            brokerName: brokerObj ? brokerObj.name : '',
            orderType,
            date,
            totalWeight,
            totalAmount: Math.round(totalAmount),
            status: 'Dispatched',
            items: lineItems
        };

        db.saveItem('WB_SALES', wbRecord);

        App.showToast(`WB Sales Entry ${slipNo} saved & stock deducted!`, 'success');
        App.closeModal('modal-wb-sales');
        this.renderWBSalesEntry();
    }
});
