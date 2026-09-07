# RAFly — Baseline SEO & Technical State (Pre-Fix)

**Target Host:** rafly.in  
**Local Audit Runtime:** http://127.0.0.1:8899 (PHP 8.3 CLI Built-in Web Server)  
**Date:** September 7, 2026  

---

## 1. Route & Page Inventory (Baseline)

| Route Path | File Target | Status Code | Indexability Directive | Rendered Canonical | Title Tag | H1 Heading | Included in Sitemap? |
| :--- | :--- | :---: | :--- | :--- | :--- | :--- | :---: |
| `/` | `index.php` | 200 | `index, follow, max-image-preview:large...` | `https://rafly.in/` | `RAFly Digital Growth — Build. Protect. Grow.` | `Digital growth, <span class="soft">delivered as a system.</span>` | Yes (`loc: ""`) |
| `/about` | `about.php` | 200 | `index, follow...` | `https://rafly.in/about` | `About Rafly \| Digital Growth` | `We build digital systems, <span class="soft">not deliverables.</span>` | Yes |
| `/team` | `team.php` | 200 | `index, follow...` | `https://rafly.in/team` | `Team \| Rafly Digital Growth` | `The team behind <span class="soft">the work.</span>` | Yes |
| `/pricing` | `pricing.php` | 200 | `index, follow...` | `https://rafly.in/pricing` | `Pricing \| Rafly Digital Growth` | `Indicative pricing, <span class="soft">before you message us.</span>` | Yes |
| `/case-studies` | `case-studies.php` | 200 | `index, follow...` | `https://rafly.in/case-studies` | `Case Studies \| Rafly Digital Growth` | `What bundled delivery <span class="soft">looks like in practice.</span>` | Yes |
| `/blog` | `blog.php` | 200 | `index, follow...` | `https://rafly.in/blog` | `Blog \| Rafly Digital Growth` | `The Rafly <span class="soft">blog.</span>` | Yes |
| `/blog/{slug}` | `blog-post.php` | 200 | `index, follow...` | `https://rafly.in/blog/{slug}` | `{post_title} \| Rafly` | `{post_title}` | Yes |
| `/contact` | `contact.php` | 200 | `index, follow...` | `https://rafly.in/contact` | `Contact Rafly \| Talk to one team about all of it` | `Talk to one team <span class="soft">about all of it.</span>` | Yes |
| `/privacy` | `privacy.php` | 200 | `index, follow...` | `https://rafly.in/privacy` | `Privacy Policy \| Rafly Digital Growth` | `Privacy Policy` | Yes |
| `/thank-you` | `thank-you.php` | 200 | `noindex, nofollow` | `https://rafly.in/thank-you` | `Thank You \| Rafly` | `Thank you — we've got your request.` | No (Correct) |
| `/services/web-development` | `service.php?service=web-development` | 200 | `index, follow...` | `https://rafly.in/services/web-development` | `Web Development \| RAFly Digital Growth` | `Web Development Engineering` | No (Sitemap lists `/web-development`!) |
| `/services/web-security` | `service.php?service=web-security` | 200 | `index, follow...` | `https://rafly.in/services/web-security` | `Web Security \| RAFly Digital Growth` | `Web Security Perimeter` | No (Sitemap lists `/web-security`!) |
| `/services/performance-marketing` | `service.php?service=marketing-advertisement` | 200 | `index, follow...` | `https://rafly.in/services/performance-marketing` | `Marketing & Advertisement \| RAFly Digital Growth` | `Marketing & Advertisement Intelligence` | No (Sitemap lists `/marketing-advertisement`!) |
| `/services/content-creation` | `service.php?service=content-creation` | 200 | `index, follow...` | `https://rafly.in/services/content-creation` | `Content Creation \| RAFly Digital Growth` | `Content Creation Studio` | No (Sitemap lists `/content-creation`!) |
| `/services/ecommerce` | `service.php?service=ecommerce-support` | 200 | `index, follow...` | `https://rafly.in/services/ecommerce` | `E-Commerce Storefronts \| RAFly Digital Growth` | `E-Commerce Storefronts Infrastructure` | No (Sitemap lists `/ecommerce-support` & `/ecommerce`!) |
| `/services/lead-automation` | `service.php?service=lead-automation` | 200 | `index, follow...` | `https://rafly.in/services/lead-automation` | `Lead Automation \| RAFly Digital Growth` | `Lead Automation` | No (Sitemap lists `/lead-automation`!) |
| `/locations/greater-noida` | `locations/greater-noida.php` | 200 | `index, follow...` | `https://rafly.in/locations/greater-noida` | `Web Development & Digital Systems in Greater Noida \| RAFly` | `Web Development & Digital Systems in Greater Noida.` | Yes |
| `/locations/noida` | `locations/noida.php` | 200 | `index, follow...` | `https://rafly.in/locations/noida` | `Web Development & Digital Systems in Noida \| RAFly` | `Web Development & Digital Systems in Noida.` | NO (Orphan Page) |
| `/locations/delhi` | `locations/delhi.php` | 200 | `index, follow...` | `https://rafly.in/locations/delhi` | `Web Development & Cyber Security Services in Delhi \| RAFly` | `Web Development & Cyber Security Services in Delhi.` | NO (Orphan Page) |
| `/locations/gurgaon` | `locations/gurgaon.php` | 200 | `index, follow...` | `https://rafly.in/locations/gurgaon` | `Web Development & E-Commerce Engineering in Gurgaon \| RAFly` | `Web Development & E-Commerce Engineering in Gurgaon.` | NO (Orphan Page) |
| `/landing/security-emergency` | `landing/security-emergency.php` | 200 | `index, follow...` | `https://rafly.in/landing/security-emergency` | `Emergency Website Security & Malware Recovery \| RAFly` | `Hacked Site or Google Blacklist? 1-Hour Incident Triage SLA.` | NO (Orphan Page) |
| `/landing/website-audit` | `landing/website-audit.php` | 200 | `index, follow...` | `https://rafly.in/landing/website-audit` | `Free 5-Point Website Speed & Security Audit \| RAFly` | `Get Your Free 5-Point Speed & Security Audit` | NO (Orphan Page) |
| `/landing/whatsapp-automation` | `landing/whatsapp-automation.php` | 200 | `index, follow...` | `https://rafly.in/landing/whatsapp-automation` | `Automated WhatsApp Lead Qualification & CRM Routing \| RAFly` | `24/7 WhatsApp Lead Qualification & CRM Routing.` | NO (Orphan Page) |
| `/client-portal` | `client-portal.php` | 200 | None (Missing `<head>`) | None (Missing `<head>`) | Missing (`head.php` not called!) | `Client Project Portal` | NO (Broken Render) |
| `/locations` | None | 404 | None | None | None | None | NO (Missing Hub Page) |

