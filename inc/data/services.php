<?php
/**
 * SEED DATA — the five services, as editorial content.
 *
 * TEMPORARY SOURCE. This is the one entity the site does not yet have an admin
 * table for, so it lives here as a literal array rather than pretending to be
 * dynamic. inc/repo/services.php is the interface every template reads through;
 * when a `services` table exists, that repository grows a DB branch exactly the
 * way bundles_all() already has, and NOTHING in the templates changes.
 *
 * Do not read this file from a template. Call services_all() / service_find().
 *
 * It previously existed three times over: as SERVICES (slug => label) in
 * inc/config.php, as a $services array of card copy in index.php, and as
 * $serviceData in service.php. Same five services, three files, maintained
 * separately — the homepage called one of them "Automation" while the nav and
 * the detail page both called it "Content Creation".
 *
 * Content rules this array must keep honouring:
 *  - no client names, no invented metrics, no ranking or lead guarantees,
 *    no uptime or response-time promises;
 *  - timeframes use the same vocabulary the homepage uses (2-3 Days /
 *    3-5 Days / 2-6 Weeks / 1 Week / Ongoing) so the two cannot disagree;
 *  - no photography. Every service page draws its own scene (partials/
 *    scene.php); the licensed stock that used to sit here is gone, because a
 *    photo of strangers under a heading beginning "Our" is a claim.
 *
 * Per-service presentation keys added when this moved out of service.php:
 *   key       short handle used to build CSS accent classes (svc-web, …)
 *   card      one-line summary for the homepage bento — deliberately shorter
 *             than `intro`, which is the detail page's opening paragraph
 *   wide      whether the homepage bento gives this card the double-width cell
 *   scene     LEGACY. Named a 3D object set for a partial that no longer
 *             exists; nothing reads it. Kept so the array shape is unchanged
 */

