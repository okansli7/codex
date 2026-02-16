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
$st = $pdo->prepare('SELECT listing_id,current_price,current_winner_id,start_time,end_time,status FROM auctions WHERE listing_id=:id LIMIT 1');
$st->execute(['id' => $id]);
$row = $st->fetch();
if (!$row) {
    http_response_code(404);
    echo json_encode(['error' => 'not_found']);
    exit;
}
echo json_encode($row, JSON_UNESCAPED_UNICODE);
