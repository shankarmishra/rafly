<?php
/**
 * partials/phone-screen.php — the inside of one phone shell.
 *
 * WHAT WAS WRONG WITH THE OLD ONE. It was a title bar, a hero block, three
 * list rows and four nav squares — which is a WEB page rendered narrow, not an
 * app. Naveen's note was exactly that: "uske app ke andar abhi website ka
 * mockup hai". An app has things a page does not: a status bar it does not
 * own, a title that belongs to the screen rather than to the document, a
 * bottom tab bar that persists between screens, and a home indicator.
 *
 * Three phones, three different app screens, because three copies of one
 * screen is a filmstrip of one app. All three share the status bar, the tab
 * bar and the indicator, which is what makes them read as three screens OF ONE
 * APP rather than three unrelated products.
 *
 * NO NUMBERS, same rule as the deck. Every price, count and rating is a shape.
 * The only words are states. The shells are aria-hidden.
 *
 * Expects: $variant — 'store' | 'booking' | 'orders'.
 */
?>
<div class="phone-screen">

    <?php /* The status bar. Nothing in an app mockup does more per pixel:
             a phone screen without one reads as a browser window. */ ?>
    <div class="ph-status">
        <span class="ph-time"></span>
        <span class="ph-icons"><i></i><i></i><i class="ph-batt"></i></span>
    </div>

    <div class="ph-head">
        <span class="ph-title"></span>
        <span class="ph-av"></span>
    </div>

<?php if ($variant === 'store'): ?>
    <?php /* A store: search, categories, a product grid. */ ?>
    <div class="ph-search"><i></i></div>
    <div class="ph-chips"><span class="is-on"></span><span></span><span></span><span></span></div>
    <div class="ph-grid">
<?php for ($i = 0; $i < 4; $i++): ?>
        <div class="ph-prod">
            <span class="ph-thumb"></span>
            <i style="width:<?= [78, 62, 70, 54][$i] ?>%"></i>
            <b style="width:<?= [40, 34, 46, 38][$i] ?>%"></b>
        </div>
<?php endfor; ?>
    </div>

<?php elseif ($variant === 'booking'): ?>
    <?php /* A booking screen: the week, then the slots in the chosen day. */ ?>
    <div class="ph-week">
<?php foreach ([0, 1, 2, 3, 4, 5, 6] as $d): ?>
        <span class="<?= $d === 3 ? 'is-on' : '' ?>"><u></u><b></b></span>
<?php endforeach; ?>
    </div>
    <div class="ph-slots">
        <div class="ph-slot"><i style="width:44%"></i><span class="ph-pill"></span></div>
        <div class="ph-slot is-on"><i style="width:52%"></i><span class="ph-pill is-on"></span></div>
        <div class="ph-slot"><i style="width:38%"></i><span class="ph-pill"></span></div>
    </div>
    <div class="ph-cta"></div>

<?php else: ?>
    <?php /* An orders screen: what is in flight, and how far along it is. */ ?>
    <div class="ph-stats">
        <div><i style="width:54%"></i><b style="width:72%"></b></div>
        <div><i style="width:46%"></i><b style="width:60%"></b></div>
    </div>
    <div class="ph-orders">
<?php foreach ([[72, 'is-on'], [44, ''], [88, 'is-on']] as [$pct, $on]): ?>
        <div class="ph-order">
            <div class="ph-order-top"><i style="width:<?= (int)$pct > 60 ? 58 : 46 ?>%"></i><span class="ph-pill <?= e($on) ?>"></span></div>
            <span class="ph-bar"><u style="width:<?= (int)$pct ?>%"></u></span>
        </div>
<?php endforeach; ?>
    </div>
<?php endif; ?>

    <?php /* The tab bar and the home indicator. These persist across all three
             screens on purpose — it is what makes them one app. */ ?>
    <div class="ph-tabs">
        <i class="is-on"></i><i></i><i></i><i></i>
    </div>
    <span class="ph-home"></span>
</div>
