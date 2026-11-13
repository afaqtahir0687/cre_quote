<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CmsPage;

class CmsPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'subtitle' => 'Empowering Printing & Custom Fabrication Businesses with Unified Cloud ERP',
                'footer_column' => 'company',
                'meta_title' => 'About CreLogics ERP - Complete Business Management Solution',
                'meta_description' => 'Learn how CreLogics ERP is modernizing printing shops, signage manufacturers, and fabricators across Pakistan, USA, and worldwide.',
                'sort_order' => 1,
                'is_active' => true,
                'is_system' => true,
                'content' => <<<HTML
<p>CreLogics ERP is a specialized, modern cloud business management platform designed specifically for printing presses, signage manufacturers, advertising firms, fabrication workshops, and multi-branch enterprises. We bridge the gap between initial customer estimation and final delivery, eliminating operational bottlenecks and lost revenue.</p>

<h3>The CreLogics Story</h3>
<p>For decades, printing presses and custom signage companies struggled with disconnected paper job bags, uncoordinated WhatsApp messages, manual rate sheets, and untracked material scrap. Crucial customer specs were lost, deadlines were breached, and invoice collection trailed months behind.</p>
<p>CreLogics was engineered from the shop floor up to solve these exact headaches. We integrated margin calculators, instant PDF quotation generators, live job order status tracking, digital proof sign-offs, and multi-branch inventory tracking into one intuitive cloud system.</p>

<h3>Our Core Principles</h3>
<p>We build software with three non-negotiable standards:</p>
<ul>
    <li><strong>Precision:</strong> Quotations and formulas that ensure every square foot, quantity, and material cost delivers an honest profit margin.</li>
    <li><strong>Simplicity:</strong> An interface clear enough for shop technicians and robust enough for financial accountants.</li>
    <li><strong>Reliability:</strong> 99.9% uptime with cloud data isolation ensuring your rates and customer directories remain strictly confidential.</li>
</ul>
HTML
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'subtitle' => 'How CreLogics Protects, Manages, and Respects Your Business Data',
                'footer_column' => 'legal',
                'meta_title' => 'Privacy Policy - CreLogics Cloud ERP',
                'meta_description' => 'Our strict privacy guidelines ensuring full confidentiality, 256-bit encryption, and tenant isolation for your customer data and financial records.',
                'sort_order' => 2,
                'is_active' => true,
                'is_system' => true,
                'content' => <<<HTML
<p>This Privacy Policy outlines our commitment to protecting your company, employee, customer, and transaction data across CreLogics Cloud ERP.</p>

<h3>1. Information We Collect</h3>
<p>When you register for a CreLogics account, utilize our 7-day free trial, or deploy our multi-branch modules, we collect the following categories of information:</p>
<ul>
    <li><strong>Account Identification:</strong> Business name, shop registration, contact person, primary email address, phone numbers, and physical facility addresses.</li>
    <li><strong>Operational Business Records:</strong> Customer directories, quotation line items, job order specifications, delivery notes, and tax invoice summaries entered into your shop database.</li>
    <li><strong>System & Authentication Logs:</strong> IP address, device browser type, login timestamps, and employee role access audits to safeguard system integrity.</li>
</ul>

<h3>2. Strict Tenant Data Isolation</h3>
<p>CreLogics utilizes modern multi-tenant architecture with scoped data isolation. Your shop's customer lists, unit prices, supplier costs, and job logs are strictly locked to your authorized shop ID. No other tenant, shop, or unauthorized user can ever access or view your operational figures.</p>

<h3>3. How We Use Your Information</h3>
<p>Your information is used strictly to provide, maintain, and enhance the ERP service, including:</p>
<ul>
    <li>Generating professional quotes, invoices, and delivery notes branded with your custom shop logo and stamps.</li>
    <li>Delivering automated notification alerts regarding job completion, payment receipts, or trial renewal reminders.</li>
    <li>Providing responsive technical customer support and resolving any database or workflow queries.</li>
    <li>We NEVER sell, lease, or monetize your business or client data to third-party advertisers.</li>
</ul>

<h3>4. Data Security & Storage</h3>
<p>All communication between your browser and our servers is secured with 256-bit TLS/SSL encryption. Database backups are taken on an automated, redundant schedule and stored in secure cloud environments with multi-factor authentication.</p>

<h3>5. Data Ownership & Export Rights</h3>
<p>You remain the sole owner of all data inputted into CreLogics. You maintain the right to export your customers, quotes, and reports in spreadsheet or PDF formats at any time. Upon account termination, your data can be purged permanently upon written request.</p>

