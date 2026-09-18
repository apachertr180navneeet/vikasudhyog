/* ==========================================================================
   VIKAS UDHYOG ERP - Main Application Router & UI Shell
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
    // Check authentication state
    if (!sessionStorage.getItem('vu_logged_in') && !window.location.pathname.includes('login.php')) {
        // If not logged in and not on login page, default demo session
        sessionStorage.setItem('vu_logged_in', 'true');
        sessionStorage.setItem('vu_user_role', 'Admin');
    }

    App.init();
});

const VIEW_ROUTES = {
    'dashboard': '/admin/dashboard',
    'master-company': '/admin/masters/company',
    'master-user': '/admin/masters/user',
    'master-access': '/admin/masters/access-level',
    'master-vendor': '/admin/masters/vendor',
    'master-customer': '/admin/masters/customer',
    'master-broker': '/admin/masters/broker',
    'master-item': '/admin/masters/item',
    'master-unit': '/admin/masters/unit',
    'master-account': '/admin/masters/account',
    'txn-purchase': '/admin/transactions/purchase-entry',
    'txn-wb-purchase': '/admin/transactions/wb-purchase-entry',
    'txn-sales-order': '/admin/transactions/sales-entry',
    'txn-wb-sales': '/admin/transactions/wb-sales-entry',
    'txn-order-dispatch': '/admin/transactions/order-dispatch',
    'txn-sales-invoice': '/admin/transactions/sales-purchase-order',
    'txn-receipt': '/admin/transactions/receipt-voucher',
    'txn-payment': '/admin/transactions/payment-voucher',
    'inv-overview': '/admin/inventory/stock-overview',
    'inv-ledger': '/admin/inventory/item-ledger',
    'inv-adjustment': '/admin/inventory/stock-adjustment',
    'inv-low-stock': '/admin/inventory/low-stock-alert',
    'rpt-purchase': '/admin/reports/purchase-report',
    'rpt-sales': '/admin/reports/sales-report',
    'rpt-order': '/admin/reports/order-report',
    'rpt-cash-reg': '/admin/reports/cash-bank-register',
    'set-company': '/admin/settings/company',
    'set-whatsapp': '/admin/settings/whatsapp',
    'set-backup': '/admin/settings/backup-restore'
};

const App = {
    currentView: 'dashboard',

    init() {
        this.bindNavigation();
        this.bindGlobalSearch();
        this.bindCompanySelector();
        this.bindNotifications();
        this.bindUserProfile();
        this.bindModals();
        
        // Initial view render based on current page
        const currentPage = document.body.getAttribute('data-page') || 'dashboard';
        this.navigateTo(currentPage);
    },

    bindNavigation() {
        // Sidebar toggle for mobile/tablet
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('mobile-open');
                overlay.classList.toggle('active');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('active');
            });
        }

        // Submenu accordion toggles
        document.querySelectorAll('.nav-item.has-submenu').forEach(item => {
            const link = item.querySelector('.nav-link');
            link.addEventListener('click', (e) => {
                e.preventDefault();
                // Close other submenus
                document.querySelectorAll('.nav-item.has-submenu').forEach(other => {
                    if (other !== item) other.classList.remove('open');
                });
                item.classList.toggle('open');
            });
        });

        // View Navigation clicks
        document.querySelectorAll('[data-view]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const href = btn.getAttribute('href');
                if (href && href !== '#' && !href.startsWith('javascript:')) {
                    // Allow normal browser navigation to the target HTML page
                    return;
                }
                e.preventDefault();
                const viewName = btn.getAttribute('data-view');
                if (viewName) {
                    this.navigateTo(viewName);
                    
                    // Highlight active menu item
                    document.querySelectorAll('.nav-item, .submenu-link').forEach(el => el.classList.remove('active'));
                    btn.classList.add('active');
                    const parentNavItem = btn.closest('.nav-item');
                    if (parentNavItem) parentNavItem.classList.add('active');

                    // Close mobile sidebar if open
                    if (sidebar) sidebar.classList.remove('mobile-open');
                    if (overlay) overlay.classList.remove('active');
                }
            });
        });
    },

    navigateTo(viewId) {
        this.currentView = viewId;

        const targetSection = document.getElementById(`view-${viewId}`);
        if (targetSection) {
            // Hide all view sections
            document.querySelectorAll('.view-section').forEach(section => {
                section.classList.remove('active');
            });
            targetSection.classList.add('active');
            
            // Trigger view-specific renderers
            if (viewId === 'dashboard' && window.Dashboard) Dashboard.render();
            else if (viewId.startsWith('master-') && window.Masters) Masters.render(viewId);
            else if (viewId.startsWith('txn-') && window.Transactions) Transactions.render(viewId);
            else if (viewId.startsWith('inv-') && window.Inventory) Inventory.render(viewId);
            else if (viewId.startsWith('rpt-') && window.Reports) Reports.render(viewId);
            else if (viewId.startsWith('set-') && window.SettingsModule) SettingsModule.render(viewId);

            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else if (VIEW_ROUTES[viewId]) {
            // Navigate to the corresponding HTML file
            window.location.href = VIEW_ROUTES[viewId];
        }
    },

    bindGlobalSearch() {
        const searchInput = document.getElementById('global-search-input');
        const dropdown = document.getElementById('global-search-dropdown');

        if (!searchInput || !dropdown) return;

        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase().trim();
            if (query.length < 2) {
                dropdown.classList.remove('active');
                dropdown.innerHTML = '';
                return;
            }

            const results = this.performSearch(query);
            this.renderSearchResults(results, dropdown);
        });

        document.addEventListener('click', (e) => {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    },

    performSearch(query) {
        const results = [];

        // Search Items
        const items = db.getAll('ITEMS');
        items.filter(i => i.name.toLowerCase().includes(query) || i.code.toLowerCase().includes(query)).forEach(i => {
            results.push({ type: 'Product Item', title: i.name, sub: `Code: ${i.code} | Stock: ${i.stock} ${i.unit}`, icon: 'fa-box', view: 'inv-overview' });
        });

        // Search Customers
        const customers = db.getAll('CUSTOMERS');
        customers.filter(c => c.name.toLowerCase().includes(query) || c.city.toLowerCase().includes(query)).forEach(c => {
            results.push({ type: 'Customer', title: c.name, sub: `City: ${c.city} | Balance: ₹${c.balance.toLocaleString('en-IN')}`, icon: 'fa-users', view: 'master-customer' });
        });

        // Search Vendors
        const vendors = db.getAll('VENDORS');
        vendors.filter(v => v.name.toLowerCase().includes(query) || v.city.toLowerCase().includes(query)).forEach(v => {
            results.push({ type: 'Vendor', title: v.name, sub: `City: ${v.city} | Outstanding: ₹${v.balance.toLocaleString('en-IN')}`, icon: 'fa-truck-field', view: 'master-vendor' });
        });

        // Search Brokers
        const brokers = db.getAll('BROKERS');
        brokers.filter(b => (b.name && b.name.toLowerCase().includes(query)) || (b.city && b.city.toLowerCase().includes(query)) || (b.contact && b.contact.toLowerCase().includes(query))).forEach(b => {
            results.push({ type: 'Broker', title: b.name, sub: `City: ${b.city || 'Sojat'} | Comm: ${b.commissionRate}%`, icon: 'fa-handshake', view: 'master-broker' });
        });

        // Search Sales Invoices
        const invoices = db.getAll('SALES_INVOICES');
        invoices.filter(inv => inv.invNo.toLowerCase().includes(query) || inv.customerName.toLowerCase().includes(query)).forEach(inv => {
            results.push({ type: 'Sales Invoice', title: inv.invNo, sub: `Customer: ${inv.customerName} | Amount: ₹${inv.grandTotal.toLocaleString('en-IN')}`, icon: 'fa-file-invoice-dollar', view: 'txn-sales-invoice' });
        });

        return results;
    },

    renderSearchResults(results, dropdown) {
        if (results.length === 0) {
            dropdown.innerHTML = `<div style="padding: 1rem; text-align: center; color: var(--text-muted); font-size: 0.85rem;">No matching records found</div>`;
            dropdown.classList.add('active');
            return;
        }

        let html = '';
        results.slice(0, 8).forEach(res => {
            html += `
                <div class="search-result-item" onclick="App.navigateTo('${res.view}')">
                    <div class="search-result-icon"><i class="fa-solid ${res.icon}"></i></div>
                    <div class="search-result-details">
                        <div class="search-result-title">${res.title}</div>
                        <div class="search-result-sub">[${res.type}] ${res.sub}</div>
                    </div>
                </div>
            `;
        });

        dropdown.innerHTML = html;
        dropdown.classList.add('active');
    },

    bindCompanySelector() {
        const select = document.getElementById('company-selector-dropdown');
        if (select) {
            select.addEventListener('change', (e) => {
                const selectedComp = e.target.value;
                localStorage.setItem(STORAGE_KEYS.CURRENT_COMPANY, selectedComp);
                App.showToast(`Switched active company to ${selectedComp}`, 'success');
                if (this.currentView === 'dashboard') Dashboard.render();
            });
        }
    },

    bindNotifications() {
        const btn = document.getElementById('notification-btn');
        const drawer = document.getElementById('notification-drawer');

        if (btn && drawer) {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                drawer.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!btn.contains(e.target) && !drawer.contains(e.target)) {
                    drawer.classList.remove('active');
                }
            });
        }
    },

    bindUserProfile() {
        const profileBtn = document.getElementById('user-profile-btn');
        const dropdown = document.getElementById('user-dropdown');

        if (profileBtn && dropdown) {
            profileBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });

            document.addEventListener('click', (e) => {
                if (!profileBtn.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('active');
                }
            });
        }
    },

    bindModals() {
        // Modal close button listeners
        document.querySelectorAll('.modal-close, [data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal-overlay');
                if (modal) modal.classList.remove('active');
            });
        });
    },

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.add('active');
    },

    closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) modal.classList.remove('active');
    },

    showToast(message, type = 'success') {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        let icon = 'fa-circle-check';
        if (type === 'danger') icon = 'fa-circle-exclamation';
        if (type === 'warning') icon = 'fa-triangle-exclamation';

        toast.innerHTML = `
            <i class="fa-solid ${icon}"></i>
            <div style="font-size: 0.88rem; font-weight: 500;">${message}</div>
        `;

        container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    },

    logout() {
        sessionStorage.removeItem('vu_logged_in');
        sessionStorage.removeItem('vu_user_role');
        sessionStorage.removeItem('vu_user_name');
        const logoutForm = document.getElementById('admin-logout-form');
        if (logoutForm) {
            logoutForm.submit();
        } else {
            window.location.href = '/admin/login';
        }
    }
};
