-- Migration 011: CRM Lead Qualification Scoring & Deal Stage fields

ALTER TABLE leads ADD COLUMN IF NOT EXISTS qualification_score INT DEFAULT 0;
ALTER TABLE leads ADD COLUMN IF NOT EXISTS budget_bracket VARCHAR(50) DEFAULT '';
ALTER TABLE leads ADD COLUMN IF NOT EXISTS urgency_level VARCHAR(50) DEFAULT '';
ALTER TABLE leads ADD COLUMN IF NOT EXISTS deal_stage VARCHAR(50) DEFAULT 'new';
