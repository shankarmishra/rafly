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

    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            io.unobserve(entry.target);
            mount(entry.target).catch(() => { /* still stays; see the docblock */ });
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

    const group = buildObject(THREE);
    scene.add(group);

    host.classList.add('is-3d-live');

    /* --------------------------------------------------------------------
       The step contract. js/scroll.js writes data-st-step onto the enclosing
       .st-stage; this watches for it rather than doing its own scroll maths,
       so the object and the text can never disagree about which step is
       current.
       -------------------------------------------------------------------- */
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
