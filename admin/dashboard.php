<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

$counts = [
    'tournaments' => (int)$pdo->query('SELECT COUNT(*) FROM tournaments')->fetchColumn(),
    'active' => (int)$pdo->query('SELECT COUNT(*) FROM tournaments WHERE status IN ("Registration Open", "Ongoing")')->fetchColumn(),
    'teams' => (int)$pdo->query('SELECT COUNT(*) FROM teams')->fetchColumn(),
    'players' => (int)$pdo->query('SELECT COUNT(*) FROM players')->fetchColumn(),
    'upcoming_matches' => (int)$pdo->query('SELECT COUNT(*) FROM matches WHERE match_date >= CURDATE()')->fetchColumn(),
];

$upcomingMatches = $pdo->query('SELECT m.*, t1.name as team1_name, t2.name as team2_name, v.name as venue_name FROM matches m LEFT JOIN teams t1 ON t1.id = m.team1_id LEFT JOIN teams t2 ON t2.id = m.team2_id LEFT JOIN venues v ON v.id = m.venue_id WHERE m.match_date >= CURDATE() ORDER BY m.match_date ASC, m.match_time ASC LIMIT 5')->fetchAll();
$recentRegistrations = $pdo->query('SELECT r.*, t.name as team_name, tr.name as tournament_name FROM registrations r LEFT JOIN teams t ON t.id = r.team_id LEFT JOIN tournaments tr ON tr.id = r.tournament_id ORDER BY r.created_at DESC LIMIT 5')->fetchAll();
$recentAnnouncements = getRecentAnnouncements(4);
$statusOverview = $pdo->query('SELECT status, COUNT(*) as total FROM tournaments GROUP BY status ORDER BY status')->fetchAll();

$pageTitle = 'Admin Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Dashboard</span>
        <h1>Administrative overview</h1>
    </div>
</div>

<div class="stats-grid admin-stats">
    <div class="stat-box">
        <span class="stat-number"><?= e($counts['tournaments']) ?></span>
        <span class="stat-label">Total Tournaments</span>
    </div>
    <div class="stat-box">
        <span class="stat-number"><?= e($counts['active']) ?></span>
        <span class="stat-label">Active Tournaments</span>
    </div>
    <div class="stat-box">
        <span class="stat-number"><?= e($counts['teams']) ?></span>
        <span class="stat-label">Total Teams</span>
    </div>
    <div class="stat-box">
        <span class="stat-number"><?= e($counts['players']) ?></span>
        <span class="stat-label">Total Players</span>
    </div>
    <div class="stat-box">
        <span class="stat-number"><?= e($counts['upcoming_matches']) ?></span>
        <span class="stat-label">Upcoming Matches</span>
    </div>
</div>

<div class="admin-grid">
    <section class="panel">
        <div class="panel-header"><h2>Upcoming matches</h2></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Match</th>
                        <th>Date</th>
                        <th>Venue</th>
                        <th>Round</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingMatches as $match): ?>
                        <tr>
                            <td><?= e($match['team1_name']) ?> vs <?= e($match['team2_name']) ?></td>
                            <td><?= e(formatDate($match['match_date'])) ?></td>
                            <td><?= e($match['venue_name']) ?></td>
                            <td><?= e($match['round']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Recent registrations</h2></div>
        <div class="stack-list">
            <?php foreach ($recentRegistrations as $registration): ?>
                <div class="stack-item">
                    <strong><?= e($registration['team_name']) ?></strong>
                    <span><?= e($registration['tournament_name']) ?></span>
                    <small><?= e($registration['status']) ?></small>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Recent announcements</h2></div>
        <div class="stack-list">
            <?php foreach ($recentAnnouncements as $announcement): ?>
                <div class="stack-item">
                    <strong><?= e($announcement['title']) ?></strong>
                    <span><?= e($announcement['content']) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <div class="panel-header"><h2>Tournament status overview</h2></div>
        <div class="stack-list">
            <?php foreach ($statusOverview as $row): ?>
                <div class="stack-item">
                    <strong><?= e($row['status']) ?></strong>
                    <span><?= e($row['total']) ?> tournaments</span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
