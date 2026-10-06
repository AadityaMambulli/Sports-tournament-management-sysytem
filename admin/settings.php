<?php
require_once __DIR__ . '/../includes/auth.php';
requireAdmin();

$pageTitle = 'Settings';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Settings</span>
        <h1>System configuration</h1>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h2>College sports profile</h2></div>
    <div class="settings-grid">
        <div class="field-group">
            <label>Institution name</label>
            <input type="text" value="North Campus Sports Council" readonly>
        </div>
        <div class="field-group">
            <label>Primary contact</label>
            <input type="text" value="sports.office@college.edu" readonly>
        </div>
        <div class="field-group">
            <label>Default venue</label>
            <input type="text" value="Main Sports Complex" readonly>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
