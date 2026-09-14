/**
 * RAFly Unified Telemetry & Tracking Engine
 * Supports: Meta Pixel, Google Analytics 4, Google Ads, GTM Architecture
 * Strictly respects user consent via js/consent.js before firing any non-essential trackers.
 */
(function () {
    'use strict';

    var script = document.currentScript;
    var pixelId = script && script.dataset.pixelId || '';
    var ga4Id   = script && script.dataset.ga4Id || '';
    var gadsId  = script && script.dataset.gadsId || '';
    var gtmId   = script && script.dataset.gtmId || '';

    var pixelReady = false;
    var ga4Ready   = false;
    var gtmReady   = false;

    // --- GTM Bootstrap (if GTM Container ID provided) ---
    function initGTM() {
        if (gtmReady || !gtmId) return;
        if (window.raflyConsent && !window.raflyConsent.isAllowed('analytics') && !window.raflyConsent.isAllowed('marketing')) return;
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ 'gtm.start': new Date().getTime(), event: 'gtm.js' });
        var f = document.getElementsByTagName('script')[0],
            j = document.createElement('script');
        j.async = true;
        j.src = 'https://www.googletagmanager.com/gtm.js?id=' + encodeURIComponent(gtmId);
        f.parentNode.insertBefore(j, f);
        gtmReady = true;
    }

    // --- Meta Pixel Bootstrap (marketing consent) ---
    function initPixel() {
        if (pixelReady || !pixelId) return;
        if (window.raflyConsent && !window.raflyConsent.isAllowed('marketing')) return;
        var f = window, b = document, e = 'script', v = 'https://connect.facebook.net/en_US/fbevents.js';
        if (f.fbq) return;
        var n = f.fbq = function () {
            n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
        };
        if (!f._fbq) f._fbq = n;
        n.push = n; n.loaded = true; n.version = '2.0'; n.queue = [];
        var t = b.createElement(e); t.async = true; t.src = v;
        var s = b.getElementsByTagName(e)[0]; s.parentNode.insertBefore(t, s);

        fbq('init', pixelId);
        fbq('track', 'PageView');
        pixelReady = true;
    }

    // --- GA4 Bootstrap (analytics consent) ---
    function initGA4() {
        if (ga4Ready || !ga4Id) return;
        if (window.raflyConsent && !window.raflyConsent.isAllowed('analytics')) return;
        var s = document.createElement('script');
        s.async = true;
        s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(ga4Id);
        document.head.appendChild(s);
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('js', new Date());
        window.gtag('config', ga4Id, { send_page_view: true });
        ga4Ready = true;
    }

    // Standard event mapping for clean analytics architecture
    var EVENT_MAP = {
        'lead':           { pixel: 'Lead',        ga4: 'generate_lead' },
        'contact':        { pixel: 'Contact',     ga4: 'contact' },
        'whatsapp_click': { pixel: 'Contact',     ga4: 'whatsapp_click' },
        'phone_click':    { pixel: 'Contact',     ga4: 'phone_click' },
        'email_click':    { pixel: 'Contact',     ga4: 'email_click' },
        'form_start':     { pixel: null,          ga4: 'form_start' },
        'form_submit':    { pixel: 'Lead',        ga4: 'generate_lead' },
        'service_view':   { pixel: 'ViewContent', ga4: 'view_item' },
        'quote_request':  { pixel: 'Lead',        ga4: 'generate_lead' }
    };

    /**
     * Centralized Tracking Dispatcher (No PII)
     */
    window.raflyTrack = function (eventName, params) {
        params = params || {};
        var mapped = EVENT_MAP[eventName];

        // GTM DataLayer
        if (window.dataLayer) {
            window.dataLayer.push({
                event: eventName,
                event_params: params
            });
        }

        // Meta Pixel Standard & Custom Events
        if (pixelReady && typeof window.fbq === 'function') {
            if (mapped && mapped.pixel) {
                window.fbq('track', mapped.pixel, params);
            } else {
                window.fbq('trackCustom', eventName, params);
            }
        }

        // GA4 / gtag
        if (ga4Ready && typeof window.gtag === 'function') {
            var ga4Event = (mapped && mapped.ga4) ? mapped.ga4 : eventName;
            window.gtag('event', ga4Event, params);
        }
    };

    // Backward compatibility for existing code calling window.trackCustomEvent
    window.trackCustomEvent = function (eventName, payload) {
        window.raflyTrack(eventName, payload);
    };

    // --- Consent-aware Initialization ---
    function tryInit() {
        initGTM();
        initPixel();
        initGA4();
    }

    // Try init on load
    tryInit();

    // Listen for user consent choices
    document.addEventListener('rafly:consent', function () {
        tryInit();
    });

    // --- Automatic DOM Event Listeners ---
    document.addEventListener('DOMContentLoaded', function () {
        // 1. WhatsApp Clicks
        document.querySelectorAll('a[href*="wa.me"], a[href*="whatsapp"]').forEach(function (el) {
            el.addEventListener('click', function () {
                window.raflyTrack('whatsapp_click', { link_url: el.href });
            });
        });

        // 2. Phone Clicks
        document.querySelectorAll('a[href^="tel:"]').forEach(function (el) {
            el.addEventListener('click', function () {
                window.raflyTrack('phone_click');
            });
        });

        // 3. Email Clicks
        document.querySelectorAll('a[href^="mailto:"]').forEach(function (el) {
            el.addEventListener('click', function () {
                window.raflyTrack('email_click');
            });
        });

        // 4. Lead Form Submissions
        document.querySelectorAll('form[action*="submit"], form.lead-form').forEach(function (form) {
            var started = false;
            form.addEventListener('focusin', function () {
                if (!started) {
                    started = true;
                    window.raflyTrack('form_start', { form_id: form.id || 'lead-form' });
                }
            });

            form.addEventListener('submit', function () {
                window.raflyTrack('form_submit', { form_id: form.id || 'lead-form' });
            });
        });

        // 5. Service View
        var svcBody = document.querySelector('.page-service');
        if (svcBody) {
            var svcClass = Array.from(svcBody.classList).find(function (c) { return c.startsWith('svc-'); });
            if (svcClass) {
                window.raflyTrack('service_view', { service: svcClass.replace('svc-', '') });
            }
        }
    });
})();
