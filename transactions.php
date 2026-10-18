<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
    $type = $_POST['transaction_type'] ?? '';
    $qty = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
    $note = trim($_POST['note'] ?? '');
    if (!$productId || !in_array($type, ['stock_in','stock_out'], true) || $qty === false || $qty < 1) {
        $errors[] = 'Choose a product and transaction type, and enter a quantity of at least 1.';
    } else {
        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare('SELECT id, quantity FROM products WHERE id=? FOR UPDATE');
            $stmt->execute([$productId]);
            $product = $stmt->fetch();
            if (!$product) throw new RuntimeException('Product not found.');
            $newQty = (int)$product['quantity'] + ($type === 'stock_in' ? $qty : -$qty);
            if ($newQty < 0) throw new RuntimeException('Not enough stock for this stock-out transaction.');
            $stmt = $pdo->prepare('UPDATE products SET quantity=? WHERE id=?');
            $stmt->execute([$newQty, $productId]);
            $stmt = $pdo->prepare('INSERT INTO stock_transactions (product_id,user_id,transaction_type,quantity,note) VALUES (?,?,?,?,?)');
            $stmt->execute([$productId,(int)$_SESSION['user_id'],$type,$qty,$note !== '' ? $note : null]);
            $pdo->commit();
            flash('success', 'Stock transaction saved.');
            header('Location: transactions.php'); exit;
        } catch (Throwable $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $errors[] = $ex instanceof RuntimeException ? $ex->getMessage() : 'Could not save transaction. Please check the product and database.';
        }
    }
}
$products = $pdo->query('SELECT id,sku,name,quantity FROM products ORDER BY name')->fetchAll();
$transactions = $pdo->query('SELECT t.*,p.name AS product_name,p.sku,u.full_name FROM stock_transactions t JOIN products p ON p.id=t.product_id JOIN users u ON u.id=t.user_id ORDER BY t.id DESC LIMIT 200')->fetchAll();
$page_title = 'Stock Transactions';
include __DIR__ . '/includes/header.php';
?>
<div class="mb-3">
    <h1 class="h2">Stock Transactions</h1>
    <p class="text-muted">Record stock-in and stock-out movements. Each transaction updates product quantity and creates a history record.</p>
</div>
<div class="card p-4 mb-4">
    <h2 class="h5 mb-3">Record Transaction</h2>
<?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
<?php endforeach; ?>
<?php if (!$products): ?>
    <div class="alert alert-warning">Add a product first before recording stock transactions.</div>
<?php else: ?>
<form method="post" class="row g-3">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="col-md-5">
    <label class="form-label">Product *</label>
<select class="form-select" name="product_id" required>
        <option value="">Choose product</option>
<?php foreach ($products as $p): ?>
    <option value="<?= (int)$p['id'] ?>"><?= e($p['sku'].' — '.$p['name'].' (current: '.$p['quantity'].')') ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="col-md-3">
    <label class="form-label">Type *</label>
    <select class="form-select" name="transaction_type" required>
        <option value="stock_in">Stock In</option>
        <option value="stock_out">Stock Out</option>
    </select>
</div>
<div class="col-md-2">
    <label class="form-label">Quantity *</label>
    <input class="form-control" type="number" name="quantity" min="1" required></div>
<div class="col-md-2">
    <label class="form-label">Note</label>
    <input class="form-control" name="note" maxlength="255" placeholder="Optional"></div>
<div class="col-12">
    <button class="btn btn-primary">Save Transaction</button>
</div></form>
<?php endif; ?>
</div>
<div class="card p-3">
    <h2 class="h5 mb-3">Transaction History (latest 200)</h2>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead><tr><th>Date</th>
            <th>SKU</th>
            <th>Product</th>
            <th>Type</th>
            <th>Qty</th>
            <th>Note</th>
            <th>User</th>
        </tr>
    </thead>
<tbody>
<?php if (!$transactions): ?>
    <tr><td colspan="7" class="text-center text-muted py-4">No transactions yet.</td></tr>
<?php endif; ?>
<?php foreach ($transactions as $t): ?>
    <tr><td><?= e($t['created_at']) ?></td>
    <td><?= e($t['sku']) ?></td>
    <td><?= e($t['product_name']) ?></td>
    <td><span class="badge text-bg-<?= $t['transaction_type']==='stock_in'?'success':'warning' ?>"><?= e(str_replace('_',' ',$t['transaction_type'])) ?>
</span>
</td>
<td>
    <?= e($t['quantity']) ?>
</td>
<td>
    <?= e($t['note'] ?? '') ?>
</td>
<td>
    <?= e($t['full_name']) ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

