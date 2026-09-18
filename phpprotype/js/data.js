/* ==========================================================================
   VIKAS UDHYOG ERP - Data Store & LocalStorage Management
   ========================================================================== */

const STORAGE_KEYS = {
    COMPANIES: 'vu_companies',
    USERS: 'vu_users',
    ROLES: 'vu_roles',
    VENDORS: 'vu_vendors',
    CUSTOMERS: 'vu_customers',
    ITEMS: 'vu_items',
    UNITS: 'vu_units',
    ACCOUNTS: 'vu_accounts',
    BROKERS: 'vu_brokers',
    PURCHASES: 'vu_purchases',
    WB_PURCHASES: 'vu_wb_purchases',
    PURCHASE_ORDERS: 'vu_purchase_orders',
    SALES_ORDERS: 'vu_sales_orders',
    WB_SALES: 'vu_wb_sales',
    DISPATCHES: 'vu_dispatches',
    SALES_INVOICES: 'vu_sales_invoices',
    RECEIPTS: 'vu_receipts',
    PAYMENTS: 'vu_payments',
    CURRENT_COMPANY: 'vu_current_company',
    SETTINGS: 'vu_settings',
    WHATSAPP: 'vu_whatsapp'
};

// Default Initial Seed Data
const INITIAL_DATA = {
    companies: [
        { id: 'COMP-1', name: 'Vikas Udhyog', gstin: '08ABCDE1234F1Z5', pan: 'ABCDE1234F', phone: '+91 98290 12345', email: 'info@vikasudhyog.com', city: 'Sojat', state: 'Rajasthan', pincode: '306104', address: 'Industrial Area, Heavy Industrial Zone, Sojat City', status: 'Active', fy: '2026-2027' },
        { id: 'COMP-2', name: 'Vikas Herbal Products', gstin: '08FGHIJ5678K1Z9', pan: 'FGHIJ5678K', phone: '+91 98290 67890', email: 'sales@vikasherbal.com', city: 'Jodhpur', state: 'Rajasthan', pincode: '342001', address: 'Boranada Industrial Park, Jodhpur', status: 'Active', fy: '2026-2027' },
        { id: 'COMP-3', name: 'Vikas Trading', gstin: '08KLMNO9012P1Z3', pan: 'KLMNO9012P', phone: '+91 98290 54321', email: 'trading@vikasudhyog.com', city: 'Pali', state: 'Rajasthan', pincode: '306401', address: 'Mandi Road, Pali', status: 'Active', fy: '2026-2027' }
    ],

    users: [
        { id: 'USR-1', name: 'Administrator', username: 'admin', password: 'admin123', email: 'admin@vikasudhyog.com', mobile: '9829012345', role: 'Admin', company: 'Vikas Udhyog', status: 'Active' },
        { id: 'USR-2', name: 'Rajesh Sharma', username: 'rajesh', password: 'rajesh123', email: 'rajesh@vikasudhyog.com', mobile: '9829023456', role: 'Manager', company: 'Vikas Udhyog', status: 'Active' },
        { id: 'USR-3', name: 'Suresh Verma', username: 'suresh', password: 'suresh123', email: 'suresh@vikasudhyog.com', mobile: '9829034567', role: 'Accountant', company: 'Vikas Udhyog', status: 'Active' },
        { id: 'USR-4', name: 'Amit Jain', username: 'amit', password: 'amit123', email: 'amit@vikasudhyog.com', mobile: '9829045678', role: 'Sales Manager', company: 'Vikas Udhyog', status: 'Active' }
    ],

    roles: [
        { role: 'Admin', dashboard: true, company: true, users: true, vendors: true, customers: true, brokers: true, items: true, purchase: true, sales: true, inventory: true, reports: true, settings: true },
        { role: 'Manager', dashboard: true, company: false, users: false, vendors: true, customers: true, brokers: true, items: true, purchase: true, sales: true, inventory: true, reports: true, settings: false },
        { role: 'Accountant', dashboard: true, company: false, users: false, vendors: true, customers: true, brokers: true, items: false, purchase: true, sales: true, inventory: false, reports: true, settings: false },
        { role: 'Purchase Manager', dashboard: true, company: false, users: false, vendors: true, customers: false, brokers: true, items: true, purchase: true, sales: false, inventory: true, reports: true, settings: false },
        { role: 'Sales Manager', dashboard: true, company: false, users: false, vendors: false, customers: true, brokers: true, items: true, purchase: false, sales: true, inventory: true, reports: true, settings: false },
        { role: 'Warehouse Staff', dashboard: true, company: false, users: false, vendors: false, customers: false, brokers: false, items: true, purchase: false, sales: false, inventory: true, reports: false, settings: false }
    ],

    vendors: [
        { id: 'VEN-101', code: 'VND-01', name: 'Natural Herbs Pvt Ltd', contact: 'Ramesh Patel', phone: '9876543210', email: 'info@naturalherbs.com', gstin: '08AAACN1234A1Z1', city: 'Udaipur', state: 'Rajasthan', balance: 184200, status: 'Active', terms: '30 Days' },
        { id: 'VEN-102', code: 'VND-02', name: 'Shree Herbal Suppliers', contact: 'Mahesh Kumar', phone: '9876543211', email: 'shreeherbal@gmail.com', gstin: '08BBBCS5678B1Z2', city: 'Nagaur', state: 'Rajasthan', balance: 90150, status: 'Active', terms: '15 Days' },
        { id: 'VEN-103', code: 'VND-03', name: 'Green Earth Traders', contact: 'Vikram Singh', phone: '9876543212', email: 'greenearth@traders.com', gstin: '08CCCGT9012C1Z3', city: 'Barmer', state: 'Rajasthan', balance: 0, status: 'Active', terms: 'Immediate' },
        { id: 'VEN-104', code: 'VND-04', name: 'Ayurveda Raw Materials', contact: 'Dinesh Agarwal', phone: '9876543213', email: 'ayurvedaraw@gmail.com', gstin: '08DDDAR3456D1Z4', city: 'Jaipur', state: 'Rajasthan', balance: 45000, status: 'Active', terms: '30 Days' }
    ],

    customers: [
        { id: 'CUST-201', name: 'Raj Traders', contact: 'Sunil Sharma', phone: '9828011223', email: 'rajtraders@gmail.com', gstin: '08EEERT7890E1Z5', city: 'Delhi', creditLimit: 500000, balance: 245600, status: 'Active' },
        { id: 'CUST-202', name: 'Sharma Cosmetics', contact: 'Pankaj Sharma', phone: '9828022334', email: 'sharmacosmetics@yahoo.com', gstin: '08FFFSC1234F1Z6', city: 'Jaipur', creditLimit: 300000, balance: 128000, status: 'Active' },
        { id: 'CUST-203', name: 'Natural Beauty Store', contact: 'Neha Gupta', phone: '9828033445', email: 'naturalbeauty@gmail.com', gstin: '08GGGNB5678G1Z7', city: 'Mumbai', creditLimit: 400000, balance: 65000, status: 'Active' },
        { id: 'CUST-204', name: 'Ayush Enterprises', contact: 'Karan Mehra', phone: '9828044556', email: 'ayushenterprises@outlook.com', gstin: '08HHHAE9012H1Z8', city: 'Ahmedabad', creditLimit: 250000, balance: 44000, status: 'Active' },
        { id: 'CUST-205', name: 'Green Care Distributors', contact: 'Sanjay Dutt', phone: '9828055667', email: 'greencare@gmail.com', gstin: '08IIIKD3456I1Z9', city: 'Indore', creditLimit: 200000, balance: 0, status: 'Active' }
    ],

    items: [
        { id: 'ITM-301', code: 'MHN-01', name: 'Premium Mehndi Powder', category: 'Mehndi', unit: 'KG', hsn: '1404', purchaseRate: 120, saleRate: 180, billRate: 90, gst: 18, stock: 450, minStock: 100, status: 'Active', batch: 'B-2026-08', exp: '2028-08-31', desc: '100% Organic Triple Sifted Sojat Henna Powder' },
        { id: 'ITM-302', code: 'MHN-02', name: 'Natural Henna Powder', category: 'Mehndi', unit: 'KG', hsn: '1404', purchaseRate: 90, saleRate: 140, billRate: 70, gst: 18, stock: 320, minStock: 80, status: 'Active', batch: 'B-2026-07', exp: '2028-07-31', desc: 'Natural Green Henna Leaf Powder' },
        { id: 'ITM-303', code: 'HRB-01', name: 'Amla Powder', category: 'Herbal Powder', unit: 'KG', hsn: '1211', purchaseRate: 150, saleRate: 230, billRate: 115, gst: 12, stock: 180, minStock: 50, status: 'Active', batch: 'B-2026-05', exp: '2027-12-31', desc: 'Pure Indian Gooseberry Organic Powder' },
        { id: 'ITM-304', code: 'HRB-02', name: 'Neem Powder', category: 'Herbal Powder', unit: 'KG', hsn: '1211', purchaseRate: 80, saleRate: 130, billRate: 65, gst: 12, stock: 18, minStock: 30, status: 'Active', batch: 'B-2026-06', exp: '2027-11-30', desc: 'Natural Azadirachta Indica Leaf Powder' },
        { id: 'ITM-305', code: 'HRB-03', name: 'Shikakai Powder', category: 'Herbal Powder', unit: 'KG', hsn: '1211', purchaseRate: 110, saleRate: 175, billRate: 90, gst: 12, stock: 140, minStock: 40, status: 'Active', batch: 'B-2026-04', exp: '2028-04-30', desc: 'Acacia Concinna Fruit Pod Powder' },
        { id: 'ITM-306', code: 'HRB-04', name: 'Brahmi Powder', category: 'Herbal Powder', unit: 'KG', hsn: '1211', purchaseRate: 200, saleRate: 310, billRate: 155, gst: 12, stock: 90, minStock: 25, status: 'Active', batch: 'B-2026-03', exp: '2027-10-31', desc: 'Bacopa Monnieri Natural Leaf Powder' },
        { id: 'ITM-307', code: 'RAW-01', name: 'Multani Mitti', category: 'Raw Material', unit: 'KG', hsn: '2508', purchaseRate: 25, saleRate: 45, billRate: 22.5, gst: 5, stock: 800, minStock: 200, status: 'Active', batch: 'B-2026-01', exp: '2030-01-01', desc: 'Fuller Earth Clay Powder' },
        { id: 'ITM-308', code: 'FIN-01', name: 'Herbal Hair Pack', category: 'Finished Product', unit: 'BOX', hsn: '3305', purchaseRate: 180, saleRate: 290, billRate: 145, gst: 18, stock: 25, minStock: 50, status: 'Active', batch: 'B-2026-09', exp: '2027-09-30', desc: 'Complete 7-Herb Blend Natural Hair Care Pack' }
    ],

    units: [
        { id: 'UNT-1', name: 'KG', description: 'Kilogram' },
        { id: 'UNT-2', name: 'GRAM', description: 'Grams' },
        { id: 'UNT-3', name: 'PCS', description: 'Pieces' },
        { id: 'UNT-4', name: 'BOX', description: 'Box Packaging' },
        { id: 'UNT-5', name: 'PACKET', description: 'Small Packet' },
        { id: 'UNT-6', name: 'BAG', description: 'Bulk Bag 50KG' },
        { id: 'UNT-7', name: 'LITER', description: 'Liquid Liter' }
    ],

    accounts: [
        { id: 'ACC-1', name: 'Cash In Hand', type: 'Cash', opening: 50000, current: 78500, status: 'Active' },
        { id: 'ACC-2', name: 'HDFC Bank - Current Account', type: 'Bank', opening: 450000, current: 890450, status: 'Active' },
        { id: 'ACC-3', name: 'SBI Bank - Corporate', type: 'Bank', opening: 300000, current: 420800, status: 'Active' },
        { id: 'ACC-4', name: 'Sales Account', type: 'Income', opening: 0, current: 1540200, status: 'Active' },
        { id: 'ACC-5', name: 'Purchase Account', type: 'Expense', opening: 0, current: 890500, status: 'Active' },
        { id: 'ACC-6', name: 'Transport & Freight Expense', type: 'Expense', opening: 0, current: 42300, status: 'Active' },
        { id: 'ACC-7', name: 'Staff Salary Expense', type: 'Expense', opening: 0, current: 185000, status: 'Active' },
        { id: 'ACC-8', name: 'Electricity & Factory Power', type: 'Expense', opening: 0, current: 34500, status: 'Active' }
    ],

    brokers: [
        { id: 'BRK-101', code: 'BRK-01', name: 'Rameshwar Brokerage', contact: 'Rameshwar Vyas', phone: '9829112233', email: 'rameshwar.broker@gmail.com', gstin: '08AAAPB1234K1Z1', city: 'Sojat', state: 'Rajasthan', commissionRate: 1.5, remarks: 'Sojat Mandi Henna Broker', status: 'Active' },
        { id: 'BRK-102', code: 'BRK-02', name: 'Jodhpur Spices & Herbs Agency', contact: 'Omprakash Rathore', phone: '9829223344', email: 'jodhpurherbs@agency.com', gstin: '08BBBJR5678L1Z2', city: 'Jodhpur', state: 'Rajasthan', commissionRate: 2.0, remarks: 'Herbal Powder Bulk Agent', status: 'Active' },
        { id: 'BRK-103', code: 'BRK-03', name: 'Sojat Mandi Dalal Samiti', contact: 'Kailash Choudhary', phone: '9829334455', email: 'kailash.dalal@yahoo.com', gstin: '08CCCKC9012M1Z3', city: 'Sojat', state: 'Rajasthan', commissionRate: 1.0, remarks: 'Raw Material Mandi Broker', status: 'Active' }
    ],

    purchases: [
        { id: 'PUR-1001', invNo: 'INV-NH-402', vendorId: 'VEN-101', vendorName: 'Natural Herbs Pvt Ltd', date: '2026-09-08', warehouse: 'Main Factory Storage', subtotal: 37500, gst: 6750, billTotal: 44250, underBillingTotal: 44250, total: 88500, status: 'Paid', items: [{ itemId: 'ITM-301', itemName: 'Premium Mehndi Powder', batch: 'B-2026-08', qty: 500, billRate: 60, underRate: 60, rate: 60, gst: 18, amount: 60000 }] },
        { id: 'PUR-1002', invNo: 'INV-SH-109', vendorId: 'VEN-102', vendorName: 'Shree Herbal Suppliers', date: '2026-09-07', warehouse: 'Raw Material Godown', subtotal: 17500, gst: 2100, billTotal: 19600, underBillingTotal: 19600, total: 39200, status: 'Pending', items: [{ itemId: 'ITM-303', itemName: 'Amla Powder', batch: 'B-2026-05', qty: 200, billRate: 75, underRate: 75, rate: 75, gst: 12, amount: 30000 }] }
    ],

    wbPurchases: [
        { id: 'WBP-101', slipNo: 'WBP-801', vendorId: 'VEN-101', vendorName: 'Natural Herbs Pvt Ltd', date: '2026-09-08', totalWeight: 400, totalAmount: 24000, status: 'Recorded', items: [{ itemId: 'ITM-301', itemName: 'Premium Mehndi Powder', batch: 'B-2026-08', hsn: '1404', netWeight: 400, unit: 'KG', ubRate: 60, underAmount: 24000 }] },
        { id: 'WBP-102', slipNo: 'WBP-802', vendorId: 'VEN-102', vendorName: 'Shree Herbal Suppliers', date: '2026-09-07', totalWeight: 200, totalAmount: 15000, status: 'Completed', items: [{ itemId: 'ITM-303', itemName: 'Amla Powder', batch: 'B-2026-05', hsn: '1211', netWeight: 200, unit: 'KG', ubRate: 75, underAmount: 15000 }] }
    ],

    purchaseOrders: [
        { id: 'PO-1042', poNo: 'PO-1042', vendorId: 'VEN-101', vendorName: 'Natural Herbs Pvt Ltd', date: '2026-09-08', expDate: '2026-09-15', billTotal: 19100, underBillingTotal: 19100, total: 38200, status: 'Pending', items: [{ itemName: 'Premium Mehndi Powder', qty: 200, billRate: 60, underRate: 60, rate: 120, amount: 24000 }] },
        { id: 'PO-1041', poNo: 'PO-1041', vendorId: 'VEN-104', vendorName: 'Ayurveda Raw Materials', date: '2026-09-05', expDate: '2026-09-12', billTotal: 32500, underBillingTotal: 32500, total: 65000, status: 'Approved', items: [{ itemName: 'Brahmi Powder', qty: 300, billRate: 100, underRate: 100, rate: 200, amount: 60000 }] }
    ],

    salesOrders: [
        { id: 'SO-1024', soNo: 'SO-1024', customerId: 'CUST-201', customerName: 'Raj Traders', date: '2026-09-08', delDate: '2026-09-10', billTotal: 22500, underBillingTotal: 22500, total: 45000, status: 'Dispatched', items: [{ itemName: 'Premium Mehndi Powder', qty: 200, billRate: 90, underRate: 90, rate: 180, amount: 36000 }] },
        { id: 'SO-1025', soNo: 'SO-1025', customerId: 'CUST-202', customerName: 'Sharma Cosmetics', date: '2026-09-08', delDate: '2026-09-11', billTotal: 14250, underBillingTotal: 14250, total: 28500, status: 'Pending', items: [{ itemName: 'Herbal Hair Pack', qty: 80, billRate: 145, underRate: 145, rate: 290, amount: 23200 }] }
    ],

    wbSales: [
        { id: 'WBS-201', slipNo: 'WBS-901', customerId: 'CUST-201', customerName: 'Raj Traders', date: '2026-09-08', totalWeight: 250, totalAmount: 22500, status: 'Dispatched', items: [{ itemId: 'ITM-301', itemName: 'Premium Mehndi Powder', batch: 'B-2026-08', hsn: '1404', netWeight: 250, unit: 'KG', ubRate: 90, underAmount: 22500 }] },
        { id: 'WBS-202', slipNo: 'WBS-902', customerId: 'CUST-202', customerName: 'Sharma Cosmetics', date: '2026-09-07', totalWeight: 150, totalAmount: 17250, status: 'Pending', items: [{ itemId: 'ITM-303', itemName: 'Amla Powder', batch: 'B-2026-05', hsn: '1211', netWeight: 150, unit: 'KG', ubRate: 115, underAmount: 17250 }] }
    ],

    dispatches: [
        { id: 'DSP-501', orderNo: 'SO-1024', customerName: 'Raj Traders', date: '2026-09-08', vehicleNo: 'RJ-19-GA-4521', driverName: 'Mohan Lal', transporter: 'Vikas Logistics', status: 'Dispatched', items: [{ itemName: 'Premium Mehndi Powder', qty: 200 }] }
    ],

    salesInvoices: [
        { id: 'INV-1025', invNo: 'INV-1025', customerId: 'CUST-201', customerName: 'Raj Traders', date: '2026-09-08', total: 12250, cgst: 934, sgst: 934, igst: 0, billTotal: 14118, underBillingTotal: 10382, grandTotal: 24500, status: 'Paid', items: [{ itemId: 'ITM-301', itemName: 'Premium Mehndi Powder', batch: 'B-2026-08', qty: 100, billRate: 90, underRate: 90, rate: 180, gst: 18, amount: 18000 }] },
        { id: 'INV-1024', invNo: 'INV-1024', customerId: 'CUST-202', customerName: 'Sharma Cosmetics', date: '2026-09-06', total: 24100, cgst: 0, sgst: 0, igst: 2582, billTotal: 26682, underBillingTotal: 21518, grandTotal: 48200, status: 'Partial', items: [{ itemId: 'ITM-303', itemName: 'Amla Powder', batch: 'B-2026-05', qty: 150, billRate: 115, underRate: 115, rate: 230, gst: 12, amount: 34500 }] }
    ],

    receipts: [
        { id: 'RCP-801', rcpNo: 'RCP-801', date: '2026-09-08', customerName: 'Raj Traders', amount: 24500, mode: 'HDFC Bank (UPI)', refNo: 'UPI-98124801', againstInv: 'INV-1025', remarks: 'Full payment against invoice INV-1025' }
    ],

    payments: [
        { id: 'PAY-901', payNo: 'PAY-901', date: '2026-09-08', vendorName: 'Natural Herbs Pvt Ltd', amount: 88500, mode: 'HDFC Bank (NEFT)', refNo: 'NEFT-8812904', againstPur: 'INV-NH-402', remarks: 'Paid for Henna bulk purchase' }
    ],

    whatsapp: {
        status: 'Connected',
        phone: '+91 98290 12345',
        creditsUsed: 4825,
        totalCredits: 5000,
        templates: {
            invoice: "Dear {{customer_name}}, your invoice {{invoice_no}} of amount {{amount}} has been generated by Vikas Udhyog. Thank you for your business!",
            order: "Hello {{customer_name}}, your order {{order_no}} is confirmed and scheduled for dispatch on {{delivery_date}}.",
            dispatch: "Greetings from Vikas Udhyog! Order {{order_no}} has been dispatched via {{transporter}} (Vehicle: {{vehicle_no}}).",
            payment_rem: "Dear {{customer_name}}, this is a friendly reminder regarding outstanding balance of {{amount}}. Kindly process at earliest."
        }
    }
};

