<?php
/**
 * RAFly Admin Chrome — Premium Agency OS Layout.
 *
 * Provides head and foot layout wrappers for all admin pages.
 * Supports responsive sidebar, sticky top header, breadcrumbs, global search modal,
 * quick-add menu, notification center, and flash toasts.
 */

/**
 * Sidebar navigation configuration.
 * [href, label, capability, group, icon]
 */
const ADMIN_NAV = [
    // Overview
    ['/admin/',                 'Dashboard',        'leads.view',    'Overview',   'gauge'],
    
    // Workspace
    ['/admin/leads.php',        'Leads & CRM',      'leads.view',    'Workspace',  'mail-open'],
    ['/admin/clients.php',      'Clients (360)',   'leads.view',    '',           'building'],
    ['/admin/projects.php',     'Projects',        'content.view',  '',           'rocket'],
    ['/admin/tasks.php',        'Tasks & My Work', 'content.view',  '',           'check-square'],
    ['/admin/services.php',     'Services CMS',     'content.view',  '',           'layers'],
    ['/admin/posts.php',        'Blog & Insights', 'content.view',  '',           'file-pen'],
    ['/admin/categories.php',   'Categories',      'content.view',  '',           'layers'],
    ['/admin/media.php',        'Media Library',   'media.view',    '',           'image'],
    ['/admin/creative.php',     'Creative Studio', 'content.view',  '',           'video'],
    ['/admin/channels.php',     'Channels Hub',    'content.view',  '',           'message-square'],
    ['/admin/documents.php',    'Docs & SOPs',     'content.view',  '',           'file-text'],
    ['/admin/approvals.php',    'Approvals',       'content.view',  '',           'shield-check'],

    // Growth
    ['/admin/analytics.php',    'Analytics',       'leads.view',    'Growth',     'trending-up'],
    ['/admin/seo.php',          'SEO Manager',     'settings.view', '',           'search'],
    ['/admin/testimonials.php', 'Testimonials',    'content.view',  '',           'star'],
    ['/admin/case-studies.php', 'Case Studies',    'content.view',  '',           'trending-up'],
    ['/admin/bundles.php',      'Packages',        'content.view',  '',           'package'],

    // System
    ['/admin/notifications.php','Notifications',   'leads.view',    'System',     'bell'],
    ['/admin/audit.php',        'Activity Log',    'audit.view',    '',           'history'],
    ['/admin/settings.php',     'Settings',        'settings.view', '',           'settings'],
    ['/admin/users.php',        'Users & Team',    'users.view',    '',           'users'],
];

