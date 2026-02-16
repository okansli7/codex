<?php
require_once __DIR__ . '/../config.php';
$user = require_role(['admin']);
require_csrf();
$pdo = db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    try {
        $action = $_POST['action'] ?? '';
        if ($action === 'seller_request') {
            $id = (int) ($_POST['request_id'] ?? 0);
            $status = ($_POST['status'] ?? '') === 'approved' ? 'approved' : 'rejected';
            $pdo->beginTransaction();
            $st = $pdo->prepare('SELECT sr.*, u.name FROM seller_requests sr JOIN users u ON u.id=sr.user_id WHERE sr.id=:id FOR UPDATE');
            $st->execute(['id' => $id]);
            $req = $st->fetch();
            if (!$req) {
                throw new RuntimeException('Talep bulunamadı.');
            }
            $pdo->prepare('UPDATE seller_requests SET status=:status WHERE id=:id')->execute(['status' => $status, 'id' => $id]);
            if ($status === 'approved') {
                $pdo->prepare('UPDATE users SET role=\'Seller\' WHERE id=:uid')->execute(['uid' => $req['user_id']]);
                $pdo->prepare('INSERT INTO seller_profiles (user_id,store_name,slug,created_at) VALUES (:uid,:name,:slug,NOW()) ON DUPLICATE KEY UPDATE store_name=VALUES(store_name)')
                    ->execute(['uid' => $req['user_id'], 'name' => $req['name'], 'slug' => slugify($req['name'])]);
            }
            $pdo->commit();
            $message = 'Satıcı talebi güncellendi.';
        }

        if ($action === 'listing_status') {
            $listingId = (int) ($_POST['listing_id'] ?? 0);
            $status = in_array($_POST['status'] ?? '', ['published', 'rejected'], true) ? $_POST['status'] : 'pending';
            $pdo->prepare('UPDATE listings SET status=:status, updated_at=NOW() WHERE id=:id')->execute(['status' => $status, 'id' => $listingId]);
            if ($status === 'published') {
                $pdo->prepare("UPDATE auctions SET status = CASE WHEN NOW() BETWEEN start_time AND end_time THEN 'active' ELSE status END WHERE listing_id=:id")
                    ->execute(['id' => $listingId]);
            }
            $message = 'İlan durumu güncellendi.';
        }
    } catch (Throwable $e) {
        if ($pdo && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$requests = $pendingListings = $auctions = $orders = [];
$stats = ['commission' => 0, 'order_total' => 0];
if ($pdo) {
    $requests = $pdo->query("SELECT sr.id,sr.status,sr.created_at,u.name,u.email FROM seller_requests sr JOIN users u ON u.id=sr.user_id ORDER BY sr.id DESC")->fetchAll();
    $pendingListings = $pdo->query("SELECT l.id,l.title,l.type,l.status,u.name AS seller_name FROM listings l JOIN users u ON u.id=l.seller_id WHERE l.status='pending' ORDER BY l.id DESC")->fetchAll();
    $auctions = $pdo->query("SELECT a.listing_id,a.current_price,a.end_time,a.status,l.title FROM auctions a JOIN listings l ON l.id=a.listing_id ORDER BY a.end_time ASC LIMIT 20")->fetchAll();
    $orders = $pdo->query("SELECT o.id,o.total_amount,o.commission_amount,o.status,l.title FROM orders o JOIN listings l ON l.id=o.listing_id ORDER BY o.id DESC LIMIT 20")->fetchAll();
    $st = $pdo->query("SELECT COALESCE(SUM(commission_amount),0) c, COALESCE(SUM(total_amount),0) t FROM orders WHERE status IN ('paid','pending_payment')");
    $stats = $st->fetch() ?: $stats;
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Admin Marketplace</title><link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>"></head><body>
<main style="margin:20px"><h1>Marketplace Yönetimi</h1>
<?php if ($message): ?><p style="color:#1f9d62"><?php echo e($message); ?></p><?php endif; ?>
<?php if ($error): ?><p style="color:#b3283b"><?php echo e($error); ?></p><?php endif; ?>
<div style="display:grid;grid-template-columns:repeat(2,minmax(280px,1fr));gap:20px">
<section><h3>Satıcı Talepleri</h3><?php foreach($requests as $r): ?><form method="post" style="margin-bottom:8px"><?php echo csrf_input(); ?><input type="hidden" name="action" value="seller_request"><input type="hidden" name="request_id" value="<?php echo (int)$r['id']; ?>">#<?php echo (int)$r['id']; ?> <?php echo e($r['name']); ?> (<?php echo e($r['status']); ?>) <button name="status" value="approved">Onayla</button> <button name="status" value="rejected">Reddet</button></form><?php endforeach; ?></section>
<section><h3>Onay Bekleyen İlanlar</h3><?php foreach($pendingListings as $l): ?><form method="post" style="margin-bottom:8px"><?php echo csrf_input(); ?><input type="hidden" name="action" value="listing_status"><input type="hidden" name="listing_id" value="<?php echo (int)$l['id']; ?>">#<?php echo (int)$l['id']; ?> <?php echo e($l['title']); ?> <button name="status" value="published">Yayınla</button> <button name="status" value="rejected">Reddet</button></form><?php endforeach; ?></section>
<section><h3>Auction Overview</h3><?php foreach($auctions as $a): ?><div><?php echo e($a['title']); ?> - ₺<?php echo number_format((float)$a['current_price'],2); ?> - <?php echo e($a['status']); ?></div><?php endforeach; ?></section>
<section><h3>Orders & Komisyon</h3><p>Toplam Komisyon: ₺<?php echo number_format((float)$stats['c'],2); ?></p><p>Toplam Ciro: ₺<?php echo number_format((float)$stats['t'],2); ?></p><?php foreach($orders as $o): ?><div>#<?php echo (int)$o['id']; ?> <?php echo e($o['title']); ?> - ₺<?php echo number_format((float)$o['total_amount'],2); ?> (K: ₺<?php echo number_format((float)$o['commission_amount'],2); ?>)</div><?php endforeach; ?></section>
</div></main></body></html>
