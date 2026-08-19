/**
 * assembly.js — THE OBJECT.
 *
 * "We build digital systems, not deliverables. One core. Five branches."
 * — about.php. The signature object is that sentence, manufactured.
 *
 * FIVE MODULES THREADED ONTO ONE CORE SPINDLE.
 *
 * Read that as the specification it is. The five services are not five icons
 * arranged around a centre — they are five machined modules with a bore
 * through them, stacked on a single shaft that runs the whole height. When
 * the object is assembled the shaft is invisible; the only thing you see of
 * it is a small anodised nose cap at the top. When the object comes apart,
 * the shaft is revealed running through every module, and that reveal IS the
 * sales argument: one core, five branches, and the core was always there.
 *
 * WHY NOT FIVE PARTS IN A RING
 * ----------------------------
 * The build this replaces put nine props on a 72-degree ring around the
 * headline and it read as confetti — evenly spaced, no hierarchy, no
 * silhouette. Radial symmetry is the failure mode, not the shape. So:
 *   - the modules stack along ONE axis, at five different heights;
 *   - each is a different diameter, height and material mix;
 *   - each is rotated to its own clock angle (8, 96, 342, 205, 128 degrees),
 *     so no two seams or lugs line up and the eye never finds a repeat;
 *   - the five branch lugs therefore climb the object as a HELIX, not a ring.
 * From any angle the silhouette is asymmetric. That is the whole trick.
 *
 * WHY LATHES
 * ----------
 * Every turned part here is a LatheGeometry built from a hand-authored 2-D
 * profile in the (radius, height) plane. That is literally how the real part
 * would be made, and it is why the chamfers, undercuts, flange steps and
 * relief grooves read as machining rather than as bevel-modifier defaults.
 * Complexity comes from the PROFILE — from form, material and light — not
 * from polygon count. The whole assembly is about 40k triangles.
 *
 * SHARED BY BOTH RENDER PATHS
 * ---------------------------
 * This module is imported by js/stage3d.js (the live desktop upgrade) AND by
 * inc/tools/render-stills.mjs (the offline still renderer that produces the
 * images every phone and every no-WebGL visitor sees). One scene definition,
 * two consumers — so the still and the live object can never drift apart,
 * which is the usual way a "static fallback" starts looking like a fallback.
 *
 * Takes THREE as an argument rather than importing it, so the caller owns the
 * single dynamic import() and this file adds no second network request.
 */

/* ==========================================================================
   Part specifications

   index   the number printed in the interface (01..05)
   slug    matches inc/data/services.php, so a label can never drift
   y       seat height of the module's base, assembled
   h       module height
   clock   rotation about the stack axis, degrees. Deliberately irregular.
   lift    how far this module travels when the assembly opens
   drift   lateral travel [x, z] while it opens — small, and different per
           module, so the exploded state is a designed arrangement rather
           than a uniform fan
   t0/t1   when this module moves within the 0..1 scrub. Staggered, so the
           assembly peels apart from the top down instead of all at once.
   ========================================================================== */

export const BORE = 0.315;          /* every module's inner clearance       */

/**
 * THE DIAMETER RHYTHM — wide, narrow, wide, narrow, wide.
 *
 * The first render of this object failed on exactly one thing, and it is
 * worth recording because it is the trap any stacked assembly walks into:
 * the modules stepped DOWN monotonically, so the silhouette read as a camera
 * lens barrel. Five round tiers, each slightly smaller than the last, is a
 * shape the eye already has a name for, and it is not "engineered system".
 *
 * The fix is not more detail. It is a non-monotonic profile: R 0.94, 0.62,
 * 0.90, 0.56, 0.62. The waist at module 02 and again at 04 gives the object
 * an hourglass rhythm and a silhouette you could pick out of a line-up in
 * black. Two of the seams also open into GAPS where the bare core spindle is
 * visible between modules — which is what stops the assembled state reading
 * as one solid canister and starts it reading as parts on a shaft.
 */
