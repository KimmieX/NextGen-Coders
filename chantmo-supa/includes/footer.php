</main>

<footer class="footer">
    <div class="container py-5">
        <div class="footer-top pb-4">
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand d-flex align-items-center mb-4">
                        <span class="logo-icon d-flex align-items-center justify-content-center me-2">CM</span>
                        <span class="brand-name h4 mb-0">ChantMO</span>
                    </div>
                    <p class="footer-text">Your trusted supermarket for quality products at affordable prices. We bring the market to your doorstep.</p>
                    <div class="social-links mt-4 d-flex gap-3">
                        <a href="https://facebook.com/yourpage" target="_blank" rel="noopener noreferrer" class="social-link">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/yourhandle" target="_blank" rel="noopener noreferrer" class="social-link">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://instagram.com/yourprofile" target="_blank" rel="noopener noreferrer" class="social-link">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="https://wa.me/yourphonenumber" target="_blank" rel="noopener noreferrer" class="social-link">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-title mb-4">Quick Links</h5>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="mb-2"><a href="<?= base_url('about.php') ?>">About Us</a></li>
                        <li class="mb-2"><a href="<?= base_url('new-products.php') ?>">New Products</a></li>
                        <li class="mb-2"><a href="<?= base_url('deals.php') ?>">Special Offers</a></li>
                        <li class="mb-2"><a href="<?= base_url('contact.php') ?>">Contact Us</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5 class="footer-title mb-4">Categories</h5>
                    <ul class="footer-links list-unstyled">
                        <li class="mb-2"><a href="<?= base_url('beverages.php') ?>">Beverages</a></li>
                        <li class="mb-2"><a href="<?= base_url('snacks-sweets.php') ?>">Snacks & Sweets</a></li>
                        <li class="mb-2"><a href="<?= base_url('cleaning_supplies.php') ?>">Cleaning</a></li>
                        <li class="mb-2"><a href="<?= base_url('meat-and-dairy.php') ?>">Dairy & Meat</a></li>
                        <li class="mb-2"><a href="<?= base_url('personal-care.php') ?>">Personal Care</a></li>
                    </ul>
                </div>
                
                <!-- Newsletter Section in footer.php -->
<div class="col-lg-4 col-md-6">
    <h5 class="footer-title mb-4">Newsletter</h5>
    <p class="footer-text">Subscribe to get updates on new products and special offers.</p>
    <form class="newsletter-form" id="footerNewsletterForm" action="<?= base_url('includes/newsletter-subscribe.php') ?>" method="POST">
        <div class="input-group">
            <input 
                type="email" 
                name="email"
                class="form-control" 
                placeholder="Your email" 
                required
                aria-label="Email for newsletter subscription"
            >
            <button 
                class="btn btn-primary" 
                type="submit"
                aria-label="Subscribe to newsletter"
            >
                Subscribe
            </button>
        </div>
        <div class="form-text mt-2 text-white-50">
            We respect your privacy. Unsubscribe at any time.
        </div>
    </form>
                    
                    <h5 class="footer-title mt-4 mb-3">Payment Methods</h5>
                    <div class="payment-methods d-flex flex-wrap gap-3">
                        <div class="payment-method">
                            <i class="fas fa-money-bill-wave me-2"></i>
                            <span>Cash</span>
                        </div>
                        <div class="payment-method">
                            <i class="fas fa-mobile-alt me-2"></i>
                            <span>Mobile Money</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom pt-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="copyright mb-md-0">&copy; <?= date('Y') ?> ChantMO Supermarket. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <div class="footer-links d-flex gap-3 justify-content-md-end">
                        <a href="<?= base_url('privacy.php') ?>">Privacy Policy</a>
                        <a href="<?= base_url('terms.php') ?>">Terms of Service</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<a href="#" class="back-to-top" id="backToTop">
    <i class="fas fa-arrow-up"></i>
</a>

<script>
// Enhanced Newsletter Form Handling for footer form
document.addEventListener('DOMContentLoaded', function() {
    const footerNewsletterForm = document.getElementById('footerNewsletterForm');
    
    if (footerNewsletterForm) {
        footerNewsletterForm.addEventListener('submit', function(e) {
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
    
    // Toast Notification Function (same as in index.php)
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
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= asset_url('js/main.js') ?>"></script>
</body>
</html>