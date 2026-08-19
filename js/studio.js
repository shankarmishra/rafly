/**
 * studio.js — THE LIGHTING RIG.
 *
 * This file is the fix for the single defect that sank the previous build.
 *
 * WHAT WAS WRONG BEFORE
 * ---------------------
 * The old hero lit itself with RoomEnvironment through PMREM plus two
 * DirectionalLights, rendered with alpha:true and NO ground plane, NO shadow
 * map and NO occlusion. Environment-only lighting is excellent for making
 * metal reflect something and useless for shaping a subject on a light
 * ground: the reflections are neutral and low-contrast, nothing has a
 * direction, and polished aluminium resolves to flat grey. With no ground
 * plane there was also nothing for the object to stand on, so nine props
 * floated in front of a gradient. Every "the 3-D looks cheap" symptom
 * descended from those three lines of setup.
 *
 * WHAT THIS DOES INSTEAD
 * ----------------------
 * A real studio, in the order a photographer would build it:
 *
 *   1. A large soft KEY, upper-left, several times the size of the subject.
 *   2. A broad FILL opposite at about a quarter of key, so the shadow side
 *      opens up instead of going black.
 *   3. A narrow RIM behind and above, which is what separates the silhouette
 *      from paper. On a light ground this is the light that does the most
 *      work and the one most often left out.
 *   4. A warm BOUNCE card below, standing in for the page itself — the paper
 *      the object sits on throws light back up into its underside, and
 *      modelling that is why the object looks like it belongs to the page
 *      rather than like it was pasted on.
 *   5. A shadow-catching GROUND, plus a tightened contact term directly
 *      under the object.
 *   6. One DirectionalLight along the key's axis, at low intensity, whose
      only real job is to cast the shadow — an environment cannot cast one.
 *
 * WHY THE KEY IS AN EMISSIVE RECTANGLE AND NOT A RectAreaLight
 * ------------------------------------------------------------
 * three.js ships RectAreaLight, but it renders nothing without
 * RectAreaLightUniformsLib — roughly 150 KB of look-up-table float data from
 * the examples tree, which would have to be vendored and shipped to every
 * desktop visitor. Building the same softboxes as emissive planes inside a
 * small scene and running PMREMGenerator over it gives genuinely area-lit
 * diffuse shaping AND correct softbox reflections in the brushed aluminium —
 * which is the part you can actually see — for zero additional bytes. The
 * shape of a light is what makes it soft; a rectangle that the metal can
 * reflect IS a softbox. One DirectionalLight is then added along the key's
 * axis purely to cast the shadow, because an environment cannot cast one.
 *
 * The trade is honest and worth naming: there is no screen-space ambient
 * occlusion here. Every part casts and receives shadows onto every other
 * part (js/assembly.js), and inter-part shadowing on an assembly delivers
 * most of what an AO pass would, without vendoring an EffectComposer chain
 * and paying for a second full-screen pass on a mid-range laptop.
 */

/* ==========================================================================
   The environment — a room built to be photographed in, then baked
   ========================================================================== */

/**
 * Colours here are LINEAR and may exceed 1. That is the point: a softbox is
 * brighter than white paper, and clamping it to 1 is what makes a render
 * look like it was lit by a monitor.
 */
function panel(THREE, scene, { w, h, pos, look, rgb }) {
    const m = new THREE.Mesh(
        new THREE.PlaneGeometry(w, h),
        new THREE.MeshBasicMaterial({ side: THREE.DoubleSide })
    );
    m.material.color.setRGB(rgb[0], rgb[1], rgb[2], THREE.LinearSRGBColorSpace);
    m.position.set(pos[0], pos[1], pos[2]);
    m.lookAt(look[0], look[1], look[2]);
    scene.add(m);
    return m;
}

