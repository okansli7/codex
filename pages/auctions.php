<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_product') {
        $title = trim($_POST['title'] ?? '');
        $price = (int) ($_POST['price'] ?? 0);
        if ($title !== '') {
            $ids = array_column($_SESSION['products'], 'id');
            $nextId = $ids ? max($ids) + 1 : 1;
            $_SESSION['products'][] = [
                'id' => $nextId,
                'title' => $title,
                'seller' => $currentUser['name'] ?? 'Artirup Üye',
                'status' => 'Yayında',
                'lots' => 1,
                'end' => '30 Mar 20:00',
                'price' => $price,
            ];
            $message = 'Ürün başarıyla eklendi.';
        }
    }

    if ($action === 'edit_product') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        foreach ($_SESSION['products'] as &$product) {
            if ($product['id'] === $productId) {
                $product['title'] = trim($_POST['title'] ?? $product['title']);
                $product['price'] = (int) ($_POST['price'] ?? $product['price']);
                $message = 'Ürün güncellendi.';
                break;
            }
        }
        unset($product);
    }

    if ($action === 'delete_product') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $_SESSION['products'] = array_values(array_filter($_SESSION['products'], fn($p) => $p['id'] !== $productId));
        $message = 'Ürün silindi.';
    }

    if ($action === 'bid') {
        $amount = (int) ($_POST['amount'] ?? 0);
        if ($amount > 0) {
            $message = 'Teklifiniz alındı.';
        } else {
            $error = 'Teklif tutarı geçersiz.';
        }
    }

    if ($action === 'add_comment') {
        if (!$currentUser) {
            $error = 'Yorum yazmak için kayıt olmalısın.';
        } else {
            $productId = (int) ($_POST['product_id'] ?? 0);
            $commentText = trim($_POST['comment'] ?? '');
            if ($commentText !== '') {
                $_SESSION['comments'][$productId][] = [
                    'author' => $currentUser['name'] ?? 'Kullanıcı',
                    'text' => $commentText,
                ];
                $message = 'Yorumun kaydedildi.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Açık Artırmalar</title>
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
                <li><a href="<?php echo url_path('pages/stores.php'); ?>">Mağazalar</a></li>
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
        <h1>Canlı Açık Artırmalar</h1>
        <p>Yayındaki tüm müzayedeler burada listelenir. Kategori, fiyat ve zaman filtreleri yakında.</p>
        <?php if ($message): ?>
            <div class="card" style="background: #e4f9ef; color: #1f9d62; margin-bottom: 16px;">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="card" style="background: #ffe1e6; color: #b3283b; margin-bottom: 16px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <div class="card" style="margin-bottom: 20px;">
            <h3>Ürün Ekle</h3>
            <form class="form" method="post">
                <input type="hidden" name="action" value="add_product" />
                <input type="text" name="title" placeholder="Ürün başlığı" required />
                <input type="number" name="price" placeholder="Başlangıç fiyatı" min="1" required />
                <button class="btn btn-primary" type="submit">Ürünü ekle</button>
            </form>
        </div>

        <div class="grid">
            <?php foreach ($_SESSION['products'] as $product): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <p>Bitiş: <?php echo $product['end']; ?> • <?php echo $product['lots']; ?> lot</p>
                    <p>Başlangıç: ₺<?php echo number_format($product['price']); ?></p>
                    <a class="btn btn-outline" href="<?php echo url_path('pages/product.php'); ?>?id=<?php echo $product['id']; ?>">Ürünü Gör</a>
                    <form class="form" method="post">
                        <input type="hidden" name="action" value="bid" />
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="number" name="amount" min="1" placeholder="Teklifin (₺)" required />
                        <button class="btn btn-outline" type="submit">Teklif ver</button>
                    </form>
                    <div style="margin-top: 10px;">
                        <form class="form" method="post">
                            <input type="hidden" name="action" value="edit_product" />
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                            <input type="text" name="title" value="<?php echo htmlspecialchars($product['title']); ?>" />
                            <input type="number" name="price" value="<?php echo (int) $product['price']; ?>" />
                            <div class="actions">
                                <button class="btn btn-outline" type="submit">Düzenle</button>
                                <button class="btn btn-danger" type="submit" name="action" value="delete_product">Sil</button>
                            </div>
                        </form>
                    </div>
                    <div style="margin-top: 12px;">
                        <h4>Yorumlar</h4>
                        <?php foreach (($_SESSION['comments'][$product['id']] ?? []) as $comment): ?>
                            <p><strong><?php echo htmlspecialchars($comment['author']); ?>:</strong> <?php echo htmlspecialchars($comment['text']); ?></p>
                        <?php endforeach; ?>
                        <?php if ($currentUser): ?>
                            <form class="form" method="post">
                                <input type="hidden" name="action" value="add_comment" />
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                                <input type="text" name="comment" placeholder="Yorum yaz" />
                                <button class="btn btn-primary" type="submit">Yorum gönder</button>
                            </form>
                        <?php else: ?>
                            <p>Yorum yapmak için <a href="<?php echo url_path('auth/register.php'); ?>">kayıt ol</a>.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Canlı açık artırma listesi.
</footer>
</body>
</html>
