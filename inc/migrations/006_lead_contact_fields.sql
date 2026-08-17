-- ---------------------------------------------------------------------------
-- 006 — name and email on leads
--
-- The contact form previously collected company_name and contact_number only.
-- International (UK/US) visitors in particular are much less likely to open
-- WhatsApp for a first contact than to reply to an email, and "which company"
-- is not the same question as "who am I talking to" — company_name answers
-- neither reliably (a sole trader may have no company name at all).
--
-- Both are optional at the column level (NOT NULL DEFAULT '') rather than
-- required, matching contact_number's existing looseness, but submit.php
-- enforces both as required on the form — the column stays permissive so a
-- future admin-created lead (phone enquiry logged manually, say) is never
-- blocked by a constraint the web form imposes on itself.
-- ---------------------------------------------------------------------------

ALTER TABLE leads ADD COLUMN contact_name  text NOT NULL DEFAULT '';
ALTER TABLE leads ADD COLUMN contact_email text NOT NULL DEFAULT '';