// Data Store Class
class DataStore {
    constructor() {
        this.init();
    }

    init() {
        // Initialize localStorage if keys don't exist
        for (const [key, storageKey] of Object.entries(STORAGE_KEYS)) {
            const dataKey = key.toLowerCase();
            // Convert camelCase or key mapping
            let initialVal = null;
            if (dataKey === 'current_company') initialVal = 'Vikas Udhyog';
            else if (dataKey === 'whatsapp') initialVal = INITIAL_DATA.whatsapp;
            else if (dataKey === 'purchase_orders') initialVal = INITIAL_DATA.purchaseOrders;
            else if (dataKey === 'sales_orders') initialVal = INITIAL_DATA.salesOrders;
            else if (dataKey === 'sales_invoices') initialVal = INITIAL_DATA.salesInvoices;
            else if (INITIAL_DATA[dataKey]) initialVal = INITIAL_DATA[dataKey];

            if (initialVal && !localStorage.getItem(storageKey)) {
                localStorage.setItem(storageKey, JSON.stringify(initialVal));
            }
        }
    }

    get(storageKey) {
        try {
            const val = localStorage.getItem(storageKey);
            return val ? JSON.parse(val) : [];
        } catch (e) {
            console.error("Error reading localStorage:", storageKey, e);
            return [];
        }
    }

