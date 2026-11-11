@php
    $sec = $sections['showcase'] ?? null;
    $showTitle = $sec?->title ?: 'See Your Business in Action';
    $showSub = $sec?->subtitle ?: 'Watch how fast your team can draft quotes, track jobs through production, and collect client payments with zero friction.';
@endphp

<!-- Showcase Section: Interactive 5-Step Automated Workflow Timeline -->
<section class="showcase-section" id="showcase">
    <div class="container position-relative">
        <!-- Section Header -->
        <div class="text-center mb-4">
            <span class="badge-pill-soft mb-3">
                <i class="fas fa-play-circle text-primary me-1"></i> Interactive Automation
            </span>
            <h2 class="section-title text-dark fw-bold mb-3">{{ $showTitle }}</h2>
            <p class="section-subtitle text-muted mx-auto" style="max-width: 680px;">{{ $showSub }}</p>
        </div>

        <!-- 5 Connected Timeline Step Nodes in One Horizontal Row -->
        <div class="workflow-timeline-wrapper" id="workflowTimelineContainer">
            <div class="workflow-progress-track">
                <div class="workflow-progress-bar-fill" id="workflowProgressBar"></div>
            </div>
            <div class="workflow-steps-row" role="tablist">
                <!-- Step 1: Lead -->
                <button type="button" class="workflow-step-node active" data-step-target="step-lead" id="node-step-lead" aria-label="Step 1: Lead">
                    <div class="workflow-node-circle">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <span class="workflow-node-label">1. Lead</span>
                </button>
                <!-- Step 2: Deal -->
                <button type="button" class="workflow-step-node" data-step-target="step-deal" id="node-step-deal" aria-label="Step 2: Deal">
                    <div class="workflow-node-circle">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <span class="workflow-node-label">2. Deal</span>
                </button>
                <!-- Step 3: Proposal -->
                <button type="button" class="workflow-step-node" data-step-target="step-proposal" id="node-step-proposal" aria-label="Step 3: Proposal">
                    <div class="workflow-node-circle">
                        <i class="fas fa-file-signature"></i>
                    </div>
                    <span class="workflow-node-label">3. Proposal</span>
                </button>
                <!-- Step 4: Invoice -->
                <button type="button" class="workflow-step-node" data-step-target="step-invoice" id="node-step-invoice" aria-label="Step 4: Invoice">
                    <div class="workflow-node-circle">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <span class="workflow-node-label">4. Invoice</span>
                </button>
                <!-- Step 5: Payment -->
                <button type="button" class="workflow-step-node" data-step-target="step-payment" id="node-step-payment" aria-label="Step 5: Payment">
                    <div class="workflow-node-circle">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="workflow-node-label">5. Payment</span>
                </button>
            </div>
        </div>

        <!-- Interactive Split Content Card Below (Updates with Active Step) -->
        <div class="showcase-display-window shadow-sm" id="workflowContentCard">
            <!-- Window Topbar -->
            <div class="showcase-window-header">
                <div class="browser-dots">
                    <span class="browser-dot red"></span>
                    <span class="browser-dot yellow"></span>
                    <span class="browser-dot green"></span>
                </div>
                <div class="showcase-window-info">
                    <span class="badge-soft badge-soft-primary" style="font-size: 0.75rem;">
                        <i class="fas fa-laptop-code me-1"></i> Interactive ERP Workflow Preview
                    </span>
                    <span class="text-muted d-none d-sm-inline" style="font-size: 0.75rem;">Automated Sales-to-Cash Pipeline</span>
                </div>
            </div>

            <div class="showcase-body-content p-4">
                
                <!-- STEP 1: LEAD -->
                <div class="workflow-step-pane active" id="pane-step-lead">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <span class="badge-soft badge-soft-primary mb-2 fw-bold">Step 01 &bull; Lead Management</span>
                            <h3 class="fw-bold text-dark mb-3">Capture Inquiries & Track Potential Leads</h3>
                            <p class="text-muted mb-4" style="line-height: 1.6;">
                                Log client requirements, phone numbers, and job descriptions in seconds. Centralized contact histories ensure no customer lead gets overlooked.
                            </p>
                            <ul class="list-unstyled d-flex flex-column gap-2 text-muted mb-4" style="font-size: 0.92rem;">
                                <li><i class="fas fa-check-circle text-primary me-2"></i> Quick customer search by name, phone (+92), or company</li>
                                <li><i class="fas fa-check-circle text-primary me-2"></i> Lead source tagging (Walk-in, WhatsApp, Referral, Online)</li>
                                <li><i class="fas fa-check-circle text-primary me-2"></i> Instant transition from Lead to Deal pipeline</li>
                            </ul>
                            <button type="button" class="btn-erp-primary btn-sm workflow-next-btn px-4 py-2 rounded-pill" data-next="step-deal">
                                <span>Proceed to Deal &rarr;</span>
                            </button>
                        </div>
                        <div class="col-lg-7">
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-user-plus text-primary"></i> Lead Profile #LD-8821</h6>
                                    <span class="badge-soft badge-soft-primary">Qualified Lead</span>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="p-3 rounded-3 border mb-3" style="background: #F8FAFC;">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-dark fs-6">Ali Imran</strong>
                                            <span class="badge-soft badge-soft-secondary"><i class="fas fa-tag me-1"></i> Walk-in Customer</span>
                                        </div>
                                        <div class="row g-2 small text-muted">
                                            <div class="col-sm-6"><strong>Company:</strong> Imran Traders LLC</div>
                                            <div class="col-sm-6"><strong>Phone:</strong> +92-300-1111111</div>
                                            <div class="col-12 mt-2">
                                                <strong>Job Requirement:</strong> 3D Acrylic Raised Lettering & Frontlit Billboard
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-2 bg-light rounded d-flex flex-wrap justify-content-between align-items-center gap-2 small text-muted">
                                        <span><i class="fas fa-clock text-primary me-1"></i> Logged Today &bull; Assigned to Sales</span>
                                        <span class="badge-soft badge-soft-success"><i class="fas fa-bolt me-1"></i> Ready for Deal Conversion</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: DEAL -->
                <div class="workflow-step-pane" id="pane-step-deal">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <span class="badge-soft badge-soft-info mb-2 fw-bold">Step 02 &bull; Deal Management</span>
                            <h3 class="fw-bold text-dark mb-3">Analyze Requirements & Stage Pipeline</h3>
                            <p class="text-muted mb-4" style="line-height: 1.6;">
                                Define project scope, measure physical site dimensions, and manage deal negotiations through transparent pipeline stages.
                            </p>
                            <ul class="list-unstyled d-flex flex-column gap-2 text-muted mb-4" style="font-size: 0.92rem;">
                                <li><i class="fas fa-check-circle text-info me-2"></i> Multi-stage visual pipeline (Requirement &rarr; Negotiation &rarr; Won)</li>
                                <li><i class="fas fa-check-circle text-info me-2"></i> Site visit notes and dimension recording</li>
                                <li><i class="fas fa-check-circle text-info me-2"></i> Estimated revenue forecasting per sales rep</li>
                            </ul>
                            <button type="button" class="btn-erp-primary btn-sm workflow-next-btn px-4 py-2 rounded-pill" data-next="step-proposal">
                                <span>Proceed to Proposal &rarr;</span>
                            </button>
                        </div>
                        <div class="col-lg-7">
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-handshake text-info"></i> Deal Pipeline #DL-302</h6>
                                    <span class="badge-soft badge-soft-info">In Negotiation</span>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <span class="text-muted small d-block">Client: Imran Traders</span>
                                            <strong class="text-dark fs-5">$1,250.00</strong>
                                        </div>
                                        <span class="badge-soft badge-soft-success">70% Win Probability</span>
                                    </div>
                                    
                                    <!-- Pipeline Stage Progress Track -->
                                    <div class="p-3 rounded-3 border mb-3" style="background: #F8FAFC;">
                                        <div class="d-flex justify-content-between small text-muted mb-2">
                                            <span>Stage 1: Site Survey <i class="fas fa-check-circle text-success ms-1"></i></span>
                                            <strong class="text-primary">Stage 2: Negotiation (Active)</strong>
                                            <span>Stage 3: Deal Won</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: 70%;" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <span><i class="fas fa-user-tie text-muted me-1"></i> Rep: Waqas Ahmad (Sales)</span>
                                        <span class="text-primary fw-semibold">Next Action: Send Final Quotation &rarr;</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: PROPOSAL -->
                <div class="workflow-step-pane" id="pane-step-proposal">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <span class="badge-soft badge-soft-warning mb-2 fw-bold">Step 03 &bull; Proposal & Pricing</span>
                            <h3 class="fw-bold text-dark mb-3">Draft Dimensional Quotations & Send PDF</h3>
                            <p class="text-muted mb-4" style="line-height: 1.6;">
                                CreLogics automatically calculates width × height area formulas, applies tax rates, generates clean PDFs with company header, and shares instantly.
                            </p>
                            <ul class="list-unstyled d-flex flex-column gap-2 text-muted mb-4" style="font-size: 0.92rem;">
                                <li><i class="fas fa-check-circle text-warning me-2"></i> Square feet, meter, and custom piece pricing formulas</li>
                                <li><i class="fas fa-check-circle text-warning me-2"></i> Professional A4 PDF with terms and WhatsApp sharing</li>
                                <li><i class="fas fa-check-circle text-warning me-2"></i> 1-Click approval conversion directly to Invoice</li>
                            </ul>
                            <button type="button" class="btn-erp-primary btn-sm workflow-next-btn px-4 py-2 rounded-pill" data-next="step-invoice">
                                <span>Proceed to Invoice &rarr;</span>
                            </button>
                        </div>
                        <div class="col-lg-7">
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-file-signature text-warning"></i> Quotation #QT-2026-089</h6>
                                    <span class="badge-soft badge-soft-warning">Proposal Drafted</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table-modern">
                                        <thead>
                                            <tr>
                                                <th>Item Description</th>
                                                <th>Dimensions / Qty</th>
                                                <th>Unit Rate</th>
                                                <th class="text-end">Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">Frontlit Flex Banner 440gsm</div>
                                                    <div class="text-muted extra-small" style="font-size: 0.75rem;">Outdoor solvent printed flex</div>
                                                </td>
                                                <td>12 x 4 ft (48 sqft)</td>
                                                <td>$5.00/sqft</td>
                                                <td class="text-end fw-bold text-dark">$240.00</td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold text-dark">Fabrication Steel Mounting Frame</div>
                                                    <div class="text-muted extra-small" style="font-size: 0.75rem;">Square pipe structural frame</div>
                                                </td>
                                                <td>1 Standard Set</td>
                                                <td>$110.00/set</td>
                                                <td class="text-end fw-bold text-dark">$110.00</td>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr style="background: #F8FAFC;">
                                                <td colspan="3" class="text-end fw-bold text-dark">Proposal Total:</td>
                                                <td class="text-end fw-bold text-primary fs-6">$350.00</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="p-2 bg-light border-top d-flex justify-content-between align-items-center">
                                    <span class="small text-muted"><i class="fas fa-file-pdf text-danger me-1"></i> Professional A4 Layout</span>
                                    <span class="badge-soft badge-soft-primary"><i class="fab fa-whatsapp me-1"></i> Instant WhatsApp Share</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 4: INVOICE -->
                <div class="workflow-step-pane" id="pane-step-invoice">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <span class="badge-soft badge-soft-danger mb-2 fw-bold">Step 04 &bull; Invoicing & Billing</span>
                            <h3 class="fw-bold text-dark mb-3">Generate Tax Invoices & Track Balances</h3>
                            <p class="text-muted mb-4" style="line-height: 1.6;">
                                Convert accepted proposals into official sales tax invoices with sequential numbering, branch tax compliance, and automated ledger postings.
                            </p>
                            <ul class="list-unstyled d-flex flex-column gap-2 text-muted mb-4" style="font-size: 0.92rem;">
                                <li><i class="fas fa-check-circle text-danger me-2"></i> 1-Click conversion without retyping line items</li>
                                <li><i class="fas fa-check-circle text-danger me-2"></i> Real-time status tags: Unpaid &bull; Partially Paid &bull; Paid</li>
                                <li><i class="fas fa-check-circle text-danger me-2"></i> Thermal POS (80mm) and corporate A4 print layouts</li>
                            </ul>
                            <button type="button" class="btn-erp-primary btn-sm workflow-next-btn px-4 py-2 rounded-pill" data-next="step-payment">
                                <span>Proceed to Payment &rarr;</span>
                            </button>
                        </div>
                        <div class="col-lg-7">
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-file-invoice-dollar text-danger"></i> Sales Tax Invoice #INV-2026-1049</h6>
                                    <span class="badge-soft badge-soft-danger">Status: Unpaid</span>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="row g-2 mb-3 small">
                                        <div class="col-6"><strong>Billed Customer:</strong> Imran Traders</div>
                                        <div class="col-6"><strong>Invoice Date:</strong> {{ date('d M, Y') }}</div>
                                        <div class="col-6"><strong>Payment Terms:</strong> Net 15 Days</div>
                                        <div class="col-6"><strong>Due Date:</strong> {{ date('d M, Y', strtotime('+15 days')) }}</div>
                                    </div>

                                    <div class="p-3 rounded-3 border mb-3" style="background: #F8FAFC;">
                                        <div class="d-flex justify-content-between small py-1 border-bottom">
                                            <span class="text-muted">Billed Net Subtotal:</span>
                                            <strong class="text-dark">$350.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between small py-1 border-bottom">
                                            <span class="text-muted">Branch Sales Tax (0%):</span>
                                            <strong class="text-dark">$0.00</strong>
                                        </div>
                                        <div class="d-flex justify-content-between pt-2">
                                            <span class="fw-bold text-dark">Total Outstanding Due:</span>
                                            <strong class="text-danger fs-5">$350.00</strong>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center small text-muted">
                                        <span><i class="fas fa-print me-1"></i> Print Options:</span>
                                        <div class="d-flex gap-2">
                                            <span class="badge-soft badge-soft-secondary"><i class="fas fa-receipt me-1"></i> POS 80mm</span>
                                            <span class="badge-soft badge-soft-secondary"><i class="fas fa-file-pdf me-1"></i> A4 Corporate</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- STEP 5: PAYMENT -->
                <div class="workflow-step-pane" id="pane-step-payment">
                    <div class="row g-4 align-items-center">
                        <div class="col-lg-5">
                            <span class="badge-soft badge-soft-success mb-2 fw-bold">Step 05 &bull; Cash Realization</span>
                            <h3 class="fw-bold text-dark mb-3">Collect Payments & Print Clean Receipts</h3>
                            <p class="text-muted mb-4" style="line-height: 1.6;">
                                Record full or partial payments across cash, card, or bank transfer. Customer ledger balances clear immediately with automated receipt printing.
                            </p>
                            <ul class="list-unstyled d-flex flex-column gap-2 text-muted mb-4" style="font-size: 0.92rem;">
                                <li><i class="fas fa-check-circle text-success me-2"></i> Partial payment ledger with outstanding balances</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> Instant receipt printouts on thermal or standard printers</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i> Super Admin daily revenue reporting updates live</li>
                            </ul>
                            @auth
                                <a href="{{ route('dashboard') }}" class="btn-erp-primary btn-sm px-4 py-2 rounded-pill">
                                    <span>Open ERP Portal</span> <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="btn-erp-primary btn-sm px-4 py-2 rounded-pill">
                                    <span>Start 7-Day Free Trial</span> <i class="fas fa-arrow-right ms-1"></i>
                                </a>
                            @endauth
                        </div>
                        <div class="col-lg-7">
                            <div class="preview-card-wrap">
                                <div class="preview-card-header">
                                    <h6 class="preview-card-title"><i class="fas fa-check-circle text-success"></i> Cash Receipt #RCP-2026-884</h6>
                                    <span class="badge-soft badge-soft-success">Paid in Full</span>
                                </div>
                                <div class="p-3 bg-white">
                                    <div class="p-3 rounded-3 text-center mb-3" style="background: #E6FBF5; border: 1px solid rgba(0, 184, 148, 0.25);">
                                        <div class="fs-3 fw-bold text-success mb-1">$350.00 Received & Cleared</div>
                                        <div class="small text-muted">Method: Cash at Counter &bull; Processed by Main Branch Cashier</div>
                                    </div>
                                    <div class="p-2 bg-light rounded d-flex justify-content-between align-items-center mb-2 small">
                                        <span class="text-muted">Customer Outstanding Balance:</span>
                                        <span class="badge-soft badge-soft-success fw-bold">$0.00 (Cleared in Full)</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center small text-muted pt-1">
                                        <span><i class="fas fa-shield-alt text-success me-1"></i> General Ledger Updated</span>
                                        <span class="badge-soft badge-soft-primary"><i class="fas fa-receipt me-1"></i> Thermal Receipt Printed</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>
