<?php
/**
 * The four Insights articles, as data. Read by inc/tools/seed-posts.php (which
 * inserts them into the database) and by the preview seed
 * (inc/repo/seed.php) so the blog can be seen populated without a database.
 *
 * Every claim in these bodies is a general industry observation or a statement
 * about how Rafly works — no client names, no case results, no metrics.
 * CSP: no inline <script>, no third-party iframes, no hotlinked images.
 *
 * @return list<array{slug:string,title:string,tag:string,excerpt:string,meta_desc:string,read:int,body:string}>
 */
return (static function (): array {
$posts = [];

// ---------------------------------------------------------------------------
$posts[] = [
    'slug'      => 'bundled-packages-vs-freelancers',
    'title'     => 'Why Bundled Packages Beat Hiring Five Freelancers',
    'tag'       => 'Digital Growth',
    'excerpt'   => "The real cost of \"cheaper\" freelance help usually isn't the invoice — it's the hours spent coordinating people who've never spoken to each other.",
    'meta_desc' => 'Five freelancers often cost more than one bundled package once you count the coordination nobody quotes for. Here is where the time actually goes.',
    'read'      => 6,
    'body'      => <<<'HTML'
<p>On paper, hiring specialists one at a time is the cheaper option. A developer for the website, a designer for the graphics, someone for ads, someone for content, and eventually someone for the store. Five invoices, each smaller than an agency retainer. The arithmetic looks obvious.</p>

<p>The arithmetic is also incomplete, because it prices the work and ignores the coordination. And coordination is not a small line item — for most small businesses it turns out to be the largest one, except it gets paid in the owner's time rather than in rupees, which is why it never shows up in the comparison.</p>

<h2 id="the-coordination-tax">The coordination tax nobody quotes for</h2>

<p>Here is what actually happens. The developer builds the site. The content writer, who has not seen the site, writes copy for a layout they are imagining. The copy arrives 40% longer than the space allows, so someone has to decide what gets cut — and that someone is you, because you are the only person who has spoken to both of them.</p>

<p>Then the ads specialist starts a campaign and needs landing pages. The developer is on another project and can get to it next week. The designer produces creative in a palette that does not quite match the site, because they were working from a logo file rather than the live build. Nobody is doing anything wrong. There is simply no shared plan, and no one whose job it is to hold one.</p>

<blockquote><p>Every handoff between two vendors who have never spoken is a decision that lands on your desk, and you are the least equipped person to make it — not because you lack judgement, but because you are the only one without the full context of either side.</p></blockquote>

<p>Count the hours honestly for a month. The status messages, the re-explaining, the "can you send that to him as well", the version of the logo that turned out to be the old one. For most owners it is several hours a week. At any sensible valuation of an owner's time, that gap closes the price difference well before the quarter is out.</p>

<h2 id="what-actually-changes">What bundling actually changes</h2>

<p>The useful thing about a bundle is not the discount. It is that the people doing the work share a plan, a brief, and a calendar — so the decisions that used to land on you get made between them, before they reach you.</p>

<ul>
    <li><strong>Context is written down once.</strong> Your goals, your customers, your constraints — captured at the start and available to everyone, instead of re-explained to each new vendor.</li>
    <li><strong>Dependencies are visible.</strong> If the campaign needs a landing page, that is on the same schedule as the campaign, not discovered the week it launches.</li>
    <li><strong>Nothing sits in the gaps.</strong> Security, performance, and analytics are the classic orphans — everyone assumes another vendor covered them. In a bundle they belong to someone by default.</li>
    <li><strong>One accountable line.</strong> When something breaks, there is no round of vendors each reasonably explaining it was not their part.</li>
</ul>

<h2 id="when-freelancers-win">When freelancers are genuinely the right answer</h2>

<p>They often are, and it is worth being straight about when.</p>

<p>If you need one clearly-bounded thing — a logo, a single landing page, a one-off migration — a specialist is faster and cheaper, and a bundle is overkill. If you already have someone in-house who can hold the plan and brief people properly, the coordination cost mostly disappears and you keep the flexibility. And if you are still testing whether a channel works at all, hiring one person to run a small experiment beats committing to a package built around it.</p>

<p>The case for bundling gets strong when the work is <em>continuous and interdependent</em>: a site that keeps changing, content that feeds campaigns, a store whose listings feed both. That is where the handoffs multiply, and handoffs are what you are actually paying for.</p>

<h2 id="questions-to-ask">Questions worth asking either way</h2>

<p>Whichever route you take, these tend to expose the difference quickly:</p>

<ul>
    <li>Who decides when two pieces of work disagree — and is it me?</li>
    <li>If the site goes down on a Saturday, who picks up?</li>
    <li>Who is responsible for the things nobody scoped: security patches, broken links, page speed?</li>
    <li>When someone leaves, how much of the context leaves with them?</li>
</ul>

<p>An honest freelancer will tell you the answer to the first is usually "you". That is not a flaw in freelancers — it is the model working as designed. It is just worth knowing before you choose it.</p>

<p>We build our <a href="/#pricing">packages</a> around this problem specifically: <a href="/web-development">web development</a>, <a href="/content-creation">content</a>, <a href="/marketing-advertisement">marketing</a>, <a href="/web-security">security</a> and <a href="/ecommerce-support">e-commerce support</a> planned together rather than bought separately. Whether or not that is the right fit for you, the question to test any proposal against is the same one: <em>who is holding the plan?</em></p>
HTML,
];

// ---------------------------------------------------------------------------
$posts[] = [
    'slug'      => 'small-business-website-security-basics',
    'title'     => 'Five Security Basics Most Small Business Sites Skip',
    'tag'       => 'Web Security',
    'excerpt'   => "Form validation, session handling, and access control rarely make it onto a launch checklist — until something goes wrong. Here's what we check first.",
    'meta_desc' => 'The five checks we run on every site before launch: forms, sessions, access control, security headers, and what gets logged.',
    'read'      => 7,
    'body'      => <<<'HTML'
<p>Most small business sites are not compromised by anything sophisticated. They are compromised by automation — scripts that sweep thousands of domains looking for the same handful of oversights, and that do not care how small you are. Being a niche business is not cover, because nothing in the process ever looked at what your business does.</p>

<p>The good news is that the same short list closes most of it. Here is what we check on every build, in the order we check it.</p>

<h2 id="forms">1. Forms that trust what they are sent</h2>

<p>A contact form is the one place you invite strangers to send you data, so it is the first thing worth hardening. Three things matter, and they are separate:</p>

<ul>
    <li><strong>Validate on the server, always.</strong> JavaScript validation is a convenience for honest users. It is not a control — anyone can post directly to your endpoint and skip it entirely.</li>
    <li><strong>Bound every field.</strong> A description field with no length limit is an invitation to fill your disk or your database. Decide the maximum and enforce it.</li>
    <li><strong>Escape on the way out, not just on the way in.</strong> Data that is safe in the database can still be dangerous when rendered into a page or an email.</li>
</ul>

<p>One that catches people out: if submissions land in a CSV that staff open in Excel or Sheets, a field beginning <code>=</code>, <code>+</code>, <code>-</code> or <code>@</code> is treated as a live formula. That is CSV injection, and the target is not your server — it is whoever opens the file. Neutralising those leading characters costs one line.</p>

<h2 id="sessions">2. Session cookies with the flags left off</h2>

<p>Sessions are where "it works" and "it is safe" diverge most quietly, because a misconfigured session behaves perfectly until someone attacks it. Four settings do most of the work:</p>

<ul>
    <li><code>HttpOnly</code> — stops JavaScript reading the cookie, which limits what a cross-site scripting bug can steal.</li>
    <li><code>Secure</code> — stops the cookie ever being sent over plain HTTP.</li>
    <li><code>SameSite</code> — the main structural defence against cross-site request forgery.</li>
    <li><strong>Regenerate the session ID on login.</strong> Without this, an ID an attacker planted before login is still valid after it — session fixation, and it is invisible in testing.</li>
</ul>

<blockquote><p>A directive that is misspelled is not a weaker setting — it is no setting at all, applied silently. Configuration is worth verifying against what the server actually sends, not against what the code appears to say.</p></blockquote>

<h2 id="access-control">3. Access control that relies on nobody guessing the URL</h2>

<p>If an admin page checks whether a link was shown rather than whether the visitor is allowed, then the URL <em>is</em> the password. The same applies to anything uploaded or generated: an invoice PDF at a predictable path is readable by anyone who can count.</p>

<p>Two rules cover most of it. Check permission on every request, in the handler, not in the template that draws the menu. And deny by default — a new page should be inaccessible until someone explicitly opens it, rather than public until someone remembers to close it.</p>

<h2 id="headers">4. Security headers, which cost nothing</h2>

<p>These are a few lines of server configuration and they close entire categories of attack:</p>

<ul>
    <li><strong>Content-Security-Policy</strong> — the big one. Restricting scripts to your own origin makes most injected-script attacks fail even if something else lets them in.</li>
    <li><strong>X-Content-Type-Options: nosniff</strong> — stops browsers second-guessing a file's type and executing something you served as data.</li>
    <li><strong>Referrer-Policy</strong> — stops full URLs, which often carry identifiers, leaking to every third party you link to.</li>
    <li><strong>Strict-Transport-Security</strong> — makes HTTPS sticky, so the first request cannot be downgraded.</li>
</ul>

<p>A note on CSP: it is worth adopting <em>before</em> you need it, because retrofitting one onto a site full of inline scripts is genuinely painful. Building without inline scripts from the start makes the strictest policy the easy one.</p>

<h2 id="logging">5. Knowing whether anything happened at all</h2>

<p>The last item is not prevention. Most small sites cannot answer basic questions after an incident — when did this change, who was logged in, was this the first attempt or the thousandth — because nothing was ever recorded.</p>

<p>You do not need a monitoring platform. Timestamped records of logins, admin changes, and failed attempts, kept somewhere that survives the thing you are investigating, answer most of it. Two cautions: keep them out of the web root, and do not log the sensitive values themselves. A log full of passwords is a breach waiting for a reader.</p>

<h2 id="the-pattern">The pattern underneath</h2>

<p>None of this is advanced, and that is the point. These are not the measures that stop a determined, targeted attacker — they are the ones that make your site uninteresting to the automated sweep that was never targeting you in the first place. That covers the overwhelming majority of what actually goes wrong.</p>

<p>Every site we build gets this pass whether or not security was the reason we were called, because a site that is fast, well-written and quietly compromised is not a site that worked. If you want it looked at properly, that is what our <a href="/web-security">web security</a> work is.</p>
HTML,
];

// ---------------------------------------------------------------------------
$posts[] = [
    'slug'      => 'clean-product-listings-before-ads',
    'title'     => 'Clean Up Your Product Listings Before You Spend On Ads',
    'tag'       => 'E-commerce',
    'excerpt'   => 'Sending paid traffic to a messy storefront is the fastest way to burn a marketing budget. A simple listing audit before launch.',
    'meta_desc' => 'A pre-campaign listing audit that stops paid traffic landing on a storefront that cannot convert it.',
    'read'      => 5,
    'body'      => <<<'HTML'
<p>Paid traffic is unforgiving in a specific way: it does not fix anything, it only reveals things faster. If your storefront converts poorly, ads do not improve that. They just buy you a larger sample of people declining to purchase, and they charge you for each one.</p>

<p>Which is why the cheapest week of any campaign is usually the week before it starts. Here is the audit we run first.</p>

<h2 id="titles">Titles that answer the question being asked</h2>

<p>Product titles tend to be written for someone who already knows what they are looking at. "Classic Blue — Model 2" means something to you and nothing to a first-time visitor arriving from an ad.</p>

<p>A title should carry the thing itself, the distinguishing attribute, and the qualifier a buyer would search for. Not keyword stuffing — just the words a customer would actually use. If someone reading only the title cannot tell what it is, the image is doing work it should not have to do.</p>

<h2 id="images">Images, and the one that is missing</h2>

<p>The single most common gap is not image quality. It is <em>scale</em>. A product photographed alone on white tells you nothing about size, and "22cm" does not either, because almost nobody converts that into a mental picture. One photo of the item in a hand, on a desk, or beside something ordinary removes a whole category of hesitation — and a whole category of return.</p>

<p>Beyond that: consistent framing across the range so the grid does not look assembled from three different shops, and enough resolution to survive zoom. Compression matters here too — an oversized hero image is paid for twice, once in load time and once in the visitor who left before it appeared.</p>

<h2 id="descriptions">Descriptions that survive being skimmed</h2>

<p>Nobody reads a product description top to bottom. They scan for the one fact that decides it, and if they cannot find it quickly they assume it is being hidden.</p>

<ul>
    <li>Lead with what it is and who it suits, before the brand narrative.</li>
    <li>Put specifications in a list, not a paragraph. Materials, dimensions, weight, compatibility, care.</li>
    <li>Answer the objection directly. If it runs small, say it runs small — the return it prevents costs more than the sale it loses.</li>
</ul>

<blockquote><p>Every unanswered question on a product page is a reason to close the tab. Paid traffic means you are buying those tabs one at a time.</p></blockquote>

<h2 id="the-boring-fields">The boring fields that decide whether it sells</h2>

<p>These are the ones that get skipped because they are not visible on the page, and they are the ones that most often break a campaign:</p>

<ul>
    <li><strong>Stock accuracy.</strong> Advertising something out of stock is a paid apology.</li>
    <li><strong>Variant completeness.</strong> A size with no price or no image reads as broken, and shoppers generalise from one broken variant to the whole shop.</li>
    <li><strong>Shipping and returns, stated before checkout.</strong> Cost surprises at the final step are the top cart-abandonment reason in nearly every study ever run on it.</li>
    <li><strong>Categories and filters.</strong> If a product sits in the wrong category, ad traffic may land on a listing page that does not contain the thing that was advertised.</li>
</ul>

<h2 id="mechanics">Mechanics, before spend</h2>

<p>Two checks that are quick and save the most money:</p>

<p><strong>Buy something.</strong> Complete a real purchase on a phone, on mobile data, as a new customer with no saved details. Not a test-mode order — a real one, refunded afterwards. Almost every store has one step that is worse than the owner believes, and this is the only reliable way to find it.</p>

<p><strong>Confirm tracking works before you need it.</strong> Conversion tracking that was never verified is worse than none: it produces confident numbers that are wrong, and you optimise toward them for weeks. Check that a real purchase registers as one, once, with the right value.</p>

<h2 id="then-spend">Then spend</h2>

<p>None of this is exciting and none of it is fast. But a campaign pointed at a storefront that answers questions, prices honestly and checks out cleanly on a phone will outperform a better-targeted campaign pointed at one that does not — and the difference compounds for as long as the campaign runs.</p>

<p>If you would rather this were handled alongside the campaign than before it, that overlap is exactly what our <a href="/ecommerce-support">e-commerce support</a> and <a href="/marketing-advertisement">marketing</a> work covers together.</p>
HTML,
];

// ---------------------------------------------------------------------------
$posts[] = [
    'slug'      => 'ai-automation-small-business-workflows',
    'title'     => 'Exploring AI Automation For Small Business Workflows',
    'tag'       => "What's Next",
    'excerpt'   => "We're testing where automation genuinely saves time versus where it just adds another tool to babysit. Early findings from our own internal use.",
    'meta_desc' => 'Where AI automation actually saves a small team time, where it does not, and how to tell the difference before you commit.',
    'read'      => 8,
    'body'      => <<<'HTML'
<p>We have been putting AI tools through our own workflows for a while now, partly because clients keep asking and partly because we would rather answer from experience than from a vendor's landing page. This is an honest interim report: what has held up, what has not, and how we now decide before committing.</p>

<p>The headline finding is unglamorous. The tools work well on a narrower set of tasks than the marketing suggests, and on that narrower set they work better than we expected.</p>

<h2 id="what-works">Where it has genuinely saved time</h2>

<p>A pattern emerged quickly. Automation earns its place where the task is <strong>high-volume, low-stakes, and easy to check</strong>. All three conditions, not two.</p>

<ul>
    <li><strong>First drafts of repetitive copy.</strong> Product descriptions across a large catalogue, alt text, meta descriptions. The output needs editing, but editing a draft is faster than facing an empty page eighty times.</li>
    <li><strong>Reformatting and extraction.</strong> Pulling structured fields out of messy input — supplier lists, enquiry text, inconsistent spreadsheets. Genuinely reliable, and the errors are obvious when they happen.</li>
    <li><strong>Summarising for triage.</strong> Not to replace reading something, but to decide what to read first.</li>
    <li><strong>Rubber-duck review.</strong> Asking a model what is unclear about a page or what a draft fails to answer surfaces real gaps, because it has no idea what you meant.</li>
</ul>

<p>The common thread is that a wrong answer is cheap and immediately visible. That is what makes the speed worth having.</p>

<h2 id="what-does-not">Where it has cost more than it saved</h2>

<p>The failures were more instructive. Three kinds:</p>

<p><strong>Anything requiring facts we have not supplied.</strong> Asked for something specific it does not know, a model produces something plausible instead — a confident, well-written answer that is wrong. In a client-facing context that is not a time saving, it is a liability, and verifying every claim costs more than writing it yourself.</p>

<p><strong>Work where checking is as hard as doing.</strong> Anything requiring judgement about a specific business, a real relationship, or a decision with consequences. If verifying the output means reconstructing the reasoning, automation has moved the work rather than removed it.</p>

<p><strong>Tools that need managing.</strong> This one surprised us most. Several automations technically worked and still lost on net, because each one added a thing to configure, monitor, and fix when it silently stopped. A small team can absorb a limited number of moving parts; every automation spends some of that budget.</p>

<blockquote><p>The question is not "can this be automated" — increasingly the answer is yes. It is "will the automation cost less attention than the task did", and that answer is often no.</p></blockquote>

<h2 id="how-we-decide">How we decide now</h2>

<p>Four questions, in order. A no anywhere stops it.</p>

<ul>
    <li><strong>How often does this actually happen?</strong> Measured, not estimated. Automating something monthly rarely repays setting it up.</li>
    <li><strong>What does a wrong answer cost?</strong> If it is embarrassment in front of a customer or a bad number in a report someone acts on, the bar rises steeply.</li>
    <li><strong>Can it be checked in seconds?</strong> If not, expect the checking to become the new task.</li>
    <li><strong>Who fixes it when it breaks?</strong> Not if. An automation with no named owner becomes an outage nobody notices for a week.</li>
</ul>

<h2 id="the-part-worth-saying">The part worth saying out loud</h2>

<p>There is real pressure to present AI capability as further along than it is, and we would rather not add to it. What we can say honestly is that we are using these tools daily on internal work, we have a clearer picture than we did six months ago of where the line sits, and we are not going to recommend automating something because it is currently interesting.</p>

<p>The useful version of this for a small business is almost never "replace a role". It is finding the two or three repetitive, checkable tasks that quietly consume hours a week, and taking those back. That is a smaller claim than most of what you will read, and it is the one we can stand behind.</p>

<p>If you have a workflow you suspect falls in that category, it is worth a conversation before it is worth a tool. Our <a href="/#contact">contact form</a> reaches us, or the <a href="/#pricing">packages</a> page explains how ongoing work is structured.</p>
HTML,
];

// ---------------------------------------------------------------------------
$posts[] = [
    'slug'      => 'choosing-a-web-development-partner-delhi-ncr',
    'title'     => 'How to Choose a Web Development Partner in Delhi NCR',
    'tag'       => 'Digital Growth',
    'excerpt'   => "A rebuild is expensive to get wrong twice. The questions worth asking before you sign — wherever the agency you're evaluating is based.",
    'meta_desc' => 'What to actually ask a web development agency in Delhi NCR before you commit — ownership, scope, timelines, and who shows up after launch.',
    'read'      => 6,
    'body'      => <<<'HTML'
<p>Delhi NCR has no shortage of web development agencies — Noida and Greater Noida alone have a dense cluster of them, ranging from one-person operations to large outsourcing shops. That makes the decision harder, not easier, because the pitch decks tend to converge on the same three words — "fast", "affordable", "quality" — regardless of what actually happens after you sign.</p>

<p>This is the list of things worth checking before you do, in the order we think they matter.</p>

<h2 id="ownership">Who owns the code when it is finished</h2>

<p>Ask this first, in writing, before discussing anything else. Some agencies build on a platform or a codebase they control, and "handover" means a set of login credentials to something you cannot move without them. Others hand over the actual repository and full intellectual property on final payment — you can walk away, take the code to anyone else, and lose nothing.</p>

<p>There is no universally correct answer here — a managed platform can be the right trade-off for the right business — but you want to know which one you are agreeing to, not discover it the day you want to switch developers.</p>

<h2 id="scope">Whether the scope is written down before work starts</h2>

<p>A verbal understanding of "a website with a few pages and a contact form" is not a scope. It is an invitation for both sides to remember the conversation differently three weeks in. A written scope should say how many pages, which integrations, what is explicitly excluded, and what happens to a request that falls outside it — a change order, or a quiet assumption that it was always included.</p>

<blockquote><p>The agencies worth working with will write the boundaries down without being asked, because they have been burned by not doing it at least once. The ones who resist a written scope are usually not being difficult — they are keeping the door open to renegotiate later, on their terms.</p></blockquote>

<h2 id="timeline">Whether the timeline survives contact with reality</h2>

<p>"Two weeks" for a real business website with content, integrations and a review cycle is rarely true, and an agency that promises it either has not thought it through or is planning to blow past it quietly. A credible timeline breaks into stages — discovery, scope and plan, build, launch — each with its own estimate, because that is what lets you see where a delay is actually happening instead of just hearing "we're a bit behind" in week five.</p>

<h2 id="security">Whether security is part of the build or an afterthought</h2>

<p>Ask directly: is a security review part of the standard build, or a separate line item you have to request? Form handling, session management and basic access control are not exotic — they are baseline work — but plenty of builds ship without them because nobody asked and nobody offered. Retrofitting security onto a live site with real customer data is a worse position than building it in from day one.</p>

<h2 id="local">Why local presence still matters, even for remote-friendly work</h2>

<p>Most of what a web build actually requires — a call, a shared document, a staging link to review — works perfectly well over a video call with an agency anywhere. But being based in the same city or region removes friction at specific moments: a launch that benefits from someone in the room, a stakeholder who would rather meet than call, a timezone that never has to be negotiated. For a business operating in Greater Noida or the wider Delhi NCR area, that is a genuine, if secondary, advantage worth weighing — not a requirement, but a tiebreaker.</p>

<h2 id="after-launch">What happens the week after launch</h2>

<p>The build is the visible part. What decides whether the relationship was worth it is what happens after: is there a support window included, or does every question after handover generate a new invoice? Is there a named person who answers when something breaks on a Saturday, or a ticket queue that resolves eventually? Ask for this in writing too — it is the part most often left to a vague verbal assurance.</p>

<h2 id="the-checklist">The short version</h2>

<ul>
    <li>Who owns the code and the repository when the project ends?</li>
    <li>Is the scope written down, including what is explicitly excluded?</li>
    <li>Does the timeline break into stages you can actually check progress against?</li>
    <li>Is a security review included, or a separate ask?</li>
    <li>What support exists after launch, and who is it from?</li>
</ul>

<p>These questions work regardless of which agency you are evaluating, in Delhi NCR or anywhere else. We answer all five plainly on our own <a href="/web-development">web development</a> page and our <a href="/locations/greater-noida">Greater Noida</a> page, including the specific things we do not take on — because a published limit is easier to trust than a verbal promise to do everything.</p>
HTML,
];

    return $posts;
})();
