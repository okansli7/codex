<?php
require_once __DIR__ . '/../config.php';
$user = require_role(['seller', 'admin']);
$pdo = db();
$rows = [];
if ($pdo && !empty($user['id'])) {
    $st = $pdo->prepare('SELECT l.*, a.end_time, a.current_price, a.status AS auction_status FROM listings l LEFT JOIN auctions a ON a.listing_id=l.id WHERE l.seller_id=:sid ORDER BY l.id DESC');
    $st->execute(['sid' => (int) $user['id']]);
    $rows = $st->fetchAll();
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>İlanlarım</title><link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>"></head><body>
<section class="container"><div class="card"><h1>İlanlarım</h1><p><a class="btn btn-primary" href="<?php echo url_path('seller/add-listing.php'); ?>">Yeni İlan</a> <a class="btn btn-outline" href="<?php echo url_path('seller/dashboard.php'); ?>">Dashboard</a></p>
<table style="width:100%"><thead><tr><th>ID</th><th>Başlık</th><th>Tür</th><th>Durum</th><th>Fiyat</th><th>İşlem</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?>
<tr><td><?php echo (int)$r['id']; ?></td><td><?php echo e($r['title']); ?></td><td><?php echo e($r['type']); ?></td><td><span class="btn btn-outline" style="padding:4px 10px"><?php echo e($r['status']); ?></span></td><td>₺<?php echo number_format((float)$r['price'],2); ?></td><td><a class="btn btn-outline" href="<?php echo url_path('seller/edit-listing.php'); ?>?id=<?php echo (int)$r['id']; ?>">Düzenle</a> <a class="btn btn-outline" href="<?php echo url_path('pages/listing.php'); ?>?id=<?php echo (int)$r['id']; ?>">Görüntüle</a></td></tr>
<?php endforeach; ?>
</tbody></table></div></section>
</body></html>
