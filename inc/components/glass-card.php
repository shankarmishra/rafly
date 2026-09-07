<?php
/**
 * Glass Card Component Helper
 * Renders a glassmorphic HUD card container.
 *
 * Usage:
 *   <?php render_glass_card('Title', 'Description', ['badge' => 'HUD ACTIVE']); ?>
 */
function render_glass_card(string $title, string $content, array $options = []): string {
    $badge = $options['badge'] ?? '';
    $icon  = $options['icon'] ?? '';
    $class = $options['class'] ?? '';
    
    $html = '<div class="glass-card ' . e($class) . '">';
    if ($badge !== '') {
        $html .= '<div class="hud-badge"><span class="pulse-dot"></span> ' . e($badge) . '</div>';
    }
    if ($icon !== '') {
        $html .= '<div class="glass-card-icon"><svg class="i-' . e($icon) . '"><use href="' . e(asset('vendor/icons/sprite.svg')) . '#i-' . e($icon) . '"></use></svg></div>';
    }
    if ($title !== '') {
        $html .= '<h3 class="glass-card-title hud-text">' . e($title) . '</h3>';
    }
    $html .= '<div class="glass-card-body">' . $content . '</div>';
    $html .= '</div>';
    return $html;
}
