# RAFly Complete Public Route Inventory

## 1. Public Indexable & Target Routes

| URL | Status | Page Type | Indexable? | Canonical | Title | H1 | Meta Description | Primary Intent | Primary Keyword | Priority |
| :--- | :---: | :--- | :---: | :--- | :--- | :--- | :--- | :--- | :--- | :---: |
| `/` | 200 | Home | Yes | `https://rafly.in/` | High-Velocity Digital Systems \| RAFly | High-Velocity Digital Systems | Performance-focused websites, web security, and digital systems built for growth. | Systems & Growth | RAFly Digital Systems | 1.0 |
| `/about` | 200 | About | Yes | `https://rafly.in/about` | About RAFly \| Digital Growth Partner | We build digital systems, not deliverables | One partner across web development, content creation, security and marketing. | Agency Identity | Digital Growth Partner | 0.8 |
| `/team` | 200 | Team | Yes | `https://rafly.in/team` | RAFly Team & Engineering Principles | The People & Principles Behind the Engine | Experienced developers, security reviewers, content writers, and marketers. | Technical Capability | RAFly Engineers | 0.6 |
| `/pricing` | 200 | Commercial | Yes | `https://rafly.in/pricing` | Transparent Bundle Packages \| RAFly | Fixed-Scope Growth Bundles | Fixed-scope website, security, and digital marketing packages with clear SLAs. | Commercial Pricing | Web Bundle Pricing | 0.9 |
| `/case-studies` | 200 | Proof | Yes | `https://rafly.in/case-studies` | Selected Work & Digital Systems \| RAFly | Proof, Not Promises | Real digital systems, custom web applications, and security architectures built by RAFly. | Work Evidence | Digital Systems Proof | 0.7 |
| `/contact` | 200 | Contact | Yes | `https://rafly.in/contact` | Direct Build Channel \| RAFly | Talk to One Team About All of It | Direct build channel for website development, security audits, and marketing support. | Lead Generation | Contact RAFly | 0.8 |
| `/privacy` | 200 | Legal | Yes | `https://rafly.in/privacy` | Privacy Policy \| RAFly | Privacy Policy | How RAFly handles visitor data, communications, and privacy commitments. | Legal Compliance | Privacy Policy | 0.3 |
| `/blog` | 200 | Insights | Yes | `https://rafly.in/blog` | Insights & Engineering Notes \| RAFly | Insights & Engineering Notes | Technical guides, security reviews, and growth benchmarks from the RAFly team. | Informational | Tech & Marketing Insights | 0.7 |
| `/blog/{slug}` | 200 | Article | Yes | `https://rafly.in/blog/{slug}` | {Article Title} \| RAFly | {Article Title} | Deep-dive guide into web architecture, security, and digital operations. | Deep-Dive Guide | Targeted Topic | 0.7 |
| `/web-development` | 200 | Service | Yes | `https://rafly.in/web-development` | Web Development Services \| RAFly | Web Development Systems | Responsive, performance-focused websites and web apps built to support your growth. | Commercial Service | Web Development Company | 0.9 |
| `/web-security` | 200 | Service | Yes | `https://rafly.in/web-security` | Web Security & WAF Services \| RAFly | Web Security & Infrastructure | Baseline security reviews, vulnerability audits, and Web Application Firewall setup. | Security Service | Web Security Audit | 0.9 |
| `/marketing-advertisement` | 200 | Service | Yes | `https://rafly.in/marketing-advertisement` | Digital Marketing & Ads \| RAFly | Marketing & Paid Media | Search and social campaigns managed directly alongside web analytics and landing pages. | Growth Service | Performance Marketing Agency | 0.9 |
| `/content-creation` | 200 | Service | Yes | `https://rafly.in/content-creation` | Content & Editorial Systems \| RAFly | Content & Editorial Systems | Technical copywriting, landing page messaging, and documentation built for conversion. | Content Service | Editorial & Content Services | 0.9 |
| `/ecommerce-support` | 200 | Service | Yes | `https://rafly.in/ecommerce-support` | E-Commerce Operations Services \| RAFly | E-Commerce Operations & Growth | Headless Shopify development, payment gateway sync, and catalog indexing. | E-Commerce Service | E-Commerce Operations | 0.9 |
| `/locations/greater-noida` | 200 | Location | Yes | `https://rafly.in/locations/greater-noida` | Web Development Greater Noida \| RAFly | Digital Systems for Greater Noida Businesses | Digital partner for tech companies and commercial brands in Greater Noida. | Local Service | Web Development Greater Noida | 0.7 |
| `/locations/noida` | 200 | Location | Yes | `https://rafly.in/locations/noida` | Web Development Services Noida \| RAFly | Web Development & Digital Systems in Noida | High-performance websites and security architectures for enterprise hubs in Noida. | Local Service | Web Development Noida | 0.8 |
| `/locations/delhi` | 200 | Location | Yes | `https://rafly.in/locations/delhi` | Web Development Services Delhi NCR \| RAFly | Web Development & Digital Systems in Delhi NCR | Engineering-grade web systems and performance marketing for brands in Delhi NCR. | Local Service | Web Development Delhi NCR | 0.8 |
| `/locations/gurgaon` | 200 | Location | Yes | `https://rafly.in/locations/gurgaon` | Web Development Services Gurgaon \| RAFly | Web Development & Systems in Gurgaon | Fast web applications and custom digital infrastructure for tech firms in Gurgaon. | Local Service | Web Development Gurgaon | 0.8 |

---

## 2. Non-Indexable & System Utility Routes

| Route | Expected HTTP | Policy | Purpose & Security Gate |
| :--- | :---: | :--- | :--- |
| `/thank-you` | 200 | `NOINDEX` | Session-gated form confirmation page. |
| `/404` | 404 | `NOINDEX` | System error 404 page (returns true 404 status code). |
| `/submit` | 303 / 400 | `NOT PUBLIC` | POST-only form handler (returns 400 if accessed directly via GET). |
| `/sitemap.xml` | 200 | Machine-Readable | XML sitemap index generated dynamically by `sitemap.php`. |
| `/robots.txt` | 200 | Machine-Readable | Static crawler directives file. |
| `/admin/*` | 302 / 200 | `DISALLOWED` | Protected management dashboard (login & 2FA gated). |
| `/inc/*`, `/partials/*` | 404 | `DISALLOWED` | Internal code modules blocked via `.htaccess` / `router.php`. |
