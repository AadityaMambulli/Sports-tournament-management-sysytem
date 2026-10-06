<?php
require_once __DIR__ . '/auth.php';

$pageTitle = $pageTitle ?? 'Sports Tournament Management System';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?></title>
    <meta name="description" content="Sports Tournament Management System">
    <link rel="stylesheet" href="<?= e(baseUrl()) ?>/assets/css/style.css">
    <script src="<?= e(baseUrl()) ?>/assets/js/app.js" defer></script>
</head>
<body class="<?= e($bodyClass) ?>">
    <?php include __DIR__ . '/navbar.php'; ?>
    <main class="page-shell">
        <?php renderFlash(); ?>
