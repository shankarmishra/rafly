<?php
/**
 * Animated Typography Component
 * Renders Space Grotesk / Inter gradient headlines with glowing text effects.
 */
function render_hud_headline(string $text, string $tag = 'h2', string $accent = 'cyan-purple'): string {
    return "<{$tag} class=\"hud-headline gradient-{$accent}\">" . e($text) . "</{$tag}>";
}
