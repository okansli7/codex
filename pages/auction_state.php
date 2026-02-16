<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
if (!$pdo || $id < 1) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_request'], JSON_UNESCAPED_UNICODE);
    exit;
}

$st = $pdo->prepare('SELECT a.listing_id,a.start_time,a.end_time,a.status,a.current_price,a.current_winner_id,a.starting_price,a.min_increment,a.reserve_price,(SELECT COUNT(*) FROM bids b WHERE b.listing_id=a.listing_id) AS bid_count FROM auctions a WHERE a.listing_id=:id LIMIT 1');
$st->execute(['id' => $id]);
$row = $st->fetch();
if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found'], JSON_UNESCAPED_UNICODE);
    exit;
}

$serverNow = new DateTimeImmutable('now');
$startAt = new DateTimeImmutable((string) $row['start_time']);
$endAt = new DateTimeImmutable((string) $row['end_time']);
$dbStatus = (string) $row['status'];

$status = $dbStatus;
if ($serverNow < $startAt) {
    $status = 'scheduled';
} elseif ($serverNow >= $endAt) {
    $status = in_array($dbStatus, ['ended_no_winner', 'cancelled'], true) ? $dbStatus : 'ended';
} elseif (!in_array($dbStatus, ['cancelled', 'ended_no_winner'], true)) {
    $status = 'active';
}

$bidCount = (int) $row['bid_count'];
$minIncrement = (float) ($row['min_increment'] ?: settings_get('default_min_increment', '10'));
$currentPrice = (float) $row['current_price'];
$startingPrice = (float) $row['starting_price'];
$hasBid = $bidCount > 0 && !empty($row['current_winner_id']);
$minValid = null;
if ($status === 'active') {
    $minValid = $hasBid ? $currentPrice + $minIncrement : max($startingPrice, $currentPrice);
}

$reserveMet = null;
if ($row['reserve_price'] !== null) {
    $reserveMet = $currentPrice >= (float) $row['reserve_price'];
}

$winnerMasked = null;
if ($status === 'ended' && !empty($row['current_winner_id'])) {
    $winnerMasked = masked_user_name((int) $row['current_winner_id']);
}

echo json_encode([
    'status' => $status,
    'current_price' => number_format($currentPrice, 2, '.', ''),
    'end_time' => $endAt->format('Y-m-d H:i:s'),
    'server_time' => $serverNow->format('Y-m-d H:i:s'),
    'bid_count' => $bidCount,
    'min_increment' => number_format($minIncrement, 2, '.', ''),
    'min_valid_bid' => $minValid !== null ? number_format((float) $minValid, 2, '.', '') : null,
    'winner_masked' => $winnerMasked,
    'reserve_met' => $reserveMet,
], JSON_UNESCAPED_UNICODE);
