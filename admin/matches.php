<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/matches.php');
    }

    $tournamentId = (int)($_POST['tournament_id'] ?? 0);
    $team1Id = (int)($_POST['team1_id'] ?? 0);
    $team2Id = (int)($_POST['team2_id'] ?? 0);
    $venueId = (int)($_POST['venue_id'] ?? 0);
    $matchDate = $_POST['match_date'] ?? '';
    $matchTime = $_POST['match_time'] ?? '00:00:00';
    $round = $_POST['round'] ?? 'League';
    $status = $_POST['status'] ?? 'Scheduled';
    $scoreTeam1 = $_POST['score_team1'] ?? 0;
    $scoreTeam2 = $_POST['score_team2'] ?? 0;

    if ($team1Id === $team2Id) {
        setFlash('error', 'A match cannot be played between the same team.');
        redirect(baseUrl() . '/admin/matches.php');
    }

    $stmt = $pdo->prepare('INSERT INTO matches (tournament_id, round, team1_id, team2_id, venue_id, match_date, match_time, score_team1, score_team2, status, created_at) VALUES (:tournament_id, :round, :team1_id, :team2_id, :venue_id, :match_date, :match_time, :score_team1, :score_team2, :status, NOW())');
    $stmt->execute([
        ':tournament_id' => $tournamentId,
        ':round' => $round,
        ':team1_id' => $team1Id,
        ':team2_id' => $team2Id,
        ':venue_id' => $venueId,
        ':match_date' => $matchDate,
        ':match_time' => $matchTime,
        ':score_team1' => $scoreTeam1,
        ':score_team2' => $scoreTeam2,
        ':status' => $status,
    ]);
    setFlash('success', 'Match created successfully.');
    redirect(baseUrl() . '/admin/matches.php');
}

$tournaments = $pdo->query('SELECT * FROM tournaments ORDER BY name ASC')->fetchAll();
$teams = $pdo->query('SELECT * FROM teams ORDER BY name ASC')->fetchAll();
$venues = $pdo->query('SELECT * FROM venues ORDER BY name ASC')->fetchAll();
$matches = $pdo->query('SELECT m.*, t1.name as team1_name, t2.name as team2_name, v.name as venue_name FROM matches m LEFT JOIN teams t1 ON t1.id = m.team1_id LEFT JOIN teams t2 ON t2.id = m.team2_id LEFT JOIN venues v ON v.id = m.venue_id ORDER BY m.match_date DESC, m.match_time DESC')->fetchAll();
$pageTitle = 'Match Management';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Matches</span>
        <h1>Manage fixtures</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Create match</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="field-group">
            <label>Tournament</label>
            <select name="tournament_id" required>
                <option value="">Select tournament</option>
                <?php foreach ($tournaments as $tournament): ?>
                    <option value="<?= e($tournament['id']) ?>"><?= e($tournament['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Round</label>
            <select name="round">
                <option value="Quarter Final">Quarter Final</option>
                <option value="Semi Final">Semi Final</option>
                <option value="Final">Final</option>
                <option value="League">League</option>
                <option value="Friendly">Friendly</option>
            </select>
        </div>
        <div class="field-group">
            <label>Team 1</label>
            <select name="team1_id" required>
                <option value="">Select team</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= e($team['id']) ?>"><?= e($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Team 2</label>
            <select name="team2_id" required>
                <option value="">Select team</option>
                <?php foreach ($teams as $team): ?>
                    <option value="<?= e($team['id']) ?>"><?= e($team['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Venue</label>
            <select name="venue_id" required>
                <option value="">Select venue</option>
                <?php foreach ($venues as $venue): ?>
                    <option value="<?= e($venue['id']) ?>"><?= e($venue['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group">
            <label>Date</label>
            <input type="date" name="match_date" required>
        </div>
        <div class="field-group">
            <label>Time</label>
            <input type="time" name="match_time" required>
        </div>
        <div class="field-group">
            <label>Status</label>
            <select name="status">
                <option value="Scheduled">Scheduled</option>
                <option value="Completed">Completed</option>
            </select>
        </div>
        <div class="field-group">
            <label>Score Team 1</label>
            <input type="number" name="score_team1" value="0">
        </div>
        <div class="field-group">
            <label>Score Team 2</label>
            <input type="number" name="score_team2" value="0">
        </div>

        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Create match</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h2>Fixtures</h2></div>
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
