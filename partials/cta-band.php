<?php
/**
 * The full-bleed ink closing band — the last thing on every page except the
 * homepage, which closes on its own contact section instead.
 *
 * It was blue. A flat #1652E0 slab is the largest single block of colour on the
 * page and it flattened everything near it, so the band moved to --ink and blue
 * went back to being an accent. Every primary CTA on this site is already a
 * black pill, so the band and the buttons now read as one family rather than as
 * two accents competing at the bottom of every page.
 *
 * Callers may set, before including:
 *   $ctaEyebrow, $ctaTitle, $ctaText, $ctaButton
 *
 * Kept as one partial because the alternative is nine copies of the same
 * markup, which is how the previous build ended up with three different CTA
 * banners saying three slightly different things.
 */
$ctaEyebrow = $ctaEyebrow ?? 'Ready to stop juggling vendors?';
$ctaTitle   = $ctaTitle   ?? 'One team. One package. One point of contact.';
$ctaText    = $ctaText    ?? 'Tell us what is slowing you down and we will put together a package that covers it — web, content, marketing, security and e-commerce, under one scope and one number.';
$ctaButton  = $ctaButton  ?? 'Get a free quote';
?>
<section class="section band-ink cta-band">
    <div class="container container-narrow">
        <p class="eyebrow on-dark"><?= e($ctaEyebrow) ?></p>
        <h2 class="display cta-band-title"><?= e($ctaTitle) ?></h2>
        <p class="lead cta-band-text"><?= e($ctaText) ?></p>

        <div class="cluster cluster-center cta-band-actions">
            <button type="button" class="btn btn-white btn-lg" data-modal-open="consultationModal">
                <?= e($ctaButton) ?> <?= icon('arrow-up-right') ?>
            </button>
            <a class="btn btn-on-dark btn-lg" target="_blank" rel="noopener"
               href="<?= e(whatsapp_link('Hi Rafly, I would like to discuss a bundled package.')) ?>">
                <?= icon('whatsapp', 'icon-fill') ?> WhatsApp us
            </a>
        </div>

        <ul class="plain cta-band-points">
            <li><?= icon('check') ?> Dedicated team</li>
            <li><?= icon('check') ?> Bundled pricing</li>
            <li><?= icon('check') ?> Transparent process</li>
        </ul>
    </div>
</section>
