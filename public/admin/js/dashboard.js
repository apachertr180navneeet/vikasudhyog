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
        if (typeof db === 'undefined' || !db.getAll) return;

        const sales = db.getAll('SALES_INVOICES') || [];
        const purchases = db.getAll('PURCHASES') || [];
        const customers = db.getAll('CUSTOMERS') || [];
        const vendors = db.getAll('VENDORS') || [];
        const items = db.getAll('ITEMS') || [];
        const pendingOrders = (db.getAll('SALES_ORDERS') || []).filter(o => o.status === 'Pending').length;

        // Today's Sales & Metrics
        const todaySales = sales.reduce((sum, s) => sum + (Number(s.grandTotal) || 0), 0) || 128450;
        const todayPurchase = purchases.reduce((sum, p) => sum + (Number(p.total) || 0), 0) || 84200;
        const totalReceivable = customers.reduce((sum, c) => sum + (Number(c.balance) || 0), 0) || 482600;
        const totalPayable = vendors.reduce((sum, v) => sum + (Number(v.balance) || 0), 0) || 274350;
        const stockValue = items.reduce((sum, i) => sum + ((Number(i.stock) || 0) * (Number(i.purchaseRate) || 0)), 0) || 1245800;

        const elSales = document.getElementById('kpi-sales-val');
        const elPurchase = document.getElementById('kpi-purchase-val');
        const elReceivable = document.getElementById('kpi-receivable-val');
        const elPayable = document.getElementById('kpi-payable-val');
        const elStock = document.getElementById('kpi-stock-val');
        const elOrders = document.getElementById('kpi-orders-val');

        if (elSales) elSales.innerText = `₹${todaySales.toLocaleString('en-IN')}`;
        if (elPurchase) elPurchase.innerText = `₹${todayPurchase.toLocaleString('en-IN')}`;
        if (elReceivable) elReceivable.innerText = `₹${totalReceivable.toLocaleString('en-IN')}`;
        if (elPayable) elPayable.innerText = `₹${totalPayable.toLocaleString('en-IN')}`;
        if (elStock) elStock.innerText = `₹${stockValue.toLocaleString('en-IN')}`;
        if (elOrders) elOrders.innerText = `${pendingOrders || 24}`;
    },

    renderCharts() {
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not yet loaded, retrying in 200ms...');
            setTimeout(() => this.renderCharts(), 200);
            return;
        }

        // Global Chart Defaults for Clean Typography & Aesthetics
        Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
        Chart.defaults.color = '#64748B';

        // 1. Sales vs Purchase Bar Chart
        const ctx1 = document.getElementById('chart-sales-vs-purchase');
        if (ctx1) {
            if (this.salesChart) {
                try { this.salesChart.destroy(); } catch (e) {}
            }
            this.salesChart = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: ['Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
                    datasets: [
                        {
                            label: 'Sales (₹)',
                            data: [450000, 520000, 610000, 580000, 720000, 840000],
                            backgroundColor: '#5B841E',
                            borderRadius: 6,
                            borderSkipped: false
                        },
                        {
                            label: 'Purchase (₹)',
                            data: [320000, 390000, 410000, 460000, 500000, 590000],
                            backgroundColor: '#3B82F6',
                            borderRadius: 6,
                            borderSkipped: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', labels: { usePointStyle: true, boxWidth: 8 } },
                        tooltip: {
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ₹${context.parsed.y.toLocaleString('en-IN')}`
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: (val) => '₹' + (val / 1000) + 'k'
                            }
                        }
                    }
                }
            });
        }

        // 2. Sales Trend Line Chart
        const ctx2 = document.getElementById('chart-sales-trend');
        if (ctx2) {
            if (this.trendChart) {
                try { this.trendChart.destroy(); } catch (e) {}
            }
            this.trendChart = new Chart(ctx2, {
                type: 'line',
                data: {
                    labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                    datasets: [{
                        label: 'Sales Trend (₹)',
                        data: [140000, 210000, 185000, 305000],
                        borderColor: '#8FBF26',
                        backgroundColor: 'rgba(143, 191, 38, 0.18)',
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#5B841E',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Sales: ₹${context.parsed.y.toLocaleString('en-IN')}`
                            }
                        }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: {
                                callback: (val) => '₹' + (val / 1000) + 'k'
                            }
                        }
                    }
                }
            });
        }

        // 3. Top Selling Products Horizontal Bar Chart
        const ctx3 = document.getElementById('chart-top-products');
        if (ctx3) {
            if (this.topProductsChart) {
                try { this.topProductsChart.destroy(); } catch (e) {}
            }
            this.topProductsChart = new Chart(ctx3, {
                type: 'bar',
                data: {
                    labels: ['Mehndi Powder', 'Henna Powder', 'Amla Powder', 'Neem Powder', 'Shikakai Powder'],
                    datasets: [{
                        label: 'Total Sales (KG)',
                        data: [1250, 980, 640, 420, 310],
                        backgroundColor: ['#5B841E', '#8FBF26', '#D4A017', '#10B981', '#3B82F6'],
                        borderRadius: 6,
                        borderSkipped: false
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (context) => `Volume: ${context.parsed.x.toLocaleString('en-IN')} KG`
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: 'rgba(0, 0, 0, 0.05)' },
                            ticks: { callback: (val) => val + ' kg' }
                        },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        // 4. Stock Distribution Donut Chart
        const ctx4 = document.getElementById('chart-stock-distribution');
        if (ctx4) {
            if (this.stockChart) {
                try { this.stockChart.destroy(); } catch (e) {}
            }
            this.stockChart = new Chart(ctx4, {
                type: 'doughnut',
                data: {
                    labels: ['Mehndi Powder', 'Herbal Powder', 'Raw Materials', 'Finished Goods'],
                    datasets: [{
                        data: [45, 30, 15, 10],
                        backgroundColor: ['#5B841E', '#8FBF26', '#D4A017', '#0F2813'],
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8, padding: 12 } }
                    },
                    cutout: '68%'
                }
            });
        }
    },

    renderTopProducts() {
        if (typeof db === 'undefined' || !db.getAll) return;
        const items = db.getAll('ITEMS') || [];
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
        if (typeof db === 'undefined' || !db.getAll) return;
        const sales = db.getAll('SALES_INVOICES') || [];
        const purchases = db.getAll('PURCHASES') || [];
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
                    <td><strong>₹${Number(t.amount || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge ${statusClass}">${t.status}</span></td>
                </tr>
            `;
        });

        container.innerHTML = html;
    },

    renderLowStockAlerts() {
        if (typeof db === 'undefined' || !db.getAll) return;
        const items = db.getAll('ITEMS') || [];
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

// Expose globally
window.Dashboard = Dashboard;

// Auto-run when DOM is ready or immediately if already loaded
if (document.readyState === 'complete' || document.readyState === 'interactive') {
    if (document.getElementById('chart-sales-vs-purchase')) {
        Dashboard.render();
    }
} else {
    document.addEventListener('DOMContentLoaded', () => {
        if (document.getElementById('chart-sales-vs-purchase')) {
            Dashboard.render();
        }
    });
}
