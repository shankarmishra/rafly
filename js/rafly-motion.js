/**
 * rafly-motion.js — Master Motion & Micro-Interaction Engine for RAFly
 * Provides:
 * 1. Multi-plane atmospheric background (grain drift, blueprint parallax, proximity dot matrix, ghost typography, dust particles, light sweeps).
 * 2. Custom magnetic cursor lerp system with depth multipliers (0.5x, 1x, 1.5x, 2x).
 * 3. Scroll progress choreography system (sectionProgress 0 → 1).
 * 4. Section transition motif bridges.
 * 5. Master easing definitions (cubic-bezier(.16,1,.3,1)).
 */

export class RAFlyMotionEngine {
    constructor() {
        this.mouseX = window.innerWidth / 2;
        this.mouseY = window.innerHeight / 2;
        this.targetMouseX = this.mouseX;
        this.targetMouseY = this.mouseY;
        this.scrollY = window.scrollY;
        this.isReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        
        this.initAtmosphere();
        if (!this.isReducedMotion) {
            this.initCursor();
            this.initScrollChoreography();
            this.startRenderLoop();
        }
    }

    initAtmosphere() {
        // Multi-plane atmosphere initialization
        const body = document.body;
        if (!document.querySelector('.rafly-atmosphere')) {
            const atmos = document.createElement('div');
            atmos.className = 'rafly-atmosphere';
            atmos.setAttribute('aria-hidden', 'true');
            atmos.innerHTML = `
                <div class="atmos-grain"></div>
                <div class="atmos-grid"></div>
                <div class="atmos-dots"></div>
                <div class="atmos-ghost-type">
                    <span class="gt-word w1" style="top:15%; left:5%;">BUILD</span>
                    <span class="gt-word w2" style="top:35%; right:8%;">PROTECT</span>
                    <span class="gt-word w3" style="top:55%; left:12%;">CREATE</span>
                    <span class="gt-word w4" style="top:75%; right:15%;">CONVERT</span>
                    <span class="gt-word w5" style="top:90%; left:8%;">COMPOUND</span>
                </div>
                <canvas class="atmos-dust-canvas" id="dustCanvas"></canvas>
                <div class="atmos-light-sweep" id="atmosSweep"></div>
            `;
            body.appendChild(atmos);
        }

        this.initDustCanvas();
    }

    initDustCanvas() {
        const canvas = document.getElementById('dustCanvas');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;

        let width = canvas.width = window.innerWidth;
        let height = canvas.height = window.innerHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = window.innerWidth;
            height = canvas.height = window.innerHeight;
        }, { passive: true });

        const particles = Array.from({ length: 24 }, () => ({
            x: Math.random() * width,
            y: Math.random() * height,
            radius: Math.random() * 1.5 + 0.5,
            alpha: Math.random() * 0.25 + 0.05,
            speedY: (Math.random() - 0.5) * 0.2,
            speedX: (Math.random() - 0.5) * 0.2
        }));

        this.dustLoop = () => {
            ctx.clearRect(0, 0, width, height);
            particles.forEach(p => {
                p.x += p.speedX;
                p.y += p.speedY;

                if (p.x < 0) p.x = width;
                if (p.x > width) p.x = 0;
                if (p.y < 0) p.y = height;
                if (p.y > height) p.y = 0;

                ctx.beginPath();
                ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                ctx.fillStyle = `rgba(10, 99, 255, ${p.alpha})`;
                ctx.fill();
            });
        };
    }

    initCursor() {
        if (!window.matchMedia('(pointer: fine)').matches) return;

        const cursor = document.createElement('div');
        cursor.className = 'rafly-cursor';
        cursor.innerHTML = '<span class="cursor-dot"></span><span class="cursor-badge"></span>';
        document.body.appendChild(cursor);
        this.cursor = cursor;
        this.cursorBadge = cursor.querySelector('.cursor-badge');

        window.addEventListener('mousemove', (e) => {
            this.targetMouseX = e.clientX;
            this.targetMouseY = e.clientY;
        }, { passive: true });

        // Magnetic interactive pull on elements with data-magnetic
        document.querySelectorAll('[data-magnetic], .btn, button, a').forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor.classList.add('is-hovering');
                const text = el.getAttribute('data-cursor-text');
                if (text && this.cursorBadge) {
                    this.cursorBadge.textContent = text;
                    cursor.classList.add('has-badge');
                }
            });
            el.addEventListener('mouseleave', () => {
                cursor.classList.remove('is-hovering', 'has-badge');
            });
        });
    }

    initScrollChoreography() {
        const sections = document.querySelectorAll('section[id]');
        
        const scrollObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-section-active');
                    this.triggerLightSweep();
                }
            });
        }, { threshold: 0.15 });

        sections.forEach(sec => scrollObserver.observe(sec));
    }

    triggerLightSweep() {
        const sweep = document.getElementById('atmosSweep');
        if (!sweep) return;
        sweep.classList.remove('is-active');
        void sweep.offsetWidth; // Reflow
        sweep.classList.add('is-active');
    }

    startRenderLoop() {
        const render = () => {
            // Lerp mouse coordinates
            this.mouseX += (this.targetMouseX - this.mouseX) * 0.1;
            this.mouseY += (this.targetMouseY - this.mouseY) * 0.1;

            // Render custom cursor
            if (this.cursor) {
                this.cursor.style.transform = `translate3d(${this.mouseX}px, ${this.mouseY}px, 0)`;
            }

            // Parallax movement on atmospheric layers
            const grid = document.querySelector('.atmos-grid');
            if (grid) {
                const offsetX = (this.mouseX - window.innerWidth / 2) * 0.015;
                const offsetY = (this.mouseY - window.innerHeight / 2) * 0.015;
                grid.style.transform = `translate3d(${offsetX}px, ${offsetY}px, 0)`;
            }

            // Render dust canvas
            if (this.dustLoop) this.dustLoop();

            requestAnimationFrame(render);
        };

        requestAnimationFrame(render);
    }
}

export function initRAFlyMotion() {
    return new RAFlyMotionEngine();
}
