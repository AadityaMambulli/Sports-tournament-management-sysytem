<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = db();
$tournaments = $pdo->query('SELECT t.*, s.name as sport_name FROM tournaments t LEFT JOIN sports s ON s.id = t.sport_id ORDER BY t.start_date ASC')->fetchAll();
$pageTitle = 'Browse Tournaments';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Tournaments</span>
        <h1>Browse available events</h1>
    </div>
</div>

<div class="panel">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sport</th>
                    <th>Dates</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tournaments as $tournament): ?>
                    <tr>
                        <td><?= e($tournament['name']) ?></td>
                        <td><?= e($tournament['sport_name']) ?></td>
                        <td><?= e(formatDate($tournament['start_date'])) ?> - <?= e(formatDate($tournament['end_date'])) ?></td>
                        <td><?= e($tournament['venue']) ?></td>
                        <td><span class="status-badge <?= e(statusBadgeClass($tournament['status'])) ?>"><?= e($tournament['status']) ?></span></td>
                        <td><a href="<?= e(baseUrl()) ?>/user/tournament_view.php?id=<?= e($tournament['id']) ?>" class="button button-secondary small">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
