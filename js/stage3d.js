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
    const THREE = await import('/vendor/three/three.module.js');

    const width = host.clientWidth;
    const height = host.clientHeight;
    if (!width || !height) return;

    const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: 'low-power' });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(width, height, false);
    host.appendChild(renderer.domElement);

    const scene = new THREE.Scene();

    const camera = new THREE.PerspectiveCamera(38, width / height, 0.1, 100);
    camera.position.set(0, 0.6, 7.4);
    camera.lookAt(0, 0, 0);

    /* Light, not flat colour. The whole reason for bringing three.js in is
       that a real material under real lights reads as an object; the CSS-3D
       and point-cloud layers cannot do that. */
    scene.add(new THREE.HemisphereLight(0xffffff, 0xd6def0, 1.25));

    const key = new THREE.DirectionalLight(0xffffff, 2.1);
    key.position.set(4, 6, 5);
    scene.add(key);

    const rim = new THREE.DirectionalLight(0xff6b35, 1.15);
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

    const palette = [0x1652e0, 0x1f5fe8, 0x2a6bf0, 0x0d47a1, 0x123f8f];

    for (let i = 0; i < 5; i++) {
        const radius = 1.85 - i * 0.16;
        const geometry = new THREE.TorusGeometry(radius, 0.085, 22, 120);
        const material = new THREE.MeshStandardMaterial({
            color: palette[i],
            roughness: 0.32,
            metalness: 0.55,
            emissive: palette[i],
            emissiveIntensity: 0.05,
        });

        const mesh = new THREE.Mesh(geometry, material);
        mesh.rotation.x = Math.PI / 2;
        mesh.rotation.z = i * 0.22;
        mesh.position.y = -0.9 + i * 0.45;
        mesh.userData.baseY = mesh.position.y;
        group.add(mesh);
    }

    const core = new THREE.Mesh(
        new THREE.IcosahedronGeometry(0.42, 1),
        new THREE.MeshStandardMaterial({
            color: 0xff6b35,
            roughness: 0.25,
            metalness: 0.3,
            emissive: 0xff6b35,
            emissiveIntensity: 0.18,
        })
    );
    core.position.y = 0.05;
    core.userData.baseY = core.position.y;
    group.add(core);

    group.rotation.x = -0.18;
    return group;
}
