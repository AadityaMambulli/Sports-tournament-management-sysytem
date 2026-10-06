<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/venues.php');
    }

    $name = trim($_POST['name'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $capacity = trim($_POST['capacity'] ?? '');
    $facilities = trim($_POST['facilities'] ?? '');

    if ($name === '' || $location === '' || $capacity === '') {
        setFlash('error', 'Venue name, location and capacity are required.');
        redirect(baseUrl() . '/admin/venues.php');
    }

    $stmt = $pdo->prepare('INSERT INTO venues (name, location, capacity, facilities, created_at) VALUES (:name, :location, :capacity, :facilities, NOW())');
    $stmt->execute([
        ':name' => $name,
        ':location' => $location,
        ':capacity' => $capacity,
        ':facilities' => $facilities,
    ]);
    setFlash('success', 'Venue added successfully.');
    redirect(baseUrl() . '/admin/venues.php');
}

$venues = $pdo->query('SELECT * FROM venues ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Venue Management';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Venues</span>
        <h1>Manage venues</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Add venue</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field-group">
            <label>Name</label>
            <input type="text" name="name" required>
        </div>
        <div class="field-group">
            <label>Location</label>
            <input type="text" name="location" required>
        </div>
        <div class="field-group">
            <label>Capacity</label>
            <input type="number" name="capacity" required>
        </div>
        <div class="field-group full-width">
            <label>Facilities</label>
            <textarea name="facilities" rows="3"></textarea>
        </div>
        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Save venue</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h2>Venue list</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Facilities</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venues as $venue): ?>
                    <tr>
                        <td><?= e($venue['name']) ?></td>
                        <td><?= e($venue['location']) ?></td>
                        <td><?= e($venue['capacity']) ?></td>
                        <td><?= e($venue['facilities']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
