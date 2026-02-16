<?php
require_once __DIR__ . '/../config.php';
$pdo = db();
if (!$pdo) {
    fwrite(STDERR, "DB bağlantısı yok.\n");
    exit(1);
}

$pdo->beginTransaction();
try {
    $st = $pdo->query("SELECT a.listing_id FROM auctions a WHERE a.status IN ('active','scheduled') AND a.end_time <= NOW() FOR UPDATE");
    $ids = $st->fetchAll(PDO::FETCH_COLUMN) ?: [];
    foreach ($ids as $listingId) {
        $q = $pdo->prepare('SELECT a.*, l.seller_id FROM auctions a JOIN listings l ON l.id=a.listing_id WHERE a.listing_id=:id FOR UPDATE');
        $q->execute(['id' => (int) $listingId]);
        $auction = $q->fetch();
        if (!$auction) {
            continue;
        }
        $pdo->prepare("UPDATE auctions SET status='ended' WHERE listing_id=:id")->execute(['id' => (int) $listingId]);
        if (!empty($auction['current_winner_id']) && (float) $auction['current_price'] > 0) {
            create_order_and_wallet_credit($pdo, $auction);
        }
    }
    $pdo->commit();
    echo 'Closed auctions: ' . count($ids) . PHP_EOL;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
