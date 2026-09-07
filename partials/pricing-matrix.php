<?php
/**
 * Package Comparison Matrix & Inclusions Partial.
 * Renders universal inclusions, comparison table, and referral policy note.
 */
?>
<section class="section band-soft pricing-matrix-section">
    <div class="container">
        <!-- Universal Inclusions -->
        <div class="sec-head sec-head-center" style="margin-bottom: 2.5rem;">
            <p class="eyebrow">Included in Every Package</p>
            <h2>Standard delivery <span class="soft">guarantees</span></h2>
            <p class="lead">Every RAFly package includes foundational ownership, review cycles, and direct support.</p>
        </div>

        <div class="grid grid-4" style="gap: 1.5rem; margin-bottom: 4rem;">
            <div class="card card-hover" style="padding: 1.5rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.15); border-radius: var(--r-xl);">
                <div class="icon-box" style="color: #0a63ff; margin-bottom: 1rem;"><?= icon('shield') ?></div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #06122f; margin-bottom: 0.4rem;">100% IP &amp; Asset Ownership</h3>
                <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5;">Full source code, domain access, and repository ownership transferred upon project completion.</p>
            </div>
            <div class="card card-hover" style="padding: 1.5rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.15); border-radius: var(--r-xl);">
                <div class="icon-box" style="color: #0a63ff; margin-bottom: 1rem;"><?= icon('refresh-cw') ?></div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #06122f; margin-bottom: 0.4rem;">Two Revision Rounds</h3>
                <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5;">Two formal feedback cycles during staging before final production launch.</p>
            </div>
            <div class="card card-hover" style="padding: 1.5rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.15); border-radius: var(--r-xl);">
                <div class="icon-box" style="color: #0a63ff; margin-bottom: 1rem;"><?= icon('monitor') ?></div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #06122f; margin-bottom: 0.4rem;">Live Staging Demo</h3>
                <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5;">Test drive your site or app on a private staging link before DNS migration.</p>
            </div>
            <div class="card card-hover" style="padding: 1.5rem; background: #ffffff; border: 1px solid rgba(10,99,255,0.15); border-radius: var(--r-xl);">
                <div class="icon-box" style="color: #0a63ff; margin-bottom: 1rem;"><?= icon('message-square') ?></div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: #06122f; margin-bottom: 0.4rem;">Direct WhatsApp SLA</h3>
                <p style="font-size: 0.88rem; color: #475569; margin: 0; line-height: 1.5;">Direct access to lead engineers with guaranteed initial response SLAs.</p>
            </div>
        </div>

        <!-- Comparison Matrix Table -->
        <div class="sec-head-split" style="margin-bottom: 1.5rem;">
            <div>
                <p class="eyebrow">Side-by-Side Comparison</p>
                <h2>Package <span class="soft">feature matrix</span></h2>
            </div>
            <p class="lead">Compare scope, deliverables, and retainers across all three commercial tiers.</p>
        </div>

        <div style="overflow-x: auto; background: #ffffff; border: 1px solid #cbd5e1; border-radius: var(--r-2xl); box-shadow: 0 10px 30px rgba(6,18,47,0.04); margin-bottom: 3rem;">
            <table style="width: 100%; border-collapse: collapse; min-width: 680px; text-align: left;">
                <thead>
                    <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <th style="padding: 1.2rem 1.5rem; font-family: var(--font-mono, monospace); font-size: 0.78rem; font-weight: 800; color: #0f172a; width: 28%;">Feature / Capability</th>
                        <th style="padding: 1.2rem 1.5rem; font-size: 0.95rem; font-weight: 800; color: #0a63ff; width: 24%;">Starter Build (Local Services)</th>
                        <th style="padding: 1.2rem 1.5rem; font-size: 0.95rem; font-weight: 800; color: #2563eb; width: 24%;">Growth Engine (E-Commerce)</th>
                        <th style="padding: 1.2rem 1.5rem; font-size: 0.95rem; font-weight: 800; color: #dc2626; width: 24%;">Emergency Defense (Security)</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem; color: #334155;">
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Target Audience</td>
                        <td style="padding: 1rem 1.5rem;">Service SMBs, Clinics &amp; Practices</td>
                        <td style="padding: 1rem 1.5rem;">Scaling D2C Brands &amp; Merchants</td>
                        <td style="padding: 1rem 1.5rem;">Compromised Web Infrastructure</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9; background: #fafafa;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Initial Investment</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 800; color: #0f172a;">₹25,000 – ₹60,000</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 800; color: #0f172a;">₹45,000 – ₹1,00,000</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 800; color: #991b1b;">₹12,000 – ₹25,000</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Website Pages / Scope</td>
                        <td style="padding: 1rem 1.5rem;">5 – 10 Custom Pages</td>
                        <td style="padding: 1rem 1.5rem;">15+ Storefront Pages</td>
                        <td style="padding: 1rem 1.5rem;">Existing Site Clean &amp; Recovery</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9; background: #fafafa;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">E-Commerce &amp; Payments</td>
                        <td style="padding: 1rem 1.5rem; color: #64748b;">Optional Add-on</td>
                        <td style="padding: 1rem 1.5rem; color: #16a34a; font-weight: 700;">✓ Full Shopify / Woo Sync</td>
                        <td style="padding: 1rem 1.5rem; color: #94a3b8;">N/A</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Speed Benchmark</td>
                        <td style="padding: 1rem 1.5rem; color: #16a34a; font-weight: 700;">✓ &lt; 1.5s Load Benchmark</td>
                        <td style="padding: 1rem 1.5rem; color: #16a34a; font-weight: 700;">✓ &lt; 1.5s Load Benchmark</td>
                        <td style="padding: 1rem 1.5rem; color: #0284c7;">Optimized &amp; Hardened</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9; background: #fafafa;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Security Level</td>
                        <td style="padding: 1rem 1.5rem;">Baseline Hardening + SSL</td>
                        <td style="padding: 1rem 1.5rem;">Advanced WAF &amp; PCI-DSS</td>
                        <td style="padding: 1rem 1.5rem; color: #dc2626; font-weight: 700;">1-Hr SLA Emergency Malware Clean</td>
                    </tr>
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Lead Qualification Flow</td>
                        <td style="padding: 1rem 1.5rem;">WhatsApp Direct Routing</td>
                        <td style="padding: 1rem 1.5rem;">WhatsApp + CRM Webhook Sync</td>
                        <td style="padding: 1rem 1.5rem;">Direct Hotline Triage</td>
                    </tr>
                    <tr style="background: #fafafa;">
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #0f172a;">Growth / Security Retainer</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 700;">Optional: ₹10k – ₹20k / mo</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #2563eb;">Growth: ₹35k – ₹75k / mo</td>
                        <td style="padding: 1rem 1.5rem; font-weight: 700; color: #dc2626;">Managed Sec: ₹5k – ₹15k / mo</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Referral Reward Policy Banner -->
        <div style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%); border: 1px solid #bfdbfe; border-radius: var(--r-xl); padding: 1.8rem 2rem; display: flex; align-items: center; justify-content: space-between; gap: 1.5rem; flex-wrap: wrap;">
            <div style="max-width: 680px;">
                <span class="badge badge-soft-blue" style="font-size: 0.75rem; font-weight: 700; margin-bottom: 0.4rem; display: inline-block;">RAFly CLIENT REFERRAL PROGRAM</span>
                <h3 style="font-size: 1.25rem; font-weight: 800; color: #1e3a8a; margin: 0 0 0.3rem 0;">Earn 10% Cash Back / Credit on Every Client Referral</h3>
                <p style="font-size: 0.92rem; color: #1e40af; margin: 0; line-height: 1.5;">
                    Refer a business to RAFly and receive <strong>10% of the total project value (up to ₹10,000)</strong> as an account credit or direct bank transfer within 14 days of deposit receipt.
                </p>
            </div>
            <div>
                <a class="btn btn-primary" href="/contact" style="white-space: nowrap;">Refer a Client <?= icon('arrow-right') ?></a>
            </div>
        </div>
    </div>
</section>
