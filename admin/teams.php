<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/teams.php');
    }

    if (isset($_POST['delete_id'])) {
        $id = (int)$_POST['delete_id'];
        $pdo->prepare('DELETE FROM teams WHERE id = :id')->execute([':id' => $id]);
        setFlash('success', 'Team deleted successfully.');
        redirect(baseUrl() . '/admin/teams.php');
    }

    $name = trim($_POST['name'] ?? '');
    $tournamentId = $_POST['tournament_id'] ?? null;
    $captainName = trim($_POST['captain_name'] ?? '');
    $captainEmail = trim($_POST['captain_email'] ?? '');
    $captainPhone = trim($_POST['captain_phone'] ?? '');

    if ($name === '' || $tournamentId === '' || $captainName === '') {
        setFlash('error', 'Team name, tournament and captain information are required.');
        redirect(baseUrl() . '/admin/teams.php');
    }

    $stmt = $pdo->prepare('INSERT INTO teams (tournament_id, name, captain_name, captain_email, captain_phone, created_at) VALUES (:tournament_id, :name, :captain_name, :captain_email, :captain_phone, NOW())');
    $stmt->execute([
        ':tournament_id' => (int)$tournamentId,
        ':name' => $name,
        ':captain_name' => $captainName,
        ':captain_email' => $captainEmail,
        ':captain_phone' => $captainPhone,
    ]);
    setFlash('success', 'Team created successfully.');
    redirect(baseUrl() . '/admin/teams.php');
}

$tournaments = $pdo->query('SELECT * FROM tournaments ORDER BY name ASC')->fetchAll();
$teams = $pdo->query('SELECT t.*, tr.name as tournament_name FROM teams t LEFT JOIN tournaments tr ON tr.id = t.tournament_id ORDER BY t.created_at DESC')->fetchAll();
$pageTitle = 'Team Management';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Teams</span>
        <h1>Manage tournament teams</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Add team</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field-group">
            <label>Team name</label>
            <input type="text" name="name" required>
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
        <div class="field-group">
            <label>Captain name</label>
            <input type="text" name="captain_name" required>
        </div>
        <div class="field-group">
            <label>Captain email</label>
            <input type="email" name="captain_email">
        </div>
        <div class="field-group">
            <label>Captain phone</label>
            <input type="tel" name="captain_phone">
        </div>
        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Save team</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h2>Teams</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Tournament</th>
                    <th>Captain</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $team): ?>
                    <tr>
                        <td><?= e($team['name']) ?></td>
                        <td><?= e($team['tournament_name']) ?></td>
                        <td><?= e($team['captain_name']) ?></td>
                        <td><?= e($team['captain_phone']) ?></td>
                        <td>
                            <form method="post" onsubmit="return confirm('Delete this team?');">
                                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="delete_id" value="<?= e($team['id']) ?>">
                                <button type="submit" class="link-button danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
