@extends('admin.layouts.app')

@section('title', 'Server Migration Runner - VIKAS UDHYOG ERP')
@section('page_code', 'set-migration')

@section('content')
<div class="container-fluid px-3 py-4" style="max-width: 1200px;">
    <!-- Breadcrumb & Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1" style="font-size: 0.8rem; background: transparent; padding: 0;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}" style="color: #64748B;">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.settings.backup-restore') }}" style="color: #64748B;">Settings</a></li>
                    <li class="breadcrumb-item active" style="color: #5B841E; font-weight: 600;">Database Migration Runner</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0" style="font-weight: 700; color: #1E293B;">
                <i class="fa-solid fa-database me-2" style="color: #5B841E;"></i>Server Database Migration Runner
            </h1>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                Execute database migrations, Unit Master synchronizer, and system cache flushes directly on the server.
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('admin.run-migration', ['action' => 'migrate-only']) }}" class="btn" style="background: #5B841E; color: #FFFFFF; font-weight: 600; font-size: 0.85rem; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="fa-solid fa-play me-1"></i> Run Migrations Only
            </a>
            <a href="{{ route('admin.run-migration') }}" class="btn btn-outline-secondary" style="font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="fa-solid fa-rotate me-1"></i> Migrate + Sync All
            </a>
            <a href="{{ route('admin.run-migration', ['action' => 'status']) }}" class="btn btn-outline-secondary" style="font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="fa-solid fa-list-check me-1"></i> Migration Status
            </a>
            <a href="{{ route('admin.run-migration', ['action' => 'seed']) }}" class="btn btn-outline-secondary" style="font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="fa-solid fa-seedling me-1"></i> Sync Units
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary" style="font-size: 0.85rem; font-weight: 600; padding: 0.5rem 1rem; border-radius: 8px;">
                <i class="fa-solid fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Status Alert Banner -->
    <div class="card mb-4" style="border-radius: 12px; border: 1px solid {{ $status === 'success' ? '#A7F3D0' : '#FECACA' }}; background: {{ $status === 'success' ? '#F0FDF4' : '#FEF2F2' }}; box-shadow: 0 2px 10px rgba(0,0,0,0.03);">
        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: {{ $status === 'success' ? '#059669' : '#DC2626' }}; display: flex; align-items: center; justify-content: center; color: #FFFFFF; font-size: 1.4rem;">
                    <i class="fa-solid {{ $status === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation' }}"></i>
                </div>
                <div>
                    <h5 class="mb-1" style="font-weight: 700; color: {{ $status === 'success' ? '#065F46' : '#991B1B' }};">
                        {{ $status === 'success' ? 'Execution Completed Successfully' : 'Execution Failed' }}
                    </h5>
                    <p class="mb-0 font-monospace" style="font-size: 0.88rem; color: {{ $status === 'success' ? '#047857' : '#B91C1C' }};">
                        {{ $message }}
                    </p>
                </div>
            </div>
            <div>
                <span class="badge" style="background: {{ $status === 'success' ? '#059669' : '#DC2626' }}; color: #FFFFFF; font-size: 0.82rem; padding: 6px 12px; border-radius: 20px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;">
                    Action: {{ strtoupper($action) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Terminal Output Card -->
    <div class="card" style="border-radius: 12px; border: 1px solid #1E293B; background: #0F172A; box-shadow: 0 4px 20px rgba(0,0,0,0.15); overflow: hidden;">
        <div class="card-header d-flex align-items-center justify-content-between py-3 px-4" style="background: #1E293B; border-bottom: 1px solid #334155;">
            <div class="d-flex align-items-center gap-2">
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #EF4444; display: inline-block;"></span>
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #F59E0B; display: inline-block;"></span>
                <span style="width: 12px; height: 12px; border-radius: 50%; background: #10B981; display: inline-block;"></span>
                <span class="ms-2 font-monospace" style="color: #94A3B8; font-size: 0.82rem;">server-console / artisan-output</span>
            </div>
            <button class="btn btn-sm btn-dark" style="font-size: 0.75rem; border: 1px solid #475569; color: #CBD5E1;" onclick="copyOutput()">
                <i class="fa-regular fa-copy me-1"></i> Copy Log
            </button>
        </div>
        <div class="card-body p-4" style="color: #F8FAFC; font-family: Consolas, 'SFMono-Regular', Menlo, Monaco, monospace; font-size: 0.85rem; line-height: 1.6; max-height: 550px; overflow-y: auto;">
            @foreach($outputs as $cmd => $out)
                <div class="mb-4">
                    <div style="color: #38BDF8; font-weight: 700; margin-bottom: 6px;">
                        $ php artisan {{ $cmd }}
                    </div>
                    <pre id="output-content-{{ $loop->index }}" style="background: transparent; color: #E2E8F0; border: none; padding: 0; margin: 0; white-space: pre-wrap; word-wrap: break-word; font-family: inherit;">{{ trim($out) ?: '(No output returned)' }}</pre>
                </div>
            @endforeach
        </div>
    </div>
</div>

@push('scripts')
<script>
    function copyOutput() {
        const text = document.querySelector('.card-body pre')?.innerText || '';
        navigator.clipboard.writeText(text).then(() => {
            alert('Console log copied to clipboard!');
        });
    }
</script>
@endpush
@endsection
