<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
$message = '';
$error = '';
$methods = enabled_payment_methods();
$cartItems = cart_items();
$total = cart_total();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($cartItems)) {
        $error = 'Ödeme için sepette ürün olmalı.';
    } else {
        $selectedMethod = trim($_POST['payment_method'] ?? '');
        if (!isset($methods[$selectedMethod])) {
            $error = 'Geçerli bir ödeme yöntemi seçiniz.';
        } else {
            $_SESSION['last_order'] = [
                'order_no' => 'ORD-' . date('YmdHis'),
                'total' => $total,
                'method' => $methods[$selectedMethod]['label'] ?? $selectedMethod,
                'items' => $cartItems,
            ];
            $_SESSION['cart'] = [];
            $message = 'Ödeme başarıyla tamamlandı. Siparişiniz alınmıştır.';
            $cartItems = [];
            $total = 0;
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Ödeme</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
</head>
<body>
<header>
    <div class="nav">
        <?php echo render_site_logo(); ?>
        <nav>
            <ul>
                <li><a href="<?php echo url_path('index.php'); ?>">Anasayfa</a></li>
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
                <li><a href="<?php echo url_path('pages/cart.php'); ?>">Sepet (<?php echo cart_count(); ?>)</a></li>
                <li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
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
        <h1>Ödeme</h1>
        <?php if ($message): ?>
            <div class="card" style="background: #e4f9ef; color: #1f9d62; margin-bottom: 16px;"><?php echo htmlspecialchars($message); ?></div>
            <?php if (!empty($_SESSION['last_order'])): ?>
                <p><strong>Sipariş No:</strong> <?php echo htmlspecialchars($_SESSION['last_order']['order_no']); ?></p>
                <p><strong>Ödeme Yöntemi:</strong> <?php echo htmlspecialchars($_SESSION['last_order']['method']); ?></p>
            <?php endif; ?>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="card" style="background: #ffe1e6; color: #b3283b; margin-bottom: 16px;"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($cartItems)): ?>
            <table>
                <thead>
                <tr><th>Ürün</th><th>Adet</th><th>Ara Toplam</th></tr>
                </thead>
                <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product']['title']); ?></td>
                        <td><?php echo (int) $item['qty']; ?></td>
                        <td>₺<?php echo number_format((int) $item['subtotal']); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <h3 style="margin-top: 16px;">Genel Toplam: ₺<?php echo number_format($total); ?></h3>

            <form class="form" method="post">
                <h3>Ödeme Yöntemi</h3>
                <?php if (!empty($methods)): ?>
                    <?php foreach ($methods as $key => $method): ?>
                        <label style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                            <input type="radio" name="payment_method" value="<?php echo htmlspecialchars($key); ?>" required />
                            <span><?php echo htmlspecialchars($method['label'] ?? $key); ?></span>
                        </label>
                    <?php endforeach; ?>
                    <?php if (isset($methods['bank_transfer'])): ?>
                        <p style="color: #5a627a;">Havale/EFT için IBAN: <strong><?php echo htmlspecialchars($_SESSION['settings']['bank_transfer_iban'] ?? '-'); ?></strong></p>
                    <?php endif; ?>
                    <button class="btn btn-primary" type="submit">Ödemeyi Tamamla</button>
                <?php else: ?>
                    <p style="color: #b3283b;">Şu anda aktif ödeme yöntemi bulunmuyor. Lütfen admin panelinden ödeme yöntemlerini aktif edin.</p>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p>Sepet boş. Önce ürün ekleyin.</p>
            <a class="btn btn-primary" href="<?php echo url_path('pages/auctions.php'); ?>">Açık artırmalara dön</a>
        <?php endif; ?>
    </div>
</section>
</body>
</html>
