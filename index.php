<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
    $productsCount = (int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $totalUnits = (int)$pdo->query('SELECT COALESCE(SUM(quantity),0) FROM products')->fetchColumn();
    $lowStock = (int)$pdo->query('SELECT COUNT(*) FROM products WHERE quantity <= reorder_level')->fetchColumn();
    $inventoryValue = (float)$pdo->query('SELECT COALESCE(SUM(quantity * unit_price),0) FROM products')->fetchColumn();
    $recent = $pdo->query('SELECT t.*, p.name AS product_name, u.full_name FROM stock_transactions t JOIN products p ON p.id=t.product_id JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 8')->fetchAll();
    $page_title = 'Dashboard';

include __DIR__ . '/includes/header.php';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4"><div>
    <h1 class="h2 mb-1">Dashboard</h1>
    <p class="text-muted mb-0">Inventory overview and recent activity.</p>
</div>
    <a class="btn btn-primary" href="product_form.php">+ Add Product</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ([['Products',$productsCount,'bi-box'],['Units in Stock',$totalUnits,'bi-stack'],['Low Stock Items',$lowStock,'bi-exclamation-triangle'],['Inventory Value','₱'.number_format($inventoryValue,2),'bi-cash']] as $stat): ?>
<div class="col-6 col-xl-3">
    <div class="card p-3 h-100">
        <div class="text-muted"><?= e($stat[0]) ?></div>
        <div class="stat-number"><?= e($stat[1]) ?></div>
    </div>
</div>
<?php endforeach; ?>
</div>
<div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h5 mb-0">Recent Stock Transactions</h2><a href="transactions.php">View all</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr><th>Date</th>
                <th>Product</th>
                <th>Type</th>
                <th>Quantity</th>
                <th>By</th></tr>
                </thead>
<tbody>
<?php if (!$recent): ?>
    <tr>
    <td colspan="5" class="text-center text-muted py-4">No stock transactions yet.</td>
</tr><?php endif; ?>
<?php foreach ($recent as $r): ?>
    <tr><td><?= e($r['created_at']) ?></td>
    <td><?= e($r['product_name']) ?></td>
    <td><span class="badge text-bg-<?= $r['transaction_type']==='stock_in'?'success':'warning' ?>"><?= e(str_replace('_',' ',$r['transaction_type'])) ?></span>
    </td>
    <td><?= e($r['quantity']) ?></td>
    <td><?= e($r['full_name']) ?></td>
    </tr><?php endforeach; ?>
</tbody>
</table>
</div>
</div>

