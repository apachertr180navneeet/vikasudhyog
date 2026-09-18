@extends('admin.layouts.app')

@section('title', 'Company Settings - VIKAS UDHYOG ERP')
@section('page_code', 'set-company')

@section('content')
<section class="view-section active" id="view-set-company">
                    <div class="page-header"><div><h1 class="page-title">Company Settings</h1></div></div>
                    <div class="card" style="max-width: 600px;">
                        <form onsubmit="SettingsModule.saveCompanySettings(event)">
                            <div class="form-group" style="margin-bottom:1rem;"><label class="form-label">Company Name</label><input type="text" id="set-comp-name" class="form-control"></div>
                            <div class="form-group" style="margin-bottom:1rem;"><label class="form-label">GSTIN</label><input type="text" id="set-comp-gstin" class="form-control"></div>
                            <div class="form-group" style="margin-bottom:1rem;"><label class="form-label">Phone</label><input type="text" id="set-comp-phone" class="form-control"></div>
                            <div class="form-group" style="margin-bottom:1rem;"><label class="form-label">Email</label><input type="email" id="set-comp-email" class="form-control"></div>
                            <div class="form-group" style="margin-bottom:1rem;"><label class="form-label">Factory Address</label><textarea id="set-comp-address" class="form-control"></textarea></div>
                            <button type="submit" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </section>
@endsection


