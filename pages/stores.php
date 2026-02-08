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
    <title>Artirup | Mağazalar</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
</head>
<body>
<header>
    <div class="nav">
        <div class="logo"><span class="logo-badge">A</span>Artirup</div>
        <nav>
            <ul>
                <li><a href="<?php echo url_path('index.php'); ?>">Anasayfa</a></li>
                <li><a href="<?php echo url_path('pages/about.php'); ?>">Hakkımızda</a></li>
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
                <li><a href="<?php echo url_path('pages/blog.php'); ?>">Blog</a></li>
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
        <h1>Mağazalar</h1>
        <p>Onaylı satıcı mağazalarını incele, güvenli alışveriş yap.</p>
        <div class="grid">
            <div class="card">
                <h3>Nova Tech</h3>
                <p>Teknoloji & koleksiyon ürünleri.</p>
            </div>
            <div class="card">
                <h3>Retro Hub</h3>
                <p>Vintage ürünler ve nostaljik lotlar.</p>
            </div>
            <div class="card">
                <h3>Studio 55</h3>
                <p>Sanat ve tasarım koleksiyonları.</p>
            </div>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Onaylı mağazalar listesi.
</footer>
</body>
</html>
