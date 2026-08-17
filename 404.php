<?php
require __DIR__ . '/inc/bootstrap.php';

/**
 * Custom 404. Wired up via ErrorDocument in .htaccess.
 *
 * Apache includes this file rather than redirecting, so the URL the visitor
 * typed stays in the address bar — which is what we want — but it also means
 * PHP's own response code starts at 200. Setting it explicitly is what makes
 * this a real 404 to crawlers rather than a soft-404 that gets indexed.
 */
http_response_code(404);

$page = [
    'id'        => '',
    'title'     => 'Page not found | ' . SITE_NAME,
    'desc'      => 'That page does not exist. Browse our services or get in touch with the Rafly team.',
    'bodyClass' => 'page-notice',
    'noindex'   => true,
];
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">
    <section class="section page-head">
        <div class="container container-narrow notice-wrap">
            <p class="notice-code" aria-hidden="true">404</p>
            <h1>We can't find that page.</h1>
            <p class="lead">
                The link may be out of date, or the address may have a typo. Nothing is broken
                on your side &mdash; here's where most people are heading.
            </p>

            <nav class="notice-links" aria-label="Popular pages">
                <a class="notice-link" href="/">
                    <span class="icon-box"><?= icon('compass') ?></span>
                    <span><strong>Home</strong>What Rafly does, and how the bundles work</span>
                </a>
                <a class="notice-link" href="/#services">
                    <span class="icon-box"><?= icon('layers') ?></span>
                    <span><strong>Services</strong>All five, and how they fit together</span>
                </a>
                <a class="notice-link" href="/pricing">
                    <span class="icon-box"><?= icon('package') ?></span>
                    <span><strong>Packages</strong>Starter, Growth and Enterprise</span>
                </a>
                <a class="notice-link" href="/blog">
                    <span class="icon-box"><?= icon('file-pen') ?></span>
                    <span><strong>Blog</strong>Notes from the team doing the work</span>
                </a>
            </nav>

            <div class="cluster cluster-center notice-actions">
                <button type="button" class="btn btn-pill" data-modal-open="consultationModal">
                    Get a free consultation
                </button>
                <a class="btn btn-line" target="_blank" rel="noopener"
                   href="<?= e(whatsapp_link('Hi Rafly team, I hit a broken link on your website.')) ?>">
                    <?= icon('whatsapp', 'icon-fill') ?> Message us
                </a>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
