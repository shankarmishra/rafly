<?php
/**
 * Neon Glow Utility Component
 * Wraps elements in GenZ neon glow borders and ambient light halos.
 */
function render_neon_box(string $content, string $variant = 'cyan'): string {
    return '<div class="neon-glow-box neon-' . e($variant) . '">' . $content . '</div>';
}
