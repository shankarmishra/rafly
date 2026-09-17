# RAFly Admin OS — Functional Test & Route Verification Matrix

**Build Version**: `v2.5.3`  
**Date**: September 17, 2026  

---

## Route QA Matrix

| Route URI | Role Access | Page Load (HTTP 200) | Navigation Active | Form / Action Save | Validation State | Modal / Popover QA | Responsive QA | Verification Status |
|---|---|---|---|---|---|---|---|---|
| `/admin/` | `leads.view` | **200 OK** | Dashboard | Filter Date Range | Valid | Global Search (Cmd+K) | PASS (320px..1920px) | **VERIFIED PASS** |
| `/admin/login.php` | Public / Guest | **200 OK** | N/A | Auth POST / Dev Quick Sign-in | CSRF + Honeypot | Confirm Dialog | PASS | **VERIFIED PASS** |
| `/admin/2fa.php` | Authenticated (Pending 2FA) | **200 OK** | N/A | TOTP Verification | 6-digit Code Guard | N/A | PASS | **VERIFIED PASS** |
| `/admin/seo.php` | `settings.view` | **200 OK** | SEO Control Center | Save Metadata POST | Length + URL Validation | SERP / OG Simulator | PASS | **VERIFIED PASS** |
| `/admin/seo.php?page=homepage` | `settings.view` | **200 OK** | Page Workspace | Save Homepage SEO | Title/Desc Counters | Schema Inspector | PASS | **VERIFIED PASS** |
| `/admin/services.php` | `content.view` | **200 OK** | Services CMS | Service Edit / Create | Required Fields | Confirm Dialog | PASS | **VERIFIED PASS** |
| `/admin/leads.php` | `leads.view` | **200 OK** | CRM & Inbound | Status Transition | Pipeline Filter | Lead Quick Drawer | PASS | **VERIFIED PASS** |
| `/admin/clients.php` | `leads.view` | **200 OK** | Clients 360 | Account Update | Email & Phone Format | Client Modal | PASS | **VERIFIED PASS** |
| `/admin/projects.php` | `content.view` | **200 OK** | Projects Console | Scope & Milestone | Health Status Guard | New Project Dialog | PASS | **VERIFIED PASS** |
| `/admin/tasks.php` | `content.view` | **200 OK** | Work Board | Kanban Drag / Move | Status Guard | Task Modal | PASS | **VERIFIED PASS** |
| `/admin/analytics.php` | `leads.view` | **200 OK** | Analytics Hub | Date Range Select | Valid | Chart Render | PASS | **VERIFIED PASS** |
| `/admin/notifications.php` | `leads.view` | **200 OK** | Notifications | Mark Read / Clear | Valid | N/A | PASS | **VERIFIED PASS** |
| `/admin/settings.php` | `settings.view` | **200 OK** | System Settings | Settings POST | Cross-DB Upsert | N/A | PASS | **VERIFIED PASS** |
| `/admin/users.php` | `users.view` | **200 OK** | Users & Roles | User Create / Edit | Role Capability Guard | User Modal | PASS | **VERIFIED PASS** |
| `/admin/audit.php` | `audit.view` | **200 OK** | Activity Log | Filter Actions | Valid | Diff Modal | PASS | **VERIFIED PASS** |

---

## Security & Access Control Assurance

- **Authentication**: Session tokens hashed with SHA-256 in `admin_sessions` table.
- **CSRF Protection**: All POST endpoints enforce `csrf_field()` token verification.
- **Role-Based Access Control (RBAC)**: Capabilities (`leads.view`, `content.edit`, `settings.edit`, `users.manage`) enforced before output.
- **Input Sanitization**: Parameterized queries used across `inc/repo/seo.php` and `inc/db.php`.
