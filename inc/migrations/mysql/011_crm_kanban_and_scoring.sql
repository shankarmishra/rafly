-- Migration 011: CRM Lead Qualification Scoring & Deal Stage fields (MySQL)

ALTER TABLE leads ADD COLUMN qualification_score INT DEFAULT 0;
ALTER TABLE leads ADD COLUMN budget_bracket VARCHAR(50) DEFAULT '';
ALTER TABLE leads ADD COLUMN urgency_level VARCHAR(50) DEFAULT '';
ALTER TABLE leads ADD COLUMN deal_stage VARCHAR(50) DEFAULT 'new';
