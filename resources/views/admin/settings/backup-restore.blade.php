@extends('admin.layouts.app')

@section('title', 'Database Backup & Restore - VIKAS UDHYOG ERP')
@section('page_code', 'set-backup')

@section('content')
<section class="view-section active" id="view-set-backup">
                    <div class="page-header"><div><h1 class="page-title">Backup & Restore Database</h1></div></div>
                    <div class="card" style="max-width: 500px;">
                        <h3 style="margin-bottom: 1rem;">Data Backup</h3>
                        <p style="font-size: 0.9rem; color: var(--text-muted); margin-bottom: 1.5rem;">Download complete JSON snapshot of products, customers, vendors, and transactions.</p>
                        <button class="btn btn-primary" onclick="SettingsModule.exportDataJSON()"><i class="fa-solid fa-download"></i> Download Full Backup JSON</button>

                        <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">

                        <h3 style="margin-bottom: 1rem;">Restore Backup</h3>
                        <input type="file" accept=".json" class="form-control" onchange="SettingsModule.importDataJSON(this)">
                    </div>
                </section>
@endsection


