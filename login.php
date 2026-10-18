<?php
require_once __DIR__ . '/includes/auth.php';
if (!empty($_SESSION['user_id'])) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, full_name, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        header('Location: index.php'); exit;
    }
    $error = 'Incorrect username or password.';
}
$page_title = 'Login';
include __DIR__ . '/includes/header.php';
?>
<div class="login-wrap">
    <div class="card p-4 p-md-5">
        <h1 class="h3 mb-1">Welcome back</h1>
        <p class="text-muted mb-4">Sign in to manage inventory.</p>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>
    <form method="post"><div class="mb-3">
        <label class="form-label" for="username">Username</label><input class="form-control" id="username" name="username" required autocomplete="username"></div>
        <div class="mb-4"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" required autocomplete="current-password"></div>
        <button class="btn btn-primary w-100">Login</button></form>
</div>
</div>

