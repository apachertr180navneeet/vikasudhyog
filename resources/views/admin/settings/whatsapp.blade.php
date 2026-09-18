@extends('admin.layouts.app')

@section('title', 'WhatsApp Business API - VIKAS UDHYOG ERP')
@section('page_code', 'set-whatsapp')

@section('content')
<section class="view-section active" id="view-set-whatsapp">
                    <div class="page-header">
                        <div><h1 class="page-title">WhatsApp Business API Integration</h1><p class="page-subtitle">Automated customer SMS & WhatsApp notifications</p></div>
                        <button class="btn btn-secondary" onclick="SettingsModule.sendTestWhatsApp()"><i class="fa-brands fa-whatsapp"></i> Send Test Message</button>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1.5rem;">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Connection Status</h3></div>
                            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                                <div style="width: 14px; height: 14px; border-radius: 50%; background: var(--status-success);"></div>
                                <div><strong style="color: var(--dark);" id="wa-status">Connected</strong><br><span style="font-size: 0.8rem; color: var(--text-muted);">API Status Active</span></div>
                            </div>
                            <div style="margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 0.4rem;"><span>Credits Used</span><strong id="wa-credits">4,825 / 5,000</strong></div>
                                <div style="height: 8px; background: #E5E7EB; border-radius: 4px; overflow: hidden;"><div id="wa-credits-bar" style="height: 100%; background: var(--primary); width: 95%;"></div></div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header"><h3 class="card-title">Message Templates</h3></div>
                            <form onsubmit="SettingsModule.saveWhatsAppTemplates(event)">
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="form-label">Invoice Generated Template</label>
                                    <textarea id="wa-tpl-invoice" class="form-control"></textarea>
                                </div>
                                <div class="form-group" style="margin-bottom: 1rem;">
                                    <label class="form-label">Order Confirmation Template</label>
                                    <textarea id="wa-tpl-order" class="form-control"></textarea>
                                </div>
                                <div class="form-group" style="margin-bottom: 1.5rem;">
                                    <label class="form-label">Dispatch Notification Template</label>
                                    <textarea id="wa-tpl-dispatch" class="form-control"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save Templates</button>
                            </form>
                        </div>
                    </div>
                </section>
@endsection


