<?php
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

if ((string)($_SESSION['role'] ?? '') !== 'user') {
    setFlash('error', 'Participant profiles are available to user accounts.');
    redirect(baseUrl() . '/dashboard.php');
}

$userId = (int)$_SESSION['user_id'];
$userQuery = db()->prepare('SELECT name, email, phone FROM users WHERE id = :id LIMIT 1');
$userQuery->execute([':id' => $userId]);
$user = $userQuery->fetch();

if (!$user) {
    setFlash('error', 'Your account could not be found. Please log in again.');
    redirect(baseUrl() . '/logout.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/user/profile.php');
    }

    $upload = $_FILES['profile_photo'] ?? null;
    $maxFileSize = 2 * 1024 * 1024;
    $errorMessage = null;

    if (!is_array($upload) || !isset($upload['error']) || !is_int($upload['error'])) {
        $errorMessage = 'The uploaded file could not be read. Please try again.';
    } elseif ($upload['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'The image exceeds the server upload size limit.',
            UPLOAD_ERR_FORM_SIZE => 'The image is larger than the 2 MB limit.',
            UPLOAD_ERR_PARTIAL => 'The image upload was interrupted. Please try again.',
            UPLOAD_ERR_NO_FILE => 'Choose a JPG, PNG or WebP image to upload.',
            UPLOAD_ERR_NO_TMP_DIR => 'The server upload folder is unavailable.',
            UPLOAD_ERR_CANT_WRITE => 'The server could not save the uploaded image.',
            UPLOAD_ERR_EXTENSION => 'The server stopped the image upload.',
        ];
        $errorMessage = $errorMessages[$upload['error']] ?? 'The image upload failed. Please try again.';
    } elseif (
        !isset($upload['name'], $upload['tmp_name'], $upload['size'])
        || !is_string($upload['name'])
        || !is_string($upload['tmp_name'])
        || !is_numeric($upload['size'])
    ) {
        $errorMessage = 'The uploaded file details are invalid.';
    } else {
        $extension = strtolower(pathinfo($upload['name'], PATHINFO_EXTENSION));
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }
        $allowedMimeTypes = [
            'jpg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
        ];
        $fileSize = (int)$upload['size'];

        if (!isset($allowedMimeTypes[$extension])) {
            $errorMessage = 'Only JPG, PNG and WebP images are allowed.';
        } elseif ($fileSize <= 0 || $fileSize > $maxFileSize) {
            $errorMessage = 'Choose an image that is smaller than 2 MB.';
        } elseif (!is_uploaded_file($upload['tmp_name'])) {
            $errorMessage = 'The uploaded file is invalid. Please choose the image again.';
        } elseif (!class_exists('finfo') || !function_exists('getimagesize')) {
            $errorMessage = 'Image validation is unavailable on this server.';
        } else {
            $fileInfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo->file($upload['tmp_name']);
            $imageInfo = @getimagesize($upload['tmp_name']);

            if (
                $mimeType !== $allowedMimeTypes[$extension]
                || !is_array($imageInfo)
                || ($imageInfo['mime'] ?? '') !== $allowedMimeTypes[$extension]
            ) {
                $errorMessage = 'The selected file is not a valid JPG, PNG or WebP image.';
            } else {
                $uploadDirectory = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile-photos';
                if (is_link($uploadDirectory)) {
                    $errorMessage = 'The profile photo folder is not configured safely.';
                } elseif (!is_dir($uploadDirectory) && !@mkdir($uploadDirectory, 0755, true) && !is_dir($uploadDirectory)) {
                    $errorMessage = 'The server could not create the profile photo folder.';
                } else {
                    $filename = 'profile-' . $userId . '.' . $extension;
                    $destination = $uploadDirectory . DIRECTORY_SEPARATOR . $filename;

                    if (is_link($destination) || (file_exists($destination) && !is_file($destination))) {
                        $errorMessage = 'The profile photo destination is not a regular file.';
                    } else {
                        $previousPhoto = getProfilePhoto($userId);
                        if (!move_uploaded_file($upload['tmp_name'], $destination)) {
                            $errorMessage = 'The server could not save your profile photo. Please try again.';
                        } else {
                            if ($previousPhoto !== null && $previousPhoto !== $filename) {
                                $previousPath = $uploadDirectory . DIRECTORY_SEPARATOR . $previousPhoto;
                                if (is_file($previousPath) && !is_link($previousPath) && !@unlink($previousPath)) {
                                    setFlash('error', 'Your new photo was saved, but the previous photo could not be removed.');
                                    redirect(baseUrl() . '/user/profile.php');
                                }
                            }

                            setFlash('success', 'Profile photo updated successfully.');
                            redirect(baseUrl() . '/user/profile.php');
                        }
                    }
                }
            }
        }
    }

    setFlash('error', $errorMessage ?? 'The image upload failed. Please try again.');
    redirect(baseUrl() . '/user/profile.php');
}

$photoFilename = getProfilePhoto($userId);
$pageTitle = 'My Profile';
require_once __DIR__ . '/../includes/header.php';
?>
<div class="page-header">
    <div>
        <span class="eyebrow dark">Participant profile</span>
        <h1>My profile</h1>
    </div>
</div>

<section class="panel">
    <div class="profile-overview">
        <div>
            <?php if ($photoFilename !== null): ?>
                <?php
                $photoPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profile-photos' . DIRECTORY_SEPARATOR . $photoFilename;
                clearstatcache(true, $photoPath);
                ?>
                <img
                    class="profile-photo"
                    src="<?= e(baseUrl() . '/uploads/profile-photos/' . rawurlencode($photoFilename) . '?v=' . (int)filemtime($photoPath)) ?>"
                    alt="Profile photo for <?= e($user['name']) ?>"
                >
            <?php else: ?>
                <p>No profile photo uploaded yet.</p>
            <?php endif; ?>
        </div>
        <div>
            <h2><?= e($user['name']) ?></h2>
            <p><strong>Email:</strong> <?= e($user['email']) ?></p>
            <p><strong>Phone:</strong> <?= e($user['phone'] ?: 'Not provided') ?></p>
        </div>
    </div>
</section>

<section class="panel">
    <div class="panel-header"><h2>Update profile photo</h2></div>
    <form method="POST" enctype="multipart/form-data" class="form-grid">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="2097152">
        <div class="field-group full-width">
            <label for="profile_photo">Choose a JPG, PNG or WebP image (maximum 2 MB)</label>
            <input
                type="file"
                id="profile_photo"
                name="profile_photo"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                required
            >
        </div>
        <div class="form-actions full-width">
            <button type="submit" class="button button-primary">Upload photo</button>
        </div>
    </form>
</section>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
