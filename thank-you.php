<?php
require __DIR__ . '/inc/bootstrap.php';

// One-shot page: reachable only immediately after a successful POST.
// Without this guard it would be a crawlable, shareable "success" URL.
if (empty($_SESSION['lead_ok'])) {
    header('Location: /', true, 302);
    exit;
}
unset($_SESSION['lead_ok']);

$page = [
    'id'        => 'contact',
    'title'     => 'Thank You | ' . SITE_NAME,
    'desc'      => 'Your request has been received. The Rafly team will be in touch shortly.',
    'bodyClass' => 'page-notice',
    'noindex'   => true,
];
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">
    <section class="section page-head">
        <div class="container container-narrow notice-wrap">
            <span class="notice-icon"><?= icon('check') ?></span>
            <h1>Thank you &mdash; we've got your request.</h1>
            <p class="lead">
                A member of the <?= e(SITE_NAME) ?> team will review your requirements and get
                back to you within one business day.
            </p>
            <p class="muted">Need something urgently? Message us directly and we'll pick it up straight away.</p>
            <div class="cluster cluster-center notice-actions">
                <a class="btn btn-pill" target="_blank" rel="noopener"
                   href="<?= e(whatsapp_link('Hi Rafly team, I just submitted an enquiry through your website.')) ?>">
                    <?= icon('whatsapp', 'icon-fill') ?> Chat on WhatsApp
                </a>
                <a class="btn btn-line" href="/">Back to homepage</a>
            </div>
        </div>
    </section>
</main>
<?php require __DIR__ . '/partials/tail.php'; ?>
