<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Hakkımızda</title>
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
                <li><a href="<?php echo url_path('pages/stores.php'); ?>">Mağazalar</a></li>
                <li><a href="<?php echo url_path('pages/blog.php'); ?>">Blog</a></li>
                <li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
            <a class="cart-icon-btn" href="<?php echo url_path('pages/cart.php'); ?>" aria-label="Sepet">🛒<span class="cart-count"><?php echo cart_count() > 0 ? cart_count() : '•'; ?></span></a>
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
        <h1>Artirup Hakkında</h1>
        <p>Artirup, güvenli ödeme altyapısı ve gerçek zamanlı açık artırma deneyimi ile koleksiyonerleri ve satıcıları bir araya getirir.</p>
        <div class="grid">
            <div class="card">
                <h3>Vizyonumuz</h3>
                <p>Türkiye'nin en güvenilir açık artırma platformu olmak.</p>
            </div>
            <div class="card">
                <h3>Misyonumuz</h3>
                <p>Her lot için şeffaf, hızlı ve adil bir teklif deneyimi sunmak.</p>
            </div>
            <div class="card">
                <h3>Ekibimiz</h3>
                <p>Ürün, güvenlik ve ödeme ekipleriyle 7/24 destek veriyoruz.</p>
            </div>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Açık artırmada güvenin adresi.
</footer>
</body>
</html>
