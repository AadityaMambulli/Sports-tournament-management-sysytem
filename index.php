<?php
require_once __DIR__ . '/includes/header.php';

$pdo = db();

$sports = $pdo->query('SELECT * FROM sports ORDER BY name ASC LIMIT 6')->fetchAll();
$upcomingTournaments = $pdo->query('SELECT t.*, s.name as sport_name FROM tournaments t LEFT JOIN sports s ON s.id = t.sport_id WHERE t.status != "Completed" ORDER BY t.start_date ASC LIMIT 4')->fetchAll();
$announcements = getRecentAnnouncements(3);
$stats = [
    'tournaments' => (int)$pdo->query('SELECT COUNT(*) FROM tournaments')->fetchColumn(),
    'teams' => (int)$pdo->query('SELECT COUNT(*) FROM teams')->fetchColumn(),
    'matches' => (int)$pdo->query('SELECT COUNT(*) FROM matches')->fetchColumn(),
    'sports' => (int)$pdo->query('SELECT COUNT(*) FROM sports')->fetchColumn(),
];

$pageTitle = 'Sports Tournament';
?>
<section class="hero">
    <div class="container hero-inner">
        <div>
            <span class="eyebrow">College Sports Platform</span>
            <h1>Manage tournaments, teams and results with confidence.</h1>
            <p>Streamline registrations, fixtures, player management and venue scheduling for your athletics calendar.</p>
            <div class="cta-row">
                <a href="<?= e(baseUrl()) ?>/index.php#tournaments" class="button button-primary">Explore Tournaments</a>
                <a href="<?= e(baseUrl()) ?>/register.php" class="button button-secondary">Register Your Team</a>
            </div>
        </div>
        <div class="hero-panel">
            <div class="mini-card">
                <span class="mini-label">This Season</span>
                <strong><?= e($stats['tournaments']) ?> Tournaments</strong>
            </div>
            <div class="mini-card">
                <span class="mini-label">Active Clubs</span>
                <strong><?= e($stats['teams']) ?> Teams</strong>
            </div>
            <div class="mini-card">
                <span class="mini-label">Fixtures</span>
                <strong><?= e($stats['matches']) ?> Matches</strong>
            </div>
        </div>
    </div>
</section>

<section id="sports" class="section light-section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow dark">Sports</span>
                <h2>Featured competitions</h2>
            </div>
        </div>
        <div class="card-grid sports-grid">
            <?php foreach ($sports as $sport): ?>
                <article class="info-card sport-card">
                    <div class="card-icon"><?= e(substr($sport['name'], 0, 2)) ?></div>
                    <h3><?= e($sport['name']) ?></h3>
                    <p><?= e($sport['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section id="tournaments" class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow dark">Upcoming events</span>
                <h2>Open tournaments</h2>
            </div>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Tournament</th>
                        <th>Sport</th>
                        <th>Dates</th>
                        <th>Venue</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($upcomingTournaments as $tournament): ?>
                        <tr>
                            <td><?= e($tournament['name']) ?></td>
                            <td><?= e($tournament['sport_name']) ?></td>
                            <td><?= e(formatDate($tournament['start_date'])) ?> - <?= e(formatDate($tournament['end_date'])) ?></td>
                            <td><?= e($tournament['venue']) ?></td>
                            <td><span class="status-badge <?= e(statusBadgeClass($tournament['status'])) ?>"><?= e($tournament['status']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="section light-section">
    <div class="container stats-grid">
        <div class="stat-box">
            <span class="stat-number"><?= e($stats['tournaments']) ?></span>
            <span class="stat-label">Active Tournaments</span>
        </div>
        <div class="stat-box">
            <span class="stat-number"><?= e($stats['teams']) ?></span>
            <span class="stat-label">Registered Teams</span>
        </div>
        <div class="stat-box">
            <span class="stat-number"><?= e($stats['matches']) ?></span>
            <span class="stat-label">Matches Played</span>
        </div>
        <div class="stat-box">
            <span class="stat-number"><?= e($stats['sports']) ?></span>
            <span class="stat-label">Sports</span>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <div>
                <span class="eyebrow dark">Announcements</span>
                <h2>Latest updates</h2>
            </div>
        </div>
        <div class="announcement-list">
            <?php foreach ($announcements as $announcement): ?>
                <article class="info-card announcement-card">
                    <h3><?= e($announcement['title']) ?></h3>
                    <p><?= e($announcement['content']) ?></p>
                    <small>Posted on <?= e(formatDateTime($announcement['created_at'])) ?></small>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