export const PARTS = [
    { index: '01', slug: 'web-development',         label: 'Web',       y: 0.000, h: 0.46, r: 0.94,
      clock:   8, lift: 0.00, drift: [ 0.00,  0.00], t0: 0.00, t1: 0.10 },
    { index: '02', slug: 'web-security',            label: 'Security',  y: 0.520, h: 0.34, r: 0.62,
      clock:  96, lift: 0.45, drift: [ 0.13, -0.08], t0: 0.30, t1: 0.62 },
    { index: '03', slug: 'marketing-advertisement', label: 'Marketing', y: 1.020, h: 0.66, r: 0.90,
      clock: 342, lift: 0.95, drift: [-0.20,  0.12], t0: 0.24, t1: 0.58 },
    { index: '04', slug: 'content-creation',        label: 'Content',   y: 1.740, h: 0.52, r: 0.56,
      clock: 205, lift: 1.50, drift: [ 0.10,  0.18], t0: 0.18, t1: 0.54 },
    { index: '05', slug: 'ecommerce-support',       label: 'Commerce',  y: 2.420, h: 0.50, r: 0.62,
      clock: 128, lift: 2.05, drift: [-0.08, -0.15], t0: 0.12, t1: 0.50 },
];

export const STACK_TOP = 3.17;      /* the tip of the core's nose cap       */

/* ==========================================================================
   Profiles

   Each is a CLOSED cross-section in (radius, height), walked anticlockwise
   from the bore outwards and back. Closing the loop at the bore is what
   generates the inner wall — without it the modules are open shells and you
   can see straight through them the moment the assembly opens.

   Read these as a technical drawing. The three-point runs like
   (0.86, 0.50), (0.83, 0.545), (0.80, 0.545) are a chamfer and then a
   shoulder: that pair is the single biggest reason these read as turned
   parts rather than as cylinders.
   ========================================================================== */

const PROFILE = {
    /* 01 WEB — the plinth. Widest part of the object and the only one that
       touches the ground: everything above is carried by it, which is the
       right relationship for the service everything else attaches to. Low
       and broad rather than tall, so it reads as a base and not as tier one
       of five. */
    web: [
        [BORE, 0.00], [0.985, 0.00], [1.02, 0.034], [1.02, 0.322], [0.962, 0.380],
        [0.962, 0.418], [0.900, 0.460], [BORE, 0.460], [BORE, 0.00],
    ],
    /* 02 SECURITY — the WAIST. A short knurled aluminium collar at barely
       60% of the base diameter. This is the module doing the most work for
       the silhouette: it is the pinch that stops the stack being a barrel. */
    security: [
        [BORE, 0.00], [0.585, 0.00], [0.620, 0.030], [0.620, 0.078], [0.582, 0.104],
        [0.582, 0.252], [0.620, 0.278], [0.620, 0.312], [0.586, 0.340],
        [BORE, 0.340], [BORE, 0.00],
    ],
    /* 03 MARKETING — the assembly widens again. The tallest module, and the
       one carrying the cantilever, so it reads as the working centre of the
       object rather than as another ring. */
    marketing: [
        [BORE, 0.00], [0.780, 0.00], [0.800, 0.030], [0.800, 0.068], [0.880, 0.104],
        [0.880, 0.548], [0.842, 0.590], [0.842, 0.622], [0.788, 0.660],
        [BORE, 0.660], [BORE, 0.00],
    ],
    /* 04 CONTENT — narrow again, with a recessed band holding a dark polymer
       sleeve. The recess is the "window", cut as a turned relief rather than
       a rectangular pocket, because a lathe cannot cut a rectangle and
       pretending otherwise is how a model stops being believable. */
    content: [
        [BORE, 0.00], [0.532, 0.00], [0.560, 0.026], [0.560, 0.108], [0.500, 0.140],
        [0.500, 0.384], [0.560, 0.416], [0.560, 0.472], [0.518, 0.520],
        [BORE, 0.520], [BORE, 0.00],
    ],
    /* 05 COMMERCE — the crown, and the third widening. It flares rather than
       tapers: the object finishes with a shoulder, which is what lets the
       core's anodised nose read as emerging from something. */
    commerce: [
        [BORE, 0.00], [0.545, 0.00], [0.545, 0.056], [0.735, 0.098], [0.760, 0.132],
        [0.760, 0.356], [0.722, 0.396], [0.560, 0.396], [0.522, 0.436],
        [0.472, 0.500], [BORE, 0.500], [BORE, 0.00],
    ],
    /* THE CORE. One turned shaft, full height, with relief grooves at the
       module seams — the grooves are where the modules index onto it, so
       they are load-bearing detail rather than decoration.
       Two stretches of it are visible even when the object is closed: the
       gap at y 0.86-1.02 and the gap at y 2.26-2.42. Those two slots are
       what make the assembled object read as parts on a shaft. */
    core: [
        [0.00, 0.02], [0.298, 0.02], [0.298, 0.40], [0.262, 0.445], [0.262, 0.50],
        [0.298, 0.545], [0.298, 0.90], [0.272, 0.935], [0.272, 0.985], [0.298, 1.02],
        [0.298, 1.72], [0.262, 1.765], [0.262, 1.82], [0.298, 1.865], [0.298, 2.30],
        [0.272, 2.335], [0.272, 2.385], [0.298, 2.42], [0.298, 2.86],
        [0.282, 2.915], [0.282, 2.97], [0.00, 2.97],
    ],
    /* The nose cap. The ONLY anodised part on the object, and roughly 1.5%
       of its visible surface when assembled. One accent, used once. */
    nose: [
        [0.00, 2.955], [0.284, 2.955], [0.284, 3.012], [0.246, 3.052],
        [0.246, 3.118], [0.176, 3.158], [0.00, 3.17],
    ],
    /* The knurl band that drops into module 02's relief. */
    knurl: [
        [0.580, 0.106], [0.626, 0.126], [0.626, 0.230], [0.580, 0.250], [0.580, 0.106],
    ],
    /* Polymer seal — a thin dark ring. These are the visual full stops: the
       eye needs somewhere to rest between two bright materials, and a 3mm
       dark ring does that better than a gap does. */
    seal: [
        [0.318, 0.00], [0.372, 0.00], [0.372, 0.028], [0.318, 0.028], [0.318, 0.00],
    ],
    /* The polymer sleeve in module 04's recess. */
    sleeve: [
        [0.490, 0.148], [0.514, 0.148], [0.514, 0.376], [0.490, 0.376], [0.490, 0.148],
    ],
};

