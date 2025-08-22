<?php
$pageTitle = 'Privacy Policy';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .hero-gradient {
        background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
    }
    .policy-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(245,247,250,0.9) 100%);
        border-radius: 12px;
        overflow: hidden;
    }
    .policy-section {
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .policy-section:last-child {
        border-bottom: none;
    }
    .policy-header {
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .policy-header:hover {
        background-color: rgba(110, 142, 251, 0.1);
    }
    .policy-header h3 {
        margin: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .policy-content {
        padding: 0 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }
    .policy-content.active {
        padding: 0 1.5rem 1.5rem;
        max-height: 1000px;
    }
    .toggle-icon {
        transition: transform 0.3s ease;
    }
    .toggle-icon.active {
        transform: rotate(180deg);
    }
</style>

<main class="container py-5">
    <section class="hero-section mb-5 rounded-4 overflow-hidden">
        <div class="hero-gradient text-white p-5">
            <h1 class="display-4 fw-bold mb-3">Privacy Policy</h1>
            <p class="lead">Last updated: August 17, 2025</p>
        </div>
    </section>

    <div class="policy-card shadow-sm">
        <div class="policy-section">
            <div class="policy-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Your Privacy Matters
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="policy-content">
                <p>At ChantMO, we are committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your personal information when you visit our website or use our services.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <div class="policy-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Information We Collect
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="policy-content">
                <p>We may collect the following types of information:</p>
                <ul>
                    <li>Personal identification information (Name, email address, phone number, etc.)</li>
                    <li>Order details and purchase history</li>
                    <li>Payment information (processed securely by our payment partners)</li>
                    <li>Browsing behavior and preferences</li>
                </ul>
            </div>
        </div>
        
        <div class="policy-section">
            <div class="policy-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    How We Use Your Information
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="policy-content">
                <p>We use the information we collect for various purposes:</p>
                <ul>
                    <li>To process and fulfill your orders</li>
                    <li>To improve our products and services</li>
                    <li>To communicate with you about your account or orders</li>
                    <li>To send promotional emails (you can opt-out anytime)</li>
                    <li>To prevent fraud and enhance security</li>
                </ul>
            </div>
        </div>
        
        <div class="policy-section">
            <div class="policy-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Data Security
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="policy-content">
                <p>We implement appropriate security measures to protect your personal information. However, no method of transmission over the Internet is 100% secure, and we cannot guarantee absolute security.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <div class="policy-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Changes to This Policy
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="policy-content">
                <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.</p>
            </div>
        </div>
        
        <div class="policy-section">
            <div class="policy-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Contact Us
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="policy-content">
                <p>If you have any questions about this Privacy Policy, please contact us at privacy@chantmo.com.</p>
            </div>
        </div>
    </div>
</main>

<script>
    function toggleSection(header) {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.toggle-icon');
        
        content.classList.toggle('active');
        icon.classList.toggle('active');
    }
    
    // Open first section by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstContent = document.querySelector('.policy-content');
        const firstIcon = document.querySelector('.toggle-icon');
        firstContent.classList.add('active');
        firstIcon.classList.add('active');
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>