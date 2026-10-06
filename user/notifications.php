<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pdo = db();
$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC');
$stmt->execute([':user_id' => $_SESSION['user_id']]);
$notifications = $stmt->fetchAll();
$pageTitle = 'Notifications';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Notifications</span>
        <h1>Your updates</h1>
    </div>
</div>

<div class="panel">
    <div class="stack-list">
        <?php if (empty($notifications)): ?>
            <div class="stack-item">
                <strong>No notifications yet.</strong>
            </div>
        <?php else: ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="stack-item">
                    <strong><?= e($notification['title']) ?></strong>
                    <span><?= e($notification['message']) ?></span>
                    <small><?= e(formatDateTime($notification['created_at'])) ?></small>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