/* ==========================================================================
   Geometry helpers
   ========================================================================== */

/**
 * LatheGeometry computes its own normals, and it computes them CORRECTLY:
 * where two profile points share a position it splits the normal and leaves
 * a hard edge, and where they do not it smooths along the profile.
 *
 * Do not call computeVertexNormals() on the result. The first version of this
 * file did, and it averaged the normals back together across every chamfer —
 * which turned every machined shoulder into a fillet and made the whole
 * object render as a smooth ceramic chess piece. Every crisp edge on this
 * model is a normal that three.js already split and we then have to not
 * un-split.
 */
/**
 * Duplicate every interior profile point.
 *
 * LatheGeometry smooths its normals ALONG the profile between distinct
 * points and splits them where two consecutive points share a position. A
 * machined part is the second case at every single vertex: the shading runs
 * smoothly AROUND the revolve and breaks hard at every shoulder, chamfer and
 * groove. Authoring that by hand means writing each coordinate twice, which
 * makes the profiles unreadable as drawings, so it is done here instead.
 *
 * This one function is the difference between the object reading as turned
 * aluminium and reading as glazed pottery. The version before it produced a
 * ceramic chess piece from exactly the same coordinates.
 */
function hardEdges(profile) {
    const out = [profile[0]];
    for (let i = 1; i < profile.length - 1; i++) out.push(profile[i], profile[i]);
    out.push(profile[profile.length - 1]);
    return out;
}

function lathe(THREE, profile, segments, smooth) {
    const src = smooth ? profile : hardEdges(profile);
    const pts = src.map(([r, y]) => new THREE.Vector2(r, y));
    return new THREE.LatheGeometry(pts, segments || 96);
}

/**
 * Knurling, done honestly: displace the band's vertices radially by a small
 * sine of their angle. Sixty peaks at 6 thousandths of a unit each.
 *
 * The alternative — 60 separate fin meshes — costs 60 draw calls to look the
 * same at every size this object is ever seen at. This costs none.
 */
function knurl(THREE, geo, peaks, amp) {
    const p = geo.attributes.position;
    const v = new THREE.Vector3();
    for (let i = 0; i < p.count; i++) {
        v.fromBufferAttribute(p, i);
        const r = Math.hypot(v.x, v.z);
        if (r < 0.001) continue;
        const a = Math.atan2(v.z, v.x);
        const k = 1 + (Math.sin(a * peaks) * amp) / r;
        p.setXYZ(i, v.x * k, v.y, v.z * k);
    }
    p.needsUpdate = true;
    geo.computeVertexNormals();
    return geo;
}

/**
 * The branch lug — the small cantilevered boss that projects from each
 * module. This is the "branch" in "one core, five branches", and the reason
 * the five of them climb the object as a helix rather than sitting on a ring.
 *
 * A short barrel, a chamfered shoulder, and a dark polymer tip.
 */
