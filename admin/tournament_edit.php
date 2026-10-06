<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();
$id = $_GET['id'] ?? 0;
if (!isValidId($id)) {
    setFlash('error', 'Invalid tournament.');
    redirect(baseUrl() . '/admin/tournaments.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/tournaments.php');
    }

    $stmt = $pdo->prepare('UPDATE tournaments SET sport_id = :sport_id, name = :name, description = :description, start_date = :start_date, end_date = :end_date, registration_deadline = :registration_deadline, venue = :venue, status = :status WHERE id = :id');
    $stmt->execute([
        ':sport_id' => $_POST['sport_id'],
        ':name' => trim($_POST['name']),
        ':description' => trim($_POST['description']),
        ':start_date' => $_POST['start_date'],
        ':end_date' => $_POST['end_date'],
        ':registration_deadline' => $_POST['registration_deadline'],
        ':venue' => trim($_POST['venue']),
        ':status' => $_POST['status'],
        ':id' => $id,
    ]);
    setFlash('success', 'Tournament updated successfully.');
    redirect(baseUrl() . '/admin/tournaments.php');
}

$tournament = $pdo->prepare('SELECT * FROM tournaments WHERE id = :id LIMIT 1');
$tournament->execute([':id' => $id]);
$tournament = $tournament->fetch();
if (!$tournament) {
    setFlash('error', 'Invalid tournament.');
    redirect(baseUrl() . '/admin/tournaments.php');
}

$sports = $pdo->query('SELECT * FROM sports ORDER BY name ASC')->fetchAll();
$pageTitle = 'Edit Tournament';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Edit tournament</span>
        <h1><?= e($tournament['name']) ?></h1>
    </div>
</div>

<div class="panel">
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field-group">
            <label>Name</label>
            <input type="text" name="name" value="<?= e($tournament['name']) ?>" required>
        </div>
        <div class="field-group">
            <label>Sport</label>
            <select name="sport_id" required>
                <?php foreach ($sports as $sport): ?>
                    <option value="<?= e($sport['id']) ?>" <?= $sport['id'] == $tournament['sport_id'] ? 'selected' : '' ?>><?= e($sport['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group full-width">
            <label>Description</label>
            <textarea name="description" rows="3"><?= e($tournament['description']) ?></textarea>
        </div>
        <div class="field-group">
            <label>Start date</label>
            <input type="date" name="start_date" value="<?= e($tournament['start_date']) ?>" required>
        </div>
        <div class="field-group">
            <label>End date</label>
            <input type="date" name="end_date" value="<?= e($tournament['end_date']) ?>" required>
        </div>
        <div class="field-group">
            <label>Registration deadline</label>
            <input type="date" name="registration_deadline" value="<?= e($tournament['registration_deadline']) ?>" required>
        </div>
        <div class="field-group">
            <label>Venue</label>
            <input type="text" name="venue" value="<?= e($tournament['venue']) ?>" required>
        </div>
        <div class="field-group">
            <label>Status</label>
            <select name="status">
                <?php foreach (['Upcoming', 'Registration Open', 'Ongoing', 'Completed', 'Cancelled'] as $status): ?>
                    <option value="<?= e($status) ?>" <?= $status === $tournament['status'] ? 'selected' : '' ?>><?= e($status) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Update tournament</button>
            <a href="<?= e(baseUrl()) ?>/admin/tournaments.php" class="button button-secondary">Back</a>
        </div>
    </form>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
