<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pdo = db();
$users = $pdo->query('SELECT id, name, email, role, phone, created_at FROM users ORDER BY created_at DESC')->fetchAll();
$pageTitle = 'Users';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Users</span>
        <h1>User accounts</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>Registered users</h2></div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= e($user['name']) ?></td>
                        <td><?= e($user['email']) ?></td>
                        <td><?= e($user['role']) ?></td>
                        <td><?= e($user['phone']) ?></td>
                        <td><?= e(formatDateTime($user['created_at'])) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
