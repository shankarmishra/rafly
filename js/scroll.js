/**
 * STICKY STORYTELLING — active-step tracking.
 *
 * The ONLY thing this file does that CSS cannot: decide which of several
 * sibling steps is currently nearest the reading position. That is a
 * comparison across siblings, and neither :has() nor a scroll timeline can
 * express "the one closest to the middle".
 *
 * Everything else about the pattern — the pinning, the rail, the dimming, the
 * mobile collapse, the reduced-motion behaviour — is CSS in 06-motion.css. If
 * this file never loads, the section still pins, still reads, and every step
 * stays legible at opacity 0.55. Nothing here is load-bearing.
 *
 * Deliberately NOT a scroll listener. An IntersectionObserver with a thin
 * horizontal band as its root margin tells us what has crossed the reading
 * line, for free, off the main thread.
 */
(function () {
    'use strict';

    if (!('IntersectionObserver' in window)) {
        return;   // CSS base state is legible; nothing to degrade to.
    }

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function initScroller(scroller) {
        var steps = [].slice.call(scroller.querySelectorAll('.st-step'));
        if (steps.length < 2) {
            return;
        }

        var stage = scroller.querySelector('.st-stage');
        var stepsWrap = scroller.querySelector('.st-steps');

        /* The reading line.
           rootMargin shrinks the viewport to a band across its middle, so a
           step "intersects" only while it is in the region a person is
           actually reading. -45%/-45% leaves a 10%-tall band; anything much
           thinner and fast scrolling can jump the band entirely without ever
           reporting an intersection. */
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;

                var i = steps.indexOf(entry.target);
                if (i < 0) return;

                setActive(i);
            });
        }, { rootMargin: '-45% 0px -45% 0px', threshold: 0 });

        steps.forEach(function (s) { observer.observe(s); });

        var current = -1;

        function setActive(i) {
            if (i === current) return;
            current = i;

            for (var n = 0; n < steps.length; n++) {
                steps[n].classList.toggle('is-active', n === i);
            }

            /* Rail progress, for browsers without scroll-driven animations.
               Where those ARE supported the CSS fills the rail natively and
               06-motion.css overrides height, so writing this property is
               harmless there rather than conflicting. */
            if (stepsWrap && !reduceMotion) {
                var pct = ((i + 1) / steps.length) * 100;
                stepsWrap.style.setProperty('--st-progress', pct.toFixed(1) + '%');
            }

            /* Let the pinned visual respond to the step, if it wants to.
               data-st-target on the stage names a custom property to write the
               index into; a stage that does not declare one is simply not
               told, which keeps the two decoupled. */
            if (stage) {
                stage.style.setProperty('--st-index', i);
                stage.setAttribute('data-st-step', String(i));
            }
        }

        // Mark the first step active immediately, so a section entered from
        // above never shows every step dimmed with none current.
        setActive(0);
    }

    function init() {
        var scrollers = document.querySelectorAll('.st-scroller');
        for (var i = 0; i < scrollers.length; i++) {
            initScroller(scrollers[i]);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
