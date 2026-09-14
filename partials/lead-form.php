<?php
/**
 * RAFly Project Intake Console — Shared Lead System
 * 
 * Rendered across Homepage, /contact, Service pages, and Consultation Modal.
 * Left 40%: Real-Time Intake Telemetry & Signal Pipeline
 * Right 60%: 3-Step Intake Environment (01 CONTACT, 02 SCOPE, 03 VERIFY)
 */
$formId        = $formId        ?? 'leadForm';
$submitLabel   = $submitLabel   ?? 'SEND PROJECT BRIEF';
$compact       = $compact       ?? false;
$hideTelemetry = $hideTelemetry ?? false;
?>
<div class="intake-console-wrapper <?= $hideTelemetry ? 'is-single-column' : '' ?>" id="<?= e($formId) ?>-wrapper">
    <div class="intake-console-grid">
        <?php if (!$hideTelemetry): ?>
        <!-- LEFT 40%: TELEMETRY & SIGNAL PIPELINE (DESKTOP) -->
        <div class="intake-telemetry-side">
            <div class="intake-status-pill">
                <span class="intake-pulse">●</span> DIRECT BUILD CHANNEL
            </div>
            
            <h3 class="intake-side-title">PROJECT INTAKE SYSTEM</h3>
            <p class="intake-side-sub">Configure your project requirements and receive a scoped milestone proposal within 24 business hours.</p>

            <!-- SIGNAL PIPELINE DIAGRAM -->
            <div class="intake-signal-pipeline" aria-hidden="true">
                <svg class="intake-signal-svg" viewBox="0 0 280 80" fill="none">
                    <path class="signal-line" d="M 30,40 L 90,40 L 150,40 L 210,40 L 250,40" stroke="#cbd5e1" stroke-width="2" stroke-dasharray="4 4" />
                    <path class="signal-line-active" d="M 30,40 L 90,40" stroke="#0a63ff" stroke-width="3" stroke-linecap="round" />
                    
                    <circle class="signal-node node-1 active" cx="30" cy="40" r="7" fill="#0a63ff" />
                    <circle class="signal-node node-2" cx="90" cy="40" r="7" fill="#cbd5e1" />
                    <circle class="signal-node node-3" cx="150" cy="40" r="7" fill="#cbd5e1" />
                    <circle class="signal-node node-4" cx="210" cy="40" r="7" fill="#cbd5e1" />
                    
                    <text x="30" y="65" font-family="monospace" font-size="9" fill="#0a63ff" text-anchor="middle" font-weight="bold">PROJECT</text>
                    <text x="90" y="65" font-family="monospace" font-size="9" fill="#64748b" text-anchor="middle">SCOPE</text>
                    <text x="150" y="65" font-family="monospace" font-size="9" fill="#64748b" text-anchor="middle">REVIEW</text>
                    <text x="210" y="65" font-family="monospace" font-size="9" fill="#64748b" text-anchor="middle">RAFLY</text>
                </svg>
            </div>

            <!-- LIVE TELEMETRY SUMMARY -->
            <div class="intake-live-summary" id="<?= e($formId) ?>-telemetry">
                <div class="telemetry-row">
                    <span class="telemetry-label">CURRENT STEP:</span>
                    <span class="telemetry-val step-indicator">01 / CONTACT</span>
                </div>
                <div class="telemetry-row">
                    <span class="telemetry-label">SELECTED SERVICE:</span>
                    <span class="telemetry-val service-indicator">WEB DEVELOPMENT</span>
                </div>
                <div class="telemetry-row">
                    <span class="telemetry-label">ESTIMATED SLA:</span>
                    <span class="telemetry-val">&lt; 24 HOURS TRIAGE</span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- RIGHT 60%: 3-STEP FORM -->
        <div class="intake-form-side">
            <!-- STEP INDICATOR NAV -->
            <div class="intake-step-bar" role="tablist">
                <button type="button" class="intake-step-btn active" data-step="1" id="<?= e($formId) ?>-tab1">
                    <span class="step-num">01</span> CONTACT
                </button>
                <button type="button" class="intake-step-btn" data-step="2" id="<?= e($formId) ?>-tab2">
                    <span class="step-num">02</span> SCOPE
                </button>
                <button type="button" class="intake-step-btn" data-step="3" id="<?= e($formId) ?>-tab3">
                    <span class="step-num">03</span> VERIFY
                </button>
            </div>

            <form class="form lead-form-premium intake-console-form" id="<?= e($formId) ?>" action="/submit" method="POST" data-ajax-form="true">
                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
                <?= lead_context_fields() ?>

                <!-- STEP 01 — CONTACT DETAILS -->
                <div class="intake-step-pane active" data-step-pane="1">
                    <div class="intake-pane-head">
                        <span class="pane-tag">STEP 01</span>
                        <h4>Contact &amp; Organization</h4>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="<?= e($formId) ?>-name">Your Name <span class="req" aria-hidden="true">*</span></label>
                            <input class="form-control" type="text" id="<?= e($formId) ?>-name" name="contact_name"
                                   autocomplete="name" maxlength="120" required placeholder="e.g. Priya Sharma">
                        </div>
                        <div class="field">
                            <label for="<?= e($formId) ?>-email">Work Email <span class="req" aria-hidden="true">*</span></label>
                            <input class="form-control" type="email" id="<?= e($formId) ?>-email" name="contact_email"
                                   autocomplete="email" maxlength="255" required placeholder="you@company.com">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="field">
                            <label for="<?= e($formId) ?>-company">Company / Brand <span class="req" aria-hidden="true">*</span></label>
                            <input class="form-control" type="text" id="<?= e($formId) ?>-company" name="company_name"
                                   autocomplete="organization" maxlength="100" required placeholder="Your business name">
                        </div>
                        <div class="field">
                            <label for="<?= e($formId) ?>-phone">Phone / WhatsApp <span class="req" aria-hidden="true">*</span></label>
                            <input class="form-control" type="tel" id="<?= e($formId) ?>-phone" name="contact_number"
                                   autocomplete="tel" maxlength="30" required placeholder="+91 98765 43210">
                        </div>
                    </div>

                    <div class="intake-pane-actions">
                        <button type="button" class="btn btn-primary btn-next-step" data-goto="2">
                            <span>Proceed to Scope</span> <?= icon('arrow-right') ?>
                        </button>
                    </div>
                </div>

                <!-- STEP 02 — INTERACTIVE SCOPE CARDS & REQUIREMENTS -->
                <div class="intake-step-pane" data-step-pane="2">
                    <div class="intake-pane-head">
                        <span class="pane-tag">STEP 02</span>
                        <h4>Service Scope &amp; Context</h4>
                    </div>

                    <!-- INTERACTIVE SERVICE CARDS -->
                    <div class="field">
                        <label>Select Primary Pillar <span class="req" aria-hidden="true">*</span></label>
                        <input type="hidden" name="service_interest" id="<?= e($formId) ?>-pillar-input" value="01_build" required>
                        <div class="intake-service-cards" role="radiogroup" aria-label="Service Selection">
                            <div class="intake-svc-card active" data-val="01_build">
                                <span class="svc-card-icon"><?= icon('globe') ?></span>
                                <span class="svc-card-title">WEB &amp; APP</span>
                            </div>
                            <div class="intake-svc-card" data-val="02_protect">
                                <span class="svc-card-icon"><?= icon('shield') ?></span>
                                <span class="svc-card-title">SECURITY</span>
                            </div>
                            <div class="intake-svc-card" data-val="03_grow">
                                <span class="svc-card-icon"><?= icon('trending-up') ?></span>
                                <span class="svc-card-title">MARKETING</span>
                            </div>
                            <div class="intake-svc-card" data-val="04_content">
                                <span class="svc-card-icon"><?= icon('video') ?></span>
                                <span class="svc-card-title">CONTENT</span>
                            </div>
                            <div class="intake-svc-card" data-val="05_ecommerce">
                                <span class="svc-card-icon"><?= icon('shopping-cart') ?></span>
                                <span class="svc-card-title">COMMERCE</span>
                            </div>
                            <div class="intake-svc-card" data-val="06_automation">
                                <span class="svc-card-icon"><?= icon('cpu') ?></span>
                                <span class="svc-card-title">AUTOMATION</span>
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label for="<?= e($formId) ?>-budget">Indicative Budget Bracket <span class="req" aria-hidden="true">*</span></label>
                        <select class="form-control" id="<?= e($formId) ?>-budget" name="budget_bracket" required>
                            <option value="" disabled selected>Select Budget Range...</option>
                            <option value="under_30k">₹25,000 – ₹45,000 (Local Service / Web App)</option>
                            <option value="45k_100k">₹45,000 – ₹1,00,000 (E-Commerce / Custom System)</option>
                            <option value="100k_plus">₹1,00,000+ (Enterprise System &amp; Retainer)</option>
                            <option value="emergency">₹12,000 – ₹25,000 (Security Audit / Emergency)</option>
                        </select>
                    </div>

                    <div class="field">
                        <div class="field-label-row">
                            <label for="<?= e($formId) ?>-desc">Project Context / Requirements <span class="req" aria-hidden="true">*</span></label>
                            <span class="field-quick-hint">Tap scope chips to append:</span>
                        </div>

                        <div class="form-tag-chips" role="group" aria-label="Quick project scope tags">
                            <button type="button" class="form-tag-chip" data-chip="Web Application">+ Web Application</button>
                            <button type="button" class="form-tag-chip" data-chip="E-Commerce">+ E-Commerce</button>
                            <button type="button" class="form-tag-chip" data-chip="Custom PHP / Security">+ Security Audit</button>
                            <button type="button" class="form-tag-chip" data-chip="Lead Automation">+ Automation</button>
                            <button type="button" class="form-tag-chip" data-chip="SEO & Performance">+ Performance</button>
                        </div>

                        <textarea class="form-control" id="<?= e($formId) ?>-desc" name="description"
                                  maxlength="2000" required
                                  <?= $compact ? 'rows="3"' : 'rows="4"' ?>
                                  placeholder="Describe project objectives, timeline, or tech requirements..."></textarea>
                    </div>

                    <div class="intake-pane-actions">
                        <button type="button" class="btn btn-ghost btn-prev-step" data-goto="1">
                            <?= icon('arrow-left') ?> Back
                        </button>
                        <button type="button" class="btn btn-primary btn-next-step" data-goto="3">
                            <span>Review &amp; Verify</span> <?= icon('arrow-right') ?>
                        </button>
                    </div>
                </div>

                <!-- STEP 03 — FINAL VERIFICATION & SUBMISSION -->
                <div class="intake-step-pane" data-step-pane="3">
                    <div class="intake-pane-head" style="display: flex; align-items: center; justify-content: space-between;">
                        <div>
                            <span class="pane-tag">STEP 03</span>
                            <h4>Final Brief Verification</h4>
                        </div>
                        <div class="verify-lottie-icon" aria-hidden="true" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                            <lottie-player
                                src="/assets/lottie/checkmark.json"
                                background="transparent"
                                speed="1"
                                style="width: 44px; height: 44px;"
                                autoplay
                                loop
                                aria-hidden="true">
                            </lottie-player>
                        </div>
                    </div>

                    <div class="intake-verify-box">
                        <div class="verify-item">
                            <span class="v-label">CONTACT:</span>
                            <span class="v-val" id="<?= e($formId) ?>-v-contact">—</span>
                        </div>
                        <div class="verify-item">
                            <span class="v-label">COMPANY:</span>
                            <span class="v-val" id="<?= e($formId) ?>-v-company">—</span>
                        </div>
                        <div class="verify-item">
                            <span class="v-label">PILLAR:</span>
                            <span class="v-val" id="<?= e($formId) ?>-v-pillar">WEB &amp; APP</span>
                        </div>
                        <div class="verify-item">
                            <span class="v-label">BUDGET:</span>
                            <span class="v-val" id="<?= e($formId) ?>-v-budget">Not selected</span>
                        </div>
                    </div>

                    <?php require __DIR__ . '/honeypot.php'; ?>
                    <?php require __DIR__ . '/antibot.php'; ?>

                    <label class="check" style="margin-top: 1rem;">
                        <input type="checkbox" name="consent" required checked>
                        <span>I agree to be contacted regarding this enquiry per RAFly's <a href="/privacy" target="_blank">privacy policy</a>.</span>
                    </label>

                    <div class="intake-pane-actions">
                        <button type="button" class="btn btn-ghost btn-prev-step" data-goto="2">
                            <?= icon('arrow-left') ?> Back
                        </button>
                        <button class="btn btn-pill btn-lg btn-lead-submit btn-primary" type="submit">
                            <span><?= e($submitLabel) ?></span> <?= icon('send') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
