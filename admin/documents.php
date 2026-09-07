<?php
/**
 * RAFly Team OS — Embedded Business Documentation Vault
 */
require __DIR__ . '/lib/bootstrap.php';
require_can('content.view');
require __DIR__ . '/lib/layout.php';

$docFiles = glob(dirname(__DIR__) . '/RAFLY_BUSINESS_DOCUMENTATION/*.md');

admin_head(['title' => 'Embedded Docs & SOPs — RAFly Team OS', 'active' => '/admin/documents.php']);
?>
<div class="container">
    <div class="sec-head-split">
        <div>
            <p class="eyebrow">RAFly Team OS</p>
            <h1 class="display">Docs & <span class="soft">SOP Vault</span></h1>
            <p class="lead">Direct embedded access to all 38 RAFly Business & Technical Documentation PDFs.</p>
        </div>
    </div>

    <div class="grid grid-3" style="margin-top:24px; gap:16px;">
        <?php foreach ($docFiles as $file): 
            $basename = basename($file, '.md');
            $pdfPath  = site_path('/RAFLY_BUSINESS_DOCUMENTATION/' . $basename . '.pdf');
            $mdPath   = site_path('/RAFLY_BUSINESS_DOCUMENTATION/' . $basename . '.md');
        ?>
            <div class="card" style="padding:16px;">
                <h4 style="margin:0 0 8px; font-size:14px;"><?= e($basename) ?></h4>
                <div style="display:flex; gap:8px;">
                    <a class="btn btn-sm btn-primary" href="<?= e($pdfPath) ?>" target="_blank">View PDF</a>
                    <a class="btn btn-sm btn-line" href="<?= e($mdPath) ?>" target="_blank">View MD</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php
admin_foot();
