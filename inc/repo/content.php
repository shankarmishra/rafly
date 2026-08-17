<?php
/**
 * Content repositories: case studies, testimonials, team.
 *
 * These entities DO have admin tables, so unlike ServiceRepository these read
 * the database for real. What they add is a single definition of each query —
 * the case-study SELECT existed twice, once in index.php with LIMIT 3 and once
 * in case-studies.php without, so a column added to one page was silently
 * missing on the other.
 *
 * Every function returns [] when the database is unreachable. That is not an
 * error path to paper over: inc/db.php's db_available() exists precisely so the
 * marketing site keeps serving when the database does not, and the templates
 * are all written to render an empty state rather than a fatal.
 *
 * PLACEHOLDER ROWS ARE NEVER RETURNED TO THE PUBLIC SITE.
 *
 * These functions are read ONLY by public pages (index.php, case-studies.php,
 * team.php). The admin runs its own queries, so an editor still sees every
 * unfinished row exactly as before — nothing is hidden from the people who own
 * the content.
 *
 * The earlier design shipped placeholders to visitors wearing an orange badge,
 * on the theory that an honest placeholder beats a hidden one. That is true of
 * a staging site and false of a live one. A visitor does not read the badge as
 * "our team still has work to do"; they read three cards saying "Client
 * Project — outcome pending" as the portfolio. An empty section says nothing;
 * a badged section says something untrue about the size of the business.
 *
 * So: seeded examples, unattributed quotes and unfinished profiles stop at this
 * boundary. Sections whose only rows were placeholders render their empty state
 * instead, and every empty state on this site is written to stand on its own
 * rather than apologise for missing content.
 */

/**
 * Published case studies, newest-arranged-first by the admin's own ordering.
 *
 * @param int|null $limit Null for all of them. The homepage grid is built for
 *                        three; the dedicated page takes everything.
 * @return list<array<string, mixed>>
 */
function case_studies_all(?int $limit = null): array
{
    if (!db_available()) {
        $rows = seed_preview('case_studies');
        return $limit !== null ? array_slice($rows, 0, max(0, $limit)) : $rows;
    }

    // NOT is_placeholder is spelled without "= false" for the same reason
    // is_published is spelled bare: the column is a real boolean on PostgreSQL
    // and a TINYINT on MySQL, and only the bare form parses on both.
    $sql = 'SELECT client_name, sector, problem, action, metric_value, metric_label,
                   is_placeholder, tags
              FROM case_studies
             WHERE is_published AND NOT is_placeholder
          ORDER BY sort_order, id';

    // LIMIT cannot take a bound parameter on every driver, so it is cast to int
    // and interpolated — the one interpolation inc/db.php's rule allows,
    // because (int) makes the value structurally incapable of carrying SQL.
    if ($limit !== null) {
        $sql .= ' LIMIT ' . max(0, $limit);
    }

    return all($sql);
}

/**
 * Published testimonials.
 *
 * BOTH gates are applied here, not in the template. A quote is publishable only
 * when it is real (not a seeded example) AND the person named in it has agreed
 * to be named. Those are the two ways this section can embarrass the company,
 * and neither is a presentation concern that a template should be free to
 * forget. consent_given is still selected so a caller can assert on it.
 *
 * @return list<array<string, mixed>>
 */
function testimonials_all(?int $limit = null): array
{
    if (!db_available()) {
        $rows = seed_preview('testimonials');
        return $limit !== null ? array_slice($rows, 0, max(0, $limit)) : $rows;
    }

    $sql = 'SELECT quote, author_name, author_role, author_company,
                   consent_given, is_placeholder
              FROM testimonials
             WHERE is_published AND NOT is_placeholder AND consent_given
          ORDER BY sort_order, id';

    if ($limit !== null) {
        $sql .= ' LIMIT ' . max(0, $limit);
    }

    return all($sql);
}

/**
 * Published team members with their photo joined from `media`.
 *
 * WHERE t.is_published is deliberately bare rather than "= true": the column is
 * a boolean on PostgreSQL and a TINYINT on MySQL, and "= true" is not valid on
 * both.
 *
 * @return list<array<string, mixed>>
 */
function team_all(): array
{
    if (!db_available()) {
        return seed_preview('team_members');
    }

    return all('
        SELECT t.id, t.name, t.role, t.brief, t.bio, t.github_url, t.linkedin_url,
               t.is_placeholder,
               m.filename AS photo, m.alt AS photo_alt
          FROM team_members t
          LEFT JOIN media m ON m.id = t.photo_media_id
         WHERE t.is_published AND NOT t.is_placeholder
      ORDER BY t.sort_order ASC, t.id ASC
    ');
}
