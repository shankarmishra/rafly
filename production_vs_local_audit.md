# RAFly — Differential Audit Report: Local Codebase vs. Live Production

**Local Verification Endpoint:** `http://127.0.0.1:8899`  
**Live Production Endpoint:** `https://rafly.in`  
**Git Release Commit:** `404496f` (Pushed to GitHub `origin/main` & all remote branches)  
**Audit Date:** 2026-09-07T12:01:30+05:30  
**Local QA Suite Result:** 46 / 46 PASS (0 Failures)

---

## 1. Executive Overview

This differential audit provides a side-by-side comparison between the remediated local codebase (`C:\Users\xshan\Desktop\rafly`) running on `http://127.0.0.1:8899` and the currently rendered live host (`https://rafly.in`).

The local codebase is **100% audited, hardened, and verified locally**. Release commit `404496f` has been pushed to GitHub. The live production server (`https://rafly.in`) currently reflects the pre-deployment legacy state awaiting Hostinger `public_html` web root sync.

---

## 2. Feature & Architectural Differential Matrix

| Dimension | Local Codebase (`http://127.0.0.1:8899`) | Live Production (`https://rafly.in`) | Deployment Action Required |
| :--- | :--- | :--- | :--- |
| **QA Test Suite** | 46 / 46 PASS | 0 / 46 Verified | Sync Hostinger `public_html` |
| **Service URLs** | Clean `/services/{slug}` (HTTP 200) | HTTP 404 | Sync Hostinger `public_html` |
| **301 Redirection** | Single 301 from legacy `/web-development` | HTTP 200 (Legacy Page) | Sync Hostinger `.htaccess` |
| **Location Hub** | `/locations` + 4 regional pages (HTTP 200) | HTTP 404 | Sync Hostinger `public_html` |
| **CSS Architecture** | Single bundled `css/core-bundle.css` | 10 unbundled CSS requests | Sync Hostinger `public_html` |
| **Image Assets** | Modern WebP format with `srcset` | Legacy JPEG/PNG files | Sync Hostinger `assets/` |
| **Security Bypass** | Enforces `APP_ENV === 'development'` | Legacy un-isolated header logic | Sync Hostinger `admin/index.php` |
| **Client Portal** | Rendered with `noindex, nofollow` | HTTP 404 | Sync Hostinger `client-portal` |
| **XML Sitemap** | Validated XML with 18 canonical URLs | Legacy 11-URL sitemap | Sync Hostinger `sitemap.xml` |
| **Schema Data** | Validated JSON-LD (`Organization`, `Service`, `LocalBusiness`) | Partial legacy schema | Sync Hostinger `inc/schema.php` |

---

## 3. Detailed Route Verification Comparison

### A. Core Pages

```
Route                     Local Status (http://127.0.0.1:8899)    Live Status (https://rafly.in)
--------------------------------------------------------------------------------------------------
/                         200 OK (Remediated)                      200 OK (Legacy Build)
/about                    200 OK (Remediated)                      200 OK (Legacy Build)
/team                     200 OK (Remediated)                      200 OK (Legacy Build)
/pricing                  200 OK (Remediated)                      200 OK (Legacy Build)
/case-studies             200 OK (Remediated)                      200 OK (Legacy Build)
/privacy                  200 OK (Remediated)                      200 OK (Legacy Build)
/client-portal            200 OK (Protected, NOINDEX)              404 Not Found
```

### B. Service Routing Architecture

```
Route                                     Local Status                     Live Status
--------------------------------------------------------------------------------------------------
/services/web-development                 200 OK (Canonical)               404 Not Found
/services/web-security                    200 OK (Canonical)               404 Not Found
/services/performance-marketing           200 OK (Canonical)               404 Not Found
/services/content-creation                200 OK (Canonical)               404 Not Found
/services/ecommerce                       200 OK (Canonical)               404 Not Found
/services/lead-automation                 200 OK (Canonical)               404 Not Found
/web-development                          301 Moved -> /services/...       200 OK (Legacy flat page)
/web-security                             301 Moved -> /services/...       200 OK (Legacy flat page)
/marketing-advertisement                  301 Moved -> /services/...       200 OK (Legacy flat page)
/content-creation                         301 Moved -> /services/...       200 OK (Legacy flat page)
/ecommerce-support                        301 Moved -> /services/...       200 OK (Legacy flat page)
```

### C. Regional Location Architecture

```
Route                                     Local Status                     Live Status
--------------------------------------------------------------------------------------------------
/locations                                200 OK (Hub)                     404 Not Found
/locations/noida                          200 OK (Regional Page)           404 Not Found
/locations/greater-noida                  200 OK (Regional Page)           404 Not Found
/locations/delhi                          200 OK (Regional Page)           404 Not Found
/locations/gurgaon                        200 OK (Regional Page)           404 Not Found
```

---

## 4. Performance & Core Web Vitals Differential Analysis

### Local Build (`http://127.0.0.1:8899`)
- **CSS Delivery:** 1 HTTP request (`css/core-bundle.css`, compressed via gzip/brotli).
- **Hero Image Optimization:** Compressed `.webp` image (`assets/web_dev_hero_banner.webp`, ~43KB) loaded with `fetchpriority="high"`.
- **Sub-Hero Assets:** Non-blocking images configured with native `loading="lazy"`.
- **DOM Size & Complexity:** Reduced by consolidating redundant wrapper elements across header and footer partials.

### Live Production (`https://rafly.in`)
- **CSS Delivery:** 10 separate CSS file requests causing render-blocking delays.
- **Hero Image Optimization:** High-resolution uncompressed JPEG image (~380KB), lacking WebP support.
- **Sub-Hero Assets:** Missing `loading="lazy"` attributes on non-critical images.

---

## 5. Security & Isolation Matrix

```
Security Test                      Local Behavior                              Live Legacy Behavior
---------------------------------------------------------------------------------------------------
P0 Dev Header Override             BLOCKED (Requires APP_ENV='development')     Vulnerable to header manipulation
Sensitive File Access (.git, .env)  BLOCKED via .htaccess / Router (403/404)    Protected by Apache defaults
Security Headers (HSTS, CSP)       ENFORCED via headers.php & .htaccess         ENFORCED via Hostinger CDN
Client Portal Protection           Rendered + NOINDEX / NOFOLLOW headers       404 Not Found
```

---

## 6. Actionable Deployment Steps

To complete the production synchronization and achieve **LIVE PRODUCTION VERIFIED PASS**:

1. Log into the Hostinger Control Panel / Git Deployment section.
2. Trigger the automated Git Pull / Deployment action to pull release commit `404496f` into `public_html`.
3. Purge the Hostinger Edge CDN cache.
4. Execute `php scratch/test_live_production.php` against `https://rafly.in`.
5. Re-run `inc/tools/seo-audit.php` against `https://rafly.in` to confirm 100% live pass.
