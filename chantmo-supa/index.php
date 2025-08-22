<?php
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/Product.php';

// Get products for different sections
$featuredProducts = Product::getFeatured(4, true); // Get 4 random featured products
$newProducts = Product::getNewArrivals(4);
$discountedProducts = Product::getDiscountedProducts(4, true); // Get 4 random discounted products
?>

<!-- New Supermarket Hero Slider -->
<section class="supermarket-hero">
    <div class="supermarket-slider">
        <!-- Slide 1 - Fresh Produce -->
        <div class="slide active" style="background-image: url('https://images.unsplash.com/photo-1542838132-92c53300491e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
            <div class="slide-content">
                <h2>Fresh Groceries</h2>
                <p>Discover our selection of farm-fresh fruits and vegetables delivered daily</p>
                <a href="<?= base_url('beverages.php') ?>" class="slide-btn">Shop Produce</a>
            </div>
        </div>
        
        <!-- Slide 2 - Special Offers -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1606787366850-de6330128bfc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
            <div class="slide-content">
                <h2>Weekly Specials</h2>
                <p>Save big on your favorite items with our exclusive weekly discounts</p>
                <a href="<?= base_url('deals.php') ?>" class="slide-btn">View Offers</a>
            </div>
        </div>
        
        <!-- Slide 3 - New Arrivals -->
        <div class="slide" style="background-image: url('https://images.unsplash.com/photo-1550583724-b2692b85b150?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80');">
            <div class="slide-content">
                <h2>New Arrivals</h2>
                <p>Explore our latest products and seasonal favorites</p>
                <a href="<?= base_url('new-products.php') ?>" class="slide-btn">Discover More</a>
            </div>
        </div>
        
        <div class="slider-dots">
            <div class="dot active" data-slide="0"></div>
            <div class="dot" data-slide="1"></div>
            <div class="dot" data-slide="2"></div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories mb-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title fw-bold">Shop by Category</h2>
            <a href="<?= base_url('categories.php') ?>" class="btn btn-outline-primary">View All</a>
        </div>
        
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <a href="<?= base_url('beverages.php') ?>" class="category-card">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="category-icon">
                                <i class="fas fa-coffee fa-3x"></i>
                            </div>
                            <h5 class="category-title mb-0">Beverages</h5>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 col-6">
                <a href="<?= base_url('snacks-sweets') ?>" class="category-card">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="category-icon">
                                <i class="fas fa-cookie fa-3x"></i>
                            </div>
                            <h5 class="category-title mb-0">Snacks & Sweets</h5>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 col-6">
                <a href="<?= base_url('cleaning_supplies.php') ?>" class="category-card">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="category-icon">
                                <i class="fas fa-broom fa-3x"></i>
                            </div>
                            <h5 class="category-title mb-0">Cleaning</h5>
                        </div>
                    </div>
                </a>
            </div>
            
            <div class="col-md-3 col-6">
                <a href="<?= base_url('meat-and-dairy.php') ?>" class="category-card">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <div class="category-icon">
                                <i class="fas fa-cheese fa-3x"></i>
                            </div>
                            <h5 class="category-title mb-0">Dairy & Meat</h5>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- New Products Section -->
<section class="new-products mb-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title fw-bold">New Products</h2>
            <a href="<?= base_url('new-products.php') ?>" class="btn btn-outline-primary">View All</a>
        </div>
        
        <div class="row g-4">
            <?php 
            // Get only the first 4 products
            $displayProducts = array_slice($newProducts, 0, 4);
            foreach ($displayProducts as $product): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us mb-5 py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title fw-bold">Why Choose ChantMO?</h2>
            <p class="lead text-muted">We're committed to providing the best shopping experience</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-truck fa-3x text-primary"></i>
                        </div>
                        <h4 class="feature-title mb-3">Fast Delivery</h4>
                        <p class="text-muted">Get your orders delivered to your doorstep within 24 hours in Accra and 48 hours nationwide.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-money-bill-wave fa-3x text-primary"></i>
                        </div>
                        <h4 class="feature-title mb-3">Flexible Payment</h4>
                        <p class="text-muted">Pay with cash on delivery, mobile money, or card. We accept all major payment methods.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center p-4">
                        <div class="feature-icon mb-3">
                            <i class="fas fa-headset fa-3x text-primary"></i>
                        </div>
                        <h4 class="feature-title mb-3">24/7 Support</h4>
                        <p class="text-muted">Our customer service team is available round the clock to assist with your queries.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="featured-products mb-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title fw-bold">Featured Products</h2>
            <a href="<?= base_url('featured-products.php') ?>" class="btn btn-outline-primary">View All</a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Special Offers Section -->
