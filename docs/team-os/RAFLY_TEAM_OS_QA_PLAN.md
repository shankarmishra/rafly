# RAFly Team OS — QA & Security Verification Plan

**Document Version:** 1.0.0  
**Governance:** Zero-Bypass Role Security & Automated Testing Standards  

---

## 1. ROLE ISOLATION QA MATRIX

Every release must be verified across all 9 roles to ensure strict data isolation:

```text
[Super Admin] ──> Full Visibility
[Client Role] ──> Restricted ONLY to own client_id records (404/403 on all internal endpoints)
[Developer]   ──> No access to CRM revenue, internal hourly calculations, or legal proposals
```

---

## 2. SECURITY PENETRATION CHECKS
- [ ] Direct Object Reference (IDOR) tests on `/api/v1/clients/{id}` and `/api/v1/files/{id}`.
- [ ] CSRF token verification on all POST/PATCH/DELETE endpoints.
- [ ] SQL Injection prevention via mandatory PDO prepared statements.
- [ ] Rate limiting enforcement (Max 100 API requests / minute per IP).
