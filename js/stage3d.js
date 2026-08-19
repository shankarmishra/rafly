/**
 * stage3d.js — the live scene, and everything that has to be true before it
 * is allowed to exist.
 *
 * THE STILL IS THE PRODUCT. THIS IS THE UPGRADE.
 *
 * assets/render/core-hero-*.webp is rendered offline by inc/tools/
 * render-stills.mjs from the same js/assembly.js and js/studio.js this file
 * imports. It is in the markup as a plain <img>, it is what the browser
 * paints first, and it is what a phone, a crawler, a printer, a visitor with
 * reduced motion and a visitor with no WebGL see permanently. Nothing in
 * this file is required for the page to be finished.
 *
 * What this adds is one thing the still cannot do: the object comes apart as
 * you scroll, and the core is revealed running through all five modules.
 *
 * THE GATES — all of them, before a single byte of three.js is fetched
 * --------------------------------------------------------------------
 *   1. Viewport is at least 761px. Phones never download three.js. This is
 *      an explicit matchMedia test and not a CSS `display: none`, because a
 *      hidden canvas still costs the full 190KB — which is exactly the bug
 *      the previous build shipped when a class got renamed.
 *   2. WebGL2, or nothing. No WebGL1 path to maintain.
 *   3. Not prefers-reduced-motion.
 *   4. Not Save-Data, not 2g/slow-2g.
 *   5. Only after window `load`, then requestIdleCallback, with a 1500ms
 *      backstop. The LCP element is the hero headline; the canvas must never
 *      compete with it for bandwidth or main thread.
 *
 * Any failure at any point leaves the still exactly where it is. There is no
 * spinner, no blank canvas and no empty rounded rectangle — the three things
 * the audit found on the build this replaces.
 */

const stage = document.querySelector('[data-stage]');

if (stage) init();

function init() {
    const still = stage.querySelector('.stage-still');
    const canvas = stage.querySelector('.stage-canvas');
    const labelHost = stage.querySelector('.stage-labels');
    if (!canvas) return;

    /* ---------------------------------------------------------- the gates */
    if (!window.matchMedia('(min-width: 761px)').matches) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    if (!('IntersectionObserver' in window)) return;

    const net = navigator.connection;
    if (net && (net.saveData || /^(slow-)?2g$/.test(net.effectiveType || ''))) return;

    const probe = document.createElement('canvas');
    if (!probe.getContext('webgl2')) return;

    const start = () => boot({ stage, still, canvas, labelHost });
    const idle = () => (window.requestIdleCallback
        ? requestIdleCallback(start, { timeout: 1500 })
        : setTimeout(start, 200));

    document.readyState === 'complete'
        ? idle()
        : window.addEventListener('load', idle, { once: true });
}

