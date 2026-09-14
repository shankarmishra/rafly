/**
 * motion.js — reveals, counters, split text, marquees.
 *
 * The entrance animations are primarily CSS (`animation-timeline: view()` in
 * css/06-motion.css). This file only does the parts CSS cannot:
 *
 *   • adds `.js-motion` so the no-JS page is never left with hidden content
 *   • runs an IntersectionObserver reveal ONLY where scroll timelines are
 *     unsupported — the two engines are mutually exclusive, which is what the
 *     previous build got wrong
 *   • counters, word splitting, and seamless marquee duplication
 *
 * No dependencies.
 */
(function () {
    'use strict';

    var doc = document;
    var root = doc.documentElement;

    root.classList.add('js-motion');

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var hasTimelines = !!(window.CSS && CSS.supports &&
        (CSS.supports('animation-timeline', 'view()') ||
         CSS.supports('animation-timeline', 'view(block)')));

    /* --------------------------------------------------------------------
       One shared observer. Anything that needs a "came into view" signal
       registers a callback on the element.
       -------------------------------------------------------------------- */
    var callbacks = new WeakMap();
    var io = 'IntersectionObserver' in window
        ? new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                var fn = callbacks.get(entry.target);
                io.unobserve(entry.target);
                callbacks.delete(entry.target);
                if (fn) fn(entry.target);
            });
        }, { threshold: 0.14, rootMargin: '0px 0px -40px 0px' })
        : null;

    function onceInView(el, fn) {
        if (!io || reduced) { fn(el); return; }
        callbacks.set(el, fn);
        io.observe(el);
    }

    function markIn(el) { el.classList.add('is-in'); }

    /* --------------------------------------------------------------------
       1. Reveals — fallback engine only
       -------------------------------------------------------------------- */
    function initReveals() {
        // Stagger indices are needed by both engines' group/lines rules.
        each('[data-r="group"], [data-r="lines"]', function (el) {
            var kids = el.children;
            for (var i = 0; i < kids.length; i++) {
                kids[i].style.setProperty('--i', String(i));
            }
        });

        if (hasTimelines || reduced) return;

        each('[data-r], .rafly-underline, .rafly-reveal, .rafly-stagger', function (el) {
            // A clipped element reports a zero-area intersection rect and would
            // never fire, so those are observed through their parent instead.
            var probe = el.getAttribute && el.getAttribute('data-r') === 'wipe' && el.parentElement
                ? el.parentElement
                : el;
            onceInView(probe, function () { markIn(el); });
        });

        /* The three ARRIVAL effects from the data-fx namespace.
           data-fx is otherwise deliberately native-only: drift, spin, zoom and
           tilt are CONTINUOUS, and an observer can only fire them once, after
           which they contradict the scroll position for the rest of the page.

           in-left, in-right and in-up are not continuous. They finish at
           `cover 42%` and end at a defined resting state — they are entrances
           that happen to live in the other namespace. Without this they simply
           never move on any engine that lacks scroll timelines, and side
           arrivals are now the site's main motion rather than a homepage
           flourish, so "it does nothing there" is not an acceptable answer. */
        each('[data-fx="in-left"], [data-fx="in-right"], [data-fx="in-up"]', function (el) {
            onceInView(el, function () { markIn(el); });
        });
    }

    /* --------------------------------------------------------------------
       2. Split text — wraps words so a headline can resolve word by word.
          The original text is preserved on aria-label so screen readers do
          not hear it letter-spaced into fragments.
       -------------------------------------------------------------------- */
    function splitText(el) {
        if (el.dataset.splitDone) return;
        var text = el.textContent.replace(/\s+/g, ' ').trim();
        if (!text) return;

        if (!el.getAttribute('aria-label')) el.setAttribute('aria-label', text);

        var words = text.split(' ');
        var frag = doc.createDocumentFragment();
        words.forEach(function (word, i) {
            var span = doc.createElement('span');
            span.className = 'split-word';
            span.setAttribute('aria-hidden', 'true');
            span.style.setProperty('--w', String(i));
            span.textContent = word;
            frag.appendChild(span);
            if (i < words.length - 1) frag.appendChild(doc.createTextNode(' '));
        });
        el.textContent = '';
        el.appendChild(frag);
        el.dataset.splitDone = '1';
    }

    function initSplit() {
        each('[data-split]', function (el) {
            splitText(el);
            onceInView(el, markIn);
        });
    }

    /* --------------------------------------------------------------------
       3. Counters
       -------------------------------------------------------------------- */
    function easeOutExpo(t) { return t === 1 ? 1 : 1 - Math.pow(2, -10 * t); }

    function runCounter(el) {
        var target = parseFloat(el.dataset.count);
        if (isNaN(target)) return;
        var suffix = el.dataset.suffix || '';
        var dur = parseInt(el.dataset.duration, 10) || 1500;
        var decimals = (String(target).split('.')[1] || '').length;

        if (reduced) {
            el.textContent = target.toFixed(decimals) + suffix;
            return;
        }

        var start = null;
        function frame(now) {
            if (start === null) start = now;
            var p = Math.min((now - start) / dur, 1);
            var value = target * easeOutExpo(p);
            el.textContent = value.toFixed(decimals) + suffix;
            if (p < 1) requestAnimationFrame(frame);
            else el.textContent = target.toFixed(decimals) + suffix;
        }
        requestAnimationFrame(frame);
    }

    function initCounters() {
        // The real number is already in the markup and is deliberately left
        // there: a visitor whose JS is blocked or slow must not read "0
        // Projects Delivered", which is not merely un-animated but actively
        // false. runCounter's first frame writes ~0 anyway, so the count-up
        // still starts from zero wherever it does run.
        each('[data-count]', function (el) {
            onceInView(el, runCounter);
        });
    }

    /* --------------------------------------------------------------------
       4. Marquee — the CSS animates to -50%, so the track has to hold exactly
          two copies of its content for the loop to be seamless.
       -------------------------------------------------------------------- */
    function initMarquees() {
        each('.marquee-track', function (track) {
            if (track.dataset.cloned) return;
            var original = Array.prototype.slice.call(track.children);
            if (!original.length) return;
            original.forEach(function (node) {
                var copy = node.cloneNode(true);
                copy.setAttribute('aria-hidden', 'true');
                // Clones must not be reachable by keyboard or a screen reader.
                copy.querySelectorAll('a, button, input').forEach(function (f) {
                    f.setAttribute('tabindex', '-1');
                });
                track.appendChild(copy);
            });
            track.dataset.cloned = '1';
        });
    }

    /* --------------------------------------------------------------------
       5. Page Component Interactions (Tilt, Filtering, Calculators, Scroll)
       -------------------------------------------------------------------- */
    function initAboutEcoStage() {
        var stage = doc.querySelector('.rafly-eco-stage');
        if (!stage || reduced) return;
        var ticking = false, rect = null;
        stage.addEventListener('mouseenter', function() { rect = stage.getBoundingClientRect(); });
        stage.addEventListener('mousemove', function (e) {
            if (ticking) return;
            ticking = true;
            var clientX = e.clientX, clientY = e.clientY;
            requestAnimationFrame(function() {
                if (!rect) rect = stage.getBoundingClientRect();
                var x = clientX - rect.left - rect.width / 2;
                var y = clientY - rect.top - rect.height / 2;
                stage.style.transform = 'perspective(1000px) rotateY(' + (x * 0.025) + 'deg) rotateX(' + (-y * 0.025) + 'deg) translateY(-4px)';
                ticking = false;
            });
        });
        stage.addEventListener('mouseleave', function () {
            rect = null;
            stage.style.transform = '';
        });
    }

    function initSvcEcoStages() {
        if (reduced) return;
        var ecoStages = doc.querySelectorAll('.svc-eco-stage');
        ecoStages.forEach(function (stage) {
            stage.style.transition = 'transform 0.15s ease-out';
            var ticking = false, rect = null;
            stage.addEventListener('mouseenter', function() { rect = stage.getBoundingClientRect(); });
            stage.addEventListener('mousemove', function (e) {
                if (ticking) return;
                ticking = true;
                var clientX = e.clientX, clientY = e.clientY;
                requestAnimationFrame(function() {
                    if (!rect) rect = stage.getBoundingClientRect();
                    var x = clientX - rect.left;
                    var y = clientY - rect.top;
                    var centerX = rect.width / 2;
                    var centerY = rect.height / 2;
                    var rotateX = ((y - centerY) / centerY) * -6;
                    var rotateY = ((x - centerX) / centerX) * 6;
                    stage.style.transform = 'perspective(1000px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg) scale(1.01)';
                    ticking = false;
                });
            });
            stage.addEventListener('mouseleave', function () {
                rect = null;
                stage.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale(1)';
            });
        });
    }

    function initLocHubCards() {
        var cards = doc.querySelectorAll('.loc-hub-card-light');
        if (!cards.length) return;
        cards.forEach(function (card) {
            if (!reduced) {
                card.addEventListener('mousemove', function (e) {
                    var rect = card.getBoundingClientRect();
                    var x = e.clientX - rect.left;
                    var y = e.clientY - rect.top;
                    var centerX = rect.width / 2;
                    var centerY = rect.height / 2;
                    var rotateX = ((y - centerY) / centerY) * -5;
                    var rotateY = ((x - centerX) / centerX) * 5;
                    card.style.transform = 'translateY(-6px) rotateX(' + rotateX.toFixed(2) + 'deg) rotateY(' + rotateY.toFixed(2) + 'deg)';
                });
                card.addEventListener('mouseleave', function () {
                    card.style.transform = 'translateY(0) rotateX(0) rotateY(0)';
                });
            }
            card.addEventListener('click', function () {
                var intakeSection = doc.getElementById('intake');
                if (intakeSection) {
                    intakeSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }

    function initTeamDiscCards() {
        var discCards = doc.querySelectorAll('.team-disc-card-light');
        discCards.forEach(function (card) {
            card.addEventListener('click', function () {
                var intakeSection = doc.getElementById('intake');
                if (intakeSection) {
                    intakeSection.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    }

    function initPricingCalculator() {
        var checks = doc.querySelectorAll('.calc-check');
        if (!checks.length) return;
        var scoreText = doc.getElementById('calcScoreText');
        var tierText = doc.getElementById('calcTierText');
        var heroBadge = doc.getElementById('heroScoreBadge');

        function updateScore() {
            var score = 20;
            checks.forEach(function (chk) {
                if (chk.checked) {
                    score += parseInt(chk.getAttribute('data-score') || '0', 10);
                }
            });
            score = Math.min(score, 100);

            if (scoreText) scoreText.textContent = score + ' / 100';
            if (heroBadge) heroBadge.textContent = 'SCORE: ' + score + '/100';

            var tier = 'Starter Scope';
            if (score >= 45 && score < 75) tier = 'Growth Build';
            if (score >= 75) tier = 'Enterprise System';

            if (tierText) tierText.textContent = tier;
        }

        checks.forEach(function (chk) { chk.addEventListener('change', updateScore); });
        updateScore();
    }

    function initCaseStudiesPage() {
        var cursor = doc.getElementById('workCursor');
        if (cursor && window.matchMedia('(pointer: fine)').matches) {
            doc.addEventListener('mousemove', function (e) {
                cursor.style.left = e.clientX + 'px';
                cursor.style.top = e.clientY + 'px';
            });

            doc.querySelectorAll('.work-card-light, .btn, .work-filter-btn').forEach(function (interactiveEl) {
                interactiveEl.addEventListener('mouseenter', function () { cursor.classList.add('is-hovering'); });
                interactiveEl.addEventListener('mouseleave', function () { cursor.classList.remove('is-hovering'); });
            });
        }

        var filterBtns = doc.querySelectorAll('.work-filter-btn');
        var cards = doc.querySelectorAll('.work-card-light');
        if (!filterBtns.length || !cards.length) return;

        filterBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterBtns.forEach(function (b) { b.classList.remove('is-active'); });
                btn.classList.add('is-active');

                var filter = btn.getAttribute('data-filter');
                cards.forEach(function (card) {
                    var categories = card.getAttribute('data-category') || '';
                    if (filter === 'all' || categories.indexOf(filter) !== -1) {
                        card.style.display = 'grid';
                        setTimeout(function () {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    } else {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(15px)';
                        setTimeout(function () { card.style.display = 'none'; }, 300);
                    }
                });
            });
        });
    }

    /* --------------------------------------------------------------------
       Utils
       -------------------------------------------------------------------- */
    function each(selector, fn) {
        Array.prototype.forEach.call(doc.querySelectorAll(selector), fn);
    }

    function init() {
        initReveals();
        initSplit();
        initCounters();
        initMarquees();
        initAboutEcoStage();
        initSvcEcoStages();
        initLocHubCards();
        initTeamDiscCards();
        initPricingCalculator();
        initCaseStudiesPage();
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.RaflyMotion = { init: init, splitText: splitText, onceInView: onceInView };
}());

