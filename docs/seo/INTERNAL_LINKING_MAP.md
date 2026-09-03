# RAFly — Master Internal Linking & Authority Graph

## Overview
This document maps internal link structures, contextual anchor text strategies, and cross-linking rules across the RAFly site to distribute page authority seamlessly.

---

## Primary Navigation Links (Header & Footer)
- `/` → Homepage Brand Portal
- `/web-development` → Custom Web Development
- `/web-security` → Web Security & Hardening
- `/marketing-advertisement` → Growth Marketing & PPC
- `/content-creation` → Content Strategy & Copywriting
- `/ecommerce-support` → E-Commerce Systems
- `/pricing` → Indicative Packages & Pricing
- `/case-studies` → Client Case Studies & Proof
- `/about` → Company Story & 5-Branch Model
- `/team` → Coordinated Core Team
- `/blog` → Engineering Insights & Articles
- `/contact` → Direct Build Channel / Consultation
- `/locations/greater-noida` → Regional Office & Local IT

---

## Contextual Cross-Linking Rules
1. **Service Pages → Related Services**: Each service page cross-links to the other 4 complementary services via dynamic footer blocks (`inc/repo/links.php`).
2. **Service Pages → Relevant Case Studies**: Service pages dynamically fetch relevant case studies matching their capability tag.
3. **Blog Posts → Commercial Services**: Blog posts link to the primary service page relevant to their topic (e.g. security guides link to `/web-security`).
4. **Location Page → Commercial Services**: `/locations/greater-noida` links to all 5 core services and case studies.
5. **Case Studies → Services & Contact**: Case studies link to the services deployed and invite visitors to request a custom consultation.