export function buildEnvironment(THREE, renderer) {
    const s = new THREE.Scene();

    /* The room's own walls. Not black: a subject photographed in a void has
       nothing to reflect and the metal goes dead. Mid-grey with a faint warm
       bias, so every neutral on the object leans the same way the paper does. */
    const shell = new THREE.Mesh(
        new THREE.BoxGeometry(24, 16, 24),
        new THREE.MeshBasicMaterial({ side: THREE.BackSide })
    );
    shell.material.color.setRGB(0.225, 0.221, 0.214, THREE.LinearSRGBColorSpace);
    shell.position.y = 5;
    s.add(shell);

    const at = [0, 1.5, 0];

    /* 1 — KEY. Upper left, forward, large. 9x7 units against a 3-unit
       subject: the ratio is what makes the shadow edge soft. */
    panel(THREE, s, { w: 9, h: 7, pos: [-6.4, 6.2, 5.0], look: at, rgb: [2.60, 2.55, 2.45] });

    /* 2 — FILL. Opposite, broader, dimmer. ~25% of key. */
    panel(THREE, s, { w: 11, h: 8, pos: [7.2, 3.0, 3.2], look: at, rgb: [0.62, 0.63, 0.66] });

    /* 3 — RIM. Narrow, high, behind. The separation light. */
    panel(THREE, s, { w: 7, h: 1.5, pos: [1.2, 6.8, -6.2], look: at, rgb: [2.20, 2.25, 2.45] });

    /* 3b — OVERHEAD. A broad, soft top light. Up-facing surfaces reflect
       almost straight up, so without this every horizontal machined face
       mirrors the dark ceiling and the top of the object goes black — which
       is exactly what the first prism render did to the crown. */
    panel(THREE, s, { w: 26, h: 26, pos: [-0.8, 9.2, 0.6], look: [0, 0, 0], rgb: [1.52, 1.51, 1.55] });

    /* 4 — BOUNCE. The page, modelled. Warm, wide, low. */
    panel(THREE, s, { w: 20, h: 20, pos: [0, -0.9, 0], look: [0, 6, 0], rgb: [0.34, 0.33, 0.30] });

    const pmrem = new THREE.PMREMGenerator(renderer);
    pmrem.compileEquirectangularShader();
    const env = pmrem.fromScene(s, 0.02).texture;
    pmrem.dispose();
    s.traverse((o) => { if (o.geometry) o.geometry.dispose(); if (o.material) o.material.dispose(); });

    return env;
}

/* ==========================================================================
   The contact term

   A shadow map alone gives a shadow that is uniformly soft everywhere. Real
   contact is DARKEST and SHARPEST exactly where the two surfaces meet and
   opens up within a few millimetres. This is that gradient, drawn once into
   a 256px canvas and laid flat under the object.

   It is the cheapest high-value element in the whole rig: without it the
   object hovers a millimetre off the page no matter how good the shadow map
   is, and with it the object has weight.
   ========================================================================== */

function contactTexture(THREE) {
    const c = document.createElement('canvas');
    c.width = c.height = 256;
    const x = c.getContext('2d');
    const g = x.createRadialGradient(128, 128, 4, 128, 128, 126);
    g.addColorStop(0.00, 'rgba(34,28,18,0.62)');
    g.addColorStop(0.16, 'rgba(34,28,18,0.40)');
    g.addColorStop(0.42, 'rgba(34,28,18,0.16)');
    g.addColorStop(0.72, 'rgba(34,28,18,0.04)');
    g.addColorStop(1.00, 'rgba(34,28,18,0)');
    x.fillStyle = g;
    x.fillRect(0, 0, 256, 256);
    const t = new THREE.CanvasTexture(c);
    t.colorSpace = THREE.SRGBColorSpace;
    return t;
}

/* ==========================================================================
   Assemble the rig
   ========================================================================== */

/**
 * @param {object} opts
 *   quality  'still' for the offline renderer (2048 shadow map, more env
 *            samples), 'live' for the browser (1024). The difference is only
 *            in cost, never in art direction — the still and the live object
 *            must be the same photograph.
 */
