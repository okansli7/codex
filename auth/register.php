<?php
require_once __DIR__ . '/../config.php';
$successMessage = '';
$redirectTo = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $sellerIntent = !empty($_POST['seller_intent']);
    $sellerAgreement = !empty($_POST['seller_agreement']);

    if ($sellerIntent && !$sellerAgreement) {
        $errorMessage = 'Satıcı olarak kayıt olmak için satıcı sözleşmesini kabul etmelisin.';
    }

    if ($errorMessage === '') {
        $_SESSION['user'] = [
            'name' => $name !== '' ? $name : 'Yeni Üye',
            'email' => $email !== '' ? $email : 'user@artirup.com',
            'phone' => $phone,
            'password' => $password,
            'role' => $sellerIntent ? 'Satıcı' : 'Kullanıcı',
            'seller_intent' => $sellerIntent,
            'seller_agreement' => $sellerIntent ? $sellerAgreement : false,
            'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=facearea&w=160&h=160&q=80',
            'purchases' => 0,
            'vip' => false,
        ];

        $successMessage = 'Başarılı giriş yapılıyor. Profiline yönlendiriliyorsun...';
        $redirectTo = url_path('profile.php');
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Kayıt Ol</title>
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
            min-height: 480px;
        }

        .eye-stage {
            margin: 28px 0;
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

        .auth-card {
            max-width: 620px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <?php echo render_site_logo(); ?>
        <nav>
            <ul>
                <li><a href="<?php echo url_path('index.php'); ?>">Anasayfa</a></li>
                <li><a href="<?php echo url_path('pages/about.php'); ?>">Hakkımızda</a></li>
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
                <li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
            <?php if (current_user()): ?>
                <a class="btn btn-primary" href="<?php echo url_path('profile.php'); ?>">Profilim</a>
            <?php else: ?>
                <a class="btn btn-outline" href="<?php echo url_path('auth/login.php'); ?>">Giriş Yap</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="container">
    <?php if ($errorMessage): ?>
        <div class="card" style="max-width: 620px; margin: 0 auto 24px; background:#ffe1e6; color:#b3283b;">
            <?php echo htmlspecialchars($errorMessage); ?>
        </div>
    <?php endif; ?>
    <?php if ($successMessage): ?>
        <div class="card" style="max-width: 620px; margin: 0 auto 24px;">
            <h2><?php echo $successMessage; ?></h2>
            <p>Otomatik yönlendirme başlamazsa <a href="<?php echo $redirectTo; ?>">buraya tıkla</a>.</p>
        </div>
        <meta http-equiv="refresh" content="3;url=<?php echo $redirectTo; ?>">
    <?php endif; ?>
    <div class="auth-layout">
        <div class="eye-panel">
            <h2>Artirup topluluğuna katıl</h2>
            <p>Göz seni takip ediyor. Şifre yazınca başka yere bakacak, çünkü gizlilik önemli.</p>
            <div class="eye-stage">
                <div class="eye" id="register-eye">
                    <div class="pupil" id="register-pupil"></div>
                </div>
            </div>
            <p>Yeni açık artırmalardan ilk sen haberdar ol.</p>
        </div>
        <div class="card auth-card">
        <h1>Kayıt Ol</h1>
        <p>Artirup topluluğuna katıl, açık artırmaları kaçırma.</p>
        <form class="form" method="post">
            <input type="text" name="name" placeholder="Ad Soyad" required />
            <input type="email" name="email" placeholder="E-posta" required />
            <input type="tel" name="phone" placeholder="Telefon" />
            <input type="password" name="password" placeholder="Şifre" id="register-password" required />
            <input type="password" name="password_repeat" placeholder="Şifre tekrar" id="register-password-repeat" required />
            <label style="display: flex; gap: 10px; align-items: center;">
                <input type="checkbox" name="seller_intent" value="1" />
                Satıcı olmak istiyorum
            </label>
            <label style="display: flex; gap: 10px; align-items: center;">
                <input type="checkbox" name="seller_agreement" value="1" />
                Satıcı sözleşmesini okudum ve kabul ediyorum
            </label>
            <button class="btn btn-primary" type="submit">Hesap Oluştur</button>
        </form>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Hızlı kayıt altyapısı.
</footer>
<script>
    const registerEye = document.getElementById('register-eye');
    const registerPupil = document.getElementById('register-pupil');
    const passwordFields = [
        document.getElementById('register-password'),
        document.getElementById('register-password-repeat'),
    ];

    document.addEventListener('mousemove', (event) => {
        const rect = registerEye.getBoundingClientRect();
        const eyeCenterX = rect.left + rect.width / 2;
        const eyeCenterY = rect.top + rect.height / 2;
        const angleX = (event.clientX - eyeCenterX) / 40;
        const angleY = (event.clientY - eyeCenterY) / 40;
        registerPupil.style.transform = `translate(${Math.max(Math.min(angleX, 20), -20)}px, ${Math.max(Math.min(angleY, 14), -14)}px)`;
    });

    passwordFields.forEach((field) => {
        field.addEventListener('focus', () => {
            registerEye.classList.add('look-away');
        });
        field.addEventListener('blur', () => {
            registerEye.classList.remove('look-away');
        });
    });
</script>
</body>
</html>
