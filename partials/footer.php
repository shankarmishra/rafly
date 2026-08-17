<?php
/** Shared site footer. Styles live in css/05-footer.css. */

/* contact.address is an admin-editable setting (seeded in inc/tools/seed.php);
   the same string used to be hardcoded here, in index.php's contact block and
   in privacy.php, so an address change in the admin altered nothing a visitor
   could see. All three read the setting. */
$footAddr  = setting('contact.address', 'A523, T3, NX-One, Tech Zone IV, Greater Noida West, 201306');
$footHours = setting('contact.hours', '');
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-top">
            <div class="footer-brand">
                <a class="footer-logo" href="/">
                    <img src="<?= e(asset('assets/logo-reversed.png')) ?>" alt="<?= e(SITE_NAME) ?>" width="97" height="30">
                </a>
                <p class="footer-statement">Digital growth, delivered as a system.</p>
                <p class="footer-blurb">One partner, one bundled package — web development, content creation, digital marketing, web security, and e-commerce support, all working together instead of stitched together from five different vendors.</p>
                <div class="footer-social">
                    <?php foreach (SOCIAL_LINKS as $s): ?>
                        <a href="<?= e($s['href']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['label']) ?>">
                            <?= icon($s['icon'], 'icon-fill') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="footer-cols">
                <div>
                    <p class="footer-col-title">Company</p>
                    <ul class="footer-links">
                        <li><a href="/">Home</a></li>
                        <li><a href="/about">About</a></li>
                        <li><a href="/team">Team</a></li>
                        <li><a href="/case-studies">Case Studies</a></li>
                        <li><a href="/blog">Blog</a></li>
                        <li><a href="/contact">Contact</a></li>
                    </ul>
                </div>

                <div>
                    <p class="footer-col-title">Services</p>
                    <ul class="footer-links">
                        <?php foreach (SERVICES as $slug => $label): ?>
                            <li><a href="/<?= e($slug) ?>"><?= e($label) ?></a></li>
                        <?php endforeach; ?>
                        <li><a href="/pricing">Bundle packages</a></li>
                    </ul>
                </div>

                <div>
                    <p class="footer-col-title">Get in touch</p>
                    <div class="footer-contact">
                        <div class="footer-contact-row">
                            <?= icon('map-pin') ?>
                            <a href="https://maps.google.com/?q=<?= rawurlencode($footAddr) ?>" target="_blank" rel="noopener"><?= e($footAddr) ?></a>
                        </div>
                        <div class="footer-contact-row">
                            <?= icon('mail') ?>
                            <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
                        </div>
                        <div class="footer-contact-row">
                            <?= icon('phone') ?>
                            <a href="tel:<?= e(preg_replace('/[^0-9+]/', '', CONTACT_PHONE)) ?>"><?= e(CONTACT_PHONE) ?></a>
                        </div>
                        <?php if ($footHours !== ''): ?>
                        <div class="footer-contact-row">
                            <?= icon('hourglass') ?>
                            <span><?= e($footHours) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="footer-bottom">
            <p>
                &copy; <?= date('Y') ?> Rafly Digital Growth Partner. All rights reserved.
<?php
/**
 * Real registration numbers, not invented — both settings default to empty
 * (see inc/tools/seed.php) and simply don't render until an admin fills them
 * in via /admin/settings.php. A blank "CIN: " line would look worse than no
 * line at all.
 */
$cin = setting('legal.cin', '');
$gst = setting('legal.gst', '');
if ($cin !== '' || $gst !== ''):
?>
                <span>
                    &middot;
<?php if ($cin !== ''): ?>CIN: <?= e($cin) ?><?php endif; ?>
<?php if ($cin !== '' && $gst !== ''): ?> &middot; <?php endif; ?>
<?php if ($gst !== ''): ?>GST: <?= e($gst) ?><?php endif; ?>
                </span>
<?php endif; ?>
            </p>
            <div class="footer-legal">
                <a href="/privacy">Privacy Policy</a>
                <a href="/sitemap.xml">Sitemap</a>
            </div>
        </div>
    </div>
</footer>
