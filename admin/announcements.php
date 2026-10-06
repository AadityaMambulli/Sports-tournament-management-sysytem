<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/admin/announcements.php');
    }

    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $createdBy = $_SESSION['user_id'];

    if ($title === '' || $content === '') {
        setFlash('error', 'Announcement title and content are required.');
        redirect(baseUrl() . '/admin/announcements.php');
    }

    $stmt = $pdo->prepare('INSERT INTO announcements (title, content, created_by, created_at) VALUES (:title, :content, :created_by, NOW())');
    $stmt->execute([
        ':title' => $title,
        ':content' => $content,
        ':created_by' => $createdBy,
    ]);

    setFlash('success', 'Announcement posted successfully.');
    redirect(baseUrl() . '/admin/announcements.php');
}

$announcements = getRecentAnnouncements(10);
$pageTitle = 'Announcements';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Announcements</span>
        <h1>Post updates</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Create announcement</h2></div>
    <form method="post" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <div class="field-group full-width">
            <label>Title</label>
            <input type="text" name="title" required>
        </div>
        <div class="field-group full-width">
            <label>Content</label>
            <textarea name="content" rows="4" required></textarea>
        </div>
        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Publish announcement</button>
        </div>
    </form>
</div>

<div class="panel">
    <div class="panel-header"><h2>Recent announcements</h2></div>
    <div class="stack-list">
        <?php foreach ($announcements as $announcement): ?>
            <div class="stack-item">
                <strong><?= e($announcement['title']) ?></strong>
                <span><?= e($announcement['content']) ?></span>
                <small><?= e(formatDateTime($announcement['created_at'])) ?></small>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
