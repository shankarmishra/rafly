# RAFly Team OS — RESTful & Webhook API Specification

**Document Version:** 1.0.0  
**Protocol:** HTTPS RESTful JSON + Server-Sent Events (SSE)  

---

## 1. RESTFUL ENDPOINTS MATRIX

### Auth & User Management
- `POST /api/v1/auth/login` — Authenticate user and issue session cookie.
- `POST /api/v1/auth/logout` — Revoke active session token.
- `GET /api/v1/user/me` — Return authenticated user profile and permissions.

### CRM & Deals
- `GET /api/v1/crm/leads` — List leads with filters (status, owner, qualification_score).
- `POST /api/v1/crm/leads` — Create lead entry.
- `PATCH /api/v1/crm/leads/{id}` — Update lead status, owner, or deal stage.

### Projects & Tasks
- `GET /api/v1/projects` — List active projects by client or status.
- `GET /api/v1/projects/{id}/board` — Return Kanban board task cards for project.
- `POST /api/v1/tasks` — Create task with title, project_id, assignee_id, priority, due_date.
- `PATCH /api/v1/tasks/{id}` — Move task status (`backlog` -> `in_progress` -> `review` -> `completed`).

### Creative Studio & Approvals
- `GET /api/v1/creative/reels` — List reel production objects.
- `POST /api/v1/creative/assets/{id}/comments` — Add timestamped comment to video asset.
- `POST /api/v1/approvals/{id}/decision` — Approve or request changes on deliverable.

---

## 2. WEBHOOK INGRESS ENDPOINTS
- `POST /api/v1/webhooks/whatsapp` — Handle WhatsApp message triggers & qualification.
- `POST /api/v1/webhooks/website-form` — Handle inbound web form submissions (`leads` ingestion).
- `POST /api/v1/webhooks/payment` — Handle gateway milestone payment alerts.
