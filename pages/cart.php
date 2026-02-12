<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_qty') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty = max(1, (int) ($_POST['qty'] ?? 1));
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['qty'] = $qty;
            $message = 'Sepet güncellendi.';
        }
    }
    if ($action === 'remove_item') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        unset($_SESSION['cart'][$productId]);
        $message = 'Ürün sepetten kaldırıldı.';
    }
    if ($action === 'clear_cart') {
        $_SESSION['cart'] = [];
        $message = 'Sepet temizlendi.';
    }
}

$cartItems = cart_items();
$total = cart_total();
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Sepet</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
</head>
<body>
<header>
    <div class="nav">
        <?php echo render_site_logo(); ?>
        <div class="nav-search">
            <form method="get" action="<?php echo url_path('pages/auctions.php'); ?>">
                <input class="nav-search-input" type="search" name="q" placeholder="Ürün, satıcı veya kategori ara..." />
            </form>
        </div>
        <nav>
            <ul>
                <li><a href="<?php echo url_path('index.php'); ?>">Anasayfa</a></li>
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
                <li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
            <a class="btn btn-outline" href="<?php echo url_path('pages/cart.php'); ?>">Sepet (<?php echo cart_count(); ?>)</a>
            <?php if ($currentUser): ?>
                <div class="profile-menu">
                    <div class="profile-trigger">
                        <img class="profile-avatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Profil" />
                        <span class="profile-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Profilim'); ?></span>
                    </div>
                    <div class="profile-dropdown">
                        <a href="<?php echo url_path('profile.php'); ?>">Profilim</a>
                        <a href="<?php echo $dashboardLink; ?>"><?php echo $dashboardLabel; ?></a>
                        <a href="<?php echo url_path('auth/logout.php'); ?>">Çıkış Yap</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn btn-outline" href="<?php echo url_path('auth/login.php'); ?>">Giriş Yap</a>
                <a class="btn btn-primary" href="<?php echo url_path('auth/register.php'); ?>">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="container">
    <div class="card">
        <h1>Sepetim</h1>
        <?php if ($message): ?>
            <div class="card" style="background: #e4f9ef; color: #1f9d62; margin-bottom: 16px;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>
            <p>Sepetiniz boş. Açık artırmalardan ürün ekleyebilirsiniz.</p>
            <a class="btn btn-primary" href="<?php echo url_path('pages/auctions.php'); ?>">Ürünlere git</a>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Ürün</th>
                        <th>Birim Fiyat</th>
                        <th>Adet</th>
                        <th>Ara Toplam</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <?php $product = $item['product']; ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['title']); ?></td>
                            <td>₺<?php echo number_format((int) $product['price']); ?></td>
                            <td>
                                <form class="form" method="post">
                                    <input type="hidden" name="action" value="update_qty" />
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>" />
                                    <input type="number" name="qty" min="1" value="<?php echo (int) $item['qty']; ?>" />
                                    <button class="btn btn-outline" type="submit">Güncelle</button>
                                </form>
                            </td>
                            <td>₺<?php echo number_format((int) $item['subtotal']); ?></td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="action" value="remove_item" />
                                    <input type="hidden" name="product_id" value="<?php echo (int) $product['id']; ?>" />
                                    <button class="btn btn-danger" type="submit">Kaldır</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="card" style="margin-top: 16px;">
                <h3>Toplam: ₺<?php echo number_format($total); ?></h3>
                <div class="actions">
                    <form method="post">
                        <input type="hidden" name="action" value="clear_cart" />
                        <button class="btn btn-outline" type="submit">Sepeti Temizle</button>
                    </form>
                    <a class="btn btn-primary" href="<?php echo url_path('pages/checkout.php'); ?>">Ödemeye Geç</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
