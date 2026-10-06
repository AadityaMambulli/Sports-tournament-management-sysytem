<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/tournaments.php');
    }

    $action = $_POST['action'] ?? 'add';
    $name = trim($_POST['name'] ?? '');
    $sportId = $_POST['sport_id'] ?? null;
    $description = trim($_POST['description'] ?? '');
    $startDate = $_POST['start_date'] ?? '';
    $endDate = $_POST['end_date'] ?? '';
    $registrationDeadline = $_POST['registration_deadline'] ?? '';
    $venue = trim($_POST['venue'] ?? '');
    $status = $_POST['status'] ?? 'Upcoming';

    if ($name === '' || $sportId === '' || $startDate === '' || $endDate === '' || $registrationDeadline === '' || $venue === '') {
        setFlash('error', 'Please complete all required tournament fields.');
        redirect(baseUrl() . '/admin/tournaments.php');
    }

    if ($action === 'edit') {
        $id = (int)($_POST['id'] ?? 0);
        $stmt = $pdo->prepare('UPDATE tournaments SET sport_id = :sport_id, name = :name, description = :description, start_date = :start_date, end_date = :end_date, registration_deadline = :registration_deadline, venue = :venue, status = :status WHERE id = :id');
        $stmt->execute([
            ':sport_id' => $sportId,
            ':name' => $name,
            ':description' => $description,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':registration_deadline' => $registrationDeadline,
            ':venue' => $venue,
            ':status' => $status,
            ':id' => $id,
        ]);
        setFlash('success', 'Tournament updated successfully.');
    } else {
        $stmt = $pdo->prepare('INSERT INTO tournaments (sport_id, name, description, start_date, end_date, registration_deadline, venue, status, created_at) VALUES (:sport_id, :name, :description, :start_date, :end_date, :registration_deadline, :venue, :status, NOW())');
        $stmt->execute([
            ':sport_id' => $sportId,
            ':name' => $name,
            ':description' => $description,
            ':start_date' => $startDate,
            ':end_date' => $endDate,
            ':registration_deadline' => $registrationDeadline,
            ':venue' => $venue,
            ':status' => $status,
        ]);
        setFlash('success', 'Tournament created successfully.');
    }

    redirect(baseUrl() . '/admin/tournaments.php');
}

$sports = $pdo->query('SELECT * FROM sports ORDER BY name ASC')->fetchAll();
$tournaments = $pdo->query('SELECT t.*, s.name as sport_name FROM tournaments t LEFT JOIN sports s ON s.id = t.sport_id ORDER BY t.start_date DESC')->fetchAll();

$pageTitle = 'Tournament Management';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Tournaments</span>
        <h1>Manage tournament schedule</h1>
    </div>
    <a href="<?= e(baseUrl()) ?>/admin/tournament_add.php" class="button button-primary">Add Tournament</a>
</div>

<div class="panel">
    <div class="panel-header"><h2>Create tournament</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="action" value="add">

        <div class="field-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="field-group">
            <label>Sport</label>
            <select name="sport_id" required>
                <option value="">Select sport</option>
                <?php foreach ($sports as $sport): ?>
                    <option value="<?= e($sport['id']) ?>"><?= e($sport['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field-group full-width">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
        </div>
        <div class="field-group">
            <label>Start date</label>
            <input type="date" name="start_date" required>
        </div>
        <div class="field-group">
            <label>End date</label>
            <input type="date" name="end_date" required>
        </div>
        <div class="field-group">
            <label>Registration deadline</label>
            <input type="date" name="registration_deadline" required>
        </div>
        <div class="field-group">
            <label>Venue</label>
            <input type="text" name="venue" required>
        </div>
        <div class="field-group">
            <label>Status</label>
            <select name="status">
                <option value="Upcoming">Upcoming</option>
                <option value="Registration Open">Registration Open</option>
                <option value="Ongoing">Ongoing</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>
        </div>

        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Create tournament</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h2>Existing tournaments</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Sport</th>
                    <th>Date</th>
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
                        <td>
                            <div class="table-actions">
                                <a href="<?= e(baseUrl()) ?>/admin/tournament_edit.php?id=<?= e($tournament['id']) ?>" class="link-button">Edit</a>
                                <a href="<?= e(baseUrl()) ?>/admin/tournament_delete.php?id=<?= e($tournament['id']) ?>" class="link-button danger" onclick="return confirm('Delete this tournament?');">Delete</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
