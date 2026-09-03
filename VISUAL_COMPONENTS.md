# RAFly — Master Visual Components & Blueprint System DNA

## Overview
This document specifies the extracted visual component primitives, CSS/JS blueprint patterns, HUD node models, ambient lighting physics, and glassmorphic depth layers that establish 10/10 visual parity between the approved homepage benchmark and all internal public routes.

---

## 1. Global Blueprint Grid Background System (`.head-field-grid`)

### CSS Specifications
```css
.head-field-grid {
    inset: -1px;
    background-image:
        linear-gradient(color-mix(in srgb, var(--accent-fg) 11%, transparent) 1px, transparent 1px),
        linear-gradient(90deg, color-mix(in srgb, var(--accent-fg) 11%, transparent) 1px, transparent 1px),
        radial-gradient(color-mix(in srgb, var(--accent-fg) 30%, transparent) 1.5px, transparent 1.5px);
    background-size: 42px 42px, 42px 42px, 42px 42px;
    background-position: 0 0, 0 0, 0 0;
    mask-image: linear-gradient(180deg, transparent 8%, #000 58%);
    -webkit-mask-image: linear-gradient(180deg, transparent 8%, #000 58%);
}
```

### Visual Rationale
The 42px grid hairline with intersection nodes transforms plain static backgrounds into a physical infrastructure network, mirroring the homepage reactor stage.

---

## 2. Animated Light Sweep & Ambient Orbs (`.head-field-sweep`)

### CSS Specifications
```css
.head-field-sweep {
    top: 0; bottom: 0;
    left: -40%;
    width: 40%;
    background: linear-gradient(90deg,
        transparent,
        color-mix(in srgb, var(--accent-fg) 13%, transparent) 45%,
        color-mix(in srgb, var(--accent-fg) 19%, transparent) 55%,
        transparent);
    filter: blur(22px);
    animation: head-sweep 9s var(--ease-in-out) infinite;
}

.head-field-orb-a {
    width: 380px; height: 380px;
    top: -100px; right: -50px;
    background: radial-gradient(circle, color-mix(in srgb, var(--accent-fg) 16%, transparent) 0%, transparent 70%);
}
```

---

## 3. Glassmorphic Depth Cards (`.card-hover`, `.glass-card`)

### CSS Specifications
```css
.glass-card {
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(10, 99, 255, 0.14);
    border-radius: var(--r-xl);
    box-shadow: inset 0 1px 0 #ffffff, 0 8px 32px rgba(6, 18, 47, 0.06);
    transition: transform 0.35s ease, box-shadow 0.35s ease, border-color 0.35s ease;
}

.glass-card:hover {
    transform: translateY(-4px);
    border-color: rgba(10, 99, 255, 0.32);
    box-shadow: inset 0 1px 0 #ffffff, 0 16px 40px rgba(10, 99, 255, 0.12);
}
```

---

## 4. Service HUD Node Models (`.svc-hud-node`)

### Component Blueprint & Placement
- **Web Development (`/web-development`)**: `[FRONTEND]`, `[API GATEWAY]`, `[DATABASE]`, `[EDGE CDN]`
- **Web Security (`/web-security`)**: `[WAF ACTIVE]`, `[TLS 1.3 SHIELD]`, `[ZERO THREATS]`
- **Marketing (`/marketing-advertisement`)**: `[ROI 3.4X]`, `[CTR +140%]`, `[CONVERSIONS]`
- **Content (`/content-creation`)**: `[STRATEGY]`, `[EDITORIAL]`, `[DISTRIBUTION]`
- **E-Commerce (`/ecommerce-support`)**: `[STOREFRONT]`, `[GATEWAY]`, `[ORDER ENGINE]`

```css
.svc-hud-node {
    position: absolute;
    z-index: 4;
    font-family: var(--font-mono, monospace);
    font-size: 0.625rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: #06122f;
    background: rgba(255, 255, 255, 0.95);
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid rgba(10, 99, 255, 0.22);
    box-shadow: 0 4px 12px rgba(6, 18, 47, 0.08), inset 0 1px 0 #ffffff;
    animation: hudNodeFloat 4s ease-in-out infinite alternate;
}
```

---

## 5. Telemetry Badges & Status Indicators (`.chip-mono`)

```css
.chip-mono {
    font-family: var(--font-mono, monospace);
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.06em;
    color: #0a63ff;
    background: rgba(255, 255, 255, 0.95);
    padding: 3px 8px;
    border-radius: 4px;
    border: 1px solid rgba(10, 99, 255, 0.2);
    box-shadow: inset 0 1px 0 #ffffff, 0 2px 6px rgba(6, 18, 47, 0.04);
}
```
