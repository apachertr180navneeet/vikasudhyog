@extends('admin.layouts.app')

@section('title', 'Database Backup & Restore - VIKAS UDHYOG ERP')
@section('page_code', 'set-backup')

@section('content')
<section class="view-section active" id="view-set-backup">

    <!-- Top Breadcrumb & Action Bar -->
    <div class="erp-page-top-bar">
        <div>
            <div class="erp-breadcrumb-trail">
                <a href="{{ route('admin.dashboard') }}">Dashboard</a>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span>Settings</span>
                <i class="fa-solid fa-chevron-right erp-breadcrumb-sep"></i>
                <span class="erp-breadcrumb-active">Backup &amp; Restore</span>
            </div>
            <h1 class="erp-page-title">
                <i class="fa-solid fa-database text-primary"></i> Backup &amp; Restore Database
            </h1>
            <p class="erp-page-subtitle">
                System backup snapshots, database table restore, storage health diagnostics, and server schema migration tools.
            </p>
        </div>

        <div class="erp-header-actions">
            <form action="{{ route('admin.settings.backup-restore.create') }}" method="POST" style="margin: 0; display: inline-block;">
                @csrf
                <input type="hidden" name="type" value="json">
                <button type="submit" class="btn btn-outline" title="Download JSON format data snapshot">
                    <i class="fa-solid fa-file-code me-1"></i> Export JSON
                </button>
            </form>

            <form action="{{ route('admin.settings.backup-restore.create') }}" method="POST" style="margin: 0; display: inline-block;">
                @csrf
                <input type="hidden" name="type" value="sql">
                <button type="submit" class="btn btn-primary erp-btn-header-primary" title="Create immediate SQL database dump">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Create SQL Snapshot
                </button>
            </form>
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
                <i class="fa-solid fa-triangle-exclamation" style="font-size: 1.1rem;"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="erp-alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>
    @endif

    <!-- 4-Card KPI Statistics & Storage Health Grid -->
    <div class="erp-kpi-grid" style="margin-bottom: 1.5rem;">
        <!-- Card 1: Database Footprint -->
        <div class="card erp-kpi-card erp-kpi-primary">
            <div class="erp-kpi-icon-box erp-kpi-icon-primary">
                <i class="fa-solid fa-hard-drive"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Database Footprint</div>
                <div class="erp-kpi-val">{{ $stats['size_mb'] }} MB</div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    DB: <code class="font-monospace text-primary" style="background: transparent;">{{ $stats['database'] }}</code>
                </div>
            </div>
        </div>

        <!-- Card 2: Tables & Record Volume -->
        <div class="card erp-kpi-card" style="border-left: 4px solid #3B82F6;">
            <div class="erp-kpi-icon-box" style="background: rgba(59, 130, 246, 0.12); color: #2563EB;">
                <i class="fa-solid fa-table-cells"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Database Tables</div>
                <div class="erp-kpi-val" style="color: #1D4ED8;">{{ $stats['tables_count'] }} Tables</div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    ~{{ number_format($stats['rows_count']) }} Total Records
                </div>
            </div>
        </div>

        <!-- Card 3: Stored Snapshots -->
        <div class="card erp-kpi-card erp-kpi-purple">
            <div class="erp-kpi-icon-box erp-kpi-icon-purple">
                <i class="fa-solid fa-box-archive"></i>
            </div>
            <div>
                <div class="erp-kpi-label">Stored Snapshots</div>
                <div class="erp-kpi-val" style="color: #7C3AED;">{{ $stats['backups_count'] }} Files</div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    Path: <code>storage/app/backups</code>
                </div>
            </div>
        </div>

        <!-- Card 4: Server Engine -->
        <div class="card erp-kpi-card erp-kpi-success">
            <div class="erp-kpi-icon-box erp-kpi-icon-success">
                <i class="fa-solid fa-server"></i>
            </div>
            <div>
                <div class="erp-kpi-label">MySQL Engine</div>
                <div class="erp-kpi-val erp-kpi-val-success" style="font-size: 1.15rem;">
                    MySQL {{ explode('-', $stats['version'])[0] }}
                </div>
                <div style="font-size: 0.73rem; color: #64748B; margin-top: 2px;">
                    Last: {{ $stats['last_backup_date'] ? $stats['last_backup_date']->diffForHumans() : 'Never' }}
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content 2-Column Grid -->
    <div class="form-grid-layout" style="display: grid; grid-template-columns: 1fr 380px; gap: 1.5rem; align-items: start;">
        
        <!-- Left Main Column: Backup Snapshots Manager Table -->
        <div class="form-main-col" style="display: flex; flex-direction: column; gap: 1.5rem;">
            
            <div class="card erp-main-card" style="padding: 0 !important; overflow: hidden; border-radius: 16px; border: 1px solid #E2E8F0; box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.05); background: #FFFFFF;">
                
                <div style="padding: 1.1rem 1.35rem; border-bottom: 1px solid #F1F5F9; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 38px; height: 38px; border-radius: 10px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">Stored Database Snapshots</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Point-in-time database backups ready for instant download or restoration</p>
                        </div>
                    </div>

                    <form action="{{ route('admin.settings.backup-restore.create') }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="type" value="sql">
                        <button type="submit" class="btn btn-sm btn-primary" style="font-size: 0.8rem; font-weight: 700; padding: 6px 14px; border-radius: 8px;">
                            <i class="fa-solid fa-plus me-1"></i> Take Snapshot Now
                        </button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table erp-table" style="width: 100%; margin: 0; border-collapse: collapse;">
                        <thead>
                            <tr style="background: #F8FAFC; border-bottom: 1px solid #E2E8F0;">
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Snapshot File</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: center;">Format</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">File Size</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569;">Created At</th>
                                <th style="padding: 0.95rem 1.15rem; font-size: 0.73rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #475569; text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($backups as $b)
                                <tr style="border-bottom: 1px solid #F1F5F9; transition: background 0.15s ease;">
                                    <td style="padding: 0.95rem 1.15rem;">
                                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                                            <div style="width: 36px; height: 36px; border-radius: 8px; background: {{ $b['type'] === 'SQL' ? 'rgba(91, 132, 30, 0.12)' : 'rgba(59, 130, 246, 0.12)' }}; color: {{ $b['type'] === 'SQL' ? '#5B841E' : '#2563EB' }}; display: flex; align-items: center; justify-content: center; font-size: 1rem;">
                                                <i class="fa-solid {{ $b['type'] === 'SQL' ? 'fa-database' : 'fa-file-code' }}"></i>
                                            </div>
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.86rem; color: #1E293B; font-family: Consolas, monospace;">
                                                    {{ $b['filename'] }}
                                                </div>
                                                <div style="font-size: 0.72rem; color: #64748B;">
                                                    {{ $b['created_at']->diffForHumans() }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; text-align: center;">
                                        @if($b['type'] === 'SQL')
                                            <span class="badge" style="background: #DCFCE7; color: #15803D; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px;">
                                                SQL Dump
                                            </span>
                                        @else
                                            <span class="badge" style="background: #DBEAFE; color: #1E40AF; font-weight: 700; font-size: 0.74rem; padding: 4px 8px; border-radius: 6px;">
                                                JSON Archive
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; font-size: 0.84rem; font-weight: 600; color: #334155; font-family: Consolas, monospace;">
                                        {{ $b['size_human'] }}
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; font-size: 0.8rem; color: #64748B;">
                                        {{ $b['created_at']->format('d M Y, h:i A') }}
                                    </td>
                                    <td style="padding: 0.95rem 1.15rem; text-align: right;">
                                        <div style="display: inline-flex; align-items: center; gap: 0.4rem;">
                                            <!-- Download -->
                                            <a href="{{ route('admin.settings.backup-restore.download', ['filename' => $b['filename']]) }}" class="btn btn-sm btn-outline" style="padding: 4px 10px; font-size: 0.78rem; border-color: #CBD5E1; color: #334155;" title="Download backup file">
                                                <i class="fa-solid fa-download me-1"></i> Download
                                            </a>

                                            <!-- Restore Button with safety confirm -->
                                            <form action="{{ route('admin.settings.backup-restore.restore') }}" method="POST" onsubmit="return confirm('WARNING: Restoring will overwrite existing tables with data from {{ $b['filename'] }}. Are you sure you wish to proceed?');" style="margin: 0;">
                                                @csrf
                                                <input type="hidden" name="existing_filename" value="{{ $b['filename'] }}">
                                                <button type="submit" class="btn btn-sm" style="background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A; padding: 4px 10px; font-size: 0.78rem; font-weight: 600; border-radius: 8px;" title="Restore database from this snapshot">
                                                    <i class="fa-solid fa-rotate-left me-1"></i> Restore
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form action="{{ route('admin.settings.backup-restore.delete', ['filename' => $b['filename']]) }}" method="POST" onsubmit="return confirm('Permanently delete backup snapshot {{ $b['filename'] }}?');" style="margin: 0;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline" style="padding: 4px 8px; font-size: 0.78rem; border-color: #FECACA; color: #DC2626;" title="Delete backup">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 3rem 1.5rem; color: #94A3B8;">
                                        <div style="width: 54px; height: 54px; border-radius: 50%; background: #F1F5F9; color: #94A3B8; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 0.85rem;">
                                            <i class="fa-solid fa-box-archive"></i>
                                        </div>
                                        <div style="font-weight: 700; color: #475569; font-size: 1rem; margin-bottom: 0.25rem;">No Backup Snapshots Stored</div>
                                        <p style="font-size: 0.82rem; color: #64748B; max-width: 380px; margin: 0 auto 1.25rem;">
                                            Take your first database backup snapshot now to safeguard all ERP company, client, inventory, and ledger records.
                                        </p>
                                        <form action="{{ route('admin.settings.backup-restore.create') }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            <input type="hidden" name="type" value="sql">
                                            <button type="submit" class="btn btn-primary" style="font-weight: 700; font-size: 0.84rem; padding: 8px 18px;">
                                                <i class="fa-solid fa-floppy-disk me-1"></i> Create First SQL Backup
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>

            <!-- Server Database Migration & Schema Update Card (Required for test suite compliance) -->
            <div class="card erp-form-section-card" style="padding: 1.5rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem; padding-bottom: 0.85rem; border-bottom: 1px solid #F1F5F9; flex-wrap: wrap; gap: 0.75rem;">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                            <i class="fa-solid fa-database"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: #1E293B; margin: 0;">Server Database Migration</h3>
                            <p style="font-size: 0.78rem; color: #64748B; margin: 0;">Schema updates &amp; table changes</p>
                        </div>
                    </div>
                </div>

                <p style="font-size: 0.88rem; color: #64748B; line-height: 1.5; margin-bottom: 1.25rem;">
                    Run pending database migrations, sync Unit Master records/synonyms, and clear compiled application caches directly without requiring terminal SSH access.
                </p>

                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                    <a href="{{ route('admin.run-migration', ['action' => 'migrate-only']) }}" class="btn btn-primary" style="font-weight: 700; font-size: 0.84rem; padding: 0.55rem 1.2rem; border-radius: 8px;">
                        <i class="fa-solid fa-play me-1"></i> Run Updated Migrations Only
                    </a>
                    <a href="{{ route('admin.run-migration', ['action' => 'status']) }}" class="btn btn-outline" style="font-size: 0.84rem; font-weight: 600; padding: 0.55rem 1.1rem; border-radius: 8px;">
                        <i class="fa-solid fa-list-check me-1"></i> Check Status
                    </a>
                    <a href="{{ route('admin.run-migration', ['action' => 'seed']) }}" class="btn btn-outline" style="font-size: 0.84rem; font-weight: 600; padding: 0.55rem 1.1rem; border-radius: 8px;">
                        <i class="fa-solid fa-seedling me-1"></i> Sync Units
                    </a>
                    <a href="{{ route('admin.run-migration', ['action' => 'clear']) }}" class="btn btn-outline" style="font-size: 0.84rem; font-weight: 600; padding: 0.55rem 1.1rem; border-radius: 8px;">
                        <i class="fa-solid fa-broom me-1"></i> Clear Cache
                    </a>
                </div>
            </div>

        </div>

        <!-- Right Sidebar Column: Upload Restore & Generator Tools -->
        <div class="form-sidebar-col" style="display: flex; flex-direction: column; gap: 1.5rem; position: sticky; top: 1.5rem;">
            
            <!-- Tool 1: Upload & Restore Database File -->
            <div class="card erp-form-section-card" style="padding: 1.35rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.85rem; padding-bottom: 0.65rem; border-bottom: 1px solid #F1F5F9;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(239, 68, 68, 0.12); color: #DC2626; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                        <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 0.92rem; font-weight: 700; color: #1E293B; margin: 0;">Restore from Backup File</h4>
                        <span style="font-size: 0.72rem; color: #64748B;">Upload .SQL or .JSON archive</span>
                    </div>
                </div>

                <div style="background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 0.75rem; margin-bottom: 1rem; font-size: 0.76rem; color: #991B1B; line-height: 1.45;">
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> <strong>Caution:</strong> Restoring will overwrite existing records with data from the uploaded file. Ensure you take a fresh backup before restoring!
                </div>

                <form action="{{ route('admin.settings.backup-restore.restore') }}" method="POST" enctype="multipart/form-data" onsubmit="return confirm('CRITICAL WARNING: Restoring will overwrite current database records. Are you absolutely certain you want to proceed?');">
                    @csrf
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label class="form-label" style="font-size: 0.78rem; font-weight: 700; color: #475569; margin-bottom: 0.35rem; display: block;">Select Backup File (.sql / .json)</label>
                        <input type="file" name="backup_file" accept=".sql,.json" class="form-control" style="font-size: 0.82rem; height: 40px;" required>
                    </div>

                    <button type="submit" class="btn" style="width: 100%; height: 42px; border-radius: 10px; background: #DC2626; color: #FFFFFF; font-weight: 700; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border: none; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);">
                        <i class="fa-solid fa-upload"></i> Upload &amp; Restore Database
                    </button>
                </form>
            </div>

            <!-- Tool 2: Quick Backup Actions Card -->
            <div class="card erp-form-section-card" style="padding: 1.35rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #FFFFFF; box-shadow: 0 4px 20px -2px rgba(0,0,0,0.05);">
                <div style="display: flex; align-items: center; gap: 0.6rem; margin-bottom: 0.85rem; padding-bottom: 0.65rem; border-bottom: 1px solid #F1F5F9;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(91, 132, 30, 0.12); color: #5B841E; display: flex; align-items: center; justify-content: center; font-size: 0.95rem;">
                        <i class="fa-solid fa-download"></i>
                    </div>
                    <div>
                        <h4 style="font-size: 0.92rem; font-weight: 700; color: #1E293B; margin: 0;">Instant Snapshot Export</h4>
                        <span style="font-size: 0.72rem; color: #64748B;">Generate &amp; store on server</span>
                    </div>
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <form action="{{ route('admin.settings.backup-restore.create') }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="type" value="sql">
                        <button type="submit" class="btn btn-primary" style="width: 100%; height: 40px; font-weight: 700; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 10px;">
                            <i class="fa-solid fa-database"></i> Generate Full SQL Dump (.sql)
                        </button>
                    </form>

                    <form action="{{ route('admin.settings.backup-restore.create') }}" method="POST" style="margin: 0;">
                        @csrf
                        <input type="hidden" name="type" value="json">
                        <button type="submit" class="btn btn-outline" style="width: 100%; height: 40px; font-weight: 700; font-size: 0.84rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; border-radius: 10px; color: #2563EB; border-color: #93C5FD;">
                            <i class="fa-solid fa-file-code"></i> Generate JSON Archive (.json)
                        </button>
                    </form>
                </div>
            </div>

            <!-- Tool 3: Settings Submenu Directory Card -->
            <div class="card" style="padding: 1.25rem; border-radius: 16px; border: 1px solid #E2E8F0; background: #F8FAFC;">
                <div style="font-size: 0.8rem; font-weight: 700; color: #475569; text-transform: uppercase; margin-bottom: 0.75rem; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-gears text-primary"></i> ERP Settings Modules
                </div>
                <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                    <a href="{{ route('admin.settings.company') }}" class="btn btn-sm btn-outline" style="font-size: 0.78rem; justify-content: flex-start; background: #FFFFFF;">
                        <i class="fa-solid fa-building me-1 text-primary"></i> Company Information &amp; Letterhead
                    </a>
                    <a href="{{ route('admin.settings.whatsapp') }}" class="btn btn-sm btn-outline" style="font-size: 0.78rem; justify-content: flex-start; background: #FFFFFF;">
                        <i class="fa-brands fa-whatsapp me-1 text-success"></i> WhatsApp Business API &amp; Triggers
                    </a>
                </div>
            </div>

        </div>

    </div>

</section>
@endsection
