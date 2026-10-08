@extends('admin.layouts.app')

@section('title', 'WhatsApp Business API - VIKAS UDHYOG ERP')
@section('page_code', 'set-whatsapp')

@section('content')
<section class="view-section active" id="view-set-whatsapp">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Settings</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">WhatsApp Business API</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-brands fa-whatsapp" style="color: #25D366;"></i> WhatsApp Business API &amp; Automated Dispatch
            </h1>
            <p class="erp-page-subtitle">
                Configure Meta Cloud Graph API, automated billing &amp; dispatch alerts, custom notification templates, and live message simulation.
            </p>
        </div>

        <div class="erp-header-actions">
            <button type="button" class="btn btn-outline" id="btn-ping-gateway" onclick="pingWhatsAppConnection()" title="Test API Handshake">
                <i class="fa-solid fa-satellite-dish me-1"></i> <span id="ping-btn-text">Ping Gateway</span>
            </button>
            <button type="button" class="btn btn-outline" onclick="openTestModal()" style="border-color: #25D366; color: #15803D;" title="Send Test WhatsApp">
                <i class="fa-brands fa-whatsapp me-1"></i> Quick Test
            </button>
            <button type="submit" form="whatsapp-settings-form" class="btn btn-primary erp-btn-header-primary">
                <i class="fa-solid fa-floppy-disk me-1"></i> Save Settings
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert erp-alert-success" style="margin-bottom: 1.5rem;">
            <div class="erp-alert-content">
                <i class="fa-solid fa-circle-check erp-alert-icon-success"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 5px solid #EF4444; padding: 1.1rem 1.35rem; border-radius: 14px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08); display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 0.6rem;">
                <i class="fa-solid fa-circle-exclamation" style="font-size: 1.1rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger" style="background: #FEF2F2; border: 1px solid #FECACA; border-left: 5px solid #EF4444; padding: 1.1rem 1.35rem; border-radius: 14px; color: #991B1B; font-size: 0.88rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.08);">
            <div style="font-weight: 700; margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.5rem; font-size: 0.95rem;">
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem;"></i> Validation Errors Found
            </div>
            <ul style="margin: 0; padding-left: 1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 4-Card KPI Statistics & Quota Ribbon -->
    <div class="erp-kpi-grid" style="margin-bottom: 1.5rem;">
        <!-- Card 1: API Status -->
        <div class="card erp-kpi-card" style="border-left: 4px solid {{ $stats['status_color'] }};">
            <div class="erp-kpi-icon-box" style="background: rgba(37, 211, 102, 0.12); color: #15803D;">
                <i class="fa-brands fa-whatsapp"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Gateway Status</div>
                <div class="erp-kpi-val" style="color: {{ $stats['status_color'] }}; display: flex; align-items: center; gap: 0.4rem; font-size: 1.3rem;">
                    <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background: {{ $stats['status_color'] }};"></span>
                    {{ $stats['status'] }}
                </div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    Provider: {{ strtoupper(str_replace('_', ' ', $settings->provider)) }}
                </div>
            </div>
        </div>

        <!-- Card 2: Monthly Quota Meter -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-gauge-high"></i>
            </div>
            <div style="width: 100%;">
                <div class="erp-kpi-label">Monthly Quota Meter</div>
                <div class="erp-kpi-val" style="font-size: 1.2rem;">
                    {{ number_format($stats['credits_used']) }} <span style="font-size: 0.8rem; font-weight: 500; color: #64748B;">/ {{ number_format($stats['monthly_quota']) }}</span>
                </div>
                <div style="margin-top: 6px; width: 100%; height: 6px; background: #E2E8F0; border-radius: 999px; overflow: hidden;">
                    <div style="height: 100%; width: {{ $stats['quota_percentage'] }}%; background: #5B841E; border-radius: 999px;"></div>
                </div>
                <div style="font-size: 0.72rem; color: #64748B; margin-top: 3px; display: flex; justify-content: space-between;">
                    <span>{{ $stats['quota_percentage'] }}% Utilized</span>
                    <span>{{ number_format($stats['credits_remaining']) }} Left</span>
                </div>
            </div>
        </div>

        <!-- Card 3: Outgoing Deliveries -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-paper-plane"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Dispatched Alerts</div>
                <div class="erp-kpi-val erp-kpi-val-success">{{ $stats['total_sent'] }}</div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    Success Rate: <strong>{{ $stats['delivery_rate'] }}%</strong>
                </div>
            </div>
        </div>

        <!-- Card 4: Active Triggers -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-bolt-lightning"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Automated Triggers</div>
                <div class="erp-kpi-val" style="color: #7C3AED;">{{ $stats['active_triggers'] }} / 5 Active</div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    Invoice, Dispatch, Receipt
                </div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Form Layout -->
    <form action="{{ route('admin.settings.whatsapp.update') }}" method="POST" id="whatsapp-settings-form">
        @csrf
        @method('PUT')

        <div class="form-grid-layout" style="display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; align-items: start;">
            
            <!-- Left Main Content Column -->
            <div class="form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- 1. Meta Cloud API Credentials & Connection Setup -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9; flex-wrap: wrap; gap: 0.75rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(37, 211, 102, 0.12); color: #15803D; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">1. Meta Cloud API Gateway Configuration</h3>
                                <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Official Meta Graph API v19.0 credentials &amp; registered number</p>
                            </div>
                        </div>

                        <!-- Global Master Switch & Sandbox Switch -->
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; cursor: pointer; font-size: 0.82rem; font-weight: 600; color: #475569;">
                                <input type="checkbox" name="sandbox_mode" id="toggle-sandbox" value="1" {{ $settings->sandbox_mode ? 'checked' : '' }} style="width: 17px; height: 17px; accent-color: #F59E0B; cursor: pointer;">
                                <span class="badge" style="background: #FEF3C7; color: #92400E; font-size: 0.75rem; padding: 4px 8px; border-radius: 6px;">
                                    <i class="fa-solid fa-flask me-1"></i> Sandbox Mode
                                </span>
                            </label>

                            <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; cursor: pointer; font-size: 0.82rem; font-weight: 600; color: #475569;">
                                <input type="checkbox" name="is_enabled" value="1" {{ $settings->is_enabled ? 'checked' : '' }} style="width: 17px; height: 17px; accent-color: #5B841E; cursor: pointer;">
                                <span>Engine Active</span>
                            </label>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.15rem;">
                        <!-- Gateway Provider -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Gateway Provider <span style="color: #DC2626;">*</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-network-wired" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <select name="provider" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" required>
                                    <option value="meta_cloud_api" {{ $settings->provider === 'meta_cloud_api' ? 'selected' : '' }}>Meta Cloud API (Official Graph API)</option>
                                    <option value="twilio" {{ $settings->provider === 'twilio' ? 'selected' : '' }}>Twilio WhatsApp Gateway</option>
                                    <option value="gupshup" {{ $settings->provider === 'gupshup' ? 'selected' : '' }}>Gupshup Enterprise Messaging</option>
                                    <option value="sandbox" {{ $settings->provider === 'sandbox' ? 'selected' : '' }}>Simulator Only (Local Development)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Registered WhatsApp Display Number -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Sender Business Number <span style="color: #DC2626;">*</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-brands fa-whatsapp" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #15803D; font-size: 1rem;"></i>
                                <input type="text" name="display_phone_number" class="form-control" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="Enter business phone number" value="{{ old('display_phone_number', $settings->display_phone_number) }}" required>
                            </div>
                        </div>

                        <!-- Phone Number ID -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                Meta Phone Number ID
                                <span style="font-size: 0.73rem; font-weight: 500; color: #64748B;">(From Meta Developer Portal)</span>
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-hashtag" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <input type="text" name="phone_number_id" class="form-control font-monospace" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="Enter phone number ID" value="{{ old('phone_number_id', $settings->phone_number_id) }}">
                            </div>
                        </div>

                        <!-- WhatsApp Business Account ID (WABA ID) -->
                        <div class="form-group">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: block;">
                                WhatsApp Business Account ID (WABA ID)
                            </label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-id-badge" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                                <input type="text" name="waba_id" class="form-control font-monospace" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="Enter WABA ID" value="{{ old('waba_id', $settings->waba_id) }}">
                            </div>
                        </div>
                    </div>

                    <!-- Permanent System User Access Token -->
                    <div class="form-group" style="margin-top: 1.15rem;">
                        <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin-bottom: 0.4rem; display: flex; justify-content: space-between;">
                            <span>Permanent System User Access Token <span style="color: #64748B; font-weight: 500;">(EAAG...)</span></span>
                            <span style="font-size: 0.74rem; font-weight: 600; color: #5B841E; cursor: pointer;" onclick="toggleTokenVisibility()">
                                <i class="fa-solid fa-eye" id="token-eye-icon"></i> <span id="token-eye-label">Show Token</span>
                            </span>
                        </label>
                        <div class="erp-field-icon-wrap" style="position: relative;">
                            <i class="fa-solid fa-key" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.95rem;"></i>
                            <input type="password" name="access_token" id="field-access-token" class="form-control font-monospace" style="padding-left: 2.75rem; height: 44px; border-radius: 10px;" placeholder="Enter Meta System User Token" value="{{ old('access_token', $settings->access_token) }}">
                        </div>
                        <span style="font-size: 0.73rem; color: #64748B; margin-top: 4px; display: block;">
                            <i class="fa-solid fa-shield-halved me-1"></i> Kept securely encrypted. Required permissions: <code>whatsapp_business_messaging</code>, <code>whatsapp_business_management</code>.
                        </span>
                    </div>

                    <!-- Webhook Endpoint & Verify Token -->
                    <div style="margin-top: 1.25rem; padding: 1rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 0.65rem; display: flex; align-items: center; gap: 0.4rem;">
                            <i class="fa-solid fa-webhook text-primary"></i> Meta Webhook Delivery Handshake &amp; Callbacks
                        </div>
                        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                            <div>
                                <label style="font-size: 0.76rem; font-weight: 600; color: #64748B; margin-bottom: 0.2rem; display: block;">Webhook Callback URL</label>
                                <div style="display: flex; gap: 6px;">
                                    <input type="text" id="webhook-url-input" class="form-control font-monospace" style="height: 38px; font-size: 0.8rem; background: #FFFFFF;" value="{{ route('admin.settings.whatsapp.webhook') }}" readonly>
                                    <button type="button" class="btn btn-sm btn-outline" onclick="copyWebhookUrl()" title="Copy URL" style="height: 38px; padding: 0 12px;">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label style="font-size: 0.76rem; font-weight: 600; color: #64748B; margin-bottom: 0.2rem; display: block;">Webhook Verify Token</label>
                                <input type="text" name="webhook_verify_token" class="form-control font-monospace" style="height: 38px; font-size: 0.8rem; background: #FFFFFF;" placeholder="Enter verify token" value="{{ old('webhook_verify_token', $settings->webhook_verify_token) }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Automation Trigger Rules -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(124, 58, 237, 0.1); color: #7C3AED; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                            <i class="fa-solid fa-wand-magic-sparkles"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">2. Automated Notification Triggers</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Define transactional events that automatically dispatch WhatsApp alerts</p>
                        </div>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                        <!-- Trigger 1: Invoice Generated -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.9rem 1.15rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.88rem; color: #1E293B;">Sales Tax Invoice &amp; WB Sales Billing</div>
                                    <div style="font-size: 0.74rem; color: #64748B;">Auto-send invoice summary &amp; PDF download link when a sales bill is saved</div>
                                </div>
                            </div>
                            <label style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="auto_send_invoice" value="1" {{ $settings->auto_send_invoice ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" class="erp-toggle-input">
                                <span class="erp-toggle-slider" style="position: absolute; inset: 0; background-color: #CBD5E1; border-radius: 24px; transition: 0.3s;"></span>
                            </label>
                        </div>

                        <!-- Trigger 2: Order Confirmation -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.9rem 1.15rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(59, 130, 246, 0.12); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.88rem; color: #1E293B;">Order Booking &amp; Confirmation Alert</div>
                                    <div style="font-size: 0.74rem; color: #64748B;">Send instant order confirmation with expected processing and dispatch dates</div>
                                </div>
                            </div>
                            <label style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="auto_send_order" value="1" {{ $settings->auto_send_order ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" class="erp-toggle-input">
                                <span class="erp-toggle-slider" style="position: absolute; inset: 0; background-color: #CBD5E1; border-radius: 24px; transition: 0.3s;"></span>
                            </label>
                        </div>

                        <!-- Trigger 3: Factory Dispatch & Bilty -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.9rem 1.15rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(245, 158, 11, 0.12); color: #D97706; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                    <i class="fa-solid fa-truck-fast"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.88rem; color: #1E293B;">Consignment Dispatch &amp; Transporter Alert</div>
                                    <div style="font-size: 0.74rem; color: #64748B;">Send transporter name, vehicle registration number, and LR/Bilty tracking number</div>
                                </div>
                            </div>
                            <label style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="auto_send_dispatch" value="1" {{ $settings->auto_send_dispatch ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" class="erp-toggle-input">
                                <span class="erp-toggle-slider" style="position: absolute; inset: 0; background-color: #CBD5E1; border-radius: 24px; transition: 0.3s;"></span>
                            </label>
                        </div>

                        <!-- Trigger 4: Payment Receipt -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.9rem 1.15rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(16, 185, 129, 0.12); color: #059669; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                    <i class="fa-solid fa-receipt"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.88rem; color: #1E293B;">Receipt Voucher &amp; Payment Acknowledgment</div>
                                    <div style="font-size: 0.74rem; color: #64748B;">Acknowledge received payment and inform current outstanding account balance</div>
                                </div>
                            </div>
                            <label style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="auto_send_receipt" value="1" {{ $settings->auto_send_receipt ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" class="erp-toggle-input">
                                <span class="erp-toggle-slider" style="position: absolute; inset: 0; background-color: #CBD5E1; border-radius: 24px; transition: 0.3s;"></span>
                            </label>
                        </div>

                        <!-- Trigger 5: Due Balance Reminder -->
                        <div style="display: flex; align-items: center; justify-content: space-between; padding: 0.9rem 1.15rem; background: #F8FAFC; border-radius: 12px; border: 1px solid #E2E8F0;">
                            <div style="display: flex; align-items: center; gap: 0.85rem;">
                                <div style="width: 34px; height: 34px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </div>
                                <div>
                                    <div style="font-weight: 700; font-size: 0.88rem; color: #1E293B;">Overdue Balance Follow-up Notification</div>
                                    <div style="font-size: 0.74rem; color: #64748B;">Send friendly balance settlement reminder with company UPI ID for quick pay</div>
                                </div>
                            </div>
                            <label style="position: relative; display: inline-block; width: 44px; height: 24px; margin: 0; cursor: pointer;">
                                <input type="checkbox" name="auto_send_due_reminder" value="1" {{ $settings->auto_send_due_reminder ? 'checked' : '' }} style="opacity: 0; width: 0; height: 0;" class="erp-toggle-input">
                                <span class="erp-toggle-slider" style="position: absolute; inset: 0; background-color: #CBD5E1; border-radius: 24px; transition: 0.3s;"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- 3. Message Templates Customization -->
                <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(14, 165, 233, 0.1); color: #0284C7; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                                <i class="fa-solid fa-message"></i>
                            </div>
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">3. Notification Message Templates</h3>
                                <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Customize message text and placeholder variables. Updates phone preview instantly.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Template Tabs -->
                    <div style="display: flex; gap: 0.4rem; margin-bottom: 1.25rem; border-bottom: 2px solid #E2E8F0; padding-bottom: 0px; overflow-x: auto;">
                        <button type="button" class="wa-tab-btn active" data-tab="tab-invoice" onclick="switchTemplateTab('tab-invoice', 'invoice')">
                            <i class="fa-solid fa-file-invoice me-1"></i> Tax Invoice
                        </button>
                        <button type="button" class="wa-tab-btn" data-tab="tab-order" onclick="switchTemplateTab('tab-order', 'order')">
                            <i class="fa-solid fa-cart-shopping me-1"></i> Order Booking
                        </button>
                        <button type="button" class="wa-tab-btn" data-tab="tab-dispatch" onclick="switchTemplateTab('tab-dispatch', 'dispatch')">
                            <i class="fa-solid fa-truck-fast me-1"></i> Consignment Dispatch
                        </button>
                        <button type="button" class="wa-tab-btn" data-tab="tab-receipt" onclick="switchTemplateTab('tab-receipt', 'receipt')">
                            <i class="fa-solid fa-receipt me-1"></i> Payment Receipt
                        </button>
                        <button type="button" class="wa-tab-btn" data-tab="tab-due" onclick="switchTemplateTab('tab-due', 'due_reminder')">
                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Due Reminder
                        </button>
                    </div>

                    <!-- Tab 1: Invoice Template -->
                    <div class="wa-tab-content" id="tab-invoice" style="display: block;">
                        <div style="margin-bottom: 0.6rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin: 0;">
                                Invoice Generated Message Body
                            </label>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; color: #64748B; align-self: center; margin-right: 4px;">Click to insert:</span>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-invoice', '@{{customer_name}}')">{customer_name}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-invoice', '@{{invoice_no}}')">{invoice_no}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-invoice', '@{{amount}}')">{amount}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-invoice', '@{{date}}')">{date}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-invoice', '@{{company_name}}')">{company_name}</button>
                            </div>
                        </div>
                        <textarea name="invoice_template" id="tpl-invoice" class="form-control font-monospace" rows="6" style="border-radius: 10px; font-size: 0.85rem; line-height: 1.5; padding: 0.85rem;" placeholder="Enter message template text" oninput="updateLivePreview('invoice')">{{ old('invoice_template', $settings->invoice_template) }}</textarea>
                    </div>

                    <!-- Tab 2: Order Booking Template -->
                    <div class="wa-tab-content" id="tab-order" style="display: none;">
                        <div style="margin-bottom: 0.6rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin: 0;">
                                Order Confirmation Message Body
                            </label>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; color: #64748B; align-self: center; margin-right: 4px;">Click to insert:</span>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-order', '@{{customer_name}}')">{customer_name}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-order', '@{{order_no}}')">{order_no}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-order', '@{{items_summary}}')">{items_summary}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-order', '@{{delivery_date}}')">{delivery_date}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-order', '@{{company_name}}')">{company_name}</button>
                            </div>
                        </div>
                        <textarea name="order_template" id="tpl-order" class="form-control font-monospace" rows="6" style="border-radius: 10px; font-size: 0.85rem; line-height: 1.5; padding: 0.85rem;" placeholder="Enter message template text" oninput="updateLivePreview('order')">{{ old('order_template', $settings->order_template) }}</textarea>
                    </div>

                    <!-- Tab 3: Consignment Dispatch Template -->
                    <div class="wa-tab-content" id="tab-dispatch" style="display: none;">
                        <div style="margin-bottom: 0.6rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin: 0;">
                                Consignment Dispatch Message Body
                            </label>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; color: #64748B; align-self: center; margin-right: 4px;">Click to insert:</span>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-dispatch', '@{{customer_name}}')">{customer_name}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-dispatch', '@{{order_no}}')">{order_no}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-dispatch', '@{{transporter}}')">{transporter}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-dispatch', '@{{vehicle_no}}')">{vehicle_no}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-dispatch', '@{{bilty_no}}')">{bilty_no}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-dispatch', '@{{driver_phone}}')">{driver_phone}</button>
                            </div>
                        </div>
                        <textarea name="dispatch_template" id="tpl-dispatch" class="form-control font-monospace" rows="6" style="border-radius: 10px; font-size: 0.85rem; line-height: 1.5; padding: 0.85rem;" placeholder="Enter message template text" oninput="updateLivePreview('dispatch')">{{ old('dispatch_template', $settings->dispatch_template) }}</textarea>
                    </div>

                    <!-- Tab 4: Payment Receipt Template -->
                    <div class="wa-tab-content" id="tab-receipt" style="display: none;">
                        <div style="margin-bottom: 0.6rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin: 0;">
                                Payment Receipt Message Body
                            </label>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; color: #64748B; align-self: center; margin-right: 4px;">Click to insert:</span>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-receipt', '@{{party_name}}')">{party_name}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-receipt', '@{{amount}}')">{amount}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-receipt', '@{{payment_mode}}')">{payment_mode}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-receipt', '@{{voucher_no}}')">{voucher_no}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-receipt', '@{{balance}}')">{balance}</button>
                            </div>
                        </div>
                        <textarea name="receipt_template" id="tpl-receipt" class="form-control font-monospace" rows="6" style="border-radius: 10px; font-size: 0.85rem; line-height: 1.5; padding: 0.85rem;" placeholder="Enter message template text" oninput="updateLivePreview('receipt')">{{ old('receipt_template', $settings->receipt_template) }}</textarea>
                    </div>

                    <!-- Tab 5: Overdue Reminder Template -->
                    <div class="wa-tab-content" id="tab-due" style="display: none;">
                        <div style="margin-bottom: 0.6rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                            <label class="form-label" style="font-weight: 700; font-size: 0.84rem; color: #1E293B; margin: 0;">
                                Overdue Balance Reminder Body
                            </label>
                            <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                <span style="font-size: 0.72rem; color: #64748B; align-self: center; margin-right: 4px;">Click to insert:</span>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-due', '@{{customer_name}}')">{customer_name}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-due', '@{{due_amount}}')">{due_amount}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-due', '@{{due_date}}')">{due_date}</button>
                                <button type="button" class="wa-chip-btn" onclick="insertPlaceholder('tpl-due', '@{{bank_upi}}')">{bank_upi}</button>
                            </div>
                        </div>
                        <textarea name="due_reminder_template" id="tpl-due" class="form-control font-monospace" rows="6" style="border-radius: 10px; font-size: 0.85rem; line-height: 1.5; padding: 0.85rem;" placeholder="Enter message template text" oninput="updateLivePreview('due_reminder')">{{ old('due_reminder_template', $settings->due_reminder_template) }}</textarea>
                    </div>
                </div>

                <!-- 4. Outgoing Message Audit Trail & Delivery Log -->
                <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); background: #FFFFFF;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <i class="fa-solid fa-clock-rotate-left text-primary" style="font-size: 1rem;"></i>
                            <h3 style="font-size: 0.95rem; font-weight: 700; color: #1E293B; margin: 0;">Recent Outgoing WhatsApp Dispatches (Audit Trail)</h3>
                        </div>
                        @if($logs->count() > 0)
                            <form action="{{ route('admin.settings.whatsapp.clear-logs') }}" method="POST" onsubmit="return confirm('Clear recent dispatch logs?');" style="margin: 0;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline" style="font-size: 0.75rem; color: #94A3B8; border-color: #E2E8F0; padding: 3px 10px;">
                                    <i class="fa-solid fa-trash-can me-1"></i> Clear Logs
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table erp-table" style="width: 100%; margin: 0; border-collapse: collapse;">
                            <thead>
                                <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                                    <th style="padding: 0.85rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Time</th>
                                    <th style="padding: 0.85rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Recipient Phone</th>
                                    <th style="padding: 0.85rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Template</th>
                                    <th style="padding: 0.85rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Message Snippet</th>
                                    <th style="padding: 0.85rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Delivery Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($logs as $log)
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td style="padding: 0.85rem 1.15rem; font-size: 0.8rem; color: #64748B; white-space: nowrap;">
                                            {{ $log->created_at->format('d M, h:i A') }}
                                        </td>
                                        <td style="padding: 0.85rem 1.15rem; font-size: 0.85rem; font-weight: 700; color: #1E293B; white-space: nowrap;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fa-brands fa-whatsapp text-success"></i>
                                                <span class="font-monospace">{{ $log->recipient_phone }}</span>
                                            </div>
                                            @if($log->recipient_name && $log->recipient_name !== 'Recipient')
                                                <div style="font-size: 0.72rem; color: #64748B; font-weight: 500;">{{ $log->recipient_name }}</div>
                                            @endif
                                        </td>
                                        <td style="padding: 0.85rem 1.15rem; font-size: 0.8rem;">
                                            <span class="badge" style="background: #F1F5F9; color: #475569; font-weight: 600; padding: 4px 8px; border-radius: 6px;">
                                                {{ ucfirst(str_replace('_', ' ', $log->template_type)) }}
                                            </span>
                                        </td>
                                        <td style="padding: 0.85rem 1.15rem; font-size: 0.8rem; color: #475569; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ Str::limit($log->message_body, 70) }}
                                        </td>
                                        <td style="padding: 0.85rem 1.15rem; text-align: center;">
                                            @if($log->status === 'delivered' || $log->status === 'read')
                                                <span class="badge" style="background: #DCFCE7; color: #15803D; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                                    <i class="fa-solid fa-check-double me-1" style="color: #34B7F1;"></i> Delivered
                                                </span>
                                            @elseif($log->status === 'sent')
                                                <span class="badge" style="background: #E0E7FF; color: #3730A3; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                                    <i class="fa-solid fa-check me-1"></i> Sent
                                                </span>
                                            @elseif($log->status === 'simulated')
                                                <span class="badge" style="background: #FEF3C7; color: #92400E; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                                    <i class="fa-solid fa-flask me-1"></i> Simulated
                                                </span>
                                            @else
                                                <span class="badge" style="background: #FEE2E2; color: #991B1B; padding: 4px 10px; border-radius: 999px; font-size: 0.75rem; font-weight: 700;">
                                                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Failed
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2.5rem 1rem; color: #94A3B8;">
                                            <div style="font-size: 2.2rem; margin-bottom: 0.5rem; color: #CBD5E1;"><i class="fa-brands fa-whatsapp"></i></div>
                                            <div style="font-weight: 700; color: #64748B; font-size: 0.95rem;">No Outgoing WhatsApp Logs Found</div>
                                            <div style="font-size: 0.78rem;">Use the quick test widget on the right to send an alert or test dispatch.</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Right Sidebar Column: Live Smartphone Preview & Quick Test Widget -->
            <div class="form-sidebar-col" style="display: flex; flex-direction: column; gap: 1.5rem; position: sticky; top: 1.5rem;">
                
                <!-- Live WhatsApp Smartphone Chat Preview Card -->
                <div class="card erp-preview-card" style="padding: 0; border-radius: 20px; overflow: hidden; border: 1px solid #CBD5E1; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.12); background: #EFEAE2;">
                    
                    <!-- WhatsApp Header (Dark Teal Green) -->
                    <div style="background: #075E54; padding: 0.75rem 1rem; color: #FFFFFF; display: flex; align-items: center; justify-content: space-between;">
                        <div style="display: flex; align-items: center; gap: 0.6rem;">
                            <i class="fa-solid fa-arrow-left" style="font-size: 0.85rem; opacity: 0.85;"></i>
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #5B841E, #3D5A12); color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.85rem; border: 1.5px solid rgba(255,255,255,0.4);">
                                VU
                            </div>
                            <div>
                                <div style="font-size: 0.88rem; font-weight: 700; display: flex; align-items: center; gap: 0.35rem; line-height: 1.1;">
                                    <span>{{ $company->name ?? 'Vikas Udhyog' }}</span>
                                    <i class="fa-solid fa-circle-check" style="color: #25D366; font-size: 0.8rem;" title="Verified Business Account"></i>
                                </div>
                                <span style="font-size: 0.68rem; opacity: 0.85;">Official Business Account &bull; Online</span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 0.85rem; opacity: 0.85; font-size: 0.9rem;">
                            <i class="fa-solid fa-phone"></i>
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </div>
                    </div>

                    <!-- Template Switcher Pills on Phone Screen -->
                    <div style="background: rgba(255,255,255,0.7); backdrop-filter: blur(4px); padding: 0.4rem 0.6rem; display: flex; gap: 4px; overflow-x: auto; border-bottom: 1px solid rgba(0,0,0,0.06);">
                        <button type="button" class="wa-phone-pill active" onclick="switchPreviewTemplate('invoice')">Invoice</button>
                        <button type="button" class="wa-phone-pill" onclick="switchPreviewTemplate('order')">Order</button>
                        <button type="button" class="wa-phone-pill" onclick="switchPreviewTemplate('dispatch')">Dispatch</button>
                        <button type="button" class="wa-phone-pill" onclick="switchPreviewTemplate('receipt')">Receipt</button>
                        <button type="button" class="wa-phone-pill" onclick="switchPreviewTemplate('due_reminder')">Due</button>
                    </div>

                    <!-- Chat Body Screen with WhatsApp Wallpaper Background -->
                    <div style="padding: 1.25rem 1rem; min-height: 290px; max-height: 420px; overflow-y: auto; background-color: #EFEAE2; background-image: radial-gradient(#D1D7DB 1px, transparent 1px); background-size: 16px 16px;">
                        
                        <!-- Encryption Notice Bubble -->
                        <div style="text-align: center; margin-bottom: 1rem;">
                            <span style="background: rgba(255,255,255,0.85); padding: 4px 10px; border-radius: 6px; font-size: 0.65rem; color: #54656F; box-shadow: 0 1px 2px rgba(0,0,0,0.06); display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fa-solid fa-lock" style="font-size: 0.6rem;"></i> Messages are end-to-end encrypted
                            </span>
                        </div>

                        <!-- Date Badge -->
                        <div style="text-align: center; margin-bottom: 0.85rem;">
                            <span style="background: #E1F3FB; padding: 2px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 600; color: #1E293B;">
                                TODAY
                            </span>
                        </div>

                        <!-- Sent WhatsApp Chat Bubble (Light Green) -->
                        <div style="display: flex; justify-content: flex-end; margin-bottom: 0.5rem;">
                            <div style="background: #E7FCE3; border-radius: 10px 10px 2px 10px; padding: 0.65rem 0.85rem; max-width: 90%; box-shadow: 0 1px 3px rgba(0,0,0,0.12); position: relative;">
                                
                                <!-- Dynamic Rendered Message Text -->
                                <div id="wa-preview-text" style="font-size: 0.82rem; color: #111B21; line-height: 1.45; white-space: pre-wrap; word-break: break-word;">
                                    Loading preview...
                                </div>

                                <!-- Timestamp and Blue Double Checkmarks -->
                                <div style="display: flex; align-items: center; justify-content: flex-end; gap: 4px; margin-top: 4px; font-size: 0.66rem; color: #667781;">
                                    <span id="wa-preview-time">10:45 AM</span>
                                    <i class="fa-solid fa-check-double" style="color: #34B7F1; font-size: 0.72rem;" title="Read Receipt"></i>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Mock Input Footer -->
                    <div style="background: #F0F2F5; padding: 0.6rem 0.75rem; display: flex; align-items: center; gap: 0.5rem; border-top: 1px solid #E2E8F0;">
                        <i class="fa-regular fa-face-smile" style="color: #54656F; font-size: 1.1rem;"></i>
                        <i class="fa-solid fa-paperclip" style="color: #54656F; font-size: 1rem;"></i>
                        <div style="flex: 1; background: #FFFFFF; border-radius: 20px; padding: 6px 12px; font-size: 0.78rem; color: #8696A0; border: 1px solid #E2E8F0;">
                            Type a message...
                        </div>
                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #00A884; color: #FFFFFF; display: flex; align-items: center; justify-content: center; font-size: 0.85rem;">
                            <i class="fa-solid fa-microphone"></i>
                        </div>
                    </div>

                </div>

                <!-- Instant Live Test Dispatch Card -->
                <div class="card erp-form-section-card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.85rem; padding-bottom: 0.65rem; border-bottom: 1px solid #F1F5F9;">
                        <i class="fa-solid fa-paper-plane" style="color: #25D366; font-size: 1rem;"></i>
                        <h4 style="font-size: 0.92rem; font-weight: 700; color: #1E293B; margin: 0;">Instant Test Dispatcher</h4>
                    </div>

                    <form id="wa-test-form" onsubmit="handleSendTestMessage(event)">
                        <div class="form-group" style="margin-bottom: 0.85rem;">
                            <label style="font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem; display: block;">Recipient Mobile Number</label>
                            <div class="erp-field-icon-wrap" style="position: relative;">
                                <i class="fa-solid fa-phone" style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94A3B8; font-size: 0.85rem;"></i>
                                <input type="text" id="test-phone-input" class="form-control" style="padding-left: 2.2rem; height: 38px; border-radius: 8px; font-size: 0.84rem;" placeholder="Enter mobile number" value="+91 98290 12345" required>
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label style="font-size: 0.76rem; font-weight: 700; color: #475569; margin-bottom: 0.3rem; display: block;">Notification Template to Test</label>
                            <select id="test-template-select" class="form-control" style="height: 38px; border-radius: 8px; font-size: 0.84rem;">
                                <option value="invoice">Tax Invoice Alert</option>
                                <option value="order">Order Confirmation</option>
                                <option value="dispatch">Consignment Dispatch</option>
                                <option value="receipt">Payment Receipt</option>
                                <option value="due_reminder">Due Balance Reminder</option>
                            </select>
                        </div>

                        <button type="submit" id="btn-send-test" class="btn btn-primary" style="width: 100%; height: 40px; border-radius: 8px; background: #15803D; border-color: #15803D; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                            <i class="fa-brands fa-whatsapp"></i> <span id="test-btn-text">Send WhatsApp Alert</span>
                        </button>
                    </form>
                    <div id="test-result-box" style="margin-top: 0.75rem; display: none;"></div>
                </div>

                <!-- Diagnostics & Quick Links Card -->
                <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #F8FAFC;">
                    <div style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fa-solid fa-circle-nodes text-primary"></i> Gateway Diagnostics
                    </div>
                    <ul style="margin: 0; padding-left: 1.2rem; font-size: 0.78rem; color: #64748B; line-height: 1.6;">
                        <li><strong>Graph API Version:</strong> v19.0</li>
                        <li><strong>SSL Webhook:</strong> Enforced HTTPS</li>
                        <li><strong>Sandbox Mode:</strong> {{ $settings->sandbox_mode ? 'Enabled (Simulating)' : 'Live' }}</li>
                    </ul>
                    <div style="margin-top: 1rem; padding-top: 0.75rem; border-top: 1px solid #E2E8F0; display: flex; flex-direction: column; gap: 0.4rem;">
                        <a href="{{ route('admin.settings.company') }}" class="btn btn-sm btn-outline" style="font-size: 0.78rem; justify-content: flex-start;">
                            <i class="fa-solid fa-building me-1"></i> Company Profile &amp; Letterhead
                        </a>
                        <a href="{{ route('admin.settings.backup-restore') }}" class="btn btn-sm btn-outline" style="font-size: 0.78rem; justify-content: flex-start;">
                            <i class="fa-solid fa-database me-1"></i> Database Backup &amp; Restore
                        </a>
                    </div>
                </div>

            </div>

        </div>
    </form>

</section>

<!-- Additional Custom Styles for WhatsApp Page -->
<style>
/* WhatsApp Tab Buttons */
.wa-tab-btn {
    background: transparent;
    border: none;
    padding: 0.6rem 1rem;
    font-size: 0.82rem;
    font-weight: 600;
    color: #64748B;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.wa-tab-btn:hover {
    color: #5B841E;
}
.wa-tab-btn.active {
    color: #5B841E;
    border-bottom-color: #5B841E;
    font-weight: 700;
}

/* Variable Insertion Chips */
.wa-chip-btn {
    background: #F1F5F9;
    color: #475569;
    border: 1px solid #E2E8F0;
    border-radius: 6px;
    font-size: 0.7rem;
    font-weight: 600;
    font-family: Consolas, monospace;
    padding: 2px 7px;
    cursor: pointer;
    transition: all 0.15s ease;
}
.wa-chip-btn:hover {
    background: #5B841E;
    color: #FFFFFF;
    border-color: #5B841E;
}

/* Phone Screen Template Switcher Pills */
.wa-phone-pill {
    background: rgba(255,255,255,0.9);
    border: 1px solid #CBD5E1;
    border-radius: 999px;
    padding: 2px 9px;
    font-size: 0.68rem;
    font-weight: 600;
    color: #475569;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.15s ease;
}
.wa-phone-pill:hover, .wa-phone-pill.active {
    background: #075E54;
    color: #FFFFFF;
    border-color: #075E54;
}

/* Toggle Switch Slider Customization */
.erp-toggle-input:checked + .erp-toggle-slider {
    background-color: #5B841E !important;
}
.erp-toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    border-radius: 50%;
    transition: 0.3s;
}
.erp-toggle-input:checked + .erp-toggle-slider:before {
    transform: translateX(20px);
}
</style>

<!-- Live Keystroke Listeners and Dynamic Interaction Script -->
<script>
// Sample variables supplied from backend controller
const SAMPLE_VARIABLES = @json($sampleVariables);

let currentPreviewTemplate = 'invoice';

// Initialize live preview on DOM load
document.addEventListener('DOMContentLoaded', function() {
    updateLivePreview('invoice');
});

// Switch active template tab in editor
function switchTemplateTab(tabId, tplType) {
    document.querySelectorAll('.wa-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.wa-tab-btn').forEach(btn => btn.classList.remove('active'));

    const targetTab = document.getElementById(tabId);
    if (targetTab) targetTab.style.display = 'block';

    const activeBtn = document.querySelector(`.wa-tab-btn[data-tab="${tabId}"]`);
    if (activeBtn) activeBtn.classList.add('active');

    switchPreviewTemplate(tplType);
}

// Switch template view in the live smartphone preview
function switchPreviewTemplate(tplType) {
    currentPreviewTemplate = tplType;

    document.querySelectorAll('.wa-phone-pill').forEach(pill => {
        pill.classList.remove('active');
        if (pill.getAttribute('onclick').includes(tplType)) {
            pill.classList.add('active');
        }
    });

    updateLivePreview(tplType);
}

// Update the text in the smartphone chat bubble in real-time
function updateLivePreview(tplType) {
    tplType = tplType || currentPreviewTemplate;

    let textareaId = 'tpl-' + (tplType === 'due_reminder' ? 'due' : tplType);
    let rawText = '';
    const textarea = document.getElementById(textareaId);

    if (textarea) {
        rawText = textarea.value;
    }

    if (!rawText) {
        rawText = "Hello, this is a message from Vikas Udhyog.";
    }

    // Substitute sample variables
    const vars = SAMPLE_VARIABLES[tplType] || {};
    let rendered = rawText;
    for (const [key, val] of Object.entries(vars)) {
        rendered = rendered.replaceAll('{{' + key + '}}', val);
        rendered = rendered.replaceAll('{{ ' + key + ' }}', val);
    }

    // Escape basic HTML
    let safeHtml = rendered
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");

    // Format WhatsApp markdown: *bold* -> <strong>bold</strong>, _italic_ -> <em>italic</em>
    safeHtml = safeHtml.replace(/\*([^\*]+)\*/g, '<strong>$1</strong>');
    safeHtml = safeHtml.replace(/_([^_]+)_/g, '<em>$1</em>');

    const previewEl = document.getElementById('wa-preview-text');
    if (previewEl) {
        previewEl.innerHTML = safeHtml;
    }

    // Update timestamp
    const now = new Date();
    let hours = now.getHours();
    let minutes = now.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    minutes = minutes < 10 ? '0' + minutes : minutes;
    const timeStr = hours + ':' + minutes + ' ' + ampm;
    const timeEl = document.getElementById('wa-preview-time');
    if (timeEl) timeEl.textContent = timeStr;
}

// Insert placeholder at cursor location inside textarea
function insertPlaceholder(textareaId, placeholder) {
    const textarea = document.getElementById(textareaId);
    if (!textarea) return;

    const startPos = textarea.selectionStart;
    const endPos = textarea.selectionEnd;
    const textBefore = textarea.value.substring(0, startPos);
    const textAfter = textarea.value.substring(endPos, textarea.value.length);

    textarea.value = textBefore + placeholder + textAfter;
    textarea.focus();
    textarea.selectionStart = startPos + placeholder.length;
    textarea.selectionEnd = startPos + placeholder.length;

    updateLivePreview(currentPreviewTemplate);
}

// Toggle token visibility
function toggleTokenVisibility() {
    const input = document.getElementById('field-access-token');
    const icon = document.getElementById('token-eye-icon');
    const label = document.getElementById('token-eye-label');

    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'fa-solid fa-eye-slash';
        label.textContent = 'Hide Token';
    } else {
        input.type = 'password';
        icon.className = 'fa-solid fa-eye';
        label.textContent = 'Show Token';
    }
}

// Copy webhook URL to clipboard
function copyWebhookUrl() {
    const input = document.getElementById('webhook-url-input');
    if (input) {
        input.select();
        navigator.clipboard.writeText(input.value).then(() => {
            alert('Webhook URL copied to clipboard!');
        });
    }
}

// Handle Send Test Message AJAX
async function handleSendTestMessage(event) {
    event.preventDefault();

    const phone = document.getElementById('test-phone-input').value.trim();
    const templateType = document.getElementById('test-template-select').value;
    const btn = document.getElementById('btn-send-test');
    const btnText = document.getElementById('test-btn-text');
    const resultBox = document.getElementById('test-result-box');

    if (!phone) {
        alert('Please enter a recipient mobile number.');
        return;
    }

    btn.disabled = true;
    btnText.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Dispatching...';
    resultBox.style.display = 'none';

    try {
        const response = await fetch("{{ route('admin.settings.whatsapp.test') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                test_phone: phone,
                template_type: templateType
            })
        });

        const data = await response.json();

        resultBox.style.display = 'block';
        if (data.success) {
            resultBox.innerHTML = `
                <div style="background: #DCFCE7; border: 1px solid #BBF7D0; color: #15803D; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.78rem;">
                    <div style="font-weight: 700; margin-bottom: 2px;"><i class="fa-solid fa-circle-check me-1"></i> Dispatched Successfully!</div>
                    <div>${data.message}</div>
                    <div style="margin-top: 4px; font-family: monospace; font-size: 0.72rem; color: #166534;">ID: ${data.response_id || 'N/A'}</div>
                </div>
            `;
            // Trigger preview for this template
            switchPreviewTemplate(templateType);
        } else {
            resultBox.innerHTML = `
                <div style="background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.78rem;">
                    <div style="font-weight: 700; margin-bottom: 2px;"><i class="fa-solid fa-triangle-exclamation me-1"></i> Dispatch Failed</div>
                    <div>${data.message}</div>
                </div>
            `;
        }
    } catch (err) {
        resultBox.style.display = 'block';
        resultBox.innerHTML = `
            <div style="background: #FEE2E2; border: 1px solid #FECACA; color: #991B1B; padding: 0.65rem 0.85rem; border-radius: 8px; font-size: 0.78rem;">
                <div style="font-weight: 700;">Network Request Failed</div>
                <div>${err.message}</div>
            </div>
        `;
    } finally {
        btn.disabled = false;
        btnText.innerHTML = 'Send WhatsApp Alert';
    }
}

// Ping WhatsApp connection handshake
async function pingWhatsAppConnection() {
    const btn = document.getElementById('btn-ping-gateway');
    const label = document.getElementById('ping-btn-text');

    btn.disabled = true;
    label.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Pinging...';

    try {
        const response = await fetch("{{ route('admin.settings.whatsapp.ping') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        });

        const data = await response.json();
        alert(`[API Diagnostic] Status: ${data.status}\n\n${data.message}`);
    } catch (err) {
        alert('Diagnostic ping failed: ' + err.message);
    } finally {
        btn.disabled = false;
        label.textContent = 'Ping Gateway';
    }
}

// Quick Test modal shortcut
function openTestModal() {
    const input = document.getElementById('test-phone-input');
    if (input) {
        input.focus();
        input.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}
</script>
@endsection
