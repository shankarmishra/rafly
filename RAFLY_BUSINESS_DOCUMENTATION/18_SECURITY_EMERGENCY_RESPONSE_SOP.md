# DOCUMENT STATUS: TEAM USE / SECURITY OPERATIONS
# 18_SECURITY_EMERGENCY_RESPONSE_SOP.md — Security Emergency Response SOP

**Scope:** Rapid isolation, containment, cleanup, and recovery of compromised client web infrastructure.

---

## 1. EMERGENCY RESPONSE LIFECYCLE (5 PHASES)

```
Detect ──> Contain ──> Restore ──> Harden ──> Monitor
```

1. **Detect (Triage SLA):** Incident logged via emergency channel. Acknowledge and triage within SLA (1h Enterprise / 4h Growth / 12h Standard).
2. **Contain:** Quarantined server environment, reset administrative passwords, restrict database access permissions.
3. **Restore:** Scan core files against clean checksums, remove web shells/malware, sanitize database tables, re-install clean core.
4. **Harden:** Implement WAF rules, block malicious IP ranges, set strict file permissions (`644` files / `755` directories).
5. **Monitor:** Submit Google Blacklist review requests, monitor security logs for 7 days post-incident.

---

## 2. EMERGENCY ESCALATION MATRIX & CONTACTS

| Incident Severity | Initial Responder | Level 1 Escalation | Level 2 Escalation | Level 3 Escalation |
|---|---|---|---|---|
| **Critical (Downtime / Breach)** | Security Engineer | Security Lead (15m) | Operations Head (30m) | CEO / Founder (1h) |
| **High (Malware Flag / Defacement)**| Security Engineer | Security Lead (30m) | Operations Head (2h) | — |
| **Medium (Suspicious Activity)** | Security Engineer | Security Lead (2h) | — | — |
| **Low (Header Warning)** | Junior Engineer | Security Engineer (24h) | — | — |

### Emergency Incident Contacts
- **Security Lead Hotline:** +91 8796882212 (Ext 1)
- **Operations Lead Hotline:** +91 8796882212 (Ext 2)
- **Emergency Ops Inbox:** `security-emergency@rafly.in`
