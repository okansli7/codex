<?php
require_once __DIR__ . '/../config.php';
$pdo = db();
if (!$pdo) {
    fwrite(STDERR, "DB bağlantısı yok.\n");
    exit(1);
}

$logsDir = __DIR__ . '/../logs';
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}
$logFile = $logsDir . '/cron_auctions.log';

$closed = 0;
$st = $pdo->query("SELECT listing_id FROM auctions WHERE end_time <= NOW() AND status NOT IN ('ended','ended_no_winner')");
$ids = $st->fetchAll(PDO::FETCH_COLUMN) ?: [];

foreach ($ids as $listingId) {
    try {
        $pdo->beginTransaction();
        $q = $pdo->prepare('SELECT a.*, l.seller_id FROM auctions a JOIN listings l ON l.id=a.listing_id WHERE a.listing_id=:id FOR UPDATE');
        $q->execute(['id' => (int) $listingId]);
        $auction = $q->fetch();
        if (!$auction) {
            $pdo->rollBack();
            continue;
        }

        $status = 'ended';
        $hasWinner = !empty($auction['current_winner_id']);
        $reserveMet = $auction['reserve_price'] === null || (float) $auction['current_price'] >= (float) $auction['reserve_price'];

        if (!$hasWinner || !$reserveMet) {
            $status = 'ended_no_winner';
        }

        $pdo->prepare('UPDATE auctions SET status=:status WHERE listing_id=:id')->execute([
            'status' => $status,
            'id' => (int) $listingId,
        ]);

        if ($status === 'ended') {
            create_order_and_wallet_credit($pdo, $auction);
        }

        $pdo->commit();
        $closed++;
        log_audit('auction_closed', 'listing', (int) $listingId);
        file_put_contents($logFile, sprintf("[%s] listing=%d status=%s\n", date('Y-m-d H:i:s'), (int) $listingId, $status), FILE_APPEND);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        file_put_contents($logFile, sprintf("[%s] listing=%d error=%s\n", date('Y-m-d H:i:s'), (int) $listingId, $e->getMessage()), FILE_APPEND);
    }
}

echo 'Closed auctions: ' . $closed . PHP_EOL;
