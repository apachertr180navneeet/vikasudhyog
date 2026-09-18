<!-- Top Navbar -->
<header class="top-navbar">
    <div class="navbar-left">
        <button class="btn-icon" id="sidebar-toggle"><i class="fa-solid fa-bars"></i></button>

        <div class="company-selector">
            <i class="fa-solid fa-building"></i>
            <select id="company-selector-dropdown">
                <option value="Vikas Udhyog">Vikas Udhyog (Sojat)</option>
                <option value="Vikas Herbal Products">Vikas Herbal Products</option>
                <option value="Vikas Trading">Vikas Trading</option>
            </select>
        </div>

        <div class="search-box-wrapper">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="global-search-input" placeholder="Search anything (Mehndi, Invoices, Customers)..." autocomplete="off">
            <div class="search-results-dropdown" id="global-search-dropdown"></div>
        </div>
    </div>

    <div class="navbar-right">
        <div class="notification-btn" id="notification-btn">
            <button class="btn-icon"><i class="fa-solid fa-bell"></i></button>
            <span class="notification-badge">3</span>

            <div class="notification-drawer" id="notification-drawer">
                <div class="notification-header">
                    <span class="notification-title">Notifications</span>
                    <span style="font-size: 0.75rem; color: var(--primary); cursor: pointer;">Mark all as read</span>
                </div>
                <div class="notification-list">
                    <div class="notification-item">
                        <div class="notification-icon-wrap"><i class="fa-solid fa-triangle-exclamation" style="color: var(--status-warning);"></i></div>
                        <div>
                            <div class="notification-text"><strong>Neem Powder</strong> is below minimum stock level (18 KG).</div>
                            <div class="notification-time">10 mins ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon-wrap"><i class="fa-solid fa-circle-check" style="color: var(--status-success);"></i></div>
                        <div>
                            <div class="notification-text">₹24,500 receipt recorded from <strong>Raj Traders</strong>.</div>
                            <div class="notification-time">1 hour ago</div>
                        </div>
                    </div>
                    <div class="notification-item">
                        <div class="notification-icon-wrap"><i class="fa-solid fa-truck-fast"></i></div>
                        <div>
                            <div class="notification-text">Order <strong>SO-1024</strong> dispatched via Vikas Logistics.</div>
                            <div class="notification-time">3 hours ago</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="user-profile-menu" id="user-profile-btn">
            <div class="avatar">A</div>
            <div class="user-info">
                <span class="user-name">{{ session('vu_user_name', 'Admin') }}</span>
                <span class="user-role">{{ session('vu_user_role', 'Super Administrator') }}</span>
            </div>
            <i class="fa-solid fa-chevron-down" style="font-size: 0.75rem; color: var(--text-muted);"></i>

            <div class="user-dropdown" id="user-dropdown">
                <a href="{{ route('admin.settings.company') }}" class="user-dropdown-item"><i class="fa-solid fa-user-gear"></i> Profile & Settings</a>
                <a href="{{ route('admin.masters.access-level') }}" class="user-dropdown-item"><i class="fa-solid fa-shield-halved"></i> Access Levels</a>
                <div class="user-dropdown-divider"></div>
                <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
                <div class="user-dropdown-item" onclick="sessionStorage.removeItem('vu_logged_in'); document.getElementById('admin-logout-form').submit();"><i class="fa-solid fa-right-from-bracket" style="color: var(--status-danger);"></i> Logout</div>
            </div>
        </div>
    </div>
</header>