function lugGeometry(THREE, reach) {
    return lathe(THREE, [
        [0.00, 0.00], [0.105, 0.00], [0.105, reach - 0.10], [0.088, reach - 0.062],
        [0.088, reach - 0.028], [0.062, reach], [0.00, reach],
    ], 40);
}

/* ==========================================================================
   Milled forms

   Three renders in, the object was still reading as a bedside lamp. Every
   module was a lathe, so every module was a circle in plan, and five stacked
   circles is a shape domestic objects own — a French press, a chess piece, a
   lamp base. No amount of chamfer detail argues with a silhouette.

   The fix is a change of manufacturing process, not of detail level. Real
   engineered assemblies mix TURNED parts (round, made on a lathe) with
   MILLED parts (prismatic, made on a mill), and the contrast between the two
   is most of what makes a machine look like a machine. So modules 01, 03 and
   05 became milled prisms — a radiused square, a hexagon, a radiused square —
   and 02 and 04 stayed turned. Alternating the two down the stack is also
   what §16 of the brief asks for in one move: unity of material and language,
   without repetition of form.

   Every prism carries the same bore as the turned parts, so the core still
   threads through all five.
   ========================================================================== */

function boredShape(THREE, outline) {
    const shape = outline(new THREE.Shape());
    const hole = new THREE.Path();
    hole.absarc(0, 0, BORE, 0, Math.PI * 2, true);
    shape.holes.push(hole);
    return shape;
}

/**
 * Extrude a bored outline into a prism of height h, standing on y = 0.
 * The bevel is the machined edge-break: 1.5mm at this scale, which is what
 * stops the top edges reading as laser-cut card.
 */
function prism(THREE, outline, h, bevel) {
    const b = bevel === undefined ? 0.022 : bevel;
    const g = new THREE.ExtrudeGeometry(boredShape(THREE, outline), {
        depth: h - b * 2, bevelEnabled: true, bevelThickness: b, bevelSize: b,
        bevelSegments: 2, curveSegments: 14, steps: 1,
    });
    /* Extrusion runs along +Z and starts at -bevelThickness; stand it up and
       drop it so its underside sits exactly on the seat height. */
    g.rotateX(-Math.PI / 2);
    g.translate(0, b, 0);
    return g;
}

/** A square with generously radiused corners — the plan of modules 01 and 05. */
function squircle(a, r) {
    return (s) => {
        s.moveTo(-a + r, -a);
        s.lineTo(a - r, -a); s.quadraticCurveTo(a, -a, a, -a + r);
        s.lineTo(a, a - r);  s.quadraticCurveTo(a, a, a - r, a);
        s.lineTo(-a + r, a); s.quadraticCurveTo(-a, a, -a, a - r);
        s.lineTo(-a, -a + r); s.quadraticCurveTo(-a, -a, -a + r, -a);
        return s;
    };
}

/** A hexagon on its circumradius — the plan of module 03. */
function hexagon(R) {
    return (s) => {
        for (let i = 0; i < 6; i++) {
            const a = (i / 6) * Math.PI * 2 + Math.PI / 6;
            const x = Math.cos(a) * R, y = Math.sin(a) * R;
            i === 0 ? s.moveTo(x, y) : s.lineTo(x, y);
        }
        s.closePath();
        return s;
    };
}

/* ==========================================================================
   Materials — four families, related.

   Ceramic is the body language, aluminium is the mechanism, polymer is
   punctuation, and anodised blue appears exactly once. Five material
   families on one object was one of the ten faults in the build this
   replaces; four, with one of them appearing once, is a palette.
   ========================================================================== */

export function buildMaterials(THREE) {
    /* DoubleSide throughout. ExtrudeGeometry triangulates the cap of a shape
       that has a hole in it with inconsistent winding, so the top face of a
       bored prism renders as a back face and goes black — which is exactly
       what happened to the crown the first time modules 01/03/05 became
       prisms. three.js flips the normal for back faces, so DoubleSide fixes
       both the culling and the shading in one property, and on a 40k-triangle
       object the extra fill is not measurable. */
    const side = THREE.DoubleSide;
    return {
        ceramic: new THREE.MeshPhysicalMaterial({
            color: 0xf5f2ec, roughness: 0.44, metalness: 0.0,
            clearcoat: 0.12, clearcoatRoughness: 0.55,
            side,
        }),
        /* Anisotropy is what makes brushed metal read as BRUSHED rather than
           as slightly rough chrome: it stretches the specular along the
           machining direction. Rotated a quarter turn so the grain runs
           around the part, which is the direction a lathe actually leaves. */
        alu: new THREE.MeshPhysicalMaterial({
            color: 0xc2c5c9, roughness: 0.27, metalness: 1.0,
            anisotropy: 0.62, anisotropyRotation: Math.PI / 2,
            side,
        }),
        anodised: new THREE.MeshPhysicalMaterial({
            color: 0x1b4fd8, roughness: 0.44, metalness: 0.55,
            clearcoat: 0.16, clearcoatRoughness: 0.45,
            side,
        }),
        polymer: new THREE.MeshStandardMaterial({
            color: 0x23262b, roughness: 0.84, metalness: 0.0,
            side,
        }),
    };
}

