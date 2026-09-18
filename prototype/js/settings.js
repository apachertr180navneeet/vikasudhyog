/* ==========================================================================
   VIKAS UDHYOG ERP - Settings & WhatsApp API Prototype Controller
   ========================================================================== */

const SettingsModule = {
    render(viewId) {
        if (viewId === 'set-company') this.renderCompanySettings();
        if (viewId === 'set-whatsapp') this.renderWhatsAppSettings();
        if (viewId === 'set-backup') this.renderBackupSettings();
    },

    renderCompanySettings() {
        const c = db.getAll('COMPANIES')[0];
        if (c) {
            document.getElementById('set-comp-name').value = c.name;
            document.getElementById('set-comp-gstin').value = c.gstin;
            document.getElementById('set-comp-phone').value = c.phone;
            document.getElementById('set-comp-email').value = c.email;
            document.getElementById('set-comp-address').value = c.address;
        }
    },

    saveCompanySettings(e) {
        if (e) e.preventDefault();
        App.showToast('Company settings updated!', 'success');
    },

    renderWhatsAppSettings() {
        const wa = db.get(STORAGE_KEYS.WHATSAPP) || INITIAL_DATA.whatsapp;
        document.getElementById('wa-status').innerText = wa.status;
        document.getElementById('wa-credits').innerText = `${wa.creditsUsed.toLocaleString('en-IN')} / ${wa.totalCredits.toLocaleString('en-IN')}`;
        
        const meterPercent = (wa.creditsUsed / wa.totalCredits) * 100;
        document.getElementById('wa-credits-bar').style.width = `${meterPercent}%`;

        document.getElementById('wa-tpl-invoice').value = wa.templates.invoice;
        document.getElementById('wa-tpl-order').value = wa.templates.order;
        document.getElementById('wa-tpl-dispatch').value = wa.templates.dispatch;
    },

    saveWhatsAppTemplates(e) {
        if (e) e.preventDefault();
        const wa = db.get(STORAGE_KEYS.WHATSAPP) || INITIAL_DATA.whatsapp;
        wa.templates.invoice = document.getElementById('wa-tpl-invoice').value;
        wa.templates.order = document.getElementById('wa-tpl-order').value;
        wa.templates.dispatch = document.getElementById('wa-tpl-dispatch').value;

        db.set(STORAGE_KEYS.WHATSAPP, wa);
        App.showToast('WhatsApp Business templates updated successfully!', 'success');
    },

    sendTestWhatsApp() {
        const phone = prompt('Enter Mobile Number for WhatsApp Test Message:', '+91 98290 12345');
        if (phone) {
            App.showToast(`[WhatsApp API Simulation] Test message sent successfully to ${phone}`, 'success');
        }
    },

    renderBackupSettings() {
        // Ready for backup/restore
    },

    exportDataJSON() {
        const exportObj = {};
        for (const [key, storageKey] of Object.entries(STORAGE_KEYS)) {
            exportObj[key] = db.get(storageKey);
        }

        const dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportObj, null, 2));
        const dlAnchorElem = document.createElement('a');
        dlAnchorElem.setAttribute("href", dataStr);
        dlAnchorElem.setAttribute("download", `Vikas_Udhyog_ERP_Backup_${new Date().toISOString().split('T')[0]}.json`);
        document.body.appendChild(dlAnchorElem);
        dlAnchorElem.click();
        dlAnchorElem.remove();

        App.showToast('Full ERP Database Backup downloaded as JSON', 'success');
    },

    importDataJSON(inputEl) {
        const file = inputEl.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const importedData = JSON.parse(e.target.result);
                for (const [key, storageKey] of Object.entries(STORAGE_KEYS)) {
                    if (importedData[key]) {
                        db.set(storageKey, importedData[key]);
                    }
                }
                App.showToast('ERP Database restored successfully! Reloading view...', 'success');
                setTimeout(() => window.location.reload(), 1000);
            } catch (err) {
                App.showToast('Invalid JSON Backup file format', 'danger');
            }
        };
        reader.readAsText(file);
    }
};
