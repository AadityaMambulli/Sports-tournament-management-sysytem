<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = db();
$user = currentUser();

$registeredTournaments = $pdo->query('SELECT DISTINCT tr.* FROM registrations r LEFT JOIN tournaments tr ON tr.id = r.tournament_id WHERE r.registered_by = ' . (int)$user['id'] . ' ORDER BY tr.start_date DESC')->fetchAll();
$teams = $pdo->query('SELECT * FROM teams ORDER BY created_at DESC LIMIT 10')->fetchAll();
$upcomingMatches = getUpcomingMatches(5);
$notifications = $pdo->prepare('SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT 5');
$notifications->execute([':user_id' => $user['id']]);
$notifications = $notifications->fetchAll();
$announcements = getRecentAnnouncements(3);

$pageTitle = 'User Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Welcome</span>
        <h1>Hello, <?= e($user['name']) ?>!</h1>
    </div>
</div>

<div class="stats-grid admin-stats">
    <div class="stat-box">
        <span class="stat-number"><?= e(count($registeredTournaments)) ?></span>
        <span class="stat-label">Registered Tournaments</span>
    </div>
    <div class="stat-box">
        <span class="stat-number"><?= e(count($teams)) ?></span>
        <span class="stat-label">Available Teams</span>
    </div>
    <div class="stat-box">
        <span class="stat-number"><?= e(count($upcomingMatches)) ?></span>
        <span class="stat-label">Upcoming Matches</span>
    </div>
</div>

<div class="admin-grid">
    <section class="panel">
        <div class="panel-header"><h2>Registered tournaments</h2></div>
        <div class="stack-list">
            <?php foreach ($registeredTournaments as $tournament): ?>
                <div class="stack-item">
                    <strong><?= e($tournament['name']) ?></strong>
                    <span><?= e($tournament['venue']) ?></span>
                    <small><?= e(formatDate($tournament['start_date'])) ?> - <?= e(formatDate($tournament['end_date'])) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Team information</h2></div>
        <div class="stack-list">
            <?php foreach ($teams as $team): ?>
                <div class="stack-item">
                    <strong><?= e($team['name']) ?></strong>
                    <span>Captain: <?= e($team['captain_name']) ?></span>
                    <small><?= e(getTournamentName((int)$team['tournament_id'])) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Upcoming matches</h2></div>
        <div class="stack-list">
            <?php foreach ($upcomingMatches as $match): ?>
                <div class="stack-item">
                    <strong><?= e($match['team1_name']) ?> vs <?= e($match['team2_name']) ?></strong>
                    <span><?= e(formatDate($match['match_date'])) ?> · <?= e($match['round']) ?></span>
                    <small><?= e($match['venue_name']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Announcements</h2></div>
        <div class="stack-list">
            <?php foreach ($announcements as $announcement): ?>
                <div class="stack-item">
                    <strong><?= e($announcement['title']) ?></strong>
                    <span><?= e($announcement['content']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
