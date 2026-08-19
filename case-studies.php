<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * Full case-studies page — Problem -> Approach -> Result per client, versus
 * the homepage's #work section which only ever shows a 3-card snippet.
 *
 * admin/case-studies.php already collects `problem` separately from `action`
 * specifically "kept for a future detail page" (its own comment says so) —
 * this is that page. Nothing new to enter; existing rows already have both
 * fields, they just were never both rendered anywhere until now.
 */

$caseStudies = case_studies_all();

// lowercase title => slug, so a tag chip that names a real service ("Web
// Development", or an admin's "web development") can link to it. tags is
// admin-typed free text — "SEO", "Content" and plenty of others will never
// match, and stay a plain chip. Same case-insensitive-exact rule
// related_case_studies_for_service() uses, so a service page's "Case study"
// cards and this page's chip links agree on what counts as a match.
$serviceSlugByTitle = [];
foreach (services_all() as $svc) {
    $serviceSlugByTitle[mb_strtolower((string)$svc['title'], 'UTF-8')] = $svc['slug'];
}

$crumbs = [
    ['name' => 'Home',         'url' => '/'],
    ['name' => 'Case Studies', 'url' => '/case-studies'],
];

$page = [
    'id'        => 'case-studies',
    'title'     => 'Case Studies | Rafly Digital Growth',
    'desc'      => 'How bundled delivery actually plays out — the problem, our approach, and the measurable result for each engagement.',
    'bodyClass' => 'page-case-studies',
    'styles'    => ['home', 'work'],
    'schema'    => array_filter([
        schema_breadcrumbs($crumbs),
        // Each card gets an id="case-study-N" anchor so this ItemList can point
        // at a genuinely distinct URL per item, not the same page URL repeated
        // — that is what makes an ItemList meaningful here versus a bare list
        // of names.
        $caseStudies ? schema_collection_list(
            'Case Studies',
            'case-studies',
            array_map(
                static fn(int $i, array $w): array => ['name' => (string)$w['client_name'], 'url' => 'case-studies#case-study-' . $i],
                array_keys($caseStudies),
                $caseStudies
            )
        ) : null,
    ]),
];
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <section class="section page-head">
        <?php require __DIR__ . '/partials/head-object.php'; ?>
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Case studies</p>
                    <h1 class="display">What bundled delivery <span class="soft">looks like in practice.</span></h1>
                </div>
                <p class="lead">
                    Every write-up below follows the same shape: the problem as the client
                    described it, what we actually did about it, and one evidenced result
                    &mdash; not a vanity metric picked to look good.
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot">
        <div class="container">
<?php
/* WHEN THERE IS NOTHING PUBLISHED YET.
   This used to read "No case studies published yet — check back soon, or get
   in touch to be our first." Two problems: it is a page whose entire content
   is an apology, and it volunteers a fact about the size of the client list
   that nobody asked for.

   What replaces it is not filler and not an invented example — it is the
   contract this page runs on, stated plainly: every engagement written up here
   follows Problem -> Approach -> Result, and the Result is a measured figure
   the client can confirm. That is a real editorial standard, it is the reason
   the page currently holds nothing, and it is worth more to a reader deciding
   whether to trust us than three cards of "outcome pending" ever were. */
