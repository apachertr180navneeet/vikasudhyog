/* ==========================================================================
   VIKAS UDHYOG ERP - Inventory & Stock Management Controller
   ========================================================================== */

const Inventory = {
    render(viewId) {
        if (viewId === 'inv-overview') this.renderOverview();
        if (viewId === 'inv-ledger') this.renderLedger();
        if (viewId === 'inv-adjustment') this.renderAdjustment();
        if (viewId === 'inv-low-stock') this.renderLowStock();
    },

    // 1. Stock Overview
    renderOverview() {
        const items = db.getAll('ITEMS');
        const purchases = db.getAll('PURCHASES');
        const sales = db.getAll('SALES_INVOICES');

        const totalItems = items.length;
        const totalStock = items.reduce((sum, i) => sum + (i.stock || 0), 0);
        const stockValue = items.reduce((sum, i) => sum + ((i.stock || 0) * (i.purchaseRate || 0)), 0);
        const lowStockCount = items.filter(i => i.stock <= i.minStock).length;

        const elItems = document.getElementById('inv-card-items');
        if (elItems) elItems.innerText = totalItems;
        const elStock = document.getElementById('inv-card-stock');
        if (elStock) elStock.innerText = `${totalStock.toLocaleString('en-IN')} Units`;
        const elValue = document.getElementById('inv-card-value');
        if (elValue) elValue.innerText = `₹${stockValue.toLocaleString('en-IN')}`;
        const elLow = document.getElementById('inv-card-low');
        if (elLow) elLow.innerText = lowStockCount;

        const container = document.getElementById('inv-overview-table-body');
        if (!container) return;

        let html = '';
        items.forEach(i => {
            const val = (i.stock || 0) * (i.purchaseRate || 0);
            const statusBadge = i.stock <= i.minStock 
                ? `<span class="badge badge-danger">Low Stock</span>`
                : `<span class="badge badge-success">In Stock</span>`;

            html += `
                <tr>
                    <td><strong>${i.name}</strong></td>
                    <td><span class="badge badge-dark">${i.category}</span></td>
                    <td>${i.batch || 'B-2026-08'}</td>
                    <td>${i.unit}</td>
                    <td><strong>${i.stock} ${i.unit}</strong></td>
                    <td>${i.minStock} ${i.unit}</td>
                    <td>₹${i.purchaseRate}</td>
                    <td><strong>₹${val.toLocaleString('en-IN')}</strong></td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    // 2. Item Ledger Timeline
    renderLedger() {
        const items = db.getAll('ITEMS');
        const select = document.getElementById('ledger-item-select');
        if (select) {
            let options = `<option value="">Select Item for Ledger...</option>`;
            items.forEach(i => options += `<option value="${i.id}">${i.name} (${i.code})</option>`);
            select.innerHTML = options;
        }

        this.loadLedgerData();
    },

    loadLedgerData() {
        const select = document.getElementById('ledger-item-select');
        const itemId = select ? select.value : null;
        const container = document.getElementById('ledger-table-body');
        if (!container) return;

        const dummyLedger = [
            { date: '2026-09-01', type: 'Opening Balance', ref: 'SYS-INIT', inQty: 500, outQty: 0, balance: 500 },
            { date: '2026-09-03', type: 'Purchase Invoice', ref: 'INV-NH-402', inQty: 200, outQty: 0, balance: 700 },
            { date: '2026-09-05', type: 'Sales Invoice', ref: 'INV-1024', inQty: 0, outQty: 75, balance: 625 },
            { date: '2026-09-08', type: 'Sales Invoice', ref: 'INV-1025', inQty: 0, outQty: 50, balance: 575 }
        ];

        let html = '';
        dummyLedger.forEach(l => {
            html += `
                <tr>
                    <td>${l.date}</td>
                    <td><span class="badge ${l.inQty > 0 ? 'badge-success' : 'badge-info'}">${l.type}</span></td>
                    <td><strong>${l.ref}</strong></td>
                    <td>${l.inQty > 0 ? `+${l.inQty}` : '-'}</td>
                    <td>${l.outQty > 0 ? `-${l.outQty}` : '-'}</td>
                    <td><strong>${l.balance} KG</strong></td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    // 3. Stock Adjustment
    renderAdjustment() {
        const items = db.getAll('ITEMS');
        ['adj-item-select', 'adj-item-select-page'].forEach(selectId => {
            const select = document.getElementById(selectId);
            if (select) {
                let options = `<option value="">Select Item to Adjust...</option>`;
                items.forEach(i => options += `<option value="${i.id}">${i.name} (Current: ${i.stock} ${i.unit})</option>`);
                select.innerHTML = options;
            }
        });
    },

    saveAdjustment(e) {
        if (e) e.preventDefault();
        const itemId = (document.getElementById('adj-item-select-page') && document.getElementById('adj-item-select-page').value) ||
                       (document.getElementById('adj-item-select') && document.getElementById('adj-item-select').value);
        const type = (document.getElementById('adj-type-page') && document.getElementById('adj-type-page').value) ||
                     (document.getElementById('adj-type') && document.getElementById('adj-type').value) || 'Add';
        const qtyVal = (document.getElementById('adj-qty-page') && document.getElementById('adj-qty-page').value) ||
                       (document.getElementById('adj-qty') && document.getElementById('adj-qty').value);
        const qty = parseFloat(qtyVal) || 0;

        if (!itemId || qty <= 0) return App.showToast('Please select Item and enter valid Quantity', 'danger');

        const change = type === 'Add' ? qty : -qty;
        db.adjustStock(itemId, change);

        App.showToast('Stock adjusted successfully!', 'success');
        App.closeModal('modal-stock-adj');
        this.renderAdjustment();
        this.renderOverview();
    },

    // 4. Low Stock Alert View
    renderLowStock() {
        const items = db.getAll('ITEMS');
        const lowStockItems = items.filter(i => i.stock <= i.minStock);
        const container = document.getElementById('low-stock-table-body');
        if (!container) return;

        let html = '';
        lowStockItems.forEach(i => {
            html += `
                <tr>
                    <td><strong>${i.code}</strong></td>
                    <td><strong>${i.name}</strong></td>
                    <td><span class="badge badge-dark">${i.category}</span></td>
                    <td><span class="badge badge-danger">${i.stock} ${i.unit}</span></td>
                    <td>${i.minStock} ${i.unit}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="App.navigateTo('txn-purchase')">+ Create Purchase</button>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    }
};
