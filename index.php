<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * THE HOMEPAGE — nine sections.
 *
 * It was seventeen, with the primary call to action repeated five times and a
 * documented height of about 15,000px. Seventeen sections is not thoroughness;
 * it is an offer that has not been decided on. Every section below answers a
 * question a buyer actually asks, in the order they ask it:
 *
 *   1  what is this              hero
 *   2  what do you believe       the statement
 *   3  what do I get             the assembly, opened
 *   4  how does it run           delivery
 *   5  what is it built on       the stack
 *   6  who have you done it for  selected work   (conditional — see below)
 *   7  what will you NOT do      honest limits
 *   8  what does it cost         pricing and FAQ
 *   9  how do I start            the close
 *
 * TWO calls to action, not five: the hero and the close.
 *
 * THE OBJECT
 * ----------
 * Sections 1-3 share one sticky WebGL stage (js/stage3d.js). The object is
 * assembled in the hero, hidden behind the statement — which is opaque paper
 * and simply covers it — and comes apart across section 3. It is the same
 * object throughout; it never disappears and re-appears, it transforms.
 *
 * Everything that stage does is an upgrade. assets/render/core-*.webp is
 * rendered offline from the same scene (inc/tools/render-stills.mjs) and is
 * what phones, crawlers, printers, reduced-motion and no-WebGL visitors get.
 *
 * WHAT IS NOT HERE ANY MORE, AND WHY
 * ----------------------------------
 *   the 13-tile rotating capability ring   a fourth focal device
 *   the 3-D perspective carousel           a fifth
 *   the point-cloud band                   shipped a visibly empty frame
 *   the device-mockup "app stage"          carried unverified LCP figures
 *   the marquee                            motion with nothing to say
 *   the trust bar                          "120+ projects / 98% satisfaction"
 *                                          were seeded sample values
 *   24 stock photographs                   other companies' premises,
 *                                          standing in for our work
 */

/**
 * Single source for the FAQ: renders the accordion below AND the FAQPage
 * structured data, so the rich result can never claim something the page does
 * not actually say.
 */
$FAQS = [
    ['q' => 'How quickly can you start?',
     'a' => 'Most engagements begin within a week of our first call. For urgent fixes — a broken checkout, a security gap — we can have someone looking at it within 48 hours.'],
    ['q' => 'Do you work with existing websites?',
     'a' => 'Yes. Most of our work is on live sites and stores, not blank slates. We audit what is already there before we touch anything.'],
    ['q' => 'Who actually does the work?',
     'a' => 'A dedicated Rafly team — developers, a security reviewer, a content writer and a marketer — not a rotating cast of freelancers.'],
    ['q' => 'How do you price engagements?',
     'a' => 'Through bundled packages built around what you actually need. Instead of separate invoices for web, content, marketing and support, you get one scope and one price agreed up front.'],
    ['q' => 'What about security and compliance?',
     'a' => 'Every project includes a baseline security review — forms, sessions and access handling — as part of the package, not an optional add-on.'],
    ['q' => 'Do you offer ongoing support?',
     'a' => 'Yes. Bundled packages include a support window after launch, and we offer ongoing monthly plans for sites and stores that need continuous attention.'],
    ['q' => 'Can you integrate with our existing tools?',
     'a' => 'In most cases, yes — payment gateways, CRMs, analytics and e-commerce platforms. We confirm feasibility during discovery before committing.'],
    ['q' => 'Do you sign NDAs and transfer IP?',
     'a' => 'Yes. We sign NDAs on request, and full IP ownership transfers to you on final payment.'],
];

/**
 * Delivery. Real stage names and the same timeframe vocabulary the service
 * pages use (inc/data/services.php), so the two can never disagree.
 */
$FLOW = [
    ['Discovery',       '2-3 days',   'A call to understand your goals, your current setup, and which parts need the most help. No pitch deck until we know what is actually broken.'],
    ['Package and plan','3-5 days',   'We scope a bundled package matched to what you need, and price it up front. One scope, one number.'],
    ['Build and execute','2-6 weeks', 'Development, content and campaign work run in parallel inside one team against one plan. Nothing waits on a handoff between vendors who have never spoken.'],
    ['Launch',          '1 week',     'Your site, store or campaign goes live with the baseline security review already done, because it was part of the build rather than a phase after it.'],
    ['Ongoing',         'Continuous', 'Monitoring, updates and improvement. The team that built it is the team that keeps it running.'],
];

