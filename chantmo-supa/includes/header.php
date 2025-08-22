<?php 
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/Cart.php';
require_once __DIR__ . '/Product.php';

// Set the PDO connection for the Product class
Product::setPDO($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>ChantMO Supermarket</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <script>window.BASE_PATH = '<?= BASE_PATH ?>';</script>
    <script src="<?= base_url('assets/js/main.js') ?>" defer></script>
    <style>
        /* Base Styles & Variables */
:root {
  --primary-gradient: linear-gradient(135deg, #6e8efb, #a777e3);
  --secondary-gradient: linear-gradient(135deg, #f093fb, #f5576c);
  --dark-gradient: linear-gradient(135deg, #434343, #000000);
  --light-gradient: linear-gradient(135deg, #f5f7fa, #c3cfe2);
  --success-gradient: linear-gradient(135deg, #43e97b, #38f9d7);
  --warning-gradient: linear-gradient(135deg, #f6d365, #fda085);
  --danger-gradient: linear-gradient(135deg, #ff758c, #ff7eb3);
  --text-color: #2d3748;
  --text-light: #4a5568;
  --text-lighter: #718096;
  --bg-color: #f8f9fa;
  --border-radius: 0.5rem;
  --box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
  --box-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  --transition: all 0.3s ease;
}

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: 'Poppins', sans-serif;
  color: var(--text-color);
  background-color: var(--bg-color);
  line-height: 1.6;
}

a {
  text-decoration: none;
  color: inherit;
  transition: var(--transition);
}

img {
  max-width: 100%;
  height: auto;
  display: block;
}

/* Header Styles */
.header {
  position: sticky;
  top: 0;
  z-index: 1000;
  background: white;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.navbar-brand {
  font-weight: 700;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
}

.logo-icon {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: var(--primary-gradient);
  color: white;
  margin-right: 0.5rem;
  font-weight: bold;
}

.nav-link {
  font-weight: 500;
  padding: 0.5rem 1rem;
  position: relative;
  color: var(--text-light);
}

.nav-link:hover {
  color: var(--text-color);
}

/* New hover effect - subtle background highlight */
.nav-item:not(.dropdown) .nav-link:hover {
  background: rgba(110, 142, 251, 0.1);
  border-radius: 4px;
}

/* Login button hover fix */
.btn-outline-primary:hover {
  color: white;
  background: var(--primary-gradient);
}

.dropdown-menu {
  border: none;
  box-shadow: var(--box-shadow-lg);
  border-radius: var(--border-radius);
  padding: 0.5rem 0;
}

.dropdown-item {
  padding: 0.5rem 1.5rem;
  transition: var(--transition);
}

.dropdown-item:hover {
  background: linear-gradient(135deg, #f5f7fa, #e6ebf5);
}

.nav-icon {
  transition: var(--transition);
  color: var(--text-light);
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 50%;
}

.nav-icon:hover {
  background: rgba(110, 142, 251, 0.1);
  color: var(--text-color);
}

.btn-cart {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #f8f9fa;
  color: var(--text-light);
}

.btn-cart:hover {
  background: rgba(110, 142, 251, 0.1);
  color: var(--text-color);
}

.cart-badge, .wishlist-count {
  font-size: 0.7rem;
  min-width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Search Collapse */
#searchCollapse {
  background: white;
  box-shadow: inset 0 2px 5px rgba(0, 0, 0, 0.05);
}

.search-form .form-control {
  border: 2px solid transparent;
  background: linear-gradient(white, white) padding-box,
              var(--light-gradient) border-box;
}

.search-form .btn {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

/* New Hero Slider Styles */
.supermarket-hero {
    position: relative;
    overflow: hidden;
    margin-top: 0;
    height: 80vh;
    min-height: 500px;
    max-height: 800px;
}

.supermarket-slider {
    height: 100%;
    width: 100%;
    position: relative;
}

.slide {
    position: absolute;
    width: 100%;
    height: 100%;
    opacity: 0;
    transition: opacity 1s ease;
    background-size: cover;
    background-position: center;
}

.slide.active {
    opacity: 1;
}

.slide-content {
    position: absolute;
    top: 50%;
    left: 10%;
    transform: translateY(-50%);
    max-width: 500px;
    background: rgba(255, 255, 255, 0.9);
    padding: 2.5rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.slide-content h2 {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #2a4365;
}

.slide-content p {
    font-size: 1.2rem;
    margin-bottom: 2rem;
    color: #4a5568;
}

.slide-btn {
    display: inline-block;
    padding: 12px 30px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: white;
    border-radius: 50px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(110, 142, 251, 0.4);
}

.slide-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(110, 142, 251, 0.6);
}

.slider-dots {
    position: absolute;
    bottom: 30px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 10px;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.5);
    cursor: pointer;
    transition: all 0.3s ease;
}

.dot.active {
    background: white;
    transform: scale(1.2);
}

/* Responsive Styles */
@media (max-width: 992px) {
    .slide-content {
        left: 5%;
        max-width: 400px;
        padding: 2rem;
    }
    
    .slide-content h2 {
        font-size: 2.2rem;
    }
}

@media (max-width: 768px) {
    .supermarket-hero {
        height: 70vh;
        min-height: 400px;
    }
    
    .slide-content {
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: none;
        text-align: center;
        padding: 1.5rem;
    }
    
    .slide-content h2 {
        font-size: 1.8rem;
    }
    
    .slide-content p {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .supermarket-hero {
        height: 60vh;
        min-height: 350px;
    }
    
    .slide-content {
        padding: 1.2rem;
    }
    
    .slide-content h2 {
        font-size: 1.5rem;
    }
}

/* Enhanced Categories Section */
.categories {
    padding: 3rem 0;
}

.category-card {
    transition: all 0.3s ease;
    display: block;
    height: 100%;
}

.category-card:hover {
    transform: translateY(-5px);
    text-decoration: none;
}

.category-card .card {
    border: none;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
    position: relative;
}

.category-card:hover .card {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.category-card .card-body {
    padding: 2rem 1rem;
    position: relative;
    z-index: 1;
}

.category-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(110, 142, 251, 0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
}

.category-card:hover .category-icon {
    background: linear-gradient(135deg, rgba(110, 142, 251, 0.2), rgba(167, 119, 227, 0.2));
    transform: scale(1.1);
}

.category-icon i {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.category-title {
    font-weight: 600;
    color: #2d3748;
    transition: all 0.3s ease;
}

.category-card:hover .category-title {
    color: #6e8efb;
}

/* Gradient overlay effect */
.category-card .card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.category-card:hover .card::before {
    opacity: 1;
}

/* Section header styling */
.section-header {
    position: relative;
    padding-bottom: 1rem;
}

.section-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 4px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    border-radius: 2px;
}

.section-title {
    position: relative;
    display: inline-block;
}

.btn-outline-primary {
    border: 2px solid transparent;
    background: linear-gradient(white, white) padding-box,
                linear-gradient(135deg, #6e8efb, #a777e3) border-box;
    color: #2d3748;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: linear-gradient(white, white) padding-box,
                linear-gradient(135deg, #5d7df4, #9a6bdb) border-box;
    color: #2d3748;
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .categories {
        padding: 2rem 0;
    }
    
    .category-card .card-body {
        padding: 1.5rem 0.5rem;
    }
    
    .category-icon {
        width: 60px;
        height: 60px;
        margin-bottom: 1rem;
    }
    
    .category-icon i {
        font-size: 2rem !important;
    }
}

/* New Products Section Styles */
.new-products {
    padding: 3rem 0;
}

.new-products .section-header {
    margin-bottom: 2rem;
}

/* Product Card Styles */
.product-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
    box-shadow: var(--box-shadow);
    position: relative;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--box-shadow-lg);
}

.product-badges {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 2;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.product-badge, .discount-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-weight: 600;
}

.product-badge {
    background: var(--primary-gradient);
    color: white;
}

.discount-badge {
    background: var(--danger-gradient);
    color: white;
}

.product-image-container {
    position: relative;
    overflow: hidden;
    padding-top: 100%; /* 1:1 Aspect Ratio */
    background: #f8f9fa;
}

.product-image-container img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: var(--transition);
}

.product-image-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #adb5bd;
    font-size: 2rem;
}

.product-actions {
    position: absolute;
    bottom: -50px;
    left: 0;
    right: 0;
    display: flex;
    justify-content: center;
    gap: 10px;
    padding: 10px;
    transition: var(--transition);
    z-index: 2;
}

.product-card:hover .product-actions {
    bottom: 10px;
}

.quick-view-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: white;
    color: var(--text-color);
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
}

.quick-view-btn:hover {
    background: var(--primary-gradient);
    color: white;
}

.product-details {
    padding: 1.25rem;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.product-name {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.75rem;
    color: var(--text-color);
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    min-height: 3em;
}

.product-price {
    margin-bottom: 1rem;
}

.current-price {
    font-weight: 700;
    font-size: 1.1rem;
    color: var(--text-color);
}

.original-price {
    font-size: 0.9rem;
    color: var(--text-lighter);
    text-decoration: line-through;
    margin-left: 0.5rem;
}

.btn-add-to-cart {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.5rem;
    border-radius: var(--border-radius);
    font-weight: 500;
    transition: var(--transition);
}

.btn-add-to-cart:hover:not(:disabled) {
    background: var(--secondary-gradient);
    transform: translateY(-2px);
}

.btn-add-to-cart:disabled {
    background: #e9ecef;
    color: #6c757d;
    cursor: not-allowed;
}

.btn-wishlist {
    background: white;
    color: var(--text-light);
    border: 1px solid #dee2e6;
    padding: 0.5rem;
    border-radius: var(--border-radius);
    font-weight: 500;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.btn-wishlist.active, .btn-wishlist:hover {
    background: #fff5f5;
    color: #ff6b6b;
    border-color: #ff6b6b;
}

/* Quick View Modal Styles */
#quickViewModal .modal-content {
    border: none;
    border-radius: var(--border-radius);
    overflow: hidden;
}

#quickViewModal .modal-header {
    border-bottom: none;
    padding-bottom: 0;
}

#quickViewModal .modal-body {
    padding-top: 0;
}

#qvProductImage {
    width: 100%;
    height: 350px;
    object-fit: cover;
    border-radius: var(--border-radius);
    margin-bottom: 1.5rem;
}

#qvProductName {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: var(--text-color);
}

.price-container {
    margin-bottom: 1.5rem;
}

#qvProductPrice {
    font-weight: 700;
    color: var(--text-color);
}

#qvOriginalPrice {
    font-size: 1.1rem;
    margin-left: 0.5rem;
}

#qvDiscountBadge {
    font-size: 0.9rem;
    padding: 0.25rem 0.5rem;
}

.product-description {
    color: var(--text-light);
    margin-bottom: 1.5rem;
    line-height: 1.7;
}

.product-meta {
    margin-top: 1.5rem;
    font-size: 0.9rem;
}

.product-meta p {
    margin-bottom: 0.5rem;
    color: var(--text-light);
}

.product-meta strong {
    color: var(--text-color);
    margin-right: 0.5rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #qvProductImage {
        height: 250px;
    }
    
    #qvProductName {
        font-size: 1.5rem;
    }
    
    .product-card {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }
}

