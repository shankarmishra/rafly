/**
 * stage3d.js — the real 3-D object on the P.E.A.C.E. section.
 *
 * The only ES module on the site, and the only file that touches three.js.
 * Everything about it is opt-in and reversible:
 *
 *   • three.js is SELF-HOSTED in vendor/three/. The CSP is `script-src 'self'`,
 *     so a CDN would need a policy change; a local copy needs none — the same
 *     reasoning that puts the fonts in vendor/fonts/.
 *   • It is loaded with a dynamic import(), and only once the section is near
 *     the viewport, WebGL2 is available, and motion is not reduced. A visitor
 *     who never scrolls that far never downloads it.
 *   • If the import fails for ANY reason — the file is not vendored yet, the
 *     network drops, the device refuses a context — nothing is thrown at the
 *     visitor. `.three-stage-still` stays visible, and that still is a designed
 *     object in its own right rather than a placeholder.
 *
 * The object itself is built from three.js primitives rather than loaded from
 * a model file, so the section is complete without shipping any binary asset.
 * Swapping in a CC0 glTF later means replacing buildObject() and nothing else.
 */

const stages = document.querySelectorAll('[data-stage3d]');
if (stages.length) init();

function init() {
    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (reduced) return;

    /* three.js is ~730 KB raw / ~190 KB gzipped. That is a fair price for a
       real lit object on a desktop connection and an unfair one on a metered
       phone, so the two signals a browser actually gives us are honoured:
       Save-Data, and an effectiveType at or below 2G. Both mean the still
       shows — and the still is a designed object, not an empty box. */
    const net = navigator.connection;
    if (net && (net.saveData || /(^|-)2g$/.test(net.effectiveType || ''))) return;

    // WebGL2 or nothing — the fallback still is good enough that a half-working
    // WebGL1 path is not worth carrying.
    const probe = document.createElement('canvas');
    if (!probe.getContext('webgl2')) return;

    /* WHEN, not just whether.

       The P.E.A.C.E. stage is far down the page, so its IntersectionObserver
       is the whole gate: scroll to it or never pay for it. The hero and the
       secondary-page heads are ABOVE THE FOLD, so the observer fires on the
       first frame and three.js — ~730 KB raw — lands in the middle of the
       initial render, competing with the CSS, the fonts and the photographs
       for the one thing that actually matters, which is how fast the words
       appear.

       They do not need to win that race. Both objects cross-fade in over a
       designed still that is already on screen, so arriving a beat later costs
       nothing visible. `load` plus an idle callback puts the download after
       first paint instead of inside it: identical bytes, but the page is
       readable before any of them are spent.

       The 1200ms timeout is the backstop for a tab that never goes idle —
       requestIdleCallback on a busy page can otherwise wait indefinitely, and
       "eventually" is not a loading strategy. */
    const idle = (fn) => {
        const run = () => (window.requestIdleCallback
            ? requestIdleCallback(fn, { timeout: 1200 })
            : setTimeout(fn, 200));
        if (document.readyState === 'complete') run();
        else window.addEventListener('load', run, { once: true });
    };

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            io.unobserve(entry.target);

            const start = () => mount(entry.target)
                .catch(() => { /* the still stays; see the docblock */ });

            // Above the fold, so it is competing with first paint: wait.
            // Below it, the visitor has already scrolled and is waiting for it.
            if (entry.target.dataset.stage3d === 'peace') start();
            else idle(start);
        });
    }, { rootMargin: '300px' });

    stages.forEach((stage) => io.observe(stage));
}

