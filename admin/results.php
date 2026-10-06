<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/results.php');
    }

    $tournamentId = (int)($_POST['tournament_id'] ?? 0);
    $matchId = (int)($_POST['match_id'] ?? 0);
    $teamId = (int)($_POST['team_id'] ?? 0);
    $points = (int)($_POST['points'] ?? 0);
    $position = (int)($_POST['position'] ?? 0);

    if ($tournamentId > 0 && $matchId > 0 && $teamId > 0) {
        $stmt = $pdo->prepare('INSERT INTO results (tournament_id, match_id, team_id, points, position, created_at) VALUES (:tournament_id, :match_id, :team_id, :points, :position, NOW())');
        $stmt->execute([
            ':tournament_id' => $tournamentId,
            ':match_id' => $matchId,
            ':team_id' => $teamId,
            ':points' => $points,
            ':position' => $position,
        ]);
        setFlash('success', 'Result entry saved.');
    }

    redirect(baseUrl() . '/admin/results.php');
}

$tournaments = $pdo->query('SELECT * FROM tournaments ORDER BY name ASC')->fetchAll();
$matches = $pdo->query('SELECT m.*, t1.name as team1_name, t2.name as team2_name FROM matches m LEFT JOIN teams t1 ON t1.id = m.team1_id LEFT JOIN teams t2 ON t2.id = m.team2_id ORDER BY m.match_date DESC')->fetchAll();
$pageTitle = 'Tournament Results';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Results</span>
        <h1>Match results and standings</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Record result</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field-group">
            <label>Tournament</label>
            <select name="tournament_id">
                <option value="">Select tournament</option>
                <?php foreach ($tournaments as $tournament): ?>
                    <option value="<?= e($tournament['id']) ?>"><?= e($tournament['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Match</label>
            <select name="match_id">
                <option value="">Select match</option>
                <?php foreach ($matches as $match): ?>
                    <option value="<?= e($match['id']) ?>"><?= e($match['team1_name']) ?> vs <?= e($match['team2_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Team</label>
            <select name="team_id">
                <option value="">Select team</option>
                <?php foreach ($pdo->query('SELECT * FROM teams ORDER BY name ASC')->fetchAll() as $team): ?>
                    <option value="<?= e($team['id']) ?>"><?= e($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Points</label>
            <input type="number" name="points" value="0">
        </div>
        <div class="field-group">
            <label>Position</label>
            <input type="number" name="position" value="1">
        </div>
        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Save result</button>
        </div>
    </form>
</div>

<?php foreach ($tournaments as $tournament): ?>
    <?php $standings = getTournamentStandings((int)$tournament['id']); ?>
    <div class="panel">
        <div class="panel-header"><h2><?= e($tournament['name']) ?> standings</h2></div>
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
                        <tr><td colspan="6">No completed matches yet.</td></tr>
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
<?php endforeach; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
