<?php
$pageTitle = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<style>
    .hero-gradient {
        background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
    }
    .value-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(245,247,250,0.9) 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .value-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .icon-box {
        background: linear-gradient(135deg, rgba(110,142,251,0.1) 0%, rgba(167,119,227,0.1) 100%);
    }
</style>

<main class="container py-5">
    <section class="hero-section mb-5 rounded-4 overflow-hidden">
        <div class="hero-gradient text-white p-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">About ChantMO</h1>
                    <p class="lead">Your trusted supermarket for quality products at affordable prices.</p>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
                         alt="Supermarket" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <section class="mb-5">
        <div class="row g-4">
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Our Story</h2>
                <p class="lead">Founded in 2023, ChantMO has grown from a small local store to a trusted supermarket serving customers across the region.</p>
                <p>We started with a simple mission: to provide fresh, high-quality groceries at prices everyone can afford. Today, we continue that tradition while expanding our product range and services to meet your evolving needs.</p>
            </div>
            <div class="col-lg-6">
                <div class="value-card h-100 p-4">
                    <h3 class="fw-bold mb-3">Our Values</h3>
                    <div class="d-flex align-items-start mb-3">
                        <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-check-circle text-primary"></i>
                        </div>
                        <div>
                            <h4 class="h5">Quality Products</h4>
                            <p class="mb-0">We carefully select our products to ensure they meet our high standards.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-tag text-primary"></i>
                        </div>
                        <div>
                            <h4 class="h5">Affordable Prices</h4>
                            <p class="mb-0">We work directly with suppliers to bring you the best prices.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                            <i class="fas fa-heart text-primary"></i>
                        </div>
                        <div>
                            <h4 class="h5">Customer Satisfaction</h4>
                            <p class="mb-0">Your happiness is our top priority.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-light rounded-4 p-5 mb-5" style="background: linear-gradient(135deg, rgba(110,142,251,0.05) 0%, rgba(167,119,227,0.05) 100%);">
        <h2 class="fw-bold mb-4 text-center">Why Choose Us</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center">
                    <div class="icon-box mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5">Fast Delivery</h4>
                    <p>Get your orders delivered quickly to your doorstep.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="icon-box mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-headset fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5">24/7 Support</h4>
                    <p>Our team is always ready to assist you.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center">
                    <div class="icon-box mx-auto rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="fas fa-shield-alt fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5">Quality Guarantee</h4>
                    <p>We stand behind the quality of our products.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>