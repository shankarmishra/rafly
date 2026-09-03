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

        // Quick scope chip click handler for lead forms (CSP compliant)
        doc.addEventListener('click', function(e) {
            var chip = e.target.closest('.form-tag-chip');
            if (!chip) return;
            e.preventDefault();
            var text = chip.getAttribute('data-chip');
            var form = chip.closest('form');
            if (!form) return;
            var textarea = form.querySelector('textarea[name="description"]');
            if (!textarea) return;
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
        });
    }

    if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', init);
    else init();
}());
