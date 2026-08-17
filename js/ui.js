/**
 * ui.js — chrome behaviour: header, dropdowns, drawer, modals, accordions,
 * back-to-top and toasts.
 *
 * Split out of the old monolithic app.js so form handling lives on its own
 * (js/forms.js) and this file only owns things you can see and click.
 *
 * No dependencies.
 */
(function () {
    'use strict';

    var doc = document;
    var body = doc.body;

    /* ====================================================================
       Focus trap — shared by the drawer and every modal
       ==================================================================== */
    var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]):not([type="hidden"]),' +
                    ' select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])';

    function trapFocus(container) {
        function onKey(e) {
            if (e.key !== 'Tab') return;
            var items = Array.prototype.filter.call(
                container.querySelectorAll(FOCUSABLE),
                function (el) { return el.offsetParent !== null || el === doc.activeElement; }
            );
            if (!items.length) return;
            var first = items[0];
            var last = items[items.length - 1];
            if (e.shiftKey && doc.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && doc.activeElement === last) { e.preventDefault(); first.focus(); }
        }
        container.addEventListener('keydown', onKey);
        return function () { container.removeEventListener('keydown', onKey); };
    }

    /* ====================================================================
       Header — shadow on scroll, hide going down, show coming up
       ==================================================================== */
    function initHeader() {
        var header = doc.querySelector('.site-header');
        if (!header) return;

        var last = window.scrollY;
        var ticking = false;

        function update() {
            var y = window.scrollY;
            header.classList.toggle('is-stuck', y > 12);

            var menuOpen = body.classList.contains('nav-open') ||
                           body.classList.contains('modal-open');
            if (!menuOpen && y > 200 && y > last + 6) header.classList.add('is-hidden');
            else if (y < last - 6 || y < 200) header.classList.remove('is-hidden');

            last = y;
            ticking = false;
        }

        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(update); ticking = true; }
        }, { passive: true });
        update();
    }

    /* ====================================================================
       Desktop dropdowns
       ==================================================================== */
    function initDropdowns() {
        var items = doc.querySelectorAll('[data-drop]');
        if (!items.length) return;

        var openOne = null;
        var closeTimer = null;

        function close(item) {
            if (!item) return;
            var trigger = item.querySelector('[aria-expanded]');
            var panel = item.querySelector('.nav-drop');
            if (trigger) trigger.setAttribute('aria-expanded', 'false');
            if (panel) panel.classList.remove('is-open');
            if (openOne === item) openOne = null;
        }

        function open(item) {
            if (openOne && openOne !== item) close(openOne);
            var trigger = item.querySelector('[aria-expanded]');
            var panel = item.querySelector('.nav-drop');
            if (trigger) trigger.setAttribute('aria-expanded', 'true');
            if (panel) panel.classList.add('is-open');
            openOne = item;
        }

        Array.prototype.forEach.call(items, function (item) {
            var trigger = item.querySelector('[aria-expanded]');
            if (!trigger) return;

            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                if (trigger.getAttribute('aria-expanded') === 'true') close(item);
                else open(item);
            });

            // Hover with a close delay, so crossing the gap between the
            // trigger and the panel does not dismiss it.
            item.addEventListener('mouseenter', function () {
                clearTimeout(closeTimer);
                if (window.matchMedia('(hover: hover)').matches) open(item);
            });
            item.addEventListener('mouseleave', function () {
                if (!window.matchMedia('(hover: hover)').matches) return;
                closeTimer = setTimeout(function () { close(item); }, 220);
            });
            item.addEventListener('focusout', function (e) {
                if (!item.contains(e.relatedTarget)) close(item);
            });
        });

        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && openOne) {
                var trigger = openOne.querySelector('[aria-expanded]');
                close(openOne);
                if (trigger) trigger.focus();
            }
        });

        doc.addEventListener('click', function (e) {
            if (openOne && !openOne.contains(e.target)) close(openOne);
        });
    }

    /* ====================================================================
       Mobile drawer
       ==================================================================== */
    function initDrawer() {
        var toggle = doc.querySelector('[data-drawer-toggle]');
        var drawer = doc.getElementById('drawer');
        if (!toggle || !drawer) return;

        var release = null;
        var lastFocus = null;

        function open() {
            lastFocus = doc.activeElement;
            drawer.classList.add('is-open');
            drawer.removeAttribute('inert');
            body.classList.add('nav-open');
            toggle.setAttribute('aria-expanded', 'true');
            release = trapFocus(drawer);
            var first = drawer.querySelector(FOCUSABLE);
            if (first) first.focus();
        }

        function close() {
            drawer.classList.remove('is-open');
            body.classList.remove('nav-open');
            toggle.setAttribute('aria-expanded', 'false');
            if (release) { release(); release = null; }
            // inert keeps the closed drawer out of the tab order entirely,
            // rather than relying on the transition having finished.
            drawer.setAttribute('inert', '');
            if (lastFocus) lastFocus.focus();
        }

        drawer.setAttribute('inert', '');

        toggle.addEventListener('click', function () {
            if (drawer.classList.contains('is-open')) close(); else open();
        });

        drawer.addEventListener('click', function (e) {
            var link = e.target.closest('a[href]');
            if (link) close();
        });

        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && drawer.classList.contains('is-open')) close();
        });

        // Collapsible sub-lists. The panel uses the `hidden` attribute rather
        // than a height transition, so links inside a collapsed section are
        // genuinely removed from the tab order.
        Array.prototype.forEach.call(drawer.querySelectorAll('[data-drawer-sub]'), function (trigger) {
            var panel = doc.getElementById(trigger.getAttribute('aria-controls'));
            if (!panel) return;
            trigger.addEventListener('click', function () {
                var open = trigger.getAttribute('aria-expanded') === 'true';
                trigger.setAttribute('aria-expanded', open ? 'false' : 'true');
                panel.hidden = open;
            });
        });

        // Coming back to desktop with the drawer open would leave the body
        // locked, so it is force-closed at the breakpoint.
        window.matchMedia('(min-width: 941px)').addEventListener('change', function (e) {
            if (e.matches && drawer.classList.contains('is-open')) close();
        });
    }

    /* ====================================================================
       Modals
       ==================================================================== */
    var modalRelease = null;
    var modalLastFocus = null;

    function openModal(id) {
        var modal = doc.getElementById(id);
        if (!modal) return;
        modalLastFocus = doc.activeElement;
        modal.classList.add('is-open');
        modal.removeAttribute('aria-hidden');
        body.classList.add('modal-open');
        modalRelease = trapFocus(modal);
        var first = modal.querySelector(FOCUSABLE);
        if (first) first.focus();
    }

    function closeModal(modal) {
        if (!modal) modal = doc.querySelector('.modal.is-open');
        if (!modal) return;
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        body.classList.remove('modal-open');
        if (modalRelease) { modalRelease(); modalRelease = null; }
        if (modalLastFocus) modalLastFocus.focus();
    }

    function initModals() {
        doc.addEventListener('click', function (e) {
            var opener = e.target.closest('[data-modal-open]');
            if (opener) {
                e.preventDefault();
                openModal(opener.getAttribute('data-modal-open'));
                return;
            }
            var closer = e.target.closest('[data-modal-close]');
            if (closer) {
                e.preventDefault();
                closeModal(closer.closest('.modal'));
                return;
            }
            // Click on the backdrop itself
            if (e.target.classList && e.target.classList.contains('modal')) closeModal(e.target);
        });

        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    }

    /* ====================================================================
       Accordion
       ==================================================================== */
    function initAccordions() {
        Array.prototype.forEach.call(doc.querySelectorAll('.accordion-trigger'), function (trigger) {
            trigger.addEventListener('click', function () {
                var open = trigger.getAttribute('aria-expanded') === 'true';
                var group = trigger.closest('[data-accordion="single"]');

                if (group && !open) {
                    Array.prototype.forEach.call(group.querySelectorAll('.accordion-trigger'), function (other) {
                        if (other !== trigger) other.setAttribute('aria-expanded', 'false');
                    });
                }
                trigger.setAttribute('aria-expanded', open ? 'false' : 'true');
            });
        });
    }

    /* ====================================================================
       Back to top
       ==================================================================== */
    function initToTop() {
        var btn = doc.querySelector('.to-top');
        if (!btn) return;
        var ticking = false;

        function update() {
            btn.classList.toggle('is-on', window.scrollY > window.innerHeight * 0.8);
            ticking = false;
        }
        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(update); ticking = true; }
        }, { passive: true });

        btn.addEventListener('click', function () {
            var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            window.scrollTo({ top: 0, behavior: reduced ? 'auto' : 'smooth' });
        });
        update();
    }

    /* ====================================================================
       Scroll spy — highlights the in-page nav link for the visible section
       ==================================================================== */
    function initScrollSpy() {
        var links = doc.querySelectorAll('.nav-link[href^="/#"], .nav-link[href^="#"]');
        if (!links.length || !('IntersectionObserver' in window)) return;

        var map = {};
        Array.prototype.forEach.call(links, function (link) {
            var id = link.getAttribute('href').split('#')[1];
            if (!id) return;
            var section = doc.getElementById(id);
            if (section) map[id] = { link: link, section: section };
        });

        var keys = Object.keys(map);
        if (!keys.length) return;

        var spy = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                keys.forEach(function (k) { map[k].link.classList.remove('is-active'); });
                var hit = map[entry.target.id];
                if (hit) hit.link.classList.add('is-active');
            });
        }, { rootMargin: '-45% 0px -50% 0px' });

        keys.forEach(function (k) { spy.observe(map[k].section); });
    }

    /* ====================================================================
       Toasts — exposed for js/forms.js
       ==================================================================== */
    function toast(message, kind) {
        var stack = doc.getElementById('toastStack');
        if (!stack) return;

        var el = doc.createElement('div');
        el.className = 'toast toast-' + (kind === 'error' ? 'error' : 'ok');
        el.setAttribute('role', kind === 'error' ? 'alert' : 'status');

        var icon = doc.createElementNS('http://www.w3.org/2000/svg', 'svg');
        icon.setAttribute('class', 'icon');
        icon.setAttribute('aria-hidden', 'true');
        var use = doc.createElementNS('http://www.w3.org/2000/svg', 'use');
        use.setAttribute('href', kind === 'error' ? '#i-circle-x' : '#i-circle-check');
        icon.appendChild(use);

        var text = doc.createElement('span');
        text.textContent = message;

        el.appendChild(icon);
        el.appendChild(text);
        stack.appendChild(el);

        setTimeout(function () {
            el.classList.add('is-out');
            el.addEventListener('animationend', function () { el.remove(); }, { once: true });
        }, 5000);
    }

    /* ==================================================================== */
    function init() {
        initHeader();
        initDropdowns();
        initDrawer();
        initModals();
        initAccordions();
        initToTop();
        initScrollSpy();
    }

    if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', init);
    else init();

    window.RaflyUI = { toast: toast, openModal: openModal, closeModal: closeModal, trapFocus: trapFocus };
}());
