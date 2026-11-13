@php
    $logo = !empty($settings['website_logo']) ? asset($settings['website_logo']) : (platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png'));
    $footerDesc = $settings['footer_description'] ?? 'A complete, modern cloud ERP and business management system engineered to streamline job orders, quotations, invoices, multi-branch tracking, and inventory fulfillment.';
    $contactEmail = $settings['contact_email'] ?? 'info@crelogics.com';
    $whatsapp = $settings['whatsapp_number'] ?? '923451438102';
    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
    $copyright = $settings['copyright_text'] ?? (date('Y') . ' CreLogics. All rights reserved.');

    $cmsCompanyPages = class_exists(\App\Models\CmsPage::class) ? \App\Models\CmsPage::forFooter('company')->get() : collect();
    $cmsLegalPages = class_exists(\App\Models\CmsPage::class) ? \App\Models\CmsPage::forFooter('legal')->get() : collect();
@endphp
<footer class="erp-footer">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Col 1: Brand & Overview -->
            <div class="col-lg-3 col-md-6 col-12">
                <div class="footer-brand-title">
                    @if($logo && (file_exists(public_path('uploads/logo/crelogics-logo.png')) || !empty($settings['website_logo']) || platform_logo_url()))
                        <img src="{{ $logo }}" alt="CreLogics" style="max-height: 40px; max-width: 150px; object-fit: contain; filter: brightness(0) invert(1);">
                    @else
                        Cre<span>Logics</span>
                    @endif
                    <span class="navbar-brand-badge ms-1" style="background: rgba(255,255,255,0.1); color: #fff;">ERP</span>
                </div>
                <p class="footer-desc mt-3">
                    {{ $footerDesc }}
                </p>
            </div>

            <!-- Col 2: Dynamic Company Pages -->
            <div class="col-lg-2 col-md-3 col-6">
                <h5 class="footer-col-title">Company</h5>
                <ul class="footer-links">
                    @if($cmsCompanyPages->count() > 0)
                        @foreach($cmsCompanyPages as $cPage)
                            <li>
                                <a href="{{ route('website.page', $cPage->slug) }}">
                                    {{ $cPage->title }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li><a href="{{ route('website.page', 'about-us') }}">About Us</a></li>
                        <li><a href="{{ route('website.page', 'contact-us') }}">Contact Us</a></li>
                    @endif
                    <li><a href="{{ url('/services') }}">Services</a></li>
                    <li><a href="{{ url('/products') }}">Products Catalog</a></li>
                </ul>
            </div>

            <!-- Col 3: ERP Solutions & Modules -->
            <div class="col-lg-2 col-md-3 col-6">
                <h5 class="footer-col-title">Solutions</h5>
                <ul class="footer-links">
                    @if(isset($footerModules) && $footerModules->count() > 0)
                        @foreach($footerModules as $fmod)
                            <li><a href="{{ $fmod->url }}">{{ $fmod->label }}</a></li>
                        @endforeach
                    @else
                        <li><a href="{{ route('home') }}#modules">Job Orders</a></li>
                        <li><a href="{{ route('home') }}#modules">Quotations</a></li>
                        <li><a href="{{ route('home') }}#modules">Invoices</a></li>
                        <li><a href="{{ route('home') }}#modules">Delivery Notes</a></li>
                        <li><a href="{{ route('home') }}#modules">Multi-Branch</a></li>
                    @endif
                </ul>
            </div>

            <!-- Col 4: Dynamic Legal & Policies -->
            <div class="col-lg-2 col-md-4 col-6">
                <h5 class="footer-col-title">Legal & Trust</h5>
                <ul class="footer-links">
                    @if($cmsLegalPages->count() > 0)
                        @foreach($cmsLegalPages as $lPage)
                            <li>
                                <a href="{{ route('website.page', $lPage->slug) }}">
                                    {{ $lPage->title }}
                                </a>
                            </li>
                        @endforeach
                    @else
                        <li><a href="{{ route('website.page', 'privacy-policy') }}">Privacy Policy</a></li>
                        <li><a href="{{ route('website.page', 'terms-conditions') }}">Terms & Conditions</a></li>
                        <li><a href="{{ route('website.page', 'refund-policy') }}">Refund Policy</a></li>
                    @endif
                    <li><a href="{{ route('home') }}#faq">Help & FAQ</a></li>
                </ul>
            </div>

            <!-- Col 5: Regional Contact Information (Pakistan & USA Only) -->
            <div class="col-lg-3 col-md-8 col-12">
                <div class="footer-contact-info-widget">
                    <h5 class="footer-col-title mb-3" style="font-size: 1.15rem; font-weight: 800; color: #FFFFFF; letter-spacing: -0.01em;">
                        Contact Information
                    </h5>

                    <!-- Regional Pill Switcher (Pakistan & USA) -->
                    <div class="footer-region-pills d-flex align-items-center gap-2 mb-3">
                        <button type="button" class="region-pill-btn active" data-region="pk">
                            PAKISTAN
                        </button>
                        <button type="button" class="region-pill-btn" data-region="usa">
                            USA
                        </button>
                    </div>

                    <!-- Region 1: PAKISTAN -->
                    <div class="region-info-pane active" id="pane-pk">
                        <div class="footer-info-row d-flex align-items-start gap-3 mb-3">
                            <div class="region-icon-wrap flex-shrink-0">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="region-text-wrap text-white">
                                <span class="d-block" style="line-height: 1.5; font-size: 0.9rem; font-weight: 500;">
                                    First Floor, Building No. 184-Commercial,<br>
                                    Airline Housing Society, Lahore, Pakistan
                                </span>
                            </div>
                        </div>

                        <div class="footer-info-row d-flex align-items-start gap-3">
                            <div class="region-icon-wrap flex-shrink-0">
                                <i class="fas fa-mobile-screen-button"></i>
                            </div>
                            <div class="region-text-wrap text-white">
                                <a href="tel:+923451438102" class="d-block text-white text-decoration-none fw-semibold mb-1" style="font-size: 0.95rem;">
                                    +92 3451438102
                                </a>
                                <a href="mailto:info@crelogics.com" class="d-block text-white text-decoration-none" style="font-size: 0.9rem; opacity: 0.9;">
                                    info@crelogics.com
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Region 2: USA -->
                    <div class="region-info-pane d-none" id="pane-usa">
                        <div class="footer-info-row d-flex align-items-start gap-3 mb-3">
                            <div class="region-icon-wrap flex-shrink-0">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <div class="region-text-wrap text-white">
                                <span class="d-block" style="line-height: 1.5; font-size: 0.9rem; font-weight: 500;">
                                    KALISPELL, MT 59901 USA.
                                </span>
                            </div>
                        </div>

                        <div class="footer-info-row d-flex align-items-start gap-3">
                            <div class="region-icon-wrap flex-shrink-0">
                                <i class="fas fa-mobile-screen-button"></i>
                            </div>
                            <div class="region-text-wrap text-white">
                                <a href="tel:+14062251210" class="d-block text-white text-decoration-none fw-semibold mb-1" style="font-size: 0.95rem;">
                                    +14062251210
                                </a>
                                <a href="mailto:info@crelogics.com" class="d-block text-white text-decoration-none" style="font-size: 0.9rem; opacity: 0.9;">
                                    info@crelogics.com
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom text-center justify-content-center">
            <div class="text-center w-100" style="color: #94A3B8; font-size: 0.88rem;">
                &copy; {{ date('Y') }} <strong>{{ !empty($settings['copyright_text']) ? $settings['copyright_text'] : 'Crelogics ERP. All rights reserved. Built with precision for modern business growth.' }}</strong>
            </div>
        </div>
    </div>
</footer>

<style>
/* Logo-Themed Modern Footer Column Headings */
.footer-col-title {
    color: #FFFFFF;
    font-size: 1.05rem;
    font-weight: 800;
    margin-bottom: 20px;
    letter-spacing: -0.01em;
    position: relative;
    display: inline-block;
    padding-bottom: 6px;
}
.footer-col-title::after {
    content: "";
    position: absolute;
    bottom: 0;
    left: 0;
    width: 26px;
    height: 3px;
    background: #FA7436; /* CreLogics Logo Orange Accent */
    border-radius: 2px;
}

/* Logo-Themed Button / Interactive Hover for Footer Links */
.footer-links {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.footer-links li a {
    color: #94A3B8;
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 12px;
    border-radius: 8px;
    border: 1px solid transparent;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    background: transparent;
}

.footer-links li a::before {
    content: "\f054"; /* FontAwesome chevron right */
    font-family: "Font Awesome 5 Free", "Font Awesome 6 Free";
    font-weight: 900;
    font-size: 0.65rem;
    color: rgba(250, 116, 54, 0.45);
    transition: all 0.25s ease;
}

/* Button-Like Hover matching CreLogics Logo Orange & Blue */
.footer-links li a:hover {
    color: #FFFFFF !important;
    background: rgba(250, 116, 54, 0.16) !important; /* Soft orange button pill */
    border-color: rgba(250, 116, 54, 0.4) !important;
    transform: translateX(4px);
    box-shadow: 0 4px 14px rgba(250, 116, 54, 0.25);
}

.footer-links li a:hover::before {
    color: #FA7436 !important;
    transform: translateX(2px);
}

/* Footer Bottom Bar Centered */
.footer-bottom {
    margin-top: 45px;
    padding-top: 24px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    text-align: center !important;
    width: 100%;
}
.footer-bottom div {
    text-align: center !important;
    color: #94A3B8;
    font-size: 0.88rem;
    letter-spacing: 0.01em;
}

/* Exact Matching Style for Regional Contact Information in Footer */
.footer-region-pills {
    flex-wrap: wrap;
}
.region-pill-btn {
    padding: 6px 18px;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.04em;
    text-transform: uppercase;
    border-radius: 8px;
    border: none;
    background-color: #FFE6C3; /* Soft light cream/peach unselected */
    color: #17243A;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.region-pill-btn:hover {
    background-color: #FFD8A8;
    transform: translateY(-1px);
}
.region-pill-btn.active {
    background-color: #FA7436 !important; /* Exact CreLogics Orange / Accent */
    color: #FFFFFF !important;
    box-shadow: 0 4px 12px rgba(250, 116, 54, 0.4);
}
.region-icon-wrap {
    width: 34px;
    height: 34px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #94A3B8;
    margin-top: 2px;
}
.region-info-pane {
    animation: fadeInRegion 0.3s ease-in-out;
}
@keyframes fadeInRegion {
    from { opacity: 0; transform: translateY(4px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pillButtons = document.querySelectorAll('.region-pill-btn');
    const panes = {
        'pk': document.getElementById('pane-pk'),
        'usa': document.getElementById('pane-usa')
    };

    pillButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const selectedRegion = this.getAttribute('data-region');

            pillButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            Object.keys(panes).forEach(reg => {
                if (panes[reg]) {
                    if (reg === selectedRegion) {
                        panes[reg].classList.remove('d-none');
                        panes[reg].classList.add('active');
                    } else {
                        panes[reg].classList.add('d-none');
                        panes[reg].classList.remove('active');
                    }
                }
            });
        });
    });
});
</script>
