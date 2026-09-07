# DOCUMENT STATUS: TEAM USE / INTERNAL
# 10_LEAD_QUALIFICATION_SOP.md — Lead Qualification Standard Operating Procedure

**Objective:** Filter inbound leads into High Fit, Medium Fit, and Disqualified categories within 15 minutes of submission.

---

## 1. QUALIFICATION SCORING MATRIX (0 - 100 SCALE)

| Criterion | Points |
|---|---|
| Business Legitimacy (Verified company/website) | +20 pts |
| Budget Alignment (Budget $\ge$ ₹25,000 or Retainer $\ge$ ₹10,000/mo) | +30 pts |
| Urgency (Need within 1-4 weeks) | +20 pts |
| Decision Maker Direct Contact (CEO/Founder/Director) | +15 pts |
| Technical Fit (Build, Protect, Grow alignment) | +15 pts |

### Action Thresholds:
- **Score $\ge$ 70 (HIGH FIT):** Schedule 15-Minute Discovery Call within 4 hours.
- **Score 40 - 69 (MEDIUM FIT):** Send Automated Audit / Nurture Sequence via Email/WhatsApp.
- **Score < 40 (LOW FIT / DISQUALIFIED):** Politely decline or refer to template solutions.

---

## 2. DISQUALIFICATION LIST CHECKLIST
Reject leads matching any of these flags:
- [ ] Budget < ₹15,000 for custom development.
- [ ] Refusal to sign SOW or pay 30% deposit.
- [ ] Demands for unlimited revisions or zero contract.
- [ ] Illegal, gambling, or copyright-infringing business models.

---

## 3. CRM INTEGRATION & AUTOMATED LEAD ROUTING

1. **Source Attribution:** Capture UTM parameters (`utm_source`, `utm_medium`, `utm_campaign`) on all web form submissions.
2. **CRM Field Mapping:** Automatically populate `leads` database table (`company_name`, `contact_email`, `budget_bracket`, `urgency_level`).
3. **Automated Response SLA:** Trigger automated WhatsApp acknowledgment within 60 seconds; assign sales owner within 15 minutes.