@media (max-width: 576px) {
    #qvProductImage {
        height: 200px;
    }
    
    #qvProductName {
        font-size: 1.3rem;
    }
    
    .product-actions {
        bottom: 10px;
        opacity: 1;
    }
}

/* Featured Products Section - matches New Products */
.featured-products {
    padding: 3rem 0;
}

.featured-products .section-header {
    margin-bottom: 2rem;
}

/* Responsive grid adjustments */
@media (max-width: 992px) {
    .featured-products .col-xl-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 576px) {
    .featured-products .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Special Offers Section - matches Featured/New Products */
.special-offers {
    padding: 3rem 0;
}

.special-offers .section-header {
    margin-bottom: 2rem;
}

/* Highlight discount badges more prominently */
.special-offers .discount-badge {
    background: var(--danger-gradient);
    font-size: 0.8rem;
    padding: 0.3rem 0.6rem;
}

/* Responsive grid adjustments */
@media (max-width: 992px) {
    .special-offers .col-xl-3 {
        flex: 0 0 50%;
        max-width: 50%;
    }
}

@media (max-width: 576px) {
    .special-offers .col-6 {
        flex: 0 0 100%;
        max-width: 100%;
    }
}

/* Newsletter Section */
.newsletter {
    position: relative;
    overflow: hidden;
}

.newsletter-container {
    background: var(--primary-gradient);
    position: relative;
    overflow: hidden;
}

.newsletter-container::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.newsletter-container::after {
    content: '';
    position: absolute;
    bottom: -80px;
    left: -80px;
    width: 250px;
    height: 250px;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
}

.newsletter-form .form-control {
    height: 56px;
    border-radius: 0.5rem 0 0 0.5rem !important;
    border: none;
    font-size: 1rem;
}

.newsletter-form .form-control:focus {
    box-shadow: none;
    border-color: transparent;
}

.newsletter-form .btn-gradient {
    background: var(--dark-gradient);
    color: white;
    font-weight: 600;
    border-radius: 0 0.5rem 0.5rem 0 !important;
    transition: var(--transition);
}

.newsletter-form .btn-gradient:hover {
    background: var(--secondary-gradient);
    transform: translateY(-2px);
}

/* Success message styles */
.newsletter-success {
    display: none;
    padding: 1rem;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 0.5rem;
    margin-top: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .newsletter-container {
        text-align: center;
    }
    
    .newsletter-form .input-group {
        flex-direction: column;
    }
    
    .newsletter-form .form-control {
        border-radius: 0.5rem !important;
        margin-bottom: 0.5rem;
    }
    
    .newsletter-form .btn-gradient {
        border-radius: 0.5rem !important;
        width: 100%;
    }
}

/* Footer Styles */
.footer {
    background: linear-gradient(135deg, #434343, #000000);
    color: white;
    padding: 3rem 0 0;
}

.footer a {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
}

.footer a:hover {
    color: white;
}

.footer-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
}

.footer-title::after {
    content: '';
    position: absolute;
    bottom: -0.5rem;
    left: 0;
    width: 40px;
    height: 3px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
}

.footer-brand {
    margin-bottom: 1.5rem;
}

.logo-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
    margin-right: 1rem;
}

