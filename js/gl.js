/**
 * GL — the site's 3D layer.
 *
 * A real-time WebGL2 renderer for the hero object and the scroll-driven story
 * stage. One file, no dependencies, ~14KB unminified.
 *
 * WHY NOT THREE.JS
 *   Two hard constraints, both already load-bearing elsewhere in this codebase:
 *   there is no build step, and the Content-Security-Policy is `script-src
 *   'self'` (inc/security.php) with every third-party origin refused. Vendoring
 *   three.js means committing ~600KB of minified code nobody here can review,
 *   to draw a few thousand points. This draws them directly.
 *
 * WHAT IT DRAWS
 *   One point cloud of S x R particles, plus the strands connecting them, lit
 *   additively on a dark ground. The same cloud can hold several FORMS — a
 *   torus knot, a sphere, a terrain, a double helix — sampled on the SAME
 *   (s, r) parameterisation, which is the trick that makes morphing free: every
 *   form has identical point count and identical line topology, so the shader
 *   only has to mix two position buffers.
 *
 * HOW IT MOVES
 *   Everything is driven from uniforms, so no geometry is rebuilt per frame:
 *
 *     uMorph      0..1 between the current pair of forms
 *     uDissolve   pushes every particle along its own outward normal
 *     uScroll     0..1 progress of the host element through the viewport
 *     uPointer    parallax tilt, -1..1
 *
 *   The hero maps scroll to dissolve + camera dolly: the object breaks apart as
 *   you leave it. The story stage maps scroll to morph: the object becomes the
 *   next form as each step is read.
 *
 * PERFORMANCE CONTRACT  (the same one js/plexus.js signs)
 *   - one rAF loop shared by every instance on the page
 *   - an instance renders only while its canvas intersects the viewport
 *   - DPR capped at 2, and at 1.5 above 1600px where the pixels are free anyway
 *   - point/line counts step down on narrow viewports and coarse pointers
 *   - prefers-reduced-motion: one static frame, then the loop never starts
 *   - no WebGL2, no context, or a failed compile: the canvas is left untouched
 *     and .is-gl-off goes on the host, which CSS uses to show the flat gradient
 *
 * USAGE
 *   <canvas data-gl="knot"></canvas>
 *   <canvas data-gl="sphere,knot,grid,helix" data-mode="story"></canvas>
 *
 *   data-gl      comma-separated form names, in the order they are morphed
 *   data-mode    'hero' (default) dissolves on scroll; 'story' morphs on scroll
 *                or on the active .st-step, whichever the host provides
 *   data-density 0..2 multiplier on point count (default 1)
 *   data-morph   'scroll' makes a hero-mode object ALSO change form as it goes
 *   data-zoom    0.2..3 multiplier on camera distance; below 1 fills the frame
 *   data-dissolve 0..2 multiplier on how far a hero-mode object breaks apart
 *   data-ink     'dark' draws dark points on a light ground (paper edition)
 *   data-col-a / data-col-b / data-col-hot   palette overrides, hex
 */
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var coarse       = window.matchMedia('(pointer: coarse)').matches;

    /* ------------------------------------------------------------------ *
     * Small matrix helpers. Column-major, the layout GL expects.
     * ------------------------------------------------------------------ */

    function perspective(out, fovy, aspect, near, far) {
        var f = 1 / Math.tan(fovy / 2), nf = 1 / (near - far);
        out[0] = f / aspect; out[1] = 0; out[2] = 0;  out[3] = 0;
        out[4] = 0; out[5] = f; out[6] = 0;           out[7] = 0;
        out[8] = 0; out[9] = 0; out[10] = (far + near) * nf; out[11] = -1;
        out[12] = 0; out[13] = 0; out[14] = 2 * far * near * nf; out[15] = 0;
        return out;
    }

    /** Model-view built directly: rotate Y, rotate X, then translate on Z. */
    function modelView(out, rx, ry, tz) {
        var cx = Math.cos(rx), sx = Math.sin(rx),
            cy = Math.cos(ry), sy = Math.sin(ry);

        out[0] = cy;       out[1] = sx * sy;   out[2] = -cx * sy;  out[3] = 0;
        out[4] = 0;        out[5] = cx;        out[6] = sx;        out[7] = 0;
        out[8] = sy;       out[9] = -sx * cy;  out[10] = cx * cy;  out[11] = 0;
        out[12] = 0;       out[13] = 0;        out[14] = tz;       out[15] = 1;
        return out;
    }

    function multiply(out, a, b) {
        for (var c = 0; c < 4; c++) {
            var b0 = b[c * 4], b1 = b[c * 4 + 1], b2 = b[c * 4 + 2], b3 = b[c * 4 + 3];
            out[c * 4]     = b0 * a[0] + b1 * a[4] + b2 * a[8]  + b3 * a[12];
            out[c * 4 + 1] = b0 * a[1] + b1 * a[5] + b2 * a[9]  + b3 * a[13];
            out[c * 4 + 2] = b0 * a[2] + b1 * a[6] + b2 * a[10] + b3 * a[14];
            out[c * 4 + 3] = b0 * a[3] + b1 * a[7] + b2 * a[11] + b3 * a[15];
        }
        return out;
    }

    /* ------------------------------------------------------------------ *
     * FORMS
     *
     * Every builder receives the same (s, r) lattice and fills position and
     * outward-normal arrays. s runs along the form, r runs around it. Keeping
     * that contract is what lets any form morph into any other, and lets one
     * index buffer draw the strands for all of them.
     * ------------------------------------------------------------------ */

    var TAU = Math.PI * 2;

    /* A (2,3) torus knot, tubed. The hero object: one continuous path that
       reads as a woven system rather than as a ball of dots. */
    function formKnot(S, R, pos, nrm) {
        /* The (2,3) torus knot, closed on itself:
             x = (2 + cos 3t) cos 2t,  y = (2 + cos 3t) sin 2t,  z = sin 3t
           Closure matters — an open curve leaves a visible seam where the last
           ring meets nothing, and the object stops reading as one path. */
        var K = 0.55;
        var curve = function (t, out) {
            var w = 2 + Math.cos(3 * t);
            out[0] = w * Math.cos(2 * t) * K;
            out[1] = w * Math.sin(2 * t) * K;
            out[2] = Math.sin(3 * t) * K;
        };

        var c = [0, 0, 0], c2 = [0, 0, 0], i = 0;
        for (var s = 0; s < S; s++) {
            var t0 = (s / S) * TAU;
            curve(t0, c);
            curve(t0 + 0.01, c2);
            var cx = c[0], cy = c[1], cz = c[2];

            var tx = c2[0] - cx, ty = c2[1] - cy, tz = c2[2] - cz;
            var tl = Math.hypot(tx, ty, tz) || 1;
            tx /= tl; ty /= tl; tz /= tl;

            // Normal from the cross product with world up, binormal from both.
            var nx = ty * 0 - tz * 1, ny = tz * 0 - tx * 0, nz = tx * 1 - ty * 0;
            var nl = Math.hypot(nx, ny, nz) || 1;
            nx /= nl; ny /= nl; nz /= nl;
            var bx = ty * nz - tz * ny, by = tz * nx - tx * nz, bz = tx * ny - ty * nx;

            for (var r = 0; r < R; r++) {
                var v = (r / R) * TAU;
                var ca = Math.cos(v) * 0.30, sa = Math.sin(v) * 0.30;
                var ox = nx * ca + bx * sa, oy = ny * ca + by * sa, oz = nz * ca + bz * sa;
                pos[i] = cx + ox; nrm[i++] = ox / 0.30;
                pos[i] = cy + oy; nrm[i++] = oy / 0.30;
                pos[i] = cz + oz; nrm[i++] = oz / 0.30;
            }
        }
    }

    /* A UV sphere.
       s is LATITUDE and r is LONGITUDE, not the other way round. The shared
       index buffer joins (s, r) to (s+1, r), so this ordering draws meridians —
       a cage running pole to pole. With the axes swapped the same buffer drew
       latitude circles instead, and a sphere made of stacked horizontal rings
       reads as a tiered cake rather than as a sphere. */
    function formSphere(S, R, pos, nrm) {
        var i = 0, rad = 1.5;
        for (var s = 0; s < S; s++) {
            var lat = ((s + 0.5) / S) * Math.PI;
            var sl = Math.sin(lat), cl = Math.cos(lat);
            for (var r = 0; r < R; r++) {
                var lon = (r / R) * TAU;
                var x = Math.cos(lon) * sl, y = cl, z = Math.sin(lon) * sl;
                pos[i] = x * rad; nrm[i++] = x;
                pos[i] = y * rad; nrm[i++] = y;
                pos[i] = z * rad; nrm[i++] = z;
            }
        }
    }

    /* A displaced plane — the "ground" form. Reads as terrain or as a data
       surface depending on what the copy beside it is saying. */
    function formGrid(S, R, pos, nrm) {
        var i = 0;
        for (var s = 0; s < S; s++) {
            var x = (s / (S - 1) - 0.5) * 4.4;
            for (var r = 0; r < R; r++) {
                var z = (r / (R - 1) - 0.5) * 4.4;
                var y = Math.sin(x * 1.6) * 0.28 + Math.cos(z * 1.9) * 0.24
                      + Math.sin((x + z) * 0.9) * 0.16;
                pos[i] = x;     nrm[i++] = x * 0.25;
                pos[i] = y - 0.2; nrm[i++] = 1;
                pos[i] = z;     nrm[i++] = z * 0.25;
            }
        }
    }

    /* Two counter-rotating strands around a vertical axis — the pipeline form:
       separate tracks running in parallel toward the same place. */
    function formHelix(S, R, pos, nrm) {
        var i = 0;
        for (var s = 0; s < S; s++) {
            var t = s / (S - 1);
            var y = (t - 0.5) * 3.6;
            var a = t * TAU * 2.4;
            for (var r = 0; r < R; r++) {
                var strand = (r % 2) * Math.PI;               // two ribbons
                var spread = ((r / R) - 0.5) * 0.55;
                var ang = a + strand + spread;
                var rad = 1.15 + Math.sin(t * Math.PI) * 0.22;
                var x = Math.cos(ang) * rad, z = Math.sin(ang) * rad;
                pos[i] = x; nrm[i++] = x;
                pos[i] = y; nrm[i++] = 0;
                pos[i] = z; nrm[i++] = z;
            }
        }
    }

    /* THE DIGITAL CORE — the hero object.
       Three regions of the same lattice, split along s so the strand topology
       stays continuous and it can still morph into anything else:

         inner 55%   a dense sphere, the core
         next 30%    two orbital rings, tilted against each other, read as
                     light trails once the point pass draws over them
         outer 15%   a sparse, larger shell — the "field" around the core

       Not a bought model. Proprietary geometry, in the brand's own colours. */
    function formCore(S, R, pos, nrm) {
        var i = 0;
        var sInner = Math.floor(S * 0.55), sRings = Math.floor(S * 0.30);
        for (var s = 0; s < S; s++) {
            for (var r = 0; r < R; r++) {
                var x, y, z, nx, ny, nz;
                if (s < sInner) {
                    var lat = ((s + 0.5) / sInner) * Math.PI;
                    var lon = (r / R) * TAU + (s % 2) * (Math.PI / R);
                    var sl = Math.sin(lat), rad = 1.02;
                    nx = Math.cos(lon) * sl; ny = Math.cos(lat); nz = Math.sin(lon) * sl;
                    x = nx * rad; y = ny * rad; z = nz * rad;
                } else if (s < sInner + sRings) {
                    var k = s - sInner;
                    var ring = k < sRings / 2 ? 0 : 1;             // which of two rings
                    var t = ((k % Math.ceil(sRings / 2)) / (sRings / 2));
                    var ang = t * TAU + (r / R) * (TAU / (sRings / 2)); // r spreads along the ring
                    var rr = 1.75 + (r % 3) * 0.05;                  // three thin threads
                    var px = Math.cos(ang) * rr, pz = Math.sin(ang) * rr, py = 0;
                    var tilt = ring === 0 ? 0.62 : -0.85;
                    var roll = ring === 0 ? 0.35 : 2.2;
                    // rotate about X by tilt, then about Y by roll
                    var y1 = py * Math.cos(tilt) - pz * Math.sin(tilt);
                    var z1 = py * Math.sin(tilt) + pz * Math.cos(tilt);
                    x = px * Math.cos(roll) + z1 * Math.sin(roll);
                    z = -px * Math.sin(roll) + z1 * Math.cos(roll);
                    y = y1;
                    var l = Math.hypot(x, y, z) || 1;
                    nx = x / l; ny = y / l; nz = z / l;
                } else {
                    var k2 = s - sInner - sRings, n2 = Math.max(1, S - sInner - sRings);
                    var lat2 = ((k2 + 0.5) / n2) * Math.PI;
                    var lon2 = (r / R) * TAU + k2 * 0.7;
                    var sl2 = Math.sin(lat2), rad2 = 2.35;
                    nx = Math.cos(lon2) * sl2; ny = Math.cos(lat2); nz = Math.sin(lon2) * sl2;
                    x = nx * rad2; y = ny * rad2; z = nz * rad2;
                }
                pos[i] = x; nrm[i++] = nx;
                pos[i] = y; nrm[i++] = ny;
                pos[i] = z; nrm[i++] = nz;
            }
        }
    }

    var FORMS = { knot: formKnot, sphere: formSphere, grid: formGrid, helix: formHelix, core: formCore };

    /* ------------------------------------------------------------------ *
     * Shaders
     * ------------------------------------------------------------------ */

    /* Shared vertex stage. The only difference between the point pass and the
       line pass is that lines ignore gl_PointSize, so one source serves both. */
    var VERT = [
        '#version 300 es',
        'precision highp float;',
        'in vec3 aA;',            // position in form A
        'in vec3 aB;',            // position in form B
        'in vec3 aN;',            // outward normal, for the dissolve
        'in vec2 aMeta;',         // x: per-point random 0..1, y: 0..1 along the form
        'uniform mat4 uMVP;',
        'uniform float uTime, uMorph, uDissolve, uSize, uDpr, uReveal, uTiltX;',
        'out float vDepth; out float vRand; out float vAlong;',
        'void main() {',
        '  float rnd = aMeta.x;',
        // Morph is eased per-point so the form does not snap over as one sheet:
        // points further along the strand arrive slightly later.
        '  float m = clamp(uMorph * 1.35 - aMeta.y * 0.35, 0.0, 1.0);',
        '  m = m * m * (3.0 - 2.0 * m);',
        '  vec3 p = mix(aA, aB, m);',
        // Dissolve: every point leaves along its own outward normal, at its own
        // rate, which is what makes the break-up read as dust and not as a
        // uniform scale-up.
        '  p += aN * (uDissolve * (0.25 + rnd * 1.75));',
        // A slow wander so a still object never looks paused.
        '  float w = uTime * 0.55 + rnd * 6.2831;',
        '  p += vec3(sin(w), cos(w * 0.87), sin(w * 1.31)) * (0.015 + uDissolve * 0.06);',
        // Reveal: the first second of life, drawn as the cloud settling inward.
        '  p *= mix(1.35, 1.0, uReveal);',
        // A slow breathing tilt about X, so a form that is otherwise only
        // spinning about Y keeps showing its top and underside — the cheapest
        // way to make a wireframe read as a solid turning in space.
        '  float cb = cos(uTiltX), sb = sin(uTiltX);',
        '  p = vec3(p.x, p.y * cb - p.z * sb, p.y * sb + p.z * cb);',
        '  vec4 clip = uMVP * vec4(p, 1.0);',
        '  gl_Position = clip;',
        '  gl_PointSize = max(1.0, (uSize * uDpr * (0.55 + rnd * 0.9)) / max(clip.w, 0.001));',
        '  vDepth = clamp(1.0 - (clip.w - 3.0) / 7.0, 0.0, 1.0);',
        '  vRand = rnd;',
        '  vAlong = aMeta.y;',
        '}'
    ].join('\n');

    var FRAG_POINT = [
        '#version 300 es',
        'precision highp float;',
        'in float vDepth; in float vRand; in float vAlong;',
        'uniform vec3 uColA, uColB, uColHot;',
        'uniform float uAlpha, uReveal, uGlow, uInk;',
        'out vec4 frag;',
        'void main() {',
        '  vec2 c = gl_PointCoord - 0.5;',
        '  float d = length(c);',
        // Two passes share this shader. uGlow=0: the sharp point — a bright
        // core with a small halo. uGlow=1: the BLOOM pass — the same point
        // drawn 3x larger, very soft and dim, underneath. Two draw calls give
        // the look of a post-process bloom with no framebuffer at all.
        '  float core = smoothstep(0.5, 0.05, d);',
        '  float halo = smoothstep(0.5, 0.0, d) * 0.55;',
        '  float soft = pow(max(0.0, 1.0 - d * 2.0), 2.2) * 0.16;',
        '  float shape = mix(core + halo, soft, uGlow);',
        '  float a = shape * (0.34 + vDepth * 1.05) * uAlpha * uReveal;',
        // INK MODE (paper edition): the bloom pass would print as a grey
        // smudge under every dot, so it is damped to a faint halo.
        '  a *= mix(1.0, 0.35, uInk * uGlow);',
        '  vec3 col = mix(uColA, uColB, smoothstep(0.15, 0.95, vDepth * 0.7 + vRand * 0.5));',
        // A small minority of points burn out to near-white. Without them the
        // cloud is evenly lit, and an evenly lit cloud reads as a texture
        // rather than as an object with light in it.
        '  col = mix(col, uColHot, smoothstep(0.93, 1.0, vRand) * vDepth);',
        // PREMULTIPLIED. The canvas is created with premultipliedAlpha:true and
        // blends ONE/ONE, so the shader multiplies the colour into the alpha
        // itself. Getting this wrong is silent: with straight alpha the browser
        // multiplies RGB by the accumulated alpha a second time at composite,
        // and an additive cloud whose alpha never reaches 1 simply vanishes.
        '  frag = vec4(col * a, a);',
        '}'
    ].join('\n');

    var FRAG_LINE = [
        '#version 300 es',
        'precision highp float;',
        'in float vDepth; in float vRand; in float vAlong;',
        'uniform vec3 uColA, uColB, uColHot;',
        'uniform float uAlpha, uReveal;',
        'out vec4 frag;',
        'void main() {',
        '  float a = (0.10 + vDepth * 0.38) * uAlpha * uReveal;',
        '  vec3 col = mix(uColA, uColB, vDepth);',
        '  frag = vec4(col * a, a);',
        '}'
    ].join('\n');

    function compile(gl, type, src) {
        var sh = gl.createShader(type);
        gl.shaderSource(sh, src);
        gl.compileShader(sh);
        if (!gl.getShaderParameter(sh, gl.COMPILE_STATUS)) {
            gl.deleteShader(sh);
            return null;
        }
        return sh;
    }

    function program(gl, fragSrc) {
        var vs = compile(gl, gl.VERTEX_SHADER, VERT);
        var fs = compile(gl, gl.FRAGMENT_SHADER, fragSrc);
        if (!vs || !fs) return null;

        var p = gl.createProgram();
        gl.attachShader(p, vs); gl.attachShader(p, fs);
        gl.linkProgram(p);
        gl.deleteShader(vs); gl.deleteShader(fs);
        if (!gl.getProgramParameter(p, gl.LINK_STATUS)) { gl.deleteProgram(p); return null; }

        var loc = { prog: p, attr: {}, u: {} };
        ['aA', 'aB', 'aN', 'aMeta'].forEach(function (n) { loc.attr[n] = gl.getAttribLocation(p, n); });
        ['uMVP', 'uTime', 'uMorph', 'uDissolve', 'uSize', 'uDpr', 'uReveal', 'uTiltX',
         'uColA', 'uColB', 'uColHot', 'uAlpha', 'uGlow', 'uInk'].forEach(function (n) {
            loc.u[n] = gl.getUniformLocation(p, n);
        });
        return loc;
    }

    /* ------------------------------------------------------------------ *
     * Instance
     * ------------------------------------------------------------------ */

    function hexToRgb(hex, fallback) {
        var m = /^#?([0-9a-f]{6})$/i.exec(hex || '');
        if (!m) return fallback;
        var n = parseInt(m[1], 16);
        return [((n >> 16) & 255) / 255, ((n >> 8) & 255) / 255, (n & 255) / 255];
    }

    function Instance(canvas) {
        var gl = canvas.getContext('webgl2', {
            alpha: true,
            antialias: false,          // additive points do not benefit from MSAA
            depth: false,              // additive blending needs no depth sort
            premultipliedAlpha: true,  // see the note in FRAG_POINT
            powerPreference: 'high-performance'
        });
        if (!gl) return null;

        this.gl = gl;
        this.canvas = canvas;
        this.host = canvas.closest('[data-gl-host]') || canvas.parentElement;
        this.mode = canvas.getAttribute('data-mode') || 'hero';

        /* Forms. A name is either a key of FORMS or "image:<same-origin url>",
           which samples a picture into the same lattice (see loadImageForm). */
        var names = (canvas.getAttribute('data-gl') || 'knot')
            .split(',').map(function (n) { return n.trim(); })
            .filter(function (n) { return FORMS[n] || n.indexOf('image:') === 0; });
        if (!names.length) names = ['knot'];
        this.names = names;
        this.isImage = names[0].indexOf('image:') === 0;

        /* INK MODE. The paper edition draws dark points on a light ground, so
           additive blending (light adds) is replaced by ordinary premultiplied
           "over", and the palette defaults flip to ink. */
        this.ink = canvas.getAttribute('data-ink') === 'dark';

        /* MORPH ON SCROLL. 'hero' mode dissolves as the host leaves the
           viewport; 'story' mode changes form. The homepage hero is asked to do
           both, so it opts in here rather than getting a third mode: the two
           mappings then run off the same 0..1 and the cloud reads as BECOMING
           the next shape while it comes apart, instead of merely blowing away.
           Opt-in, so the delivery band already using hero mode is unchanged. */
        this.morphOnScroll = canvas.getAttribute('data-morph') === 'scroll';

        /* ZOOM. The camera distance is otherwise fixed, and the object is framed
           by the canvas HEIGHT, so a host that is much wider than it is tall
           gets a small object in a lot of empty space. Below 1 pulls the camera
           in and the object fills more of the frame. Multiplier rather than an
           absolute distance, so the per-mode dolly maths above is untouched. */
        var zoom = parseFloat(canvas.getAttribute('data-zoom'));
        this.zoom = (zoom > 0.2 && zoom < 3) ? zoom : 1;

        /* DISSOLVE SCALE. Hero mode pushes every point out along its normal by
           up to 2.6 units as the section leaves. That was tuned against a small
           object on a dark band, where the scatter reads as sparks. Pulled in
           close on a light ground it becomes a web of strands across the whole
           section — over the lead, over the buttons — and the effect stops
           being the object leaving and starts being a mess. Below 1 keeps the
           form legible while it goes. */
        var diss = parseFloat(canvas.getAttribute('data-dissolve'));
        this.dissolveScale = (diss >= 0 && diss <= 2) ? diss : 1;

        /* Resolution. The strand count (R) stays fixed so the topology reads the
           same everywhere; only the ring count (S) steps down, which costs
           density rather than shape. */
        var density = parseFloat(canvas.getAttribute('data-density'));
        if (!(density > 0)) density = 1;
        var wide = window.innerWidth;
        var base = wide < 640 ? 96 : wide < 1100 ? 150 : 208;
        if (coarse) base = Math.round(base * 0.72);

        this.R = 16;
        this.S = Math.max(48, Math.round(base * density));
        this.count = this.S * this.R;

        this.build();
        this.progPoint = program(gl, FRAG_POINT);
        this.progLine  = program(gl, FRAG_LINE);
        if (!this.progPoint || !this.progLine) return null;

        this.colA   = hexToRgb(canvas.getAttribute('data-col-a'),   this.ink ? [0.06, 0.06, 0.06] : [0.10, 0.32, 0.78]);
        this.colB   = hexToRgb(canvas.getAttribute('data-col-b'),   this.ink ? [0.30, 0.30, 0.30] : [0.22, 0.74, 0.97]);
        this.colHot = hexToRgb(canvas.getAttribute('data-col-hot'), this.ink ? [0.00, 0.00, 0.00] : [0.85, 0.95, 1.00]);

        this.mvp   = new Float32Array(16);
        this.proj  = new Float32Array(16);
        this.view  = new Float32Array(16);

        this.scroll = 0;      // 0..1, host through viewport
        this.morph = 0;       // 0..(forms-1)
        this.targetMorph = 0;
        this.px = 0; this.py = 0;         // pointer, smoothed
        this.tpx = 0; this.tpy = 0;
        this.reveal = 0;
        this.visible = false;
        this.dpr = 1;
        this.w = 0; this.h = 0;
        this.born = 0;        // time the object first became visible (intro ramp)

        gl.disable(gl.DEPTH_TEST);
        gl.enable(gl.BLEND);
        if (this.ink) gl.blendFunc(gl.ONE, gl.ONE_MINUS_SRC_ALPHA);   // premultiplied over
        else          gl.blendFunc(gl.ONE, gl.ONE);                   // additive: light adds
        gl.clearColor(0, 0, 0, 0);

        this.resize();

        // Image forms load after the fact and overwrite their placeholder.
        for (var f = 0; f < names.length; f++) {
            if (names[f].indexOf('image:') === 0) this.loadImageForm(f, names[f].slice(6));
        }
        return this;
    }

    /** Builds every form's vertex data once, plus the shared strand indices. */
    Instance.prototype.build = function () {
        var gl = this.gl, S = this.S, R = this.R, n = this.count;

        this.buffers = this.names.map(function (name) {
            var pos = new Float32Array(n * 3), nrm = new Float32Array(n * 3);
            // An image form starts as a sphere and is overwritten once the
            // picture has been sampled (loadImageForm).
            (FORMS[name] || formSphere)(S, R, pos, nrm);

            var pb = gl.createBuffer();
            gl.bindBuffer(gl.ARRAY_BUFFER, pb);
            gl.bufferData(gl.ARRAY_BUFFER, pos, gl.STATIC_DRAW);

            var nb = gl.createBuffer();
            gl.bindBuffer(gl.ARRAY_BUFFER, nb);
            gl.bufferData(gl.ARRAY_BUFFER, nrm, gl.STATIC_DRAW);

            return { pos: pb, nrm: nb };
        });

        /* Per-point metadata. The "random" is a hash of the index, NOT
           Math.random(): the cloud must look identical on every load, because a
           hero that composes itself differently each refresh is a hero nobody
           can art-direct. */
        var meta = new Float32Array(n * 2);
        for (var i = 0; i < n; i++) {
            var h = Math.sin(i * 12.9898 + 78.233) * 43758.5453;
            meta[i * 2]     = h - Math.floor(h);
            meta[i * 2 + 1] = (i / R | 0) / S;
        }
        this.metaBuf = gl.createBuffer();
        gl.bindBuffer(gl.ARRAY_BUFFER, this.metaBuf);
        gl.bufferData(gl.ARRAY_BUFFER, meta, gl.STATIC_DRAW);

        /* Strands: point (s, r) joined to (s+1, r). One index buffer serves
           every form, which is the pay-off for the shared parameterisation. */
        var idx = new Uint16Array((S - 1) * R * 2), k = 0;
        for (var s = 0; s < S - 1; s++) {
            for (var r = 0; r < R; r++) {
                idx[k++] = s * R + r;
                idx[k++] = (s + 1) * R + r;
            }
        }
        this.lineCount = k;
        this.idxBuf = gl.createBuffer();
        gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, this.idxBuf);
        gl.bufferData(gl.ELEMENT_ARRAY_BUFFER, idx, gl.STATIC_DRAW);
    };

    /**
     * IMAGE FORM. Samples a same-origin picture into the lattice: S*R points
     * placed where the picture is dark, arranged as R near-vertical threads
     * (each thread is a column-quantile of the sample sorted top to bottom),
     * so the line pass draws the picture as scan-threads and the point pass
     * as ink dots. Depth is a little relief from darkness plus jitter, so a
     * tilt reads as 3D; normals point outward from the centroid, so the hero
     * dissolve explodes the picture rather than sliding it.
     *
     * The picture is expected to be a small greyscale mask with alpha
     * (a same-origin greyscale mask PNG). Same origin only: getImageData taints on
     * anything else, and CSP img-src is 'self' anyway.
     */
    Instance.prototype.loadImageForm = function (slot, url) {
        var self = this, gl = this.gl, S = this.S, R = this.R, n = this.count;
        var img = new Image();
        img.decoding = 'async';
        img.onload = function () {
            var w = img.naturalWidth, h = img.naturalHeight;
            if (!w || !h) return;
            var c = document.createElement('canvas');
            c.width = w; c.height = h;
            var ctx = c.getContext('2d', { willReadFrequently: true });
            if (!ctx) return;
            ctx.drawImage(img, 0, 0);
            var px;
            try { px = ctx.getImageData(0, 0, w, h).data; } catch (e) { return; }

            // Cumulative weight over pixels: darkness x alpha.
            var cum = new Float32Array(w * h), total = 0;
            for (var i = 0; i < w * h; i++) {
                var g = px[i * 4], a = px[i * 4 + 3] / 255;
                var wgt = (1 - g / 255) * a;
                total += wgt * wgt;
                cum[i] = total;
            }
            if (total <= 0) return;

            // Stratified sample: n draws at evenly spaced quantiles, jittered.
            var pts = new Array(n);
            for (var k = 0; k < n; k++) {
                var hh = Math.sin(k * 91.7 + 13.1) * 43758.5453; hh -= Math.floor(hh);
                var u = ((k + hh) / n) * total;
                var lo = 0, hi = w * h - 1;
                while (lo < hi) { var mid = (lo + hi) >> 1; if (cum[mid] < u) lo = mid + 1; else hi = mid; }
                var yy = (lo / w) | 0, xx = lo - yy * w;
                var h2 = Math.sin(k * 12.9898 + 78.233) * 43758.5453; h2 -= Math.floor(h2);
                var h3 = Math.sin(k * 45.164 + 94.673) * 43758.5453; h3 -= Math.floor(h3);
                pts[k] = { x: xx + h2, y: yy + h3, d: 1 - px[lo * 4] / 255, r: hh };
            }

            // Threads: sort by x, cut into R equal groups, each sorted by y.
            pts.sort(function (p, q) { return p.x - q.x; });
            var pos = new Float32Array(n * 3), nrm = new Float32Array(n * 3);
            // World extents: fill the canvas at the hero's resting camera.
            var aspect = (self.w && self.h) ? self.w / self.h : w / h;
            var viewH = 2 * 6.1 * Math.tan(0.85 / 2);
            var worldW = viewH * aspect * 0.96;
            var worldH = worldW * h / w;
            for (var r = 0; r < R; r++) {
                var band = pts.slice(Math.round(r * n / R), Math.round((r + 1) * n / R));
                band.sort(function (p, q) { return p.y - q.y; });
                for (var si = 0; si < S && si < band.length; si++) {
                    var p = band[si];
                    var idx = (si * R + r) * 3;                    // lattice order (s, r)
                    var X = (p.x / w - 0.5) * worldW;
                    var Y = (0.5 - p.y / h) * worldH;
                    var Z = (p.d - 0.5) * 0.35 + (p.r - 0.5) * 0.18;
                    pos[idx] = X; pos[idx + 1] = Y; pos[idx + 2] = Z;
                    var len = Math.sqrt(X * X + Y * Y) || 1;
                    nrm[idx] = X / len * 0.8; nrm[idx + 1] = Y / len * 0.8; nrm[idx + 2] = (p.r - 0.35) * 1.4;
                }
            }
            gl.bindBuffer(gl.ARRAY_BUFFER, self.buffers[slot].pos);
            gl.bufferSubData(gl.ARRAY_BUFFER, 0, pos);
            gl.bindBuffer(gl.ARRAY_BUFFER, self.buffers[slot].nrm);
            gl.bufferSubData(gl.ARRAY_BUFFER, 0, nrm);
            self.imageReady = true;
            self.born = 0;                                       // assemble from now
            kick();
        };
        img.src = url;
    };

    Instance.prototype.resize = function () {
        var rect = this.canvas.getBoundingClientRect();
        if (!rect.width || !rect.height) return;

        var cap = window.innerWidth > 1600 ? 1.5 : 2;
        var dpr = Math.min(cap, window.devicePixelRatio || 1);
        var w = Math.round(rect.width * dpr), h = Math.round(rect.height * dpr);
        if (w === this.w && h === this.h) return;

        this.w = w; this.h = h; this.dpr = dpr;
        this.canvas.width = w; this.canvas.height = h;
        this.gl.viewport(0, 0, w, h);
        perspective(this.proj, 0.85, w / h, 0.1, 60);
    };

    /** Binds the four attributes for one program against one pair of forms. */
    Instance.prototype.bindAttribs = function (loc, a, b) {
        var gl = this.gl;
        var pairs = [
            [loc.attr.aA, this.buffers[a].pos, 3],
            [loc.attr.aB, this.buffers[b].pos, 3],
            [loc.attr.aN, this.buffers[a].nrm, 3],
            [loc.attr.aMeta, this.metaBuf, 2]
        ];
        for (var i = 0; i < pairs.length; i++) {
            var p = pairs[i];
            if (p[0] < 0) continue;
            gl.bindBuffer(gl.ARRAY_BUFFER, p[1]);
            gl.enableVertexAttribArray(p[0]);
            gl.vertexAttribPointer(p[0], p[2], gl.FLOAT, false, 0, 0);
        }
    };

    Instance.prototype.draw = function (t) {
        var gl = this.gl;
        if (!this.w || !this.h) return;

        /* Ease everything that a person can perceive as motion. The raw inputs
           (scroll position, pointer position) are steps; the rendered values
           have to be curves, or the object twitches on every wheel tick. */
        var k = reduceMotion ? 1 : 0.085;
        this.px += (this.tpx - this.px) * k;
        this.py += (this.tpy - this.py) * k;
        this.morph += (this.targetMorph - this.morph) * (reduceMotion ? 1 : 0.06);
        this.reveal += ((this.visible ? 1 : 0) - this.reveal) * (reduceMotion ? 1 : 0.05);

        var seg = Math.min(this.buffers.length - 1, Math.floor(this.morph));
        var a = seg, b = Math.min(this.buffers.length - 1, seg + 1);
        var localMorph = this.buffers.length > 1 ? this.morph - seg : 0;

        var spin  = reduceMotion ? 0.6 : t * 0.11;
        var scroll = this.scroll;

        var dissolve, camZ, rx, ry;
        if (this.isImage) {
            // A picture must stay a picture: no spin, only a parallax lean to
            // the pointer and a slow sway. It ASSEMBLES on arrival — the first
            // 1.6s run the dissolve backwards from a wide cloud to the print —
            // and, in hero mode only, dissolves and recedes again on scroll.
            var e2 = this.mode === 'hero' ? scroll * scroll : 0;
            if (!this.born) this.born = t || 0.0001;
            var age = t - this.born;
            var intro = Math.max(0, 1 - age / 1.6);
            intro = intro * intro * intro;
            dissolve = Math.max(e2 * 2.4, intro * 2.6);
            camZ = -(6.1 + e2 * 3.0);
            ry = this.px * 0.22 + Math.sin(t * 0.31) * 0.05 + scroll * 0.4;
            rx = this.py * 0.16 + Math.sin(t * 0.23) * 0.03;
        } else if (this.mode === 'story') {
            // The story stage keeps its object whole — the change being shown is
            // the change of FORM, and a dissolve on top of it reads as noise.
            dissolve = 0.04;
            // A wide, short canvas (the pricing tier strip on a phone can be
            // 700x150) frames the object by its HEIGHT. The perspective matrix
            // is built from a vertical field of view, so the object's on-screen
            // size already tracks canvas height — pushing the camera back per
            // aspect (the first version of this) shrank it to a dot on tablets.
            // Only a slight pull-back for very wide strips, so the object does
            // not touch the top and bottom edges.
            var aspect = this.w / Math.max(1, this.h);
            camZ = -(5.4 + Math.min(1.2, Math.max(0, aspect - 1.6) * 0.5)) * this.zoom;
            ry = spin + this.px * 0.5 + scroll * 0.7;
            rx = -0.18 + this.py * 0.28;
        } else {
            // The hero object comes apart as the page leaves it, and the camera
            // pulls back at the same time. Scrolling past IS the animation.
            var e = scroll * scroll;
            dissolve = e * 2.6 * this.dissolveScale;
            camZ = -(6.1 + e * 4.2) * this.zoom;
            ry = spin + this.px * 0.55 + scroll * 1.5;
            rx = -0.12 + this.py * 0.3 + scroll * 0.45;
        }

        modelView(this.view, rx, ry, camZ);
        multiply(this.mvp, this.proj, this.view);

        gl.clear(gl.COLOR_BUFFER_BIT);

        // Fade the whole object out as it dissolves, so the last frames are
        // dust rather than a wide grey smear.
        var alpha = Math.max(0, 1 - scroll * (this.mode === 'story'
            ? 0.15
            : 0.85 + (1 - this.dissolveScale) * 0.55));
        if (alpha <= 0.01) return;

        var self = this;
        var tiltX = reduceMotion ? 0.12 : Math.sin(t * 0.23) * (this.isImage ? 0.04 : 0.16);

        function pass(loc, size, glow, drawFn) {
            gl.useProgram(loc.prog);
            self.bindAttribs(loc, a, b);
            gl.uniformMatrix4fv(loc.u.uMVP, false, self.mvp);
            gl.uniform1f(loc.u.uTime, reduceMotion ? 0 : t);
            gl.uniform1f(loc.u.uMorph, localMorph);
            gl.uniform1f(loc.u.uDissolve, dissolve);
            gl.uniform1f(loc.u.uSize, size);
            gl.uniform1f(loc.u.uDpr, self.dpr);
            gl.uniform1f(loc.u.uReveal, self.reveal);
            gl.uniform1f(loc.u.uTiltX, tiltX);
            gl.uniform1f(loc.u.uAlpha, alpha);
            if (loc.u.uGlow) gl.uniform1f(loc.u.uGlow, glow);
            if (loc.u.uInk)  gl.uniform1f(loc.u.uInk, self.ink ? 1 : 0);
            gl.uniform3fv(loc.u.uColA, self.colA);
            gl.uniform3fv(loc.u.uColB, self.colB);
            gl.uniform3fv(loc.u.uColHot, self.colHot);
            drawFn();
        }

        function drawLines()  { gl.bindBuffer(gl.ELEMENT_ARRAY_BUFFER, self.idxBuf); gl.drawElements(gl.LINES, self.lineCount, gl.UNSIGNED_SHORT, 0); }
        function drawPoints() { gl.drawArrays(gl.POINTS, 0, self.count); }

        // Order is bottom-up: bloom under everything, strands, then the sharp
        // points on top so the light sits on the wire.
        // Ink dots are smaller and crisper than light points.
        pass(this.progPoint, this.ink ? 30 : 44, 1, drawPoints);
        pass(this.progLine,  1,  0, drawLines);
        pass(this.progPoint, this.ink ? 12 : 17, 0, drawPoints);

        // First real frame: tell CSS the canvas is live, so any printed
        // stand-in behind it (the hero's fallback <img>) can step aside.
        if (!this.live) {
            this.live = true;
            if (this.host) this.host.classList.add('is-gl-live');
        }
    };

    /* ------------------------------------------------------------------ *
     * Page wiring — one loop, one pointer listener, one observer.
     * ------------------------------------------------------------------ */

    var instances = [];
    var running = false;
    var start = 0;

    function frame(now) {
        if (!start) start = now;
        var t = (now - start) / 1000;

        var any = false;
        for (var i = 0; i < instances.length; i++) {
            var inst = instances[i];
            if (!inst.visible && inst.reveal < 0.01) continue;
            inst.updateScroll();
            inst.draw(t);
            any = true;
        }

        if (any && !reduceMotion) {
            requestAnimationFrame(frame);
        } else if (!reduceMotion) {
            running = false;
        }
    }

    function kick() {
        if (running || reduceMotion) return;
        running = true;
        requestAnimationFrame(frame);
    }

    /** Progress of the host element through the viewport, clamped to 0..1. */
    Instance.prototype.updateScroll = function () {
        // Story-mode instances driven by .st-step (the flow stage) and the
        // small still objects (pricing tiers) do not need a per-frame rect at
        // all: their scroll input is either the step attribute or nothing.
        if (this.mode === 'story' && (this.stepDriven || this.buffers.length === 1)) {
            this.scroll = 0;
            return;
        }
        var host = this.host || this.canvas;
        var rect = host.getBoundingClientRect();
        var span = rect.height || 1;

        if (this.mode === 'story') {
            // Sticky sections are taller than the viewport: progress is how far
            // the section's top has travelled past the top of the screen.
            var travel = span - window.innerHeight;
            this.scroll = travel > 0
                ? Math.min(1, Math.max(0, -rect.top / travel))
                : 0;
            if (this.stepDriven) return;                 // .st-step wins if present
            this.targetMorph = this.scroll * (this.buffers.length - 1);
        } else {
            this.scroll = Math.min(1, Math.max(0, -rect.top / span));
            if (this.morphOnScroll) {
                this.targetMorph = this.scroll * (this.buffers.length - 1);
            }
        }
    };

    function init() {
        var canvases = document.querySelectorAll('canvas[data-gl]');
        if (!canvases.length) return;

        for (var i = 0; i < canvases.length; i++) {
            var canvas = canvases[i];
            var host = canvas.closest('[data-gl-host]') || canvas.parentElement;
            var inst = null;

            try {
                inst = Instance.call(Object.create(Instance.prototype), canvas);
            } catch (err) {
                inst = null;
            }

            if (!inst) {
                // No WebGL2, or a shader that would not compile on this driver.
                // CSS takes over: .is-gl-off shows the flat gradient the hero was
                // designed to survive on, and the copy is untouched either way.
                if (host) host.classList.add('is-gl-off');
                continue;
            }

            if (host) host.classList.add('is-gl-on');
            instances.push(inst);

            /* A story stage prefers the ACTIVE STEP over raw scroll: js/scroll.js
               already decides which step is being read, and matching the object
               to the text beats matching it to a pixel offset. */
            var scroller = canvas.closest('.st-scroller');
            if (inst.mode === 'story' && scroller) {
                var stage = scroller.querySelector('.st-stage');
                if (stage) {
                    inst.stepDriven = true;
                    var steps = scroller.querySelectorAll('.st-step').length || 1;
                    var last = Math.max(1, steps - 1);
                    new MutationObserver(function (inst2, stage2, last2) {
                        return function () {
                            var idx = parseInt(stage2.getAttribute('data-st-step') || '0', 10) || 0;
                            inst2.targetMorph = (idx / last2) * (inst2.buffers.length - 1);
                            kick();
                        };
                    }(inst, stage, last)).observe(stage, { attributes: true, attributeFilter: ['data-st-step'] });
                }
            }
        }

        if (!instances.length) return;

        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                for (var i = 0; i < instances.length; i++) {
                    if (instances[i].canvas === entry.target) {
                        instances[i].visible = entry.isIntersecting;
                    }
                }
            });
            kick();
        }, { rootMargin: '120px' });

        instances.forEach(function (inst) { io.observe(inst.canvas); });

        var ro = ('ResizeObserver' in window)
            ? new ResizeObserver(function () {
                instances.forEach(function (inst) { inst.resize(); });
                kick();
            })
            : null;
        if (ro) instances.forEach(function (inst) { ro.observe(inst.canvas); });
        else window.addEventListener('resize', function () {
            instances.forEach(function (inst) { inst.resize(); });
            kick();
        });

        if (!coarse) {
            window.addEventListener('pointermove', function (e) {
                var x = (e.clientX / window.innerWidth) * 2 - 1;
                var y = (e.clientY / window.innerHeight) * 2 - 1;
                instances.forEach(function (inst) { inst.tpx = x; inst.tpy = y; });
                kick();
            }, { passive: true });
        }

        window.addEventListener('scroll', kick, { passive: true });

        if (reduceMotion) {
            // One composed frame, then nothing. The object is present and still.
            instances.forEach(function (inst) {
                inst.visible = true; inst.reveal = 1;
                inst.updateScroll(); inst.draw(0);
            });
            return;
        }

        kick();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
