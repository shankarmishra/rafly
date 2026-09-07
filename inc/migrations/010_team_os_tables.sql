-- RAFly Team OS Core Tables Migration (PostgreSQL)

CREATE TABLE IF NOT EXISTS clients (
    id BIGSERIAL PRIMARY KEY,
    company_name VARCHAR(191) NOT NULL,
    industry VARCHAR(100),
    website_url VARCHAR(255),
    status VARCHAR(30) DEFAULT 'active',
    account_manager_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS projects (
    id BIGSERIAL PRIMARY KEY,
    client_id BIGINT NOT NULL REFERENCES clients(id) ON DELETE CASCADE,
    name VARCHAR(191) NOT NULL,
    code VARCHAR(30) UNIQUE NOT NULL,
    health_status VARCHAR(20) DEFAULT 'green',
    pillar VARCHAR(30) NOT NULL,
    contract_value NUMERIC(12,2) DEFAULT 0.00,
    start_date DATE,
    target_date DATE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS project_milestones (
    id BIGSERIAL PRIMARY KEY,
    project_id BIGINT NOT NULL REFERENCES projects(id) ON DELETE CASCADE,
    title VARCHAR(191) NOT NULL,
    percentage INT NOT NULL,
    amount NUMERIC(12,2) NOT NULL,
    status VARCHAR(30) DEFAULT 'pending',
    due_date DATE
);

CREATE TABLE IF NOT EXISTS tasks (
    id BIGSERIAL PRIMARY KEY,
    project_id BIGINT REFERENCES projects(id) ON DELETE CASCADE,
    client_id BIGINT REFERENCES clients(id) ON DELETE CASCADE,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status VARCHAR(30) DEFAULT 'backlog',
    priority VARCHAR(20) DEFAULT 'medium',
    creator_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    due_date TIMESTAMP WITH TIME ZONE,
    estimated_hours NUMERIC(6,2),
    actual_hours NUMERIC(6,2),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS task_assignees (
    task_id BIGINT NOT NULL REFERENCES tasks(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (task_id, user_id)
);

CREATE TABLE IF NOT EXISTS channels (
    id BIGSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    type VARCHAR(30) DEFAULT 'topic',
    project_id BIGINT REFERENCES projects(id) ON DELETE CASCADE,
    client_id BIGINT REFERENCES clients(id) ON DELETE CASCADE,
    is_private BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS channel_messages (
    id BIGSERIAL PRIMARY KEY,
    channel_id BIGINT NOT NULL REFERENCES channels(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    message TEXT NOT NULL,
    parent_id BIGINT REFERENCES channel_messages(id) ON DELETE SET NULL,
    is_task_converted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS creative_assets (
    id BIGSERIAL PRIMARY KEY,
    project_id BIGINT REFERENCES projects(id) ON DELETE CASCADE,
    client_id BIGINT REFERENCES clients(id) ON DELETE CASCADE,
    title VARCHAR(191) NOT NULL,
    type VARCHAR(30) NOT NULL,
    status VARCHAR(30) DEFAULT 'idea',
    editor_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    designer_id BIGINT REFERENCES users(id) ON DELETE SET NULL,
    aspect_ratio VARCHAR(20) DEFAULT '9:16',
    raw_file_url VARCHAR(255),
    preview_file_url VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS asset_comments (
    id BIGSERIAL PRIMARY KEY,
    asset_id BIGINT NOT NULL REFERENCES creative_assets(id) ON DELETE CASCADE,
    user_id BIGINT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    timestamp_seconds NUMERIC(8,2),
    comment TEXT NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS approvals (
    id BIGSERIAL PRIMARY KEY,
    object_type VARCHAR(50) NOT NULL,
    object_id BIGINT NOT NULL,
    client_id BIGINT REFERENCES clients(id) ON DELETE CASCADE,
    project_id BIGINT REFERENCES projects(id) ON DELETE CASCADE,
    title VARCHAR(191) NOT NULL,
    status VARCHAR(30) DEFAULT 'draft',
    requested_by BIGINT REFERENCES users(id) ON DELETE SET NULL,
    approved_at TIMESTAMP WITH TIME ZONE,
    notes TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS crm_deals (
    id BIGSERIAL PRIMARY KEY,
    lead_id BIGINT REFERENCES leads(id) ON DELETE SET NULL,
    client_id BIGINT REFERENCES clients(id) ON DELETE CASCADE,
    title VARCHAR(191) NOT NULL,
    deal_stage VARCHAR(30) DEFAULT 'new',
    proposal_value NUMERIC(12,2) DEFAULT 0.00,
    probability INT DEFAULT 50,
    lost_reason VARCHAR(255),
    target_close_date DATE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);