async function mount(host) {
    const [THREE, roomMod] = await Promise.all([
        import('/vendor/three/three.module.min.js'),
        import('/vendor/three/RoomEnvironment.js'),
    ]);

    const width = host.clientWidth;
    const height = host.clientHeight;
    if (!width || !height) return;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'low-power' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(width, height, false);
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.05;
    host.appendChild(renderer.domElement);

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(38, width / height, 0.1, 100);
    camera.position.set(0, 1.35, 7.0);
    camera.lookAt(0, 0, 0);

    /* THE ENVIRONMENT IS THE LIGHTING, and it is generated rather than loaded.

       Three directional lights make a shape legible; they do not make it look
       like a photographed object. What does that is an environment map, because
       a metal or a clearcoat surface is mostly a mirror and a mirror with
       nothing to reflect is flat grey no matter how many lamps you point at it.

       The obvious way to get one is an HDRI, and the obvious HDRI is a 1k .hdr
       from Poly Haven — 2 to 3 MB of float data for a decorative object, plus a
       loader, plus a licence row. RoomEnvironment builds an equivalent studio
       lightbox out of a dozen emissive boxes at runtime: about 5 KB of code, no
       download, no licence, and PMREMGenerator turns it into the same
       prefiltered cube map an HDRI would have produced. */
    const pmrem = new THREE.PMREMGenerator(renderer);
    const envScene = new roomMod.RoomEnvironment();
    scene.environment = pmrem.fromScene(envScene, 0.04).texture;
    scene.environmentIntensity = 0.85;
    envScene.dispose?.();
    pmrem.dispose();

    /* One key on top of the environment, for a directional highlight the room
       alone does not give, and a warm rim so the object separates from a light
       ground on the side away from the key. */
    const key = new THREE.DirectionalLight(0xffffff, 1.4);
    key.position.set(4, 6, 5);
    scene.add(key);

    const rim = new THREE.DirectionalLight(0xff6b35, 0.9);
    rim.position.set(-5, -1, -3);
    scene.add(rim);

    const kind = host.dataset.stage3d || 'peace';
    const group = kind === 'peace' ? buildObject(THREE) : buildHero(THREE, kind === 'head');
    scene.add(group);

    /* The hero is a wider frame than the P.E.A.C.E. stage and its object has to
       carry a whole viewport rather than sit beside a column of text, so it is
       shot longer and from slightly above. */
    if (kind !== 'peace') {
        /* The framing is arithmetic, not taste. The knot's outer radius is
           1.82 and the satellites sit at 2.55-3.15, so the object's bounding
           radius is about 3.3. At a 38 degree vertical FOV the visible
           half-height at distance z is z*tan(19deg) = 0.344z, so anything
           closer than z=9.6 puts the satellites off the top and bottom of the
           frame — which is exactly what the first version did: it filled the
           whole viewport and swamped the headline it is supposed to sit
           behind. 11.4 leaves the object about two thirds of the stage and
           paper around it, with room for the scroll push-back. */
        /* Framing is arithmetic. Half-height at distance z is z*tan(19deg) =
           0.344z, and half-width is that times the aspect. The object spans
           about 6.6 units across and 4.4 down, so the hero needs z ~ 8.4 to
           fill its column with a margin left for the panels to separate into.
           The head variant sits in a narrower band and wants more air. */
        /* Framing is arithmetic. Half-height at distance z is z*tan(19deg) =
           0.344z and half-width is that times the aspect. The hero scatter
           spans about 13.5 units across and 6.5 down, so z ~ 9.6 puts the
           outermost models just inside the frame with room to travel outward
           on scroll. The head variant holds one narrow band and wants more air. */
        camera.position.set(0, 0.15, kind === 'head' ? 13.4 : 9.6);
        camera.lookAt(0, -0.05, 0);
        scene.environmentIntensity = 1.0;
    }

    host.classList.add('is-3d-live');

    /* --------------------------------------------------------------------
       The step contract. js/scroll.js writes data-st-step onto the enclosing
       .st-stage; this watches for it rather than doing its own scroll maths,
       so the object and the text can never disagree about which step is
       current.
       -------------------------------------------------------------------- */
    /* SCROLL INPUT.

       The P.E.A.C.E. object is told which STEP is being read, because there is
       a step to read. The hero has no steps — it has a viewport of scroll — so
       it reads its own progress instead. One passive listener, one rect, and
       the value is consumed by the same damped loop below, so the two inputs
       never both drive the object. */
    let scrollP = 0;
    if (kind !== 'peace') {
        const readScroll = () => {
            const r = host.getBoundingClientRect();
            const span = r.height || 1;
            scrollP = Math.min(1, Math.max(0, -r.top / span));
        };
        readScroll();
        window.addEventListener('scroll', readScroll, { passive: true });
        window.addEventListener('resize', readScroll, { passive: true });
    }

    const stageEl = host.closest('.st-stage') || host;
    const stepCount = Math.max(parseInt(host.dataset.steps, 10) || group.children.length, 1);
    let targetStep = 0;
    let step = 0;

    const readStep = () => {
        const raw = parseInt(stageEl.getAttribute('data-st-step'), 10);
        if (!Number.isNaN(raw)) targetStep = raw;
    };
    readStep();
    new MutationObserver(readStep).observe(stageEl, { attributes: true, attributeFilter: ['data-st-step'] });

    /* --------------------------------------------------------------------
       Pointer parallax — a few degrees, damped, so the object feels present
       without becoming a toy.
       -------------------------------------------------------------------- */
    let pointerX = 0;
    let pointerY = 0;
    if (window.matchMedia('(hover: hover)').matches) {
        host.addEventListener('pointermove', (e) => {
            const rect = host.getBoundingClientRect();
            pointerX = ((e.clientX - rect.left) / rect.width - 0.5) * 2;
            pointerY = ((e.clientY - rect.top) / rect.height - 0.5) * 2;
        });
        host.addEventListener('pointerleave', () => { pointerX = 0; pointerY = 0; });
    }

    /* --------------------------------------------------------------------
       Render loop. Paused whenever the section is off screen — an idle rAF
       burning a GPU on a page the visitor has scrolled past is exactly the
       kind of thing that makes 3-D on a marketing site a liability.
       -------------------------------------------------------------------- */
    let visible = true;
    new IntersectionObserver(([entry]) => { visible = entry.isIntersecting; }, { rootMargin: '120px' })
        .observe(host);

    let raf = 0;
    let last = performance.now();

    const frame = (now) => {
        raf = requestAnimationFrame(frame);
        if (!visible) { last = now; return; }

        const dt = Math.min((now - last) / 1000, 0.05);
        last = now;

        // Frame-rate independent damping, same curve js/smooth.js uses.
        const k = 1 - Math.exp(-3.2 * dt);
        step += (targetStep - step) * k;

        if (kind !== 'peace') {
            const p = scrollP;

            /* THE GROUP OSCILLATES, IT DOES NOT SPIN.

               The old object was a knot, and a knot looks the same from every
               angle, so turning it continuously cost nothing. These are PANELS
               — a browser window seen edge-on is a line, and seen from behind
               it is a blank sheet. So the group swings through a slow arc and
               comes back, which shows the depth between the panels without
               ever turning any of them away from the reader. */
            group.rotation.y = Math.sin(now / 5200) * 0.26 + pointerX * 0.24 + p * 0.75;
            group.rotation.x = -0.06 + pointerY * 0.14 + p * 0.34;
            group.rotation.z = p * 0.16;

            // Breathing at rest; pushed back and down as the page leaves.
            group.position.y = Math.sin(now / 2100) * 0.08 - p * 0.55;
            group.scale.setScalar(1 - p * 0.16);

            group.children.forEach((mesh) => {
                const u = mesh.userData;
                if (!u.panel) return;

                /* Each panel leaves along ITS OWN vector, at p squared so
                   nothing moves for the first part of the scroll and then the
                   whole composition opens at once. Pushing them all along one
                   axis, or scaling the group, would read as a zoom; separate
                   vectors read as an exploded view — which is the right idea
                   for an object made of parts. */
                const k = p * p;
                mesh.position.set(
                    u.bx + u.dx * k,
                    u.by + u.dy * k + Math.sin(now / 1700 + (u.phase || 0)) * 0.05,
                    u.bz + u.dz * k
                );
            });

            renderer.render(scene, camera);
            return;
        }

        group.rotation.y = (step / stepCount) * Math.PI * 2 + pointerX * 0.22;
        group.rotation.x = -0.18 + pointerY * 0.12;
        group.position.y = Math.sin(now / 1400) * 0.07;

        // Each ring lifts and brightens as its own step comes up.
        group.children.forEach((mesh, i) => {
            const distance = Math.abs(i - step);
            const near = Math.max(0, 1 - distance);
            mesh.position.y = mesh.userData.baseY + near * 0.24;
            mesh.material.emissiveIntensity = 0.04 + near * 0.5;
            mesh.scale.setScalar(1 + near * 0.06);
        });

        renderer.render(scene, camera);
    };
    raf = requestAnimationFrame(frame);

    /* --------------------------------------------------------------------
       Resize
       -------------------------------------------------------------------- */
    let resizeTimer = 0;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const w = host.clientWidth;
            const h = host.clientHeight;
            if (!w || !h) return;
            camera.aspect = w / h;
            camera.updateProjectionMatrix();
            renderer.setSize(w, h, false);
        }, 150);
    });

    // Losing the context is normal on laptops that switch GPUs; recover to the
    // still rather than leaving a frozen last frame on screen.
    renderer.domElement.addEventListener('webglcontextlost', (e) => {
        e.preventDefault();
        cancelAnimationFrame(raf);
        host.classList.remove('is-3d-live');
    });
}

