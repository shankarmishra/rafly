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
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Degrees the ring turns across one full pass of the section through the
       viewport. A third of a turn: enough that the movement is unmistakably
       tied to the scroll, little enough that a cell you were reading has not
       swung away by the time you finish the sentence. */
    var SCROLL_TURN = 120;

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
        /* Declared here, not beside the scroll handler below: reset() runs
           before that point and calls apply(), and a hoisted  would be
           undefined at that moment — which writes "--rot: NaNdeg". */
        var scrollRot = 0;
        var ticking = false;
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

        /* The single writer for --rot. index is the committed position, live is
           the in-flight drag, scrollRot is the section's scroll offset; every
           path sets its own part and calls this rather than writing the
           property, which is what keeps them from overwriting each other. */
        function apply(live) {
            if (flat) return;
            var base = live === undefined ? -index * step : live;
            root.style.setProperty('--rot', (base + scrollRot) + 'deg');
        }

        function render() {
            if (flat) return;
            apply();

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
                apply((-index * step) + (moved * 0.16));
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

        /* ------------------------------------------------------------------
           Scroll drive.

           The ring already answers to drag, the two arrows and the keyboard.
           What it did not answer to was the one input every visitor gives it:
           scrolling past. So the section's own progress through the viewport
           adds a rotation on top of whatever the index is set to, and the ring
           turns about a third of a turn while you read past it.

           It is ADDITIVE, deliberately. --rot stays owned by render() and the
           drag handler; this only contributes an offset, so there is still one
           source of truth for which cell is at the front and the arrows never
           fight the wheel. Under reduced motion, in flat mode, or with the
           section off screen it contributes nothing at all.
           ------------------------------------------------------------------ */
        function scrollDrive() {
            ticking = false;
            if (flat) return;

            var rect = root.getBoundingClientRect();
            var span = rect.height + window.innerHeight;
            if (rect.bottom < 0 || rect.top > window.innerHeight) return;

            // 0 as the section enters the bottom of the screen, 1 as it leaves
            // the top — the same normalisation js/gl.js uses for its dissolve.
            var p = (window.innerHeight - rect.top) / span;
            p = p < 0 ? 0 : p > 1 ? 1 : p;

            var turn = (p - 0.5) * SCROLL_TURN;
            if (Math.abs(turn - scrollRot) < 0.15) return;   // sub-pixel churn
            scrollRot = turn;
            apply();
        }

        function onScroll() {
            if (ticking) return;
            ticking = true;
            requestAnimationFrame(scrollDrive);
        }

        if (!reduceMotion) {
            window.addEventListener('scroll', onScroll, { passive: true });
            scrollDrive();
        }

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
