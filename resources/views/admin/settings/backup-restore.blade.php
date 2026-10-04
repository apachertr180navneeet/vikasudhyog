@extends('admin.layouts.app')

@section('title', 'Database Backup & Restore - VIKAS UDHYOG ERP')
@section('page_code', 'set-backup')

@section('content')
<section class="view-section active" id="view-set-backup">
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title">Backup &amp; Restore Database</h1>
            <p class="text-muted" style="font-size: 0.85rem; margin-top: 4px;">System backup snapshots, data restore, and server database schema migration tools.</p>
        </div>
    </div>

    <div class="row g-4" style="max-width: 1050px;">
        <!-- Card 1: Server Migration & Schema Update -->
        <div class="col-md-6">
            <div class="card h-100 p-4" style="border-radius: 12px; border: 1px solid #E2E8F0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fa-solid fa-database"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" style="font-weight: 700; font-size: 1.1rem; color: #1E293B;">Server Database Migration</h4>
                        <span class="text-muted" style="font-size: 0.78rem;">Schema updates &amp; table changes</span>
                    </div>
                </div>
                <p style="font-size: 0.88rem; color: #64748B; line-height: 1.5; margin-bottom: 1.25rem;">
                    Run pending database migrations, sync Unit Master records/synonyms, and clear compiled application caches directly without requiring terminal SSH access.
                </p>
                <div class="d-flex flex-column gap-2 mt-auto">
                    <a href="{{ route('admin.run-migration', ['action' => 'migrate-only']) }}" class="btn text-white text-center" style="background: #5B841E; font-weight: 600; font-size: 0.88rem; padding: 0.55rem 1rem; border-radius: 8px;">
                        <i class="fa-solid fa-play me-1"></i> Run Updated Migrations Only
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.run-migration', ['action' => 'status']) }}" class="btn btn-outline-secondary w-50" style="font-size: 0.8rem; font-weight: 600;">
                            <i class="fa-solid fa-list-check me-1"></i> Check Status
                        </a>
                        <a href="{{ route('admin.run-migration', ['action' => 'seed']) }}" class="btn btn-outline-secondary w-50" style="font-size: 0.8rem; font-weight: 600;">
                            <i class="fa-solid fa-seedling me-1"></i> Sync Units
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: JSON Backup & Restore -->
        <div class="col-md-6">
            <div class="card h-100 p-4" style="border-radius: 12px; border: 1px solid #E2E8F0; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(59, 130, 246, 0.12); color: #2563EB; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                        <i class="fa-solid fa-cloud-arrow-down"></i>
                    </div>
                    <div>
                        <h4 class="mb-0" style="font-weight: 700; font-size: 1.1rem; color: #1E293B;">JSON Backup &amp; Restore</h4>
                        <span class="text-muted" style="font-size: 0.78rem;">Full snapshot export / import</span>
                    </div>
                </div>
                <p style="font-size: 0.88rem; color: #64748B; line-height: 1.5; margin-bottom: 1.25rem;">
                    Download complete JSON snapshot of products, customers, vendors, and transactions, or restore from a previous JSON backup file.
                </p>
                <div class="d-flex flex-column gap-3 mt-auto">
                    <button class="btn btn-outline-primary" style="font-weight: 600; font-size: 0.88rem; padding: 0.55rem 1rem; border-radius: 8px;" onclick="SettingsModule.exportDataJSON()">
                        <i class="fa-solid fa-download me-1"></i> Download Full Backup JSON
                    </button>
                    <div>
                        <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #475569; text-transform: uppercase;">Restore from JSON</label>
                        <input type="file" accept=".json" class="form-control" style="font-size: 0.85rem;" onchange="SettingsModule.importDataJSON(this)">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
