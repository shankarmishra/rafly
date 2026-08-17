<?php
/**
 * MetricsRepository — every number the site claims about itself.
 *
 * This file exists to make one rule enforceable in one place: A NUMBER IS
 * EITHER BACKED OR IT IS NOT SHOWN AS FACT.
 *
 * Two kinds of number appear on this site and they are not interchangeable:
 *
 *   CLAIMS      "20+ projects delivered", "98% client satisfaction".
 *               Assertions about business performance. A visitor can be misled
 *               by these, and a competitor can challenge them. They come from
 *               the settings table, are editable in the admin, and carry a
 *               `verified` flag. Unverified until someone actively says
 *               otherwise — shipping a claim unmarked makes it read as audited
 *               fact, so the default has to be the cautious one.
 *
 *   STRUCTURAL  "5 services", "1 invoice", "0 handoffs", "8 published
 *               articles". Facts about the shape of the offer or counts this
 *               code can derive from its own database. Nothing to verify: they
 *               are true by construction, and metric_structural() computes
 *               them rather than storing them.
 *
 * What must never appear: a number invented to make a section look impressive.
 * The homepage carried "182% traffic / 64% conversion / 3x faster launch" in a
 * fake dashboard for exactly that reason. Nothing produced those figures; they
 * were chosen because they looked good. They are gone, and metric_claim() is
 * the only door back in — which requires someone to type them into the admin
 * and tick `verified`, i.e. to take responsibility for them.
 */

/**
 * A business claim, with its provenance attached.
 *
 * (int)setting() is not enough on its own. setting() returns the DEFAULT only
 * when the key is ABSENT; a key that exists holding an empty string returns
 * that empty string, and (int)'' is 0. A settings row someone cleared in the
 * admin therefore rendered a trust bar reading "0 Projects Delivered" — worse
 * than any number it could have shown. This falls back whenever the stored
 * value is not a usable number, so the only way to display a figure is for
 * someone to have actually set one.
 *
 * @return array{value:int, suffix:string, label:string, verified:bool}
 */
function metric_claim(string $key, int $fallback, string $suffix, string $label): array
{
    $raw = trim((string)setting('trust.' . $key . '.value', ''));

    return [
        'value'    => is_numeric($raw) ? (int)$raw : $fallback,
        'suffix'   => $suffix,
        'label'    => $label,
        'verified' => setting('trust.' . $key . '.verified', '0') === '1',
    ];
}

/**
 * A structural fact. Always verified, because it is derived rather than
 * asserted — there is no version of "we offer 5 services" that needs auditing
 * when count(services_all()) is 5.
 *
 * @return array{value:int, suffix:string, label:string, verified:bool}
 */
function metric_structural(int $value, string $suffix, string $label): array
{
    return ['value' => $value, 'suffix' => $suffix, 'label' => $label, 'verified' => true];
}

/**
 * The four figures in the homepage trust bar.
 *
 * AN UNVERIFIED CLAIM IS NOT SHOWN AT ALL. It used to be shown wearing a small
 * "unverified" treatment, which solved the wrong problem: the badge told the
 * team something, and told a visitor "120 projects delivered". The bar led with
 * two figures — projects delivered, client satisfaction — that nobody had
 * measured, in the first screenful after the headline, which is the single
 * easiest thing on a site to challenge and the single hardest to walk back.
 *
 * So the bar is now assembled, not listed. Verified claims come first, in the
 * order below; whatever slots remain are filled from a pool of STRUCTURAL facts
 * — each one either counted from this codebase or a policy the company itself
 * sets, and each one restated in plain English elsewhere on the same page:
 *
 *   services      count(services_all()), the same list the nav and the five
 *                 cards below render from — it cannot go stale
 *   contact       the offer itself: one team, one point of contact
 *   support       the support window stated in the FAQ and in Ongoing Support
 *   IP            "full IP ownership transfers to you on final payment", which
 *                 is FAQ #8 on this very page
 *
 * The moment someone verifies a real number in the admin, it takes the first
 * slot and pushes the last structural fact out. Nothing here needs editing for
 * that to happen — which is the point, because the alternative is a code change
 * standing between the company and being able to state a true fact about
 * itself.
 *
 * @return list<array{value:int, suffix:string, label:string, verified:bool}>
 */
function metrics_trust_bar(): array
{
    $claims = array_values(array_filter([
        metric_claim('projects',     0, '+', 'Projects Delivered'),
        metric_claim('satisfaction', 0, '%', 'Client Satisfaction'),
    ], static fn (array $m): bool => $m['verified'] && $m['value'] > 0));

    $structural = [
        metric_structural(count(services_all()), '',    'Services Under One Roof'),
        metric_structural(1,                     '',    'Point Of Contact'),
        metric_structural(24,                    '/7',  'Support Availability'),
        metric_structural(100,                   '%',   'IP Ownership Transferred'),
    ];

    // Four cells is what the grid is built for. Claims take the front; the
    // structural pool backfills and is trimmed from the end, so the bar is
    // always full and never padded with something untrue.
    return array_slice(array_merge($claims, $structural), 0, 4);
}

/**
 * Counts the site can prove about itself, for places that want a number but
 * must not invent one. Every value here is a COUNT(*) or a derived constant.
 *
 * Returns null for a count whose table is unreachable, so a caller can drop the
 * item rather than print a zero — "0 articles published" on a blog with four
 * articles is a bug wearing a fact's clothing.
 *
 * @return array<string, int|null>
 */
function metrics_structural_counts(): array
{
    $count = static function (string $sql): ?int {
        if (!db_available()) {
            return null;
        }
        $n = scalar($sql);
        return $n === null || $n === false ? null : (int)$n;
    };

    return [
        'services'     => count(services_all()),
        'articles'     => $count('SELECT COUNT(*) FROM posts WHERE status = \'published\''),
        'team'         => $count('SELECT COUNT(*) FROM team_members WHERE is_published AND NOT is_placeholder'),
        'case_studies' => $count('SELECT COUNT(*) FROM case_studies WHERE is_published AND NOT is_placeholder'),
    ];
}
