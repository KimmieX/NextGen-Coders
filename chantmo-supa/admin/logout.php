<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

// Destroy admin session and redirect to login
redirect(base_url('pages/auth/logout.php'));
?>