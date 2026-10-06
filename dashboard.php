<?php
require_once __DIR__ . '/includes/auth.php';

if (!userIsLoggedIn()) {
    setFlash('error', 'Please log in to continue.');
    redirect(baseUrl() . '/login.php');
}

if ($_SESSION['role'] === 'admin') {
    redirect(baseUrl() . '/admin/dashboard.php');
}

redirect(baseUrl() . '/user/dashboard.php');
