<?php
require_once __DIR__ . '/config/database.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($name === '' || $username === '' || strlen($password) < 8) {
        $message = 'Enter a name, username, and password with at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (full_name, username, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $username, password_hash($password, PASSWORD_DEFAULT), 'admin']);
            $message = 'Admin account created. Delete setup_admin.php now, then log in.';
        } catch (PDOException $ex) {
            $message = 'Could not create account. Username may already exist, or database tables are not imported.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Setup Admin</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body class="bg-light">
        <div class="container py-5">
            <div class="card p-4 mx-auto" style="max-width:520px">
                <h1 class="h3">Create First Admin</h1>
                <p class="text-muted">Run once after importing database/schema.sql.</p>
<?php if ($message !== ''): ?>
    <div class="alert alert-info"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<form method="post">
    <div class="mb-3">
        <label class="form-label">Full name</label>
        <input class="form-control" name="full_name" required maxlength="120"></div>
        <div class="mb-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" required maxlength="60"></div>
        <div class="mb-3">
            <label class="form-label">Password (minimum 8 characters)</label>
            <input class="form-control" type="password" name="password" required minlength="8"></div>
        <button class="btn btn-primary">Create Admin</button>
         <a class="btn btn-outline-secondary" href="login.php">Login</a></form></div></div></body></html>
