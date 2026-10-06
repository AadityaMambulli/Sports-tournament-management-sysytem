<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = db();
$id = $_GET['id'] ?? 0;
if (!isValidId($id)) {
    setFlash('error', 'Invalid tournament.');
    redirect(baseUrl() . '/user/tournaments.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/user/tournament_view.php?id=' . (int)$id);
    }

    $teamId = (int)($_POST['team_id'] ?? 0);
    $tournamentId = (int)$id;

    $check = $pdo->prepare('SELECT id FROM registrations WHERE tournament_id = :tournament_id AND team_id = :team_id LIMIT 1');
    $check->execute([':tournament_id' => $tournamentId, ':team_id' => $teamId]);
    if ($check->fetch()) {
        setFlash('error', 'This team is already registered for the tournament.');
        redirect(baseUrl() . '/user/tournament_view.php?id=' . $tournamentId);
    }

    $stmt = $pdo->prepare('INSERT INTO registrations (tournament_id, team_id, registered_by, status, created_at) VALUES (:tournament_id, :team_id, :registered_by, :status, NOW())');
    $stmt->execute([
        ':tournament_id' => $tournamentId,
        ':team_id' => $teamId,
        ':registered_by' => $_SESSION['user_id'],
        ':status' => 'Pending',
    ]);

    setFlash('success', 'Team registered successfully.');
    redirect(baseUrl() . '/user/tournament_view.php?id=' . $tournamentId);
}

$tournament = $pdo->prepare('SELECT t.*, s.name as sport_name FROM tournaments t LEFT JOIN sports s ON s.id = t.sport_id WHERE t.id = :id LIMIT 1');
$tournament->execute([':id' => $id]);
$tournament = $tournament->fetch();
if (!$tournament) {
    setFlash('error', 'Invalid tournament.');
    redirect(baseUrl() . '/user/tournaments.php');
}

$teams = $pdo->query('SELECT * FROM teams ORDER BY name ASC')->fetchAll();
$standings = getTournamentStandings((int)$id);
$pageTitle = 'Tournament Details';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Tournament details</span>
        <h1><?= e($tournament['name']) ?></h1>
    </div>
</div>

<div class="panel">
    <div class="details-grid">
        <div>
            <p><strong>Sport:</strong> <?= e($tournament['sport_name']) ?></p>
            <p><strong>Venue:</strong> <?= e($tournament['venue']) ?></p>
            <p><strong>Dates:</strong> <?= e(formatDate($tournament['start_date'])) ?> - <?= e(formatDate($tournament['end_date'])) ?></p>
            <p><strong>Registration Deadline:</strong> <?= e(formatDate($tournament['registration_deadline'])) ?></p>
            <p><strong>Status:</strong> <span class="status-badge <?= e(statusBadgeClass($tournament['status'])) ?>"><?= e($tournament['status']) ?></span></p>
        </div>
        <div>
            <p><?= e($tournament['description']) ?></p>
            <form method="post" class="inline-form">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <label>Register a team
                    <select name="team_id" required>
                        <option value="">Select team</option>
                        <?php foreach ($teams as $team): ?>
                            <option value="<?= e($team['id']) ?>"><?= e($team['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <button type="submit" class="button button-primary">Register team</button>
            </form>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Standings</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Team</th>
                    <th>Played</th>
                    <th>Won</th>
                    <th>Lost</th>
                    <th>Points</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($standings)): ?>
                    <tr><td colspan="6">Standings will appear once matches are completed.</td></tr>
                <?php else: ?>
                    <?php foreach ($standings as $standing): ?>
                        <tr>
                            <td><?= e($standing['position']) ?></td>
                            <td><?= e($standing['team']) ?></td>
                            <td><?= e($standing['played']) ?></td>
                            <td><?= e($standing['won']) ?></td>
                            <td><?= e($standing['lost']) ?></td>
                            <td><?= e($standing['points']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
