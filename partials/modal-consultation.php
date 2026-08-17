<?php
/**
 * The consultation modal — opened by any [data-modal-open="consultationModal"]
 * trigger. Rendered once per page from partials/tail.php.
 *
 * It shares partials/lead-form.php with the page's own contact form, which is
 * why both need unique field ids and why the anti-spam challenge has to be
 * re-issued after a submission (see partials/antibot.php).
 */
?>
<div class="modal" id="consultationModal" role="dialog" aria-modal="true"
     aria-labelledby="consultationModalTitle" aria-hidden="true">
    <div class="modal-card">
        <button type="button" class="modal-close" data-modal-close aria-label="Close">
            <?= icon('x') ?>
        </button>
        <div class="modal-head">
            <p class="eyebrow is-blue">Free consultation</p>
            <h2 id="consultationModalTitle">Tell us what needs fixing</h2>
            <p class="muted">No pitch deck until we understand your setup. We reply within one working day.</p>
        </div>
        <?php
        $formId      = 'consultForm';
        $submitLabel = 'Request consultation';
        $compact     = true;
        require __DIR__ . '/lead-form.php';
        ?>
    </div>
</div>
