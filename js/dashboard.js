/* ==========================================================================
   VIKAS UDHYOG ERP - Executive Dashboard Controller
   ========================================================================== */

const Dashboard = {
    salesChart: null,
    trendChart: null,
    topProductsChart: null,
    stockChart: null,

    render() {
        this.renderKPIs();
        this.renderCharts();
        this.renderTopProducts();
        this.renderRecentTransactions();
        this.renderLowStockAlerts();
    },

    renderKPIs() {
        const sales = db.getAll('SALES_INVOICES');
        const purchases = db.getAll('PURCHASES');
        const customers = db.getAll('CUSTOMERS');
        const vendors = db.getAll('VENDORS');
        const items = db.getAll('ITEMS');
        const pendingOrders = db.getAll('SALES_ORDERS').filter(o => o.status === 'Pending').length;

        // Today's Sales
        const todaySales = sales.reduce((sum, s) => sum + (s.grandTotal || 0), 0);
        const todayPurchase = purchases.reduce((sum, p) => sum + (p.total || 0), 0);
        const totalReceivable = customers.reduce((sum, c) => sum + (c.balance || 0), 0);
        const totalPayable = vendors.reduce((sum, v) => sum + (v.balance || 0), 0);
        const stockValue = items.reduce((sum, i) => sum + ((i.stock || 0) * (i.purchaseRate || 0)), 0);

        document.getElementById('kpi-sales-val').innerText = `₹${todaySales.toLocaleString('en-IN')}`;
        document.getElementById('kpi-purchase-val').innerText = `₹${todayPurchase.toLocaleString('en-IN')}`;
        document.getElementById('kpi-receivable-val').innerText = `₹${totalReceivable.toLocaleString('en-IN')}`;
        document.getElementById('kpi-payable-val').innerText = `₹${totalPayable.toLocaleString('en-IN')}`;
        document.getElementById('kpi-stock-val').innerText = `₹${stockValue.toLocaleString('en-IN')}`;
        document.getElementById('kpi-orders-val').innerText = `${pendingOrders}`;
    },

    renderCharts() {
        // 1. Sales vs Purchase Bar Chart
        const ctx1 = document.getElementById('chart-sales-vs-purchase');
        if (ctx1) {
            if (this.salesChart) this.salesChart.destroy();
            this.salesChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                    datasets: [
                        { label: 'Sales (₹)', data: [450000, 520000, 610000, 580000, 720000, 840000], backgroundColor: '#6B8E23', borderRadius: 6 },
                        { label: 'Purchase (₹)', data: [320000, 390000, 410000, 460000, 500000, 590000], backgroundColor: '#3B82F6', borderRadius: 6 }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // 2. Sales Trend Line Chart
        const ctx2 = document.getElementById('chart-sales-trend');
        if (ctx2) {
            if (this.trendChart) this.trendChart.destroy();
            this.trendChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Sales Trend (₹)',
                        data: [140000, 210000, 185000, 305000],
                        borderColor: '#8FBF26',
                        backgroundColor: 'rgba(143, 191, 38, 0.15)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        // 3. Top Selling Products Horizontal Bar Chart
        const ctx3 = document.getElementById('chart-top-products');
        if (ctx3) {
            if (this.topProductsChart) this.topProductsChart.destroy();
            this.topProductsChart = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: ['Mehndi Powder', 'Henna Powder', 'Amla Powder', 'Neem Powder', 'Shikakai Powder'],
                    datasets: [{
                        label: 'Total Sales (KG)',
                        data: [1250, 980, 640, 420, 310],
                        backgroundColor: ['#6B8E23', '#8FBF26', '#D4A017', '#10B981', '#3B82F6'],
                        borderRadius: 4
                    }]
                },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false }
            });
        }

        // 4. Stock Distribution Donut Chart
        const ctx4 = document.getElementById('chart-stock-distribution');
        if (ctx4) {
            if (this.stockChart) this.stockChart.destroy();
            this.stockChart = new Chart(ctx4, {
                type: 'doughnut',
                data: {
                    labels: ['Mehndi Powder', 'Herbal Powder', 'Raw Materials', 'Finished Goods'],
                    datasets: [{
                        data: [45, 30, 15, 10],
                        backgroundColor: ['#6B8E23', '#8FBF26', '#D4A017', '#183A1D']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }
    },

    renderTopProducts() {
        const items = db.getAll('ITEMS');
        const container = document.getElementById('top-products-list');
        if (!container) return;

        let html = '';
        items.slice(0, 5).forEach(item => {
            const statusBadge = item.stock > item.minStock 
                ? `<span class="badge badge-success">In Stock</span>`
                : `<span class="badge badge-danger">Low Stock</span>`;
            
            html += `
                <tr>
                    <td><strong>${item.name}</strong></td>
                    <td>${item.category}</td>
                    <td><strong>${item.stock} ${item.unit}</strong></td>
                    <td>₹${item.saleRate} / ${item.unit}</td>
                    <td>${statusBadge}</td>
                </tr>
            `;
        });

        container.innerHTML = html;
    },

    renderRecentTransactions() {
        const sales = db.getAll('SALES_INVOICES');
        const purchases = db.getAll('PURCHASES');
        const container = document.getElementById('recent-transactions-list');
        if (!container) return;

        const txns = [];
        sales.forEach(s => txns.push({ no: s.invNo, date: s.date, party: s.customerName, type: 'Sales', amount: s.grandTotal, status: s.status }));
        purchases.forEach(p => txns.push({ no: p.invNo, date: p.date, party: p.vendorName, type: 'Purchase', amount: p.total, status: p.status }));

        txns.sort((a, b) => new Date(b.date) - new Date(a.date));

        let html = '';
        txns.slice(0, 6).forEach(t => {
            const typeBadge = t.type === 'Sales' 
                ? `<span class="badge badge-success">Sales</span>`
                : `<span class="badge badge-info">Purchase</span>`;
            
            const statusClass = t.status === 'Paid' ? 'badge-success' : 'badge-warning';

            html += `
                <tr>
                    <td><strong>${t.no}</strong></td>
                    <td>${t.date}</td>
                    <td>${t.party}</td>
                    <td>${typeBadge}</td>
                    <td><strong>₹${t.amount.toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge ${statusClass}">${t.status}</span></td>
                </tr>
            `;
        });

        container.innerHTML = html;
    },

    renderLowStockAlerts() {
        const items = db.getAll('ITEMS');
        const lowStockItems = items.filter(i => i.stock <= i.minStock);
        const container = document.getElementById('low-stock-alert-list');
        if (!container) return;

        if (lowStockItems.length === 0) {
            container.innerHTML = `<div style="padding: 1rem; text-align: center; color: var(--text-muted);">All items have healthy stock levels.</div>`;
            return;
        }

        let html = '';
        lowStockItems.forEach(i => {
            html += `
                <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong style="color: var(--dark);">${i.name}</strong>
                        <div style="font-size: 0.78rem; color: var(--text-muted);">Min Level: ${i.minStock} ${i.unit}</div>
                    </div>
                    <span class="badge badge-danger">Current: ${i.stock} ${i.unit}</span>
                </div>
            `;
        });

        container.innerHTML = html;
    }
};
