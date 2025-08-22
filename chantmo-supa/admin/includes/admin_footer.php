</main>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted">&copy; <?= date('Y') ?> ChantMO Supermarket. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="text-muted">Admin Panel v1.0</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Admin Custom JS -->
    <script src="<?= asset_url('js/admin.js') ?>"></script>
    
    <!-- Page-specific JS -->
    <?php if (isset($customJS)): ?>
        <script src="<?= asset_url('js/' . $customJS) ?>"></script>
    <?php endif; ?>

    <script>
    // Initialize Bootstrap components
    document.addEventListener('DOMContentLoaded', function() {
        // Password visibility toggle
        document.querySelectorAll('.toggle-password').forEach(function(button) {
            button.addEventListener('click', function() {
                const input = document.querySelector(this.getAttribute('toggle'));
                const icon = this.querySelector('i');
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.replace('bi-eye', 'bi-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.replace('bi-eye-slash', 'bi-eye');
                }
            });
        });

        // Dropdown menus (as fallback)
        if (typeof bootstrap === 'undefined') {
            document.querySelectorAll('.dropdown-toggle').forEach(function(dropdown) {
                dropdown.addEventListener('click', function(e) {
                    e.preventDefault();
                    const menu = this.nextElementSibling;
                    menu.classList.toggle('show');
                });
            });

            // Close when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                        menu.classList.remove('show');
                    });
                }
            });
        }
    });
    </script>
</body>
</html>