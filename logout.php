<?php
require_once __DIR__ . '/includes/auth.php';

clearRememberedEmailCookie();
$_SESSION = [];
session_destroy();

redirect(baseUrl() . '/index.php');
