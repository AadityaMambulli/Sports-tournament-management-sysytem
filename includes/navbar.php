<?php
$role = $_SESSION['role'] ?? null;
$currentUser = currentUser();
$navItems = [
    ['label' => 'Home', 'url' => baseUrl() . '/index.php'],
    ['label' => 'Tournaments', 'url' => baseUrl() . '/index.php#tournaments'],
    ['label' => 'Sports', 'url' => baseUrl() . '/index.php#sports'],
];

if ($role === 'admin') {
    $navItems = [
        ['label' => 'Dashboard', 'url' => baseUrl() . '/admin/dashboard.php'],
        ['label' => 'Tournaments', 'url' => baseUrl() . '/admin/tournaments.php'],
        ['label' => 'Teams', 'url' => baseUrl() . '/admin/teams.php'],
        ['label' => 'Matches', 'url' => baseUrl() . '/admin/matches.php'],
        ['label' => 'Results', 'url' => baseUrl() . '/admin/results.php'],
        ['label' => 'Announcements', 'url' => baseUrl() . '/admin/announcements.php'],
    ];
} elseif ($role === 'user') {
    $navItems = [
        ['label' => 'Dashboard', 'url' => baseUrl() . '/dashboard.php'],
        ['label' => 'Tournaments', 'url' => baseUrl() . '/user/tournaments.php'],
        ['label' => 'Team', 'url' => baseUrl() . '/user/team.php'],
        ['label' => 'Matches', 'url' => baseUrl() . '/user/matches.php'],
        ['label' => 'Notifications', 'url' => baseUrl() . '/user/notifications.php'],
        ['label' => 'Profile', 'url' => baseUrl() . '/user/profile.php'],
    ];
}
?>
<header class="topbar">
    <div class="container nav-wrap">
        <a href="<?= e(baseUrl()) ?>" class="brand" aria-label="Sports Tournament home">
            <span class="brand-mark">ST</span>
            <span>Sports Tournament</span>
        </a>

        <button class="nav-toggle" type="button" aria-label="Open menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" aria-label="Main navigation">
            <?php foreach ($navItems as $item): ?>
                <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
            <?php endforeach; ?>

            <?php if ($role === 'admin'): ?>
                <a href="<?= e(baseUrl()) ?>/admin/settings.php">Settings</a>
                <a href="<?= e(baseUrl()) ?>/logout.php">Logout</a>
            <?php elseif ($role === 'user'): ?>
                <span class="user-badge"><?= e($currentUser['name'] ?? 'User') ?></span>
                <a href="<?= e(baseUrl()) ?>/logout.php">Logout</a>
            <?php else: ?>
                <a href="<?= e(baseUrl()) ?>/login.php">Login</a>
                <a href="<?= e(baseUrl()) ?>/register.php" class="button button-primary small">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