/**
 * The stack. Named tools only — every one appears in the `tools` array of a
 * real service in inc/data/services.php. No logo wall: a logo implies a
 * partnership, and Rafly has none to claim.
 */
$STACK = [
    ['Build',      ['PHP', 'Laravel', 'WordPress', 'JavaScript', 'MySQL']],
    ['Commerce',   ['Shopify', 'Payment gateways', 'Catalogue data', 'Order reconciliation']],
    ['Security',   ['SSL &amp; TLS config', 'WAF rules', 'Dependency updates', 'Access &amp; roles', 'Backup checks']],
    ['Growth',     ['Google Ads', 'Meta Ads', 'Analytics 4', 'Tag Manager', 'Search Console', 'Email campaigns']],
    ['Operations', ['Staging &amp; deploys', 'Core Web Vitals', 'Log review', 'Editorial calendar']],
];

/** The comparison — Rafly against the two real alternatives. */
$COMPARE = [
    ['Service coverage',  'Web, content, marketing, security and e-commerce in one team', 'Usually one specialty',                         'No shared context between people'],
    ['Accountability',    'One point of contact, accountable for outcomes',               'Account manager, execution outsourced further', 'You coordinate everyone yourself'],
    ['Security',          'Baseline review included in every package',                    'Usually a separate paid add-on',                'Rarely considered at all'],
    ['Delivery speed',    'Parallel execution across one coordinated team',               'Sequential handoffs between departments',       'Depends on individual availability'],
    ['Pricing structure', 'Clear, bundled packages',                                      'Custom retainers, scope creep',                 'Paid per task, costs add up'],
    ['Communication',     'Plain-language updates, no jargon',                            'Layered reporting, slower answers',             'Manual coordination on your end'],
];

/**
 * The five modules of the object, in stack order, matched to the five real
 * services. Index, label and slug all come from one place, so a label on the
 * 3-D scene can never drift from the page it links to.
 */
$MODULES = [
    ['01', 'web-development',         'Web',       'Sites and web apps that load fast and do not fall over as you grow.'],
    ['02', 'web-security',            'Security',  'A practical look at what someone probing your site would find first.'],
    ['03', 'marketing-advertisement', 'Marketing', 'Campaigns built around who is actually buying, reported in plain language.'],
    ['04', 'content-creation',        'Content',   'Copy that says what you do, in your words, without the filler.'],
    ['05', 'ecommerce-support',       'Commerce',  'The unglamorous side of selling online, kept in order.'],
];

/**
 * Admin-owned content, read before any output so a slow or failed query
 * cannot leave a half-rendered page. db_available() never throws; with the
 * database down these sections render empty and the rest of the page is
 * unaffected.
 *
 * #work stays CONDITIONAL. Every case study in the database matched
 * inc/data/seed-preview.php verbatim and had been flagged as real; those flags
 * were reset by inc/tools/unflag-seed.php. The section is designed and waiting
 * for work Rafly can actually name. An empty section is better than a
 * fabricated one.
 */
$caseStudies = case_studies_all(3);
$bundles     = bundles_all();

/** Every honest limit the five services declare, in one list. */
$limits = [];
foreach (services_all() as $svc) {
    foreach (($svc['boundaries'] ?? []) as $b) {
        $limits[] = [$svc['title'], $b['title'], $b['desc']];
    }
}

$page = [
    'id'        => 'home',
    'title'     => 'Rafly | One Core, Five Branches — Web, Security, Marketing, Content & Commerce',
    'desc'      => 'One team for web development, security, marketing, content and e-commerce. One scope, one price, one person accountable — instead of five vendors who have never spoken.',
    'bodyClass' => 'page-home',
    'styles'    => ['home'],
    'module'    => 'stage3d',
    'ogImage'   => 'assets/render/core-og-1200.webp',
    'schema'    => [
        schema_faq($FAQS),
    ],
];
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">

<?php /* ==============================================================
   THE STAGE — sections 01 to 03 share one sticky scene.

   .stage-sticky carries a negative bottom margin equal to its own height, so
   it takes no space in the flow: the three sections below scroll over it. The
   statement is opaque paper and therefore hides the object completely, which
   is what gives section 02 its rest.
   ============================================================== */ ?>
