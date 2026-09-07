# RAFly Team OS — Role-Based Access Control (RBAC) & Permission Matrix

**Document Version:** 1.0.0  
**Security Governance:** Strictly Enforced Server-Side API & Database Policy  

---

## 1. ROLES DEFINITION

RAFly Team OS implements 9 distinct operational roles:

1. **Super Admin:** Full system access, system configuration, audit logs, user management.
2. **Admin / Operations:** Full access to CRM, Projects, Team Workload, Finance records, and SOPs.
3. **Sales:** Access to Leads, CRM Pipeline, Proposals, Discovery Scripts, and Client Communication.
4. **Developer:** Access to assigned Web Projects, Code Tasks, Staging Environments, and Technical Specs.
5. **Designer:** Access to Creative Studio, UI/UX Tasks, Design Assets, and Internal Approvals.
6. **Video / Reel Editor:** Access to Creative Studio, Media Assets, Video Annotations, and Reel Workflows.
7. **Marketing:** Access to Paid Ad Campaigns, Lead Automation, SEO Analytics, and Social Content.
8. **Finance:** Access to Invoices, Payments, Milestones, and Revenue Dashboards.
9. **Client:** Restricted isolation to their own Client 360 Workspace, Shared Files, Invoices, & Approvals ONLY.

---

## 2. GRANULAR PERMISSION MATRIX

| Module / Resource | Super Admin | Admin/Ops | Sales | Dev | Designer | Editor | Mktg | Finance | Client |
|---|---|---|---|---|---|---|---|---|---|
| Executive Dashboard | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Internal Pricing Formula | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| CRM & Leads | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ | 👁️ | ❌ | ❌ |
| Project Tasks | ✅ | ✅ | 👁️ | ✅ | ✅ | ✅ | ✅ | ❌ | 👁️ (Own) |
| Creative Studio | ✅ | ✅ | 👁️ | ❌ | ✅ | ✅ | ✅ | ❌ | 👁️ (Own) |
| Financials / Invoices | ✅ | ✅ | 👁️ | ❌ | ❌ | ❌ | ❌ | ✅ | 👁️ (Own) |
| Internal SOPs | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| Client Portal Isolation | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ (Strict) |

*Legend: ✅ Full Access | 👁️ Read / Context Only | ❌ No Access*
