<?php
/**
 * Two more anti-spam layers, alongside the honeypot — include right after
 * partials/honeypot.php inside every public form.
 *
 * Both are checked server-side in submit.php (form_timing_ok(), antibot_check()
 * in inc/security.php) and both work identically with JavaScript on or off —
 * neither needs a separate no-JS fallback to keep in sync with a JS one.
 *
 * The arithmetic question is visible to everyone, not hidden like the
 * honeypot: it is the one layer a scripted client cannot pass just by
 * skipping fields it doesn't recognise, so it has to actually be answered.
 *
 * The two numbers are wrapped in [data-antibot-a] / [data-antibot-b] so that
 * js/forms.js can rewrite them from the fresh challenge the server returns
 * with every response. Both gates are single-use server-side, so without that
 * rewrite the SECOND submission from one page load would always fail — which
 * is exactly how the previous build silently lost leads.
 */

form_timing_touch();
$antibot = antibot_challenge();

// Counter, same reasoning as partials/honeypot.php: index.php renders both
// the main contact form and the consultation modal, so the field id must not
// collide even though the field NAME (read once, server-side) is fixed.
$GLOBALS['antibot_instance'] = ($GLOBALS['antibot_instance'] ?? 0) + 1;
$antibotId = 'antibot_answer-' . $GLOBALS['antibot_instance'];
?>
<div class="field">
    <label for="<?= e($antibotId) ?>">
        What is <span data-antibot-a><?= (int)$antibot['a'] ?></span> + <span data-antibot-b><?= (int)$antibot['b'] ?></span>?
        <span class="req" aria-hidden="true">*</span>
    </label>
    <input class="form-control" type="text" id="<?= e($antibotId) ?>" name="antibot_answer"
           inputmode="numeric" autocomplete="off" required maxlength="3">
    <span class="field-hint">A quick check that you are not a script.</span>
</div>
