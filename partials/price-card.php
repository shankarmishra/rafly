<?php
/**
 * One bundle card. Rendered by the homepage #pricing section and by
 * pricing.php, which previously each carried their own copy of this markup.
 *
 * Expects $t (a row from bundles_all()) and $i (0-based position).
 *
 * The featured tier is the ink card — inverted, lifted, and the only one with
 * a badge. Position does not decide that; the FEATURED flag does.
 */
$featured = !empty($t['featured']);
?>
<div class="price-card<?= $featured ? ' is-featured' : '' ?>">
    <?php if ($featured): ?>
        <span class="price-flag">Most popular</span>
    <?php endif; ?>

    <div class="price-head">
        <span class="price-index"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
        <h3 class="price-name"><?= e($t['name']) ?> Bundle</h3>
        <p class="price-sub"><?= e($t['sub']) ?></p>
        <?php if (trim((string)($t['price_text'] ?? '')) !== ''): ?>
            <p class="price-value"><?= e($t['price_text']) ?></p>
        <?php endif; ?>
    </div>

    <ul class="list-check price-list">
        <?php foreach ($t['points'] as $p): ?>
            <li><?= icon('circle-check') ?><span><?= e($p) ?></span></li>
        <?php endforeach; ?>
    </ul>

    <a class="btn <?= $featured ? 'btn-white' : 'btn-line' ?> btn-block price-cta"
       target="_blank" rel="noopener"
       href="<?= e(whatsapp_link("Hi Rafly team! I'm interested in the {$t['name']} Bundle. Could you share an exact quote?")) ?>">
        <?= icon('whatsapp', 'icon-fill') ?> Get exact quote
    </a>
</div>