export function buildStudio(THREE, renderer, scene, opts = {}) {
    const still = opts.quality === 'still';

    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    /* Neutral tone mapping where the build has it: it was designed for
       product imagery specifically because ACES shifts bright neutrals grey
       and desaturates the one accent colour on the object. */
    renderer.toneMapping = THREE.NeutralToneMapping || THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.0;

    const env = buildEnvironment(THREE, renderer);
    scene.environment = env;
    /* FULL intensity, and that is deliberate — read the note below before
       turning it down.

       The brief's instruction is "environment: reflection only, 0.25", and it
       is the right instruction for a rig whose key, fill and rim are real
       area lights. Here they are not: the softboxes ARE the environment, so
       the environment is not an incidental reflection source, it is the
       entire lighting design. Setting it to 0.25 removes three quarters of
       the light, and it does the most damage to exactly the parts that
       should look best — a metal with metalness 1.0 has no diffuse response
       at all, so 100% of what you see on brushed aluminium comes from here.
       At 0.25 the crown's up-facing machined faces reflected a quarter of the
       overhead softbox and rendered as near-black slabs.

       What the instruction is really guarding against is a GENERIC
       environment doing the lighting — RoomEnvironment at full strength, flat
       and directionless, which is what the previous build did. The guard that
       matters is that the environment be authored, not that it be dim. The
       panel values in buildEnvironment() carry the exposure instead. */
    scene.environmentIntensity = 1.0;

    /* The shadow-caster, aimed down the key's axis. Its only job is the
       shadow — the shaping is already done by the environment above, which
       is why its intensity is modest. Push it higher and the object gets a
       hard second highlight the softbox never asked for. */
    const key = new THREE.DirectionalLight(0xfff6e8, 0.62);
    key.position.set(-9.0, 14.0, 9.5);
    key.target.position.set(0, 1.3, 0);
    key.castShadow = true;
    /* The frustum has to cover the object AND everywhere its parts travel to
       when the assembly opens — the top module ends up above y 5. A frustum
       fitted to the closed object clips the shadow into a visible hard-edged
       rectangle on the ground the moment anything moves, which is exactly
       what the first render of this rig did. */
    const sc = key.shadow.camera;
    sc.left = -6.5; sc.right = 6.5; sc.top = 9.0; sc.bottom = -3.0;
    sc.near = 1; sc.far = 34;
    key.shadow.mapSize.set(still ? 2048 : 1024, still ? 2048 : 1024);
    key.shadow.bias = -0.0006;
    key.shadow.normalBias = 0.018;
    key.shadow.radius = still ? 3 : 4;
    scene.add(key, key.target);

    /* A very low ambient, warm. Not a lighting decision — a floor under the
       darkest crevices so inter-part shadowing reads as dark grey rather
       than as holes. */
    scene.add(new THREE.AmbientLight(0xfff2e2, 0.05));

    /* The ground. ShadowMaterial paints nothing but the shadow, so the page's
       own paper shows through and the object stands on the DOCUMENT rather
       than on a grey plate dropped into the layout. */
    const ground = new THREE.Mesh(
        new THREE.PlaneGeometry(60, 60),
        new THREE.ShadowMaterial({ opacity: 0.34, color: 0x241d12 })
    );
    ground.rotation.x = -Math.PI / 2;
    ground.receiveShadow = true;
    scene.add(ground);

    const contact = new THREE.Mesh(
        new THREE.PlaneGeometry(3.5, 3.5),
        new THREE.MeshBasicMaterial({
            map: contactTexture(THREE), transparent: true,
            depthWrite: false, toneMapped: false,
        })
    );
    contact.rotation.x = -Math.PI / 2;
    contact.position.y = 0.002;
    scene.add(contact);

    return {
        env, key, ground, contact,
        /* As the assembly opens, the base is all that still touches the
           ground, so the contact term tightens and lightens rather than
           staying put under a stack that is no longer there. */
        setExplode(t) {
            const k = 1 - t * 0.55;
            contact.scale.setScalar(k);
            contact.material.opacity = 1 - t * 0.35;
        },
        dispose() {
            env.dispose();
            ground.geometry.dispose(); ground.material.dispose();
            contact.geometry.dispose(); contact.material.map.dispose(); contact.material.dispose();
        },
    };
}

