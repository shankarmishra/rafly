<?php
/**
 * Core team.
 *
 * Each member is a native <details> card: collapsed it shows the photo, name,
 * role and brief line; opening it reveals the full bio and the profile links.
 *
 * <details> rather than the FAQ accordion pattern on purpose. The accordion is
 * a button toggled by js/ui.js which force-closes its siblings — correct for an
 * FAQ, wrong for a team grid where several cards may usefully be open at once.
 * <details> also needs no JavaScript at all and gets keyboard and screen-reader
 * behaviour from the browser.
 *
 * The previous build layered a cloned looping strip and a custom overlay dialog
 * on top of this, driven by a JSON payload and a page-specific script. Both are
 * gone: they were roughly 330 lines of JS and CSS to show information the card
 * already holds, and the clones meant a click had to be resolved back through
 * data-team-index to work out which person had been clicked. Nothing a visitor
 * can read here was lost with them.
 */

require __DIR__ . '/inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home', 'url' => '/'],
    ['name' => 'Team', 'url' => '/team'],
];

$page = [
    'id'        => 'team',
    'title'     => 'Our Team | Rafly Digital Growth',
    'desc'      => 'The people behind Rafly — the developers, marketers and strategists who run every bundled package.',
    'bodyClass' => 'page-team',
    'styles'    => ['home', 'team'],
    'schema'    => [
        [
            '@type'       => 'AboutPage',
            '@id'         => schema_id('team'),
            'url'         => SITE_ORIGIN . '/team',
            'name'        => 'Our Team | Rafly Digital Growth',
            'description' => 'The people behind Rafly — the developers, marketers and strategists who run every bundled package.',
            'isPartOf'    => ['@id' => schema_id('website')],
            'about'       => ['@id' => schema_id('organization')],
        ],
        schema_breadcrumbs($crumbs),
    ],
];

/**
 * Read before any output, and guarded, so a database that is down degrades to
 * an empty section rather than a fatal.
 */
$team = team_all();

/* team_all() no longer returns unfinished profiles at all, so every person
   reaching this page is a real, published one and can be claimed as such in
   structured data. The is_placeholder filter used to live here, applied to the
   schema only — which meant an unfinished profile was hidden from Google and
   shown to the visitor, exactly the wrong way round. */
foreach ($team as $p) {
    $page['schema'][] = schema_person($p);
}

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <section class="section page-head">
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Core team</p>
                    <h1 class="display">The people behind <span class="soft">every package.</span></h1>
                </div>
                <p class="lead">
                    One coordinated team rather than five separate vendors &mdash; these are the
                    people who actually do the work, and the ones you will meet on the
                    consultation call.
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot" id="team">
        <div class="container">
<?php if (!$team): ?>
            <?php /* Not "profiles are on their way". A team page whose content is a
                     promise of content is worse than one that simply offers the
                     introduction directly — which is what a visitor on this page
                     wanted in the first place. */ ?>
            <div class="panel team-empty" data-r="rise">
                <h2>Meet the team directly</h2>
                <p class="lead">
                    We introduce you to the exact people who will work on your account &mdash; on
                    the consultation call, before anything is signed.
                </p>
                <p class="cluster">
                    <a class="btn btn-pill" target="_blank" rel="noopener"
                       href="<?= e(whatsapp_link('Hi Rafly team, I would like to know who I would be working with.')) ?>">
                        <?= icon('whatsapp', 'icon-fill') ?> Message us
                    </a>
                    <a class="btn btn-line" href="/contact">Send requirements</a>
                </p>
            </div>
<?php else: ?>
            <div class="grid grid-3 team-grid" data-r="group">
<?php foreach ($team as $i => $p):
            $hasLinks = $p['github_url'] !== '' || $p['linkedin_url'] !== '';
            $alt      = (string)($p['photo_alt'] !== '' ? $p['photo_alt'] : $p['name']);
?>
                <details class="team-card">
                    <summary class="team-card-head">
                        <span class="team-avatar">
<?php if ($p['photo'] !== null): ?>
                            <?php /* site_path(), unlike admin/media.php, so the photo
                                     still resolves when the site is installed in a
                                     subfolder. Do not "simplify" this to a bare
                                     /uploads/ path. */ ?>
                            <img src="<?= e(site_path('/uploads/' . rawurlencode((string)$p['photo']))) ?>"
                                 alt="<?= e($alt) ?>" width="128" height="128" loading="lazy" decoding="async">
<?php else: ?>
                            <span class="avatar avatar-lg avatar-t<?= (int)(($i % 6) + 1) ?>" aria-hidden="true"><?= e(mb_substr((string)$p['name'], 0, 1)) ?></span>
<?php endif; ?>
                        </span>

                        <span class="team-card-id">
                            <strong><?= e((string)$p['name']) ?></strong>
                            <span class="team-role"><?= e((string)$p['role']) ?></span>
                            <span class="team-brief"><?= e((string)$p['brief']) ?></span>
                        </span>

                        <span class="team-arrow" aria-hidden="true"><?= icon('chevron-down') ?></span>
                    </summary>

                    <div class="team-card-body">
<?php if ($p['bio'] !== ''): ?>
                        <p><?= e((string)$p['bio']) ?></p>
<?php endif; ?>
<?php if ($hasLinks): ?>
                        <div class="team-links">
<?php /* Visible text stays short; the aria-label carries the name, because a
         screen reader reading the card out of context otherwise hears six
         identical "GitHub" links on one page. */ ?>
<?php if ($p['github_url'] !== ''): ?>
                            <a class="chip" href="<?= e((string)$p['github_url']) ?>" target="_blank" rel="noopener noreferrer"
                               aria-label="<?= e($p['name'] . ' on GitHub') ?>">
                                <?= icon('github', 'icon-fill') ?><span>GitHub</span>
                            </a>
<?php endif; ?>
<?php if ($p['linkedin_url'] !== ''): ?>
                            <a class="chip" href="<?= e((string)$p['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer"
                               aria-label="<?= e($p['name'] . ' on LinkedIn') ?>">
                                <?= icon('linkedin', 'icon-fill') ?><span>LinkedIn</span>
                            </a>
<?php endif; ?>
                        </div>
<?php endif; ?>
                    </div>
                </details>
<?php endforeach; ?>
            </div>

            <p class="team-note">
                Every card opens on its own &mdash; several can be open at once. The people
                listed here are the people who work on your account; nobody is subcontracted
                out without telling you first.
            </p>
<?php endif; ?>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Want to meet them?';
    $ctaTitle   = 'The first call is with the people who do the work.';
    $ctaText    = 'No account manager relaying messages. You speak to the team that will build, write, secure and run the thing.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
