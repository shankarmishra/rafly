<?php
/**
 * Compact High-Efficiency CTA Band & Card Shell (partials/cta-band.php)
 * Low-height, space-saving horizontal banner section.
 */
$ctaEyebrow = $ctaEyebrow ?? 'Ready to work with us?';
$ctaTitle   = $ctaTitle   ?? 'One team. One package. One point of contact.';
$ctaText    = $ctaText    ?? 'Tell us what is slowing you down and we will put together a package that covers it — web, content, marketing, security and e-commerce.';
$ctaButton  = $ctaButton  ?? 'Get a free quote';
?>
<section class="section band-ink cta-band" style="padding-block: clamp(1.25rem, 2.5vw, 2.25rem);">
    <div class="container">
        <div class="cta-card-shell cta-card-compact">
            <div class="cta-ambient-glow" aria-hidden="true"></div>
            
            <div class="cta-compact-row">
                <div class="cta-compact-text">
                    <span class="cta-badge cta-badge-sm">
                        <span class="pulse-dot"></span>
                        <?= e($ctaEyebrow) ?>
                    </span>
                    <h2 class="display cta-band-title cta-title-sm"><?= e($ctaTitle) ?></h2>
                    <p class="lead cta-band-text cta-text-sm"><?= e($ctaText) ?></p>
                </div>

                <div class="cta-compact-right">
                    <div class="cta-band-actions cta-actions-sm">
                        <button type="button" class="btn btn-primary cta-main-btn" data-modal-open="consultationModal">
                            <span><?= e($ctaButton) ?></span> <?= icon('arrow-up-right') ?>
                        </button>
                        <a class="btn btn-wa cta-wa-btn" target="_blank" rel="noopener"
                           href="<?= e(whatsapp_link('Hi Rafly, I would like to discuss a bundled package.')) ?>">
                            <?= icon('whatsapp', 'icon-fill') ?> <span>WhatsApp</span>
                        </a>
                    </div>

                    <div class="cta-inline-chips">
                        <span><?= icon('check') ?> Dedicated Team</span>
                        <span><?= icon('check') ?> 30/40/30 Policy</span>
                        <span><?= icon('check') ?> 100% IP Ownership</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