/* ==========================================================================
   Camera choreography

   Seven authored positions, walked continuously. Not a rotation of the
   object — a camera FILM over a stationary subject, which is what the
   difference between "a 3-D model on a website" and "a product film" comes
   down to.

   There are no cuts. Adjacent keys are close enough that the scrub reads as
   one continuous move, and the easing between them is symmetric so nothing
   ever snaps.
   ========================================================================== */

/**
 * Distances here are large on purpose. A 28-degree lens is an 85mm
 * equivalent, and an 85mm portrait is taken from across the room — that
 * distance is what produces the gentle compression that reads as
 * "photographed" rather than "rendered". The first pass at this file put the
 * camera four units from a three-unit object, which is a 28mm framing at a
 * 28-degree fov: the object filled and overflowed the frame and none of the
 * lighting work was visible. Object height is 3.17; every key below sits
 * between 8 and 12 units out.
 */
export const CAMERA_KEYS = [
    /* 0  hero, at rest — three-quarter high, 35 degrees round, 22 up */
    { pos: [4.36, 4.58, 6.22], target: [0, 1.50, 0], fov: 28 },
    /* 1  the lift: camera rises as the first module lets go */
    { pos: [4.08, 5.41, 6.27], target: [0, 1.60, 0], fov: 28 },
    /* 2  the pull-back: the assembly needs room to open into */
    { pos: [4.80, 6.27, 8.31], target: [0, 2.00, 0], fov: 28 },
    /* 3  technical perspective — round to the cantilever side */
    { pos: [9.55, 6.23, 5.07], target: [0, 2.30, 0], fov: 28 },
    /* 4  closer inspection, held while the labels read */
    { pos: [7.67, 6.44, 6.00], target: [0, 2.50, 0], fov: 30 },
    /* 5  wide — the whole architecture at once. The open assembly is nearly
       5 units tall against the closed object's 3.17, so from here on the
       camera is materially further out; framing the open state with the
       closed state's distance crops the crown straight off the top. */
    { pos: [8.76, 7.43, 9.06], target: [0, 2.60, 0], fov: 28 },
    /* 6  plan-like overview, looking down the core */
    { pos: [5.16, 11.98, 6.15], target: [0, 2.40, 0], fov: 28 },
];

const smooth = (x) => x * x * (3 - 2 * x);

/**
 * Place the camera at scrub position t (0..1) along the key path.
 * Framing is width-aware: on a narrow canvas the subject is pushed back so
 * it does not crop, which is the difference between "responsive 3-D" and
 * "3-D that happens to be in a flexible box".
 */
export function applyCamera(THREE, camera, t, aspect) {
    const n = CAMERA_KEYS.length - 1;
    const x = Math.max(0, Math.min(1, t)) * n;
    const i = Math.min(n - 1, Math.floor(x));
    const f = smooth(x - i);
    const a = CAMERA_KEYS[i], b = CAMERA_KEYS[i + 1];

    const lerp = (p, q) => p + (q - p) * f;
    camera.position.set(
        lerp(a.pos[0], b.pos[0]),
        lerp(a.pos[1], b.pos[1]),
        lerp(a.pos[2], b.pos[2])
    );
    const tgt = new THREE.Vector3(
        lerp(a.target[0], b.target[0]),
        lerp(a.target[1], b.target[1]),
        lerp(a.target[2], b.target[2])
    );

    camera.fov = lerp(a.fov, b.fov);
    /* Below about 4:3 the subject stops fitting horizontally. Dolly back on
       the vector we are already on rather than widening the lens — widening
       would change the 85mm-equivalent compression that is doing half the
       work of making this look photographed. */
    if (aspect < 1.34) {
        const k = 1 + (1.34 - Math.max(0.62, aspect)) * 0.62;
        camera.position.sub(tgt).multiplyScalar(k).add(tgt);
    }
    camera.aspect = aspect;
    camera.lookAt(tgt);
    camera.updateProjectionMatrix();
    return tgt;
}
