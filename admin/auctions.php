<?php
require_once __DIR__ . '/../config.php';
if (!is_admin() && !is_moderator()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $productId = (int) ($_POST['product_id'] ?? 0);
    if ($action === 'edit_product') {
        foreach ($_SESSION['products'] as &$product) {
            if ($product['id'] === $productId) {
                $product['title'] = trim($_POST['title'] ?? $product['title']);
                $product['price'] = (int) ($_POST['price'] ?? $product['price']);
                $product['status'] = trim($_POST['status'] ?? $product['status']);
                $message = 'Açık artırma güncellendi.';
                break;
            }
        }
        unset($product);
    }
    if ($action === 'delete_product') {
        $_SESSION['products'] = array_values(array_filter($_SESSION['products'], fn($p) => $p['id'] !== $productId));
        $message = 'Açık artırma silindi.';
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Açık Artırma Yönetimi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>" />
</head>
<body>
<aside>
    <div class="brand"><span>A</span>Artirup Admin</div>
    <div class="menu">
        <a href="<?php echo url_path('admin.php'); ?>">Genel Bakış</a>
        <a class="active" href="<?php echo url_path('admin/auctions.php'); ?>">Açık Artırmalar</a>
        <a href="<?php echo url_path('admin/users.php'); ?>">Kullanıcılar</a>
        <a href="<?php echo url_path('admin/moderators.php'); ?>">Moderatörler</a>
        <a href="#">Raporlar</a>
        <a href="<?php echo url_path('admin/settings.php'); ?>">Ayarlar</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Açık Artırma Yönetimi</h2>
            <p style="color: var(--muted); margin: 0;">Tüm müzayedeleri incele, düzenle veya yayından kaldır.</p>
        </div>
        <input type="search" placeholder="Açık artırma ara..." />
        <a class="btn btn-primary" href="<?php echo url_path('pages/auctions.php'); ?>">Yeni açık artırma oluştur</a>
    </div>

    <?php if ($message): ?>
        <div class="panel" style="background: #e4f9ef; color: #1f9d62;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="panel" style="margin-top: 16px;">
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Satıcı</th>
                    <th>Lot</th>
                    <th>Durum</th>
                    <th>Bitiş</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['products'] as $auction): ?>
                    <tr>
                        <td><?php echo $auction['title']; ?></td>
                        <td><?php echo $auction['seller']; ?></td>
                        <td><?php echo $auction['lots']; ?></td>
                        <td>
                            <span class="status <?php echo $auction['status'] === 'Yayında' ? 'ok' : ($auction['status'] === 'Onay Bekliyor' ? 'wait' : 'no'); ?>">
                                <?php echo $auction['status']; ?>
                            </span>
                        </td>
                        <td><?php echo $auction['end']; ?></td>
                        <td>
                            <form class="actions" method="post">
                                <input type="hidden" name="product_id" value="<?php echo $auction['id']; ?>" />
                                <input type="text" name="title" value="<?php echo htmlspecialchars($auction['title']); ?>" />
                                <input type="number" name="price" value="<?php echo (int) $auction['price']; ?>" />
                                <select name="status">
                                    <option <?php echo $auction['status'] === 'Yayında' ? 'selected' : ''; ?>>Yayında</option>
                                    <option <?php echo $auction['status'] === 'Onay Bekliyor' ? 'selected' : ''; ?>>Onay Bekliyor</option>
                                    <option <?php echo $auction['status'] === 'Durduruldu' ? 'selected' : ''; ?>>Durduruldu</option>
                                </select>
                                <button class="btn btn-outline" type="submit" name="action" value="edit_product">Düzenle</button>
                                <button class="btn btn-danger" type="submit" name="action" value="delete_product">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
