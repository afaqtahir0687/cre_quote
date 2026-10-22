<!-- Shop Status Change Modal (Inactive / Suspended / Active) -->
<div class="modal fade" id="shopStatusModal" tabindex="-1" aria-labelledby="shopStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form id="shopStatusForm" method="POST" action="">
                @csrf
                <input type="hidden" name="status" id="modalStatusInput" value="inactive">

                <div class="modal-header border-bottom py-3 px-4" id="modalHeaderBg">
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="shopStatusModalLabel">
                            <i class="fas fa-store-alt me-2 text-primary" id="modalIcon"></i>
                            <span id="modalTitleText">Update Shop Operating Status</span>
                        </h5>
                        <p class="text-muted small mb-0 mt-1">Shop: <strong id="modalShopName" class="text-dark">---</strong></p>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Dynamic Alert Banner based on target status -->
                    <div class="p-3 rounded-3 mb-4 border" id="modalAlertBox">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-exclamation-triangle fa-2x me-3" id="modalAlertIcon"></i>
                            <div>
                                <div class="fw-bold" id="modalAlertHeading">Temporary Account Deactivation</div>
                                <div class="small opacity-90" id="modalAlertDesc">
                                    The shop will be temporarily deactivated. Staff will not be able to log in, but can be reactivated once the issue is settled.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fields for Inactive / Suspended -->
                    <div id="statusDetailsFields">
                        <div class="row g-3">
                            <!-- Preset Reason Dropdown -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Primary Reason *</label>
                                <select class="form-select" id="presetReasonSelect" onchange="applyPresetReason(this.value)">
                                    <option value="Overdue Monthly Subscription / Platform Fee">Overdue Monthly Subscription / Platform Fee</option>
                                    <option value="Pending Invoice Payment / Unpaid Dues">Pending Invoice Payment / Unpaid Dues</option>
                                    <option value="Commercial Registration / ID Verification Pending">Commercial Registration / ID Verification Pending</option>
                                    <option value="Terms of Service & Policy Review">Terms of Service & Policy Review</option>
                                    <option value="Scheduled Maintenance / Administrative Hold">Scheduled Maintenance / Administrative Hold</option>
                                    <option value="custom">Other / Custom Reason...</option>
                                </select>
                            </div>

                            <!-- Custom Reason Input -->
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-dark">Reason Description *</label>
                                <input type="text" name="status_reason" id="statusReasonInput" class="form-control" placeholder="e.g. Unpaid subscription invoice #INV-102" value="Overdue Monthly Subscription / Platform Fee" required>
                            </div>

                            <!-- Grace Period / Deadline Section (Shown only for Inactive) -->
                            <div class="col-12" id="gracePeriodContainer">
                                <div class="p-3 rounded-3 bg-light border">
                                    <label class="form-label fw-bold text-dark mb-2">
                                        <i class="fas fa-hourglass-half text-warning me-1"></i> Payment Grace Period & Deadline
                                    </label>
                                    <div class="row g-3">
                                        <div class="col-md-5">
                                            <label class="small text-muted fw-semibold">Days to Pay / Resolve</label>
                                            <select name="grace_period_days" class="form-select" id="gracePeriodDaysSelect" onchange="calculateDeadlineFromDays(this.value)">
                                                <option value="3">3 Days (Urgent)</option>
                                                <option value="7" selected>7 Days (Standard Grace Period)</option>
                                                <option value="14">14 Days (Two Weeks)</option>
                                                <option value="30">30 Days (One Month)</option>
                                                <option value="custom">Custom Date Picker</option>
                                            </select>
                                        </div>
                                        <div class="col-md-7">
                                            <label class="small text-muted fw-semibold">Final Deadline Date</label>
                                            <input type="date" name="action_deadline" id="actionDeadlineInput" class="form-control" value="{{ now()->addDays(7)->format('Y-m-d') }}">
                                        </div>
                                    </div>
                                    <div class="small text-danger mt-2" id="deadlineNotice">
                                        <i class="fas fa-info-circle me-1"></i>
                                        If payment is not received before this date, the shop will be eligible for <strong>permanent suspension</strong>.
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Message / Instructions -->
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark">
                                    Instructions for Shop Owner / Message in Email
                                </label>
                                <textarea name="status_message" id="statusMessageInput" rows="3" class="form-control" placeholder="Explain the next steps, banking details, or how they can pay to restore access..."></textarea>
                                <small class="text-muted">This message will be prominently highlighted in their email notification and shown on their login screen.</small>
                            </div>

                            <!-- Email Notification Option -->
                            <div class="col-12">
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="send_email" id="sendEmailCheckbox" value="1" checked>
                                    <label class="form-check-label fw-semibold text-dark" for="sendEmailCheckbox">
                                        <i class="fas fa-envelope text-primary me-1"></i> Send official notification email to shop & administrators
                                    </label>
                                    <div class="small text-muted ms-4">
                                        Will dispatch a formatted notice with reasons, deadline, and payment instructions.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top py-3 px-4 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-modern" id="modalSubmitBtn">
                        <i class="fas fa-check-circle me-1"></i> Confirm & Apply Status
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openStatusModal(shopId, shopName, targetStatus, currentReason = '', currentGraceDays = 7, currentDeadline = '', currentMsg = '') {
        const form = document.getElementById('shopStatusForm');
        form.action = "{{ url('super-admin/shops') }}/" + shopId + "/update-status";

        document.getElementById('modalShopName').innerText = shopName;
        document.getElementById('modalStatusInput').value = targetStatus;

        const titleText = document.getElementById('modalTitleText');
        const modalIcon = document.getElementById('modalIcon');
        const alertBox = document.getElementById('modalAlertBox');
        const alertIcon = document.getElementById('modalAlertIcon');
        const alertHeading = document.getElementById('modalAlertHeading');
        const alertDesc = document.getElementById('modalAlertDesc');
        const submitBtn = document.getElementById('modalSubmitBtn');
        const graceContainer = document.getElementById('gracePeriodContainer');
        const reasonInput = document.getElementById('statusReasonInput');
        const msgInput = document.getElementById('statusMessageInput');
        const deadlineInput = document.getElementById('actionDeadlineInput');
        const graceSelect = document.getElementById('gracePeriodDaysSelect');

        if (targetStatus === 'inactive') {
            titleText.innerText = "Temporarily Deactivate Shop (Inactive)";
            modalIcon.className = "fas fa-pause-circle me-2 text-warning";
            alertBox.className = "p-3 rounded-3 mb-4 border bg-warning bg-opacity-10 border-warning-subtle text-dark";
            alertIcon.className = "fas fa-exclamation-triangle fa-2x me-3 text-warning";
            alertHeading.innerText = "Temporary Deactivation (Payment / Policy Issue)";
            alertDesc.innerText = "Shop staff cannot log in until payment or compliance is settled. A grace period allows them to clear dues before permanent suspension.";
            
            graceContainer.style.display = 'block';
            submitBtn.className = "btn btn-warning text-dark fw-bold px-4";
            submitBtn.innerHTML = '<i class="fas fa-pause me-1"></i> Set Inactive & Send Notice';

            reasonInput.value = currentReason || "Overdue Monthly Subscription / Platform Fee";
            msgInput.value = currentMsg || `Dear ${shopName} Team,\nYour branch account access has been temporarily placed on hold due to pending payment. Please clear the pending balance within the grace period to avoid permanent suspension. Thank you.`;
            
            if (currentDeadline) {
                deadlineInput.value = currentDeadline;
            } else {
                calculateDeadlineFromDays(7);
            }
        } else if (targetStatus === 'suspended') {
            titleText.innerText = "Permanently Suspend Shop (Block Access)";
            modalIcon.className = "fas fa-ban me-2 text-danger";
            alertBox.className = "p-3 rounded-3 mb-4 border bg-danger bg-opacity-10 border-danger-subtle text-danger";
            alertIcon.className = "fas fa-shield-alt fa-2x me-3 text-danger";
            alertHeading.innerText = "Permanent Suspension Warning";
            alertDesc.innerText = "This will immediately terminate all access for this shop and its employees. Data will remain archived.";
            
            graceContainer.style.display = 'none';
            submitBtn.className = "btn btn-danger fw-bold px-4";
            submitBtn.innerHTML = '<i class="fas fa-ban me-1"></i> Suspend Shop Permanently';

            reasonInput.value = currentReason || "Unresolved Payment Default / Policy Violation";
            msgInput.value = currentMsg || `Dear ${shopName} Team,\nYour branch operations and access have been suspended by Platform Super Admin due to non-payment or terms violation.`;
        } else if (targetStatus === 'active') {
            titleText.innerText = "Re-Activate Shop";
            modalIcon.className = "fas fa-play-circle me-2 text-success";
            alertBox.className = "p-3 rounded-3 mb-4 border bg-success bg-opacity-10 border-success-subtle text-success";
            alertIcon.className = "fas fa-check-circle fa-2x me-3 text-success";
            alertHeading.innerText = "Restore Operating Access";
            alertDesc.innerText = "This will reactivate the shop immediately. Staff will be able to log in and all operations will resume.";

            graceContainer.style.display = 'none';
            submitBtn.className = "btn btn-success fw-bold px-4";
            submitBtn.innerHTML = '<i class="fas fa-play me-1"></i> Re-Activate Shop';

            reasonInput.value = "";
            msgInput.value = `Dear ${shopName} Team,\nYour payment has been received and verified. Your branch account has been successfully re-activated.`;
        }

        const modal = new bootstrap.Modal(document.getElementById('shopStatusModal'));
        modal.show();
    }

    function applyPresetReason(val) {
        const input = document.getElementById('statusReasonInput');
        if (val !== 'custom') {
            input.value = val;
        } else {
            input.value = '';
            input.focus();
        }
    }

    function calculateDeadlineFromDays(days) {
        const deadlineInput = document.getElementById('actionDeadlineInput');
        if (days === 'custom') {
            deadlineInput.focus();
            return;
        }
        const d = new Date();
        d.setDate(d.getDate() + parseInt(days));
        const formatted = d.toISOString().split('T')[0];
        deadlineInput.value = formatted;
    }
</script>
