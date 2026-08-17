/* ==========================================================================
   team.js — the team page's detail overlay. No dependencies.

   Loaded only on /team, via $page['scripts'] => ['team'] and the
   js/pages/{name}.js hook in partials/tail.php.

   WHAT THIS REPLACES, AND WHAT IT DOES NOT
   Every card on the page is a native <details>. That markup stays exactly as
   it is and keeps working with this file absent, blocked or broken. What this
   file adds is an interception: a click on a summary is prevented from
   toggling its own card and drives one shared overlay instead. One detail card
   exists on the page rather than one per person, so stepping to the next
   member is a re-render rather than a close-and-reopen.

   WHY THE DATA COMES FROM JSON RATHER THAN THE CARDS
   Scraping the clicked card would mean re-parsing markup that has already been
   escaped once, and it ties the overlay's content to the card's layout — change
   the card and the overlay silently loses a field. One payload in #team-data,
   indexed by data-team-index, has neither problem, and it is also what let the
   original version survive the cards being cloned into a looping strip.

   Text is written with textContent throughout. The only innerHTML in the file
   is a fixed <use href="#i-github"> sprite reference built from a literal, so
   no database value ever reaches a markup parser.
   ========================================================================== */

(function () {
    'use strict';

    var doc = document;

    var grid    = doc.querySelector('.team-grid');
    var overlay = doc.getElementById('teamDetail');
    var dataEl  = doc.getElementById('team-data');
    if (!grid || !overlay || !dataEl) return;

    var people;
    try {
        people = JSON.parse(dataEl.textContent || '[]');
    } catch (err) {
        return;   // Malformed payload: leave the <details> fallback in charge.
    }
    if (!Array.isArray(people) || !people.length) return;

    var stage    = overlay.querySelector('.team-detail-stage');
    var elPhoto  = doc.getElementById('teamDetailPhoto');
    var elIcon   = overlay.querySelector('.team-detail-avatar-icon');
    var elName   = doc.getElementById('teamDetailName');
    var elRole   = doc.getElementById('teamDetailRole');
    var elBrief  = doc.getElementById('teamDetailBrief');
    var elBio    = doc.getElementById('teamDetailBio');
    var elLinks  = doc.getElementById('teamDetailLinks');
    var elCount  = doc.getElementById('teamDetailCount');
    var elScroll = overlay.querySelector('.team-detail-scroll');
    if (!stage || !elName) return;

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* Same list js/ui.js uses for the modal and the drawer. Duplicated rather
       than exported, because ui.js keeps it inside its own IIFE and widening
       that to a global to save nine tokens would be the worse trade. */
    var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), textarea:not([disabled]), select:not([disabled]), [tabindex]:not([tabindex="-1"])';

    var current     = -1;
    var isOpen      = false;
    var returnFocus = null;

    /* ---------------------------------------------------------------------
       Helpers
       --------------------------------------------------------------------- */

    function focusSafely(el) {
        if (!el) return;
        try {
            el.focus({ preventScroll: true });
        } catch (err) {
            el.focus();
        }
    }

    function summaryFor(i) {
        var card = grid.querySelector('.team-card[data-team-index="' + i + '"]');
        return card ? card.querySelector('.team-card-head') : null;
    }

    function trapFocus(container, e) {
        var items = Array.prototype.filter.call(
            container.querySelectorAll(FOCUSABLE),
            function (el) { return el.offsetParent !== null; }
        );
        if (!items.length) return;

        var first = items[0];
        var last  = items[items.length - 1];

        if (e.shiftKey && doc.activeElement === first) {
            e.preventDefault();
            focusSafely(last);
        } else if (!e.shiftKey && doc.activeElement === last) {
            e.preventDefault();
            focusSafely(first);
        }
    }

    /**
     * network is always one of our own two literals, so the sprite markup is
     * fixed; href and the label are set as properties and never concatenated
     * into HTML.
     */
    function buildLink(href, network, personName) {
        var id = network === 'GitHub' ? 'github' : 'linkedin';

        var a = doc.createElement('a');
        a.className = 'chip';
        a.href = href;
        a.target = '_blank';
        a.rel = 'noopener noreferrer';
        a.setAttribute('aria-label', personName + ' on ' + network);
        a.innerHTML = '<svg class="icon icon-fill" aria-hidden="true" focusable="false">'
                    + '<use href="#i-' + id + '"></use></svg>';

        var span = doc.createElement('span');
        span.textContent = network;
        a.appendChild(span);
        return a;
    }

    /* ---------------------------------------------------------------------
       Render
       --------------------------------------------------------------------- */

    function render(i) {
        var p = people[i];
        if (!p) return;
        current = i;

        if (p.p) {
            elPhoto.src = p.p;
            elPhoto.alt = p.a || p.n;
            elPhoto.hidden = false;
            if (elIcon) elIcon.hidden = true;
        } else {
            // removeAttribute, not src='': an empty src resolves against the
            // document URL, so the browser fetches the page itself as an image
            // and reports a broken one — hidden or not.
            elPhoto.hidden = true;
            elPhoto.removeAttribute('src');
            elPhoto.alt = '';
            if (elIcon) elIcon.hidden = false;
        }

        elName.textContent = p.n || '';
        elRole.textContent = p.r || '';

        elBrief.textContent = p.b || '';
        elBrief.hidden = !p.b;

        elBio.textContent = p.d || '';
        elBio.hidden = !p.d;

        elLinks.textContent = '';
        if (p.g) elLinks.appendChild(buildLink(p.g, 'GitHub', p.n));
        if (p.l) elLinks.appendChild(buildLink(p.l, 'LinkedIn', p.n));
        elLinks.hidden = !(p.g || p.l);

        elCount.textContent = (i + 1) + ' of ' + people.length;

        if (elScroll) elScroll.scrollTop = 0;
    }

    /**
     * Restarting a CSS animation needs the class gone, a forced reflow, then
     * the class back — without the reflow read the browser coalesces both
     * mutations into one frame and nothing replays.
     */
    function animateSwap(dir) {
        if (reduceMotion) return;
        stage.classList.remove('is-swap-next', 'is-swap-prev');
        void stage.offsetWidth;
        stage.classList.add(dir > 0 ? 'is-swap-next' : 'is-swap-prev');
    }

    /* ---------------------------------------------------------------------
       Open / close / step
       --------------------------------------------------------------------- */

    function open(i, trigger) {
        if (!people[i]) return;

        returnFocus = trigger || summaryFor(i);
        render(i);

        overlay.hidden = false;
        doc.body.classList.add('modal-open');      // shared scroll lock
        isOpen = true;

        // Un-hide, then add .is-open next frame so the fan-out has a state to
        // transition from — the drawer and modal in ui.js do the same.
        requestAnimationFrame(function () {
            overlay.classList.add('is-open');
            focusSafely(overlay.querySelector('.team-detail-close'));
        });
    }

    function close() {
        if (!isOpen) return;
        isOpen = false;

        overlay.classList.remove('is-open', 'is-swap-next', 'is-swap-prev');
        stage.classList.remove('is-swap-next', 'is-swap-prev');
        doc.body.classList.remove('modal-open');

        var done = function () {
            if (!overlay.classList.contains('is-open')) overlay.hidden = true;
            overlay.removeEventListener('transitionend', done);
        };
        overlay.addEventListener('transitionend', done);
        setTimeout(done, 420);   // transitionend never fires under reduced motion

        if (returnFocus && doc.contains(returnFocus)) focusSafely(returnFocus);
        returnFocus = null;
    }

    /**
     * Wraps at both ends. returnFocus deliberately stays on whatever opened the
     * overlay rather than following the steps: a dialog should hand focus back
     * to where the user left it, not to a card they have never seen.
     */
    function step(delta) {
        var next = (current + delta + people.length) % people.length;
        if (next === current) return;
        render(next);
        animateSwap(delta);
    }

    /* ---------------------------------------------------------------------
       Wiring
       --------------------------------------------------------------------- */

    grid.addEventListener('click', function (e) {
        var head = e.target.closest ? e.target.closest('.team-card-head') : null;
        if (!head) return;

        var card = head.closest('.team-card');
        if (!card || card.dataset.teamIndex === undefined) return;

        var idx = parseInt(card.dataset.teamIndex, 10);
        if (isNaN(idx) || !people[idx]) return;

        // The default action of a click on <summary> is the toggle. Preventing
        // it here also covers Enter and Space, which the browser delivers as
        // clicks — so the keyboard path needs no separate handler.
        e.preventDefault();
        open(idx, head);
    });

    overlay.addEventListener('click', function (e) {
        var t = e.target;
        if (!t.closest) return;
        if (t.closest('[data-team-close]')) { close(); return; }
        if (t.closest('[data-team-prev]'))  { step(-1); return; }
        if (t.closest('[data-team-next]'))  { step(1); }
    });

    doc.addEventListener('keydown', function (e) {
        if (!isOpen) return;

        if (e.key === 'Escape')     { e.preventDefault(); close();  return; }
        if (e.key === 'ArrowRight') { e.preventDefault(); step(1);  return; }
        if (e.key === 'ArrowLeft')  { e.preventDefault(); step(-1); return; }
        if (e.key === 'Tab')        { trapFocus(stage, e); }
    });

    /* Swipe, for the phone layout where the two step buttons sit at the bottom
       of a tall card. Horizontal intent only (|dx| must beat |dy|), so it never
       competes with scrolling a long biography. */
    var swipeX = 0, swipeY = 0, swiping = false;

    stage.addEventListener('pointerdown', function (e) {
        if (e.pointerType === 'mouse') return;
        swipeX = e.clientX;
        swipeY = e.clientY;
        swiping = true;
    }, { passive: true });

    stage.addEventListener('pointerup', function (e) {
        if (!swiping) return;
        swiping = false;

        var dx = e.clientX - swipeX;
        var dy = e.clientY - swipeY;
        if (Math.abs(dx) < 55 || Math.abs(dx) <= Math.abs(dy)) return;

        step(dx < 0 ? 1 : -1);
    }, { passive: true });
}());
