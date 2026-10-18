<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    verify_csrf();
    if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); exit('Only admins can delete products.'); }
    $id = filter_input(INPUT_POST, 'delete_id', FILTER_VALIDATE_INT);
    if ($id) {
        try {
            $stmt = $pdo->prepare('DELETE FROM products WHERE id=?');
            $stmt->execute([$id]);
            flash('success', 'Product deleted.');
        } catch (PDOException $ex) { flash('danger', 'Cannot delete this product because it has stock transaction history.'); }
    }
    header('Location: products.php'); exit;
}
$products = $pdo->query('SELECT * FROM products ORDER BY name')->fetchAll();
$page_title = 'Products';
include __DIR__ . '/includes/header.php';
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h2 mb-1">Products</h1>
        <p class="text-muted mb-0">Create, view, edit, and manage inventory items.</p>
    </div>
    <a class="btn btn-primary" href="product_form.php">+ Add Product</a>
</div>
<div class="card p-3">
    <div class="mb-3">
        <input class="form-control" placeholder="Search products..." data-filter-input="#productsTable" aria-label="Search products">
    </div>
<div class="table-responsive">
    <table class="table table-hover align-middle" id="productsTable">
        <thead>
            <tr><th>SKU</th>
            <th>Product</th>
            <th>Category</th>
            <th>Qty</th>
            <th>Reorder level</th>
            <th>Unit price</th>
            <th>Actions</th>
        </tr>
    </thead>
<tbody>
<?php if (!$products): ?>
    <tr><td colspan="7" class="text-center text-muted py-4">No products found. Add your first product.</td></tr>
    <?php endif; ?>
<?php foreach ($products as $p): ?><tr><td><?= e($p['sku']) ?></td>
    <td><strong><?= e($p['name']) ?></strong>
    <div class="small text-muted"><?= e($p['description'] ?? '') ?></div>
        </td><td><?= e($p['category']) ?></td>
        <td><span class="badge text-bg-<?= (int)$p['quantity'] <= (int)$p['reorder_level']?'danger':'success' ?>"><?= e($p['quantity']) ?></span>
    </td>
    <td>
        <?= e($p['reorder_level']) ?></td>
    <td>
        ₱<?= number_format((float)$p['unit_price'],2) ?></td>
    <td class="text-nowrap">
        <a class="btn btn-sm btn-outline-primary" href="product_form.php?id=<?= (int)$p['id'] ?>">Edit</a> 
        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
            <form class="d-inline" method="post" data-confirm="Delete this product? Products with transaction history cannot be deleted.">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="delete_id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn-sm btn-outline-danger">Delete</button>
</form>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>
</div>

