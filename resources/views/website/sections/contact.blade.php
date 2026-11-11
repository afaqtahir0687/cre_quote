@php
    $sec = $sections['contact'] ?? null;
    $contactTitle = $sec?->title ?: 'Get in Touch With Our Team';
    $contactSub = $sec?->subtitle ?: 'Have questions or need a custom demonstration? We are here to help.';
@endphp

<section class="contact-section bg-white position-relative" id="contact">
    <div class="container">
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="section-tag">Direct Communication</span>
            <h2 class="section-title">{{ $contactTitle }}</h2>
            <p class="section-subtitle">{{ $contactSub }}</p>
        </div>

        <div class="row g-5 justify-content-center">
            <!-- Left Info Column -->
            <div class="col-lg-5">
                <div class="p-4 p-lg-5 rounded-4 h-100" style="background: var(--clr-light); border: 1px solid var(--clr-border);">
                    <h4 class="fw-bold text-dark mb-4">Contact Information</h4>
                    
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 rounded-circle bg-white shadow-sm text-primary fs-5" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Email Address</div>
                            <a href="mailto:{{ $settings['contact_email'] ?? 'support@crelogics.com' }}" class="fw-bold text-dark text-decoration-none">
                                {{ $settings['contact_email'] ?? 'support@crelogics.com' }}
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 rounded-circle bg-white shadow-sm text-primary fs-5" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Direct Line</div>
                            <span class="fw-bold text-dark">
                                {{ $settings['contact_phone'] ?? '+996 598 500 143' }}
                            </span>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 mb-4">
                        <div class="p-3 rounded-circle bg-white shadow-sm text-success fs-5" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <div>
                            <div class="text-muted small">WhatsApp Support</div>
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp_number'] ?? '996598500143') }}" target="_blank" class="fw-bold text-success text-decoration-none">
                                Instant Chat on WhatsApp &rarr;
                            </a>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3">
                        <div class="p-3 rounded-circle bg-white shadow-sm text-primary fs-5" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <div class="text-muted small">Office Location</div>
                            <span class="fw-bold text-dark">
                                {{ $settings['company_address'] ?? 'Business District, Modern Industrial Zone' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Form Column -->
            <div class="col-lg-7">
                <div class="p-4 p-lg-5 rounded-4 shadow-sm border bg-white">
                    <form action="{{ route('contact.submit') }}" method="POST" id="contactUsForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Your Full Name *</label>
                                <input type="text" name="name" class="form-control" required placeholder="e.g. Ali Imran">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Your Email Address *</label>
                                <input type="email" name="email" class="form-control" required placeholder="ali@example.com">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Phone Number</label>
                                <input type="text" name="phone" class="form-control" placeholder="+92-300-1111111">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="Inquiry about Multi-Branch ERP">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small">Your Message *</label>
                                <textarea name="message" class="form-control" rows="5" required placeholder="Tell us about your business requirements..."></textarea>
                            </div>
                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn-erp-primary py-3 px-5">
                                    <span>Send Message</span>
                                    <i class="fas fa-paper-plane ms-1"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

