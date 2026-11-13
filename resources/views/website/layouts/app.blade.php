<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $siteTitle = $settings['meta_title'] ?? ($settings['site_name'] ?? 'Crelogics ERP') . ' - All-In-One Cloud ERP Solution';
        $siteDesc = $settings['meta_description'] ?? 'Manage job orders, quotations, invoices, customers, products and daily business operations with Crelogics ERP.';
        $logo = !empty($settings['website_logo']) ? asset($settings['website_logo']) : (platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png'));
        $waNum = preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '996598500143');
    @endphp
    <title>@hasSection('title')@yield('title')@else{{ $siteTitle }}@endif</title>
    <meta name="description" content="@hasSection('meta_description')@yield('meta_description')@else{{ $siteDesc }}@endif">

    @if($logo)
        <link rel="icon" type="image/png" href="{{ $logo }}">
    @endif

    <!-- Google Fonts: Inter & Caveat (for hand-drawn Odoo style notes) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600;700&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS (Local first, CDN fallback) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" onerror="this.onerror=null;">

    <!-- FontAwesome Free Icons (Local first, CDN fallback) -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" onerror="this.onerror=null;">

    <!-- Crelogics ERP Landing Page Styles -->
    <link rel="stylesheet" href="{{ asset('assets/website/css/landing.css') }}?v={{ @filemtime(public_path('assets/website/css/landing.css')) ?: time() }}">

    @stack('styles')
</head>
<body class="landing-page">

    <!-- Header Navigation -->
    @include('website.partials.header')

    <!-- Main Landing Page Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    @include('website.partials.footer')

    <!-- Floating WhatsApp Support Button -->
    <a href="https://wa.me/{{ $waNum }}" target="_blank" rel="noopener noreferrer" 
       title="Chat on WhatsApp"
       style="position: fixed; width: 56px; height: 56px; bottom: 30px; right: 30px; background-color: #25D366; color: #FFF; border-radius: 50%; text-align: center; font-size: 30px; box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4); z-index: 1040; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.25s ease;"
       onmouseover="this.style.transform='scale(1.12)'" 
       onmouseout="this.style.transform='scale(1)'">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- 4-Step Get Your Free Proposal Multi-Step Modal -->
    @include('website.partials.proposal_modal')

    <!-- Bootstrap 5 JS Bundle -->
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        if (typeof bootstrap === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"><\/script>');
        }
    </script>

    <!-- SweetAlert2 for Toast Notifications (Local first, CDN fallback) -->
    <script src="{{ asset('assets/vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
        if (typeof Swal === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"><\/script>');
        }
    </script>

    <!-- Custom Landing Page JS -->
    <script src="{{ asset('assets/website/js/landing.js') }}?v={{ @filemtime(public_path('assets/website/js/landing.js')) ?: time() }}"></script>

    <!-- Global Flash Message Toast Notification -->
    @if(session('contact_success') || session('success') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var successMsg = @json(session('contact_success') ?: session('success'));
                var errorMsg = @json(session('error'));

                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    if (successMsg) {
                        Toast.fire({
                            icon: 'success',
                            title: successMsg
                        });
                    } else if (errorMsg) {
                        Toast.fire({
                            icon: 'error',
                            title: errorMsg
                        });
                    }
                }
            });
        </script>
    @endif

    @stack('scripts')
</body>
</html>