<h3>6. Contacting Our Privacy Officer</h3>
<p>If you have any questions or data compliance requests, please email our security team at info@crelogics.com.</p>
HTML
            ],
            [
                'title' => 'Terms & Conditions',
                'slug' => 'terms-conditions',
                'subtitle' => 'Platform Service Terms, Acceptable Use, and Subscription Guidelines',
                'footer_column' => 'legal',
                'meta_title' => 'Terms & Conditions - CreLogics Cloud ERP',
                'meta_description' => 'Official service agreement, trial terms, account responsibilities, and subscription policies for CreLogics ERP subscribers.',
                'sort_order' => 3,
                'is_active' => true,
                'is_system' => true,
                'content' => <<<HTML
<p>Please read these terms carefully before creating an account or accessing the CreLogics ERP platform.</p>

<h3>1. Agreement to Terms</h3>
<p>By creating an account, registering a shop, or using any CreLogics software, websites, or APIs, you agree to be bound by these Terms and Conditions. If you do not agree to these terms, you must not access or use the platform.</p>

<h3>2. 7-Day Free Trial</h3>
<p>New subscribers receive an initial 7-Day Unrestricted Free Trial upon registration.</p>
<ul>
    <li>No credit card or payment commitment is required to start your trial.</li>
    <li>You enjoy full access to quotation creation, job orders, invoicing, and delivery tracking.</li>
    <li>At the end of the 7-day period, you may choose an official subscription package to continue active business operations uninterrupted.</li>
</ul>

<h3>3. User Account & Security</h3>
<p>You are responsible for maintaining the confidentiality of your shop administrative credentials and passwords. You agree to notify us immediately of any unauthorized breach or account misuse. CreLogics is not liable for any losses caused by uncompromised local credentials.</p>

<h3>4. Acceptable Platform Use</h3>
<p>CreLogics is intended for legitimate commercial management. You agree NOT to:</p>
<ul>
    <li>Reverse engineer, decompile, or attempt to extract source code from the cloud application.</li>
    <li>Transmit malicious code, viruses, or conduct automated denial-of-service tests against our infrastructure.</li>
    <li>Use the system for fraudulent invoicing or illegal transactional documentation.</li>
</ul>

<h3>5. Service Availability & Uptime SLA</h3>
<p>We target a 99.9% platform availability uptime. Scheduled system maintenance or server upgrades are conducted during off-peak hours with advance notice posted to the administrative dashboard whenever practical.</p>

<h3>6. Termination of Service</h3>
<p>You may terminate your account at any time. CreLogics reserves the right to suspend or terminate accounts that violate system integrity, engage in fraudulent activity, or fail to resolve prolonged expired subscription statuses after repeated notifications.</p>
HTML
            ],
            [
                'title' => 'Refund & Cancellation Policy',
                'slug' => 'refund-policy',
                'subtitle' => 'Transparent Guidelines on Subscription Renewals, Cancellations, and Refunds',
                'footer_column' => 'legal',
                'meta_title' => 'Refund & Cancellation Policy - CreLogics Cloud ERP',
                'meta_description' => 'Review CreLogics policies regarding 7-day trials, renewal cancellations, and refund eligibility.',
                'sort_order' => 4,
                'is_active' => true,
                'is_system' => true,
                'content' => <<<HTML
<p>At CreLogics, we pride ourselves on transparent, honest pricing with zero hidden fees. We want every client to feel completely confident in our cloud software.</p>

<h3>1. Try Before You Pay (7-Day Trial)</h3>
<p>Because we provide a full-featured, zero-risk 7-Day Free Trial, you have the opportunity to test real quotes, job sheets, branch switching, and invoices before spending a single dollar or rupee. We encourage all businesses to thoroughly evaluate the system during this trial period.</p>

<h3>2. Subscription Cancellation</h3>
<p>You can cancel your subscription renewal at any time directly through your Super Admin / Shop Subscription dashboard or by emailing info@crelogics.com.</p>
<ul>
    <li>When you cancel, your account remains fully active until the end of your current paid billing cycle.</li>
    <li>You will not be charged for subsequent billing periods following your cancellation request.</li>
    <li>Your data remains securely preserved for at least 30 days following cancellation so you can reactivate if needed.</li>
</ul>

<h3>3. Refund Eligibility</h3>
<p>Refunds are evaluated fairly under the following criteria:</p>
<ul>
    <li><strong>Duplicate Charges:</strong> If you are accidentally billed multiple times due to gateway errors, a 100% immediate refund of the duplicate amount will be issued.</li>
    <li><strong>Service Non-Delivery:</strong> If prolonged server downtime prevents your business from using the system for more than 48 continuous hours, pro-rated service credits or refunds will be granted upon investigation.</li>
    <li><strong>Annual Subscriptions:</strong> If you choose an annual package and request cancellation within the first 14 days of activation, you are eligible for a refund minus the standard single-month monthly rate.</li>
</ul>

<h3>4. Requesting a Refund</h3>
<p>To request a refund, please send your shop name, invoice number, and reason for the request to info@crelogics.com. Our support team responds within 24 to 48 business hours.</p>
HTML
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'subtitle' => 'Get in Touch with CreLogics Sales, Technical Support & Onboarding Teams',
                'footer_column' => 'company',
                'meta_title' => 'Contact CreLogics ERP - Pakistan & USA Offices',
                'meta_description' => 'Contact CreLogics ERP team for product demonstrations, technical assistance, custom pricing, or multi-branch consultations.',
                'sort_order' => 5,
                'is_active' => true,
                'is_system' => true,
                'content' => <<<HTML
<p>Have a question about our cloud ERP, need help setting up your printing/signage business, or want a personalized workflow demonstration? Our support and engineering teams are ready to assist you.</p>

<h3>Direct Assistance Across Time Zones</h3>
<p>Whether you need on-site onboarding assistance in Pakistan or remote operational support in North America, our specialists are accessible via phone, email, and live WhatsApp chat.</p>

<h3>Frequently Asked Inquiries</h3>
<ul>
    <li><strong>Trial Onboarding:</strong> Assistance with importing your existing customers, products, and rate lists into CreLogics.</li>
    <li><strong>Custom Pricing & Quotation Formulas:</strong> Setting up square-foot calculations, acrylic sheets, vinyl rolls, and banner printing costs.</li>
    <li><strong>Multi-Branch Configuration:</strong> Connecting multiple workshop branches or retail outlets under one central database.</li>
</ul>
HTML
            ],
        ];

        foreach ($pages as $p) {
            CmsPage::updateOrCreate(
                ['slug' => $p['slug']],
                $p
            );
        }
    }
}
