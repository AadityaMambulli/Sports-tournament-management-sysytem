<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$id = $_GET['id'] ?? 0;
if (!isValidId($id)) {
    setFlash('error', 'Invalid tournament.');
    redirect(baseUrl() . '/admin/tournaments.php');
}

$stmt = db()->prepare('DELETE FROM tournaments WHERE id = :id');
$stmt->execute([':id' => (int)$id]);

setFlash('success', 'Tournament deleted successfully.');
redirect(baseUrl() . '/admin/tournaments.php');