/* ==========================================================================
   The assembly
   ========================================================================== */

function mesh(THREE, geo, mat) {
    const m = new THREE.Mesh(geo, mat);
    /* Every part both casts and receives. Inter-part shadowing is what gives
       an assembly its crevice darkening — it is doing the job a screen-space
       AO pass would do, at the cost of one shadow map instead of a whole
       post-processing chain. */
    m.castShadow = true;
    m.receiveShadow = true;
    return m;
}

/**
 * Build the object. Returns the root group plus the per-module groups, so a
 * caller can drive the exploded view and project label anchors without
 * knowing anything about the geometry.
 */
export function buildAssembly(THREE) {
    const mats = buildMaterials(THREE);
    const root = new THREE.Group();
    const modules = [];

    /* --- the core -------------------------------------------------------- */
    const core = new THREE.Group();
    core.add(mesh(THREE, lathe(THREE, PROFILE.core, 64), mats.alu));
    core.add(mesh(THREE, lathe(THREE, PROFILE.nose, 64), mats.anodised));
    root.add(core);

    /* --- the five modules ------------------------------------------------ */
    PARTS.forEach((spec, i) => {
        const g = new THREE.Group();
        g.position.y = spec.y;
        g.rotation.y = (spec.clock * Math.PI) / 180;

        const key = ['web', 'security', 'marketing', 'content', 'commerce'][i];

        /* MILLED or TURNED — alternating down the stack. */
        if (key === 'web') {
            g.add(mesh(THREE, prism(THREE, squircle(0.94, 0.28), 0.46, 0.026), mats.ceramic));
        } else if (key === 'marketing') {
            g.add(mesh(THREE, prism(THREE, hexagon(0.90), 0.66, 0.024), mats.ceramic));
        } else if (key === 'commerce') {
            g.add(mesh(THREE, prism(THREE, squircle(0.62, 0.15), 0.50, 0.020), mats.alu));
        } else {
            g.add(mesh(THREE, lathe(THREE, PROFILE[key]), key === 'security' ? mats.alu : mats.ceramic));
        }

        /* Per-module detail. Each module gets exactly one thing that is only
           on that module — the reason you can tell them apart at a glance
           without a label, which is what "component individuality" means. */
        if (key === 'web') {
            /* A milled aluminium sole plate under the base — the one surface
               that touches the ground, so it is the one that gets the metal.
               It also puts a hard, bright edge right where the contact shadow
               is darkest, which is what sells the weight. */
            g.add(mesh(THREE, prism(THREE, squircle(0.985, 0.30), 0.055, 0.012), mats.alu));
            g.add(mesh(THREE, lathe(THREE, PROFILE.seal, 48).translate(0, 0.452, 0), mats.polymer));
        }
        if (key === 'security') {
            g.add(mesh(THREE, knurl(THREE, lathe(THREE, PROFILE.knurl, 220), 72, 0.0042), mats.alu));
            g.add(mesh(THREE, lathe(THREE, PROFILE.seal, 48), mats.polymer));
        }
        if (key === 'marketing') {
            /* THE CANTILEVER. One module breaks the silhouette, and it is
               this one — a flat machined arm reaching out past r 1.5 at its
               own clock angle, with a rectangular section. It is the only
               non-turned form on the object, which is exactly why it works:
               after four round modules the eye lands on the one flat plate.
               Without it the stack is a column; with it, the object has a
               direction and a front. */
            const arm = new THREE.Shape();
            const w = 0.145, L = 0.70;
            arm.moveTo(-w, 0); arm.lineTo(w, 0); arm.lineTo(w * 0.62, L * 0.82);
            arm.lineTo(w * 0.42, L); arm.lineTo(-w * 0.42, L); arm.lineTo(-w * 0.62, L * 0.82);
            arm.lineTo(-w, 0);
            const ag = new THREE.ExtrudeGeometry(arm, {
                depth: 0.115, bevelEnabled: true, bevelThickness: 0.016,
                bevelSize: 0.016, bevelSegments: 3, curveSegments: 4,
            });
            ag.rotateX(Math.PI / 2).rotateY(Math.PI / 2).translate(0.74, 0.330, 0);
            g.add(mesh(THREE, ag, mats.alu));
            /* the boss the arm bolts through */
            const boss = mesh(THREE, lathe(THREE, [
                [0.00, 0.00], [0.135, 0.00], [0.135, 0.085], [0.108, 0.115], [0.00, 0.115],
            ], 40), mats.polymer);
            boss.rotation.z = -Math.PI / 2;
            boss.position.set(0.80, 0.330, 0);
            g.add(boss);
            g.add(mesh(THREE, lathe(THREE, PROFILE.seal, 48), mats.polymer));
        }
        if (key === 'content') {
            g.add(mesh(THREE, lathe(THREE, PROFILE.sleeve, 96), mats.polymer));
            g.add(mesh(THREE, lathe(THREE, PROFILE.seal, 48), mats.polymer));
        }
        if (key === 'commerce') {
            /* A turned ceramic collar dropped into the milled crown: the one
               place on the object where both processes meet on one part, and
               the matt surround the anodised nose reads against. */
            g.add(mesh(THREE, lathe(THREE, [
                [BORE, 0.395], [0.470, 0.395], [0.470, 0.455], [0.418, 0.505],
                [BORE, 0.505], [BORE, 0.395],
            ], 64), mats.ceramic));
        }

        /* the branch lug */
        const reach = key === 'security' || key === 'content' ? 0.34 : 0.28;
        const lugOuter = key === 'marketing' ? 0.86 : spec.r - 0.06;
        const lug = mesh(THREE, lugGeometry(THREE, reach), mats.alu);
        lug.rotation.z = -Math.PI / 2;
        lug.position.set(lugOuter, spec.h * 0.5, 0);
        g.add(lug);
        const tip = mesh(THREE, lathe(THREE, [
            [0.00, 0.00], [0.064, 0.00], [0.058, 0.026], [0.00, 0.026],
        ], 40), mats.polymer);
        tip.rotation.z = -Math.PI / 2;
        tip.position.set(lugOuter + reach, spec.h * 0.5, 0);
        g.add(tip);

        root.add(g);
        modules.push({ group: g, spec, home: spec.y });
    });

    return { root, core, modules, materials: mats };
}

