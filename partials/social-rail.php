<?php
/**
 * The floating social rail — next-gen 3D spatial dock with magnetic hover,
 * platform electric auras, and two-line rich glass tooltips.
 */
?>
<div class="social-rail" aria-label="Rafly on social media">
    <div class="social-rail-glass">
        <div class="social-rail-beam" aria-hidden="true"></div>
        <div class="social-rail-energy-line" aria-hidden="true"></div>
        <div class="social-rail-links">
            <?php foreach (SOCIAL_LINKS as $s): ?>
                <a href="<?= e($s['href']) ?>" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   aria-label="Rafly on <?= e($s['label']) ?>"
                   data-social="<?= e($s['key']) ?>">
                    <span class="social-halo-ring" aria-hidden="true"></span>
                    <span class="social-icon-wrapper">
                        <?= icon($s['icon'], 'icon-fill') ?>
                    </span>
                    <span class="social-tooltip" aria-hidden="true">
                        <span class="social-tooltip-card">
                            <span class="social-tooltip-title"><?= e($s['label']) ?></span>
                            <?php if (!empty($s['sub'])): ?>
                                <span class="social-tooltip-sub"><?= e($s['sub']) ?></span>
                            <?php endif; ?>
                        </span>
                    </span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>


