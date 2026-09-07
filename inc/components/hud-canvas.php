<?php
/**
 * HUD Canvas Animation Component
 * Renders interactive canvas containers for page-specific visual telemetry.
 *
 * Usage:
 *   <?php render_hud_canvas('microservices', ['status' => 'SYSTEM ONLINE', 'nodes' => 4]); ?>
 */
function render_hud_canvas(string $type, array $config = []): string {
    $status = $config['status'] ?? 'SYSTEM ONLINE';
    $fps = $config['fps'] ?? '60 FPS';
    
    $html = '<div class="hud-canvas-container" data-hud-type="' . e($type) . '">';
    $html .= '<div class="hud-canvas-header">';
    $html .= '  <span class="hud-status-tag"><span class="pulse-dot"></span> ' . e($status) . '</span>';
    $html .= '  <span class="hud-telemetry-tag">' . e($fps) . '</span>';
    $html .= '</div>';
    $html .= '<canvas class="hud-canvas-element" data-type="' . e($type) . '" width="800" height="300" aria-label="Interactive HUD Animation"></canvas>';
    $html .= '<div class="hud-canvas-footer">';
    $html .= '  <span class="hud-node-count">// REALTIME TELEMETRY ENGINE</span>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}
