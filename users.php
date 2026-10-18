<?php
require_once __DIR__ . '/includes/auth.php';
require_admin();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    if ($name === '' || $username === '' || strlen($password) < 8 || !in_array($role, ['admin','user'], true)) {
        $errors[] = 'Complete all fields. Password must be at least 8 characters.';
    } else {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (full_name,username,password_hash,role) VALUES (?,?,?,?)');
            $stmt->execute([$name,$username,password_hash($password,PASSWORD_DEFAULT),$role]);
            flash('success','User account created.');
            header('Location: users.php'); exit;
        } catch (PDOException $ex) { $errors[] = 'Could not create user. Username may already exist.'; }
    }
}
$users = $pdo->query('SELECT id,full_name,username,role,created_at FROM users ORDER BY id DESC')->fetchAll();
$page_title = 'User Management';
include __DIR__ . '/includes/header.php';
?>
<div class="mb-3">
    <h1 class="h2">User Management</h1>
    <p class="text-muted">Admin-only page for creating accounts and assigning roles.</p>
</div>
<div class="card p-4 mb-4">
    <h2 class="h5">Add User</h2>
<?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"><?= e($err) ?>
</div>
<?php endforeach; ?>
<form method="post" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="col-md-4">
        <label class="form-label">Full name</label>
        <input class="form-control" name="full_name" required maxlength="120"></div>
        <div class="col-md-3">
            <label class="form-label">Username</label>
            <input class="form-control" name="username" required maxlength="60"></div>
            <div class="col-md-3"><label class="form-label">Temporary password</label>
            <input class="form-control" type="password" name="password" required minlength="8">
        </div>
    <div class="col-md-2">
        <label class="form-label">Role</label>
        <select class="form-select" name="role">
            <option value="user">Standard User</option>
            <option value="admin">Admin</option>
        </select>
    </div>
            <div class="col-12">
                <button class="btn btn-primary">Create User</button>
            </div>
        </form>
    </div>
<div class="card p-3">
    <h2 class="h5 mb-3">Registered Users</h2>
    <div class="table-responsive"><table class="table align-middle">
        <thead>
            <tr><th>Name</th>
            <th>Username</th>
            <th>Role</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr><td>
                <?= e($u['full_name']) ?></td>
                <td><?= e($u['username']) ?></td>
                <td><span class="badge text-bg-<?= $u['role']==='admin'?'primary':'secondary' ?>"><?= e($u['role']) ?></span>
            </td>
            <td>
        <?= e($u['created_at']) ?></td>
    </tr>
    <?php endforeach; ?>
</tbody>
</table>
</div>
</div>

