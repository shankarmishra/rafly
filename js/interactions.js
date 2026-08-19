/**
 * interactions.js — the reusable interaction layer.
 *
 * Everything here is a pattern used in more than one place. One-off hover
 * behaviour belongs in CSS next to the component; this file exists so that
 * "buttons lean toward the pointer" is written once rather than five times
 * with five slightly different damping constants.
 *
 * WHAT IS IN HERE
 *   magnetic   [data-magnetic]  — a control leans toward the pointer
 *   cursor     the desktop cursor and its states
 *   spotlight  enforces "only one .is-lit child per group", which CSS cannot
 *              express and which is the discipline behind the glow system
 *
 * THE RULES EVERY ONE OF THEM FOLLOWS
 *   - pointer: fine only. A magnetic button on a touchscreen is a control
 *     that moves out from under the thumb pressing it.
 *   - prefers-reduced-motion: reduce turns all of it off, and the page is
 *     complete without it. Nothing here communicates anything; it is all
 *     emphasis on something the markup already says.
 *   - keyboard users get the normal focus ring and no movement. A control
 *     that slides while focused is one whose focus ring lies about where it
 *     is.
 *   - ONE shared rAF loop for the whole file. Three components each running
 *     their own is how a page reaches 40fps with nothing visibly moving.
 *
 * No dependencies.
 */
(function () {
    'use strict';

    var doc = document;
    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)');
    var fine = window.matchMedia('(hover: hover) and (pointer: fine)');

    /** Everything that wants a frame registers here. One loop, one rAF. */
    var tickers = [];
    var raf = 0;

    function loop() {
        raf = requestAnimationFrame(loop);
        for (var i = 0; i < tickers.length; i++) tickers[i]();
    }
    function addTicker(fn) {
        tickers.push(fn);
        if (!raf) raf = requestAnimationFrame(loop);
    }

    /* ====================================================================
       MAGNETIC

       The control does not follow the pointer, it LEANS toward it: the offset
       is a fraction of the distance from its centre, hard-capped, and eased
       so it settles rather than tracking. Following the pointer exactly is
       the version of this effect that feels like a toy.
       ==================================================================== */
    function initMagnetic() {
        if (!fine.matches || reduced.matches) return;

        var items = [].slice.call(doc.querySelectorAll('[data-magnetic]'));
        if (!items.length) return;

        var MAX = 8;      // px. Past about ten it stops reading as a lean.
        var PULL = 0.28;  // fraction of the distance from centre

        var state = items.map(function (el) {
            return { el: el, x: 0, y: 0, tx: 0, ty: 0, on: false };
        });

        state.forEach(function (s) {
            s.el.addEventListener('pointerenter', function () { s.on = true; });
            s.el.addEventListener('pointermove', function (e) {
                var r = s.el.getBoundingClientRect();
                var dx = e.clientX - (r.left + r.width / 2);
                var dy = e.clientY - (r.top + r.height / 2);
                s.tx = Math.max(-MAX, Math.min(MAX, dx * PULL));
                s.ty = Math.max(-MAX, Math.min(MAX, dy * PULL));
            });
            s.el.addEventListener('pointerleave', function () {
                s.on = false; s.tx = 0; s.ty = 0;
            });
            /* Focus resets it and holds it reset: a focus ring has to sit on
               the control it is describing. */
            s.el.addEventListener('focus', function () { s.tx = 0; s.ty = 0; });
        });

        addTicker(function () {
            for (var i = 0; i < state.length; i++) {
                var s = state[i];
                s.x += (s.tx - s.x) * 0.18;
                s.y += (s.ty - s.y) * 0.18;
                // Below a tenth of a pixel, stop writing to the DOM at all.
                if (Math.abs(s.x) < 0.1 && Math.abs(s.y) < 0.1 && !s.on) {
                    if (s.el.style.translate !== '') s.el.style.translate = '';
                    continue;
                }
                s.el.style.translate = s.x.toFixed(2) + 'px ' + s.y.toFixed(2) + 'px';
            }
        });
    }

    /* ====================================================================
       CURSOR

       A dot that trails the pointer and grows over things worth clicking.

       IT IS NEVER THE ONLY SIGNAL. The real cursor is not hidden, every
       target keeps its own hover state, and any label it shows repeats
       something already on screen. Switch this file off and nothing becomes
       unusable — which is the test a custom cursor has to pass.
       ==================================================================== */
    function initCursor() {
        if (!fine.matches || reduced.matches) return;

        var dot = doc.createElement('div');
        dot.className = 'cursor';
        dot.setAttribute('aria-hidden', 'true');
        var label = doc.createElement('span');
        label.className = 'cursor-label';
        dot.appendChild(label);
        doc.body.appendChild(dot);

        var x = window.innerWidth / 2, y = window.innerHeight / 2;
        var tx = x, ty = y, shown = false;

        doc.addEventListener('pointermove', function (e) {
            tx = e.clientX; ty = e.clientY;
            if (!shown) { shown = true; dot.classList.add('is-on'); }
        }, { passive: true });

        doc.addEventListener('pointerleave', function () {
            shown = false;
            dot.classList.remove('is-on');
        });

        /* One delegated listener rather than one per target, so anything the
           page adds later is covered without re-binding. */
        doc.addEventListener('pointerover', function (e) {
            var t = e.target.closest
                ? e.target.closest('[data-cursor], a, button, .svc-row, .statement-item')
                : null;
            if (!t) {
                dot.className = 'cursor is-on';
                label.textContent = '';
                return;
            }
            var mode = t.getAttribute('data-cursor')
                || (t.matches('.svc-row, .statement-item') ? 'explore' : 'link');
            dot.className = 'cursor is-on is-' + mode;
            label.textContent = mode === 'explore' ? 'Explore'
                : mode === 'view' ? 'View'
                : mode === 'open' ? 'Open' : '';
        });

        addTicker(function () {
            x += (tx - x) * 0.22;
            y += (ty - y) * 0.22;
            dot.style.translate = x.toFixed(1) + 'px ' + y.toFixed(1) + 'px';
        });
    }

    /* ====================================================================
       SPOTLIGHT

       css/08-ground.css styles .is-lit and states the rule it cannot
       enforce: a container may only ever have ONE lit child. Two things
       glowing at once means neither is emphasised, which is the failure that
       turns a glow system into a light show.
       ==================================================================== */
    function initSpotlight() {
        var groups = [].slice.call(doc.querySelectorAll('[data-spotlight]'));

        groups.forEach(function (group) {
            var items = [].slice.call(group.querySelectorAll('.glow-anchor'));
            if (!items.length) return;

            function lightOnly(target) {
                items.forEach(function (el) {
                    el.classList.toggle('is-lit', el === target);
                });
            }

            items.forEach(function (el) {
                el.addEventListener('pointerenter', function () { lightOnly(el); });
                // Keyboard gets the same emphasis, via focus anywhere inside.
                el.addEventListener('focusin', function () { lightOnly(el); });
            });

            group.addEventListener('pointerleave', function () { lightOnly(null); });
            group.addEventListener('focusout', function (e) {
                if (!group.contains(e.relatedTarget)) lightOnly(null);
            });
        });
    }

    function init() {
        initMagnetic();
        initCursor();
        initSpotlight();
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init, { once: true });
    } else {
        init();
    }
}());
