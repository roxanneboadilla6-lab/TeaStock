<?php
require_once __DIR__ . '/includes/auth.php';
require_login();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: 0;
$product = ['sku'=>'','name'=>'','category'=>'General','description'=>'','quantity'=>0,'reorder_level'=>5,'unit_price'=>'0.00'];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) { http_response_code(404); exit('Product not found.'); }
    $product = $found;
}
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $sku = trim($_POST['sku'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'General');
    $description = trim($_POST['description'] ?? '');
    $reorder = filter_var($_POST['reorder_level'] ?? null, FILTER_VALIDATE_INT);
    $price = filter_var($_POST['unit_price'] ?? null, FILTER_VALIDATE_FLOAT);
    if ($sku === '' || strlen($sku)>50) $errors[] = 'SKU is required and must be at most 50 characters.';
    if ($name === '' || strlen($name)>150) $errors[] = 'Product name is required and must be at most 150 characters.';
    if ($category === '' || strlen($category)>100) $errors[] = 'Enter a category up to 100 characters.';
    if ($reorder === false || $reorder < 0) $errors[] = 'Reorder level must be zero or greater.';
    if ($price === false || $price < 0) $errors[] = 'Unit price must be zero or greater.';
    $product = array_merge($product, ['sku'=>$sku,'name'=>$name,'category'=>$category,'description'=>$description,'reorder_level'=>$reorder===false?0:$reorder,'unit_price'=>$price===false?0:$price]);
    if (!$errors) {
        try {
            if ($id) {
                $stmt = $pdo->prepare('UPDATE products SET sku=?, name=?, category=?, description=?, reorder_level=?, unit_price=? WHERE id=?');
                $stmt->execute([$sku,$name,$category,$description,$reorder,$price,$id]);
                flash('success', 'Product updated. Use Stock Transactions to change stock quantity.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO products (sku,name,category,description,quantity,reorder_level,unit_price) VALUES (?,?,?,?,0,?,?)');
                $stmt->execute([$sku,$name,$category,$description,$reorder,$price]);
                flash('success', 'Product added. Use Stock Transactions to add starting stock.');
            }
            header('Location: products.php'); exit;
        } catch (PDOException $ex) { $errors[] = 'Unable to save. SKU may already be in use.'; }
    }
}
$page_title = $id ? 'Edit Product' : 'Add Product';
include __DIR__ . '/includes/header.php';
?>
<div class="mb-3">
    <h1 class="h2"><?= $id?'Edit Product':'Add Product' ?></h1>
    <p class="text-muted">Product quantity is changed through the stock transaction page to keep an audit history.</p>
</div>
<div class="card p-4"><?php foreach ($errors as $err): ?>
    <div class="alert alert-danger"><?= e($err) ?></div>
    <?php endforeach; ?>
        <form method="post" class="row g-3" id="productForm"><input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <div class="col-md-4"><label class="form-label">SKU *</label>
    <input class="form-control" name="sku" maxlength="50" required value="<?= e($product['sku']) ?>">
</div>
<div class="col-md-8">
    <label class="form-label">Product name *</label>
    <input class="form-control" name="name" maxlength="150" required value="<?= e($product['name']) ?>">
</div>
<div class="col-md-6">
    <label class="form-label">Category *</label>
    <input class="form-control" name="category" maxlength="100" required value="<?= e($product['category']) ?>">
</div>
<div class="col-md-3">
    <label class="form-label">Reorder level *</label>
    <input class="form-control" type="number" name="reorder_level" min="0" required value="<?= e($product['reorder_level']) ?>">
</div>
<div class="col-md-3">
    <label class="form-label">Unit price (₱) *</label>
    <input class="form-control" type="number" name="unit_price" min="0" step="0.01" required value="<?= e($product['unit_price']) ?>">
</div>
<div class="col-12">
    <label class="form-label">Description</label>
    <textarea class="form-control" name="description" rows="3"><?= e($product['description'] ?? '') ?></textarea>
</div>
<div class="col-12">
    <button class="btn btn-primary">Save Product</button> 
    <a class="btn btn-outline-secondary" href="products.php">Cancel</a>
</div>
</form>
</div>

