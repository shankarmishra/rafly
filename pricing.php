<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * Dedicated pricing page. The homepage's #pricing section already shows the
 * same three cards; this exists because every CTA on the site otherwise
 * routes straight to WhatsApp with zero price signal, which loses any visitor
 * who won't message a stranger before knowing a ballpark figure. Ranges are
 * indicative (price_text, admin-editable via admin/bundles.php) — not exact
 * quotes, which is why "Get exact quote" stays on every card rather than
 * disappearing.
 */

$crumbs = [
    ['name' => 'Home',    'url' => '/'],
    ['name' => 'Pricing', 'url' => '/pricing'],
];

$PRICING_FAQ = [
    ['q' => 'Why is there no price list?',
     'a' => 'Because a bundle is scoped to what you already have. A store with a working checkout and no content needs a different package from a five-page brochure site that has never been reviewed for security. We quote after one call, and the number does not move afterwards.'],
    ['q' => 'What is not included?',
     'a' => 'Third-party costs you would pay anyway — hosting, domains, ad spend, paid plugins and licences. Those stay in your name and on your card, so you keep control of them if you ever leave.'],
    ['q' => 'Can I start with one service instead of a bundle?',
     'a' => 'Yes. The bundle is where the value is, but plenty of engagements start with a single piece of work — usually a security review or a site rebuild — and grow from there.'],
    ['q' => 'How does payment work?',
     'a' => 'A deposit to begin, then staged payments against agreed milestones. Full IP ownership transfers to you on final payment.'],
];

$page = [
    'id'        => 'pricing',
    'title'     => 'Pricing | Rafly Digital Growth',
    'desc'      => 'Indicative pricing for our bundled packages — Starter, Growth and Enterprise. Get an exact quote for your specific requirements.',
    'bodyClass' => 'page-pricing',
    'styles'    => ['home'],
    'schema'    => [schema_breadcrumbs($crumbs), schema_faq($PRICING_FAQ)],
];

/* Read once, before output. The hero copy below promises "ranges" — that
   promise is only made when at least one bundle actually carries a price, so
   the page can never say "ranges below" above three cards with no numbers on
   them. With the admin's price fields empty the same page speaks about scope
   instead, which is true in both states. */
$bundles   = bundles_all();
$hasPrices = (bool)array_filter($bundles, static fn (array $b): bool => trim((string)($b['price_text'] ?? '')) !== '');


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
                    <p class="eyebrow">Pricing</p>
                    <?php if ($hasPrices): ?>
                        <h1 class="display">Indicative pricing, <span class="soft">before you message us.</span></h1>
                    <?php else: ?>
                        <h1 class="display">Three bundles. <span class="soft">One scope, one price.</span></h1>
                    <?php endif; ?>
                </div>
                <p class="lead">
                    <?php if ($hasPrices): ?>
                        Ranges below are a starting point, not a final invoice &mdash; every engagement
                        is scoped to what you actually need during a free consultation.
                    <?php else: ?>
                        Every engagement is scoped on a free call and priced as a single package
                        &mdash; web, content, marketing, security and e-commerce support under one
                        number, instead of five invoices from five vendors.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot">
        <div class="container">
            <div class="grid grid-3 price-grid" data-r="group">
                <?php foreach ($bundles as $i => $t) { require __DIR__ . '/partials/price-card.php'; } ?>
            </div>
            <p class="price-note">
                Not sure which package fits? <a href="/contact">Request a free consultation</a>
                and we'll help you work it out. Nothing starts until the scope and the number are
                both in writing.
            </p>
        </div>
    </section>

    <section class="section band-soft">
        <div class="container">
            <div class="faq-card" data-r="rise">
                <div class="faq-aside">
                    <p class="eyebrow">Before you ask</p>
                    <h2>What a bundle <span class="scribble">actually covers<svg viewBox="0 0 300 24" aria-hidden="true" preserveAspectRatio="none"><path d="M4 17C58 6 120 4 178 8c38 3 74 6 118 3"/></svg></span></h2>
                    <p class="muted">The four questions that come up on almost every first call.</p>
                    <div class="faq-aside-actions">
                        <button type="button" class="btn btn-pill" data-modal-open="consultationModal">
                            Get an exact quote <?= icon('arrow-right') ?>
                        </button>
                    </div>
                </div>
                <div class="faq-list">
                    <div class="accordion" data-accordion="single">
                        <?php foreach ($PRICING_FAQ as $i => $f): ?>
                            <div class="accordion-item">
                                <button type="button" class="accordion-trigger" aria-expanded="false" aria-controls="pfaq-panel-<?= $i ?>" id="pfaq-trigger-<?= $i ?>">
                                    <span><?= e($f['q']) ?></span>
                                    <span class="accordion-icon" aria-hidden="true"><?= icon('chevron-down') ?></span>
                                </button>
                                <div class="accordion-panel" id="pfaq-panel-<?= $i ?>" role="region" aria-labelledby="pfaq-trigger-<?= $i ?>">
                                    <div><p><?= e($f['a']) ?></p></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $ctaTitle = 'Ready to put a number on it?'; require __DIR__ . '/partials/cta-band.php'; ?>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
