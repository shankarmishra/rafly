<?php
/**
 * The lead form. One partial, rendered by the homepage contact section, by
 * /contact, and by the consultation modal — previously three copies of the
 * same eleven fields, which is how they drifted apart.
 *
 * Callers may set, before including:
 *   $formId       string  unique id (required if more than one on a page)
 *   $submitLabel  string  button text
 *   $compact      bool    drop the description field's extra rows
 *
 * Every anti-spam layer is included here rather than left to the caller, so a
 * new form cannot accidentally ship without one.
 */
$formId      = $formId      ?? 'leadForm';
$submitLabel = $submitLabel ?? 'Submit requirements';
$compact     = $compact     ?? false;
?>
<form class="form" id="<?= e($formId) ?>" action="/submit" method="POST" data-ajax-form="true">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
    <?= lead_context_fields() ?>

    <div class="form-row">
        <div class="field">
            <label for="<?= e($formId) ?>-name">Your name <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="text" id="<?= e($formId) ?>-name" name="contact_name"
                   autocomplete="name" maxlength="120" required placeholder="Priya Sharma">
        </div>
        <div class="field">
            <label for="<?= e($formId) ?>-email">Email <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="email" id="<?= e($formId) ?>-email" name="contact_email"
                   autocomplete="email" maxlength="255" required placeholder="you@company.com">
        </div>
    </div>

    <div class="form-row">
        <div class="field">
            <label for="<?= e($formId) ?>-company">Company name <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="text" id="<?= e($formId) ?>-company" name="company_name"
                   autocomplete="organization" maxlength="100" required placeholder="Your business">
        </div>
        <div class="field">
            <label for="<?= e($formId) ?>-phone">Contact number <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="tel" id="<?= e($formId) ?>-phone" name="contact_number"
                   autocomplete="tel" maxlength="30" required placeholder="+91 98765 43210">
        </div>
    </div>

    <div class="field">
        <label for="<?= e($formId) ?>-desc">Project context / requirements <span class="req" aria-hidden="true">*</span></label>
        <textarea class="form-control" id="<?= e($formId) ?>-desc" name="description"
                  maxlength="2000" required
                  <?= $compact ? 'rows="4"' : 'rows="6"' ?>
                  placeholder="Describe what you want us to execute — what you have now, what is slowing you down, and what a good outcome looks like."></textarea>
    </div>

    <?php require __DIR__ . '/honeypot.php'; ?>
    <?php require __DIR__ . '/antibot.php'; ?>

    <label class="check">
        <input type="checkbox" name="consent" required>
        <span>I agree to be contacted about this enquiry and accept the <a href="/privacy">privacy policy</a>.</span>
    </label>

    <button class="btn btn-pill btn-lg" type="submit">
        <?= e($submitLabel) ?> <?= icon('arrow-right') ?>
    </button>
</form>
