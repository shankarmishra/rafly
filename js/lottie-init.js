/**
 * RAFly Self-Hosted Bulletproof Lottie Animation Initializer
 * Automatically converts all <lottie-player> and <dotlottie-player> tags
 * into native, high-performance inline SVG animations.
 */
(function() {
    'use strict';

    var io = ('IntersectionObserver' in window)
        ? new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                var anim = entry.target._lottieInstance;
                if (!anim) return;
                if (entry.isIntersecting) {
                    anim.play();
                } else {
                    anim.pause();
                }
            });
        }, { threshold: 0.05 })
        : null;

    function initLotties() {
        if (typeof window.lottie === 'undefined') {
            console.warn('[RAFly Motion] Lottie engine not loaded yet. Retrying...');
            setTimeout(initLotties, 100);
            return;
        }

        const selectors = 'lottie-player, dotlottie-player, [data-lottie-src], [data-path]';
        const elements = document.querySelectorAll(selectors);

        elements.forEach(function(el) {
            if (el.dataset.lottieDone) return;
            el.dataset.lottieDone = 'true';

            const src = el.getAttribute('src') || el.getAttribute('data-lottie-src') || el.getAttribute('data-path');
            if (!src) return;

            // Ensure container display and layout geometry
            el.style.display = 'block';
            if (!el.style.height && el.offsetHeight === 0) {
                el.style.height = '100%';
            }

            // Create inner container for SVG rendering
            const renderBox = document.createElement('div');
            renderBox.className = 'rafly-lottie-svg-box';
            renderBox.style.width = '100%';
            renderBox.style.height = '100%';
            renderBox.style.display = 'flex';
            renderBox.style.justifyContent = 'center';
            renderBox.style.alignItems = 'center';

            // Clear element shadow/inner DOM and append SVG render box
            el.innerHTML = '';
            el.appendChild(renderBox);

            // Fetch animation JSON with fallback path resolution & render native SVG
            const tryFetch = function(p) {
                const normPath = p.startsWith('/') ? p : '/' + p;
                const relPath = p.startsWith('/') ? p.slice(1) : p;
                return fetch(normPath)
                    .then(function(res) {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .catch(function() {
                        return fetch(relPath).then(function(res) {
                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            return res.json();
                        });
                    });
            };

            tryFetch(src)
                .then(function(animationData) {
                    const anim = window.lottie.loadAnimation({
                        container: renderBox,
                        renderer: 'svg',
                        loop: true,
                        autoplay: true,
                        animationData: animationData,
                        rendererSettings: {
                            progressiveLoad: true,
                            preserveAspectRatio: 'xMidYMid meet'
                        }
                    });

                    renderBox._lottieInstance = anim;
                    if (io) io.observe(renderBox);
                })
                .catch(function(err) {
                    console.error('[RAFly Motion] Failed to load Lottie JSON:', src, err);
                });
        });
    }

    if (document.readyState === 'interactive' || document.readyState === 'complete') {
        initLotties();
    } else {
        document.addEventListener('DOMContentLoaded', initLotties);
    }
})();