/**
 * Five stacked rings — one per P.E.A.C.E. stage — around a central core.
 * A deliberately abstract object: it stands for a layered method, and unlike a
 * laptop or a server it does not imply we do something we don't.
 */
function buildObject(THREE) {
    const group = new THREE.Group();

    /* Five rings and a core, the five P.E.A.C.E. stages and the work at the
       centre of them. The geometry is deliberately primitive — every shape here
       is one three.js constructor, so nothing is downloaded and nothing can
       arrive at the wrong scale, wrong orientation or wrong licence.

       The look comes from the MATERIAL, not from the mesh. MeshPhysicalMaterial
       with a clearcoat over a low-roughness metal is what turns a torus into
       something that looks manufactured: the environment map above supplies the
       reflections, the clearcoat adds the second, sharper highlight that reads
       as lacquer, and iridescence puts a faint colour shift on the grazing
       angles the way an anodised surface does. */
    const palette = [0x2a6bf0, 0x1f5fe8, 0x1652e0, 0x0d47a1, 0x123f8f];

    for (let i = 0; i < 5; i++) {
        const radius = 1.86 - i * 0.17;
        const geometry = new THREE.TorusGeometry(radius, 0.075, 28, 140);
        const material = new THREE.MeshPhysicalMaterial({
            color: palette[i],
            roughness: 0.18,
            metalness: 0.9,
            clearcoat: 1,
            clearcoatRoughness: 0.12,
            iridescence: 0.35,
            iridescenceIOR: 1.4,
            emissive: palette[i],
            emissiveIntensity: 0.04,
        });

        const mesh = new THREE.Mesh(geometry, material);
        mesh.rotation.x = Math.PI / 2;
        mesh.rotation.z = i * 0.22;
        mesh.position.y = -0.92 + i * 0.46;
        mesh.userData.baseY = mesh.position.y;
        group.add(mesh);
    }

    /* The core is glass rather than metal so it reads as a different kind of
       thing from the rings around it — transmission is the one property that
       makes an object look like it is made of a material rather than painted
       one. thickness and ior are what stop it looking like a soap bubble. */
    const core = new THREE.Mesh(
        new THREE.IcosahedronGeometry(0.44, 3),
        new THREE.MeshPhysicalMaterial({
            color: 0xffd9c8,
            roughness: 0.06,
            metalness: 0,
            transmission: 0.92,
            thickness: 0.85,
            ior: 1.45,
            clearcoat: 1,
            clearcoatRoughness: 0.05,
            attenuationColor: new THREE.Color(0xff6b35),
            attenuationDistance: 1.6,
        })
    );
    core.position.y = 0.05;
    core.userData.baseY = core.position.y;
    group.add(core);

    /* A halo of small spheres on the outer ring, so the object has some
       small-scale detail to catch light. Without them the silhouette is five
       smooth curves and reads as a diagram. */
    const beadGeo = new THREE.SphereGeometry(0.055, 18, 18);
    const beadMat = new THREE.MeshPhysicalMaterial({
        color: 0xff8a5c,
        roughness: 0.15,
        metalness: 0.35,
        clearcoat: 1,
        emissive: 0xff6b35,
        emissiveIntensity: 0.22,
    });
    for (let i = 0; i < 12; i++) {
        const a = (i / 12) * Math.PI * 2;
        const bead = new THREE.Mesh(beadGeo, beadMat);
        bead.position.set(Math.cos(a) * 2.02, -0.90, Math.sin(a) * 2.02);
        bead.userData.baseY = bead.position.y;
        group.add(bead);
    }

    group.rotation.x = -0.30;
    return group;
}

