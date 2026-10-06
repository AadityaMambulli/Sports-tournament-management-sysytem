<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = db();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/user/register_team.php');
    }

    $teamId = (int)($_POST['team_id'] ?? 0);
    $tournamentId = (int)($_POST['tournament_id'] ?? 0);

    if ($teamId <= 0 || $tournamentId <= 0) {
        setFlash('error', 'Please select a team and tournament.');
        redirect(baseUrl() . '/user/register_team.php');
    }

    $check = $pdo->prepare('SELECT id FROM registrations WHERE tournament_id = :tournament_id AND team_id = :team_id LIMIT 1');
    $check->execute([':tournament_id' => $tournamentId, ':team_id' => $teamId]);
    if ($check->fetch()) {
        setFlash('error', 'This team is already registered.');
        redirect(baseUrl() . '/user/register_team.php');
    }

    $stmt = $pdo->prepare('INSERT INTO registrations (tournament_id, team_id, registered_by, status, created_at) VALUES (:tournament_id, :team_id, :registered_by, :status, NOW())');
    $stmt->execute([
        ':tournament_id' => $tournamentId,
        ':team_id' => $teamId,
        ':registered_by' => $_SESSION['user_id'],
        ':status' => 'Pending',
    ]);

    setFlash('success', 'Team registered successfully.');
    redirect(baseUrl() . '/user/dashboard.php');
}

$teams = $pdo->query('SELECT * FROM teams ORDER BY name ASC')->fetchAll();
$tournaments = $pdo->query('SELECT * FROM tournaments ORDER BY name ASC')->fetchAll();
$pageTitle = 'Register Team';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Register</span>
        <h1>Register a team</h1>
    </div>
</div>

<div class="panel">
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field-group">
            <label>Team</label>
            <select name="team_id" required>
                <option value="">Select team</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= e($team['id']) ?>"><?= e($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Tournament</label>
            <select name="tournament_id" required>
                <option value="">Select tournament</option>
                <?php foreach ($tournaments as $tournament): ?>
                    <option value="<?= e($tournament['id']) ?>"><?= e($tournament['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Register team</button>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
