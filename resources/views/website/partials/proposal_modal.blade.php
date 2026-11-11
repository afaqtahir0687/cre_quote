@php
    $modalTitle = $proposalSettings['proposal_modal_title'] ?? 'Get Your Free Custom Proposal';
    $modalSub = $proposalSettings['proposal_modal_subtitle'] ?? 'Answer 4 quick questions to receive a tailored digital roadmap and quote from CreLogics.';
    $submitBtnText = $proposalSettings['proposal_submit_btn_text'] ?? 'Send Proposal';
@endphp

<!-- Sleek Modern Proposal Modal (Styled matching CreLogics ERP Design System) -->
<div class="modal fade proposal-modal-backdrop" id="proposalModal" tabindex="-1" aria-labelledby="proposalModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 520px;">
        <div class="modal-content proposal-compact-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <!-- Compact Premium Header -->
            <div class="modal-header proposal-compact-header border-0 text-white py-3 px-4 position-relative">
                <div class="d-flex align-items-center gap-3">
                    <div class="proposal-mini-icon rounded-3 d-flex align-items-center justify-content-center text-white">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge-brand-pill">
                                CRELOGICS
                            </span>
                            <span class="text-white-50 small fw-semibold" id="stepCounterText">Step 1 of 4</span>
                        </div>
                        <h6 class="modal-title fw-bold text-white mb-0" id="proposalModalLabel" style="font-size: 1.05rem; letter-spacing: -0.01em;">
                            {{ $modalTitle }}
                        </h6>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
            </div>

            <!-- Sleek Modern Progress Bar & Step Tracker -->
            <div class="proposal-progress-wrap bg-light border-bottom px-4 py-2">
                <div class="d-flex justify-content-between text-muted mb-1" style="font-size: 0.72rem; font-weight: 700; letter-spacing: 0.02em;" id="stepIndicatorLabels">
                    <span class="step-label active" data-step="1"><i class="fas fa-circle-dot me-1"></i>1. Service</span>
                    <span class="step-label" data-step="2"><i class="far fa-circle me-1"></i>2. Goals</span>
                    <span class="step-label" data-step="3"><i class="far fa-circle me-1"></i>3. Budget</span>
                    <span class="step-label" data-step="4"><i class="far fa-circle me-1"></i>4. Contact</span>
                </div>
                <div class="progress" style="height: 5px; border-radius: 10px; background-color: #E2E8F0;">
                    <div class="progress-bar" id="proposalProgressBar" role="progressbar" style="width: 25%; background: linear-gradient(90deg, #0A6FB7, #FF8300); border-radius: 10px; transition: width 0.3s ease;"></div>
                </div>
            </div>

            <!-- Modal Body Form Area -->
            <div class="modal-body p-3 p-sm-4">
                <form id="proposalForm" novalidate>
                    @csrf

                    <!-- STEP 1: PRIMARY SERVICE -->
                    <div class="proposal-step" id="proposalStep1">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">What service are you interested in?</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.82rem;">Select your primary focus area to get an accurate estimate.</p>
                        </div>

                        <!-- 2-Column Service Selection Cards -->
                        <div class="row g-2 mb-3" id="serviceCardsContainer">
                            @if(isset($proposalServices) && $proposalServices->count() > 0)
                                @foreach($proposalServices as $srv)
                                    <div class="col-6">
                                        <div class="proposal-service-card p-2 rounded-3 cursor-pointer text-start transition-all" data-value="{{ $srv->name }}">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="service-card-icon rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 30px; height: 30px; background: rgba(67, 97, 238, 0.08); font-size: 0.85rem; flex-shrink: 0;">
                                                    <i class="{{ $srv->icon ?: 'fas fa-check' }}"></i>
                                                </div>
                                                <div class="flex-grow-1 text-truncate">
                                                    <div class="fw-bold text-dark small text-truncate" style="font-size: 0.82rem;">{{ $srv->name }}</div>
                                                </div>
                                                <div class="service-radio-check" style="font-size: 0.85rem;">
                                                    <i class="far fa-circle text-muted check-empty"></i>
                                                    <i class="fas fa-check-circle text-primary check-filled d-none"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-6"><div class="proposal-service-card p-2 rounded-3 cursor-pointer" data-value="SEO"><div class="fw-bold small">SEO</div></div></div>
                                <div class="col-6"><div class="proposal-service-card p-2 rounded-3 cursor-pointer" data-value="Google Ads"><div class="fw-bold small">Google Ads</div></div></div>
                                <div class="col-6"><div class="proposal-service-card p-2 rounded-3 cursor-pointer" data-value="Social Media"><div class="fw-bold small">Social Media</div></div></div>
                                <div class="col-6"><div class="proposal-service-card p-2 rounded-3 cursor-pointer" data-value="Web Design"><div class="fw-bold small">Web Design</div></div></div>
                            @endif
                        </div>

                        <!-- Service Select Dropdown -->
                        <div>
                            <select name="service" id="proposalServiceSelect" class="form-select form-control-erp" required>
                                <option value="" disabled selected>{{ __('Or select service from dropdown...') }}</option>
                                @if(isset($proposalServices) && $proposalServices->count() > 0)
                                    @foreach($proposalServices as $srv)
                                        <option value="{{ $srv->name }}">{{ $srv->name }}</option>
                                    @endforeach
                                @else
                                    <option value="SEO">SEO</option>
                                    <option value="Google Ads">Google Ads</option>
                                    <option value="Social Media">Social Media</option>
                                    <option value="Web Design">Web Design</option>
                                @endif
                            </select>
                            <div class="invalid-feedback small" id="step1Error">Please choose a service to proceed.</div>
                        </div>
                    </div>

                    <!-- STEP 2: LOCATION & CAMPAIGN GOALS -->
                    <div class="proposal-step d-none" id="proposalStep2">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Target Location & Goals</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.82rem;">Tell us where your market is and what you want to achieve.</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> Customer Location <span class="text-danger">*</span>
                            </label>
                            <select name="customer_location" id="proposalLocationSelect" class="form-select form-control-erp" required>
                                <option value="" disabled selected>Select location...</option>
                                @if(isset($proposalLocations) && $proposalLocations->count() > 0)
                                    @foreach($proposalLocations as $loc)
                                        <option value="{{ $loc->name }}">{{ $loc->name }}</option>
                                    @endforeach
                                @else
                                    <option value="Saudi Arabia (KSA)">Saudi Arabia (KSA)</option>
                                    <option value="United Arab Emirates (UAE)">United Arab Emirates (UAE)</option>
                                    <option value="Gulf / GCC Countries">Gulf / GCC Countries</option>
                                    <option value="USA & Canada">USA & Canada</option>
                                    <option value="UK & Europe">UK & Europe</option>
                                    <option value="Global / International">Global / International</option>
                                @endif
                            </select>
                            <div class="invalid-feedback small">Please select target location.</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-bullseye text-primary me-1"></i> Primary Campaign Goal <span class="text-danger">*</span>
                            </label>
                            <select name="campaign_goal" id="proposalGoalSelect" class="form-select form-control-erp" required>
                                <option value="" disabled selected>Select goal...</option>
                                @if(isset($proposalGoals) && $proposalGoals->count() > 0)
                                    @foreach($proposalGoals as $goal)
                                        <option value="{{ $goal->name }}">{{ $goal->name }}</option>
                                    @endforeach
                                @else
                                    <option value="Generate High-Quality Leads">Generate High-Quality Leads</option>
                                    <option value="Boost Online Sales & Revenue">Boost Online Sales & Revenue</option>
                                    <option value="Improve Search Engine Rankings (SEO)">Improve Search Engine Rankings (SEO)</option>
                                    <option value="Build Brand Awareness">Build Brand Awareness</option>
                                @endif
                            </select>
                            <div class="invalid-feedback small">Please select campaign goal.</div>
                        </div>
                    </div>

                    <!-- STEP 3: BUDGET & TIMELINE -->
                    <div class="proposal-step d-none" id="proposalStep3">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Budget & Timeline</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.82rem;">Helps us propose the optimal scope and milestone plan.</p>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-wallet text-success me-1"></i> Approximate Monthly Budget <span class="text-danger">*</span>
                            </label>
                            <select name="monthly_budget" id="proposalBudgetSelect" class="form-select form-control-erp" required>
                                <option value="" disabled selected>Select budget...</option>
                                @if(isset($proposalBudgets) && $proposalBudgets->count() > 0)
                                    @foreach($proposalBudgets as $bgt)
                                        <option value="{{ $bgt->name }}">{{ $bgt->name }}</option>
                                    @endforeach
                                @else
                                    <option value="Under $1,000 / month">Under $1,000 / month</option>
                                    <option value="$1,000 – $2,500 / month">$1,000 – $2,500 / month</option>
                                    <option value="$2,500 – $5,000 / month">$2,500 – $5,000 / month</option>
                                    <option value="$5,000 – $10,000 / month">$5,000 – $10,000 / month</option>
                                    <option value="$10,000+ / Enterprise Scale">$10,000+ / Enterprise Scale</option>
                                @endif
                            </select>
                            <div class="invalid-feedback small">Please select budget range.</div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">
                                <i class="fas fa-bolt text-warning me-1"></i> When to Get Started? <span class="text-danger">*</span>
                            </label>
                            <select name="starting_timeline" id="proposalTimelineSelect" class="form-select form-control-erp" required>
                                <option value="" disabled selected>Select starting timeline...</option>
                                @if(isset($proposalTimelines) && $proposalTimelines->count() > 0)
                                    @foreach($proposalTimelines as $tml)
                                        <option value="{{ $tml->name }}">{{ $tml->name }}</option>
                                    @endforeach
                                @else
                                    <option value="Immediately">Immediately (Ready now)</option>
                                    <option value="Within 1 to 2 Weeks">Within 1 to 2 Weeks</option>
                                    <option value="Next Month">Next Month</option>
                                    <option value="Exploring Options">Exploring Options & Researching</option>
                                @endif
                            </select>
                            <div class="invalid-feedback small">Please specify timeline.</div>
                        </div>
                    </div>

                    <!-- STEP 4: CONTACT INFORMATION -->
                    <div class="proposal-step d-none" id="proposalStep4">
                        <div class="text-center mb-3">
                            <h6 class="fw-bold text-dark mb-1" style="font-size: 1.05rem;">Where Should We Send Your Proposal?</h6>
                            <p class="text-muted small mb-0" style="font-size: 0.82rem;">Our senior strategist will review and email your proposal within 24 hours.</p>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" id="propFirstName" class="form-control form-control-erp" placeholder="John" required>
                                <div class="invalid-feedback small">Required.</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" id="propLastName" class="form-control form-control-erp" placeholder="Doe" required>
                                <div class="invalid-feedback small">Required.</div>
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" id="propEmail" class="form-control form-control-erp" placeholder="john@example.com" required>
                                <div class="invalid-feedback small">Valid email required.</div>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-bold text-dark small mb-1">Phone <span class="text-danger">*</span></label>
                                <input type="tel" name="phone" id="propPhone" class="form-control form-control-erp" placeholder="+966 50 123 4567" required>
                                <div class="invalid-feedback small">Phone required.</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-bold text-dark small mb-1">Website URL (Optional)</label>
                            <input type="url" name="website_url" id="propWebsite" class="form-control form-control-erp" placeholder="https://example.com">
                        </div>

                        <div class="p-2 rounded-3 bg-light border d-flex align-items-center gap-2 mt-3">
                            <i class="fas fa-lock text-success" style="font-size: 0.85rem;"></i>
                            <span class="text-muted" style="font-size: 0.74rem;">
                                100% Free & confidential. No spam, guaranteed.
                            </span>
                        </div>
                    </div>

                    <!-- SUCCESS CONFIRMATION SCREEN -->
                    <div class="proposal-step d-none text-center py-4" id="proposalSuccessScreen">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3" style="width: 60px; height: 60px; background: var(--clr-accent, #00B894); box-shadow: 0 8px 20px rgba(0, 184, 148, 0.35);">
                            <i class="fas fa-check fs-4"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-1" style="font-size: 1.2rem;">Proposal Request Submitted!</h5>
                        <div class="badge-ref-pill d-inline-block my-2">
                            Reference: <span id="successRefNo" class="fw-bold">#PROP-2026-0001</span>
                        </div>
                        <p class="text-muted small mb-4 px-3" style="font-size: 0.88rem; max-width: 420px; margin: 0 auto;" id="successMsgText">
                            Thank you! Your proposal request has been received. Our team will contact you within 24 hours.
                        </p>
                        <div>
                            <button type="button" class="btn-erp-primary" data-bs-dismiss="modal" style="padding: 10px 32px; font-size: 0.92rem;">
                                <i class="fas fa-check me-1"></i> Done
                            </button>
                        </div>
                    </div>

                </form>
            </div>

            <!-- Modal Footer Navigation (CreLogics ERP Standard Buttons) -->
            <div class="modal-footer proposal-compact-footer bg-light border-top py-2 px-3 d-flex justify-content-between" id="proposalModalFooter">
                <button type="button" class="btn-erp-secondary btn-sm-erp d-none" id="proposalBtnBack">
                    <i class="fas fa-arrow-left me-1"></i> Back
                </button>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <button type="button" class="btn-erp-secondary btn-sm-erp" data-bs-dismiss="modal">Cancel</button>
                    
                    <button type="button" class="btn-erp-primary btn-sm-erp" id="proposalBtnNext">
                        <span>Continue</span> <i class="fas fa-arrow-right ms-1"></i>
                    </button>
                    
                    <button type="button" class="btn-erp-primary btn-sm-erp d-none" id="proposalBtnSubmit" style="background: var(--clr-accent, #00B894); border-color: var(--clr-accent, #00B894); box-shadow: 0 6px 18px rgba(0, 184, 148, 0.3);">
                        <span class="btn-text"><i class="fas fa-paper-plane me-1"></i> {{ $submitBtnText }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
/* CreLogics Proposal Modal Theme Styling (Fully integrated with landing.css) */
.proposal-modal-backdrop {
    background-color: rgba(15, 23, 42, 0.75) !important;
    backdrop-filter: blur(8px) !important;
    -webkit-backdrop-filter: blur(8px) !important;
}

.proposal-compact-content {
    background-color: #FFFFFF;
    border: 1px solid #E2E8F0;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.18) !important;
}

.proposal-compact-header {
    background: linear-gradient(135deg, #0F1E36 0%, #0A6FB7 50%, #FF8300 100%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.proposal-mini-icon {
    width: 38px;
    height: 38px;
    background: rgba(255, 255, 255, 0.16);
    border: 1px solid rgba(255, 255, 255, 0.25);
    font-size: 0.95rem;
}

.badge-brand-pill {
    background: rgba(255, 255, 255, 0.95);
    color: #FF8300;
    font-weight: 800;
    font-size: 0.65rem;
    padding: 3px 8px;
    border-radius: 50px;
    letter-spacing: 0.05em;
    display: inline-block;
}

.badge-ref-pill {
    background: #EEF6FC;
    color: #0A6FB7;
    border: 1px solid rgba(10, 111, 183, 0.25);
    padding: 4px 12px;
    border-radius: 50px;
    font-size: 0.78rem;
}

.step-label {
    transition: color 0.2s ease;
    color: #94A3B8;
}

.step-label.active {
    color: #0A6FB7 !important;
}

.step-label.completed {
    color: #FF8300 !important;
}

/* Service Selection Cards */
.proposal-service-card {
    background: #FFFFFF;
    border: 1.5px solid #E2E8F0;
    transition: all 0.2s ease;
}

.proposal-service-card:hover {
    border-color: #0A6FB7 !important;
    background: #F8FAFD;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(10, 111, 183, 0.08);
}

.proposal-service-card.selected {
    border-color: #FF8300 !important;
    background: #FFF4E6 !important;
    box-shadow: 0 0 0 2px rgba(255, 131, 0, 0.25) !important;
}

.proposal-service-card.selected .check-empty {
    display: none !important;
}

.proposal-service-card.selected .check-filled {
    display: inline-block !important;
}

/* Form Controls matched with project style */
.form-control-erp {
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 9px 14px;
    font-size: 0.88rem;
    color: #17243A;
    background-color: #FFFFFF;
    transition: all 0.2s ease;
}

.form-control-erp:focus {
    border-color: #4361EE;
    box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
    outline: none;
    background-color: #FFFFFF;
}

/* Modal Compact Buttons using exact btn-erp specs */
.btn-sm-erp {
    padding: 9px 20px !important;
    font-size: 0.86rem !important;
    border-radius: 10px !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 4;

    const step1 = document.getElementById('proposalStep1');
    const step2 = document.getElementById('proposalStep2');
    const step3 = document.getElementById('proposalStep3');
    const step4 = document.getElementById('proposalStep4');
    const successScreen = document.getElementById('proposalSuccessScreen');
    const modalFooter = document.getElementById('proposalModalFooter');

    const btnNext = document.getElementById('proposalBtnNext');
    const btnBack = document.getElementById('proposalBtnBack');
    const btnSubmit = document.getElementById('proposalBtnSubmit');
    const progressBar = document.getElementById('proposalProgressBar');
    const stepCounterText = document.getElementById('stepCounterText');

    const serviceSelect = document.getElementById('proposalServiceSelect');
    const serviceCards = document.querySelectorAll('.proposal-service-card');

    // Service card sync with select
    serviceCards.forEach(card => {
        card.addEventListener('click', function() {
            serviceCards.forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            const val = this.getAttribute('data-value');
            if (serviceSelect) {
                serviceSelect.value = val;
                serviceSelect.classList.remove('is-invalid');
            }
        });
    });

    if (serviceSelect) {
        serviceSelect.addEventListener('change', function() {
            const val = this.value;
            serviceCards.forEach(c => {
                if (c.getAttribute('data-value') === val) {
                    c.classList.add('selected');
                } else {
                    c.classList.remove('selected');
                }
            });
            this.classList.remove('is-invalid');
        });
    }

    function updateStepView() {
        [step1, step2, step3, step4].forEach((s, idx) => {
            if (idx + 1 === currentStep) {
                s.classList.remove('d-none');
            } else {
                s.classList.add('d-none');
            }
        });

        const pct = (currentStep / totalSteps) * 100;
        progressBar.style.width = pct + '%';
        stepCounterText.textContent = `Step ${currentStep} of ${totalSteps}`;

        document.querySelectorAll('#stepIndicatorLabels .step-label').forEach(lbl => {
            const stepNum = parseInt(lbl.getAttribute('data-step'));
            const icon = lbl.querySelector('i');
            if (stepNum === currentStep) {
                lbl.classList.add('active');
                lbl.classList.remove('completed');
                if (icon) icon.className = 'fas fa-circle-dot me-1';
            } else if (stepNum < currentStep) {
                lbl.classList.add('completed');
                lbl.classList.remove('active');
                if (icon) icon.className = 'fas fa-check-circle me-1';
            } else {
                lbl.classList.remove('active', 'completed');
                if (icon) icon.className = 'far fa-circle me-1';
            }
        });

        if (currentStep === 1) {
            btnBack.classList.add('d-none');
        } else {
            btnBack.classList.remove('d-none');
        }

        if (currentStep === totalSteps) {
            btnNext.classList.add('d-none');
            btnSubmit.classList.remove('d-none');
        } else {
            btnNext.classList.remove('d-none');
            btnSubmit.classList.add('d-none');
        }
    }

    function validateStep(step) {
        let isValid = true;

        if (step === 1) {
            if (!serviceSelect.value) {
                serviceSelect.classList.add('is-invalid');
                isValid = false;
            } else {
                serviceSelect.classList.remove('is-invalid');
            }
        } else if (step === 2) {
            const locSelect = document.getElementById('proposalLocationSelect');
            const goalSelect = document.getElementById('proposalGoalSelect');

            if (!locSelect.value) {
                locSelect.classList.add('is-invalid');
                isValid = false;
            } else {
                locSelect.classList.remove('is-invalid');
            }

            if (!goalSelect.value) {
                goalSelect.classList.add('is-invalid');
                isValid = false;
            } else {
                goalSelect.classList.remove('is-invalid');
            }
        } else if (step === 3) {
            const bgtSelect = document.getElementById('proposalBudgetSelect');
            const tmlSelect = document.getElementById('proposalTimelineSelect');

            if (!bgtSelect.value) {
                bgtSelect.classList.add('is-invalid');
                isValid = false;
            } else {
                bgtSelect.classList.remove('is-invalid');
            }

            if (!tmlSelect.value) {
                tmlSelect.classList.add('is-invalid');
                isValid = false;
            } else {
                tmlSelect.classList.remove('is-invalid');
            }
        } else if (step === 4) {
            const fName = document.getElementById('propFirstName');
            const lName = document.getElementById('propLastName');
            const email = document.getElementById('propEmail');
            const phone = document.getElementById('propPhone');

            if (!fName.value.trim()) {
                fName.classList.add('is-invalid');
                isValid = false;
            } else {
                fName.classList.remove('is-invalid');
            }

            if (!lName.value.trim()) {
                lName.classList.add('is-invalid');
                isValid = false;
            } else {
                lName.classList.remove('is-invalid');
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email.value.trim() || !emailRegex.test(email.value.trim())) {
                email.classList.add('is-invalid');
                isValid = false;
            } else {
                email.classList.remove('is-invalid');
            }

            if (!phone.value.trim()) {
                phone.classList.add('is-invalid');
                isValid = false;
            } else {
                phone.classList.remove('is-invalid');
            }
        }

        return isValid;
    }

    btnNext.addEventListener('click', function() {
        if (validateStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStepView();
            }
        }
    });

    btnBack.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepView();
        }
    });

    btnSubmit.addEventListener('click', function() {
        if (!validateStep(4)) return;

        const form = document.getElementById('proposalForm');
        const formData = new FormData(form);

        btnSubmit.disabled = true;
        btnSubmit.querySelector('.btn-text').classList.add('d-none');
        btnSubmit.querySelector('.spinner-border').classList.remove('d-none');

        const csrfToken = document.querySelector('meta[name="csrf-token"]') 
            ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
            : (document.querySelector('input[name="_token"]') ? document.querySelector('input[name="_token"]').value : '');

        fetch("{{ route('proposal.submit') }}", {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(response => response.json().then(data => ({ status: response.status, body: data })))
        .then(({ status, body }) => {
            btnSubmit.disabled = false;
            btnSubmit.querySelector('.btn-text').classList.remove('d-none');
            btnSubmit.querySelector('.spinner-border').classList.add('d-none');

            if (body.success) {
                document.getElementById('successRefNo').textContent = '#' + (body.reference_no || 'RECEIVED');
                if (body.message) {
                    document.getElementById('successMsgText').textContent = body.message;
                }

                [step1, step2, step3, step4].forEach(s => s.classList.add('d-none'));
                modalFooter.classList.add('d-none');
                successScreen.classList.remove('d-none');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Proposal Request Received!',
                        text: body.message,
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true
                    });
                }
            } else {
                let errText = body.message || 'There was an issue submitting your proposal.';
                if (body.errors) {
                    errText = Object.values(body.errors).flat().join('\n');
                }
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Issue',
                        text: errText,
                        confirmButtonColor: '#4361EE'
                    });
                } else {
                    alert(errText);
                }
            }
        })
        .catch(err => {
            btnSubmit.disabled = false;
            btnSubmit.querySelector('.btn-text').classList.remove('d-none');
            btnSubmit.querySelector('.spinner-border').classList.add('d-none');
            console.error('Proposal submit error:', err);
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Network Error',
                    text: 'A network error occurred. Please check your connection and try again.',
                    confirmButtonColor: '#4361EE'
                });
            } else {
                alert('A network error occurred. Please check your connection and try again.');
            }
        });
    });

    const modalEl = document.getElementById('proposalModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            currentStep = 1;
            successScreen.classList.add('d-none');
            modalFooter.classList.remove('d-none');
            updateStepView();
        });
    }

    updateStepView();
});
</script>
