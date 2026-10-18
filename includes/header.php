<?php
$page_title = $page_title ?? 'Inventory Management System';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($page_title) ?> | IMS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">IMS | Inventory</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
    <div class="collapse navbar-collapse" id="mainNav">
      <?php if (!empty($_SESSION['user_id'])): ?>
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="transactions.php">Stock Transactions</a></li>
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?><li class="nav-item"><a class="nav-link" href="users.php">Users</a></li><?php endif; ?>
      </ul>
      <span class="navbar-text me-lg-3"><?= e($_SESSION['full_name'] ?? 'User') ?> (<?= e($_SESSION['role'] ?? 'user') ?>)</span>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
      <?php endif; ?>
    </div>
  </div>
</nav>
<main class="container py-4">
<?php show_flash(); ?>
