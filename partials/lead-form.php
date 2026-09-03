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
<form class="form lead-form-premium" id="<?= e($formId) ?>" action="/submit" method="POST" data-ajax-form="true">
    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token'] ?? '') ?>">
    <?= lead_context_fields() ?>

    <div class="form-hud-bar">
        <span class="form-hud-status"><span class="form-hud-pulse">●</span> DIRECT BUILD CHANNEL</span>
        <span class="form-hud-sla">EST. RESPONSE &lt; 24 HOURS</span>
    </div>

    <div class="form-row">
        <div class="field">
            <label for="<?= e($formId) ?>-name">Your Name <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="text" id="<?= e($formId) ?>-name" name="contact_name"
                   autocomplete="name" maxlength="120" required placeholder="e.g. Priya Sharma">
        </div>
        <div class="field">
            <label for="<?= e($formId) ?>-email">Email Address <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="email" id="<?= e($formId) ?>-email" name="contact_email"
                   autocomplete="email" maxlength="255" required placeholder="you@company.com">
        </div>
    </div>

    <div class="form-row">
        <div class="field">
            <label for="<?= e($formId) ?>-company">Company Name <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="text" id="<?= e($formId) ?>-company" name="company_name"
                   autocomplete="organization" maxlength="100" required placeholder="Your business name">
        </div>
        <div class="field">
            <label for="<?= e($formId) ?>-phone">Phone / WhatsApp <span class="req" aria-hidden="true">*</span></label>
            <input class="form-control" type="tel" id="<?= e($formId) ?>-phone" name="contact_number"
                   autocomplete="tel" maxlength="30" required placeholder="+91 98765 43210">
        </div>
    </div>

    <div class="field">
        <div class="field-label-row">
            <label for="<?= e($formId) ?>-desc">Project Context / Requirements <span class="req" aria-hidden="true">*</span></label>
            <span class="field-quick-hint">Tap a scope chip to add:</span>
        </div>

        <div class="form-tag-chips" role="group" aria-label="Quick project scope tags">
            <button type="button" class="form-tag-chip" data-chip="Web Application">+ Web Application</button>
            <button type="button" class="form-tag-chip" data-chip="E-Commerce">+ E-Commerce</button>
            <button type="button" class="form-tag-chip" data-chip="Custom PHP / Laravel">+ Custom Stack</button>
            <button type="button" class="form-tag-chip" data-chip="UI/UX Redesign">+ Redesign</button>
            <button type="button" class="form-tag-chip" data-chip="SEO & Performance">+ Performance</button>
        </div>

        <textarea class="form-control" id="<?= e($formId) ?>-desc" name="description"
                  maxlength="2000" required
                  <?= $compact ? 'rows="3"' : 'rows="4"' ?>
                  placeholder="Tell us what you need executed — current setup, timeline, or key objectives..."></textarea>
    </div>

    <?php require __DIR__ . '/honeypot.php'; ?>
    <?php require __DIR__ . '/antibot.php'; ?>

    <label class="check">
        <input type="checkbox" name="consent" required>
        <span>I agree to be contacted regarding this enquiry per the <a href="/privacy" target="_blank">privacy policy</a>.</span>
    </label>

    <button class="btn btn-pill btn-lg btn-lead-submit" type="submit">
        <span><?= e($submitLabel) ?></span>
    </button>
</form>

<script>
(function() {
    document.addEventListener('click', function(e) {
        var chip = e.target.closest('.form-tag-chip');
        if (!chip) return;
        e.preventDefault();
        var text = chip.getAttribute('data-chip');
        var form = chip.closest('form');
        if (!form) return;
        var textarea = form.querySelector('textarea[name="description"]');
        if (!textarea) return;
        var val = textarea.value.trim();
        var tagStr = '[Scope: ' + text + ']';
        if (val.indexOf(tagStr) === -1) {
            textarea.value = val ? val + '\n' + tagStr : tagStr + ' ';
            chip.classList.add('is-selected');
        } else {
            textarea.value = val.replace(tagStr, '').trim();
            chip.classList.remove('is-selected');
        }
        textarea.focus();
    });
})();
</script>

