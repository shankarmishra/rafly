# DOCUMENT STATUS: TEAM USE / AUTOMATION
# 29_LEAD_AUTOMATION_REQUIREMENTS.md — Lead Automation Architecture

---

## 1. AUTOMATION FLOW
```text
Web Form Submission ──> Webhook Validation ──> CRM DB Entry ──> WhatsApp Auto-Ack (60s) ──> Sales Notification ──> Discovery Link
```
- **Rule:** AI automation assists lead qualification but MUST NOT generate binding financial quotes or contracts automatically.
