/*
 * Admin progressive enhancement. The FIRST script the admin has ever loaded.
 *
 * Two jobs, both pure vanilla JS with no dependencies, served from the same
 * origin so the admin Content-Security-Policy (script-src 'self', no
 * 'unsafe-inline') allows it:
 *
 *   1. Flash messages become dismissible, auto-hiding toasts.
 *   2. Destructive forms get a styled confirmation dialog.
 *
 * Note on (2): the pages used to carry `onsubmit="return confirm(...)"`. Under
 * the admin CSP an inline handler is BLOCKED — so those confirmations silently
 * did nothing and a mis-click deleted immediately. Moving the prompt here, keyed
 * off a `data-confirm` attribute, is what actually restores it. With JavaScript
 * off the form submits directly, exactly as it already did, so nothing regresses.
 */
(function () {
    'use strict';

    /* --- 1. Flash → toast -------------------------------------------------- */

    function enhanceFlash(flash) {
        flash.classList.add('is-toast');
        flash.setAttribute('role', 'status');

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'flash-close';
        close.setAttribute('aria-label', 'Dismiss');
        close.innerHTML = '&times;';
        flash.appendChild(close);

        var dismiss = function () {
            flash.classList.add('is-leaving');
            // Remove after the CSS transition so it does not leave a gap.
            window.setTimeout(function () {
                if (flash.parentNode) { flash.parentNode.removeChild(flash); }
            }, 300);
        };

        close.addEventListener('click', dismiss);
        // Errors stay until dismissed; success/info time out on their own.
        if (!flash.classList.contains('flash-error')) {
            window.setTimeout(dismiss, 5000);
        }
    }

    /* --- 2. Styled confirm dialog ----------------------------------------- */

    var dialog = null;
    var pendingForm = null;

    function buildDialog() {
        // <dialog> is supported everywhere the admin targets; if it is somehow
        // absent we fall back to native confirm() inside handleSubmit.
        if (typeof HTMLDialogElement === 'undefined') { return null; }

        var d = document.createElement('dialog');
        d.className = 'confirm-dialog';
        d.innerHTML =
            '<form method="dialog">' +
                '<p class="confirm-message"></p>' +
                '<div class="confirm-actions">' +
                    '<button type="button" class="btn btn-secondary" data-confirm-cancel>Cancel</button>' +
                    '<button type="submit" class="btn btn-danger" data-confirm-ok>Confirm</button>' +
                '</div>' +
            '</form>';
        document.body.appendChild(d);

        d.querySelector('[data-confirm-cancel]').addEventListener('click', function () {
            pendingForm = null;
            d.close();
        });

        // The default-action submit button resolves the dialog; act on it.
        d.querySelector('form').addEventListener('submit', function () {
            var form = pendingForm;
            pendingForm = null;
            d.close();
            if (form) {
                // Detach the guard so the real submit is not intercepted again.
                form.removeAttribute('data-confirm');
                if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                } else {
                    form.submit();
                }
            }
        });

        return d;
    }

    function handleSubmit(e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement)) { return; }

        var message = form.getAttribute('data-confirm');
        if (!message) { return; }

        e.preventDefault();

        if (!dialog) { dialog = buildDialog(); }

        // No <dialog> support: keep the user safe with the native prompt.
        if (!dialog) {
            if (window.confirm(message)) {
                form.removeAttribute('data-confirm');
                form.submit();
            }
            return;
        }

        pendingForm = form;
        dialog.querySelector('.confirm-message').textContent = message;

        // Match the action tone to the button already in the form.
        var danger = form.querySelector('.btn-danger');
        dialog.querySelector('[data-confirm-ok]').classList.toggle('btn-danger', !!danger);

        dialog.showModal();
    }

    /* --- Wire up ----------------------------------------------------------- */

    function init() {
        var flashes = document.querySelectorAll('.flash');
        for (var i = 0; i < flashes.length; i++) { enhanceFlash(flashes[i]); }

        // Delegated so it also covers forms rendered after load.
        document.addEventListener('submit', handleSubmit, true);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
