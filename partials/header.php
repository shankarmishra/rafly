<?php
/**
 * Site header — the floating pill bar, its services dropdown, and the mobile
 * drawer. One partial for every page; the active item comes from $page['id'].
 */

$currentId = $page['id'] ?? '';
$navServices = services_all();
?>
<header class="site-header">
    <nav class="nav-pill" aria-label="Primary">
        <a class="nav-logo" href="/" aria-label="<?= e(SITE_NAME) ?> — home">
            <img src="<?= e(asset('assets/logo.png')) ?>" alt="<?= e(SITE_NAME) ?>" width="116" height="36">
        </a>

        <ul class="nav-menu">
            <li data-drop>
                <button type="button" class="nav-link <?= nav_active('services', $currentId) ?>"
                        aria-expanded="false" aria-controls="navDropServices">
                    Services <?= icon('chevron-down') ?>
                </button>
                <div class="nav-drop nav-drop-wide" id="navDropServices">
                    <?php foreach ($navServices as $s): ?>
                        <a class="nav-drop-item" href="/<?= e($s['slug']) ?>">
                            <span class="icon-box"><?= icon($s['icon']) ?></span>
                            <span>
                                <span class="nav-drop-title"><?= e($s['title']) ?></span><br>
                                <span class="nav-drop-desc"><?= e(str_trunc($s['card'], 74)) ?></span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                    <div class="nav-drop-foot">
                        <span class="nav-drop-desc">All five in one bundled package.</span>
                        <a class="link-arrow" href="/pricing">See packages <?= icon('arrow-right') ?></a>
                    </div>
                </div>
            </li>
            <li><a class="nav-link <?= nav_active('pricing', $currentId) ?>" href="/pricing">Pricing</a></li>
            <li><a class="nav-link <?= nav_active('case-studies', $currentId) ?>" href="/case-studies">Work</a></li>
            <li><a class="nav-link <?= nav_active('about', $currentId) ?>" href="/about">About</a></li>
            <li><a class="nav-link <?= nav_active('team', $currentId) ?>" href="/team">Team</a></li>
            <li><a class="nav-link <?= nav_active('blog', $currentId) ?>" href="/blog">Blog</a></li>
        </ul>

        <div class="nav-actions">
            <a class="btn btn-ghost btn-sm btn-quote" href="/contact">Contact</a>
            <button type="button" class="btn btn-pill btn-sm" data-modal-open="consultationModal">
                Get a quote <?= icon('arrow-up-right') ?>
            </button>
            <button type="button" class="nav-toggle" data-drawer-toggle
                    aria-expanded="false" aria-controls="drawer" aria-label="Open menu">
                <?= icon('menu', 'icon-open') ?><?= icon('x', 'icon-close') ?>
            </button>
        </div>
    </nav>
</header>

<div class="drawer" id="drawer">
    <ul class="drawer-list">
        <li>
            <button type="button" class="drawer-link" data-drawer-sub
                    aria-expanded="false" aria-controls="drawerServices">
                Services <?= icon('chevron-down') ?>
            </button>
            <ul class="drawer-sub" id="drawerServices" hidden>
                <?php foreach ($navServices as $s): ?>
                    <li>
                        <a href="/<?= e($s['slug']) ?>">
                            <span class="icon-box"><?= icon($s['icon']) ?></span>
                            <?= e($s['title']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li><a class="drawer-link" href="/pricing">Pricing <?= icon('arrow-up-right') ?></a></li>
        <li><a class="drawer-link" href="/case-studies">Work <?= icon('arrow-up-right') ?></a></li>
        <li><a class="drawer-link" href="/about">About <?= icon('arrow-up-right') ?></a></li>
        <li><a class="drawer-link" href="/team">Team <?= icon('arrow-up-right') ?></a></li>
        <li><a class="drawer-link" href="/blog">Blog <?= icon('arrow-up-right') ?></a></li>
        <li><a class="drawer-link" href="/contact">Contact <?= icon('arrow-up-right') ?></a></li>
    </ul>

    <div class="drawer-foot">
        <a class="btn btn-pill btn-block" href="/contact">Start a conversation <?= icon('arrow-right') ?></a>
        <a class="btn btn-line btn-block" target="_blank" rel="noopener"
           href="<?= e(whatsapp_link('Hi Rafly, I would like to discuss a bundled package.')) ?>">
            <?= icon('whatsapp', 'icon-fill') ?> WhatsApp us
        </a>
        <div class="drawer-meta">
            <a href="mailto:<?= e(CONTACT_EMAIL) ?>"><?= e(CONTACT_EMAIL) ?></a>
            <a href="tel:<?= e(str_replace(' ', '', CONTACT_PHONE)) ?>"><?= e(CONTACT_PHONE) ?></a>
        </div>

        <?php /* The fixed .social-rail is display:none below 1240px
           (css/04-nav.css). Every width that loses it has this drawer, so the
           links move here rather than simply disappearing for the entire
           tablet and phone range — which is what happened before. */ ?>
        <div class="drawer-social">
            <?php foreach (SOCIAL_LINKS as $s): ?>
                <a href="<?= e($s['href']) ?>" target="_blank" rel="noopener noreferrer"
                   aria-label="Rafly on <?= e($s['label']) ?>">
                    <?= icon($s['icon'], 'icon-fill') ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
