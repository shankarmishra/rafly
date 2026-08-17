<?php
require __DIR__ . '/inc/bootstrap.php';

$lastUpdated = 'July 18, 2026';

$page = [
    'id'        => '',
    'title'     => 'Privacy Policy | Rafly Digital Growth Partner',
    'desc'      => 'How Rafly Digital Growth Partner collects, uses, stores and protects your information.',
    'bodyClass' => 'page-legal',
    'styles'    => ['legal'],
    'schema'    => [schema_breadcrumbs($crumbs = [
        ['name' => 'Home',    'url' => '/'],
        ['name' => 'Privacy', 'url' => '/privacy'],
    ])],
];
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
?>
<main id="main">
    <section class="section page-head band-soft">
        <div class="container container-narrow">
            <?= breadcrumbs($crumbs) ?>
            <p class="eyebrow">Legal</p>
            <h1 class="display">Privacy Policy</h1>
            <p class="policy-updated">Last updated: <?= e($lastUpdated) ?></p>
        </div>
    </section>

    <div class="container container-narrow policy-wrap">
        <div class="policy-notice">
            This policy explains what information Rafly Digital Growth Partner ("Rafly", "we", "us") collects through this website, why we collect it, and the choices you have. It applies to <strong><?= e(SITE_DOMAIN) ?></strong> and the consultation and requirement forms hosted on it.
        </div>

        <div class="policy-toc" data-r="rise">
            <h2>On This Page</h2>
            <ol>
                <li><a href="#information-we-collect">1. Information We Collect</a></li>
                <li><a href="#how-we-use-it">2. How We Use Your Information</a></li>
                <li><a href="#cookies">3. Cookies &amp; Similar Technologies</a></li>
                <li><a href="#security">4. How We Protect Your Information</a></li>
                <li><a href="#retention">5. Data Retention</a></li>
                <li><a href="#sharing">6. Sharing Your Information</a></li>
                <li><a href="#rights">7. Your Rights &amp; Choices</a></li>
                <li><a href="#children">8. Children's Privacy</a></li>
                <li><a href="#changes">9. Changes to This Policy</a></li>
                <li><a href="#contact-us">10. Contact Us</a></li>
            </ol>
        </div>

        <section class="policy-section" data-r="rise" id="information-we-collect">
            <h2><?= icon('database') ?>1. Information We Collect</h2>
            <p>We collect information you choose to submit through our "Get a Free Consultation" and requirement forms, including:</p>
            <ul>
                <li><strong>Company name</strong> — to identify your business and personalise our response.</li>
                <li><strong>Contact number</strong> — so our team can reach you about your enquiry.</li>
                <li><strong>Project description / requirements</strong> — so we understand what you need before we speak with you.</li>
            </ul>
            <p>We do not ask for or knowingly collect payment details, government identifiers, or other sensitive personal data through these forms. If you contact us by email, phone, or WhatsApp instead, we collect whatever information you choose to share through those channels.</p>
        </section>

        <section class="policy-section" data-r="rise" id="how-we-use-it">
            <h2><?= icon('settings') ?>2. How We Use Your Information</h2>
            <p>We use the information you provide to:</p>
            <ul>
                <li>Respond to your enquiry and follow up on your consultation request;</li>
                <li>Prepare quotes, proposals, or scoped packages relevant to what you asked for;</li>
                <li>Maintain internal records of leads and client communications;</li>
                <li>Improve our services, forms, and the security of this website.</li>
            </ul>
            <p>We do not use the information you submit through our forms for automated decision-making or profiling. We do not sell your personal information to third parties. We do use the Meta Pixel described below to measure and improve our advertising — see Sections 3, 6 and 7 for what that involves and how to opt out.</p>
        </section>

        <section class="policy-section" data-r="rise" id="cookies">
            <h2><?= icon('cookie') ?>3. Cookies &amp; Similar Technologies</h2>
            <p>This site uses a single strictly-necessary session cookie to keep your browsing session secure and to generate a CSRF (Cross-Site Request Forgery) token that protects our forms from being submitted by malicious third-party sites. This cookie does not track you across other websites and is not used for advertising.</p>
            <p>We use the <strong>Meta Pixel</strong> (from Meta, the company behind Facebook and Instagram) to understand how visitors reach and use this site and to measure the results of our advertising. When you visit, the Pixel can share with Meta: the pages you view, whether you submitted one of our enquiry forms (as a "Lead" event, without the content of what you wrote), and whether you clicked through to WhatsApp. It also sets its own cookies and may combine this with information Meta already has about you if you are logged into a Meta product in the same browser.</p>
            <p>This is separate from the browsing data Meta itself collects when its Pixel loads in your browser — that collection is governed by <a href="https://www.facebook.com/privacy/policy/" target="_blank" rel="noopener">Meta's own Privacy Policy</a>, not this one. You can limit or stop it at any time: use <a href="https://www.facebook.com/adpreferences/" target="_blank" rel="noopener">Meta's Ad Preferences</a> to control ad personalisation, or a browser extension / tracking-protection setting to block the Pixel outright — the site works identically either way, since nothing about the forms or pages depends on it.</p>
        </section>

        <section class="policy-section" data-r="rise" id="security">
            <h2><?= icon('shield') ?>4. How We Protect Your Information</h2>
            <p>We apply reasonable technical safeguards to protect the information submitted through this site, including transport encryption (HTTPS), CSRF protection on all forms, input sanitisation, and restricted access to stored submissions. No method of transmission or storage is completely secure, so while we work to protect your information, we cannot guarantee its absolute security.</p>
        </section>

        <section class="policy-section" data-r="rise" id="retention">
            <h2><?= icon('history') ?>5. Data Retention</h2>
            <p>We retain form submissions for as long as reasonably necessary to respond to your enquiry, maintain business records, and comply with our legal obligations. If you would like your information deleted sooner, contact us using the details below and we will action your request within a reasonable timeframe.</p>
        </section>

        <section class="policy-section" data-r="rise" id="sharing">
            <h2><?= icon('users') ?>6. Sharing Your Information</h2>
            <p>We do not sell or rent your personal information. We may share limited information with:</p>
            <ul>
                <li>Our website hosting provider, solely to operate and secure this website;</li>
                <li>Communication tools such as WhatsApp Business, where you choose to contact us that way;</li>
                <li>Meta, via the Meta Pixel described in Section 3, for advertising measurement — page views, whether a form was submitted, and WhatsApp click-throughs, not the content of anything you write to us;</li>
                <li>Authorities or professional advisors, where required to comply with the law or to protect our legal rights.</li>
            </ul>
        </section>

        <section class="policy-section" data-r="rise" id="rights">
            <h2><?= icon('shield') ?>7. Your Rights &amp; Choices</h2>
            <p>Depending on your location, you may have the right to ask us to:</p>
            <ul>
                <li>Confirm what personal information we hold about you;</li>
                <li>Correct inaccurate or incomplete information;</li>
                <li>Delete your personal information from our records;</li>
                <li>Withdraw any consent you previously gave us.</li>
            </ul>
            <p>To exercise any of these rights, email us at <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>. We may need to verify your identity before actioning certain requests. To opt out of the Meta Pixel specifically, see Section 3 — <a href="https://www.facebook.com/adpreferences/" target="_blank" rel="noopener">Meta's Ad Preferences</a> or a browser tracking-protection setting, no request to us required.</p>
        </section>

        <section class="policy-section" data-r="rise" id="children">
            <h2><?= icon('users') ?>8. Children's Privacy</h2>
            <p>Our services are intended for businesses and individuals who are at least 18 years old. We do not knowingly collect personal information from children, and we ask that minors do not submit information through this website.</p>
        </section>

        <section class="policy-section" data-r="rise" id="changes">
            <h2><?= icon('file-pen') ?>9. Changes to This Policy</h2>
            <p>We may update this Privacy Policy from time to time to reflect changes to our practices or for legal, operational, or regulatory reasons. The "Last updated" date at the top of this page will always reflect the most recent version. We encourage you to review this page periodically.</p>
        </section>

        <section class="policy-section" data-r="rise" id="contact-us">
            <h2><?= icon('mail-open') ?>10. Contact Us</h2>
            <p>If you have questions about this Privacy Policy or how we handle your information, reach out to us:</p>
            <div class="contact-callout">
                <h3>Rafly Digital Growth Partner</h3>
                <p><?= e(setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306')) ?><br>
                <?= e(CONTACT_EMAIL) ?> &nbsp;•&nbsp; <?= e(CONTACT_PHONE) ?></p>
                <a href="/contact" class="btn btn-pill">Contact Rafly <?= icon('arrow-right') ?></a>
            </div>
        </section>
    </div>

    <?php
    $ctaEyebrow = 'Questions about your data?';
    $ctaTitle   = 'Ask us directly.';
    $ctaText    = 'We answer privacy questions the same way we answer everything else — a person replies, in plain language, within one working day.';
    require __DIR__ . '/partials/cta-band.php';
    ?>
</main>

<?php require __DIR__ . "/partials/tail.php"; ?>
