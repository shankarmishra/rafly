/**
 * RAFly Consent Manager — lightweight, config-driven consent layer.
 * Categories: 'necessary' (always on), 'analytics', 'marketing'
 * Persists to localStorage. Fires 'rafly:consent' CustomEvent on update.
 */
(function() {
    'use strict';
    var STORAGE_KEY = 'rafly_consent';
    var saved = null;
    try { saved = JSON.parse(localStorage.getItem(STORAGE_KEY)); } catch(e) {}
    
    var state = saved || null; // null = not yet decided
    
    window.raflyConsent = {
        isAllowed: function(cat) {
            if (cat === 'necessary') return true;
            return state ? !!state[cat] : false;
        },
        getState: function() { return state; },
        update: function(choices) {
            state = { analytics: !!choices.analytics, marketing: !!choices.marketing, ts: Date.now() };
            try { localStorage.setItem(STORAGE_KEY, JSON.stringify(state)); } catch(e) {}
            document.dispatchEvent(new CustomEvent('rafly:consent', { detail: state }));
        }
    };
    
    // If already decided, don't show banner
    if (state) return;
    
    // Build banner
    var banner = document.createElement('div');
    banner.id = 'consentBanner';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-label', 'Cookie consent');
    banner.innerHTML = 
        '<div style="max-width:1200px;margin:0 auto;display:flex;align-items:center;justify-content:space-between;gap:1.25rem;flex-wrap:wrap">' +
            '<p style="margin:0;font-size:0.88rem;color:#cbd5e1;line-height:1.5;flex:1;min-width:280px">' +
                'We use essential cookies for site functionality, and optional cookies for performance analytics and privacy-respecting marketing. ' +
                '<a href="/privacy" style="color:#60a5fa;text-decoration:underline">Privacy Policy</a>' +
            '</p>' +
            '<div style="display:flex;gap:0.5rem;flex-shrink:0">' +
                '<button id="consentReject" type="button" style="padding:0.5rem 1rem;border-radius:6px;border:1px solid #334155;background:transparent;color:#e2e8f0;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:inherit">Necessary Only</button>' +
                '<button id="consentAccept" type="button" style="padding:0.5rem 1rem;border-radius:6px;border:none;background:#0a63ff;color:#fff;font-size:0.82rem;font-weight:600;cursor:pointer;font-family:inherit">Accept All</button>' +
            '</div>' +
        '</div>';
    // Styles
    banner.style.cssText = 'position:fixed;bottom:0;left:0;right:0;z-index:9999;background:#0f172a;border-top:1px solid #1e293b;padding:1rem 1.5rem;font-family:var(--font-body,Inter,system-ui,sans-serif);box-shadow:0 -4px 24px rgba(0,0,0,0.4)';
    
    document.addEventListener('DOMContentLoaded', function() {
        document.body.appendChild(banner);
        
        var acceptBtn = document.getElementById('consentAccept');
        var rejectBtn = document.getElementById('consentReject');

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function() {
                window.raflyConsent.update({ analytics: true, marketing: true });
                banner.remove();
            });
        }
        
        if (rejectBtn) {
            rejectBtn.addEventListener('click', function() {
                window.raflyConsent.update({ analytics: false, marketing: false });
                banner.remove();
            });
        }
    });
})();
