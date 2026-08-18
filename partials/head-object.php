<?php
/**
 * A 3-D object for a secondary page's head.
 *
 * The homepage hero got the big one. Naveen asked for the rest of the site to
 * carry the same language rather than falling back to flat pages, so this is
 * the same object at a third of the presence: nine satellites instead of
 * twenty-two, a tighter frame, held to one side, and gone by the time the first
 * real section arrives.
 *
 * This WAS a js/gl.js point cloud, and it was replaced for the same reason the
 * hero's was: "ye 3d design bilkul pasand nahi". Dots have no surface, so they
 * cannot catch an environment, so they cannot look manufactured. The knot can.
 *
 * WHAT IT COSTS, PLAINLY. three.js is ~730 KB raw / ~190 KB gzipped, and this
 * puts it on seven secondary pages that previously did not load it at all. The
 * gates all still hold — WebGL2 or nothing, never under reduced motion, never
 * on Save-Data or 2G, loaded only when the head is within 300px of the
 * viewport, and any failure at all leaves the designed still in place.
 */
?>
<div class="head-3d three-stage" data-stage3d="head" aria-hidden="true">
    <div class="three-stage-still" aria-hidden="true"></div>
</div>
