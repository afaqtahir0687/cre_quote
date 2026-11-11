@php
    $logo = !empty($settings['website_logo']) ? asset($settings['website_logo']) : (platform_logo_url() ?: asset('uploads/logo/crelogics-logo.png'));
    $footerDesc = $settings['footer_description'] ?? 'A complete, modern cloud ERP and business management system engineered to streamline job orders, quotations, invoices, multi-branch tracking, and inventory fulfillment.';
    $contactEmail = $settings['contact_email'] ?? 'info@crelogics.com';
    $whatsapp = $settings['whatsapp_number'] ?? '923451438102';
    $cleanWhatsapp = preg_replace('/[^0-9]/', '', $whatsapp);
    $copyright = $settings['copyright_text'] ?? (date('Y') . ' CreLogics. All rights reserved.');
@endphp
<footer class="erp-footer">
    <div class="container">
        <div class="row g-4 justify-content-between">
            <!-- Brand & Info -->
            <div class="col-lg-3 col-md-6">
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

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-3 col-6">
                <h5 class="footer-col-title">Navigation</h5>
                <ul class="footer-links">
                    @if(isset($footerNav) && $footerNav->count() > 0)
                        @foreach($footerNav as $fnav)
                            <li><a href="{{ $fnav->url }}" target="{{ $fnav->target ?: '_self' }}">{{ $fnav->label }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#features">Core Features</a></li>
                        <li><a href="#modules">ERP Modules</a></li>
                        <li><a href="#showcase">Live Workflow</a></li>
                        <li><a href="#solutions">Industry Solutions</a></li>
                        <li><a href="#pricing">Plans & Pricing</a></li>
                        <li><a href="#faq">Frequently Asked</a></li>
                    @endif
                </ul>
            </div>

            <!-- ERP Modules -->
            <div class="col-lg-2 col-md-3 col-6">
                <h5 class="footer-col-title">Solutions</h5>
                <ul class="footer-links">
                    @if(isset($footerModules) && $footerModules->count() > 0)
                        @foreach($footerModules as $fmod)
                            <li><a href="{{ $fmod->url }}"><i class="fas fa-chevron-right fa-xs me-1 text-primary"></i> {{ $fmod->label }}</a></li>
                        @endforeach
                    @else
                        <li><a href="#modules"><i class="fas fa-chevron-right fa-xs me-1 text-primary"></i> Job Orders</a></li>
                        <li><a href="#modules"><i class="fas fa-chevron-right fa-xs me-1 text-primary"></i> Quotations</a></li>
                        <li><a href="#modules"><i class="fas fa-chevron-right fa-xs me-1 text-primary"></i> Invoices</a></li>
                        <li><a href="#modules"><i class="fas fa-chevron-right fa-xs me-1 text-primary"></i> Delivery Notes</a></li>
                        <li><a href="#modules"><i class="fas fa-chevron-right fa-xs me-1 text-primary"></i> Multi-Branch</a></li>
                    @endif
                </ul>
            </div>

            <!-- Regional Contact Information (Pakistan & USA Only) -->
            <div class="col-lg-5 col-md-12">
                <div class="footer-contact-info-widget">
                    <h5 class="footer-col-title mb-3" style="font-size: 1.25rem; font-weight: 800; color: #FFFFFF; letter-spacing: -0.01em;">
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
                                <span class="d-block" style="line-height: 1.5; font-size: 0.95rem; font-weight: 500;">
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
                                <a href="tel:+923451438102" class="d-block text-white text-decoration-none fw-semibold mb-1" style="font-size: 1rem;">
                                    +92 3451438102
                                </a>
                                <a href="mailto:info@crelogics.com" class="d-block text-white text-decoration-none" style="font-size: 0.95rem; opacity: 0.9;">
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
                                <span class="d-block" style="line-height: 1.5; font-size: 0.95rem; font-weight: 500;">
                                    KALISPELL, MT 59901 USA.
                                </span>
                            </div>
                        </div>

                        <div class="footer-info-row d-flex align-items-start gap-3">
                            <div class="region-icon-wrap flex-shrink-0">
                                <i class="fas fa-mobile-screen-button"></i>
                            </div>
                            <div class="region-text-wrap text-white">
                                <a href="tel:+14062251210" class="d-block text-white text-decoration-none fw-semibold mb-1" style="font-size: 1rem;">
                                    +14062251210
                                </a>
                                <a href="mailto:info@crelogics.com" class="d-block text-white text-decoration-none" style="font-size: 0.95rem; opacity: 0.9;">
                                    info@crelogics.com
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer Bottom Bar -->
        <div class="footer-bottom">
            <div>
                &copy; {{ date('Y') }} <strong>{{ $copyright }}</strong>
            </div>
            <div class="d-flex align-items-center gap-3">
                <a href="#features">Privacy & Terms</a>
                <span>&bull;</span>
                <a href="#pricing">Subscription Policies</a>
                <span>&bull;</span>
                <a href="https://wa.me/{{ $cleanWhatsapp }}" target="_blank"><i class="fab fa-whatsapp me-1 text-success"></i> WhatsApp Support</a>
            </div>
        </div>
    </div>
</footer>

<style>
/* Exact Matching Style for Regional Contact Information in Footer */
.footer-region-pills {
    flex-wrap: wrap;
}
.region-pill-btn {
    padding: 7px 22px;
    font-size: 0.8rem;
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
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.45rem;
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
