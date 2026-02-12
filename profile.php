<?php
require_once __DIR__ . '/config.php';
$user = current_user();
if (!$user) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}

$successMessage = '';
$successType = 'ok';
$profileMessage = '';
$role = $user['role'] ?? 'Kullanıcı';
$avatar = $user['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=160&h=160&q=80';
$vip = !empty($user['vip']) || (($user['purchases'] ?? 0) >= 20);
$countryOptions = country_options();
$countryCode = $user['country'] ?? 'TR';
$tickClass = '';
if ($vip) {
    $tickClass = 'gold';
} elseif ($role === 'Admin') {
    $tickClass = 'red';
} elseif ($role === 'Moderatör') {
    $tickClass = 'green';
} elseif ($role === 'Satıcı') {
    $tickClass = 'blue';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_profile') {
        $user['name'] = trim($_POST['name'] ?? $user['name']);
        $user['address'] = trim($_POST['address'] ?? '');
        $user['birthdate'] = trim($_POST['birthdate'] ?? '');
        $user['country'] = $_POST['country'] ?? $countryCode;

        if (!empty($_FILES['avatar']['name']) && is_uploaded_file($_FILES['avatar']['tmp_name'])) {
            $uploadsDir = __DIR__ . '/uploads';
            if (!is_dir($uploadsDir)) {
                mkdir($uploadsDir, 0755, true);
            }
            $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
            $safeExtension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
            $fileName = 'avatar_' . time() . ($safeExtension ? '.' . $safeExtension : '');
            $destination = $uploadsDir . '/' . $fileName;
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destination)) {
                $user['avatar'] = url_path('uploads/' . $fileName);
                $avatar = $user['avatar'];
            }
        }

        $_SESSION['user'] = $user;
        $profileMessage = 'Profil bilgileri güncellendi.';
        $countryCode = $user['country'] ?? $countryCode;
    }
    if ($action === 'seller_application') {
        $sellerStore = trim($_POST['store_name'] ?? '');
        $sellerCategory = trim($_POST['store_category'] ?? '');
        $sellerNote = trim($_POST['store_note'] ?? '');
        $sellerAgreement = !empty($_POST['seller_agreement']);
        if (!$sellerAgreement) {
            $successMessage = 'Satıcı olmak için sözleşmeyi kabul etmelisin.';
            $successType = 'error';
        } else {
            $_SESSION['seller_application'] = [
                'store_name' => $sellerStore,
                'store_category' => $sellerCategory,
                'store_note' => $sellerNote,
                'approved_at' => date('Y-m-d H:i:s'),
            ];
            $user['role'] = 'Satıcı';
            $user['seller_intent'] = true;
            $user['seller_agreement'] = true;
            $_SESSION['user'] = $user;
            $role = $user['role'];
            $tickClass = 'blue';
            $successMessage = 'Satıcı sözleşmesi onaylandı. Satıcı panelin aktif edildi.';
            $successType = 'ok';
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Profilim</title>
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
            <a class="cart-icon-btn" href="<?php echo url_path('pages/cart.php'); ?>" aria-label="Sepet">🛒<span class="cart-count"><?php echo cart_count() > 0 ? cart_count() : '•'; ?></span></a>
            <a class="btn btn-outline" href="<?php echo url_path('auth/logout.php'); ?>">Çıkış Yap</a>
        </div>
    </div>
</header>

<section class="container">
    <div class="grid" style="grid-template-columns: minmax(240px, 320px) 1fr;">
        <div class="card">
            <div style="text-align: center;">
                <img class="profile-avatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Profil fotoğrafı" />
                <h2 style="margin: 16px 0 6px;"><?php echo htmlspecialchars($user['name']); ?></h2>
                <p style="margin: 0; color: var(--text);">
                    <?php echo htmlspecialchars($role); ?>
                    <?php if ($tickClass): ?>
                        <span class="tick <?php echo $tickClass; ?>">✔</span>
                    <?php endif; ?>
                </p>
                <?php if ($vip): ?>
                    <p style="margin: 8px 0 0; color: #d4a017;">VIP Kullanıcı</p>
                <?php endif; ?>
            </div>
            <div style="margin-top: 20px;">
                <h4>Hızlı İşlemler</h4>
                <div class="form">
                    <?php if (($role === 'Satıcı' || is_seller()) && !$vip): ?>
                        <a class="btn btn-outline" href="<?php echo url_path('seller/index.php'); ?>">Açık artırma oluştur</a>
                    <?php elseif (!$vip): ?>
                        <a class="btn btn-outline" href="#seller-basvuru">Satıcı olmak istiyorum</a>
                    <?php endif; ?>
                    <a class="btn btn-outline" href="<?php echo url_path('pages/auctions.php'); ?>">Yeni ilan ver</a>
                    <a class="btn btn-outline" href="<?php echo url_path('pages/auctions.php'); ?>">Açık artırmalarım</a>
                    <a class="btn btn-outline" href="<?php echo url_path('pages/auctions.php'); ?>">İlanlarım</a>
                    <a class="btn btn-primary" href="#profil-duzenle">Profili düzenle</a>
                </div>
            </div>
        </div>
        <div>
            <div class="card">
                <h1>Profilim</h1>
                <p>Hoş geldin, <strong><?php echo htmlspecialchars($user['name']); ?></strong>. Açık artırma yolculuğunu buradan yönet.</p>
        <div class="grid">
            <div class="card">
                <h3>Hesap Bilgileri</h3>
                <p><strong>E-posta:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                <?php if (!empty($user['phone'])): ?>
                    <p><strong>Telefon:</strong> <?php echo htmlspecialchars($user['phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($user['country'])): ?>
                    <p><strong>Ülke:</strong> <?php echo htmlspecialchars($countryOptions[$user['country']]['name'] ?? $user['country']); ?></p>
                <?php endif; ?>
            </div>
            <div class="card">
                <h3>Biyografi</h3>
                <p>Modern koleksiyonlar, teknoloji ve sanat lotlarıyla ilgileniyorum. Hedefim; en iyi lotlara zamanında teklif vermek.</p>
            </div>
        </div>
    </div>

    <div class="card" id="profil-duzenle" style="margin-top: 24px;">
        <h2>Profili Düzenle</h2>
        <?php if ($profileMessage): ?>
            <div class="card" style="background: #e4f9ef; color: #1f9d62; margin-bottom: 16px;">
                <?php echo $profileMessage; ?>
            </div>
        <?php endif; ?>
        <form class="form" method="post" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update_profile" />
            <input type="text" name="name" placeholder="Ad Soyad" value="<?php echo htmlspecialchars($user['name']); ?>" required />
            <input type="text" name="address" placeholder="Yaşadığın adres" />
            <input type="date" name="birthdate" placeholder="Doğum tarihi" />
            <select name="country" required>
                <?php foreach ($countryOptions as $code => $country): ?>
                    <option value="<?php echo $code; ?>" <?php echo $code === $countryCode ? 'selected' : ''; ?>>
                        <?php echo $country['flag'] . ' ' . $country['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="file" name="avatar" accept="image/*" />
            <button class="btn btn-primary" type="submit">Kaydet</button>
        </form>
    </div>
        </div>
    </div>

    <div class="card" id="seller-basvuru" style="margin-top: 24px;">
        <h2>Satıcı Olmak İstiyorum</h2>
        <p>Normal kullanıcıysan sözleşmeyi kabul ederek satıcı panelini aktif edebilirsin.</p>
        <?php if ($successMessage): ?>
            <div class="card" style="background: <?php echo $successType === 'ok' ? '#e4f9ef' : '#ffe1e6'; ?>; color: <?php echo $successType === 'ok' ? '#1f9d62' : '#b3283b'; ?>; margin-bottom: 16px;">
                <?php echo $successMessage; ?>
            </div>
        <?php endif; ?>
        <form class="form" method="post">
            <input type="hidden" name="action" value="seller_application" />
            <input type="text" name="store_name" placeholder="Mağaza adı" required />
            <input type="text" name="store_category" placeholder="Kategori" required />
            <textarea rows="4" name="store_note" placeholder="Kısaca mağazanı anlat"></textarea>
            <label style="display:flex; gap:10px; align-items:center;">
                <input type="checkbox" name="seller_agreement" value="1" required />
                Satıcı sözleşmesini ve platform koşullarını kabul ediyorum
            </label>
            <button class="btn btn-primary" type="submit">Başvuruyu Gönder</button>
        </form>
    </div>
</section>

<footer>
    Artirup © 2050 • Profil ve satıcı başvuruları.
</footer>
</body>
</html>