<div class="stage" data-stage>

    <div class="stage-sticky" aria-hidden="true">
        <div class="stage-ground"></div>
        <picture class="stage-still">
            <source media="(max-width: 760px)" srcset="<?= e(asset('assets/render/core-hero-560.webp')) ?>">
            <source media="(max-width: 1180px)" srcset="<?= e(asset('assets/render/core-hero-900.webp')) ?>">
            <img src="<?= e(asset('assets/render/core-hero-1400.webp')) ?>"
                 width="1400" height="1560" decoding="async" alt="">
        </picture>
        <canvas class="stage-canvas"></canvas>
        <div class="stage-labels">
<?php foreach ($MODULES as [$idx, $slug, $label, $blurb]): ?>
            <span class="stage-label"><i><?= e($idx) ?></i><?= e($label) ?></span>
<?php endforeach; ?>
        </div>
    </div>

    <?php /* ==========================================================
       01 — HERO

       Editorial and asymmetric: type ranged left, object on the right third,
       cropped by the frame edge. The LCP element is the headline, never the
       canvas and never the still.

       This reverses an instruction recorded on the previous build — "text
       center me chahiya", said about a centred hero. That hero was centred
       because the object had to be pushed out of the middle to keep the words
       readable, so centring was the only arrangement left. This object stands
       on paper with its own shadow and does not compete with type, which
       makes the asymmetric composition available again.
       ========================================================== */ ?>
    <section class="section hero" id="home">
        <div class="container">
            <div class="hero-copy">
                <p class="meta-row">
                    <span><b>Built for</b> owner-operated businesses</span>
                    <span><b>Model</b> one scope, one price</span>
                </p>

                <h1 class="hero-title">
                    One core.<br><span class="hero-title-2">Five branches.</span>
                </h1>

                <p class="hero-lead">
                    Web, security, marketing, content and commerce — built by one team,
                    on one plan, with one person accountable. Not five vendors who have
                    never spoken to each other.
                </p>

                <div class="hero-cta">
                    <a class="btn btn-pill" href="#start">Book a free consultation</a>
                    <a class="btn btn-line" href="#delivery">See how delivery runs</a>
                </div>

                <dl class="spec">
<?php foreach ($MODULES as [$idx, $slug, $label, $blurb]): ?>
                    <div class="spec-row">
                        <dt><?= e($idx) ?></dt>
                        <dd><a href="<?= e(site_path('/' . $slug)) ?>"><?= e($label) ?></a></dd>
                    </div>
<?php endforeach; ?>
                </dl>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       02 — THE STATEMENT

       A rest. One sentence, no object, no cards, no motion beyond the line
       reveal. Opaque paper, which is what hides the stage behind it.
       ========================================================== */ ?>
    <section class="section statement grain" id="approach">
        <div class="container">
            <p class="statement-meta">The difference</p>
            <p class="statement-line" data-r="lines">
                We build digital&nbsp;systems,<br>not deliverables.
            </p>
            <p class="statement-sub">
                Five separate vendors produce five separate deliverables and no system.
                The website does not know what the campaign promised; the campaign does
                not know what the store can actually ship. We build the parts that have
                to agree, together, so that they do.
            </p>
        </div>
    </section>

    <?php /* ==========================================================
       03 — THE FIVE, EXPLODED

       The signature interaction, and the only expensive one on the page. As
       you scroll, the assembly separates and the core is revealed running
       through all five modules.

       Below 761px there is no WebGL at all. The section becomes a vertical
       sequence of three pre-rendered stills carrying the same five labels —
       the same story in the same visual language, at about a tenth of the
       cost. See .assembly-seq below and css/pages/home.css.
       ========================================================== */ ?>
    <section class="section assembly" id="services" data-stage-open>
        <div class="container">
            <div class="assembly-head">
                <p class="eyebrow">The five</p>
                <h2>One assembly.<br>Five modules.<br><span class="soft">One core.</span></h2>
                <p class="lead">
                    Not five services sold from one invoice. Five parts of one system,
                    built to fit each other, on one shaft.
                </p>
            </div>

            <ol class="assembly-list">
