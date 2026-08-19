<?php
/**
 * partials/deck-mock.php — the inside of one product surface in the deck.
 *
 * WHY THIS IS A FILE AND NOT A LOOP BODY. Every mock in the deck used to
 * render the same three KPI blocks and the same bar chart, with only the
 * accent colour changing. Five identical screens tinted five ways do not say
 * "five products"; they say one product, screenshotted five times. Naveen
 * asked for the inside of each one to be about the service it is labelled
 * with, and that is five genuinely different layouts, which is more markup
 * than belongs inline in index.php.
 *
 * WHAT STAYS THE SAME, DELIBERATELY: the browser chrome, the sidebar and the
 * accent. That shared frame is the section's whole argument — one system, five
 * surfaces — so the parts that make them look like one product are exactly the
 * parts that must not vary.
 *
 * THE RULE ON NUMBERS IS UNCHANGED. Nothing in here carries a figure. Every
 * bar, ring and row is a shape; the only words are states a screen would
 * genuinely show ("Live", "Draft", "Paid"), never a measurement, because a
 * measurement in a mock is a claim nobody made. Same rule inc/repo/metrics.php
 * enforces for the trust bar. The whole deck is aria-hidden.
 *
 * Expects: $app (slug), $label.
 */
?>
<div class="mock-body mock-<?= e($app) ?>">
<?php if ($app === 'web'): ?>
    <?php /* A build and deploy screen: what a site looks like from inside the
             repository that produces it. */ ?>
    <div class="mock-top">
        <span class="h"></span>
        <span class="mock-pill is-ok">Live</span>
    </div>
    <div class="mock-code">
        <span class="mock-gut"></span>
        <div class="mock-code-lines">
            <i style="width:62%"></i>
            <i style="width:44%; margin-left:12px"></i>
            <i style="width:72%; margin-left:12px" class="is-hi"></i>
            <i style="width:38%; margin-left:24px"></i>
            <i style="width:56%; margin-left:12px"></i>
            <i style="width:28%"></i>
        </div>
    </div>
    <div class="mock-strip">
        <span class="mock-chip"></span>
        <span class="mock-chip"></span>
        <span class="mock-chip is-on"></span>
    </div>

<?php elseif ($app === 'security'): ?>
    <?php /* A scan result. The ring is a gauge with no reading on it — the
             shape of a score, not a score. */ ?>
    <div class="mock-top">
        <span class="h"></span>
        <span class="mock-pill">Scan</span>
    </div>
    <div class="mock-split">
        <span class="mock-ring"></span>
        <div class="mock-findings">
            <div class="mock-find"><u class="is-ok"></u><i style="width:74%"></i></div>
            <div class="mock-find"><u class="is-warn"></u><i style="width:58%"></i></div>
            <div class="mock-find"><u class="is-ok"></u><i style="width:66%"></i></div>
            <div class="mock-find"><u class="is-ok"></u><i style="width:48%"></i></div>
        </div>
    </div>
    <div class="mock-rowline"><i style="width:34%"></i><span class="mock-pill is-ok">TLS</span></div>

<?php elseif ($app === 'marketing'): ?>
    <?php /* Channels and the shape of what each one is doing. The area chart
             has no axis and no unit, on purpose. */ ?>
    <div class="mock-top">
        <span class="h"></span>
        <span class="mock-pill">30D</span>
    </div>
    <div class="mock-area"><span></span></div>
    <div class="mock-channels">
        <div class="mock-ch"><i style="width:26%"></i><b style="width:78%"></b></div>
        <div class="mock-ch"><i style="width:20%"></i><b style="width:54%"></b></div>
        <div class="mock-ch"><i style="width:30%"></i><b style="width:38%"></b></div>
    </div>

<?php elseif ($app === 'content'): ?>
    <?php /* An editor. An outline down the side, a headline, body copy, and
             one line mid-edit. */ ?>
    <div class="mock-top">
        <span class="h"></span>
        <span class="mock-pill">Draft</span>
    </div>
    <div class="mock-split is-editor">
        <div class="mock-outline"><i></i><i class="is-on"></i><i></i><i></i></div>
        <div class="mock-doc">
            <i class="mock-head" style="width:76%"></i>
            <i style="width:96%"></i>
            <i style="width:88%"></i>
            <i style="width:92%" class="is-hi"></i>
            <i style="width:64%"></i>
        </div>
    </div>

<?php else: ?>
    <?php /* Orders. The unglamorous side of selling online, which is exactly
             what the service page says it is. */ ?>
    <div class="mock-top">
        <span class="h"></span>
        <span class="mock-pill is-ok">Paid</span>
    </div>
    <div class="mock-spark">
        <i style="height:38%"></i><i style="height:62%"></i><i style="height:48%"></i>
        <i style="height:74%"></i><i style="height:58%"></i><i style="height:88%"></i>
        <i style="height:70%"></i><i style="height:96%"></i>
    </div>
    <div class="mock-orders">
        <div class="mock-order"><u></u><i style="width:52%"></i><span class="mock-pill is-ok"></span></div>
        <div class="mock-order"><u></u><i style="width:64%"></i><span class="mock-pill"></span></div>
        <div class="mock-order"><u></u><i style="width:44%"></i><span class="mock-pill is-ok"></span></div>
    </div>
<?php endif; ?>
</div>