---

## 2. Identified Technical & Architectural Defects (Pre-Fix Baseline)

1. **P0 Security Vulnerability:** `admin/login.php` allows Host header spoofing (`Host: localhost` + `?dev_role=admin`) to bypass authentication and issue admin sessions on production.
2. **P0 Broken Render / Layout:** `client-portal.php` does not call `partials/head.php` and does not set `$page`. It outputs `<header>` markup without `<!DOCTYPE>`, `<head>`, CSS, or JS scripts, creating a broken user experience and invalid HTML for crawlers.
3. **P1 Canonical / Sitemap Conflict:** `sitemap.php` outputs bare service paths (`/web-development`, `/web-security`, `/marketing-advertisement`, `/content-creation`, `/ecommerce-support`, `/lead-automation`), whereas `service_url()` in `inc/helpers.php`, `<link rel="canonical">` in `head.php`, and site navigation use `/services/web-development`, `/services/web-security`, `/services/performance-marketing`, `/services/ecommerce`, `/services/lead-automation`.
4. **P1 Omitted & Orphan Pages:** 6 public pages (`locations/noida`, `locations/delhi`, `locations/gurgaon`, `landing/security-emergency`, `landing/website-audit`, `landing/whatsapp-automation`) are completely missing from `sitemap.php` and have zero links pointing to them from the header or footer navigation.
5. **P1 Missing Location Hub Route:** Location pages have breadcrumb items linking to `/locations/greater-noida` as parent, but no `/locations` hub route exists.
6. **P2 Duplicate 200 OK Routes:** Bare service URLs (`/web-development`) return 200 OK instead of 301 redirecting to `/services/web-development`.
7. **P2 CSS Asset Delivery:** 10 core CSS files are loaded individually per request without production bundling.
8. **P2 Accessibility Deficiencies:** Form inputs in `landing/security-emergency.php` and `landing/website-audit.php` lack `id` and `for` attribute label links.
9. **P2 Hero Image Optimization:** Service hero banner images are raw JPEGs without `.webp` / `.avif` WebP fallbacks or responsive `srcset`.
10. **P3 Missing Automated QA Test:** No CLI automated test suite exists to verify sitemap equality against canonical URLs and validate route health.
