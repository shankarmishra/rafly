<?php
/**
 * The inner-page head field.
 *
 * Twelve pages include this partial, inside .page-head. It was DELIBERATELY
 * EMPTY, and it held the argument for that: a page header is two columns of
 * prose, an object behind it competes with the only thing the reader came for,
 * and the version before that had put ~190 KB of WebGL on /about, /pricing and
 * every blog post to decorate a heading.
 *
 * THAT DECISION IS REVERSED, and the reason it is safe to reverse is that this
 * is not the same thing. What was here was an OBJECT — a three.js stage, then
 * an SVG redraw of one — sitting behind the words. What is here now is a
 * GROUND: a lit grid with a sweep passing over it, at the same 42px module
 * js/field.js uses on the homepage, so an inner page reads as the same system
 * framed tighter rather than as a second visual language.
 *
 * IT IS PURE CSS. No canvas, no module, no bytes of JavaScript. That is the
 * whole reason it can go on twelve pages at once: eleven of them currently
 * ship 59 KB of JS against an 80 KB budget, and this adds nothing to it. It
 * also means it survives Save-Data, a dead GPU and scripts being off, which an
 * ornament on twelve pages has to.
 *
 * IT TAKES THE PAGE'S OWN ACCENT FOR FREE. The sweep and the nodes read
 * --accent-fg, which css/00-tokens.css remaps per body.svc-* — so each of the
 * five service pages gets its own colour with no per-page code, and a
 * .ground-chapter ancestor would flip it correctly too.
 *
 * Everything is aria-hidden and pointer-events: none. It says nothing.
 */
?>
<div class="head-field" aria-hidden="true">
    <span class="head-field-grid"></span>
    <span class="head-field-sweep"></span>
    <span class="head-field-orb head-field-orb-a"></span>
    <span class="head-field-orb head-field-orb-b"></span>
</div>
