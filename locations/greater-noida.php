<?php
/**
 * The one real location page on the site.
 *
 * Rafly has exactly one registered office — Greater Noida West, part of the
 * Delhi NCR metro — so this is exactly one page, not a templated city-name
 * generator. A second, third and fourth near-identical "we serve <city>" page
 * with no real local presence behind it is the doorway-page pattern search
 * engines penalise; one honest page for the one real place is not.
 *
 * Lives in its own locations/ directory (rather than flat at the project
 * root like every other page) because the IA is meant to grow the same way
 * if a second real office is ever opened — /locations/<slug> — without a
 * rename. Every require below therefore climbs one level (__DIR__ . '/../…'),
 * the same pattern admin/*.php already uses for its own subdirectory.
 */

require __DIR__ . '/../inc/bootstrap.php';

$crumbs = [
    ['name' => 'Home',                    'url' => '/'],
    ['name' => BUSINESS_GEO_LOCALITY,     'url' => '/locations/greater-noida'],
];

$title = 'Web Development & Digital Growth in ' . BUSINESS_GEO_LOCALITY . ' | ' . SITE_NAME;
$desc  = 'Rafly is based in ' . BUSINESS_GEO_LOCALITY . ', ' . BUSINESS_GEO_REGION
       . ' — web development, security, marketing, content and e-commerce support for businesses there and across ' . BUSINESS_GEO_COUNTRY . '.';

$page = [
    'id'        => 'locations',
    'title'     => $title,
    'desc'      => $desc,
    'bodyClass' => 'page-location',
    'styles'    => ['home', 'about'],
    'canonical' => 'locations/greater-noida',
    'schema'    => [
        schema_webpage('locations/greater-noida', $title, $desc, 'WebPage'),
        schema_breadcrumbs($crumbs),
    ],
];

$services = array_values(services_all());
$addr     = setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306');
$hours    = setting('contact.hours', 'Mon - Fri, 09:00 - 18:00 IST');

require __DIR__ . '/../partials/head.php';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/social-rail.php';
?>
<main id="main">
    <section class="section page-head">
        <?php require __DIR__ . '/../partials/head-object.php'; ?>
        <div class="container">
            <?= breadcrumbs($crumbs) ?>
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">Based in <?= e(BUSINESS_GEO_LOCALITY) ?></p>
                    <h1 class="display">Digital growth, <span class="soft">run from <?= e(BUSINESS_GEO_LOCALITY) ?>.</span></h1>
                </div>
                <p class="lead">
                    Rafly is registered and based in <?= e(BUSINESS_GEO_LOCALITY) ?>, part of the
                    <?= e(BUSINESS_GEO_REGION) ?> metro. We work with businesses across
                    <?= e(BUSINESS_GEO_LOCALITY) ?> and <?= e(BUSINESS_GEO_REGION) ?> in person when it
                    helps, and remotely with businesses anywhere in <?= e(BUSINESS_GEO_COUNTRY) ?> — the
                    same bundled team and the same process either way.
                </p>
            </div>
        </div>
    </section>

    <?php /* ===================== OFFICE DETAILS ===================== */ ?>
    <section class="section-bot">
        <div class="container">
            <div class="split split-wide-l">
                <div>
                    <p class="eyebrow">Office</p>
                    <h2>Where to find us</h2>
                    <div class="deflist contact-details" style="margin-top:1.5rem">
                        <div class="deflist-row">
                            <?= icon('map-pin') ?>
                            <span><span class="deflist-label">Address</span>
                                <span class="deflist-value">
                                    <a href="https://maps.google.com/?q=<?= rawurlencode($addr) ?>" target="_blank" rel="noopener"><?= e($addr) ?></a>
                                </span>
                            </span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('hourglass') ?>
                            <span><span class="deflist-label">Hours</span><span class="deflist-value"><?= e($hours) ?></span></span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('phone') ?>
                            <span><span class="deflist-label">Phone</span>
                                <span class="deflist-value"><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', CONTACT_PHONE)) ?>"><?= e(CONTACT_PHONE) ?></a></span>
                            </span>
                        </div>
                    </div>
                    <p style="margin-top:2rem">
                        <a class="btn btn-pill" href="/contact">Get in touch <?= icon('arrow-right') ?></a>
                    </p>
                </div>
                <div>
                    <p class="eyebrow">Why it matters</p>
                    <h2>Local when it helps, <span class="soft">remote when it doesn't</span></h2>
                    <p class="lead">
                        Most of what we deliver — builds, campaigns, content, security reviews,
                        store operations — travels perfectly well over a call and a shared
                        document. Being based in <?= e(BUSINESS_GEO_LOCALITY) ?> means an in-person
                        meeting is on the table whenever a project genuinely benefits from one,
                        for businesses across <?= e(BUSINESS_GEO_REGION) ?>.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <?php /* ===================== SERVICES, LINKED ===================== */ ?>
    <section class="section band-soft">
        <div class="container">
            <div class="sec-head-split">
                <div>
                    <p class="eyebrow">What we do here</p>
                    <h2>Five services, <span class="soft">one team</span></h2>
                </div>
                <p class="lead">Every one of these is delivered from <?= e(BUSINESS_GEO_LOCALITY) ?>, available on its own or bundled.</p>
            </div>

            <ol class="rail" data-r="group">
                <?php foreach ($services as $n => $svc): ?>
                    <li class="rail-item">
                        <span class="rail-num"><?= str_pad((string)($n + 1), 2, '0', STR_PAD_LEFT) ?></span>
                        <span class="icon-box"><?= icon($svc['icon']) ?></span>
                        <span class="rail-body">
                            <span class="rail-title"><?= e($svc['title']) ?></span>
                            <span class="rail-text"><?= e($svc['card']) ?></span>
                        </span>
                        <span class="rail-go"><?= icon('arrow-right') ?></span>
                        <a class="card-link" href="/<?= e($svc['slug']) ?>" aria-label="<?= e($svc['title']) ?>"></a>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <?php
    $ctaEyebrow = 'Based in ' . BUSINESS_GEO_LOCALITY . '?';
    $ctaTitle   = 'Let\'s talk about what you need.';
    $ctaText    = 'Whether you would rather meet in person or start on a call, tell us what you need and we will put together a clear plan.';
    require __DIR__ . '/../partials/cta-band.php';
    ?>
</main>
<?php require __DIR__ . '/../partials/tail.php'; ?>
