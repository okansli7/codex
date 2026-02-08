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
    <title>Artirup | Satıcı Paneli</title>
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
                <li><a href="<?php echo url_path('pages/stores.php'); ?>">Mağazalar</a></li>
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
        <h1>Satıcı Paneli</h1>
        <p>Lot oluşturma, fiyatlandırma önerileri, satış raporları ve en fazla teklif veren kullanıcılar burada.</p>
        <div class="grid">
            <div class="card">
                <h3>Toplam satış</h3>
                <p>₺128.400 • 32 lot satıldı</p>
            </div>
            <div class="card">
                <h3>Popüler açık artırma</h3>
                <p>Retro Teknoloji Lotları • 58 teklif</p>
            </div>
            <div class="card">
                <h3>En fazla teklif veren</h3>
                <p>Elif Demir • 18 teklif</p>
            </div>
        </div>
        <div class="grid" style="margin-top: 20px;">
            <div class="card">
                <h3>Yeni lot oluştur</h3>
                <p>Ürün bilgilerini ekle, otomatik fiyat önerisi al.</p>
                <a class="btn btn-outline" href="#">Lot oluştur</a>
            </div>
            <div class="card">
                <h3>Aktif müzayedeler</h3>
                <p>Devam eden açık artırmalarını izle.</p>
                <a class="btn btn-outline" href="#">Müzayedeleri gör</a>
            </div>
            <div class="card">
                <h3>Raporlar</h3>
                <p>Satış performansı ve teklif geçmişi raporları.</p>
                <a class="btn btn-outline" href="#">Rapor indir</a>
            </div>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Satıcı operasyon merkezi.
</footer>
</body>
</html>
