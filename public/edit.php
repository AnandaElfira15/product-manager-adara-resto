<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
$stmt->execute(['id' => $id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: index.php");
    exit;
}

$errors = [];
$name = $product['name'];
$category = $product['category'];
$price = $product['price'];
$stock = $product['stock'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? 'Makanan');
    $price_input = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $stock_input = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);

    if (mb_strlen($name) < 3) $errors['name'] = "Nama menu minimal 3 karakter.";
    if ($price_input === false || $price_input <= 0) $errors['price'] = "Harga harus lebih dari 0.";
    if ($stock_input === false || $stock_input < 0) $errors['stock'] = "Stok tidak boleh negatif.";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("UPDATE products SET name = :name, category = :category, price = :price, stock = :stock WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'category' => $category,
                'price' => $price_input,
                'stock' => $stock_input,
                'id' => $id
            ]);
            header("Location: index.php?status=updated");
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $errors['name'] = "Nama menu sudah digunakan di Adara Resto.";
            } else {
                $errors['system'] = "Gagal memperbarui menu.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu - Adara Resto</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="container">
            <h1 class="brand-title"><i class="fa-solid fa-pen-to-square"></i> Edit Menu</h1>
            <div class="brand-subtitle">Adara Resto Management</div>
        </div>
    </div>
    <div class="container">
        <div class="form-card">
            <?php if (!empty($errors['system'])): ?>
                <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($errors['system']) ?></div>
            <?php endif; ?>
            <form action="edit.php?id=<?= $id ?>" method="POST">
                <div class="form-group">
                    <label for="name">Nama Menu</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required minlength="3">
                    <?php if (isset($errors['name'])): ?><small style="color: var(--danger);"><?= $errors['name'] ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="category">Kategori</label>
                    <select id="category" name="category" class="form-control">
                        <option value="Makanan" <?= $category === 'Makanan' ? 'selected' : '' ?>>Makanan</option>
                        <option value="Minuman" <?= $category === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                        <option value="Pastry" <?= $category === 'Pastry' ? 'selected' : '' ?>>Pastry</option>
                        <option value="Dessert" <?= $category === 'Dessert' ? 'selected' : '' ?>>Dessert</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="price">Harga (Rp)</label>
                    <input type="number" id="price" name="price" step="any" class="form-control" value="<?= htmlspecialchars((string)$price) ?>" required min="1">
                    <?php if (isset($errors['price'])): ?><small style="color: var(--danger);"><?= $errors['price'] ?></small><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="stock">Stok Porsi</label>
                    <input type="number" id="stock" name="stock" class="form-control" value="<?= htmlspecialchars((string)$stock) ?>" required min="0">
                    <?php if (isset($errors['stock'])): ?><small style="color: var(--danger);"><?= $errors['stock'] ?></small><?php endif; ?>
                </div>
                <div style="display: flex; gap: 10px; margin-top: 28px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;"><i class="fa-solid fa-floppy-disk"></i> Update Menu</button>
                    <a href="index.php" class="btn btn-danger"><i class="fa-solid fa-xmark"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>