/**
 * Drive the exploded view. t = 0 assembled, t = 1 fully open.
 *
 * The core does not move. Everything else travels away from it, at its own
 * start time, over its own duration, by its own distance and in its own
 * lateral direction. That asymmetry is authored in PARTS above and it is
 * what separates an engineering assembly from a five-petal flower opening.
 */
export function setExplode(assembly, t) {
    const clamp = (v) => (v < 0 ? 0 : v > 1 ? 1 : v);
    /* Ease each module independently. Cubic out: parts leave quickly and
       arrive slowly, which is how a part on a rail behaves. */
    const ease = (x) => 1 - Math.pow(1 - x, 3);

    for (const m of assembly.modules) {
        const { spec } = m;
        const local = ease(clamp((t - spec.t0) / Math.max(0.001, spec.t1 - spec.t0)));
        m.group.position.y = spec.y + spec.lift * local;
        m.group.position.x = spec.drift[0] * local;
        m.group.position.z = spec.drift[1] * local;
        /* A few degrees of counter-rotation as it opens. Small enough that
           you read it as the part turning off its thread, not as spin. */
        m.group.rotation.y = (spec.clock * Math.PI) / 180 + local * 0.16 * (spec.clock > 180 ? -1 : 1);
    }
}

/**
 * Where a label should point, in world space, for module i at the current
 * explode state. The caller projects this to screen coordinates and puts a
 * DOM label there — DOM, not a sprite, because a label has to be selectable,
 * translatable and readable by a screen reader.
 */
export function anchorFor(assembly, i) {
    const m = assembly.modules[i];
    const a = (m.spec.clock * Math.PI) / 180;
    const r = 1.05;
    return [
        m.group.position.x + Math.cos(a) * r,
        m.group.position.y + m.spec.h * 0.5,
        m.group.position.z - Math.sin(a) * r,
    ];
}
