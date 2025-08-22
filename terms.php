<?php
$pageTitle = 'Terms of Service';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .hero-gradient {
        background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
    }
    .terms-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(245,247,250,0.9) 100%);
        border-radius: 12px;
        overflow: hidden;
    }
    .terms-section {
        border-bottom: 1px solid rgba(0,0,0,0.1);
    }
    .terms-section:last-child {
        border-bottom: none;
    }
    .terms-header {
        padding: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .terms-header:hover {
        background-color: rgba(110, 142, 251, 0.1);
    }
    .terms-header h3 {
        margin: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .terms-content {
        padding: 0 1.5rem;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease, padding 0.3s ease;
    }
    .terms-content.active {
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
            <h1 class="display-4 fw-bold mb-3">Terms of Service</h1>
            <p class="lead">Last updated: August 17, 2025</p>
        </div>
    </section>

    <div class="terms-card shadow-sm">
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Terms and Conditions
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>Welcome to ChantMO! These terms and conditions outline the rules and regulations for the use of our website and services.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Acceptance of Terms
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>By accessing this website, you agree to be bound by these Terms of Service and agree that you are responsible for compliance with any applicable local laws.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Orders and Payments
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>All orders are subject to product availability. We reserve the right to refuse or cancel any order for any reason at any time. Prices are subject to change without notice.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Product Information
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>We make every effort to display our products as accurately as possible. However, we cannot guarantee that your device's display of any color will be accurate.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Returns and Refunds
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>Please review our Return Policy for details about returning products and requesting refunds. This policy is available on our website.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    User Accounts
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>You are responsible for maintaining the confidentiality of your account and password. You agree to accept responsibility for all activities that occur under your account.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Limitation of Liability
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>ChantMO shall not be liable for any special or consequential damages resulting from the use of, or the inability to use, the services or products on this site.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Governing Law
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>These terms shall be governed by and construed in accordance with the laws of Ghana, without regard to its conflict of law provisions.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Changes to Terms
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>We reserve the right to modify these terms at any time. Your continued use of the site after such changes constitutes your acceptance of the new terms.</p>
            </div>
        </div>
        
        <div class="terms-section">
            <div class="terms-header" onclick="toggleSection(this)">
                <h3 class="fw-bold">
                    Contact Information
                    <i class="fas fa-chevron-down toggle-icon"></i>
                </h3>
            </div>
            <div class="terms-content">
                <p>If you have any questions about these Terms, please contact us at legal@chantmo.com.</p>
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
        const firstContent = document.querySelector('.terms-content');
        const firstIcon = document.querySelector('.toggle-icon');
        firstContent.classList.add('active');
        firstIcon.classList.add('active');
    });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>