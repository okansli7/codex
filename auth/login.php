<?php
require_once __DIR__ . '/../config.php';
$errorMessage = '';
$successMessage = '';
$redirectTo = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $user = null;

    if ($email === $adminAccount['email'] && $password === $adminAccount['password']) {
        $user = $adminAccount;
    } elseif ($email === $moderatorAccount['email'] && $password === $moderatorAccount['password']) {
        $user = $moderatorAccount;
    } elseif (!empty($_SESSION['user']) && $email === ($_SESSION['user']['email'] ?? '') && $password !== '') {
        $user = $_SESSION['user'];
    }

    if ($user) {
        $_SESSION['user'] = $user;
        $successMessage = 'Başarılı giriş yapılıyor. Profiline yönlendiriliyorsun...';
        $redirectTo = url_path('profile.php');
    } else {
        $errorMessage = 'Giriş bilgileri hatalı. Lütfen tekrar dene.';
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Giriş Yap</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
    <style>
        .auth-layout {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 28px;
            align-items: stretch;
        }

        .eye-panel {
            background: linear-gradient(140deg, #fff1f5, #f7f8ff);
            border-radius: 24px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow);
            min-height: 420px;
        }

        .eye-panel h2 {
            margin-top: 0;
        }

        .eye-stage {
            margin-top: 28px;
            display: grid;
            place-items: center;
            height: 260px;
        }

        .eye {
            width: 180px;
            height: 120px;
            border-radius: 100px;
            background: #fff;
            border: 4px solid #1f2335;
            display: grid;
            place-items: center;
            position: relative;
        }

        .pupil {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1f2335;
            position: absolute;
            transition: transform 0.2s ease;
        }

        .eye.look-away .pupil {
            transform: translate(45px, -20px);
        }

        .eye-panel .note {
            color: var(--text);
        }

        .auth-card {
            max-width: 520px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<div class="top-strip">
    <div class="top-strip-left">
        <span><strong>TR</strong> • Canlı Destek</span>
        <span>Bizi ara: <strong>+90 850 840 00 00</strong></span>
        <span>E-posta: <a href="mailto:destek@artirup.com">destek@artirup.com</a></span>
    </div>
    <div class="top-strip-right">
        <span>🚚 Sipariş Takibi</span>
    </div>
</div>
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
                <li><a href="<?php echo url_path('pages/about.php'); ?>">Hakkımızda</a></li>
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
                <li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
            <a class="cart-icon-btn" href="<?php echo url_path('pages/cart.php'); ?>" aria-label="Sepet">🛒<span class="cart-count"><?php echo cart_count() > 0 ? cart_count() : '•'; ?></span></a>
            <?php if (current_user()): ?>
                <a class="btn btn-primary" href="<?php echo url_path('profile.php'); ?>">Profilim</a>
            <?php else: ?>
                <a class="btn btn-primary" href="<?php echo url_path('auth/register.php'); ?>">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="container">
    <?php if ($successMessage): ?>
        <div class="card" style="max-width: 520px; margin: 0 auto 24px;">
            <h2><?php echo $successMessage; ?></h2>
            <p>Otomatik yönlendirme başlamazsa <a href="<?php echo $redirectTo; ?>">buraya tıkla</a>.</p>
        </div>
        <meta http-equiv="refresh" content="3;url=<?php echo $redirectTo; ?>">
    <?php endif; ?>
    <div class="auth-layout">
        <div class="eye-panel">
            <h2>Açık artırma evine hoş geldin!</h2>
            <p class="note">Göz seni takip ediyor. Şifre yazarken başka tarafa bakıp “görmedim” diyecek.</p>
            <div class="eye-stage">
                <div class="eye" id="auth-eye">
                    <div class="pupil" id="auth-pupil"></div>
                </div>
            </div>
            <p class="note">Canlı teklifleri kaçırmamak için giriş yap.</p>
        </div>
        <div class="card auth-card">
        <h1>Giriş Yap</h1>
        <p>Hesabına giriş yaparak canlı artırmaları takip et.</p>
        <?php if ($errorMessage): ?>
            <div class="card" style="background: #ffe1e6; color: #b3283b; margin-bottom: 16px;">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <form class="form" method="post">
            <input type="email" name="email" placeholder="E-posta" required />
            <input type="password" name="password" placeholder="Şifre" id="password-input" required />
            <button class="btn btn-primary" type="submit">Giriş Yap</button>
            <a class="btn btn-outline" href="<?php echo url_path('auth/register.php'); ?>">Hesabın yok mu? Kayıt ol</a>
        </form>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Güvenli giriş altyapısı.
</footer>
<script>
    const eye = document.getElementById('auth-eye');
    const pupil = document.getElementById('auth-pupil');
    const passwordInput = document.getElementById('password-input');

    document.addEventListener('mousemove', (event) => {
        const rect = eye.getBoundingClientRect();
        const eyeCenterX = rect.left + rect.width / 2;
        const eyeCenterY = rect.top + rect.height / 2;
        const angleX = (event.clientX - eyeCenterX) / 40;
        const angleY = (event.clientY - eyeCenterY) / 40;
        pupil.style.transform = `translate(${Math.max(Math.min(angleX, 20), -20)}px, ${Math.max(Math.min(angleY, 14), -14)}px)`;
    });

    passwordInput.addEventListener('focus', () => {
        eye.classList.add('look-away');
    });

    passwordInput.addEventListener('blur', () => {
        eye.classList.remove('look-away');
    });
</script>
</body>
</html>
