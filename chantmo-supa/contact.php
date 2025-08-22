<?php
$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/functions.php'; // Include functions.php

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verify CSRF token
    if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
        $_SESSION['contact_message'] = [
            'status' => 'error',
            'message' => 'Invalid form submission. Please try again.'
        ];
    } else {
        // Sanitize input using FILTER_SANITIZE_FULL_SPECIAL_CHARS instead of deprecated FILTER_SANITIZE_STRING
        $name = filter_var($_POST['name'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $subject = filter_var($_POST['subject'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $message = filter_var($_POST['message'], FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        // Validate input
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $_SESSION['contact_message'] = [
                'status' => 'error',
                'message' => 'All fields are required.'
            ];
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['contact_message'] = [
                'status' => 'error',
                'message' => 'Please enter a valid email address.'
            ];
        } else {
            // Prepare email content
            $emailSubject = "Contact Form: $subject";
            $emailBody = "
                <h2>New Contact Form Submission</h2>
                <p><strong>Name:</strong> $name</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Subject:</strong> $subject</p>
                <p><strong>Message:</strong></p>
                <p>$message</p>
            ";

            // Send email using our function
            if (sendEmail('info@chantmo.com', $emailSubject, $emailBody)) {
                $_SESSION['contact_message'] = [
                    'status' => 'success',
                    'message' => 'Thank you for your message! We\'ll get back to you soon.'
                ];
                // Redirect to prevent form resubmission
                redirect($_SERVER['REQUEST_URI']);
            } else {
                $_SESSION['contact_message'] = [
                    'status' => 'error',
                    'message' => 'There was an error sending your message. Please try again later.'
                ];
            }
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<!-- Your existing styles remain the same -->
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
    }
    .contact-card {
        background: linear-gradient(135deg, rgba(255,255,255,0.9) 0%, rgba(245,247,250,0.9) 100%);
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .contact-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }
    .btn-submit {
        background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
        border: none;
        transition: all 0.3s ease;
    }
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(110, 142, 251, 0.4);
    }
    .alert {
        border-radius: 8px;
    }
</style>

<main class="container py-5">
    <!-- Display contact message if exists -->
    <?php if (isset($_SESSION['contact_message'])): ?>
        <div class="alert alert-<?= $_SESSION['contact_message']['status'] === 'success' ? 'success' : 'danger' ?> mb-4">
            <?= htmlspecialchars($_SESSION['contact_message']['message']) ?>
        </div>
        <?php unset($_SESSION['contact_message']); ?>
    <?php endif; ?>

    <section class="hero-section mb-5 rounded-4 overflow-hidden">
        <div class="hero-gradient text-white p-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Contact Us</h1>
                    <p class="lead">We'd love to hear from you! Reach out with any questions or feedback.</p>
                </div>
                <div class="col-lg-6">
                    <img src="https://images.unsplash.com/photo-1552664730-d307ca884978?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1470&q=80" 
                         alt="Contact Us" class="img-fluid rounded shadow-lg">
                </div>
            </div>
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="contact-card h-100 p-4">
                <h2 class="fw-bold mb-4">Get In Touch</h2>
                <form id="contactForm" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Your Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject</label>
                        <input type="text" class="form-control" id="subject" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-submit text-white px-4 py-2">Send Message</button>
                </form>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="contact-card h-100 p-4">
                <h2 class="fw-bold mb-4">Our Information</h2>
                <div class="d-flex align-items-start mb-4">
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-map-marker-alt text-primary"></i>
                    </div>
                    <div>
                        <h4 class="h5">Address</h4>
                        <p class="mb-0">123 Market Street<br>Accra, Ghana</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-phone-alt text-primary"></i>
                    </div>
                    <div>
                        <h4 class="h5">Phone</h4>
                        <p class="mb-0">+233 123 456 789</p>
                    </div>
                </div>
                <div class="d-flex align-items-start mb-4">
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-envelope text-primary"></i>
                    </div>
                    <div>
                        <h4 class="h5">Email</h4>
                        <p class="mb-0">info@chantmo.com</p>
                    </div>
                </div>
                <div class="d-flex align-items-start">
                    <div class="icon-box rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                        <i class="fas fa-clock text-primary"></i>
                    </div>
                    <div>
                        <h4 class="h5">Opening Hours</h4>
                        <p class="mb-0">Monday - Friday: 8am - 8pm<br>Saturday - Sunday: 9am - 6pm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Add contact form submission handling with AJAX if desired
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            // You can optionally add AJAX submission here
            // Similar to the newsletter form in index.php
            // But the current PHP form handling will work as well
        });
    }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>