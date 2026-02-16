<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
if (!$pdo || $id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid']);
    exit;
}
$st = $pdo->prepare('SELECT a.listing_id,a.current_price,a.current_winner_id,a.start_time,a.end_time,a.status,(SELECT COUNT(*) FROM bids b WHERE b.listing_id=a.listing_id) AS bid_count FROM auctions a WHERE a.listing_id=:id LIMIT 1');
$st->execute(['id' => $id]);
$row = $st->fetch();
if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}
$now = new DateTimeImmutable('now');
$start = new DateTimeImmutable($row['start_time']);
$end = new DateTimeImmutable($row['end_time']);
$status = $row['status'];
if ($status === 'scheduled' && $now >= $start && $now < $end) {
    $status = 'active';
}
if (in_array($status, ['active', 'scheduled'], true) && $now >= $end) {
    $status = 'ended';
}

echo json_encode([
    'current_price' => (float) $row['current_price'],
    'bid_count' => (int) $row['bid_count'],
    'highest_bidder_masked' => masked_user_name(!empty($row['current_winner_id']) ? (int) $row['current_winner_id'] : null),
    'end_time' => $row['end_time'],
    'status' => $status,
    'server_time' => $now->format('Y-m-d H:i:s'),
], JSON_UNESCAPED_UNICODE);