/* ==========================================================================
   THE HERO OBJECT — the things this company makes, arranged AROUND the words.

   Four versions of this were rejected, and the history is the specification:

     1. A ring of flat tiles      → right idea, wrong place; it moved to
                                    #capabilities and stayed there.
     2. A gl.js point cloud       → "ye 3d design bilkul pasand nahi".
                                    Dots have no surface, so nothing reflects,
                                    so it can never look manufactured.
     3. A glass torus knot        → "ye bahut common model h". True: a knot is
                                    the default object of every 3-D demo and
                                    said nothing about Rafly.
     4. Browser + phone, off to
        one side of the headline  → "aise kuch nhi chahiya. text center me
                                    chahiya."

   So: the headline is centred, and the models are scattered around it.

   THE ONE RULE THAT MAKES IT WORK — THE MIDDLE IS EMPTY.
   Every previous centred attempt put an object BEHIND the type and then fought
   to keep the words readable: a mask, a bloom, a scrim, each of which erased
   the object it was protecting the text from. Here nothing is placed inside
   the headline's rectangle at all. No mask, no bloom, nothing to trade off.

   KEEP OUT is the ellipse |x|/4.4 + |y|/2.3 < 1 in world units, which is the
   text block plus a margin at the camera distance set in mount(). Everything
   below sits outside it. If a model is ever added or moved, check it against
   that ellipse first — it is the whole design.

   A SCATTER, DELIBERATELY NOT A RING: #capabilities is already a circle of
   thirteen tiles further down the same page, and two circles is the repetition
   this pass exists to remove.
   ========================================================================== */