return [
    'web-development' => [
        'title' => 'Web Development',
        'icon' => 'code',
        'key'   => 'web',
        'wide'  => true,
        'scene' => 'browser',
        'card'  => 'Responsive, performance-focused websites and web apps built to support your growth.',
        'badge' => 'Design & Build',
        'tagline' => 'Sites and web apps that load fast, read clearly, and do not fall over as you grow.',
        'intro' => 'We create fast, secure, and conversion-focused websites and web apps designed to support your growth from day one.',
        'points' => [
            'Responsive layouts tailored for desktop and mobile audiences.',
            'Clear information architecture and performance-focused development.',
            'Scalable platforms suitable for products, services, and internal operations.'
        ],
        'highlights' => [
            'Custom business websites',
            'Landing pages and marketing funnels',
            'SaaS, admin, and client portal development'
        ],
        'signals' => [
            'Your site was built years ago by someone you can no longer reach, and nobody wants to touch the code.',
            'Every small change — a new page, a price update — turns into a week of back-and-forth.',
            'It looks fine on a laptop and falls apart on the phone most of your visitors actually use.',
        ],
        'deliverables' => [
            ['icon' => 'compass',  'title' => 'Discovery and scope',        'desc' => 'We map what the site has to do, who it is for, and what can honestly wait until version two.'],
            ['icon' => 'layers',   'title' => 'Information architecture',   'desc' => 'Page structure, navigation and URLs planned before anything gets designed or built.'],
            ['icon' => 'code',     'title' => 'Front-end build',            'desc' => 'Responsive layouts built from your real content and checked at phone widths, not guessed at on a desktop.'],
            ['icon' => 'database', 'title' => 'Back-end and integrations',  'desc' => 'Forms, dashboards, payment gateways and CRMs — feasibility confirmed during discovery before we commit to it.'],
            ['icon' => 'gauge',    'title' => 'Performance pass',           'desc' => 'Image sizing, caching and script weight sorted before launch rather than filed as a later fix.'],
            ['icon' => 'shield',   'title' => 'Baseline security review',   'desc' => 'Forms, sessions and access handling reviewed as part of the build — included, not an optional add-on.'],
        ],
        'outcomes' => [
            'A site your team can update without filing a ticket with us.',
            'Pages that hold together on the phone screen most of your traffic arrives on.',
            'A codebase you own outright, documented enough for whoever works on it next.',
        ],
        'process' => [
            ['title' => 'Discovery',          'time' => '2-3 Days',  'desc' => 'A call and a look at what you already have — goals, current setup, and the parts causing the most friction.'],
            ['title' => 'Scope & plan',       'time' => '3-5 Days',  'desc' => 'We write the scope down: pages, features, integrations, and what is explicitly out of it. You approve before we build.'],
            ['title' => 'Build',              'time' => '2-6 Weeks', 'desc' => 'Design, front-end, back-end and content move together, with something viewable early instead of a reveal at the end.'],
            ['title' => 'Launch & handover',  'time' => '1 Week',    'desc' => 'Migration, redirects and a baseline security review, then a support window afterwards. Full IP transfers to you on final payment.'],
        ],
        'tools' => [
            ['icon' => 'code',     'label' => 'PHP &amp; Laravel'],
            ['icon' => 'layers',   'label' => 'WordPress'],
            ['icon' => 'terminal', 'label' => 'JavaScript'],
            ['icon' => 'database', 'label' => 'MySQL'],
            ['icon' => 'settings', 'label' => 'Staging &amp; deploys'],
            ['icon' => 'gauge',    'label' => 'Core Web Vitals'],
        ],
        'boundaries' => [
            ['title' => 'Native mobile apps',        'desc' => 'We build for the browser. App development is something we are still building out, so a true iOS or Android app is better placed with a dedicated studio.'],
            ['title' => 'Brand identity from scratch','desc' => 'We can work to an existing brand or tidy one up, but logo design and full visual identity work is not what we do best.'],
            ['title' => 'Enterprise replatforming',  'desc' => 'Moving a large legacy system onto a new stack is a specialist job with its own risks. We would rather say so early than discover it halfway through.'],
        ],
        'faqs' => [
            ['q' => 'Do you work on existing sites or only new builds?', 'a' => 'Both, and most of our work is on sites that already exist. We audit what is there before changing anything.'],
            ['q' => 'Who owns the code when we are done?',              'a' => 'You do. Full IP ownership transfers to you on final payment, and the repository and access are handed over with it.'],
            ['q' => 'How long does a typical build take?',              'a' => 'Discovery takes 2-3 days and scoping another 3-5. The build itself usually runs 2-6 weeks depending on how many pages and integrations are involved.'],
            ['q' => 'What happens after launch?',                       'a' => 'Bundled packages include a support window after launch. Beyond that we offer ongoing monthly plans for sites that need continuous attention.'],
        ],
    ],
    'web-security' => [
        'title' => 'Web Security',
        'icon' => 'shield',
        'key'   => 'security',
        'wide'  => true,
        'scene' => 'shield',
        'card'  => 'Practical security reviews and hardening for your site, forms, and customer data.',
        'badge' => 'Review & Hardening',
        'tagline' => 'A practical look at what someone probing your site would find first — and what to fix before they do.',
        'intro' => 'We help businesses harden their digital presence through practical security reviews, risk reduction, and safer infrastructure choices.',
        'points' => [
            'Security audits and vulnerability checks.',
            'Protection guidance for forms, sessions, and access handling.',
            'Recommendations to improve resilience and trust.'
        ],
        'highlights' => [
            'Threat review and mitigation planning',
            'Secure form and authentication improvements',
            'Best-practice hardening for public-facing systems'
        ],
        'signals' => [
            'You collect customer data and nobody has actually checked whether the forms collecting it are safe.',
            'A plugin or library has not been updated in over a year and you are not sure what breaks if it is.',
            'Someone got into an account, the password was changed, and you still do not know how they got in.',
        ],
        'deliverables' => [
            ['icon' => 'search',         'title' => 'Surface review',           'desc' => 'We look at your site the way someone probing it would — exposed endpoints, outdated software, and anything publicly visible that should not be.'],
            ['icon' => 'file-pen',       'title' => 'Form and input handling',  'desc' => 'Validation, sanitisation and rate limiting on every form that accepts data from strangers.'],
            ['icon' => 'lock',           'title' => 'Session and access review','desc' => 'How logins, sessions and roles are handled, and who can reach what once they are inside.'],
            ['icon' => 'shield',         'title' => 'Configuration hardening',  'desc' => 'Response headers, transport settings, error handling and file permissions brought to sane defaults.'],
            ['icon' => 'database',       'title' => 'Backup and recovery check','desc' => 'Whether backups exist, whether they actually restore, and how long that would realistically take.'],
            ['icon' => 'message-circle', 'title' => 'Plain-language report',    'desc' => 'What we found, ranked by what to fix first, written so a non-technical decision maker can act on it.'],
        ],
        'outcomes' => [
            'A written list of what is wrong, ordered by how much it matters.',
            'The easy wins closed before they turn into an incident.',
            'A real answer to "are we secure?" instead of an assumption.',
        ],
        'process' => [
            ['title' => 'Discovery',           'time' => '2-3 Days',  'desc' => 'We agree scope in writing — which domains and systems are in, which are off limits — before anyone touches anything.'],
            ['title' => 'Review & plan',       'time' => '3-5 Days',  'desc' => 'The assessment itself, followed by findings ranked by severity and effort so you can decide what gets fixed.'],
            ['title' => 'Remediation',         'time' => '2-6 Weeks', 'desc' => 'We work through the agreed fixes with your team, or hand the ranked list over if you would rather do it in-house.'],
            ['title' => 'Re-check & handover', 'time' => '1 Week',    'desc' => 'We verify the fixes landed and leave you with the report. NDAs are signed on request.'],
        ],
        'tools' => [
            ['icon' => 'lock',     'label' => 'SSL &amp; TLS config'],
            ['icon' => 'shield',   'label' => 'WAF rules'],
            ['icon' => 'package',  'label' => 'Dependency updates'],
            ['icon' => 'users',    'label' => 'Access &amp; roles'],
            ['icon' => 'search',   'label' => 'Log review'],
            ['icon' => 'database', 'label' => 'Backup checks'],
        ],
        'boundaries' => [
            ['title' => 'Formal penetration testing', 'desc' => 'An accredited pen test with a signed certificate is a different discipline. If a customer or auditor is asking for one, engage a specialist firm.'],
            ['title' => 'Compliance certification',   'desc' => 'We can help you tidy things up ahead of an audit, but we are not auditors and cannot sign off on ISO, SOC 2 or PCI compliance.'],
            ['title' => 'Live incident response',     'desc' => 'If you are being actively attacked right now, your host and a dedicated incident response team will move faster than we can.'],
        ],
        'faqs' => [
            ['q' => 'Will the review take our site offline?',       'a' => 'No. The work is non-disruptive by design, and anything carrying any risk to a live site is agreed with you and scheduled first.'],
            ['q' => 'Do we need to sign anything before you start?','a' => 'We agree the scope in writing, and we sign NDAs on request. We will not test a system you have not explicitly authorised.'],
            ['q' => 'Do you fix what you find, or just report it?', 'a' => 'Either. Most clients want the fixes done, but some prefer the ranked list and hand it to their own developers.'],
            ['q' => 'Is security included in your other services?', 'a' => 'A baseline security review — forms, sessions and access handling — is part of every bundled package rather than an add-on. This service goes further than that baseline.'],
        ],
    ],
    'marketing-advertisement' => [
        'title' => 'Marketing & Advertisement',
        'icon' => 'trending-up',
        'key'   => 'marketing',
        'wide'  => false,
        'scene' => 'signal',
        'card'  => 'Targeted campaigns and audience strategy built around measurable results.',
        'badge' => 'Campaigns & Audience',
        'tagline' => 'Campaigns built around who is actually buying, and reporting you can read without a translator.',
        'intro' => 'We design campaigns that align messaging, audience targeting, and conversion goals so your channels perform with clarity.',
        'points' => [
            'Audience-focused campaign planning and positioning.',
            'Content structure that supports better engagement and leads.',
            'Performance tracking and practical marketing recommendations.'
        ],
        'highlights' => [
            'Paid advertising strategy',
            'Brand and outreach messaging',
            'Lead generation campaign support'
        ],
        'signals' => [
            'Ads are running, money is leaving the account, and nobody can say which part of it is working.',
            'The traffic numbers look healthy but the enquiries have not moved.',
            'Your last monthly report was full of charts that never turned into a decision.',
        ],
        'deliverables' => [
            ['icon' => 'users',          'title' => 'Audience and positioning','desc' => 'Who we are actually talking to, what they are comparing you against, and the angle worth leading with.'],
            ['icon' => 'megaphone',      'title' => 'Channel plan',            'desc' => 'Which platforms are worth your budget, and which are a distraction at your current size.'],
            ['icon' => 'pencil',         'title' => 'Ad and landing copy',     'desc' => 'Written alongside the page people land on, so the click and the promise waiting for them agree.'],
            ['icon' => 'trending-up',    'title' => 'Campaign setup',          'desc' => 'Structure, targeting and budgets configured inside your own ad accounts, not ours.'],
            ['icon' => 'pie-chart',      'title' => 'Tracking and events',     'desc' => 'Analytics and conversion events set up so the numbers mean something before any spend starts.'],
            ['icon' => 'message-circle', 'title' => 'Reporting you can read',  'desc' => 'What ran, what it cost, what it produced and what we would change — in plain language.'],
        ],
        'outcomes' => [
            'A clear line between what you spent and what it produced.',
            'Fewer channels run properly, instead of a thin presence everywhere.',
            'Reports that end in a decision rather than a chart.',
        ],
        'process' => [
            ['title' => 'Discovery',        'time' => '2-3 Days',  'desc' => 'Your goals, your margins, the accounts you already have, and what has been tried before.'],
            ['title' => 'Plan & scope',     'time' => '3-5 Days',  'desc' => 'Channels, budget split, messaging angles, and the tracking that has to exist before anything is allowed to run.'],
            ['title' => 'Build & launch',   'time' => '2-6 Weeks', 'desc' => 'Creative, copy, landing pages and account setup — then campaigns go live in a controlled, staged way.'],
            ['title' => 'Review & adjust',  'time' => 'Ongoing',   'desc' => 'Regular reviews against what we agreed, with changes explained to you rather than applied quietly.'],
        ],
        'tools' => [
            ['icon' => 'trending-up', 'label' => 'Google Ads'],
            ['icon' => 'facebook',    'label' => 'Meta Ads'],
            ['icon' => 'pie-chart',   'label' => 'Analytics 4'],
            ['icon' => 'settings',    'label' => 'Tag Manager'],
            ['icon' => 'search',      'label' => 'Search Console'],
            ['icon' => 'mail',        'label' => 'Email campaigns'],
        ],
        'boundaries' => [
            ['title' => 'Guaranteed rankings or leads', 'desc' => 'Nobody can honestly promise a search position or a number of leads. Anyone who does is guessing with your money.'],
            ['title' => 'Large-scale media buying',     'desc' => 'Budgets at the level where you need a dedicated buying desk and direct publisher deals are past what we take on.'],
            ['title' => 'Influencer and PR management', 'desc' => 'We can point you toward people, but running talent relationships and press outreach is not a service we offer.'],
        ],
        'faqs' => [
            ['q' => 'Do we keep control of the ad accounts?', 'a' => 'Yes. Campaigns are built in accounts you own, so if we part ways the history and the assets stay with you.'],
            ['q' => 'What can you promise on results?',       'a' => 'Nothing about rankings or lead counts — those depend on your market, your offer and your budget. What we commit to is a clear plan, honest reporting, and every change explained.'],
            ['q' => 'Is ad spend included in the price?',     'a' => 'No. What you pay the platforms is separate from what you pay us, and it is billed to your own accounts.'],
            ['q' => 'How soon do campaigns go live?',         'a' => 'Discovery takes 2-3 days and planning another 3-5. Setup and creative typically runs 2-6 weeks depending on how much has to be built first.'],
        ],
    ],
    'content-creation' => [
        'title' => 'Content Creation',
        'icon' => 'pencil',
        'key'   => 'content',
        'wide'  => false,
        'scene' => 'sheets',
        'card'  => 'Website copy, social content, and brand messaging that sounds like you, not a template.',
        'badge' => 'Words & Messaging',
        'tagline' => 'Copy that says what you do, in your words, without the filler everyone skims past.',
        'intro' => 'We create polished brand content that helps businesses speak clearly, present value confidently, and strengthen their digital presence.',
        'points' => [
            'Website and campaign content writing.',
            'Messaging that reflects your business tone and goals.',
            'Structured content for better clarity and trust.'
        ],
        'highlights' => [
            'Service page content',
            'Social and promotional copy',
            'Brand voice and storytelling support'
        ],
        'signals' => [
            'Your homepage takes three paragraphs to say what the business actually does.',
            'Every page sounds like it was written by a different person, because it was.',
            'You have been meaning to rewrite the service pages for a year and keep not doing it.',
        ],
        'deliverables' => [
            ['icon' => 'compass',  'title' => 'Messaging groundwork',    'desc' => 'What you do, who for, and why they should pick you — settled before a single page gets written.'],
            ['icon' => 'file-pen', 'title' => 'Website and service copy','desc' => 'Homepage, service and about copy written to an agreed structure rather than poured into a template.'],
            ['icon' => 'megaphone','title' => 'Campaign and ad copy',    'desc' => 'Landing pages and ad text written alongside the campaign so the two never contradict each other.'],
            ['icon' => 'share',    'title' => 'Social and promo copy',   'desc' => 'Posts and captions in a voice that matches the site instead of inventing a second personality.'],
            ['icon' => 'search',   'title' => 'Search-aware structure',  'desc' => 'Headings, page titles and descriptions written for readers first and search engines second.'],
            ['icon' => 'history',  'title' => 'Revisions',               'desc' => 'Two rounds of changes built into the scope, because a first draft is something to react to.'],
        ],
        'outcomes' => [
            'A visitor who knows what you sell within seconds of landing.',
            'One voice across the site instead of five.',
            'Pages you are actually willing to send a prospect to.',
        ],
        'process' => [
            ['title' => 'Discovery',            'time' => '2-3 Days',  'desc' => 'A call about the business, the customer, and the words you already use when you describe it out loud.'],
            ['title' => 'Outline & scope',      'time' => '3-5 Days',  'desc' => 'Page list, structure and key messages agreed in writing before any drafting starts.'],
            ['title' => 'Drafting',             'time' => '2-6 Weeks', 'desc' => 'Copy written page by page and delivered in batches, so you are reacting to real work as it appears.'],
            ['title' => 'Revisions & handover', 'time' => '1 Week',    'desc' => 'Two rounds of changes, then final copy handed over in a format your developer can use directly.'],
        ],
        'tools' => [
            ['icon' => 'file-pen',  'label' => 'Brand tone guides'],
            ['icon' => 'search',    'label' => 'Keyword research'],
            ['icon' => 'gauge',     'label' => 'Search Console'],
            ['icon' => 'pie-chart', 'label' => 'Analytics 4'],
            ['icon' => 'layers',    'label' => 'WordPress'],
            ['icon' => 'history',   'label' => 'Editorial calendar'],
        ],
        'boundaries' => [
            ['title' => 'Video production',       'desc' => 'We can write the script. Filming, editing and motion work need a production team, and we would rather say so than subcontract it badly.'],
            ['title' => 'Technical documentation','desc' => 'API references and developer docs need a specialist technical writer sitting next to your engineers.'],
            ['title' => 'Translation',            'desc' => 'We write in English. Translating your site properly means a native speaker in the target market, not a tool.'],
        ],
        'faqs' => [
            ['q' => 'Will the copy sound like us or like an agency?', 'a' => 'Like you, if we do it right. Most of the discovery call is us listening to how you already describe the business, then tightening it.'],
            ['q' => 'How many rounds of changes do we get?',          'a' => 'Two rounds are in the scope as standard. If a page needs a rethink rather than an edit, we will say so rather than quietly bill for it.'],
            ['q' => 'Do you write for SEO?',                          'a' => 'We write for readers, with structure, headings and descriptions search engines can read properly. We do not promise rankings, because nobody honestly can.'],
            ['q' => 'Can you work from our existing content?',        'a' => 'Usually the faster route. We audit what is already there, keep what works, and rewrite the rest.'],
        ],
    ],
    'ecommerce-support' => [
        'title' => 'E-commerce Support',
        'icon' => 'shopping-cart',
        'key'   => 'ecom',
        'wide'  => false,
        'scene' => 'commerce',
        'card'  => 'Storefront structure, listings, and operational support for online sellers.',
        'badge' => 'Storefront & Operations',
        'tagline' => 'The unglamorous side of selling online — listings, orders, reconciliation — kept in order.',
        'intro' => 'We support online sellers with operational structure, product presentation, account clarity, and scalable systems that reduce friction.',
        'points' => [
            'Product listing structure and storefront clarity.',
            'Order-flow, reconciliation, and operational support.',
            'Practical systems for compliance, reporting, and growth.'
        ],
        'highlights' => [
            'Storefront and catalog support',
            'Account reconciliation and admin processes',
            'Growth-focused operational guidance'
        ],
        'signals' => [
            'The catalogue has grown to the point where nobody is quite sure which listings are still accurate.',
            'Month-end reconciliation between the store, the gateway and the accounts takes days.',
            'Orders keep arriving and the process for handling them lives entirely in one person\'s head.',
        ],
        'deliverables' => [
            ['icon' => 'package',       'title' => 'Catalogue structure',     'desc' => 'Categories, attributes and product data organised so the store stays navigable as it grows.'],
            ['icon' => 'image',         'title' => 'Product presentation',    'desc' => 'Titles, descriptions and image standards applied consistently across the whole catalogue.'],
            ['icon' => 'shopping-cart', 'title' => 'Checkout review',         'desc' => 'Where people drop out of the buying flow, and what can be removed from the path in front of them.'],
            ['icon' => 'settings',      'title' => 'Order-flow documentation','desc' => 'The steps from order to dispatch written down, so the process survives someone being on leave.'],
            ['icon' => 'database',      'title' => 'Reconciliation support',  'desc' => 'Matching store, gateway and payout records so month-end stops being an investigation.'],
            ['icon' => 'pie-chart',     'title' => 'Reporting setup',         'desc' => 'Sales, stock and returns reporting configured so you can see the store without exporting spreadsheets.'],
        ],
        'outcomes' => [
            'A catalogue you can add to without it getting messier.',
            'A month-end that closes in hours instead of days.',
            'Processes written down instead of remembered.',
        ],
        'process' => [
            ['title' => 'Discovery',           'time' => '2-3 Days',  'desc' => 'A look at the store, your order volume, and the part of the current process that hurts most.'],
            ['title' => 'Scope & plan',        'time' => '3-5 Days',  'desc' => 'What gets fixed first, what can wait, and how the result is handed back to your team.'],
            ['title' => 'Implementation',      'time' => '2-6 Weeks', 'desc' => 'Catalogue work, process documentation and reporting setup, staged so the store keeps selling throughout.'],
            ['title' => 'Handover & support',  'time' => 'Ongoing',   'desc' => 'Your team walked through the new process, with ongoing monthly support available if you want it.'],
        ],
        'tools' => [
            ['icon' => 'shopping-cart', 'label' => 'Shopify'],
            ['icon' => 'layers',        'label' => 'WooCommerce'],
            ['icon' => 'lock',          'label' => 'Payment gateways'],
            ['icon' => 'pie-chart',     'label' => 'Analytics 4'],
            ['icon' => 'database',      'label' => 'Inventory records'],
            ['icon' => 'package',       'label' => 'Marketplace accounts'],
        ],
        'boundaries' => [
            ['title' => 'Warehousing and fulfilment', 'desc' => 'We work on the systems around the order. Actually storing and shipping your stock needs a fulfilment partner, not us.'],
            ['title' => 'Bookkeeping and filing',     'desc' => 'We can get store data into a state your accountant can use. We are not accountants and will not file anything on your behalf.'],
            ['title' => 'Staffing your inbox',        'desc' => 'We can document how enquiries should be handled. Answering them day to day is not something we take on.'],
        ],
        'faqs' => [
            ['q' => 'Which platforms do you work with?',          'a' => 'Most commonly Shopify and WooCommerce. If you are on something else, tell us early and we will be straight about whether we know it well enough to help.'],
            ['q' => 'Do you need access to our store admin?',     'a' => 'Yes, at the access level the work needs and no more. We tell you exactly what we need and why, and we sign NDAs on request.'],
            ['q' => 'Can you work while the store keeps trading?','a' => 'That is the normal case. Work is staged so the storefront stays live, and anything disruptive is scheduled with you in advance.'],
            ['q' => 'Is this a one-off or ongoing?',              'a' => 'Either. Some clients want a one-time cleanup, others move onto a monthly plan for continuous support.'],
        ],
    ]
];
