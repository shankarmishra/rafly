/**
 * SMOOTH SCROLL — inertial wheel scrolling.
 *
 * The thing people mean when they say a site "feels expensive". A wheel notch
 * on a normal page jumps ~100px instantly; here it adds to a target and the
 * page eases toward it over a few frames, so a flick decelerates instead of
 * stopping dead. Every scroll-driven thing on this site — the pinned delivery
 * flow, the GL object's dissolve, the reveals — reads better against an eased
 * scroll than a stepped one, because they are all sampling a position that is
 * now continuous.
 *
 * WHY IT IS WRITTEN HERE RATHER THAN INSTALLED
 *   Lenis is the library that popularised this and it is MIT, so vendoring it
 *   would have been legitimate. It is not vendored for two reasons. The first
 *   is that this codebase ships zero third-party JavaScript and that is a claim
 *   worth keeping (assets/CREDITS.md). The second is that the integration is
 *   the hard part, not the easing: what must NOT be hijacked is a longer list
 *   than what must, and every item on it below is a real failure mode.
 *
 * WHAT IT DOES NOT TOUCH
 *   touch          phones already have inertial scrolling, tuned by the OS.
 *                  Overriding it is the single most common way a "smooth
 *                  scroll" site becomes unusable on a phone.
 *   scrollbar      dragging the bar sets scrollY directly; the loop re-syncs
 *                  to it rather than fighting it back.
 *   keyboard       Home/End/PageDown/arrows and, critically, tabbing to a
 *                  focused element must land exactly where the browser says.
 *   anchors        href="#id" keeps CSS scroll-behavior. One easing, not two
 *                  stacked on top of each other.
 *   locked body    while the mobile drawer or a modal is open the page must
 *                  not move at all.
 *   reduced motion off entirely. Someone who asked for less motion did not ask
 *                  for the page to keep gliding after they stopped.
 *
 * WHY window.scrollTo AND NOT A TRANSFORMED WRAPPER
 *   The other way to build this is to fix the body and translate a wrapper.
 *   That version breaks position:sticky, position:fixed, scroll-driven
 *   animation timelines and IntersectionObserver root maths — which is to say,
 *   it would break the pinned flow section, the header, css/10-scroll.css and
 *   js/scroll.js respectively. Writing the real scroll position each frame
 *   keeps every one of those correct, because as far as the rest of the page
 *   is concerned nothing unusual is happening.
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var coarse       = window.matchMedia('(pointer: coarse)').matches;

    if (reduceMotion || coarse) {
        return;
    }

    /* Damping rate, per second. FRAME-RATE INDEPENDENT: the fraction of the
       remaining distance covered each frame is 1 - e^(-LAMBDA * dt), so a
       120Hz display and a 60Hz one describe the same curve, and a dropped
       frame is absorbed rather than felt. A fixed per-frame lerp — the obvious
       first version of this — is what made the same page feel snappy on one
       machine and syrupy on another. 7.5 lands where Lenis' default sits: a
       flick decelerates visibly, one notch does not feel laggy. */
    var LAMBDA = 7.5;

    /* Pixels per wheel notch. Chrome reports ~100 for deltaMode 0; this is a
       little larger so one notch covers the same ground it would natively once
       the easing has eaten the edges. */
    var NOTCH = 118;

    /* Below this the loop stops and hands the position back to the browser.
       Sub-pixel scrollTo calls keep the compositor awake for nothing. */
    var EPSILON = 0.35;

    var target  = window.scrollY;
    var running = false;
    var raf     = null;

    /* THE ONE LINE THAT MAKES THIS WORK.
       01-reset.css sets `html { scroll-behavior: smooth }` for anchor links.
       window.scrollTo() honours that: every call starts the browser's OWN
       smooth animation toward the new position. Called sixty times a second
       from the loop below, that is sixty animations a second, each cancelled
       by the next — which is felt as the page catching and stuttering under
       the wheel. The property is switched to auto for exactly as long as the
       loop is driving, and restored the moment it stops, so anchor links keep
       their native easing. */
    var docEl = document.documentElement;
    function drive(y) {
        docEl.style.scrollBehavior = 'auto';
        window.scrollTo(0, y);
    }
    function release() {
        docEl.style.scrollBehavior = '';
    }

    function maxScroll() {
        return Math.max(0, document.documentElement.scrollHeight - window.innerHeight);
    }

    function locked() {
        var c = document.body.classList;
        return c.contains('nav-open') || c.contains('modal-open');
    }

    var lastT = 0;

    function frame(now) {
        // dt is clamped: a tab that was backgrounded returns with a huge dt,
        // and one giant step to the target is a jump cut, not a scroll.
        var dt = lastT ? Math.min(0.05, (now - lastT) / 1000) : 1 / 60;
        lastT = now;

        var current = window.scrollY;
        var delta   = target - current;

        if (Math.abs(delta) < EPSILON) {
            drive(target);
            release();
            running = false;
            raf = null;
            lastT = 0;
            return;
        }

        drive(current + delta * (1 - Math.exp(-LAMBDA * dt)));
        raf = requestAnimationFrame(frame);
    }

    function start() {
        if (running) return;
        running = true;
        lastT = 0;
        raf = requestAnimationFrame(frame);
    }

    function onWheel(e) {
        // Anything with its own scrollable box — a dropdown, the team overlay,
        // a code block — keeps its native scrolling. Without this check the
        // page moves while the pointer is over a list the visitor is reading.
        if (locked() || e.ctrlKey || insideScrollable(e.target)) {
            return;
        }

        var d = e.deltaY;

        // deltaMode 1 is lines, 2 is pages. Firefox reports lines.
        if (e.deltaMode === 1)      d *= NOTCH / 3;
        else if (e.deltaMode === 2) d *= window.innerHeight;

        e.preventDefault();

        /* Re-anchor to the REAL position, not to the stale target, whenever the
           two have drifted apart — the visitor dragged the scrollbar, followed
           an anchor, or the page resized under us. Without this the first wheel
           notch after any of those snaps back to wherever the target last was. */
        if (Math.abs(target - window.scrollY) > window.innerHeight) {
            target = window.scrollY;
        }

        target = Math.min(maxScroll(), Math.max(0, target + d));
        start();
    }

    /** Walks up from the event target looking for a box that can scroll itself. */
    function insideScrollable(node) {
        while (node && node !== document.body && node.nodeType === 1) {
            var style = window.getComputedStyle(node);
            var oy = style.overflowY;
            if ((oy === 'auto' || oy === 'scroll') && node.scrollHeight > node.clientHeight + 1) {
                return true;
            }
            node = node.parentNode;
        }
        return false;
    }

    /* Any scroll this file did not cause resets the target. Keyboard, anchor
       jumps, scrollbar drags, focus scrolls and scroll restoration all arrive
       here, and all of them mean "the page is somewhere new now". */
    window.addEventListener('scroll', function () {
        if (!running) target = window.scrollY;
    }, { passive: true });

    window.addEventListener('resize', function () {
        target = window.scrollY;
    }, { passive: true });

    // passive:false because the whole mechanism depends on preventDefault().
    window.addEventListener('wheel', onWheel, { passive: false });

    // A locked body means an overlay is open; drop any momentum still in flight
    // rather than letting the page glide to a stop behind it.
    document.addEventListener('click', function () {
        if (locked() && raf) {
            cancelAnimationFrame(raf);
            raf = null;
            running = false;
            release();
            target = window.scrollY;
        }
    }, true);
})();
