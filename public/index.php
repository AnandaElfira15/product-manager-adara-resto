<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$q = trim($_GET['q'] ?? '');

if ($q !== '') {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE name LIKE :q1 OR category LIKE :q2 ORDER BY id DESC");
    $stmt->execute([
        'q1' => "%$q%",
        'q2' => "%$q%"
    ]);
} else {
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
}
$products = $stmt->fetchAll();

// Statistik Ringkas
$total_products = count($products);
$total_stock = array_sum(array_column($products, 'stock'));
$low_stock_count = count(array_filter($products, fn($p) => $p['stock'] <= 5));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adara Resto - Management Menu</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header">
        <div class="container header-content">
            <div class="brand">
                <i class="fa-solid fa-utensils brand-icon"></i>
                <div>
                    <h1 class="brand-title">Adara Resto</h1>
                    <div class="brand-subtitle">System Management & Culinary Menu</div>
                </div>
            </div>
            <a href="create.php" class="btn btn-light"><i class="fa-solid fa-plus"></i> Tambah Menu</a>
        </div>
    </div>

    <div class="container">
        <!-- Dashboard Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-title">Total Menu</div>
                    <div class="stat-value"><?= $total_products ?></div>
                </div>
                <div class="stat-card-icon"><i class="fa-solid fa-burger"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-title">Total Stok Porsi</div>
                    <div class="stat-value"><?= $total_stock ?></div>
                </div>
                <div class="stat-card-icon"><i class="fa-solid fa-cubes"></i></div>
            </div>
            <div class="stat-card">
                <div class="stat-info">
                    <div class="stat-title">Stok Menipis (&lt;=5)</div>
                    <div class="stat-value" style="color: <?= $low_stock_count > 0 ? 'var(--danger)' : 'var(--primary)' ?>;">
                        <?= $low_stock_count ?>
                    </div>
                </div>
                <div class="stat-card-icon" style="color: var(--danger); background: #fee2e2;"><i class="fa-solid fa-triangle-exclamation"></i></div>
            </div>
        </div>

        <?php if (isset($_GET['status'])): ?>
            <?php if ($_GET['status'] === 'created'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Menu baru berhasil ditambahkan!</div>
            <?php elseif ($_GET['status'] === 'updated'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Data menu berhasil diperbarui!</div>
            <?php elseif ($_GET['status'] === 'deleted'): ?>
                <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> Menu berhasil dihapus!</div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="search-bar">
            <form method="GET" action="index.php" class="search-form">
                <div class="input-icon-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="q" class="form-control" placeholder="Cari menu atau kategori (contoh: Kopi, Makanan)..." value="<?= htmlspecialchars($q, ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i> Cari</button>
                <?php if ($q !== ''): ?>
                    <a href="index.php" class="btn btn-danger"><i class="fa-solid fa-rotate-left"></i> Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if (empty($products)): ?>
            <div style="text-align: center; color: var(--text-muted); padding: 50px 0;">
                <i class="fa-solid fa-box-open" style="font-size: 3rem; margin-bottom: 12px; opacity: 0.5;"></i>
                <p>Tidak ada data menu ditemukan.</p>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $p): ?>
                    <div class="card">
                        <div>
                            <div class="card-header-badge">
                                <span class="card-badge"><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($p['category'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php if ($p['stock'] <= 5): ?>
                                    <span class="stock-badge warning"><i class="fa-solid fa-triangle-exclamation"></i> Stok Menipis</span>
                                <?php else: ?>
                                    <span class="stock-badge safe"><i class="fa-solid fa-circle-check"></i> Tersedia</span>
                                <?php endif; ?>
                            </div>
                            <h3><?= htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <div class="card-price">Rp <?= number_format($p['price'], 0, ',', '.') ?></div>
                            <div class="card-stock"><i class="fa-solid fa-boxes-stacked"></i> Tersedia: <?= (int)$p['stock'] ?> porsi</div>
                        </div>
                        <div class="card-actions">
                            <a href="edit.php?id=<?= $p['id'] ?>" class="btn btn-primary" style="flex: 1;"><i class="fa-solid fa-pen-to-square"></i> Edit</a>
                            <form action="delete.php" method="POST" style="flex: 1;" onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu ini?');">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <input type="hidden" name="csrf" value="<?= $_SESSION['csrf'] ?>">
                                <button type="submit" class="btn btn-danger" style="width: 100%;"><i class="fa-solid fa-trash"></i> Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>