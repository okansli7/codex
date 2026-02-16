<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
$productId = (int) ($_GET['id'] ?? 0);
$product = null;
foreach ($_SESSION['products'] as $item) {
    if ($item['id'] === $productId) {
        $product = $item;
        break;
    }
}
$message = '';
$error = '';
require_csrf();
$productUrl = url_path('pages/product.php') . '?id=' . $productId;

if (!$product) {
    $error = 'Ürün bulunamadı.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $product) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add_to_cart') {
        $qty = (int) ($_POST['qty'] ?? 1);
        add_to_cart((int) $product['id'], $qty);
        $message = 'Ürün sepete eklendi.';
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
            $error = 'Yorum yapmak için kayıt olmalısın.';
        } else {
            $commentText = trim($_POST['comment'] ?? '');
            if ($commentText !== '') {
                $_SESSION['comments'][$productId][] = [
                    'author' => $currentUser['name'] ?? 'Kullanıcı',
                    'avatar' => $currentUser['avatar'] ?? '',
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
    <title>Artirup | Ürün Detayı</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
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
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
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
        <h1>Ürün Detayı</h1>
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
        <?php if ($product): ?>
            <div class="grid" style="grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));">
                <div class="card">
                    <img class="product-main-image" src="<?php echo htmlspecialchars($product['image'] ?? ''); ?>" alt="<?php echo htmlspecialchars($product['title']); ?>" />
                    <?php if (!empty($product['gallery'])): ?>
                        <div class="product-gallery">
                            <?php foreach ($product['gallery'] as $galleryImage): ?>
                                <img src="<?php echo htmlspecialchars($galleryImage); ?>" alt="Galeri" />
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <h2><?php echo htmlspecialchars($product['title']); ?></h2>
                    <?php if (!empty($product['tags'])): ?>
                        <p class="muted">#<?php echo htmlspecialchars(implode(' #', $product['tags'])); ?></p>
                    <?php endif; ?>
                    <p><strong>Satıcı:</strong> <?php echo htmlspecialchars($product['seller']); ?></p>
                    <p><strong>Durum:</strong> <?php echo htmlspecialchars($product['status']); ?></p>
                    <p><strong>Lot:</strong> <?php echo htmlspecialchars($product['lots']); ?></p>
                    <p><strong>Bitiş:</strong> <?php echo htmlspecialchars(product_deadline_label($product)); ?></p>
                    <p><strong>Kalan:</strong> <span class="countdown-live" data-end-at="<?php echo product_end_at($product); ?>"><?php echo htmlspecialchars(product_countdown_label($product)); ?></span></p>
                </div>
                <div class="card">
                    <h3>Teklif Ver</h3>
                    <form class="form" method="post" style="margin-bottom: 12px;"><?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="add_to_cart" />
                        <input type="number" name="qty" min="1" value="1" />
                        <button class="btn btn-primary" type="submit">Sepete Ekle</button>
                    </form>
                    <form class="form" method="post"><?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="bid" />
                        <input type="number" name="amount" min="1" placeholder="Teklif tutarı (₺)" required />
                        <button class="btn btn-primary" type="submit">Teklif ver</button>
                    </form>
                    <p style="margin-top: 12px;">Mevcut fiyat: ₺<?php echo number_format($product['price']); ?></p>
                </div>
            </div>
            <div class="card" style="margin-top: 24px;">
                <h3>Yorumlar</h3>
                <div class="card" style="margin-bottom: 12px;">
                    <h4>Paylaş</h4>
                    <div class="social-share">
                        <a class="social-icon" title="WhatsApp" target="_blank" href="https://wa.me/?text=<?php echo urlencode('Bu müzayedeye bak: ' . $productUrl); ?>">💬</a>
                        <a class="social-icon" title="Facebook" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($productUrl); ?>">📘</a>
                        <a class="social-icon" title="X / Twitter" target="_blank" href="https://twitter.com/intent/tweet?url=<?php echo urlencode($productUrl); ?>&text=<?php echo urlencode($product['title'] ?? 'Müzayede'); ?>">𝕏</a>
                        <a class="social-icon" title="Telegram" target="_blank" href="https://t.me/share/url?url=<?php echo urlencode($productUrl); ?>&text=<?php echo urlencode($product['title'] ?? 'Müzayede'); ?>">✈️</a>
                        <a class="social-icon" title="LinkedIn" target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo urlencode($productUrl); ?>">💼</a>
                    </div>
                </div>
                <?php foreach (($_SESSION['comments'][$productId] ?? []) as $comment): ?>
                    <div class="comment-item">
                        <?php if (!empty($comment['avatar'])): ?>
                            <img class="comment-avatar" src="<?php echo htmlspecialchars($comment['avatar']); ?>" alt="<?php echo htmlspecialchars($comment['author'] ?? 'Kullanıcı'); ?>" />
                        <?php else: ?>
                            <span class="comment-avatar"><?php echo strtoupper(substr($comment['author'] ?? 'U', 0, 1)); ?></span>
                        <?php endif; ?>
                        <div>
                            <strong><?php echo htmlspecialchars($comment['author']); ?></strong>
                            <div><?php echo htmlspecialchars($comment['text']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($currentUser): ?>
                    <form class="form" method="post"><?php echo csrf_input(); ?>
                        <input type="hidden" name="action" value="add_comment" />
                        <input id="comment-input" type="text" name="comment" placeholder="Yorum yaz" />
                        <div class="emoji-row">
                            <button class="emoji-btn" type="button" data-emoji="😀">😀</button>
                            <button class="emoji-btn" type="button" data-emoji="🔥">🔥</button>
                            <button class="emoji-btn" type="button" data-emoji="👏">👏</button>
                            <button class="emoji-btn" type="button" data-emoji="😍">😍</button>
                            <button class="emoji-btn" type="button" data-emoji="🎉">🎉</button>
                        </div>
                        <button class="btn btn-outline" type="submit">Yorum gönder</button>
                    </form>
                <?php else: ?>
                    <p>Yorum yapmak için <a href="<?php echo url_path('auth/register.php'); ?>">kayıt ol</a>.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<footer>
    Artirup © 2050 • Ürün detayları.
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

document.querySelectorAll('.emoji-btn').forEach((btn) => {
  btn.addEventListener('click', () => {
    const input = document.getElementById('comment-input');
    if (!input) return;
    input.value = (input.value + " " + (btn.dataset.emoji || "")).trimStart();
    input.focus();
  });
});
</script>
</body>
</html>
