<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/players.php');
    }

    $name = trim($_POST['name'] ?? '');
    $teamId = $_POST['team_id'] ?? null;
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $jerseyNumber = trim($_POST['jersey_number'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if ($name === '' || $teamId === '' || $jerseyNumber === '' || $position === '') {
        setFlash('error', 'Player name, team, jersey number and position are required.');
        redirect(baseUrl() . '/admin/players.php');
    }

    $stmt = $pdo->prepare('INSERT INTO players (team_id, name, email, phone, jersey_number, position, created_at) VALUES (:team_id, :name, :email, :phone, :jersey_number, :position, NOW())');
    $stmt->execute([
        ':team_id' => (int)$teamId,
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone,
        ':jersey_number' => $jerseyNumber,
        ':position' => $position,
    ]);
    setFlash('success', 'Player added successfully.');
    redirect(baseUrl() . '/admin/players.php');
}

$teams = $pdo->query('SELECT * FROM teams ORDER BY name ASC')->fetchAll();
$players = $pdo->query('SELECT p.*, t.name as team_name FROM players p LEFT JOIN teams t ON t.id = p.team_id ORDER BY p.created_at DESC')->fetchAll();
$pageTitle = 'Player Management';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Players</span>
        <h1>Manage players</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Add player</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="field-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
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
            <label>Email</label>
            <input type="email" name="email">
        </div>
        <div class="field-group">
            <label>Phone</label>
            <input type="tel" name="phone">
        </div>
        <div class="field-group">
            <label>Jersey number</label>
            <input type="number" name="jersey_number" required>
        </div>
        <div class="field-group">
            <label>Position</label>
            <input type="text" name="position" required>
        </div>

        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Save player</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h2>Player roster</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Team</th>
                    <th>Jersey</th>
                    <th>Position</th>
                    <th>Phone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($players as $player): ?>
                    <tr>
                        <td><?= e($player['name']) ?></td>
                        <td><?= e($player['team_name']) ?></td>
                        <td><?= e($player['jersey_number']) ?></td>
                        <td><?= e($player['position']) ?></td>
                        <td><?= e($player['phone']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
