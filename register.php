<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/register.php');
    }

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($name === '' || $email === '' || $phone === '' || $password === '' || $confirmPassword === '') {
        setFlash('error', 'All fields are required.');
        redirect(baseUrl() . '/register.php');
    }

    if ($password !== $confirmPassword) {
        setFlash('error', 'Passwords do not match.');
        redirect(baseUrl() . '/register.php');
    }

    $existing = db()->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
    $existing->execute([':email' => $email]);
    if ($existing->fetch()) {
        setFlash('error', 'An account with that email already exists.');
        redirect(baseUrl() . '/register.php');
    }

    $stmt = db()->prepare('INSERT INTO users (name, email, password, role, phone, created_at) VALUES (:name, :email, :password, :role, :phone, NOW())');
    $stmt->execute([
        ':name' => $name,
        ':email' => strtolower($email),
        ':password' => password_hash($password, PASSWORD_DEFAULT),
        ':role' => 'user',
        ':phone' => $phone,
    ]);

    setFlash('success', 'Registration successful. Please log in.');
    redirect(baseUrl() . '/login.php');
}

$pageTitle = 'Register';
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <div class="auth-card">
        <div class="auth-header">
            <span class="eyebrow dark">Create account</span>
            <h1>Register as participant</h1>
        </div>

        <form method="post" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <label>
                Full name
                <input type="text" name="name" required>
            </label>

            <label>
                Email
                <input type="email" name="email" required>
            </label>

            <label>
                Phone
                <input type="tel" name="phone" required>
            </label>

            <label>
                Password
                <div class="password-box">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password" data-target="password">Show</button>
                </div>
            </label>

            <label>
                Confirm password
                <div class="password-box">
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <button type="button" class="toggle-password" data-target="confirm_password">Show</button>
                </div>
            </label>

            <button type="submit" class="button button-primary full-width">Create account</button>
        </form>

        <p class="inline-note">Already have an account? <a href="<?= e(baseUrl()) ?>/login.php">Login here</a></p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
