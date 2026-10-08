@extends('admin.layouts.app')

@section('title', 'Server Migration Runner - VIKAS UDHYOG ERP')
@section('page_code', 'set-migration')

@section('content')
<section class="view-section active" id="view-set-migration">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <a href="{{ route('admin.settings.backup-restore') }}">Settings</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Server Database Migration Runner</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-database text-primary"></i> Server Database Migration Runner
            </h1>
            <p class="erp-page-subtitle">
                Execute database migrations, Unit Master synchronizer, and system cache flushes directly on the server.
            </p>
        </div>

        <div class="erp-header-actions">
            <a href="{{ route('admin.settings.backup-restore') }}" class="btn btn-outline" style="border-radius: 8px;">
                <i class="fa-solid fa-rotate-left me-1"></i> Backup &amp; Restore
            </a>
            <a href="{{ route('admin.run-migration', ['action' => 'status']) }}" class="btn btn-outline" style="border-radius: 8px;">
                <i class="fa-solid fa-list-check me-1"></i> Migration Status
            </a>
            <a href="{{ route('admin.run-migration', ['action' => 'seed']) }}" class="btn btn-outline" style="border-radius: 8px;">
                <i class="fa-solid fa-seedling me-1"></i> Sync Units
            </a>
            <a href="{{ route('admin.run-migration', ['action' => 'migrate-only']) }}" class="btn btn-primary erp-btn-header-primary" style="border-radius: 8px;">
                <i class="fa-solid fa-play me-1"></i> Run Migrations Only
            </a>
        </div>
    </div>

    <!-- 4-Card Status & Diagnostic Metrics Ribbon -->
    <div class="erp-kpi-grid" style="margin-bottom: 1.5rem;">
        <!-- Card 1: Execution Status -->
        <div class="card erp-kpi-card {{ $status === 'success' ? 'erp-kpi-success' : '' }}" style="{{ $status !== 'success' ? 'border-left: 4px solid #EF4444;' : '' }}">
            <div class="erp-kpi-icon-box {{ $status === 'success' ? 'erp-kpi-icon-success' : '' }}" style="{{ $status !== 'success' ? 'background: rgba(239, 68, 68, 0.12); color: #DC2626;' : '' }}">
                <i class="fa-solid {{ $status === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Execution Result</div>
                <div class="erp-kpi-val {{ $status === 'success' ? 'erp-kpi-val-success' : '' }}" style="font-size: 1.25rem; {{ $status !== 'success' ? 'color: #DC2626;' : '' }}">
                    {{ $status === 'success' ? 'Success' : 'Failed' }}
                </div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    {{ $status === 'success' ? 'All Artisan tasks completed' : 'Check console errors below' }}
                </div>
            </div>
        </div>

        <!-- Card 2: Action Triggered -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-terminal"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Triggered Action</div>
                <div class="erp-kpi-val" style="font-size: 1.25rem; text-transform: uppercase; letter-spacing: 0.04em;">
                    {{ strtoupper($action) }}
                </div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    Mode: {{ $action === 'status' ? 'Status Inspector' : 'Server Execution' }}
                </div>
            </div>
        </div>

        <!-- Card 3: Execution Time -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Executed At</div>
                <div class="erp-kpi-val" style="color: #1D4ED8; font-size: 1.2rem;">
                    {{ date('h:i:s A') }}
                </div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    {{ date('d-M-Y') }} (IST)
                </div>
            </div>
        </div>

        <!-- Card 4: Cache & State -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-server"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Environment</div>
                <div class="erp-kpi-val" style="color: #7C3AED; font-size: 1.2rem;">
                    {{ strtoupper(app()->environment()) }}
                </div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    PHP {{ PHP_VERSION }} &bull; Laravel 11
                </div>
            </div>
        </div>
    </div>

    <!-- Status Alert Banner -->
    <div class="card" style="margin-bottom: 1.5rem; border-radius: 14px; border: 1px solid {{ $status === 'success' ? '#BBF7D0' : '#FECACA' }}; background: {{ $status === 'success' ? '#F0FDF4' : '#FEF2F2' }}; box-shadow: 0 4px 16px rgba(0,0,0,0.03); overflow: hidden;">
        <div style="padding: 1.15rem 1.4rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; align-items: center; gap: 1rem;">
                <div style="width: 44px; height: 44px; border-radius: 12px; background: {{ $status === 'success' ? '#15803D' : '#DC2626' }}; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-size: 1.3rem; flex-shrink: 0; box-shadow: 0 4px 12px {{ $status === 'success' ? 'rgba(21, 128, 61, 0.25)' : 'rgba(220, 38, 38, 0.25)' }};">
                    <i class="fa-solid {{ $status === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                </div>
                <div>
                    <h4 style="font-size: 1rem; font-weight: 700; color: {{ $status === 'success' ? '#14532D' : '#991B1B' }}; margin: 0 0 3px 0;">
                        {{ $status === 'success' ? 'Execution Completed Successfully' : 'Execution Failed' }}
                    </h4>
                    <p style="font-size: 0.85rem; color: {{ $status === 'success' ? '#166534' : '#B91C1C' }}; margin: 0; font-family: Consolas, monospace;">
                        {{ $message }}
                    </p>
                </div>
            </div>
            <div>
                <span class="badge" style="background: {{ $status === 'success' ? '#15803D' : '#DC2626' }}; color: #FFFFFF; font-size: 0.78rem; padding: 6px 14px; border-radius: 999px; font-weight: 700; letter-spacing: 0.05em; font-family: Consolas, monospace;">
                    ACTION: {{ strtoupper($action) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Terminal Console Output Window -->
    <div class="card" style="border-radius: 16px; border: 1px solid #1E293B; background: #0B0F19; box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.2); overflow: hidden; margin-bottom: 1.5rem;">
        
        <!-- Terminal Titlebar -->
        <div style="background: #111827; padding: 0.75rem 1.25rem; border-bottom: 1px solid #1F2937; display: flex; align-items: center; justify-content: space-between;">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                <span style="margin-left: 10px; font-family: Consolas, monospace; color: #94A3B8; font-size: 0.8rem; font-weight: 600;">
                    server-console / artisan-output
                </span>
            </div>
            
            <button type="button" class="btn btn-sm" onclick="copyConsoleOutput()" style="background: rgba(255,255,255,0.08); color: #CBD5E1; border: 1px solid rgba(255,255,255,0.15); font-size: 0.75rem; padding: 4px 12px; border-radius: 6px;">
                <i class="fa-regular fa-copy me-1"></i> <span id="copy-btn-text">Copy Log</span>
            </button>
        </div>

        <!-- Terminal Body -->
        <div style="padding: 1.35rem 1.5rem; color: #F8FAFC; font-family: Consolas, 'SFMono-Regular', Menlo, Monaco, monospace; font-size: 0.86rem; line-height: 1.65; max-height: 520px; overflow-y: auto;">
            @foreach($outputs as $cmd => $out)
                <div style="margin-bottom: 1.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.4rem; color: #38BDF8; font-weight: 700;">
                        <span style="color: #A855F7;">➜</span>
                        <span>$ php artisan {{ $cmd }}</span>
                    </div>
                    <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; padding: 0.85rem 1rem;">
                        <pre id="output-block-{{ $loop->index }}" style="background: transparent; color: #E2E8F0; border: none; padding: 0; margin: 0; white-space: pre-wrap; word-wrap: break-word; font-family: inherit; font-size: 0.84rem;">{{ trim($out) ?: 'INFO  Nothing to migrate / Command completed without output.' }}</pre>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Terminal Footer Bar -->
        <div style="background: #111827; padding: 0.5rem 1.25rem; border-top: 1px solid #1F2937; display: flex; align-items: center; justify-content: space-between; font-size: 0.72rem; color: #64748B; font-family: Consolas, monospace;">
            <span>Vikas Udhyog ERP &bull; Artisan CLI v11.x</span>
            <span>Status: 200 OK &bull; Exit 0</span>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.75rem;">
        <a href="{{ route('admin.settings.backup-restore') }}" class="btn btn-outline" style="border-radius: 8px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Backup &amp; Restore
        </a>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.run-migration') }}" class="btn btn-outline" style="border-radius: 8px;">
                <i class="fa-solid fa-rotate me-1"></i> Run Complete Sync (Migrate + Seed + Clear)
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-primary" style="border-radius: 8px;">
                <i class="fa-solid fa-gauge me-1"></i> Return to Dashboard
            </a>
        </div>
    </div>

</section>

<!-- Copy Output Script -->
<script>
function copyConsoleOutput() {
    const preEls = document.querySelectorAll('pre[id^="output-block-"]');
    let text = '';
    preEls.forEach(el => {
        text += el.innerText + "\n\n";
    });

    if (!text.trim()) {
        text = 'Nothing to copy';
    }

    navigator.clipboard.writeText(text).then(() => {
        const btnText = document.getElementById('copy-btn-text');
        if (btnText) {
            btnText.innerText = 'Copied!';
            setTimeout(() => {
                btnText.innerText = 'Copy Log';
            }, 2000);
        }
    });
}
</script>
@endsection