async function boot({ stage, still, canvas, labelHost }) {
    let THREE, assembly, studio;
    try {
        [THREE, assembly, studio] = await Promise.all([
            import('../vendor/three/three.module.min.js'),
            import('./assembly.js'),
            import('./studio.js'),
        ]);
    } catch {
        return;                 /* the still stays. that is the whole plan. */
    }

    const renderer = new THREE.WebGLRenderer({
        canvas, antialias: true, alpha: true, powerPreference: 'high-performance',
    });
    renderer.setClearAlpha(0);
    /* Capped at 1.5. Above that the shadow map and the fill cost double for a
       difference nobody can see on a machined grey object, and the laptops
       that report dpr 2 are frequently the ones least able to pay for it. */
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 1.5));

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(28, 1, 0.1, 100);
    const rig = studio.buildStudio(THREE, renderer, scene, { quality: 'live' });
    const obj = assembly.buildAssembly(THREE);
    scene.add(obj.root);

    /* ------------------------------------------------------------- labels */
    const labels = labelHost ? Array.from(labelHost.querySelectorAll('.stage-label')) : [];
    const projected = new THREE.Vector3();

    /* ------------------------------------------------------- scroll state
       Two independent readings, not one, because the statement section sits
       between the hero and the assembly and takes an unpredictable amount of
       scroll — it is a paragraph, so its height depends on the viewport and
       on the reader's font size. Deriving both phases from a single
       whole-stage percentage made the choreography drift every time that
       paragraph rewrapped. Each phase now measures the element it belongs to. */
    let heroP = 0;              /* 0..1 as the hero scrolls away            */
    let openP = 0;              /* 0..1 through the assembly section        */
    let px = 0, py = 0;         /* damped pointer, -1..1                    */
    let tx = 0, ty = 0;         /* pointer target                           */
    let visible = true;
    let running = false;
    let w = 0, h = 0;

    const clamp01 = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
    const between = (v, a, b) => clamp01((v - a) / (b - a));
    const smooth = (x) => x * x * (3 - 2 * x);

    function measure() {
        const r = canvas.getBoundingClientRect();
        w = Math.max(1, Math.round(r.width));
        h = Math.max(1, Math.round(r.height));
        renderer.setSize(w, h, false);
    }

    const openEl = stage.querySelector('[data-stage-open]');

    function readScroll() {
        const vh = window.innerHeight;
        const hero = stage.querySelector('.hero');
        if (hero) {
            const r = hero.getBoundingClientRect();
            heroP = clamp01(-r.top / Math.max(1, r.height));
        }
        if (openEl) {
            const r = openEl.getBoundingClientRect();
            openP = clamp01(-r.top / Math.max(1, r.height - vh));
        }
    }

    /**
     * The choreography, in one place.
     *
     * HERO      heroP 0 -> 1 as the hero scrolls away. The object stays
     *           assembled and the camera barely moves; all that happens is
     *           the composition shift releasing and the topmost module
     *           giving one small hint that it comes apart.
     * ASSEMBLY  openP 0 -> 1 through the pinned section. 0.10-0.80 of that
     *           range is the opening; the last fifth is held still so the
     *           labels can be read without the thing moving underneath them.
     */
    function choreograph() {
        const hint = heroP * 0.05;
        const t = Math.max(smooth(between(openP, 0.10, 0.80)), hint);

        assembly.setExplode(obj, t);
        rig.setExplode(t);

        /* The camera walks its seven authored keys: a little during the hero,
           the bulk of the path across the assembly. */
        const cam = heroP * 0.06 + openP * 0.78;
        const target = studio.applyCamera(THREE, camera, cam, w / h);

        /* THE COMPOSITION SHIFT. In the hero the object belongs on the right
           third with the headline ranged left beside it; by the time the
           assembly opens it belongs in the middle of its own section. Look at
           a point to the object's LEFT and it moves RIGHT in frame — done on
           the camera rather than by translating the canvas, so the ground
           shadow travels with it instead of sliding underneath. */
        const shift = (1 - smooth(clamp01(heroP * 1.6))) * heroShift();
        if (shift !== 0 || px || py) {
            const dir = new THREE.Vector3().subVectors(target, camera.position).normalize();
            const right = new THREE.Vector3().crossVectors(dir, camera.up).normalize();
            const aim = target.clone().addScaledVector(right, -shift);

            /* Pointer parallax: at most 3 degrees of yaw and 2 of pitch,
               applied by orbiting the CAMERA around the subject. The object
               itself never moves, so it stays planted and its shadow stays
               put — which is the difference between parallax and wobble. */
            if (px || py) {
                const off = new THREE.Vector3().subVectors(camera.position, aim);
                off.applyAxisAngle(camera.up, -px * 0.052);
                off.applyAxisAngle(right, -py * 0.035);
                camera.position.copy(aim).add(off);
            }
            camera.lookAt(aim);
        }

        positionLabels(t);
    }

    function heroShift() {
        /* Zero, and that is a layout decision rather than a missing feature.
           The object is placed on the right of the screen by giving the
           sticky layer the right 54% of the viewport in CSS, so the canvas
           and the pre-rendered still occupy the SAME box and frame the object
           identically. Shifting the camera instead would have moved the live
           object and not the still, and the two are cross-faded over each
           other — any difference between them shows up as a jump at exactly
           the moment the upgrade lands. Composition in CSS, subject in the
           scene, and the two never have to agree about anything but the box. */
        return 0;
    }

    function positionLabels(t) {
        if (!labels.length) return;
        const on = t > 0.42;
        labelHost.classList.toggle('is-on', on);
        if (!on) return;
        labels.forEach((el, i) => {
            const [x, y, z] = assembly.anchorFor(obj, i);
            projected.set(x, y, z).project(camera);
            el.style.setProperty('--x', `${((projected.x + 1) / 2) * 100}%`);
            el.style.setProperty('--y', `${((1 - projected.y) / 2) * 100}%`);
            el.classList.toggle('is-behind', projected.z > 1);
        });
    }

    /* --------------------------------------------------------- the loop */
    let idleT = 0;
    function frame() {
        if (!running) return;
        requestAnimationFrame(frame);
        if (!visible) return;

        /* Damped pointer. The damping IS the premium feel: an undamped
           parallax reads as a jitter attached to the mouse. */
        px += (tx - px) * 0.055;
        py += (ty - py) * 0.055;

        /* Idle drift, hero only, and deliberately below the threshold where
           you would call it movement. The brief's test is that the viewer
           should ask "is it moving?" rather than see it spinning. */
        if (heroP < 0.12) {
            idleT += 0.0032;
            tx += (Math.sin(idleT) * 0.16 - tx) * 0.004;
            ty += (Math.sin(idleT * 0.73) * 0.10 - ty) * 0.004;
        }

        choreograph();
        renderer.render(scene, camera);
    }

    /* ------------------------------------------------------------ events */
    let queued = false;
    const onScroll = () => {
        if (queued) return;
        queued = true;
        requestAnimationFrame(() => { queued = false; readScroll(); });
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', () => { measure(); readScroll(); }, { passive: true });

    if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        window.addEventListener('pointermove', (e) => {
            if (heroP > 0.12) { tx = 0; ty = 0; return; }
            tx = (e.clientX / window.innerWidth) * 2 - 1;
            ty = (e.clientY / window.innerHeight) * 2 - 1;
        }, { passive: true });
    }

    /* Stop rendering entirely when the stage is off screen or the tab is
       hidden. A scene that keeps drawing behind the FAQ is the single most
       common way a well-built WebGL page still drains a laptop battery. */
    new IntersectionObserver(([e]) => { visible = e.isIntersecting; }, { rootMargin: '120px' })
        .observe(stage);
    document.addEventListener('visibilitychange', () => {
        visible = !document.hidden && visible;
    });

    canvas.addEventListener('webglcontextlost', (e) => {
        e.preventDefault();
        running = false;
        stage.classList.remove('is-live');   /* the still comes back */
    });

    /* ------------------------------------------------------------- start */
    measure();
    readScroll();
    choreograph();
    /* Two warm frames before the cross-fade: the first compiles shaders and
       fills the shadow map, and cross-fading onto a cold frame is how a
       "sometimes the shadow pops in" bug reaches production. */
    renderer.render(scene, camera);
    renderer.render(scene, camera);

    running = true;
    requestAnimationFrame(frame);

    /* The hand-off. The still does not disappear — it fades under a canvas
       showing the identical frame, so there is nothing to see at the moment
       of the swap. */
    requestAnimationFrame(() => stage.classList.add('is-live'));
    if (still) still.setAttribute('aria-hidden', 'true');
}
