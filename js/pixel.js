/**
 * Meta Pixel & GA4 Custom Event Telemetry Engine (SOP 27)
 * Handles PageView tracking and custom event listeners for:
 * - LeadForm_Start
 * - LeadForm_Complete
 * - WhatsApp_Click
 * - Audit_Request
 * - Call_Scheduled
 */
(function (f, b, e, v, n, t, s) {
    var id = document.currentScript && document.currentScript.dataset.pixelId;
    if (!id || f.fbq) return;
    n = f.fbq = function () {
        n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
    };
    if (!f._fbq) f._fbq = n;
    n.push = n;
    n.loaded = true;
    n.version = '2.0';
    n.queue = [];
    t = b.createElement(e);
    t.async = true;
    t.src = v;
    s = b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t, s);

    fbq('init', id);
    fbq('track', 'PageView');
})(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

/**
 * Universal Custom Telemetry Dispatcher (SOP 27)
 */
window.trackCustomEvent = function (eventName, payload) {
    payload = payload || {};
    
    // Meta Pixel Event
    if (typeof window.fbq === 'function') {
        window.fbq('trackCustom', eventName, payload);
    }
    // Google Analytics 4 (gtag) Event
    if (typeof window.gtag === 'function') {
        window.gtag('event', eventName, payload);
    }
};

// Automatic Event Listeners for telemetry
document.addEventListener('DOMContentLoaded', function () {
    // 1. WhatsApp Click Event Listener
    document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(function (el) {
        el.addEventListener('click', function () {
            window.trackCustomEvent('WhatsApp_Click', { link: el.href });
        });
    });

    // 2. Audit Request & Consultation Buttons
    document.querySelectorAll('a[href*="website-audit"], a[href*="pricing"], .btn-audit').forEach(function (el) {
        el.addEventListener('click', function () {
            window.trackCustomEvent('Audit_Request', { text: el.innerText });
        });
    });

    // 3. Lead Form Tracking (Start & Complete)
    var forms = document.querySelectorAll('form[action*="submit"], form.lead-form');
    forms.forEach(function (form) {
        var started = false;
        form.addEventListener('focusin', function () {
            if (!started) {
                started = true;
                window.trackCustomEvent('LeadForm_Start', { form_id: form.id || 'contact-form' });
            }
        });

        form.addEventListener('submit', function () {
            window.trackCustomEvent('LeadForm_Complete', { form_id: form.id || 'contact-form' });
        });
    });
});
