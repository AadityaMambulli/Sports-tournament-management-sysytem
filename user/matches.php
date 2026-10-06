<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = db();
$matches = $pdo->query('SELECT m.*, t1.name as team1_name, t2.name as team2_name, v.name as venue_name FROM matches m LEFT JOIN teams t1 ON t1.id = m.team1_id LEFT JOIN teams t2 ON t2.id = m.team2_id LEFT JOIN venues v ON v.id = m.venue_id ORDER BY m.match_date ASC, m.match_time ASC')->fetchAll();
$pageTitle = 'Matches';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Matches</span>
        <h1>Upcoming and completed fixtures</h1>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Round</th>
                    <th>Venue</th>
                    <th>Date</th>
                    <th>Score</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($matches as $match): ?>
                    <tr>
                        <td><?= e($match['team1_name']) ?> vs <?= e($match['team2_name']) ?></td>
                        <td><?= e($match['round']) ?></td>
                        <td><?= e($match['venue_name']) ?></td>
                        <td><?= e(formatDate($match['match_date'])) ?></td>
                        <td><?= e($match['score_team1']) ?> - <?= e($match['score_team2']) ?></td>
                        <td><?= e($match['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
