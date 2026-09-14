/**
 * forms.js — client-side validation and AJAX submission for the lead forms.
 *
 * Two things here exist because of real defects in the previous build:
 *
 *   1. The anti-spam challenge is single-use server-side. After one successful
 *      submission the arithmetic answer and the timing stamp were consumed and
 *      never re-issued, so a SECOND submission from the same page load always
 *      failed — silently losing the lead. The server now returns a fresh
 *      challenge with every response and this file writes it back into the DOM.
 *   2. The CSRF token rotates on every submission, so it is re-read from the
 *      response into every form on the page, not just the one submitted.
 *
 * No dependencies.
 */
(function () {
    'use strict';

    var doc = document;

    var MESSAGES = {
        required: 'This field is required.',
        email:    'Enter a valid email address, e.g. name@company.com',
        phone:    'Enter a valid contact number.',
        consent:  'Please tick the consent box so we can reply to you.',
        network:  'Could not reach the server. Please check your connection and try again.'
    };

    /* --------------------------------------------------------------------
       Validation
       -------------------------------------------------------------------- */
    function fieldOf(input) { return input.closest('.field') || input.closest('.check') || input.parentElement; }

    function setError(input, message) {
        var field = fieldOf(input);
        if (!field) return;
        field.classList.add('has-error');
        input.setAttribute('aria-invalid', 'true');

        var slot = field.querySelector('.field-error');
        if (!slot) {
            slot = doc.createElement('span');
            slot.className = 'field-error';
            field.appendChild(slot);
        }
        slot.textContent = message;
    }

    function clearError(input) {
        var field = fieldOf(input);
        if (!field) return;
        field.classList.remove('has-error');
        input.removeAttribute('aria-invalid');
        var slot = field.querySelector('.field-error');
        if (slot) slot.remove();
    }

    // Deliberately permissive: this is a courtesy check to catch typos before a
    // round trip, not an authority. submit.php runs FILTER_VALIDATE_EMAIL.
    var EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    function validate(form) {
        var ok = true;
        var firstBad = null;

        Array.prototype.forEach.call(form.querySelectorAll('input, textarea, select'), function (input) {
            if (input.type === 'hidden' || input.closest('.hp-field')) return;
            clearError(input);

            var value = (input.value || '').trim();

            if (input.type === 'checkbox' && input.required && !input.checked) {
                setError(input, MESSAGES.consent);
                ok = false; firstBad = firstBad || input;
                return;
            }
            if (input.type === 'checkbox') return;

            if (input.required && !value) {
                setError(input, MESSAGES.required);
                ok = false; firstBad = firstBad || input;
                return;
            }
            if (value && input.type === 'email' && !EMAIL.test(value)) {
                setError(input, MESSAGES.email);
                ok = false; firstBad = firstBad || input;
                return;
            }
            if (value && input.type === 'tel' && value.replace(/[^0-9]/g, '').length < 7) {
                setError(input, MESSAGES.phone);
                ok = false; firstBad = firstBad || input;
            }
        });

        if (firstBad) {
            firstBad.focus();
            form.classList.add('shake');
            setTimeout(function () { form.classList.remove('shake'); }, 460);
        }
        return ok;
    }

    /* --------------------------------------------------------------------
       Re-seeding the anti-spam challenge and the CSRF token, site-wide.
       Both forms on a page share one session, so both have to be updated.
       -------------------------------------------------------------------- */
    function applyChallenge(data) {
        if (data.csrf_token) {
            Array.prototype.forEach.call(doc.querySelectorAll('input[name="csrf_token"]'), function (input) {
                input.value = data.csrf_token;
            });
        }
        if (data.antibot && typeof data.antibot.a !== 'undefined') {
            Array.prototype.forEach.call(doc.querySelectorAll('[data-antibot-a]'), function (el) {
                el.textContent = String(data.antibot.a);
            });
            Array.prototype.forEach.call(doc.querySelectorAll('[data-antibot-b]'), function (el) {
                el.textContent = String(data.antibot.b);
            });
            Array.prototype.forEach.call(doc.querySelectorAll('input[name="antibot_answer"]'), function (input) {
                input.value = '';
            });
        }
    }

    /* --------------------------------------------------------------------
       Analytics Custom Event Tracking Helper
       -------------------------------------------------------------------- */
    function trackEvent(eventName, params) {
        params = params || {};
        if (typeof window.gtag === 'function') {
            window.gtag('event', eventName, params);
        }
        if (typeof window.fbq === 'function') {
            window.fbq('trackCustom', eventName, params);
        }
    }

    /* --------------------------------------------------------------------
       Submission
       -------------------------------------------------------------------- */
    function submit(form) {
        var button = form.querySelector('[type="submit"]');
        var label = button ? button.innerHTML : '';

        if (button) {
            button.classList.add('is-loading');
            button.disabled = true;
            button.innerHTML = '<span>Sending</span>';
        }

        function restore() {
            if (!button) return;
            button.classList.remove('is-loading');
            button.disabled = false;
            button.innerHTML = label;
        }

        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (res) {
                return res.json().catch(function () {
                    throw new Error('bad-json');
                });
            })
            .then(function (data) {
                restore();
                applyChallenge(data);

                if (data.success) {
                    form.reset();
                    Array.prototype.forEach.call(form.querySelectorAll('[aria-invalid]'), clearError);

                    if (window.RaflyUI) {
                        window.RaflyUI.toast(data.message || 'Thanks — we have your details and will reply shortly.', 'ok');
                        window.RaflyUI.closeModal();
                    }
                    if (typeof window.fbq === 'function') window.fbq('track', 'Lead');

                    var sourcePage = (form.querySelector('input[name="source_page"]') || {}).value || '';
                    trackEvent('LeadForm_Complete', { form_id: form.id, source_page: sourcePage });
                    if (sourcePage === '/landing/website-audit') {
                        trackEvent('Audit_Request', { form_id: form.id });
                    }

                    var done = form.getAttribute('data-success-redirect');
                    if (done) setTimeout(function () { window.location.href = done; }, 900);
                } else {
                    if (data.field) {
                        var input = form.querySelector('[name="' + data.field + '"]');
                        if (input) { setError(input, data.message || MESSAGES.required); input.focus(); }
                    }
                    if (window.RaflyUI) {
                        window.RaflyUI.toast(data.message || 'Something went wrong. Please try again.', 'error');
                    }
                }
            })
            .catch(function () {
                restore();
                if (window.RaflyUI) window.RaflyUI.toast(MESSAGES.network, 'error');
            });
    }

    /* -------------------------------------------------------------------- */
    function init() {
        Array.prototype.forEach.call(doc.querySelectorAll('form[data-ajax-form]'), function (form) {
            form.setAttribute('novalidate', '');

            form.addEventListener('focusin', function () {
                if (!form.getAttribute('data-started')) {
                    form.setAttribute('data-started', 'true');
                    trackEvent('LeadForm_Start', { form_id: form.id });
                }
            });

            form.addEventListener('submit', function (e) {
                e.preventDefault();
                if (validate(form)) submit(form);
            });

            // Clear an error as soon as the visitor starts fixing it.
            form.addEventListener('input', function (e) {
                if (e.target.matches('input, textarea, select')) {
                    var field = fieldOf(e.target);
                    if (field && field.classList.contains('has-error')) clearError(e.target);
                }
            });
        });

        // Global click listener for WhatsApp & Consultation CTA analytics events
        doc.addEventListener('click', function(e) {
            var waLink = e.target.closest('a[href*="wa.me"]');
            if (waLink) {
                trackEvent('WhatsApp_Click', { link_url: waLink.href });
            }
            var modalTrigger = e.target.closest('[data-modal-open="consultationModal"]');
            if (modalTrigger) {
                trackEvent('Call_Scheduled', { trigger_label: modalTrigger.textContent.trim() });
            }

            // Quick scope chip click handler for lead forms (CSP compliant)
            var chip = e.target.closest('.form-tag-chip');
            if (chip) {
                e.preventDefault();
                var text = chip.getAttribute('data-chip');
                var form = chip.closest('form');
                if (form) {
                    var textarea = form.querySelector('textarea[name="description"]');
                    if (textarea) {
                        var val = textarea.value.trim();
                        var tagStr = '[Scope: ' + text + ']';
                        if (val.indexOf(tagStr) === -1) {
                            textarea.value = val ? val + '\n' + tagStr : tagStr + ' ';
                            chip.classList.add('is-selected');
                        } else {
                            textarea.value = val.replace(tagStr, '').trim();
                            chip.classList.remove('is-selected');
                        }
                        textarea.focus();
                    }
                }
                return;
            }

            // 3-Step Intake Console Handlers
            var stepBtn = e.target.closest('.intake-step-btn, .btn-next-step, .btn-prev-step');
            if (stepBtn) {
                var consoleWrap = stepBtn.closest('.intake-console-wrapper');
                if (consoleWrap) {
                    var targetStep = stepBtn.getAttribute('data-step') || stepBtn.getAttribute('data-goto');
                    if (targetStep) {
                        e.preventDefault();
                        var currentPane = consoleWrap.querySelector('.intake-step-pane.active');
                        var currentStepNum = currentPane ? parseInt(currentPane.getAttribute('data-step-pane'), 10) : 1;
                        var targetStepNum = parseInt(targetStep, 10);

                        // If advancing, validate current step fields first
                        if (targetStepNum > currentStepNum && currentPane) {
                            var inputs = currentPane.querySelectorAll('input, textarea, select');
                            var stepValid = true;
                            Array.prototype.forEach.call(inputs, function (input) {
                                clearError(input);
                                if (input.required && !(input.value || '').trim()) {
                                    setError(input, MESSAGES.required);
                                    stepValid = false;
                                } else if (input.type === 'email' && input.value && !EMAIL.test(input.value.trim())) {
                                    setError(input, MESSAGES.email);
                                    stepValid = false;
                                } else if (input.type === 'tel' && input.value && input.value.replace(/[^0-9]/g, '').length < 7) {
                                    setError(input, MESSAGES.phone);
                                    stepValid = false;
                                }
                            });
                            if (!stepValid) return;
                        }

                        // Switch Active Tab and Pane
                        Array.prototype.forEach.call(consoleWrap.querySelectorAll('.intake-step-btn'), function(b) {
                            b.classList.toggle('active', b.getAttribute('data-step') === String(targetStepNum));
                        });
                        Array.prototype.forEach.call(consoleWrap.querySelectorAll('.intake-step-pane'), function(p) {
                            p.classList.toggle('active', p.getAttribute('data-step-pane') === String(targetStepNum));
                        });

                        // Update Signal Diagram & Telemetry
                        var stepNames = { 1: '01 / CONTACT', 2: '02 / SCOPE', 3: '03 / VERIFY' };
                        var stepInd = consoleWrap.querySelector('.step-indicator');
                        if (stepInd) stepInd.textContent = stepNames[targetStepNum] || ('0' + targetStepNum + ' / INTAKE');

                        var svgNodes = consoleWrap.querySelectorAll('.signal-node');
                        Array.prototype.forEach.call(svgNodes, function(n, idx) {
                            if (idx + 1 <= targetStepNum) {
                                n.setAttribute('fill', '#0a63ff');
                                n.classList.add('active');
                            } else {
                                n.setAttribute('fill', '#cbd5e1');
                                n.classList.remove('active');
                            }
                        });

                        // If Step 3, populate Verification Summary
                        if (targetStepNum === 3) {
                            var nameVal = (consoleWrap.querySelector('input[name="contact_name"]') || {}).value || '';
                            var emailVal = (consoleWrap.querySelector('input[name="contact_email"]') || {}).value || '';
                            var compVal = (consoleWrap.querySelector('input[name="company_name"]') || {}).value || '';
                            var phoneVal = (consoleWrap.querySelector('input[name="contact_number"]') || {}).value || '';
                            var pillarInput = consoleWrap.querySelector('input[name="service_interest"]');
                            var budgetSelect = consoleWrap.querySelector('select[name="budget_bracket"]');

                            var vContact = consoleWrap.querySelector('[id$="-v-contact"]');
                            var vComp = consoleWrap.querySelector('[id$="-v-company"]');
                            var vPillar = consoleWrap.querySelector('[id$="-v-pillar"]');
                            var vBudget = consoleWrap.querySelector('[id$="-v-budget"]');

                            if (vContact) vContact.textContent = nameVal + (emailVal ? ' (' + emailVal + ')' : '');
                            if (vComp) vComp.textContent = compVal + (phoneVal ? ' • ' + phoneVal : '');
                            if (vPillar && pillarInput) vPillar.textContent = (pillarInput.value || 'WEB & APP').toUpperCase();
                            if (vBudget && budgetSelect && budgetSelect.selectedIndex >= 0) {
                                vBudget.textContent = budgetSelect.options[budgetSelect.selectedIndex].text;
                            }
                        }
                    }
                }
            }

            // Interactive Scope Cards Selection
            var svcCard = e.target.closest('.intake-svc-card');
            if (svcCard) {
                var cardGroup = svcCard.closest('.intake-service-cards');
                if (cardGroup) {
                    Array.prototype.forEach.call(cardGroup.querySelectorAll('.intake-svc-card'), function(c) { c.classList.remove('active'); });
                    svcCard.classList.add('active');
                    var val = svcCard.getAttribute('data-val');
                    var hiddenInput = cardGroup.parentElement.querySelector('input[name="service_interest"]');
                    if (hiddenInput) hiddenInput.value = val;

                    var consoleWrap = svcCard.closest('.intake-console-wrapper');
                    if (consoleWrap) {
                        var svcInd = consoleWrap.querySelector('.service-indicator');
                        var titleText = (svcCard.querySelector('.svc-card-title') || {}).textContent || val;
                        if (svcInd) svcInd.textContent = titleText.toUpperCase();
                    }
                }
            }
        });
    }

    if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', init);
    else init();
}());
