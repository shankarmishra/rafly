/**
 * hero-glass-core.js — Three.js Interactive Digital Infrastructure Glass Core
 *
 * Renders the central floating RAFLY glass platform:
 * 1. Transparent glass top slab
 * 2. 3-4 stacked blue glass layers
 * 3. RAFLY logo emblem on top
 * 4. Circular transparent base
 * 5. Illuminated blue ring
 * 6. Orbital rings
 * 7. Subtle particles
 * 8. Mouse-following 3D tilt & smooth floating motion
 */

export function initHeroGlassCore(container) {
    if (!container || typeof THREE === 'undefined') return;

    // Detect reduced motion preference
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Setup Scene, Camera, Renderer
    const scene = new THREE.Scene();

    const width = container.clientWidth || 540;
    const height = container.clientHeight || 540;

    const camera = new THREE.PerspectiveCamera(42, width / height, 0.1, 1000);
    camera.position.set(0, 0, 7.5);

    const renderer = new THREE.WebGLRenderer({
        alpha: true,
        antialias: true,
        powerPreference: 'high-performance'
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setSize(width, height);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;

    container.appendChild(renderer.domElement);

    // Root Group
    const rootGroup = new THREE.Group();
    scene.add(rootGroup);

    // Core Assembly Group
    const coreGroup = new THREE.Group();
    rootGroup.add(coreGroup);

    // ---------------------------------------------------- Lighting System
    const ambientLight = new THREE.AmbientLight(0xf6f8fc, 1.2);
    scene.add(ambientLight);

    const keyLight = new THREE.DirectionalLight(0xffffff, 2.5);
    keyLight.position.set(5, 8, 5);
    scene.add(keyLight);

    const bluePointLight = new THREE.PointLight(0x0a63ff, 4.0, 15);
    bluePointLight.position.set(-2, 1, 3);
    scene.add(bluePointLight);

    const cyanPointLight = new THREE.PointLight(0x00aeef, 3.5, 12);
    cyanPointLight.position.set(2, -2, 2);
    scene.add(cyanPointLight);

    const edgeRimLight = new THREE.DirectionalLight(0x17c8ff, 1.8);
    edgeRimLight.position.set(-4, -4, -2);
    scene.add(edgeRimLight);

    // ---------------------------------------------------- Materials
    // Glass Top Slab Material
    const topGlassMaterial = new THREE.MeshPhysicalMaterial({
        color: 0xffffff,
        metalness: 0.1,
        roughness: 0.1,
        transmission: 0.92,
        transparent: true,
        opacity: 0.88,
        ior: 1.5,
        reflectivity: 0.9,
        clearcoat: 1.0,
        clearcoatRoughness: 0.05
    });

    // Translucent Blue Glass Layer Materials
    const blueGlassMat1 = new THREE.MeshPhysicalMaterial({
        color: 0x0a63ff,
        metalness: 0.2,
        roughness: 0.15,
        transmission: 0.65,
        transparent: true,
        opacity: 0.7,
        clearcoat: 0.8
    });

    const blueGlassMat2 = new THREE.MeshPhysicalMaterial({
        color: 0x00aeef,
        metalness: 0.1,
        roughness: 0.2,
        transmission: 0.75,
        transparent: true,
        opacity: 0.6,
        clearcoat: 0.6
    });

    const blueGlassMat3 = new THREE.MeshPhysicalMaterial({
        color: 0x050f33,
        metalness: 0.4,
        roughness: 0.25,
        transmission: 0.4,
        transparent: true,
        opacity: 0.85
    });

    // Emissive Ring Material
    const emissiveRingMat = new THREE.MeshBasicMaterial({
        color: 0x00aeef,
        transparent: true,
        opacity: 0.9
    });

    // Metallic Orbital Ring Material
    const orbitalRingMat = new THREE.MeshStandardMaterial({
        color: 0x0a63ff,
        metalness: 0.8,
        roughness: 0.2,
        transparent: true,
        opacity: 0.65
    });

    // Base Plate Material
    const basePlateMat = new THREE.MeshPhysicalMaterial({
        color: 0xeef2fa,
        metalness: 0.2,
        roughness: 0.3,
        transmission: 0.8,
        transparent: true,
        opacity: 0.5
    });

    // ---------------------------------------------------- Geometries & Assembly

    // 1. Circular Transparent Base
    const baseGeo = new THREE.CylinderGeometry(2.4, 2.5, 0.08, 64);
    const baseMesh = new THREE.Mesh(baseGeo, basePlateMat);
    baseMesh.position.y = -1.2;
    coreGroup.add(baseMesh);

    // 2. Illuminated Blue Glow Ring Base
    const glowRingGeo = new THREE.TorusGeometry(2.2, 0.03, 16, 100);
    const glowRingMesh = new THREE.Mesh(glowRingGeo, emissiveRingMat);
    glowRingMesh.rotation.x = Math.PI / 2;
    glowRingMesh.position.y = -1.14;
    coreGroup.add(glowRingMesh);

    // 3. Stacked 4 Translucent Glass Layers (Building up the RAFLY Platform)
    const layerGeom1 = new THREE.CylinderGeometry(1.9, 2.0, 0.16, 64);
    const layerMesh1 = new THREE.Mesh(layerGeom1, blueGlassMat3);
    layerMesh1.position.y = -0.8;
    coreGroup.add(layerMesh1);

    const layerGeom2 = new THREE.CylinderGeometry(1.7, 1.8, 0.16, 64);
    const layerMesh2 = new THREE.Mesh(layerGeom2, blueGlassMat1);
    layerMesh2.position.y = -0.5;
    coreGroup.add(layerMesh2);

    const layerGeom3 = new THREE.CylinderGeometry(1.5, 1.6, 0.16, 64);
    const layerMesh3 = new THREE.Mesh(layerGeom3, blueGlassMat2);
    layerMesh3.position.y = -0.2;
    coreGroup.add(layerMesh3);

    // 4. Transparent Glass Top Slab
    const topSlabGeo = new THREE.CylinderGeometry(1.35, 1.4, 0.22, 64);
    const topSlabMesh = new THREE.Mesh(topSlabGeo, topGlassMaterial);
    topSlabMesh.position.y = 0.15;
    coreGroup.add(topSlabMesh);

    // 5. RAFLY Emblem / Logo Core Object on Top
    const emblemGroup = new THREE.Group();
    emblemGroup.position.y = 0.5;

    // Central Prism / Diamond Logo Representation
    const prismGeo = new THREE.OctahedronGeometry(0.55, 0);
    const prismMat = new THREE.MeshPhysicalMaterial({
        color: 0x0a63ff,
        metalness: 0.3,
        roughness: 0.1,
        transmission: 0.8,
        transparent: true,
        opacity: 0.9,
        clearcoat: 1.0
    });
    const prismMesh = new THREE.Mesh(prismGeo, prismMat);
    emblemGroup.add(prismMesh);

    // Inner Glowing Core
    const innerCoreGeo = new THREE.IcosahedronGeometry(0.3, 2);
    const innerCoreMat = new THREE.MeshBasicMaterial({
        color: 0x00aeef,
        wireframe: true
    });
    const innerCoreMesh = new THREE.Mesh(innerCoreGeo, innerCoreMat);
    emblemGroup.add(innerCoreMesh);

    coreGroup.add(emblemGroup);

    // 6. Independent Orbital Rings
    const orbitRing1Geo = new THREE.TorusGeometry(2.3, 0.018, 16, 100);
    const orbitRing1 = new THREE.Mesh(orbitRing1Geo, orbitalRingMat);
    orbitRing1.rotation.x = Math.PI / 3;
    orbitRing1.rotation.y = Math.PI / 6;
    coreGroup.add(orbitRing1);

    const orbitRing2Geo = new THREE.TorusGeometry(2.7, 0.015, 16, 100);
    const orbitRing2 = new THREE.Mesh(orbitRing2Geo, orbitalRingMat);
    orbitRing2.rotation.x = -Math.PI / 4;
    orbitRing2.rotation.y = -Math.PI / 3;
    coreGroup.add(orbitRing2);

    // 7. Ambient Micro Particle Field
    const particleCount = 140;
    const particleGeo = new THREE.BufferGeometry();
    const particlePositions = new Float32Array(particleCount * 3);

    for (let i = 0; i < particleCount * 3; i += 3) {
        particlePositions[i] = (Math.random() - 0.5) * 8.0;
        particlePositions[i + 1] = (Math.random() - 0.5) * 6.0;
        particlePositions[i + 2] = (Math.random() - 0.5) * 6.0;
    }

    particleGeo.setAttribute('position', new THREE.BufferAttribute(particlePositions, 3));

    const particleMat = new THREE.PointsMaterial({
        color: 0x00aeef,
        size: 0.045,
        transparent: true,
        opacity: 0.65,
        blending: THREE.AdditiveBlending
    });

    const particleSystem = new THREE.Points(particleGeo, particleMat);
    rootGroup.add(particleSystem);

    // Initial Group Tilts
    rootGroup.rotation.x = 0.35;
    rootGroup.rotation.y = -0.25;

    // ---------------------------------------------------- Pointer Interaction
    let targetRotX = 0.35;
    let targetRotY = -0.25;
    let currentRotX = 0.35;
    let currentRotY = -0.25;

    const handlePointerMove = (e) => {
        const rect = container.getBoundingClientRect();
        const x = (e.clientX - rect.left) / rect.width - 0.5;
        const y = (e.clientY - rect.top) / rect.height - 0.5;
        targetRotY = -0.25 + x * 0.45;
        targetRotX = 0.35 + y * 0.35;
    };

    window.addEventListener('pointermove', handlePointerMove, { passive: true });

    // ---------------------------------------------------- Animation Loop
    let clock = new THREE.Clock();
    let animFrameId = null;
    let isVisible = true;

    const animate = () => {
        if (!isVisible) return;
        animFrameId = requestAnimationFrame(animate);

        const elapsedTime = clock.getElapsedTime();

        if (!prefersReducedMotion) {
            // Floating & subtle rotations
            coreGroup.position.y = Math.sin(elapsedTime * 1.2) * 0.12;
            emblemGroup.rotation.y = elapsedTime * 0.5;
            innerCoreMesh.rotation.x = elapsedTime * 0.8;

            orbitRing1.rotation.z = elapsedTime * 0.25;
            orbitRing2.rotation.z = -elapsedTime * 0.2;

            particleSystem.rotation.y = elapsedTime * 0.04;

            // Pulse point lights
            cyanPointLight.intensity = 3.5 + Math.sin(elapsedTime * 2.0) * 0.8;
            bluePointLight.intensity = 4.0 + Math.cos(elapsedTime * 1.5) * 0.6;
        }

        // Smooth Mouse Tilt Interpolation
        currentRotX += (targetRotX - currentRotX) * 0.05;
        currentRotY += (targetRotY - currentRotY) * 0.05;
        rootGroup.rotation.x = currentRotX;
        rootGroup.rotation.y = currentRotY;

        renderer.render(scene, camera);
    };

    // IntersectionObserver to pause rendering when offscreen
    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                isVisible = entry.isIntersecting;
                if (isVisible && !animFrameId) {
                    clock.start();
                    animate();
                }
            });
        },
        { threshold: 0.1 }
    );
    observer.observe(container);

    // Handle Resize
    const handleResize = () => {
        if (!container) return;
        const w = container.clientWidth || 540;
        const h = container.clientHeight || 540;
        camera.aspect = w / h;
        camera.updateProjectionMatrix();
        renderer.setSize(w, h);
    };

    window.addEventListener('resize', handleResize, { passive: true });

    // Start loop
    animate();

    return () => {
        observer.disconnect();
        window.removeEventListener('pointermove', handlePointerMove);
        window.removeEventListener('resize', handleResize);
        if (animFrameId) cancelAnimationFrame(animFrameId);
        renderer.dispose();
    };
}
