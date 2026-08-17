<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * A real /contact page.
 *
 * Until now every "contact us" on the site pointed at the homepage anchor
 * #contact, which means anyone arriving from a search for "rafly contact"
 * landed on a 15,000px homepage and had to scroll to the bottom. Search
 * engines had nothing to rank for the intent either. Same form, same server
 * path — just a page of its own.
 */

$crumbs = [
    ['name' => 'Home',    'url' => '/'],
    ['name' => 'Contact', 'url' => '/contact'],
];

$page = [
    'id'        => 'contact',
    'title'     => 'Contact Rafly | Talk to one team about all of it',
    'desc'      => 'Tell us what is slowing your growth down. One point of contact for web development, content, marketing, security and e-commerce support. We reply within one working day.',
    'bodyClass' => 'page-contact',
    'styles'    => ['home'],
    'canonical' => 'contact',
    'schema'    => [schema_breadcrumbs($crumbs)],
];

$addr  = setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306');
$hours = setting('contact.hours', 'Mon - Fri, 09:00 - 18:00 IST');

$WAYS = [
    ['whatsapp', 'WhatsApp', 'Fastest for a quick question. Usually answered the same hour during working hours.',
     whatsapp_link('Hi Rafly, I have a question about your bundled packages.'), 'Message us', true],
    ['mail', 'Email', 'Best when you have a brief, a spec or files to send across.',
     'mailto:' . CONTACT_EMAIL, CONTACT_EMAIL, false],
    ['phone', 'Phone', 'For anything urgent — a broken checkout, a site down, a security scare.',
     'tel:' . preg_replace('/[^0-9+]/', '', CONTACT_PHONE), CONTACT_PHONE, false],
];

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
                    <p class="eyebrow">Contact</p>
                    <h1 class="display">Talk to one team <span class="soft">about all of it.</span></h1>
                </div>
                <p class="lead">
                    No call centre, no ticket queue, no account manager relaying messages to
                    someone you never meet. You get one point of contact who knows your project.
                </p>
            </div>
        </div>
    </section>

    <section class="section-bot">
        <div class="container">
            <div class="grid grid-3" data-r="group">
                <?php foreach ($WAYS as [$ico, $title, $copy, $href, $label, $external]): ?>
                    <article class="card card-hover way-card">
                        <div class="card-body">
                            <span class="icon-box icon-box-lg"><?= icon($ico) ?></span>
                            <h2 class="card-title"><?= e($title) ?></h2>
                            <p class="card-text"><?= e($copy) ?></p>
                            <div class="card-foot">
                                <span class="link-arrow"><?= e($label) ?> <?= icon('arrow-right') ?></span>
                            </div>
                        </div>
                        <a class="card-link" href="<?= e($href) ?>"
                           <?= $external ? 'target="_blank" rel="noopener"' : '' ?>
                           aria-label="<?= e($title . ' — ' . $label) ?>"></a>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="section band-blue" id="form">
        <div class="container">
            <div class="split split-wide-l split-top">
                <div data-r="left">
                    <p class="eyebrow on-dark">Start a conversation</p>
                    <h2>Tell us what's <span class="soft">slowing you down</span></h2>
                    <p class="lead">
                        The more you can say about what you already have and what is not working,
                        the more useful our first reply will be. There is no pitch deck until we
                        understand the actual problem.
                    </p>

                    <ul class="list-check contact-assurances">
                        <li><?= icon('check') ?><span>A person replies within one working day</span></li>
                        <li><?= icon('check') ?><span>No obligation, and no sales sequence</span></li>
                        <li><?= icon('check') ?><span>NDA on request; full IP transfers to you</span></li>
                    </ul>

                    <div class="deflist contact-details">
                        <div class="deflist-row">
                            <?= icon('map-pin') ?>
                            <span><span class="deflist-label">Office</span><span class="deflist-value"><?= e($addr) ?></span></span>
                        </div>
                        <div class="deflist-row">
                            <?= icon('hourglass') ?>
                            <span><span class="deflist-label">Hours</span><span class="deflist-value"><?= e($hours) ?></span></span>
                        </div>
                    </div>
                </div>

                <div class="contact-card" data-r="right">
                    <h3 class="contact-card-title">Send us your requirements</h3>
                    <?php
                    $formId      = 'contactPageForm';
                    $submitLabel = 'Submit requirements';
                    require __DIR__ . '/partials/lead-form.php';
                    ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
