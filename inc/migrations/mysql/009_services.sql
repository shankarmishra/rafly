-- ---------------------------------------------------------------------------
-- 009 services table (MySQL)
-- ---------------------------------------------------------------------------

CREATE TABLE services (
    id            bigint NOT NULL AUTO_INCREMENT PRIMARY KEY,
    slug          varchar(200) NOT NULL UNIQUE,
    title         varchar(200) NOT NULL,
    icon          varchar(60)  NOT NULL DEFAULT 'layers',
    key_name      varchar(60)  NOT NULL DEFAULT 'web',
    tagline       text         NOT NULL,
    intro         text         NOT NULL,
    card          text         NOT NULL,
    sort_order    int          NOT NULL DEFAULT 0,
    is_published  tinyint(1)   NOT NULL DEFAULT 1,
    created_at    datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    datetime     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE INDEX services_sort      ON services (sort_order, id);
CREATE INDEX services_published ON services (is_published, sort_order);
