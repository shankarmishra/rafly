<?php
require __DIR__ . '/inc/bootstrap.php';
require_once __DIR__ . '/inc/repo/resources.php';

$slug = (string)($_GET['slug'] ?? '');
$res  = resource_find($slug);

if ($res === null) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$isIndexable = resource_is_indexable($slug);

$crumbs = [
    ['name' => 'Home',      'url' => '/'],
    ['name' => 'Resources', 'url' => '/resources'],
    ['name' => $res['title'], 'url' => '/resources/' . $slug],
];

$page = [
    'id'        => 'resources',
    'title'     => $res['title'] . ' | RAFly Technical Resources',
    'desc'      => $res['intro'],
    'bodyClass' => 'page-resource-detail',
    'styles'    => ['home', 'service'],
    'module'    => 'home',
    'canonical' => 'resources/' . $slug,
    'noindex'   => !$isIndexable,
    'schema'    => [
        schema_breadcrumbs($crumbs),
        [
            '@context'         => 'https://schema.org',
            '@type'            => 'TechArticle',
            '@id'              => SITE_ORIGIN . '/resources/' . $slug . '#article',
            'headline'         => $res['title'],
            'description'      => $res['intro'],
            'inLanguage'       => 'en',
            'datePublished'    => $res['updated_at'],
            'dateModified'     => $res['updated_at'],
            'author'           => [
                '@type' => 'Organization',
                'name'  => 'RAFly Digital Growth Partner',
                'url'   => SITE_ORIGIN,
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => 'RAFly Digital Growth Partner',
                'url'   => SITE_ORIGIN,
            ],
            'mainEntityOfPage' => SITE_ORIGIN . '/resources/' . $slug,
        ],
    ],
];

require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/header.php';
require __DIR__ . '/partials/social-rail.php';
?>
<main id="main">
    <!-- HERO -->
    <section class="section hero sig-hero blueprint-canvas" style="padding-block: 4rem; position: relative;">
        <div class="container" style="max-width: 900px;">
            <div class="machined-badge machined-badge-blue" style="margin-bottom: 1.2rem;">
                <span class="glow-dot-active"></span> <?= e($res['badge']) ?>
            </div>
            <h1 style="font-size: clamp(2.2rem, 3.8vw, 3.4rem); font-weight: 800; color: #050f33; line-height: 1.12; letter-spacing: -0.025em; margin-bottom: 1.25rem;">
                <?= e($res['title']) ?>
            </h1>
            <p style="font-size: 1.1rem; color: #334155; line-height: 1.65; margin-bottom: 2rem;">
                <?= e($res['intro']) ?>
            </p>
            <div style="font-size: 0.88rem; color: #64748b; font-family: var(--font-mono); border-top: 1px solid #e2e8f0; padding-top: 1.25rem; display: flex; gap: 1.5rem; flex-wrap: wrap;">
                <span>By <?= e($res['author']) ?></span>
                <span>&middot;</span>
                <span>Updated <?= e($res['updated_at']) ?></span>
                <span>&middot;</span>
                <span><?= e($res['read_time']) ?></span>
            </div>
        </div>
    </section>

    <!-- KEY TECHNICAL HIGHLIGHTS -->
    <section class="section band-soft">
        <div class="container" style="max-width: 900px;">
            <div class="machined-card" style="background: #ffffff; border-color: rgba(10,99,255,0.2);">
                <strong style="color: #0a63ff; font-family: var(--font-mono); font-size: 0.85rem; display: block; margin-bottom: 0.75rem;">KEY TECHNICAL ARCHITECTURE HIGHLIGHTS</strong>
                <ul style="margin: 0; padding-left: 1.2rem; display: grid; gap: 0.6rem; color: #334155; font-size: 0.98rem; line-height: 1.6;">
                    <?php foreach ($res['highlights'] as $item): ?>
                        <li><?= e($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </section>

    <!-- DETAILED CONTENT SECTIONS -->
    <section class="section">
        <div class="container" style="max-width: 900px;">
            <div style="display: grid; gap: 3rem;">
                <?php foreach ($res['sections'] as $sec): ?>
                    <article id="<?= e($sec['id']) ?>">
                        <h2 style="font-size: 1.6rem; font-weight: 800; color: #050f33; margin-bottom: 1rem; border-bottom: 2px solid #e2e8f0; padding-bottom: 0.5rem;">
                            <?= e($sec['title']) ?>
                        </h2>
                        <div style="font-size: 1.05rem; color: #334155; line-height: 1.75; white-space: pre-line;">
                            <?= e($sec['content']) ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- FAQS SECTION -->
    <?php if (!empty($res['faqs'])): ?>
    <section class="section band-soft">
        <div class="container" style="max-width: 900px;">
            <div class="sec-head" style="margin-bottom: 2rem;">
                <span class="machined-badge machined-badge-blue" style="margin-bottom: 0.5rem;">TECHNICAL FAQS</span>
                <h2>FREQUENTLY ASKED QUESTIONS</h2>
            </div>
            <div style="display: grid; gap: 1rem;">
                <?php foreach ($res['faqs'] as $faq): ?>
                    <details class="loc-faq-item" open>
                        <summary class="loc-faq-head"><?= e($faq['q']) ?></summary>
                        <div class="loc-faq-body"><?= e($faq['a']) ?></div>
                    </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- CONTEXTUAL CALL TO ACTION -->
    <section class="section">
        <div class="container" style="max-width: 900px;">
            <div class="machined-card" style="background: linear-gradient(135deg, #050f33 0%, #0a2540 100%); color: #ffffff; padding: 2.5rem; text-align: center;">
                <h3 style="font-size: 1.8rem; font-weight: 800; color: #ffffff; margin-bottom: 1rem;">BUILD WITH RAFly MOBILE ENGINEERING</h3>
                <p style="font-size: 1.05rem; color: #cbd5e1; line-height: 1.6; max-width: 600px; margin: 0 auto 2rem;">
                    Ready to engineer a high-performance iOS, Android, or cross-platform application? Speak directly with our mobile product team.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a class="btn btn-primary btn-lg" href="/services/app-development">Explore App Development Service <?= icon('arrow-up-right') ?></a>
                    <a class="btn btn-outline-primary btn-lg" style="color: #ffffff; border-color: rgba(255,255,255,0.4);" href="/contact">Get in Touch</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
require __DIR__ . '/partials/footer.php';
