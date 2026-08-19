# Content Strategy & Topic Map — rafly.in

## The topic map

```
PILLAR: Bundled digital delivery (the core differentiation argument)
 └─ /blog/bundled-packages-vs-freelancers                    (existing)
     ├─ /web-development
     ├─ /web-security
     ├─ /marketing-advertisement
     ├─ /content-creation
     ├─ /ecommerce-support
     └─ /pricing

PILLAR: Choosing a partner in Delhi NCR (commercial-investigation, local)
 └─ /blog/choosing-a-web-development-partner-delhi-ncr        (NEW, this engagement)
     ├─ /web-development
     └─ /locations/greater-noida

CLUSTER: Web security
 └─ /blog/small-business-website-security-basics              (existing)
     └─ /web-security

CLUSTER: E-commerce operations
 └─ /blog/clean-product-listings-before-ads                   (existing)
     └─ /ecommerce-support

CLUSTER: What's next (forward-looking, deliberately hedged)
 └─ /blog/ai-automation-small-business-workflows               (existing)
     — intentionally NOT linked to a service page: AI automation is not
       a service Rafly offers today (see index.php's honest "actively
       building out" framing). Linking it to a commercial page would
       misrepresent current capability.
```

## Why only one new pillar, not three

The original engagement plan (see the approved plan file) scoped 2-3 new
pillar pages. On inspection, **two of the three planned topics already
existed** as genuine, well-written articles:

- "What bundled digital delivery actually means" ≈
  `bundled-packages-vs-freelancers` (already live, already covers this
  argument in depth).
- "Web security baseline for small business sites" ≈
  `small-business-website-security-basics` (already live).

Writing new pages covering the same ground would have been the exact
"keyword cannibalization" and "duplicate target pages" failure mode Section
11 of the brief explicitly asks this engagement to detect and avoid. The one
genuinely uncovered high-value topic — **choosing a web development partner
in Delhi NCR** — was written instead. This is the correct outcome of doing
the topic-map exercise honestly rather than treating a pre-agreed page count
as a target to hit regardless of what the map actually showed.

## Sourcing discipline

Every claim in the new article traces to something already true and
verifiable in the codebase:

- The five evaluation questions (ownership, scope, timeline, security,
  after-launch support) are drawn directly from `inc/data/services.php`'s
  real `process`, `deliverables`, and `boundaries` arrays for
  `web-development` — not invented criteria.
- The "published limits build trust" argument is the same argument
  `competitors.md` verifies with live evidence: none of the three researched
  competitors publish a boundaries list; Rafly's service pages do, and did
  before this engagement.
- No client is named, no metric is invented, no ranking or lead volume is
  promised — the same content rules `inc/data/services.php`'s own header
  comment states for the service data, applied to the new article.

## Real production content, not preview-only

The article was added to `inc/data/seed-posts.php` — the file explicitly
documented as the *real*, production-intended editorial pipeline (as opposed
to `inc/data/seed-preview.php`, explicitly marked sample/preview-only content
that must not survive into production). Publishing it required extending
`inc/tools/seed-posts.php` to also create and link a real category on every
run — closing a gap where new articles added after initial launch had no
durable way to receive a category at all (see `audit.md`'s "real bug found
only by testing against production data"). Verified end-to-end against the
actual local development database, not just the design-preview fallback.

## Editorial standards this engagement did not relax

- No fabricated case study, statistic, testimonial, rating, award, or
  certification — anywhere, including in the new article.
- No mass content generation. One article, chosen because the topic map
  showed it was the one genuine gap, not because a target count required
  filling.
- No AI-citation-bait sections, no keyword-stuffed question blocks. The
  article is structured for a human reader first (the same H2/list/blockquote
  rhythm the four existing articles already use), which is also what makes
  it legible to an AI answer engine — Section 17 of the brief's "concise
  direct answers, clearly structured sections" guidance was followed by
  matching the site's own existing voice, not by adding a separate
  "AI-optimized" block.

## Remaining gaps (not filled — see `roadmap.md`)

- `content-creation` and `marketing-advertisement` have no supporting article
  at all — a real gap, but writing one without a specific, well-scoped angle
  (the way "choosing a partner" had a clear local + commercial-investigation
  angle) would risk exactly the generic, low-differentiation content this
  strategy is built to avoid.
- `/case-studies` has no real content to point new commercial-investigation
  traffic at once it arrives — genuinely blocked on client consent and real
  data, not a content-strategy decision this engagement can resolve.
