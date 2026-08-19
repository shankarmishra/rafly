<?php
/**
 * Closes the document: floating chrome, the consultation modal, the footer,
 * then every script. Included by every template as its last line.
 */
?>
<button type="button" class="to-top" aria-label="Back to top"><?= icon('arrow-up') ?></button>

<div class="sticky-cta">
    <button type="button" class="btn btn-pill" data-modal-open="consultationModal">Get a free quote</button>
    <a class="btn btn-wa" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"
       href="<?= e(whatsapp_link('Hi Rafly, I would like to discuss a bundled package.')) ?>">
        <?= icon('whatsapp', 'icon-fill') ?>
    </a>
</div>

<div class="toast-stack" id="toastStack" aria-live="polite" aria-atomic="false"></div>

<?php
require __DIR__ . '/modal-consultation.php';
require __DIR__ . '/footer.php';

/**
 * Scripts, all deferred, all same-origin (CSP is script-src 'self').
 *
 * smooth.js runs first on purpose: everything after it samples an eased
 * scroll position, so it has to be the thing driving the scroll before the
 * observers start reading it.
 *
 * gl.js left this list in the Machined Paper rebuild and is not coming back:
 * its one host element shipped a visibly empty 400px frame in full-page
 * capture. 44 KB, gone. carousel.js went earlier with the services carousel.
 *
 * interactions.js was deleted alongside them, on the argument that magnetic
 * buttons and a custom cursor are portfolio-site tells a firm selling stores
 * does not need. That argument LOST. The approved design calls for both, so
 * the file is back in the repository — but deliberately NOT in this list. It
 * is loaded by the homepage module along with everything else the redesign
 * gates, because an unconditional entry here would ship 12 KB to phones that
 * cannot use a single line of it. Until that module lands, the file is
 * tracked and dormant; grep data-magnetic, data-cursor and data-spotlight to
 * see what it wants.
 */
$tailScripts = ['smooth', 'motion', 'ui', 'forms', 'scroll'];
foreach ($tailScripts as $s):
?>
<script src="<?= e(asset("js/{$s}.js")) ?>" defer></script>
<?php endforeach; ?>
<?php foreach (($page['scripts'] ?? []) as $s): ?>
<script src="<?= e(asset("js/pages/{$s}.js")) ?>" defer></script>
<?php endforeach; ?>
<?php if (!empty($page['module'])): ?>
<script src="<?= e(asset("js/{$page['module']}.js")) ?>" type="module"></script>
<?php endif; ?>
</body>
</html>