<section class="special-offers mb-5">
    <div class="container">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h2 class="section-title fw-bold">Special Offers</h2>
            <a href="<?= base_url('deals.php') ?>" class="btn btn-outline-primary">View All</a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($discountedProducts as $product): ?>
            <div class="col-xl-3 col-lg-4 col-md-6 col-6">
                <?php include __DIR__ . '/includes/product-card.php'; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Newsletter Section -->
<section class="newsletter py-5">
    <div class="container">
        <div class="newsletter-container bg-primary text-white rounded-4 p-4 p-lg-5">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h3 class="mb-3 fw-bold">Join Our Newsletter</h3>
                    <p class="mb-0 fs-5 opacity-75">Get exclusive 20% off your first order plus updates on new arrivals and special offers.</p>
                </div>
                <div class="col-lg-6">
                    <form class="newsletter-form" id="newsletterSubscription" action="<?= base_url('includes/newsletter-subscribe.php') ?>" method="POST">
                        <div class="input-group shadow-lg">
                            <input 
                                type="email" 
                                name="email"
                                class="form-control form-control-lg border-0" 
                                placeholder="Enter your email" 
                                required
                                aria-label="Email for newsletter subscription"
                            >
                            <button 
                                class="btn btn-gradient border-0 px-4" 
                                type="submit"
                                aria-label="Subscribe to newsletter"
                            >
                                <span class="d-none d-sm-inline">Subscribe</span>
                                <i class="fas fa-paper-plane d-sm-none"></i>
                            </button>
                        </div>
                        <div class="form-text mt-2 text-white-50">
                            We respect your privacy. Unsubscribe at any time.
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // Simple Slider Functionality
document.addEventListener('DOMContentLoaded', function() {
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    let currentSlide = 0;
    
    // Auto-rotate slides every 5 seconds
    const slideInterval = setInterval(nextSlide, 5000);
    
    // Dot click functionality
    dots.forEach(dot => {
        dot.addEventListener('click', function() {
            const slideIndex = parseInt(this.getAttribute('data-slide'));
            goToSlide(slideIndex);
            resetInterval();
        });
    });
    
    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        updateSlider();
    }
    
    function goToSlide(index) {
        currentSlide = index;
        updateSlider();
    }
    
    function updateSlider() {
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));
        
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }
    
    function resetInterval() {
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 5000);
    }
});

// Enhanced Newsletter Form Handling
document.addEventListener('DOMContentLoaded', function() {
    const newsletterForm = document.getElementById('newsletterSubscription');
    
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Processing...';
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Reset button state
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                
                // Show appropriate toast notification
                if (data.status && data.message) {
                    showNewsletterToast(data.status, data.message);
                } else {
                    showNewsletterToast('error', 'Unexpected response from server');
                }
                
                // Reset form if success
                if (data.status === 'success') {
                    form.reset();
                }
            })
            .catch(error => {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
                showNewsletterToast('error', 'Failed to submit. Please check your connection and try again.');
                console.error('Newsletter submission error:', error);
            });
        });
    }
    
    // Check for verification success in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('newsletter_verified')) {
        showNewsletterToast('success', 'Subscription confirmed! Check your email for your discount code.');
        // Clean URL without reload
        history.replaceState(null, '', window.location.pathname);
    }
});

// Toast Notification Function with Improved Styling
function showNewsletterToast(type, message) {
    // Remove any existing toasts first
    document.querySelectorAll('.newsletter-toast').forEach(toast => toast.remove());
    
    const toastContainer = document.createElement('div');
    toastContainer.className = 'position-fixed bottom-0 end-0 p-3 newsletter-toast';
    toastContainer.style.zIndex = '1090';
    
    // Determine icon and colors
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        info: 'fa-info-circle'
    };
    const icon = icons[type] || 'fa-bell';
    const bgClass = `bg-${type}`;
    
    toastContainer.innerHTML = `
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgClass} text-white">
                <strong class="me-auto">
                    <i class="fas ${icon} me-2"></i>
                    ${type.charAt(0).toUpperCase() + type.slice(1)}
                </strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body bg-white text-dark">
                ${message}
            </div>
        </div>
    `;
    
    document.body.appendChild(toastContainer);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        toastContainer.remove();
    }, 5000);
    
    // Add click handler for close button
    const closeBtn = toastContainer.querySelector('.btn-close');
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            toastContainer.remove();
        });
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>