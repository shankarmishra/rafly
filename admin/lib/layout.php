<?php
/**
 * Admin chrome — sidebar, topbar, flash. Two functions rather than a
 * head/foot partial pair so a page cannot accidentally emit one without the
 * other.
 *
 * Navigation is filtered by capability: a viewer never sees a Settings link
 * they would be refused. That is presentation only — every target page still
 * calls require_can() for itself, because a hidden link is not access control.
 */

/**
 * Sidebar entries: [href, label, capability, group, icon].
 *
 * The icon name is a symbol id in vendor/icons/sprite.svg (without the `i-`
 * prefix). Every one of these already existed in the sprite the public site
 * ships — the admin simply never loaded it.
 */
const ADMIN_NAV = [
    ['/admin/',                'Dashboard',    'leads.view',    '',          'gauge'],
    ['/admin/leads.php',       'Leads',        'leads.view',    'Enquiries', 'mail-open'],
    ['/admin/posts.php',       'Blog',         'content.view',  'Content',   'file-pen'],
    ['/admin/categories.php',  'Categories',   'content.view',  '',          'layers'],
    ['/admin/case-studies.php','Case studies', 'content.view',  '',          'trending-up'],
    ['/admin/testimonials.php','Testimonials', 'content.view',  '',          'star'],
    ['/admin/bundles.php',     'Packages',     'content.view',  '',          'package'],
    ['/admin/team.php',        'Team',         'content.view',  '',          'users'],
    ['/admin/media.php',       'Media',        'media.view',    '',          'image'],
    ['/admin/settings.php',    'Settings',     'settings.view', 'System',    'settings'],
    ['/admin/users.php',       'Users',        'users.view',    '',          'users'],
    ['/admin/audit.php',       'Audit log',    'audit.view',    '',          'history'],
];

function admin_head(array $page): void
{
    $user  = current_user();
    $flash = admin_take_flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($page['title'] ?? 'Admin') ?> — Rafly Admin</title>
<?php
/* Fonts. admin.css asks for 'Inter' and 'Space Grotesk' in six places, but this
   file never linked the stylesheet that defines them — so every admin page was
   falling through to Segoe UI. The whole panel has been rendering in the wrong
   typeface, which is a large part of why it reads as unfinished.
   Preloaded on the same terms as the public site: bare filenames, no
   cache-buster, so the preload and the @font-face request are byte-identical
   and the browser does not download each face twice. */
?>
    <link rel="preload" href="<?= e(site_path('/vendor/fonts/space-grotesk-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= e(site_path('/vendor/fonts/inter-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(asset('vendor/fonts/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(asset('admin/assets/admin.css')) ?>">
    <link rel="icon" href="<?= e(asset('assets/favicon.svg')) ?>" type="image/svg+xml">
<?php /* Progressive enhancement only — toasts and a real confirm dialog. Served
         same-origin so the admin CSP (script-src 'self') allows it; deferred so
         it never blocks first paint. Every page works with it disabled. */ ?>
    <script src="<?= e(asset('admin/assets/admin.js')) ?>" defer></script>
</head>
<body>
<?php
/* The icon sprite: 52 symbols already shipped for the public site and never
   once referenced here, which is why the admin nav is bare text. Inlined once
   per document so every icon() call below is a cheap <use href="#i-*">. */
?>
<?= icon_sprite() ?>
<div class="shell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <!-- Reversed variant: the sidebar is --dark. -->
            <a href="<?= e(site_path('/admin/')) ?>"><img src="<?= e(asset('assets/logo-reversed.png')) ?>" alt="Rafly" width="107" height="33"></a>
            <span>Admin</span>
        </div>

        <nav aria-label="Admin sections">
<?php foreach (ADMIN_NAV as [$href, $label, $cap, $group, $ico]):
          if (!can($cap)) { continue; }
          if ($group !== ''): ?>
            <div class="nav-group"><?= e($group) ?></div>
<?php     endif;
          $isActive = ($page['active'] ?? '') === $href; ?>
            <a href="<?= e(site_path($href)) ?>"<?= $isActive ? ' class="is-active" aria-current="page"' : '' ?>><?= icon($ico) ?><span><?= e($label) ?></span></a>
<?php endforeach; ?>
        </nav>

        <div class="sidebar-foot">
            <strong><?= e($user['name'] ?? '') ?></strong>
            <div class="roles"><?= e(implode(', ', $user['roles'] ?? [])) ?></div>
            <?php /* Account-level, not content-permission-level — every signed-in
                     user should be able to secure their own login regardless of
                     role, so this is hand-placed here rather than in the
                     capability-filtered ADMIN_NAV list above. */ ?>
            <p><a href="<?= e(site_path('/admin/2fa-setup.php')) ?>">Two-factor auth</a></p>
            <p><a href="<?= e(site_path('/admin/logout.php')) ?>">Sign out</a></p>
        </div>
    </aside>

    <main class="main">
        <div class="content">
<?php if ($flash !== null): ?>
            <div class="flash flash-<?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
<?php endif;
}

function admin_foot(): void
{
    ?>
        </div>
    </main>
</div>
</body>
</html>
<?php
}
