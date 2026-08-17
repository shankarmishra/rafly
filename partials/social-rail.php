<?php
/**
 * The floating social rail.
 *
 * Restrained on purpose. The previous build ran five icons down the left edge
 * of every page in their raw brand colours — a rainbow strip against an
 * otherwise controlled palette, and the single loudest element on the site.
 * Here the icons sit in the ink ramp and colour arrives only on hover, and the
 * whole rail is hidden below 1240px where it would compete with content.
 */
?>
<div class="social-rail" aria-label="Rafly on social media">
    <?php foreach (SOCIAL_LINKS as $s): ?>
        <a href="<?= e($s['href']) ?>" target="_blank" rel="noopener" aria-label="<?= e($s['label']) ?>">
            <?= icon($s['icon'], 'icon-fill') ?>
        </a>
    <?php endforeach; ?>
</div>
