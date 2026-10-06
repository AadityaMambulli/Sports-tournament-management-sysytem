<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();
redirect(baseUrl() . '/admin/matches.php');
