# DOCUMENT STATUS: TEAM USE / TECHNICAL
# 19_SECURITY_HARDENING_SOP.md — Security Hardening SOP

---

## 1. TECHNICAL HARDENING CHECKLIST
- [ ] Enforce SSL/TLS 1.3 with HSTS headers.
- [ ] Implement Security Headers: `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Content-Security-Policy`.
- [ ] Change default database prefixes from default `wp_` or `db_`.
- [ ] Disable directory listing (`Options -Indexes` in `.htaccess`).
- [ ] Implement Rate Limiting on login endpoints.