.brand-name {
    font-weight: 700;
}

.footer-text {
    color: rgba(255, 255, 255, 0.7);
    margin-bottom: 1.5rem;
}

.social-links {
    display: flex;
    gap: 1rem;
}

.social-link {
    width: 40px;
    height: 40px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.social-link:hover {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    transform: translateY(-3px);
}

.footer-links li {
    margin-bottom: 0.75rem;
}

.footer-bottom {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 2rem;
    margin-top: 2rem;
}

.copyright {
    color: rgba(255, 255, 255, 0.5);
}

.payment-method {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50px;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
}

/* Back to Top Button */
.back-to-top {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    background: #333;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
    z-index: 999;
}

.back-to-top.show {
    opacity: 1;
    visibility: visible;
}

.back-to-top:hover {
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    transform: translateY(-3px);
}

/* Responsive */
@media (max-width: 768px) {
    .footer {
        text-align: center;
    }
    
    .footer-title::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .footer-brand {
        justify-content: center;
    }
    
    .social-links {
        justify-content: center;
    }
    
    .footer-links {
        justify-content: center !important;
    }
}

/* Toast Notifications */
.toast {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.toast-header {
    padding: 0.75rem 1rem;
    border-bottom: none;
}

.toast-body {
    padding: 1rem;
    color: var(--text-color);
}

.bg-success {
    background: var(--success-gradient) !important;
}

.bg-error {
    background: var(--danger-gradient) !important;
}

.bg-info {
    background: var(--primary-gradient) !important;
}

/* Animation */
.toast.show {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* AUTH PAGES GRADIENT STYLES */
.auth-wrapper {
    min-height: 100vh;
    display: flex;
    align-items: center;
    background: linear-gradient(135deg, rgba(110, 142, 251, 0.1) 0%, rgba(167, 119, 227, 0.1) 100%);
}

.auth-card {
    border: none !important;
    border-radius: 16px !important;
    overflow: hidden;
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
    background: white;
}

.auth-header {
    background: var(--primary-gradient);
    color: white;
    padding: 2rem;
    text-align: center;
}

.auth-title {
    font-weight: 700;
    margin: 0;
    font-size: 1.8rem;
    position: relative;
    display: inline-block;
}

.auth-title:after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 3px;
    background: white;
    border-radius: 3px;
}

.auth-body {
    padding: 2.5rem;
}

.form-control {
    border: 2px solid #e9ecef !important;
    border-radius: 8px !important;
    padding: 12px 15px !important;
    transition: all 0.3s ease !important;
}

.form-control:focus {
    border-color: #a777e3 !important;
    box-shadow: 0 0 0 0.25rem rgba(167, 119, 227, 0.25) !important;
}

.btn-auth {
    background: var(--primary-gradient);
    border: none !important;
    padding: 12px !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px;
    border-radius: 8px !important;
    transition: all 0.3s ease !important;
    color: white !important;
    text-transform: uppercase;
}

.btn-auth:hover {
    background: var(--secondary-gradient);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
}

.auth-footer {
    text-align: center;
    margin-top: 1.5rem;
    color: var(--text-light);
}

.auth-link {
    color: #6e8efb !important;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.auth-link:hover {
    color: #a777e3 !important;
    text-decoration: underline;
}

/* Password toggle */
.password-toggle-container {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: var(--text-lighter);
}

/* Strength meter */
.strength-meter {
    height: 5px;
    background: #e9ecef;
    border-radius: 5px;
    margin-top: 8px;
    overflow: hidden;
}

.strength-meter-fill {
    height: 100%;
    width: 0;
    transition: width 0.3s ease;
}

.strength-weak {
    background: var(--danger-gradient);
    width: 30% !important;
}

.strength-medium {
    background: var(--warning-gradient);
    width: 60% !important;
}

.strength-strong {
    background: var(--success-gradient);
    width: 100% !important;
}

/* Responsive */
@media (max-width: 768px) {
    .auth-body {
        padding: 1.5rem;
    }
    
    .auth-header {
        padding: 1.5rem;
    }
}

.auth-header {
    background: var(--primary-gradient);
    color: white;
    padding: 2rem;
    text-align: center;
}

.auth-title {
    font-weight: 700;
    margin: 0;
    font-size: 1.8rem;
    position: relative;
    display: inline-block;
}

.auth-subtitle {
    color: rgba(255, 255, 255, 0.9);
    margin: 0.5rem 0 0;
    font-size: 1rem;
}

/* In your header.php CSS section */
.newsletter-form .input-group {
    flex-direction: column;
}

.newsletter-form .form-control {
    border-radius: 0.5rem !important;
    margin-bottom: 0.5rem;
    width: 100%;
}

.newsletter-form .btn-gradient {
    border-radius: 0.5rem !important;
    width: 100%;
}

@media (min-width: 768px) {
    .newsletter-form .input-group {
        flex-direction: row;
    }
    
    .newsletter-form .form-control {
        border-radius: 0.5rem 0 0 0.5rem !important;
        margin-bottom: 0;
        width: auto;
        flex: 1;
    }
    
    .newsletter-form .btn-gradient {
        border-radius: 0 0.5rem 0.5rem 0 !important;
        width: auto;
    }
}

/* Auth page spacing adjustments */
.auth-wrapper {
    padding: 1rem 0;
    min-height: calc(100vh - 120px); /* Adjust based on your header/footer height */
    display: flex;
    align-items: center;
}

.auth-card {
    margin: 1rem 0;
}

@media (max-width: 768px) {
    .auth-header {
        padding: 1.5rem;
    }
    
    .auth-body {
        padding: 1.5rem;
    }
    
    .auth-title {
        font-size: 1.5rem;
    }
    
    .auth-subtitle {
        font-size: 0.9rem;
    }
}

@media (max-width: 576px) {
    .auth-header {
        padding: 1.25rem;
    }
    
    .auth-body {
        padding: 1.25rem;
    }
    
    .auth-title {
        font-size: 1.3rem;
    }
    
    .btn-auth {
        padding: 0.75rem !important;
    }
}

@media screen and (max-width: 768px) {
    input, select, textarea {
        font-size: 16px;
    }
}

/* Auth Layout System */
.auth-wrapper {
    display: flex;
    min-height: calc(100vh - (header_height + footer_height)); /* Adjust these values */
    align-items: center;
    padding: 2rem 0;
}

.auth-card {
    width: 100%;
    max-width: 500px; /* Consistent max width */
    margin: 0 auto; /* Center horizontally */
}

/* Content-based spacing */
.auth-body {
    padding: 2rem;
}

/* For pages with less content (login, forgot password) */
.auth-body--compact {
    padding: 1.5rem 2rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .auth-wrapper {
        padding: 1rem 0;
        min-height: calc(100vh - (mobile_header_height + mobile_footer_height));
    }
    
    .auth-body {
        padding: 1.5rem;
    }
    
    .auth-body--compact {
        padding: 1.25rem 1.5rem;
    }
}
    </style>
</head>
<body class="<?= isLoggedIn() ? 'logged-in' : '' ?>">
    <header class="header">
        <nav class="navbar navbar-expand-lg navbar-light bg-white">
            <div class="container">
                <a class="navbar-brand" href="<?= base_url() ?>">
                    <span class="logo-icon">CM</span>
                    <span class="brand-name">ChantMO</span>
                </a>
                
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                    <span class="navbar-toggler-icon"></span>
                </button>
                
                <div class="collapse navbar-collapse" id="navbarContent">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url() ?>">Home</a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Browse product categories">
                                Categories
                            </a>
                            <ul class="dropdown-menu">
                                <li><h6 class="dropdown-header">Shop by Category</h6></li>
                                <li><a class="dropdown-item" href="<?= base_url('beverages.php') ?>">Beverages</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('snacks-sweets.php') ?>">Snacks & Sweets</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('cleaning_supplies.php') ?>">Cleaning Supplies</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('meat-and-dairy.php') ?>">Dairy & Meat</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('personal-care.php') ?>">Personal Care</a></li>
                            </ul>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('new-products.php') ?>">New Arrivals</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('deals.php') ?>">Special Offers</a>
                        </li>
                    </ul>
                    
                    <div class="d-flex align-items-center">
                        <!-- Search Toggle Button -->
                        <button class="btn btn-link nav-icon me-2" type="button" data-bs-toggle="collapse" data-bs-target="#searchCollapse" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Search products">
                            <i class="fas fa-search"></i>
                        </button>
                        
                        <?php if (isLoggedIn()): ?>
                            <div class="dropdown me-3">
                                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-user-circle me-2"></i>
                                    <span><?= htmlspecialchars($_SESSION['username']) ?></span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="<?= base_url('pages/dashboard.php') ?>">Dashboard</a></li>
                                    <li><a class="dropdown-item" href="<?= base_url('orders.php') ?>">My Orders</a></li>
                                    <li class="dropdown-item position-relative">
                                        <a href="<?= base_url('pages/wishlist.php') ?>" class="text-decoration-none d-flex align-items-center justify-content-between">
                                            <span>Wishlist</span>
                                            <span class="badge bg-primary wishlist-count ms-2">
                                                <?= Product::getWishlistCount($_SESSION['user_id']) ?>
                                            </span>
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="<?= base_url('pages/auth/logout.php') ?>">Logout</a></li>
                                </ul>
                            </div>
                            
                            <a href="<?= base_url('pages/wishlist.php') ?>" class="btn btn-cart position-relative me-3" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Your wishlist">
                                <i class="fas fa-heart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary wishlist-count">
                                    <?= Product::getWishlistCount($_SESSION['user_id']) ?>
                                </span>
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('pages/auth/login.php') ?>" class="btn btn-outline-primary me-2">Login</a>
                            <a href="<?= base_url('pages/auth/register.php') ?>" class="btn btn-gradient">Register</a>
                        <?php endif; ?>
                        
                        <a href="<?= base_url('cart.php') ?>" class="btn btn-cart position-relative" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Your shopping cart">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary cart-badge">
                                <?= Cart::getTotalItems() ?>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        
        <!-- Search Collapse Section -->
        <div class="collapse" id="searchCollapse">
            <div class="container py-3">
                <form action="<?= base_url('search.php') ?>" method="get" class="search-form">
                    <div class="input-group">
                        <input type="text" class="form-control" name="q" placeholder="Search products..." 
                               value="<?= htmlspecialchars($_GET['q'] ?? '') ?>" autocomplete="off">
                        <button class="btn btn-primary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <?php displayMessage(); ?>
    </header>
    
    <main class="main-content">