if (!$caseStudies): ?>
            <div class="cs-standard" data-r="rise">
                <p class="eyebrow">How we write these up</p>
                <h2>Every engagement, <span class="soft">in the same three parts</span></h2>
                <p class="lead">
                    Write-ups are published here once the client has signed off on the numbers in
                    them. No composite clients, and no metric we cannot attribute to a single
                    engagement.
                </p>

                <div class="steps-row cs-standard-list">
                    <div class="step-item">
                        <span class="step-num">+01</span>
                        <span class="step-title">Problem</span>
                        <p class="step-text">The situation in the client's own words &mdash; what was breaking, what it was costing, and what had already been tried.</p>
                    </div>
                    <div class="step-item">
                        <span class="step-num">+02</span>
                        <span class="step-title">Approach</span>
                        <p class="step-text">What our team actually did across web, content, marketing, security and store operations, and the order we did it in.</p>
                    </div>
                    <div class="step-item">
                        <span class="step-num">+03</span>
                        <span class="step-title">Result</span>
                        <p class="step-text">One measured outcome, taken from the client's own analytics or accounts, over a stated period &mdash; not a round number chosen because it reads well.</p>
                    </div>
                </div>

                <p class="cs-standard-foot">
                    Want to see the working before you commit? Ask on the
                    <a href="/contact">consultation call</a> &mdash; we will walk you through a
                    live engagement in this exact shape, under NDA if you prefer.
                </p>
            </div>
<?php else: ?>
            <div class="cs-list">
<?php foreach ($caseStudies as $i => $w):
        $tags = array_values(array_filter(array_map('trim', explode(',', (string)$w['tags'])), 'strlen'));

        // Both halves or neither. A metric block reading "— Outcome" (or a bare
        // number with no unit) is the one thing on this page a reader stops on.
        $hasMetric = trim((string)$w['metric_value']) !== '' && trim((string)$w['metric_label']) !== '';
?>
                <article class="cs-item" id="case-study-<?= (int)$i ?>"
                         data-fx="<?= $i % 2 ? 'in-right' : 'in-left' ?>"
                         style="--travel: 9%; --turn: 2deg;">
                    <div class="cs-item-head">
                        <?php /* No cover image. The illustrative sector photographs that used
                           to sit here were stock — an office, a shop, a cafe — beside a real
                           client's name, which reads as "this is their premises" whatever the
                           alt text says. They are gone, and the whole photo set with them.
                           The replacement is a generated sector plate, not another photograph;
                           until it lands the card is text, which is the honest form. */ ?>
                        <span class="badge badge-soft"><?= e((string)$w['sector']) ?></span>
                        <h2><?= e((string)$w['client_name']) ?></h2>
<?php if ($hasMetric): ?>
                        <p class="cs-metric"><?= e((string)$w['metric_value']) ?></p>
                        <p class="cs-metric-label"><?= e((string)$w['metric_label']) ?></p>
<?php endif; ?>
<?php if ($tags): ?>
                        <div class="cluster cs-tags">
                            <?php foreach ($tags as $t): ?>
                                <?php $tSlug = $serviceSlugByTitle[mb_strtolower($t, 'UTF-8')] ?? null; ?>
                                <?php if ($tSlug !== null): ?>
                                    <a class="chip" href="/<?= e($tSlug) ?>"><?= e($t) ?></a>
                                <?php else: ?>
                                    <span class="chip"><?= e($t) ?></span>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
<?php endif; ?>
                    </div>

                    <div class="cs-item-body">
<?php if (trim((string)$w['problem']) !== ''): ?>
                        <div class="cs-part">
                            <span class="step-num">+01</span>
                            <h3>Problem</h3>
                            <p><?= nl2br(e((string)$w['problem'])) ?></p>
                        </div>
<?php endif; ?>
<?php if (trim((string)$w['action']) !== ''): ?>
                        <div class="cs-part">
                            <span class="step-num">+02</span>
                            <h3>Approach</h3>
                            <p><?= nl2br(e((string)$w['action'])) ?></p>
                        </div>
<?php endif; ?>
<?php if ($hasMetric): ?>
                        <div class="cs-part">
                            <span class="step-num">+03</span>
                            <h3>Result</h3>
                            <p><strong><?= e((string)$w['metric_value']) ?></strong> &mdash; <?= e((string)$w['metric_label']) ?></p>
                        </div>
<?php endif; ?>
                    </div>
                </article>
<?php endforeach; ?>
            </div>
<?php endif; ?>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Want a result like these?';
    $ctaTitle   = 'Let us look at what you already have.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
