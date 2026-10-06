<?php
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid form submission.');
        redirect(baseUrl() . '/login.php');
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        setFlash('error', 'Email and password are required.');
        redirect(baseUrl() . '/login.php');
    }

    $stmt = db()->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect(baseUrl() . '/dashboard.php');
    }

    setFlash('error', 'Invalid email or password.');
    redirect(baseUrl() . '/login.php');
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<section class="auth-wrap">
    <div class="auth-card">
        <div class="auth-header">
            <span class="eyebrow dark">Welcome back</span>
            <h1>Login to your account</h1>
        </div>

        <form method="post" class="auth-form">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

            <label>
                Email
                <input type="email" name="email" value="" required>
            </label>

            <label>
                Password
                <div class="password-box">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password" data-target="password">Show</button>
                </div>
            </label>

            <button type="submit" class="button button-primary full-width">Login</button>
        </form>

        <p class="inline-note">Need an account? <a href="<?= e(baseUrl()) ?>/register.php">Register here</a></p>
    </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
