<?php
/**
 * Honeypot field — include inside every public form.
 *
 * Hidden from humans in CSS (.hp-field, css/03-components.css) rather than with
 * type="hidden", because many bots skip hidden inputs but happily fill visible
 * text fields. tabindex="-1" and autocomplete="off" keep it away from keyboard
 * users and browser autofill; aria-hidden keeps it out of the accessibility
 * tree, so screen-reader users never encounter it either.
 *
 * Server side: honeypot_tripped() in inc/security.php.
 *
 * The NAME must be identical across forms (the server reads one field), but the
 * ID must not be — index.php renders both the contact form and the consultation
 * modal, so a fixed id would be duplicated on one page and break the
 * label/input association. A per-render counter keeps each instance unique.
 */

// A `static` here would not persist — this file is re-executed on each require,
// and static only survives across calls inside a function. A global does.
$GLOBALS['hp_instance'] = ($GLOBALS['hp_instance'] ?? 0) + 1;
$hpId = HONEYPOT_FIELD . '-' . $GLOBALS['hp_instance'];
?>
<div class="hp-field" aria-hidden="true">
    <label for="<?= e($hpId) ?>">Leave this field empty</label>
    <input type="text"
           id="<?= e($hpId) ?>"
           name="<?= e(HONEYPOT_FIELD) ?>"
           value=""
           tabindex="-1"
           autocomplete="off">
</div>
