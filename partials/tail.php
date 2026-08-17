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
 */
$tailScripts = ['smooth', 'motion', 'ui', 'forms', 'carousel', 'scroll', 'gl'];
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
