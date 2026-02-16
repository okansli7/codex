<?php
require_once __DIR__ . '/../config.php';
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';

$q = trim($_GET['q'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$endingSoon = !empty($_GET['ending_soon']);
$categoryFilter = trim($_GET['category'] ?? '');

$rows = [];
$categories = [];
$pdo = db();
if ($pdo) {
    $where = ["l.type='auction'", "l.status='published'"];
    $params = [];
    if ($q !== '') {
        $where[] = 'l.title LIKE :q';
        $params['q'] = '%' . $q . '%';
    }
    if ($minPrice !== '' && is_numeric($minPrice)) {
        $where[] = 'a.current_price >= :min_price';
        $params['min_price'] = (float) $minPrice;
    }
    if ($maxPrice !== '' && is_numeric($maxPrice)) {
        $where[] = 'a.current_price <= :max_price';
        $params['max_price'] = (float) $maxPrice;
    }
    if ($endingSoon) {
        $where[] = "a.end_time <= DATE_ADD(NOW(), INTERVAL 24 HOUR)";
    }
    if ($categoryFilter !== '') {
        $where[] = 'EXISTS (SELECT 1 FROM categories c WHERE c.name = :category)';
        $params['category'] = $categoryFilter;
    }

    $sql = 'SELECT l.id,l.title,l.price,l.created_at,a.current_price,a.end_time,a.status,(SELECT COUNT(*) FROM bids b WHERE b.listing_id=l.id) AS bid_count,(SELECT path FROM listing_images li WHERE li.listing_id=l.id ORDER BY li.id ASC LIMIT 1) AS image FROM listings l JOIN auctions a ON a.listing_id=l.id WHERE ' . implode(' AND ', $where) . ' ORDER BY a.end_time ASC';
    $st = $pdo->prepare($sql);
    $st->execute($params);
    $rows = $st->fetchAll();

    $categories = $pdo->query('SELECT name FROM categories WHERE is_active=1 ORDER BY sort_order ASC, id ASC')->fetchAll();
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Canlı Açık Artırmalar</title>
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
</head>
<body>
<div class="top-strip">
    <div class="top-strip-left">
        <span><strong>TR</strong> • Canlı Destek</span>
        <span>Bizi ara: <strong><?php echo e(settings_get('support_phone', '+90 850 840 00 00')); ?></strong></span>
        <span>E-posta: <a href="mailto:<?php echo e(settings_get('support_email', 'destek@artirup.com')); ?>"><?php echo e(settings_get('support_email', 'destek@artirup.com')); ?></a></span>
    </div>
    <div class="top-strip-right"><span>🚚 Sipariş Takibi</span></div>
</div>
<header>
    <div class="nav">
        <?php echo render_site_logo(); ?>
        <div class="nav-search"><form method="get"><input class="nav-search-input" type="search" name="q" value="<?php echo e($q); ?>" placeholder="Açık artırma ara..." /></form></div>
        <nav><ul><li><a href="<?php echo url_path('index.php'); ?>">Anasayfa</a></li><li><a href="<?php echo url_path('pages/about.php'); ?>">Hakkımızda</a></li><li><a href="<?php echo url_path('pages/stores.php'); ?>">Mağazalar</a></li><li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li></ul></nav>
        <div class="nav-actions">
            <a class="cart-icon-btn" href="<?php echo url_path('pages/cart.php'); ?>" aria-label="Sepet">🛒<span class="cart-count"><?php echo cart_count() > 0 ? cart_count() : '•'; ?></span></a>
            <?php if ($currentUser): ?><div class="profile-menu"><div class="profile-trigger"><img class="profile-avatar" src="<?php echo e($avatar); ?>" alt="Profil" /><span class="profile-name"><?php echo e($currentUser['name'] ?? 'Profilim'); ?></span></div><div class="profile-dropdown"><a href="<?php echo url_path('profile.php'); ?>">Profilim</a><a href="<?php echo $dashboardLink; ?>"><?php echo $dashboardLabel; ?></a><a href="<?php echo url_path('auth/logout.php'); ?>">Çıkış Yap</a></div></div><?php else: ?><a class="btn btn-outline" href="<?php echo url_path('auth/login.php'); ?>">Giriş Yap</a><a class="btn btn-primary" href="<?php echo url_path('auth/register.php'); ?>">Kayıt Ol</a><?php endif; ?>
        </div>
    </div>
</header>

<section class="container">
    <div class="card">
        <h1>Canlı Açık Artırmalar</h1>
        <form class="form" method="get" style="grid-template-columns:repeat(auto-fit,minmax(170px,1fr));">
            <input type="search" name="q" value="<?php echo e($q); ?>" placeholder="Başlığa göre ara" />
            <select name="category"><option value="">Kategori</option><?php foreach ($categories as $c): ?><option value="<?php echo e($c['name']); ?>" <?php echo $categoryFilter===$c['name']?'selected':''; ?>><?php echo e($c['name']); ?></option><?php endforeach; ?></select>
            <input type="number" step="0.01" name="min_price" value="<?php echo e($minPrice); ?>" placeholder="Min fiyat" />
            <input type="number" step="0.01" name="max_price" value="<?php echo e($maxPrice); ?>" placeholder="Max fiyat" />
            <label><input type="checkbox" name="ending_soon" value="1" <?php echo $endingSoon?'checked':''; ?>> 24 saatte bitecek</label>
            <button class="btn btn-primary" type="submit">Filtrele</button>
        </form>

        <div class="grid" style="margin-top:16px;">
            <?php foreach ($rows as $row): ?>
                <?php $endTs = strtotime((string)$row['end_time']) ?: time(); ?>
                <div class="card">
                    <img class="auction-thumb" src="<?php echo e($row['image'] ?: 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80'); ?>" alt="<?php echo e($row['title']); ?>" />
                    <h3><?php echo e($row['title']); ?></h3>
                    <p><strong>Güncel Teklif:</strong> ₺<?php echo number_format((float)$row['current_price'],2,',','.'); ?></p>
                    <p><strong>Teklif Sayısı:</strong> <span class="pill"><?php echo (int)$row['bid_count']; ?></span></p>
                    <p><strong>Kalan:</strong> <span class="countdown-live" data-end-at="<?php echo $endTs; ?>"><?php echo e(date('d.m.Y H:i', $endTs)); ?></span></p>
                    <a class="btn btn-outline" href="<?php echo url_path('pages/listing.php'); ?>?id=<?php echo (int)$row['id']; ?>">Açık Artırmaya Git</a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($rows)): ?><div class="card"><p>Filtreye uygun açık artırma bulunamadı.</p></div><?php endif; ?>
        </div>
    </div>
</section>
<footer><?php echo e(settings_get('footer_text', 'Artirup © 2050')); ?></footer>
<script>
(function(){const nodes=document.querySelectorAll('.countdown-live[data-end-at]');if(!nodes.length)return;const tick=()=>{const now=Math.floor(Date.now()/1000);nodes.forEach((n)=>{let d=parseInt(n.dataset.endAt||'0',10)-now;if(d<=0){n.textContent='Bitti';return;}const day=Math.floor(d/86400);d%=86400;const h=Math.floor(d/3600);d%=3600;const m=Math.floor(d/60);const s=d%60;n.textContent=`${day}g ${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;});};tick();setInterval(tick,1000);})();
</script>
</body>
</html>
