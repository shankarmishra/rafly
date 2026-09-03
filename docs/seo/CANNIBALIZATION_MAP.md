# RAFly — Keyword Cannibalization Audit & Resolution Map

## Overview
This matrix identifies potential search query overlaps between homepage, individual service pages, location pages, and blog posts, along with the strict canonical rules enforced to preserve page differentiation.

---

| Potential Conflict Area | Overlapping Pages | Primary Winner URL | Resolution / Differentiation Strategy |
|---|---|---|---|
| **"Web Development Services"** | Homepage (`/`) vs `/web-development` | `/web-development` | Homepage targets overall brand bundle (*"Bundled Web, Security & Marketing Partner"*). `/web-development` targets specific technical execution (*"Custom Web Application & PHP Engineering"*). |
| **"Web Security & Firewall"** | Homepage (`/`) vs `/web-security` | `/web-security` | Homepage mentions security as 1 of 5 capabilities. `/web-security` owns deep technical queries (*"WAF Setup, Vulnerability Audits, Patching"*). |
| **"Greater Noida Web Agency"** | Homepage (`/`) vs `/locations/greater-noida` | `/locations/greater-noida` | `/locations/greater-noida` owns local geo-targeted queries with office address, regional IT context, and local FAQs. Homepage remains nationwide brand portal. |
| **"Digital Agency Pricing"** | Homepage (`#pricing`) vs `/pricing` | `/pricing` | `/pricing` owns direct pricing queries with full tier comparisons, indicative ranges, and ROI FAQs. Homepage `#pricing` links directly to `/pricing`. |
| **"SEO & Content Strategy"** | `/marketing-advertisement` vs `/content-creation` vs Blog Articles | `/content-creation` (Content) / `/marketing-advertisement` (PPC/Growth) | Content Creation handles editorial writing & copywriting. Marketing handles PPC, analytics, & conversion funnels. Blog articles target long-tail informational "How-To" queries. |

---

## Canonicalization & On-Page Boundary Rules
1. **Self-Referential Canonicals**: Every service page (`/web-development`, `/web-security`, etc.) uses an explicit self-referential canonical (`https://rafly.in/<service>`) to prevent search engines from merging service pages with the homepage.
2. **Explicit Headings Distinction**: Service H1s clearly specify the distinct technical discipline rather than generic "Digital Services".
3. **No Duplicate Paragraphs**: Page copy across all 18 indexable routes is 100% unique.