    set(storageKey, data) {
        try {
            localStorage.setItem(storageKey, JSON.stringify(data));
        } catch (e) {
            console.error("Error writing localStorage:", storageKey, e);
        }
    }

    // Generic CRUD helper methods
    getAll(key) {
        return this.get(STORAGE_KEYS[key.toUpperCase()]);
    }

    saveItem(key, item) {
        const list = this.getAll(key);
        if (item.id) {
            const idx = list.findIndex(i => i.id === item.id);
            if (idx >= 0) list[idx] = item;
            else list.push(item);
        } else {
            item.id = key.substr(0, 3).toUpperCase() + '-' + Date.now().toString().substr(-5);
            list.unshift(item);
        }
        this.set(STORAGE_KEYS[key.toUpperCase()], list);
        return item;
    }

    deleteItem(key, id) {
        let list = this.getAll(key);
        list = list.filter(i => i.id !== id);
        this.set(STORAGE_KEYS[key.toUpperCase()], list);
    }

    // Business Logic Helper Methods
    adjustStock(itemId, qtyChange) {
        const items = this.getAll('ITEMS');
        const item = items.find(i => i.id === itemId);
        if (item) {
            item.stock = Math.max(0, (item.stock || 0) + qtyChange);
            this.set(STORAGE_KEYS.ITEMS, items);
        }
    }

    updateVendorBalance(vendorName, amountChange) {
        const vendors = this.getAll('VENDORS');
        const vendor = vendors.find(v => v.name === vendorName);
        if (vendor) {
            vendor.balance = Math.max(0, (vendor.balance || 0) + amountChange);
            this.set(STORAGE_KEYS.VENDORS, vendors);
        }
    }

    updateCustomerBalance(customerName, amountChange) {
        const customers = this.getAll('CUSTOMERS');
        const cust = customers.find(c => c.name === customerName);
        if (cust) {
            cust.balance = Math.max(0, (cust.balance || 0) + amountChange);
            this.set(STORAGE_KEYS.CUSTOMERS, customers);
        }
    }
}

const db = new DataStore();
