# RAFly — Production Inventory & Baseline Audit Report (Pre-Deployment Sync)

**Target Production URL:** `https://rafly.in`  
**Hosting Environment:** Hostinger Edge CDN + PHP 8.3  
**Audit Timestamp:** 2026-09-07T12:01:30+05:30  
**Baseline Git Commit (Pre-Deploy Backup):** `8ee5da1aab30c20951f74c69c78bd2ba7cf41888`  
**Git Backup Tag:** `production-backup-before-deploy-2026-09-07`  
**New Deployment Release Commit:** `404496f` (Pushed to all remote branches)

---

## 1. Executive Summary

This inventory report establishes the empirical baseline state of the live production environment (`https://rafly.in`) immediately prior to triggering the Hostinger web root (`public_html`) synchronization for release commit `404496f`.

A complete pre-deployment backup tag (`production-backup-before-deploy-2026-09-07`) has been verified on the remote repository.

---

## 2. Infrastructure & Hosting Environment Verification

| Parameter | Observed Live Value | Status / Assessment |
| :--- | :--- | :--- |
| **Domain / Protocol** | `https://rafly.in` | PASS — Active TLS/SSL Certificate |
| **HTTP -> HTTPS Redirect** | HTTP 301 to HTTPS | PASS — Force HTTPS Active |
| **Edge CDN / Reverse Proxy** | Hostinger Edge CDN (`x-hcdn-request-id`) | PASS — Active Edge Caching |
| **HTTP/3 Support** | `alt-svc: h3=":443"` | PASS — HTTP/3 Enabled |
| **PHP Runtime** | PHP 8.3.33 | PASS — Compatible with Local Architecture |
| **HSTS Header** | `Strict-Transport-Security: max-age=31536000; includeSubDomains` | PASS — Strong HSTS Enforced |
| **Cookie Flags** | `PHPSESSID`: `HttpOnly; Secure; SameSite=Strict` | PASS — Secure Cookie Policy |

---

## 3. Live Route Inventory & Baseline Response Matrix (Pre-Sync)

### A. Main Pages (Legacy Live Deployment)
- `https://rafly.in/` — **HTTP 200** (Legacy home page)
- `https://rafly.in/about` — **HTTP 200** (Legacy about page)
- `https://rafly.in/team` — **HTTP 200** (Legacy team page)
- `https://rafly.in/pricing` — **HTTP 200** (Legacy pricing page)
- `https://rafly.in/case-studies` — **HTTP 200** (Legacy case studies page)
- `https://rafly.in/privacy` — **HTTP 200** (Legacy privacy policy)
- `https://rafly.in/blog` — **HTTP 404** (Missing route)
- `https://rafly.in/contact` — **HTTP 404** (Missing standalone route)

### B. Service Routing Architecture (Pre-Sync Issues)
- `https://rafly.in/web-development` — **HTTP 200** ⚠️ *Legacy URL serving 200 without 301 redirect to `/services/web-development`*
- `https://rafly.in/web-security` — **HTTP 200** ⚠️ *Legacy URL serving 200 without 301 redirect*
- `https://rafly.in/marketing-advertisement` — **HTTP 200** ⚠️ *Legacy URL serving 200 without 301 redirect*
- `https://rafly.in/content-creation` — **HTTP 200** ⚠️ *Legacy URL serving 200 without 301 redirect*
- `https://rafly.in/ecommerce-support` — **HTTP 200** ⚠️ *Legacy URL serving 200 without 301 redirect*
- `https://rafly.in/services/web-development` — **HTTP 404** ❌ *New canonical route missing on live host*
- `https://rafly.in/services/web-security` — **HTTP 404** ❌ *New canonical route missing on live host*
- `https://rafly.in/services/performance-marketing` — **HTTP 404** ❌ *New canonical route missing on live host*
- `https://rafly.in/services/content-creation` — **HTTP 404** ❌ *New canonical route missing on live host*
- `https://rafly.in/services/ecommerce` — **HTTP 404** ❌ *New canonical route missing on live host*
- `https://rafly.in/services/lead-automation` — **HTTP 404** ❌ *New canonical route missing on live host*

### C. Local SEO & Location Hub Architecture (Pre-Sync Issues)
- `https://rafly.in/locations` — **HTTP 404** ❌ *Location hub missing on live host*
- `https://rafly.in/locations/noida` — **HTTP 404** ❌ *Noida location page missing on live host*
- `https://rafly.in/locations/greater-noida` — **HTTP 404** ❌ *Greater Noida page missing on live host*
- `https://rafly.in/locations/delhi` — **HTTP 404** ❌ *Delhi location page missing on live host*
- `https://rafly.in/locations/gurgaon` — **HTTP 404** ❌ *Gurgaon location page missing on live host*

### D. Asset & Performance Optimization (Pre-Sync Issues)
- `https://rafly.in/css/core-bundle.css` — **HTTP 404** ❌ *Bundled stylesheet missing on live host (10 individual CSS files loaded instead)*
- `https://rafly.in/assets/web_dev_hero_banner.webp` — **HTTP 404** ❌ *WebP optimized hero image missing on live host*

### E. Indexing & Protection Controls
- `https://rafly.in/robots.txt` — **HTTP 200** (Legacy robots.txt)
- `https://rafly.in/sitemap.xml` — **HTTP 200** (Legacy sitemap)
- `https://rafly.in/client-portal` — **HTTP 404** (Protected / Not indexed)

---

## 4. Pre-Deployment Risk Assessment & Remediation Requirements

1. **URL Architecture Divergence:**  
   The live server hosts the legacy flat URL structure (`/web-development`), whereas the remediated local codebase introduces clean `/services/{slug}` routing with single 301 redirects and a `/locations/` hub hierarchy.
2. **Performance Bottleneck:**  
   The live site requests 10 unbundled CSS files and legacy JPEG/PNG hero assets. The remediated codebase introduces `css/core-bundle.css` and WebP hero assets.
3. **Security Patch:**  
   The legacy codebase contained a dev header override flaw in `admin/index.php`. Local codebase commit `404496f` enforces strict environment isolation (`APP_ENV === 'development'`).

---

## 5. Rollback Plan & Backup Metadata

- **Backup Tag:** `production-backup-before-deploy-2026-09-07`
- **Rollback Target:** `git checkout production-backup-before-deploy-2026-09-07`
- **Backup Location:** Git remote branch history on GitHub (`origin`) and local scratch log `scratch/production_backup_details.txt`.
