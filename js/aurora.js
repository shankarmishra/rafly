/**
 * aurora.js — the hero's mesh-gradient ground.
 *
 * Four drifting colour blobs, a travelling scan band, fine scanlines and a
 * little grain, all in one fragment shader over one full-screen triangle.
 *
 * WHY THIS IS NOT three.js. The prototype drew it with a THREE.ShaderMaterial
 * on a PlaneGeometry, which works and costs 365 KB of library to do what 120
 * lines of raw WebGL2 do here: compile two shaders, set two uniforms, draw
 * three vertices. three.js earns its size when there is a scene graph, a
 * camera, materials and lights. There is none of that in a full-screen quad.
 *
 * ONE TRIANGLE, NOT TWO. A single oversized triangle covering the viewport
 * has no diagonal seam down the middle, so the GPU never rasterises the same
 * pixel twice and the interpolation across the screen is continuous. The
 * vertex shader generates it from gl_VertexID, so there is no vertex buffer
 * at all.
 *
 * THE FALLBACK IS THE SAME DESIGN, FROZEN. When this cannot run, CSS paints
 * four radial-gradients at the blobs' t=0 positions (css/09-scenes.css). That
 * is deliberate: a visitor without WebGL2 should see the hero as designed and
 * still, not a different hero. Nothing here carries meaning; it is the room's
 * lighting.
 */

const VERT = `#version 300 es
// Three vertices, no buffer: (-1,-1), (3,-1), (-1,3) covers the viewport.
out vec2 v;
void main() {
    vec2 p = vec2((gl_VertexID << 1) & 2, gl_VertexID & 2);
    v = p;
    gl_Position = vec4(p * 2.0 - 1.0, 0.0, 1.0);
}`;

const FRAG = `#version 300 es
precision highp float;
in vec2 v;
out vec4 outColor;
uniform float uT;
uniform vec2  uR;
uniform vec3  uPaper;

float hash(vec2 p) { return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453); }
float blob(vec2 uv, vec2 c, float r) { return smoothstep(r, 0.0, distance(uv, c)); }

void main() {
    vec2  uv = v;
    float a  = uR.x / uR.y;
    uv.x *= a;
    float t = uT * 0.055;

    // The ground is the page's own paper token, passed in rather than
    // hardcoded, so the hero cannot drift away from the rest of the site.
    vec3 col = uPaper;

    col = mix(col, vec3(0.04, 0.39, 1.00), blob(uv, vec2(0.30 * a + sin(t * 1.3) * 0.16, 0.70 + cos(t * 1.1) * 0.13), 0.58) * 0.28);
    col = mix(col, vec3(0.01, 0.19, 0.78), blob(uv, vec2(0.78 * a + cos(t * 0.9) * 0.15, 0.34 + sin(t * 1.4) * 0.15), 0.52) * 0.24);
    col = mix(col, vec3(0.36, 0.62, 1.00), blob(uv, vec2(0.55 * a + sin(t * 0.7) * 0.22, 0.52 + cos(t * 0.8) * 0.18), 0.44) * 0.20);
    col = mix(col, vec3(0.00, 0.10, 0.48), blob(uv, vec2(0.14 * a + cos(t * 1.6) * 0.10, 0.18 + sin(t * 0.9) * 0.10), 0.38) * 0.13);

    // A slow scan band crossing the hero. It is the one thing here that
    // reads as instrumentation rather than as weather.
    float band = smoothstep(0.055, 0.0, abs(fract(v.y * 0.5 - uT * 0.045) - 0.5));
    col -= band * 0.020;

    // Scanlines and grain, both small enough to be felt and not seen. They
    // darken by at most 0.026 of a unit, which is why the headline over this
    // still measures where css/00-tokens.css says it does.
    col -= (sin(v.y * uR.y * 1.6) * 0.5 + 0.5) * 0.006;
    col += (hash(v * uR + t) - 0.5) * 0.020;

    outColor = vec4(col, 1.0);
}`;

function compile(gl, type, src) {
    const s = gl.createShader(type);
    gl.shaderSource(s, src);
    gl.compileShader(s);
    if (!gl.getShaderParameter(s, gl.COMPILE_STATUS)) {
        gl.deleteShader(s);
        return null;
    }
    return s;
}

/**
 * @param {HTMLCanvasElement} canvas
 * @param {HTMLElement} host   the element whose box the shader fills
 * @param {[number,number,number]} paper  page ground, 0-255
 * @returns {() => void} destroy
 */
export function initAurora(canvas, host, paper) {
    const gl = canvas.getContext('webgl2', { antialias: false, alpha: false, depth: false });
    if (!gl) return null;

    const vs = compile(gl, gl.VERTEX_SHADER, VERT);
    const fs = compile(gl, gl.FRAGMENT_SHADER, FRAG);
    if (!vs || !fs) return null;

    const prog = gl.createProgram();
    gl.attachShader(prog, vs);
    gl.attachShader(prog, fs);
    gl.linkProgram(prog);
    if (!gl.getProgramParameter(prog, gl.LINK_STATUS)) return null;

    gl.useProgram(prog);
    const uT = gl.getUniformLocation(prog, 'uT');
    const uR = gl.getUniformLocation(prog, 'uR');
    gl.uniform3f(gl.getUniformLocation(prog, 'uPaper'), paper[0] / 255, paper[1] / 255, paper[2] / 255);

    let raf = 0, running = false, lost = false;
    const t0 = performance.now();

    function resize() {
        const dpr = Math.min(window.devicePixelRatio || 1, 1.5);
        const w = Math.max(1, Math.round(host.clientWidth * dpr));
        const h = Math.max(1, Math.round(host.clientHeight * dpr));
        if (canvas.width !== w || canvas.height !== h) {
            canvas.width = w;
            canvas.height = h;
            gl.viewport(0, 0, w, h);
        }
        gl.uniform2f(uR, w, h);
    }

    function frame() {
        if (lost) return;
        resize();
        gl.uniform1f(uT, (performance.now() - t0) / 1000);
        gl.drawArrays(gl.TRIANGLES, 0, 3);
        raf = requestAnimationFrame(frame);
    }

    const start = () => { if (!running && !lost) { running = true; raf = requestAnimationFrame(frame); } };
    const stop = () => { running = false; cancelAnimationFrame(raf); };

    /* A lost context is normal, not exceptional: the OS reclaims the GPU on
       sleep, on a driver update, on another tab going fullscreen. Mark it
       lost and let the CSS fallback underneath show through, exactly as it
       does for a visitor who never had WebGL2 at all. */
    const onLost = (e) => { e.preventDefault(); lost = true; stop(); host.classList.remove('is-live'); };
    const onVisibility = () => { document.hidden ? stop() : start(); };
    const onResize = () => resize();

    canvas.addEventListener('webglcontextlost', onLost);
    document.addEventListener('visibilitychange', onVisibility);
    window.addEventListener('resize', onResize, { passive: true });

    resize();
    start();
    host.classList.add('is-live');

    return function destroy() {
        stop();
        canvas.removeEventListener('webglcontextlost', onLost);
        document.removeEventListener('visibilitychange', onVisibility);
        window.removeEventListener('resize', onResize);
        host.classList.remove('is-live');
    };
}