/** A rounded rectangle, extruded. Panels, screens and UI bars are all this. */
function plate(THREE, w, h, r, depth) {
    const shape = new THREE.Shape();
    const x = -w / 2, y = -h / 2;
    r = Math.min(r, w / 2, h / 2);

    shape.moveTo(x + r, y);
    shape.lineTo(x + w - r, y);
    shape.quadraticCurveTo(x + w, y, x + w, y + r);
    shape.lineTo(x + w, y + h - r);
    shape.quadraticCurveTo(x + w, y + h, x + w - r, y + h);
    shape.lineTo(x + r, y + h);
    shape.quadraticCurveTo(x, y + h, x, y + h - r);
    shape.lineTo(x, y + r);
    shape.quadraticCurveTo(x, y, x + r, y);

    const geo = new THREE.ExtrudeGeometry(shape, {
        depth,
        bevelEnabled: true,
        bevelThickness: 0.010,
        bevelSize: 0.010,
        bevelSegments: 2,
        curveSegments: 10,
    });
    // ExtrudeGeometry pushes along +Z from the shape plane, so an untouched
    // panel would pivot about its BACK face. Recentre it on its own middle.
    geo.translate(0, 0, -depth / 2);
    return geo;
}

function buildHero(THREE, small) {
    const group = new THREE.Group();

    /* ---------------------------------------------------------- materials
       NOT transmissive, and that is a correction rather than a preference.

       These panels were transmission 0.92 for one pass and photographed GREY.
       A transmissive surface shows what is behind it, and behind these is a
       sheet of cream paper — so a large flat plate refracts nothing, picks up
       whatever is immediately behind it, and lands between frosted plastic and
       dirty perspex. Transmission pays off on a small thick object with depth
       behind it, not on a flat screen.

       What a screen should look like is a bright polished surface, so that is
       what this is: white, near-mirror clearcoat, and iridescence for the
       colour shift a coated display has at a grazing angle. */
    const shell = new THREE.MeshPhysicalMaterial({
        color: 0xf6f8fc, roughness: 0.07, metalness: 0.04,
        clearcoat: 1, clearcoatRoughness: 0.03,
        iridescence: 0.55, iridescenceIOR: 1.8,
        iridescenceThicknessRange: [140, 460], envMapIntensity: 1.45,
    });
    const chrome = new THREE.MeshPhysicalMaterial({
        color: 0xe4eaf4, roughness: 0.09, metalness: 1,
        clearcoat: 1, clearcoatRoughness: 0.05,
        iridescence: 0.35, iridescenceIOR: 1.6, envMapIntensity: 1.7,
    });

    /* Interface furniture is OPAQUE, and that is the whole trick. Glass on
       glass reads as a smudge; it is the solid bars inside a bright sheet that
       make the sheet legible as a screen at this size. */
    const ui = (hex, em, ei) => new THREE.MeshPhysicalMaterial({
        color: hex, roughness: 0.22, metalness: 0.55, clearcoat: 1,
        clearcoatRoughness: 0.12, emissive: em, emissiveIntensity: ei,
        envMapIntensity: 1.1,
    });
    const blue = ui(0x1652e0, 0x0d2a63, 0.30);
    const deep = ui(0x0d2a63, 0x081c45, 0.22);
    const hot  = ui(0xff7a45, 0xff6b35, 0.55);
    const pale = ui(0xdfe7f5, 0x9fb4d8, 0.10);

    const put = (parent, geo, mat, x, y, z) => {
        const m = new THREE.Mesh(geo, mat);
        m.position.set(x, y, z);
        parent.add(m);
        return m;
    };

    /** Places a model and records where it rests, so the scroll can push it out. */
    const place = (o, x, y, z, rx, ry, rz, out) => {
        o.position.set(x, y, z);
        o.rotation.set(rx, ry, rz);
        o.userData = {
            panel: true, bx: x, by: y, bz: z,
            // Outward vector: straight away from the centre, so on scroll the
            // whole composition opens like an exploded view rather than sliding.
            dx: x * out, dy: y * out, dz: z * out - 0.4,
            spin: (Math.abs(x) % 0.7) * 0.5 - 0.15,
            phase: Math.abs(x * 3 + y * 7) % 6.28,
        };
        group.add(o);
        return o;
    };

    /* ===================================================== the browser window
       Left flank, the largest object and the one that says what this company
       is. A title bar with three dots, a hero block, text lines and a call to
       action: the smallest set of parts that reads as "a web page" rather than
       as "a rectangle". */
    const win = new THREE.Group();
    win.add(new THREE.Mesh(plate(THREE, 2.60, 1.72, 0.11, 0.08), shell));
    const wf = new THREE.Mesh(plate(THREE, 2.68, 1.80, 0.13, 0.025), chrome);
    wf.position.z = -0.06;
    win.add(wf);

    const F = 0.052;
    put(win, plate(THREE, 2.38, 0.15, 0.07, 0.02), pale, 0, 0.68, F);
    const dot = new THREE.SphereGeometry(0.034, 12, 12);
    [-1.10, -1.00, -0.90].forEach((x, i) => {
        put(win, dot, i === 0 ? hot : (i === 1 ? blue : deep), x, 0.68, F + 0.018);
    });
    put(win, plate(THREE, 1.14, 0.72, 0.06, 0.025), blue, -0.58, 0.14, F);
    [0.32, 0.14, -0.04].forEach((y, i) => {
        put(win, plate(THREE, 0.94 - i * 0.24, 0.075, 0.037, 0.018), pale, 0.50 - i * 0.12, y, F);
    });
    put(win, plate(THREE, 0.52, 0.15, 0.075, 0.025), hot, 0.22, -0.26, F);
    [-0.80, -0.27, 0.27, 0.80].forEach((x) => {
        put(win, plate(THREE, 0.46, 0.30, 0.05, 0.02), pale, x, -0.56, F);
    });
    place(win, -5.05, 0.35, 0.30, -0.04, 0.42, -0.03, 0.40);

    /* ============================================================= the phone
       Right flank, opposite the browser. "The same site on every screen" is the
       actual claim this company makes, so the two sit as a pair across the
       headline rather than stacked on one side. */
    const phone = new THREE.Group();
    phone.add(new THREE.Mesh(plate(THREE, 0.86, 1.74, 0.17, 0.075), shell));
    const pf = new THREE.Mesh(plate(THREE, 0.92, 1.80, 0.19, 0.025), chrome);
    pf.position.z = -0.05;
    phone.add(pf);
    const P = 0.048;
    put(phone, plate(THREE, 0.27, 0.055, 0.027, 0.018), deep, 0, 0.75, P);
    put(phone, plate(THREE, 0.68, 0.48, 0.06, 0.025), blue, 0, 0.34, P);
    [0.01, -0.13, -0.27].forEach((y, i) => {
        put(phone, plate(THREE, 0.65 - i * 0.13, 0.065, 0.032, 0.018), pale, -0.02 - i * 0.06, y, P);
    });
    put(phone, plate(THREE, 0.60, 0.14, 0.07, 0.025), hot, 0, -0.49, P);
    put(phone, plate(THREE, 0.41, 0.04, 0.02, 0.018), pale, 0, -0.75, P);
    place(phone, 5.00, -0.15, 0.45, -0.03, -0.46, 0.04, 0.40);

    /* ==================================================== the analytics card
       Bottom left. Marketing and analytics are two of the five services, and a
       rising bar chart is the one glyph that says so without a caption. */
    const chart = new THREE.Group();
    chart.add(new THREE.Mesh(plate(THREE, 1.16, 0.82, 0.10, 0.06), shell));
    const cf = new THREE.Mesh(plate(THREE, 1.22, 0.88, 0.11, 0.022), chrome);
    cf.position.z = -0.045;
    chart.add(cf);
    [0.18, 0.32, 0.46, 0.66].forEach((h, i) => {
        put(chart, plate(THREE, 0.14, h, 0.035, 0.025), i === 3 ? hot : blue,
            -0.36 + i * 0.24, -0.24 + h / 2, 0.042);
    });
    place(chart, -4.05, -2.30, 0.55, 0.07, 0.40, 0.07, 0.42);

    /* ======================================================== the store cube
       Top left. E-commerce, as the only thing a parcel can be. Deliberately
       OPAQUE: after three bright panels the composition needs one solid mass,
       or the whole scatter reads as a single pale smear. */
    place(new THREE.Mesh(plate(THREE, 0.52, 0.52, 0.07, 0.50), blue),
          -4.35, 2.20, -0.20, 0.34, 0.58, 0.20, 0.44);

    /* ====================================================== the security tile
       Top right. Web security is a service and the only one with no interface
       of its own, so it needs a symbol. FLAT SHOULDERS and a point at the base:
       an earlier version curved all the way round and rendered as an egg — a
       shield is read from its straight top, not from its curve. */
    const sh = new THREE.Shape();
    sh.moveTo(-0.30, 0.36);
    sh.lineTo(0.30, 0.36);
    sh.lineTo(0.30, 0.02);
    sh.quadraticCurveTo(0.30, -0.25, 0, -0.41);
    sh.quadraticCurveTo(-0.30, -0.25, -0.30, 0.02);
    sh.closePath();
    place(new THREE.Mesh(new THREE.ExtrudeGeometry(sh, {
        depth: 0.14, bevelEnabled: true, bevelSize: 0.02,
        bevelThickness: 0.02, bevelSegments: 2, curveSegments: 10,
    }), deep), 4.30, 2.25, -0.15, 0.10, -0.34, 0.14, 0.44);

    if (!small) {
        /* ============================================== the code brackets
           Far left, low. Two chevrons and a slash: development, in the one mark
           every developer reads instantly. */
        /* A CHEVRON IS ONE OUTLINE, NOT TWO TILTED BARS.

           The first version built each bracket from two rotated rectangles.
           Two bars crossing at an angle is an X, which is exactly what it
           rendered as — the arms met in the middle instead of at a point.
           Drawn as a single Shape the vertex is where it is put. */
        const chevron = (flip) => {
            const c = new THREE.Shape();
            const k = flip ? -1 : 1;
            c.moveTo(k * 0.20,  0.32);
            c.lineTo(k * 0.02,  0.00);
            c.lineTo(k * 0.20, -0.32);
            c.lineTo(k * 0.34, -0.26);
            c.lineTo(k * 0.17,  0.00);
            c.lineTo(k * 0.34,  0.26);
            c.closePath();
            return new THREE.ExtrudeGeometry(c, {
                depth: 0.10, bevelEnabled: true, bevelSize: 0.014,
                bevelThickness: 0.014, bevelSegments: 2, curveSegments: 6,
            });
        };

        const brackets = new THREE.Group();
        /* Far enough apart to read as two brackets with something between
           them. At 0.30 each chevron's arms reached past the other's point and
           the pair closed into a diamond. */
        const left  = new THREE.Mesh(chevron(false), chrome);
        left.position.x = -0.52;
        brackets.add(left);
        const right = new THREE.Mesh(chevron(true), chrome);
        right.position.x = 0.52;
        brackets.add(right);

        // The slash between them, leaning the way a forward slash leans.
        const slash = new THREE.Mesh(plate(THREE, 0.085, 0.66, 0.042, 0.10), hot);
        slash.rotation.z = -0.30;
        brackets.add(slash);

        place(brackets, -2.55, -2.95, -0.20, 0.05, 0.26, 0, 0.44);

        /* ================================================== the chat bubble
           Far right, high. Support: a rounded panel with a tail, and three dots
           in it, which is the universal "someone is answering you". */
        const chat = new THREE.Group();
        chat.add(new THREE.Mesh(plate(THREE, 0.86, 0.60, 0.16, 0.09), shell));
        const tail = new THREE.Mesh(plate(THREE, 0.20, 0.20, 0.04, 0.09), shell);
        tail.position.set(-0.26, -0.34, 0);
        tail.rotation.z = 0.78;
        chat.add(tail);
        [-0.20, 0, 0.20].forEach((x, i) => {
            put(chat, new THREE.SphereGeometry(0.055, 12, 12), i === 1 ? hot : blue, x, 0, 0.058);
        });
        place(chat, 5.75, 1.85, -0.35, 0.04, -0.34, -0.06, 0.38);

        /* ================================================= the database stack
           Bottom right. Hosting: three cylinders, the shape every diagram in
           this industry has used for storage for forty years. */
        const dbg = new THREE.Group();
        const disc = new THREE.CylinderGeometry(0.30, 0.30, 0.11, 28);
        [0.20, 0.03, -0.14].forEach((y, i) => {
            const m = new THREE.Mesh(disc, i === 0 ? chrome : deep);
            m.position.y = y;
            dbg.add(m);
        });
        place(dbg, 4.45, -2.25, 0.20, 0.16, 0.30, -0.10, 0.42);
    }

    /* ======================================================= connecting nodes
       Small spheres filling the gaps between the models — "one team, everything
       joined up", and the small-scale detail that stops the silhouette being
       six rectangles.

       Positions come from a fixed table rather than Math.random(): random
       points clump into a bare side and a crowded one, and a deterministic
       layout means the composition is identical on every load and can actually
       be judged from a screenshot. Each one is checked against the keep-out
       ellipse at the top of this file. */
    const NODES = [
        [-5.9, 1.75], [-3.3, 2.75], [-1.2, 3.05], [1.4, 3.00], [3.2, 2.85],
        [5.9, 2.55], [6.7, -0.5], [5.4, -1.5], [2.9, -2.85], [0.4, -3.05],
        [-5.5, -2.20], [-6.6, 0.55], [-4.9, -0.95], [4.9, 1.15],
        [3.6, -0.15],
    ];
    const nodeGeo = new THREE.SphereGeometry(0.055, 14, 14);
    const list = small ? NODES.slice(0, 7) : NODES;
    list.forEach(([x, y], i) => {
        const node = new THREE.Mesh(nodeGeo, i % 4 === 0 ? hot : chrome);
        place(node, x, y, ((i % 3) - 1) * 0.45, 0, 0, 0, 0.55);
    });

    group.rotation.x = -0.04;
    return group;
}
