<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
$message = '';
$error = '';
$searchQuery = trim($_GET['q'] ?? '');

$filteredProducts = $_SESSION['products'];
if ($searchQuery !== '') {
    $filteredProducts = array_values(array_filter($_SESSION['products'], function (array $product) use ($searchQuery): bool {
        $haystack = strtolower(($product['title'] ?? '') . ' ' . ($product['seller'] ?? ''));
        return str_contains($haystack, strtolower($searchQuery));
    }));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_product') {
        $title = trim($_POST['title'] ?? '');
        $price = (int) ($_POST['price'] ?? 0);
        $lots = max(1, (int) ($_POST['lots'] ?? 1));
        $image = trim($_POST['image'] ?? '');
        $galleryRaw = trim($_POST['gallery_urls'] ?? '');
        $tagRaw = trim($_POST['tags'] ?? '');
        $endAtInput = trim($_POST['end_at'] ?? '');
        $endAt = $endAtInput !== '' ? date('Y-m-d H:i:s', strtotime($endAtInput)) : date('Y-m-d H:i:s', strtotime('+7 days'));
        $gallery = array_values(array_filter(array_map('trim', explode(',', $galleryRaw))));
        if (empty($gallery) && $image !== '') {
            $gallery = [$image];
        }
        $tags = array_values(array_filter(array_map('trim', explode(',', $tagRaw))));
        if ($title !== '' && $price > 0) {
            $ids = array_column($_SESSION['products'], 'id');
            $nextId = $ids ? max($ids) + 1 : 1;
            $_SESSION['products'][] = [
                'id' => $nextId,
                'title' => $title,
                'seller' => $currentUser['name'] ?? 'Artirup Üye',
                'status' => 'Yayında',
                'lots' => $lots,
                'end_at' => $endAt,
                'price' => $price,
                'image' => $image !== '' ? $image : 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80',
                'gallery' => !empty($gallery) ? $gallery : [($image !== '' ? $image : 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80')],
                'tags' => $tags,
            ];
            $message = 'Ürün başarıyla eklendi.';
        } else {
            $error = 'Ürün eklemek için başlık ve fiyat zorunludur.';
        }
    }

    if ($action === 'edit_product') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        foreach ($_SESSION['products'] as &$product) {
            if ($product['id'] === $productId && (($currentUser && (($currentUser['name'] ?? '') === ($product['seller'] ?? ''))) || is_admin())) {
                $product['title'] = trim($_POST['title'] ?? $product['title']);
                $product['price'] = max(1, (int) ($_POST['price'] ?? $product['price']));
                $product['lots'] = max(1, (int) ($_POST['lots'] ?? $product['lots']));
                $product['status'] = trim($_POST['status'] ?? $product['status']);
                $product['image'] = trim($_POST['image'] ?? $product['image']);
                $galleryRaw = trim($_POST['gallery_urls'] ?? '');
                if ($galleryRaw !== '') {
                    $product['gallery'] = array_values(array_filter(array_map('trim', explode(',', $galleryRaw))));
                }
                $tagRaw = trim($_POST['tags'] ?? '');
                $product['tags'] = $tagRaw !== '' ? array_values(array_filter(array_map('trim', explode(',', $tagRaw)))) : [];
                $endAtInput = trim($_POST['end_at'] ?? '');
                if ($endAtInput !== '') {
                    $product['end_at'] = date('Y-m-d H:i:s', strtotime($endAtInput));
                }
                $message = 'Ürün güncellendi.';
                break;
            }
        }
        unset($product);
    }

    if ($action === 'delete_product') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $_SESSION['products'] = array_values(array_filter($_SESSION['products'], function (array $p) use ($productId, $currentUser): bool {
            if (($p['id'] ?? 0) !== $productId) {
                return true;
            }
            if (is_admin()) {
                return false;
            }
            return ($currentUser['name'] ?? '') !== ($p['seller'] ?? '');
        }));
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

    if ($action === 'add_to_cart') {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty = (int) ($_POST['qty'] ?? 1);
        if ($productId > 0) {
            add_to_cart($productId, $qty);
            $message = 'Ürün sepete eklendi.';
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
        <?php echo render_site_logo(); ?>
        <div class="nav-search">
            <form method="get" action="<?php echo url_path('pages/auctions.php'); ?>">
                <input class="nav-search-input" type="search" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>" placeholder="Ürün, satıcı veya kategori ara..." />
            </form>
        </div>
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
                <input type="number" name="lots" placeholder="Lot sayısı" min="1" value="1" required />
                <input type="url" name="image" placeholder="Ürün görsel URL" />
                <input type="text" name="gallery_urls" placeholder="Ek fotoğraflar (virgülle URL)" />
                <input type="text" name="tags" placeholder="Etiketler (virgülle)" />
                <label class="muted">Müzayede bitiş tarihi</label>
                <input type="datetime-local" name="end_at" />
                <button class="btn btn-primary" type="submit">Ürünü ekle</button>
            </form>
        </div>

        <div class="grid">
            <?php foreach ($filteredProducts as $product): ?>
                <div class="card">
                    <img class="auction-thumb" src="<?php echo htmlspecialchars($product['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" />
                    <h3><?php echo htmlspecialchars($product['title']); ?></h3>
                    <?php if (!empty($product['tags'])): ?>
                        <p class="muted">#<?php echo htmlspecialchars(implode(' #', $product['tags'])); ?></p>
                    <?php endif; ?>
                    <p>Kalan süre: <span class="countdown-live" data-end-at="<?php echo product_end_at($product); ?>"><?php echo product_countdown_label($product); ?></span></p>
                    <p>Bitiş: <?php echo htmlspecialchars(product_deadline_label($product)); ?> • <?php echo (int) $product['lots']; ?> lot</p>
                    <p>Başlangıç: ₺<?php echo number_format((int) $product['price']); ?></p>
                    <a class="btn btn-outline" href="<?php echo url_path('pages/product.php'); ?>?id=<?php echo $product['id']; ?>">Ürünü Gör</a>
                    <form class="form" method="post">
                        <input type="hidden" name="action" value="add_to_cart" />
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>" />
                        <input type="number" name="qty" min="1" value="1" />
                        <button class="btn btn-primary" type="submit">Sepete Ekle</button>
                    </form>
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
                            <input type="number" name="price" value="<?php echo (int) $product['price']; ?>" min="1" />
                            <input type="number" name="lots" value="<?php echo (int) ($product['lots'] ?? 1); ?>" min="1" />
                            <input type="text" name="status" value="<?php echo htmlspecialchars($product['status'] ?? 'Yayında'); ?>" />
                            <input type="url" name="image" value="<?php echo htmlspecialchars($product['image'] ?? ''); ?>" />
                            <input type="text" name="gallery_urls" value="<?php echo htmlspecialchars(implode(', ', $product['gallery'] ?? [])); ?>" />
                            <input type="text" name="tags" value="<?php echo htmlspecialchars(implode(', ', $product['tags'] ?? [])); ?>" />
                            <input type="datetime-local" name="end_at" value="<?php echo date('Y-m-d\TH:i', product_end_at($product)); ?>" />
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
            <?php if (empty($filteredProducts)): ?>
                <div class="card">
                    <h3>Sonuç bulunamadı</h3>
                    <p>Arama kriterlerinize uygun ürün bulunamadı. Farklı bir anahtar kelime deneyin.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<footer>
    Artirup © 2050 • Canlı açık artırma listesi.
</footer>
<script>
(function initLiveCountdowns(){
  const nodes = document.querySelectorAll('.countdown-live[data-end-at]');
  if (!nodes.length) return;
  const tick = () => {
    const now = Math.floor(Date.now()/1000);
    nodes.forEach((node) => {
      const endAt = parseInt(node.dataset.endAt || '0', 10);
      let diff = endAt - now;
      if (diff <= 0) {
        node.textContent = 'Süre doldu';
        return;
      }
      const days = Math.floor(diff / 86400);
      diff %= 86400;
      const hours = Math.floor(diff / 3600);
      diff %= 3600;
      const minutes = Math.floor(diff / 60);
      const seconds = diff % 60;
      node.textContent = `${days}g ${String(hours).padStart(2,'0')}s ${String(minutes).padStart(2,'0')}d ${String(seconds).padStart(2,'0')}sn`;
    });
  };
  tick();
  setInterval(tick, 1000);
})();
</script>
</body>
</html>
