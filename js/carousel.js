/**
 * carousel.js — the 3-D perspective carousel.
 *
 * Cells are arranged on a cylinder: each is rotated by `n * step` around Y and
 * pushed out by the radius, and the whole ring counter-rotates to bring one
 * cell to the front. The radius is computed from the measured cell width so
 * neighbours sit edge to edge at any viewport size rather than being guessed.
 *
 * Below 760px, and under prefers-reduced-motion, css/07-fx.css turns the same
 * markup into a horizontal snap strip and this file stands down completely.
 *
 * No dependencies.
 */
(function () {
    'use strict';

    var doc = document;
    var flatQuery = window.matchMedia('(max-width: 760px), (prefers-reduced-motion: reduce)');

    function Carousel(root) {
        var ring = root.querySelector('.carousel-ring');
        var cells = Array.prototype.slice.call(root.querySelectorAll('.carousel-cell'));
        if (!ring || cells.length < 2) return;

        var realCount = cells.length;

        /* A ring needs enough faces to close.
           Five services at 360/5 = 72deg turns every neighbour edge-on, and
           capping the step instead leaves a 200deg arc with nothing behind the
           first card — so bringing item 1 to the front shows empty space on its
           left. Padding the ring with aria-hidden copies until it has at least
           eight faces fixes both: a ~36deg step, and a card always flanking on
           each side.

           The copies follow the same contract js/motion.js uses for marquee
           clones — hidden from the accessibility tree and out of the tab order,
           so nothing is announced or focusable twice. */
        if (realCount < 8) {
            var copies = Math.ceil(8 / realCount) - 1;
            for (var c = 0; c < copies; c++) {
                for (var n = 0; n < realCount; n++) {
                    var clone = cells[n].cloneNode(true);
                    clone.setAttribute('aria-hidden', 'true');
                    clone.dataset.clone = '1';
                    Array.prototype.forEach.call(clone.querySelectorAll('a, button'), function (f) {
                        f.setAttribute('tabindex', '-1');
                    });
                    ring.appendChild(clone);
                }
            }
            cells = Array.prototype.slice.call(root.querySelectorAll('.carousel-cell'));
        }

        var count = cells.length;
        var step = 360 / count;
        var index = 0;
        var dots = [];
        var flat = flatQuery.matches;

        var prev = root.querySelector('[data-carousel="prev"]');
        var next = root.querySelector('[data-carousel="next"]');
        var dotWrap = root.querySelector('.carousel-dots');
        var live = root.querySelector('[data-carousel-status]');

        /* ---------------------------------------------------------------- */
        function layout() {
            root.style.setProperty('--step', step + 'deg');
            cells.forEach(function (cell, i) { cell.style.setProperty('--n', String(i)); });

            // radius = (w / 2) / tan(step / 2) — the exact distance at which
            // adjacent faces of a regular polygon meet.
            var width = cells[0].getBoundingClientRect().width || 240;
            var radians = (step / 2) * Math.PI / 180;
            var radius = Math.round((width / 2) / Math.tan(radians));
            root.style.setProperty('--radius', radius + 'px');
        }

        function render() {
            if (flat) return;
            root.style.setProperty('--rot', (-index * step) + 'deg');

            cells.forEach(function (cell, i) {
                var active = i === index;
                if (cell.dataset.clone) return;   // copies stay hidden, always
                cell.setAttribute('aria-hidden', active ? 'false' : 'true');
                // Everything behind the front face is out of the tab order.
                Array.prototype.forEach.call(cell.querySelectorAll('a, button'), function (f) {
                    if (active) f.removeAttribute('tabindex');
                    else f.setAttribute('tabindex', '-1');
                });
            });

            var real = index % realCount;
            dots.forEach(function (dot, i) {
                dot.classList.toggle('is-on', i === real);
                dot.setAttribute('aria-selected', i === real ? 'true' : 'false');
            });

            if (live) live.textContent = 'Item ' + (real + 1) + ' of ' + realCount;
        }

        function go(n) {
            index = ((n % count) + count) % count;
            render();
        }

        /* ---------------------------------------------------------------- */
        function buildDots() {
            if (!dotWrap) return;
            dotWrap.innerHTML = '';
            dotWrap.setAttribute('role', 'tablist');
            cells.slice(0, realCount).forEach(function (cell, i) {
                var dot = doc.createElement('button');
                dot.type = 'button';
                dot.className = 'carousel-dot';
                dot.setAttribute('role', 'tab');
                var title = cell.querySelector('h2, h3, .carousel-cell-label h3');
                dot.setAttribute('aria-label', 'Show ' + (title ? title.textContent.trim() : 'item ' + (i + 1)));
                dot.addEventListener('click', function () {
                    var delta = ((i - (index % realCount)) + realCount) % realCount;
                    if (delta > realCount / 2) delta -= realCount;
                    go(index + delta);
                });
                dotWrap.appendChild(dot);
                dots.push(dot);
            });
        }

        /* ---------------------------------------------------------------- */
        function initDrag() {
            var startX = 0;
            var dragging = false;
            var moved = 0;

            root.addEventListener('pointerdown', function (e) {
                if (flat || e.button !== 0) return;
                dragging = true;
                moved = 0;
                startX = e.clientX;
                root.classList.add('is-dragging');
                root.setPointerCapture(e.pointerId);
            });

            root.addEventListener('pointermove', function (e) {
                if (!dragging) return;
                moved = e.clientX - startX;
                var live = (-index * step) + (moved * 0.16);
                root.style.setProperty('--rot', live + 'deg');
            });

            function end() {
                if (!dragging) return;
                dragging = false;
                root.classList.remove('is-dragging');
                if (Math.abs(moved) > 44) go(index + (moved < 0 ? 1 : -1));
                else render();
            }
            root.addEventListener('pointerup', end);
            root.addEventListener('pointercancel', end);
            root.addEventListener('lostpointercapture', end);
        }

        /* ---------------------------------------------------------------- */
        if (prev) prev.addEventListener('click', function () { go(index - 1); });
        if (next) next.addEventListener('click', function () { go(index + 1); });

        root.setAttribute('tabindex', '0');
        root.setAttribute('role', 'group');
        root.addEventListener('keydown', function (e) {
            if (flat) return;
            if (e.key === 'ArrowLeft')  { e.preventDefault(); go(index - 1); }
            if (e.key === 'ArrowRight') { e.preventDefault(); go(index + 1); }
            if (e.key === 'Home')       { e.preventDefault(); go(0); }
            if (e.key === 'End')        { e.preventDefault(); go(count - 1); }
        });

        buildDots();
        initDrag();

        function reset() {
            flat = flatQuery.matches;
            if (flat) {
                root.style.removeProperty('--rot');
                cells.forEach(function (cell) {
                    // The padding copies stay hidden — CSS removes them from the
                    // flat strip, and un-hiding them here would put duplicates
                    // back into the accessibility tree.
                    if (cell.dataset.clone) return;
                    cell.setAttribute('aria-hidden', 'false');
                    Array.prototype.forEach.call(cell.querySelectorAll('a, button'), function (f) {
                        f.removeAttribute('tabindex');
                    });
                });
                return;
            }
            layout();
            render();
        }

        reset();

        var resizeTimer = null;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(reset, 160);
        });
        flatQuery.addEventListener('change', reset);
    }

    function init() {
        Array.prototype.forEach.call(doc.querySelectorAll('.carousel'), function (root) {
            new Carousel(root);
        });
    }

    if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', init);
    else init();
}());
