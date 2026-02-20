<?php
require_once __DIR__ . '/../config.php';
$user = require_role(['seller', 'admin']);
$pdo = db();

$stats = ['listings' => 0, 'pending' => 0, 'auctions' => 0, 'orders' => 0, 'wallet' => 0];
if ($pdo && !empty($user['id'])) {
    $uid = (int) $user['id'];
    $stats['listings'] = (int) $pdo->query("SELECT COUNT(*) FROM listings WHERE seller_id = {$uid}")->fetchColumn();
    $stats['pending'] = (int) $pdo->query("SELECT COUNT(*) FROM listings WHERE seller_id = {$uid} AND status='pending'")->fetchColumn();
    $stats['auctions'] = (int) $pdo->query("SELECT COUNT(*) FROM listings WHERE seller_id = {$uid} AND type='auction'")->fetchColumn();
    $st = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE seller_id = :sid');
    $st->execute(['sid' => $uid]);
    $stats['orders'] = (int) $st->fetchColumn();
    $st = $pdo->prepare('SELECT balance FROM seller_wallets WHERE seller_id = :sid');
    $st->execute(['sid' => $uid]);
    $stats['wallet'] = (float) ($st->fetchColumn() ?: 0);
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Seller Dashboard</title><link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>"></head><body>
<section class="container"><div class="card"><h1>Satıcı Dashboard</h1><p><a class="btn btn-outline" href="<?php echo url_path('seller/add-listing.php'); ?>">+ İlan Ekle</a> <a class="btn btn-outline" href="<?php echo url_path('seller/my-listings.php'); ?>">İlanlarım</a></p><div class="grid"><div class="card"><h3><?php echo (int) $stats['listings']; ?></h3><p>Toplam İlan</p></div><div class="card"><h3><?php echo (int) $stats['pending']; ?></h3><p>Onay Bekleyen</p></div><div class="card"><h3><?php echo (int) $stats['auctions']; ?></h3><p>Auction İlanı</p></div><div class="card"><h3><?php echo (int) $stats['orders']; ?></h3><p>Sipariş</p></div><div class="card"><h3>₺<?php echo number_format((float) $stats['wallet'],2); ?></h3><p>Cüzdan</p></div></div></div></section>
</body></html>