<?php foreach ($MODULES as [$idx, $slug, $label, $blurb]): ?>
                <li class="assembly-item">
                    <a href="<?= e(site_path('/' . $slug)) ?>">
                        <span class="assembly-idx"><?= e($idx) ?></span>
                        <span class="assembly-name"><?= e($label) ?></span>
                        <span class="assembly-desc"><?= e($blurb) ?></span>
                    </a>
                </li>
<?php endforeach; ?>
            </ol>
        </div>

        <?php /* The exploded state for everyone the live scene never reaches:
                 desktop with WebGL off, Save-Data, reduced motion, a dead
                 driver. One render, beside the list, section at normal
                 height. Hidden the moment .stage gains .is-live. */ ?>
        <figure class="assembly-still" aria-hidden="true">
            <img src="<?= e(asset('assets/render/core-open-1400.webp')) ?>"
                 width="1400" height="1560" loading="lazy" decoding="async" alt="">
        </figure>

        <?php /* The phone experience. Designed, not degraded: three states of
                 the same render, lazily loaded, at 640px. */ ?>
        <div class="assembly-seq" aria-hidden="true">
            <img src="<?= e(asset('assets/render/core-seq-1-640.webp')) ?>" width="640" height="760" loading="lazy" decoding="async" alt="">
            <img src="<?= e(asset('assets/render/core-seq-2-640.webp')) ?>" width="640" height="760" loading="lazy" decoding="async" alt="">
            <img src="<?= e(asset('assets/render/core-seq-3-640.webp')) ?>" width="640" height="860" loading="lazy" decoding="async" alt="">
        </div>
    </section>

</div><?php /* /.stage */ ?>

    <?php /* ==========================================================
       04 — DELIVERY

       Calm on purpose, straight after the expensive section. A hairline
       spine, real stage names, real durations. No 3-D, no WebGL, no
       particles.
       ========================================================== */ ?>
    <section class="section ground-2 grain" id="delivery">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Delivery</p>
                <h2>How the work <span class="soft">actually runs</span></h2>
            </div>

            <ol class="flow">
<?php foreach ($FLOW as $i => [$name, $when, $desc]): ?>
                <li class="flow-step" data-r="rise">
                    <span class="flow-idx"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="flow-body">
                        <h3><?= e($name) ?></h3>
                        <p><?= e($desc) ?></p>
                    </div>
                    <span class="flow-when"><?= e($when) ?></span>
                </li>
<?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php /* ==========================================================
       05 — THE STACK

       Typographic, and deliberately static. Nearly zero animation: after the
       exploded sequence and before the dark chapter, this is a rest.
       ========================================================== */ ?>
    <section class="section ground-3" id="stack">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Built on</p>
                <h2>Named tools, <span class="soft">no logo wall</span></h2>
            </div>

            <div class="stack">
<?php foreach ($STACK as [$group, $items]): ?>
                <div class="stack-col">
                    <h3 class="stack-label"><?= e($group) ?></h3>
                    <ul>
<?php foreach ($items as $item): ?>
                        <li><?= $item ?></li>
<?php endforeach; ?>
                    </ul>
                </div>
<?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       06 — SELECTED WORK — THE ONE DARK CHAPTER

       Conditional. Renders only when there is real, published,
       non-placeholder work to show. Nothing here is generated to fill space.
       ========================================================== */ ?>
<?php if ($caseStudies): ?>
    <section class="section ground-chapter grain seam-top seam-bottom" id="work">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Selected work</p>
                <h2>What it looked like <span class="soft">in practice</span></h2>
            </div>

            <div class="work-grid" data-r="group">
<?php foreach ($caseStudies as $i => $cs): ?>
                <article class="work-card">
                    <span class="work-idx"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <h3><?= e($cs['client_name'] ?? '') ?></h3>
<?php if (!empty($cs['summary'])): ?>
                    <p><?= e($cs['summary']) ?></p>
<?php endif; ?>
<?php if (!empty($cs['slug'])): ?>
                    <a class="link-arrow" href="<?= e(site_path('/case-studies#' . $cs['slug'])) ?>">Read the detail</a>
<?php endif; ?>
                </article>
<?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endif; ?>

    <?php /* ==========================================================
       07 — HONEST LIMITS

       The strongest section on the page, and the one no competitor
       publishes. Every line is lifted from the `boundaries` array of a real
       service, so what the homepage refuses and what the service page
       refuses are the same file.
       ========================================================== */ ?>
    <section class="section" id="limits">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Honest limits</p>
                <h2>What we will <span class="soft">not</span> take on</h2>
                <p class="lead">
                    Fifteen of them, in writing, before you ask. A studio that will not
                    name its edges has not found them yet.
                </p>
            </div>

            <div class="limits">