function admin_head(array $page): void
{
    $user  = current_user();
    $flash = admin_take_flash();
    
    // Determine active route
    $currentPath = $_SERVER['REQUEST_URI'] ?? '/admin/';
    $activeHref = $page['active'] ?? '/admin/';
    
    // Breadcrumbs resolution
    $breadcrumbs = $page['breadcrumbs'] ?? [
        ['name' => 'Admin', 'url' => site_path('/admin/')],
        ['name' => $page['heading'] ?: ($page['title'] ?? 'Dashboard'), 'url' => '']
    ];
    
    // Count unread notifications if table exists
    $unreadCount = 0;
    if (db_available()) {
        try {
            $unreadCount = (int)scalar("SELECT count(*) FROM leads WHERE status = 'new'");
        } catch (\Throwable $e) {}
    }
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= e($page['title'] ?? 'Admin') ?> — RAFly Agency OS</title>
    
    <!-- Premium Fonts -->
    <link rel="preload" href="<?= e(admin_asset('vendor/fonts/space-grotesk-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= e(admin_asset('vendor/fonts/inter-var.woff2')) ?>" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="<?= e(admin_asset('vendor/fonts/fonts.css')) ?>">
    <link rel="stylesheet" href="<?= e(admin_asset('admin/assets/admin.css')) ?>">
    <link rel="icon" href="<?= e(admin_asset('assets/favicon.svg')) ?>" type="image/svg+xml">
    
    <script src="<?= e(admin_asset('admin/assets/admin.js')) ?>" defer></script>
</head>
<body>

<?= icon_sprite() ?>

<div class="shell" id="admin-shell">
    
    <!-- LEFT SIDEBAR -->
    <aside class="sidebar" id="admin-sidebar">
        <div class="sidebar-brand">
            <a href="<?= e(admin_path('/admin/')) ?>" class="brand-logo-link">
                <img src="<?= e(admin_asset('assets/logo.png')) ?>" alt="RAFly" width="100" height="30" class="brand-img-light">
                <span class="brand-badge">Console</span>
            </a>
            <button type="button" class="sidebar-collapse-btn" id="sidebar-toggle" title="Toggle Sidebar">
                <?= icon('menu') ?>
            </button>
        </div>

        <nav class="sidebar-nav" aria-label="Main Navigation">
            <?php 
            $lastGroup = null;
            foreach (ADMIN_NAV as [$href, $label, $cap, $group, $ico]):
                if (!can($cap)) { continue; }
                
                if ($group !== '' && $group !== $lastGroup): 
                    $lastGroup = $group;
            ?>
                <div class="nav-group-header"><?= e($group) ?></div>
            <?php endif; 
                $isActive = ($activeHref === $href) || (admin_path($href) === $currentPath) || (str_starts_with($currentPath, admin_path($href)) && $href !== '/admin/');
            ?>
                <a href="<?= e(admin_path($href)) ?>" class="nav-link <?= $isActive ? 'is-active' : '' ?>" <?= $isActive ? 'aria-current="page"' : '' ?> data-tooltip="<?= e($label) ?>">
                    <?= icon($ico, 'nav-icon') ?>
                    <span class="nav-label"><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="sidebar-foot">
            <div class="user-profile-card">
                <div class="user-avatar">
                    <?= e(strtoupper(substr($user['name'] ?? 'A', 0, 2))) ?>
                </div>
                <div class="user-info">
                    <span class="user-name"><?= e($user['name'] ?? 'Admin') ?></span>
                    <span class="user-role"><?= e(implode(', ', $user['roles'] ?? ['Admin'])) ?></span>
                </div>
            </div>

            <?php 
            $isDevHost = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1', 'localhost:8000', '127.0.0.1:8000'], true) || (defined('APP_ENV') && APP_ENV === 'dev');
            if ($isDevHost): ?>
                <div class="dev-role-switcher">
                    <span class="dev-tag">Dev Role</span>
                    <div class="dev-links">
                        <a href="<?= e(site_path('/admin/login.php?dev_role=admin&next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/admin/'))) ?>">Admin</a>
                        <a href="<?= e(site_path('/admin/login.php?dev_role=editor&next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/admin/'))) ?>">Editor</a>
                        <a href="<?= e(site_path('/admin/login.php?dev_role=viewer&next=' . rawurlencode($_SERVER['REQUEST_URI'] ?? '/admin/'))) ?>">Viewer</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="foot-actions">
                <a href="<?= e(site_path('/admin/2fa-setup.php')) ?>" class="foot-link" title="2FA Security"><?= icon('shield', 'sm-icon') ?> 2FA</a>
                <a href="<?= e(site_path('/admin/logout.php')) ?>" class="foot-link logout" title="Sign out"><?= icon('x', 'sm-icon') ?> Logout</a>
            </div>
        </div>
    </aside>

    <!-- MAIN WRAPPER -->
    <div class="main-wrapper">
        
        <!-- TOP HEADER -->
        <header class="top-header">
            <div class="header-left">
                <button type="button" class="mobile-nav-toggle" id="mobile-menu-toggle" aria-label="Toggle Navigation">
                    <?= icon('menu') ?>
                </button>
                
                <nav class="header-breadcrumbs" aria-label="Breadcrumb">
                    <ol>
                        <?php foreach ($breadcrumbs as $index => $crumb): ?>
                            <?php if ($index === count($breadcrumbs) - 1): ?>
                                <li class="crumb-active" aria-current="page"><?= e($crumb['name']) ?></li>
                            <?php else: ?>
                                <li><a href="<?= e($crumb['url']) ?>"><?= e($crumb['name']) ?></a></li>
                                <span class="crumb-separator">/</span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            </div>

            <div class="header-center">
                <div class="global-search-trigger" id="search-trigger" tabindex="0" role="button" aria-label="Global Search (Ctrl+K)">
                    <?= icon('search', 'search-icon') ?>
                    <span class="search-placeholder">Search leads, clients, projects, services...</span>
                    <kbd class="search-shortcut">⌘K</kbd>
                </div>
            </div>

            <div class="header-right">
                <!-- Quick Add Dropdown -->
                <div class="dropdown-wrapper" id="quick-add-dropdown">
                    <button type="button" class="btn btn-primary btn-sm quick-add-btn" id="quick-add-toggle">
                        <?= icon('plus', 'icon-sm') ?>
                        <span>Quick Add</span>
                        <?= icon('chevron-down', 'icon-xs') ?>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" id="quick-add-menu">
                        <a href="<?= e(admin_path('/admin/leads.php?action=new')) ?>" class="dropdown-item">
                            <?= icon('mail-open') ?> <span>New Lead</span>
                        </a>
                        <a href="<?= e(admin_path('/admin/clients.php?action=new')) ?>" class="dropdown-item">
                            <?= icon('building') ?> <span>New Client</span>
                        </a>
                        <a href="<?= e(admin_path('/admin/projects.php?action=new')) ?>" class="dropdown-item">
                            <?= icon('rocket') ?> <span>New Project</span>
                        </a>
                        <a href="<?= e(admin_path('/admin/tasks.php?action=new')) ?>" class="dropdown-item">
                            <?= icon('check-square') ?> <span>New Task</span>
                        </a>
                        <a href="<?= e(admin_path('/admin/services.php?action=new')) ?>" class="dropdown-item">
                            <?= icon('layers') ?> <span>Add Service</span>
                        </a>
                        <a href="<?= e(admin_path('/admin/posts.php?action=new')) ?>" class="dropdown-item">
                            <?= icon('file-pen') ?> <span>New Article</span>
                        </a>
                    </div>
                </div>

                <!-- Notifications -->
                <a href="<?= e(admin_path('/admin/notifications.php')) ?>" class="header-icon-btn" title="Notifications">
                    <?= icon('bell') ?>
                    <?php if ($unreadCount > 0): ?>
                        <span class="notification-dot"><?= $unreadCount > 9 ? '9+' : $unreadCount ?></span>
                    <?php endif; ?>
                </a>

                <!-- Admin Profile Avatar -->
                <a href="<?= e(admin_path('/admin/users.php')) ?>" class="header-user-avatar" title="<?= e($user['name'] ?? 'Admin') ?>">
                    <?= e(strtoupper(substr($user['name'] ?? 'A', 0, 2))) ?>
                </a>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="main-content">
            <?php if ($flash !== null): ?>
                <div class="toast-flash flash-<?= e($flash['type']) ?>" id="flash-message">
                    <div class="flash-content">
                        <?= icon($flash['type'] === 'ok' ? 'circle-check' : 'alert-triangle', 'flash-icon') ?>
                        <span><?= e($flash['text']) ?></span>
                    </div>
                    <button type="button" class="flash-close" onclick="this.parentElement.remove()">✕</button>
                </div>
            <?php endif; ?>

            <div class="page-container">
<?php
}

function admin_foot(): void
{
    ?>
            </div><!-- /.page-container -->
        </main><!-- /.main-content -->
    </div><!-- /.main-wrapper -->
</div><!-- /.shell -->

<!-- GLOBAL SEARCH MODAL (CMD+K) -->
<div class="modal-backdrop" id="search-modal-backdrop" style="display:none;">
    <div class="modal-dialog search-modal-dialog">
        <div class="search-modal-header">
            <?= icon('search', 'search-modal-icon') ?>
            <input type="text" id="global-search-input" class="search-modal-input" placeholder="Type to search leads, clients, projects, services, blog..." autocomplete="off">
            <kbd class="modal-esc-key">ESC</kbd>
        </div>
        <div class="search-modal-body" id="global-search-results">
            <div class="search-hint">
                <span>Start typing to perform instant search across RAFly Operating System...</span>
            </div>
        </div>
        <div class="search-modal-footer">
            <span><kbd>↑</kbd> <kbd>↓</kbd> Navigate</span>
            <span><kbd>↵</kbd> Select</span>
            <span><kbd>ESC</kbd> Close</span>
        </div>
    </div>
</div>

</body>
</html>
<?php
}
