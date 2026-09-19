<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'VIKAS UDHYOG ERP - Herbal Manufacturing & Trade Management')</title>
    
    <!-- Google Fonts & Font Awesome Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Admin Styles -->
    <link rel="stylesheet" href="{{ asset('admin/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('admin/css/responsive.css') }}">

    <!-- Toastr & SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- jQuery, Chart.js, Toastr & SweetAlert2 JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('styles')
</head>
<body data-page="@yield('page_code', 'dashboard')">

    <div class="app-container">
        @include('admin.layouts.partials.sidebar')

        <div class="sidebar-overlay" id="sidebar-overlay"></div>

        <!-- Main Wrapper -->
        <main class="main-wrapper">
            @include('admin.layouts.partials.topbar')

            <!-- Page Content Body -->
            <div class="content-body">
                @yield('content')
            </div><!-- /.content-body -->
        </main><!-- /.main-wrapper -->
    </div><!-- /.app-container -->

    @include('admin.layouts.partials.modals')

    <!-- Toast Notification Container -->
    <div class="toast-container" id="toast-container"></div>

    <!-- Admin Application JavaScript Modules -->
    <script src="{{ asset('admin/js/data.js') }}"></script>
    <script src="{{ asset('admin/js/dashboard.js') }}"></script>
    <script src="{{ asset('admin/js/masters.js') }}"></script>
    <script src="{{ asset('admin/js/purchase.js') }}"></script>
    <script src="{{ asset('admin/js/sales.js') }}"></script>
    <script src="{{ asset('admin/js/inventory.js') }}"></script>
    <script src="{{ asset('admin/js/reports.js') }}"></script>
    <script src="{{ asset('admin/js/settings.js') }}"></script>
    <script src="{{ asset('admin/js/app.js') }}"></script>

    <script>
        // Global Toastr Configuration
        if (typeof toastr !== 'undefined') {
            toastr.options = {
                "closeButton": true,
                "closeHtml": '<button type="button" class="toast-close-button" aria-label="Close"><i class="fa-solid fa-xmark" style="font-size: 0.85rem;"></i></button>',
                "debug": false,
                "newestOnTop": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "preventDuplicates": false,
                "showDuration": "250",
                "hideDuration": "500",
                "timeOut": "4000",
                "extendedTimeOut": "1500",
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };
        }

        // Global Flash Message Toasts
        @if(session('success'))
            if (typeof toastr !== 'undefined') toastr.success({!! json_encode(session('success')) !!});
        @endif
        @if(session('error'))
            if (typeof toastr !== 'undefined') toastr.error({!! json_encode(session('error')) !!});
        @endif
        @if(session('warning'))
            if (typeof toastr !== 'undefined') toastr.warning({!! json_encode(session('warning')) !!});
        @endif
        @if(session('info'))
            if (typeof toastr !== 'undefined') toastr.info({!! json_encode(session('info')) !!});
        @endif
    </script>
    @stack('scripts')
</body>
</html>