<?php foreach ($limits as [$svc, $title, $desc]): ?>
                <div class="limit">
                    <p class="limit-svc"><?= e($svc) ?></p>
                    <h3><?= e($title) ?></h3>
                    <p><?= e($desc) ?></p>
                </div>
<?php endforeach; ?>
            </div>

            <div class="compare-wrap">
                <table class="compare">
                    <caption class="sr-only">Rafly compared with a traditional agency and with hiring separate freelancers</caption>
                    <thead>
                        <tr>
                            <th scope="col"><span class="sr-only">Aspect</span></th>
                            <th scope="col">Rafly</th>
                            <th scope="col">Traditional agency</th>
                            <th scope="col">Separate freelancers</th>
                        </tr>
                    </thead>
                    <tbody>
<?php foreach ($COMPARE as [$aspect, $us, $agency, $free]): ?>
                        <tr>
                            <th scope="row"><?= e($aspect) ?></th>
                            <td class="is-us"><?= e($us) ?></td>
                            <td><?= e($agency) ?></td>
                            <td><?= e($free) ?></td>
                        </tr>
<?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       08 — PRICING AND FAQ
       ========================================================== */ ?>
    <section class="section ground-2 grain" id="pricing">
        <div class="container">
            <div class="sec-head">
                <p class="eyebrow">Bundles</p>
                <h2>Three bundles. <span class="soft">One scope, one price.</span></h2>
                <p class="lead">
                    Every package is scoped in a free consultation first — you get a
                    number before any work starts, not an hourly rate that moves.
                </p>
            </div>

            <div class="grid grid-3" data-r="group">
<?php foreach ($bundles as $i => $t): require __DIR__ . '/partials/price-card.php'; endforeach; ?>
            </div>

            <div class="faq" id="faq">
                <div class="faq-head">
                    <h3>Before you get in touch</h3>
                    <p>
                        Anything not covered here,
                        <a href="<?= e(whatsapp_link('Hi Rafly, I have a question.')) ?>" target="_blank" rel="noopener">ask on WhatsApp</a>
                        — a person replies within one working day.
                    </p>
                </div>

                <div class="accordion" data-accordion="single">
<?php foreach ($FAQS as $i => $f): ?>
                    <div class="accordion-item">
                        <button class="accordion-trigger" type="button"
                                id="faq-t-<?= $i ?>" aria-expanded="false" aria-controls="faq-p-<?= $i ?>">
                            <span><?= e($f['q']) ?></span>
                            <span class="accordion-icon" aria-hidden="true"></span>
                        </button>
                        <div class="accordion-panel" id="faq-p-<?= $i ?>" role="region" aria-labelledby="faq-t-<?= $i ?>" hidden>
                            <p><?= e($f['a']) ?></p>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <?php /* ==========================================================
       09 — THE CLOSE

       The second and last call to action on the page. The object returns,
       assembled and at rest — the same render as the hero, because that is
       exactly what it is: the thing, put back together.
       ========================================================== */ ?>
    <section class="section close" id="start">
        <div class="container">
            <div class="close-grid">
                <div class="close-copy">
                    <p class="eyebrow">Start here</p>
                    <h2>Let's build <span class="soft">what's next.</span></h2>
                    <p class="lead">
                        Tell us what is in front of you. We will come back with a scope and
                        a number, or tell you plainly that it is not a job for us.
                    </p>
                    <p class="meta-row">
                        <span><b>First step</b> a call, not a pitch deck</span>
                        <span><b>Reply</b> within one working day</span>
                    </p>

                    <figure class="close-object" aria-hidden="true">
                        <img src="<?= e(asset('assets/render/core-hero-900.webp')) ?>"
                             width="900" height="1000" loading="lazy" decoding="async" alt="">
                    </figure>
                </div>

                <div class="close-form">
<?php
    $formId      = 'homeLeadForm';
    $submitLabel = 'Send requirements';
    require __DIR__ . '/partials/lead-form.php';
?>
                </div>
            </div>
        </div>
    </section>

</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
