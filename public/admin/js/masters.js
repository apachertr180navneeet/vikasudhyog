/* ==========================================================================
   VIKAS UDHYOG ERP - Masters Management Controller
   ========================================================================== */

const Masters = {
    render(viewId) {
        if (viewId === 'master-company') this.renderCompanyMaster();
        if (viewId === 'master-user') this.renderUserMaster();
        if (viewId === 'master-access') this.renderAccessLevel();
        if (viewId === 'master-vendor') this.renderVendorMaster();
        if (viewId === 'master-customer') this.renderCustomerMaster();
        if (viewId === 'master-broker') this.renderBrokerMaster();
        if (viewId === 'master-item') this.renderItemMaster();
        if (viewId === 'master-unit') this.renderUnitMaster();
        if (viewId === 'master-account') this.renderAccountMaster();
    },

    // 1. Company Master
    renderCompanyMaster() {
        const companies = db.getAll('COMPANIES');
        const container = document.getElementById('company-table-body');
        if (!container) return;

        let html = '';
        companies.forEach(c => {
            html += `
                <tr>
                    <td><strong>${c.name}</strong></td>
                    <td>${c.gstin}</td>
                    <td>${c.pan}</td>
                    <td>${c.phone}</td>
                    <td>${c.email}</td>
                    <td>${c.city}</td>
                    <td><span class="badge badge-success">${c.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editCompany('${c.id}')"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="Masters.deleteCompany('${c.id}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveCompany(e) {
        if (e) e.preventDefault();
        const form = document.getElementById('company-form');
        const company = {
            id: document.getElementById('comp-id').value,
            name: document.getElementById('comp-name').value,
            gstin: document.getElementById('comp-gstin').value,
            pan: document.getElementById('comp-pan').value,
            phone: document.getElementById('comp-phone').value,
            email: document.getElementById('comp-email').value,
            address: document.getElementById('comp-address').value,
            city: document.getElementById('comp-city').value,
            state: document.getElementById('comp-state').value,
            pincode: document.getElementById('comp-pincode').value,
            fy: document.getElementById('comp-fy').value,
            status: 'Active'
        };

        if (!company.name) return App.showToast('Please enter Company Name', 'danger');

        db.saveItem('COMPANIES', company);
        App.showToast('Company saved successfully', 'success');
        App.closeModal('modal-company');
        this.renderCompanyMaster();
    },

    editCompany(id) {
        const c = db.getAll('COMPANIES').find(item => item.id === id);
        if (!c) return;

        document.getElementById('comp-id').value = c.id;
        document.getElementById('comp-name').value = c.name;
        document.getElementById('comp-gstin').value = c.gstin;
        document.getElementById('comp-pan').value = c.pan;
        document.getElementById('comp-phone').value = c.phone;
        document.getElementById('comp-email').value = c.email;
        document.getElementById('comp-address').value = c.address;
        document.getElementById('comp-city').value = c.city;
        document.getElementById('comp-state').value = c.state;
        document.getElementById('comp-pincode').value = c.pincode;
        document.getElementById('comp-fy').value = c.fy;

        App.openModal('modal-company');
    },

    deleteCompany(id) {
        if (confirm('Are you sure you want to delete this company?')) {
            db.deleteItem('COMPANIES', id);
            App.showToast('Company deleted', 'warning');
            this.renderCompanyMaster();
        }
    },

    // 2. User Master
    populateUserCompanyDropdown() {
        const select = document.getElementById('usr-company');
        if (!select) return;
        const companies = db.getAll('COMPANIES');
        if (companies && companies.length > 0) {
            let html = '';
            companies.forEach(c => {
                html += `<option value="${c.name}">${c.name}</option>`;
            });
            select.innerHTML = html;
        }
    },

    renderUserMaster() {
        this.populateUserCompanyDropdown();
        const users = db.getAll('USERS');
        const container = document.getElementById('user-table-body');
        if (!container) return;

        let html = '';
        users.forEach(u => {
            const pw = u.password || 'admin123';
            html += `
                <tr>
                    <td><strong>${u.name}</strong></td>
                    <td>${u.username}</td>
                    <td>
                        <span id="pw-text-${u.id}" data-pw="${pw}">••••••••</span>
                        <button type="button" onclick="Masters.toggleUserTablePassword('${u.id}', this)" title="Show/Hide Password" style="background:none; border:none; color:#6b7280; cursor:pointer; margin-left:6px;">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td>
                    <td>${u.email}</td>
                    <td>${u.mobile}</td>
                    <td><span class="badge badge-info">${u.role}</span></td>
                    <td>${u.company}</td>
                    <td><span class="badge badge-success">${u.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editUser('${u.id}')"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="Masters.deleteUser('${u.id}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveUser(e) {
        if (e) e.preventDefault();
        const user = {
            id: document.getElementById('usr-id').value,
            name: document.getElementById('usr-name').value,
            username: document.getElementById('usr-username').value,
            password: document.getElementById('usr-password').value,
            email: document.getElementById('usr-email').value,
            mobile: document.getElementById('usr-mobile').value,
            role: document.getElementById('usr-role').value,
            company: document.getElementById('usr-company').value,
            status: 'Active'
        };

        if (!user.name || !user.username || !user.password) return App.showToast('Please enter Name, Username, and Password', 'danger');

        db.saveItem('USERS', user);
        App.showToast('User saved successfully', 'success');
        App.closeModal('modal-user');
        this.renderUserMaster();
    },

    editUser(id) {
        this.populateUserCompanyDropdown();
        const u = db.getAll('USERS').find(item => item.id === id);
        if (!u) return;

        document.getElementById('usr-id').value = u.id;
        document.getElementById('usr-name').value = u.name;
        document.getElementById('usr-username').value = u.username;
        document.getElementById('usr-password').value = u.password || '';
        document.getElementById('usr-email').value = u.email;
        document.getElementById('usr-mobile').value = u.mobile;
        document.getElementById('usr-role').value = u.role;
        document.getElementById('usr-company').value = u.company;

        App.openModal('modal-user');
    },

    deleteUser(id) {
        if (confirm('Delete this user?')) {
            db.deleteItem('USERS', id);
            App.showToast('User deleted', 'warning');
            this.renderUserMaster();
        }
    },

    togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    },

    toggleUserTablePassword(userId, btn) {
        const el = document.getElementById(`pw-text-${userId}`);
        if (!el) return;
        const icon = btn.querySelector('i');
        const realPw = el.getAttribute('data-pw');
        if (el.innerText === '••••••••') {
            el.innerText = realPw;
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            el.innerText = '••••••••';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    },

    // 3. Access Level Management
    renderAccessLevel() {
        const roles = db.getAll('ROLES');
        const roleList = document.getElementById('role-list-container');
        if (!roleList) return;

        let html = '';
        roles.forEach((r, idx) => {
            html += `
                <div class="user-dropdown-item ${idx === 0 ? 'active' : ''}" style="padding: 0.8rem; font-weight: 600;" onclick="Masters.selectRole('${r.role}', this)">
                    <i class="fa-solid fa-shield-halved"></i> ${r.role}
                </div>
            `;
        });
        roleList.innerHTML = html;
        if (roles.length > 0) this.selectRole(roles[0].role);
    },

    selectRole(roleName, element) {
        if (element) {
            document.querySelectorAll('#role-list-container .user-dropdown-item').forEach(el => el.classList.remove('active'));
            element.classList.add('active');
        }

        const roles = db.getAll('ROLES');
        const r = roles.find(item => item.role === roleName) || roles[0];
        document.getElementById('selected-role-title').innerText = `Permissions for: ${r.role}`;

        const permissions = ['dashboard', 'company', 'users', 'vendors', 'customers', 'items', 'purchase', 'sales', 'inventory', 'reports', 'settings'];
        permissions.forEach(p => {
            const chk = document.getElementById(`perm-${p}`);
            if (chk) chk.checked = !!r[p];
        });
    },

    savePermissions() {
        App.showToast('Role permissions updated successfully', 'success');
    },

    // 4. Vendor Master
    renderVendorMaster() {
        const vendors = db.getAll('VENDORS');
        const container = document.getElementById('vendor-table-body');
        if (!container) return;

        let html = '';
        vendors.forEach(v => {
            html += `
                <tr>
                    <td><strong>${v.code}</strong></td>
                    <td><strong>${v.name}</strong></td>
                    <td>${v.contact}</td>
                    <td>${v.phone}</td>
                    <td>${v.gstin}</td>
                    <td>${v.city}</td>
                    <td><strong>₹${(v.balance || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge badge-success">${v.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editVendor('${v.id}')"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="Masters.deleteVendor('${v.id}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveVendor(e) {
        if (e) e.preventDefault();
        const vendor = {
            id: document.getElementById('vnd-id').value,
            code: document.getElementById('vnd-code').value || 'VND-' + Math.floor(Math.random()*100),
            name: document.getElementById('vnd-name').value,
            contact: document.getElementById('vnd-contact').value,
            phone: document.getElementById('vnd-phone').value,
            email: document.getElementById('vnd-email').value,
            gstin: document.getElementById('vnd-gstin').value,
            city: document.getElementById('vnd-city').value,
            state: document.getElementById('vnd-state').value,
            balance: parseFloat(document.getElementById('vnd-balance').value) || 0,
            terms: document.getElementById('vnd-terms').value,
            status: 'Active'
        };

        if (!vendor.name) return App.showToast('Please enter Vendor Firm Name', 'danger');

        db.saveItem('VENDORS', vendor);
        App.showToast('Vendor saved successfully', 'success');
        App.closeModal('modal-vendor');
        this.renderVendorMaster();
    },

    editVendor(id) {
        const v = db.getAll('VENDORS').find(item => item.id === id);
        if (!v) return;

        document.getElementById('vnd-id').value = v.id;
        document.getElementById('vnd-code').value = v.code;
        document.getElementById('vnd-name').value = v.name;
        document.getElementById('vnd-contact').value = v.contact;
        document.getElementById('vnd-phone').value = v.phone;
        document.getElementById('vnd-email').value = v.email;
        document.getElementById('vnd-gstin').value = v.gstin;
        document.getElementById('vnd-city').value = v.city;
        document.getElementById('vnd-state').value = v.state;
        document.getElementById('vnd-balance').value = v.balance;
        document.getElementById('vnd-terms').value = v.terms;

        App.openModal('modal-vendor');
    },

    deleteVendor(id) {
        if (confirm('Delete vendor record?')) {
            db.deleteItem('VENDORS', id);
            App.showToast('Vendor removed', 'warning');
            this.renderVendorMaster();
        }
    },

    // 5. Customer Master
    renderCustomerMaster() {
        const customers = db.getAll('CUSTOMERS');
        const container = document.getElementById('customer-table-body');
        if (!container) return;

        let html = '';
        customers.forEach(c => {
            html += `
                <tr>
                    <td><strong>${c.name}</strong></td>
                    <td>${c.contact}</td>
                    <td>${c.phone}</td>
                    <td>${c.gstin}</td>
                    <td>${c.city}</td>
                    <td>₹${(c.creditLimit || 0).toLocaleString('en-IN')}</td>
                    <td><strong>₹${(c.balance || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge badge-success">${c.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editCustomer('${c.id}')"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="Masters.deleteCustomer('${c.id}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveCustomer(e) {
        if (e) e.preventDefault();
        const customer = {
            id: document.getElementById('cst-id').value,
            name: document.getElementById('cst-name').value,
            contact: document.getElementById('cst-contact').value,
            phone: document.getElementById('cst-phone').value,
            email: document.getElementById('cst-email').value,
            gstin: document.getElementById('cst-gstin').value,
            city: document.getElementById('cst-city').value,
            creditLimit: parseFloat(document.getElementById('cst-limit').value) || 0,
            balance: parseFloat(document.getElementById('cst-balance').value) || 0,
            status: 'Active'
        };

        if (!customer.name) return App.showToast('Please enter Customer Name', 'danger');

        db.saveItem('CUSTOMERS', customer);
        App.showToast('Customer saved successfully', 'success');
        App.closeModal('modal-customer');
        this.renderCustomerMaster();
    },

    editCustomer(id) {
        const c = db.getAll('CUSTOMERS').find(item => item.id === id);
        if (!c) return;

        document.getElementById('cst-id').value = c.id;
        document.getElementById('cst-name').value = c.name;
        document.getElementById('cst-contact').value = c.contact;
        document.getElementById('cst-phone').value = c.phone;
        document.getElementById('cst-email').value = c.email;
        document.getElementById('cst-gstin').value = c.gstin;
        document.getElementById('cst-city').value = c.city;
        document.getElementById('cst-limit').value = c.creditLimit;
        document.getElementById('cst-balance').value = c.balance;

        App.openModal('modal-customer');
    },

    deleteCustomer(id) {
        if (confirm('Delete customer record?')) {
            db.deleteItem('CUSTOMERS', id);
            App.showToast('Customer deleted', 'warning');
            this.renderCustomerMaster();
        }
    },

    // 6. Item Master
    renderItemMaster() {
        const items = db.getAll('ITEMS');
        const container = document.getElementById('item-table-body');
        if (!container) return;

        let html = '';
        items.forEach(i => {
            const stockBadge = i.stock <= i.minStock 
                ? `<span class="badge badge-danger">Low Stock (${i.stock})</span>`
                : `<span class="badge badge-success">${i.stock} ${i.unit}</span>`;

            html += `
                <tr>
                    <td><strong>${i.code}</strong></td>
                    <td><strong>${i.name}</strong></td>
                    <td><span class="badge badge-dark">${i.category}</span></td>
                    <td>${i.unit}</td>
                    <td>₹${i.purchaseRate}</td>
                    <td>₹${i.saleRate}</td>
                    <td>${i.hsn || '1404'}</td>
                    <td>${i.gst}%</td>
                    <td>${stockBadge}</td>
                    <td>${i.minStock} ${i.unit}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editItem('${i.id}')"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="Masters.deleteItem('${i.id}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveItemMaster(e) {
        if (e) e.preventDefault();
        const item = {
            id: document.getElementById('itm-id').value,
            code: document.getElementById('itm-code').value || 'ITM-' + Math.floor(Math.random()*1000),
            name: document.getElementById('itm-name').value,
            category: document.getElementById('itm-category').value,
            unit: document.getElementById('itm-unit').value,
            purchaseRate: parseFloat(document.getElementById('itm-purchase-rate').value) || 0,
            saleRate: parseFloat(document.getElementById('itm-sale-rate').value) || 0,
            gst: parseFloat(document.getElementById('itm-gst').value) || 0,
            hsn: document.getElementById('itm-hsn').value || '1404',
            stock: parseFloat(document.getElementById('itm-stock').value) || 0,
            minStock: parseFloat(document.getElementById('itm-min-stock').value) || 0,
            batch: document.getElementById('itm-batch').value || 'B-2026-01',
            exp: document.getElementById('itm-exp').value || '2028-12-31',
            status: 'Active'
        };

        if (!item.name) return App.showToast('Please enter Product Name', 'danger');

        db.saveItem('ITEMS', item);
        App.showToast('Product saved successfully', 'success');
        App.closeModal('modal-item');
        this.renderItemMaster();
    },

    editItem(id) {
        const i = db.getAll('ITEMS').find(item => item.id === id);
        if (!i) return;

        document.getElementById('itm-id').value = i.id;
        document.getElementById('itm-code').value = i.code;
        document.getElementById('itm-name').value = i.name;
        document.getElementById('itm-category').value = i.category;
        document.getElementById('itm-unit').value = i.unit;
        document.getElementById('itm-purchase-rate').value = i.purchaseRate;
        document.getElementById('itm-sale-rate').value = i.saleRate;
        document.getElementById('itm-gst').value = i.gst;
        if (document.getElementById('itm-hsn')) document.getElementById('itm-hsn').value = i.hsn || '1404';
        document.getElementById('itm-stock').value = i.stock;
        document.getElementById('itm-min-stock').value = i.minStock;

        App.openModal('modal-item');
    },

    deleteItem(id) {
        if (confirm('Delete product item?')) {
            db.deleteItem('ITEMS', id);
            App.showToast('Item deleted', 'warning');
            this.renderItemMaster();
        }
    },

    // 7. Unit Master
    renderUnitMaster() {
        const units = db.getAll('UNITS');
        const container = document.getElementById('unit-table-body');
        if (!container) return;

        let html = '';
        units.forEach(u => {
            html += `
                <tr>
                    <td><strong>${u.name}</strong></td>
                    <td>${u.description}</td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action delete" onclick="Masters.deleteUnit('${u.id}')"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveUnit(e) {
        if (e) e.preventDefault();
        const unit = {
            id: document.getElementById('unt-id').value,
            name: document.getElementById('unt-name').value,
            description: document.getElementById('unt-desc').value
        };

        if (!unit.name) return App.showToast('Please enter Unit Name', 'danger');

        db.saveItem('UNITS', unit);
        App.showToast('Unit saved', 'success');
        App.closeModal('modal-unit');
        this.renderUnitMaster();
    },

    deleteUnit(id) {
        if (confirm('Delete unit?')) {
            db.deleteItem('UNITS', id);
            App.showToast('Unit deleted', 'warning');
            this.renderUnitMaster();
        }
    },

    // 8. Account Master
    renderAccountMaster() {
        const accounts = db.getAll('ACCOUNTS');
        const container = document.getElementById('account-table-body');
        if (!container) return;

        let html = '';
        accounts.forEach(a => {
            html += `
                <tr>
                    <td><strong>${a.name}</strong></td>
                    <td><span class="badge badge-info">${a.type}</span></td>
                    <td>₹${(a.opening || 0).toLocaleString('en-IN')}</td>
                    <td><strong>₹${(a.current || 0).toLocaleString('en-IN')}</strong></td>
                    <td><span class="badge badge-success">${a.status}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editAccount('${a.id}')"><i class="fa-solid fa-pen"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveAccount(e) {
        if (e) e.preventDefault();
        const accId = document.getElementById('acc-id').value;
        const existing = accId ? db.getAll('ACCOUNTS').find(item => item.id === accId) : null;
        const opening = parseFloat(document.getElementById('acc-opening').value) || 0;
        const acc = {
            id: accId,
            name: document.getElementById('acc-name').value,
            type: document.getElementById('acc-type').value,
            opening: opening,
            current: existing ? existing.current : opening,
            status: 'Active'
        };

        if (!acc.name) return App.showToast('Please enter Account Name', 'danger');

        db.saveItem('ACCOUNTS', acc);
        App.showToast('Account saved successfully', 'success');
        App.closeModal('modal-account');
        this.renderAccountMaster();
    },

    editAccount(id) {
        const a = db.getAll('ACCOUNTS').find(item => item.id === id);
        if (!a) return;

        document.getElementById('acc-id').value = a.id;
        document.getElementById('acc-name').value = a.name;
        document.getElementById('acc-type').value = a.type;
        document.getElementById('acc-opening').value = a.opening;

        App.openModal('modal-account');
    },

    // 9. Broker Master
    renderBrokerMaster() {
        const brokers = db.getAll('BROKERS');
        const container = document.getElementById('broker-table-body');
        if (!container) return;

        if (brokers.length === 0) {
            container.innerHTML = `<tr><td colspan="9" style="text-align:center; color:var(--text-muted); padding:1.5rem;">No brokers found. Click "+ Add Broker" to create one.</td></tr>`;
            return;
        }

        let html = '';
        brokers.forEach(b => {
            const statusBadge = b.status === 'Active' ? 'badge-success' : 'badge-danger';
            html += `
                <tr>
                    <td><span class="badge badge-info">${b.code || b.id}</span></td>
                    <td><strong>${b.name}</strong></td>
                    <td>${b.contact || '-'}</td>
                    <td>${b.phone || '-'}</td>
                    <td>${b.email || '-'}</td>
                    <td>${b.city || '-'}</td>
                    <td><strong>${b.commissionRate || 0}%</strong></td>
                    <td><span class="badge ${statusBadge}">${b.status || 'Active'}</span></td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="Masters.editBroker('${b.id}')" title="Edit Broker"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action delete" onclick="Masters.deleteBroker('${b.id}')" title="Delete Broker"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>
            `;
        });
        container.innerHTML = html;
    },

    saveBroker(e) {
        if (e) e.preventDefault();
        const existingId = document.getElementById('brk-id').value;
        const existingCode = document.getElementById('brk-code').value;
        
        const broker = {
            id: existingId || null,
            code: existingCode || ('BRK-' + Math.floor(10 + Math.random() * 90)),
            name: document.getElementById('brk-name').value.trim(),
            contact: document.getElementById('brk-contact').value.trim(),
            phone: document.getElementById('brk-phone').value.trim(),
            email: document.getElementById('brk-email').value.trim(),
            gstin: document.getElementById('brk-gstin').value.trim(),
            city: document.getElementById('brk-city').value.trim() || 'Sojat',
            state: 'Rajasthan',
            commissionRate: parseFloat(document.getElementById('brk-commission').value) || 0,
            status: document.getElementById('brk-status').value,
            remarks: document.getElementById('brk-remarks').value.trim()
        };

        if (!broker.name) return App.showToast('Please enter Broker / Agency Name', 'danger');

        db.saveItem('BROKERS', broker);
        App.showToast('Broker details saved successfully', 'success');
        App.closeModal('modal-broker');
        this.renderBrokerMaster();
    },

    editBroker(id) {
        const b = db.getAll('BROKERS').find(item => item.id === id);
        if (!b) return;

        document.getElementById('brk-id').value = b.id;
        document.getElementById('brk-code').value = b.code || '';
        document.getElementById('brk-name').value = b.name || '';
        document.getElementById('brk-contact').value = b.contact || '';
        document.getElementById('brk-phone').value = b.phone || '';
        document.getElementById('brk-email').value = b.email || '';
        document.getElementById('brk-gstin').value = b.gstin || '';
        document.getElementById('brk-city').value = b.city || '';
        document.getElementById('brk-commission').value = b.commissionRate !== undefined ? b.commissionRate : 1.5;
        document.getElementById('brk-status').value = b.status || 'Active';
        document.getElementById('brk-remarks').value = b.remarks || '';

        App.openModal('modal-broker');
    },

    deleteBroker(id) {
        if (confirm('Are you sure you want to delete this broker record?')) {
            db.deleteItem('BROKERS', id);
            App.showToast('Broker deleted', 'warning');
            this.renderBrokerMaster();
        }
    }
};
