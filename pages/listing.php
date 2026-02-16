<?php
require_once __DIR__ . '/../config.php';
require_csrf();
$pdo = db();
$id = (int) ($_GET['id'] ?? $_POST['listing_id'] ?? 0);
$error = '';
$message = '';
$listing = null;
$images = [];
$auction = null;
$bids = [];

if ($pdo && $id > 0) {
    $st = $pdo->prepare('SELECT * FROM listings WHERE id=:id AND status=\'published\' LIMIT 1');
    $st->execute(['id' => $id]);
    $listing = $st->fetch();
    if ($listing) {
        $st = $pdo->prepare('SELECT path FROM listing_images WHERE listing_id=:id ORDER BY id ASC');
        $st->execute(['id' => $id]);
        $images = $st->fetchAll();
        $st = $pdo->prepare('SELECT * FROM auctions WHERE listing_id=:id LIMIT 1');
        $st->execute(['id' => $id]);
        $auction = $st->fetch() ?: null;
        $st = $pdo->prepare('SELECT b.amount,b.created_at,u.name FROM bids b JOIN users u ON u.id=b.user_id WHERE b.listing_id=:id ORDER BY b.id DESC LIMIT 10');
        $st->execute(['id' => $id]);
        $bids = $st->fetchAll();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo && $listing && $auction) {
    $user = require_role(['buyer', 'seller', 'admin']);
    try {
        $amount = (float) ($_POST['amount'] ?? 0);
        $pdo->beginTransaction();
        $st = $pdo->prepare('SELECT a.*, l.seller_id FROM auctions a JOIN listings l ON l.id=a.listing_id WHERE a.listing_id=:id FOR UPDATE');
        $st->execute(['id' => $id]);
        $locked = $st->fetch();
        if (!$locked) {
            throw new RuntimeException('Açık artırma bulunamadı.');
        }
        if ((int) $locked['seller_id'] === (int) ($user['id'] ?? 0)) {
            throw new RuntimeException('Kendi ilanınıza teklif veremezsiniz.');
        }

        $now = time();
        $start = strtotime((string) $locked['start_time']);
        $end = strtotime((string) $locked['end_time']);
        if ($now < $start || $now > $end || !in_array($locked['status'], ['active', 'scheduled'], true)) {
            throw new RuntimeException('Açık artırma aktif değil.');
        }

        $minIncrement = max(0.01, (float) ($locked['min_increment'] ?: setting_get('default_min_increment', '10')));
        $minValid = (float) $locked['current_price'] + $minIncrement;
        if ($amount < $minValid) {
            throw new RuntimeException('Teklif en az ' . number_format($minValid, 2) . ' olmalıdır.');
        }

        $pdo->prepare('INSERT INTO bids (listing_id,user_id,amount,created_at) VALUES (:listing_id,:user_id,:amount,NOW())')->execute([
            'listing_id' => $id,
            'user_id' => (int) $user['id'],
            'amount' => $amount,
        ]);

        $extendWindow = (int) setting_get('extend_window_seconds', '120');
        $extendBy = (int) setting_get('extend_by_seconds', '120');
        $newEnd = $locked['end_time'];
        if (($end - $now) <= $extendWindow) {
            $newEnd = date('Y-m-d H:i:s', $end + $extendBy);
        }

        $pdo->prepare('UPDATE auctions SET current_price=:price,current_winner_id=:winner,status=\'active\',end_time=:end_time WHERE listing_id=:listing_id')->execute([
            'price' => $amount,
            'winner' => (int) $user['id'],
            'end_time' => $newEnd,
            'listing_id' => $id,
        ]);

        $pdo->commit();
        $message = 'Teklif başarıyla kaydedildi.';
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}

$ended = false;
$winnerMasked = '-';
$finalPrice = 0.0;
if ($auction) {
    $endTs = strtotime((string) $auction['end_time']);
    $ended = in_array($auction['status'], ['ended', 'ended_no_winner'], true) || ($endTs !== false && time() >= $endTs);
    $winnerMasked = masked_user_name(!empty($auction['current_winner_id']) ? (int) $auction['current_winner_id'] : null);
    $finalPrice = (float) $auction['current_price'];
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Listing</title><link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>"></head><body>
<section class="container"><div class="card">
<?php if (!$pdo): ?><p>MySQL bağlantısı yapılandırılmamış.</p>
<?php elseif (!$listing): ?><p>İlan bulunamadı veya yayında değil.</p>
<?php else: ?>
<h1><?php echo e($listing['title']); ?></h1>
<?php if ($message): ?><div class="card" style="background:#e4f9ef;color:#1f9d62"><?php echo e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="card" style="background:#ffe1e6;color:#b3283b"><?php echo e($error); ?></div><?php endif; ?>
<div class="grid" style="grid-template-columns:1fr 1fr"><div>
<?php foreach ($images as $img): ?><img class="product-main-image" src="<?php echo e($img['path']); ?>" alt="image"><?php endforeach; ?>
<p><?php echo nl2br(e($listing['description'])); ?></p></div>
<div><p><strong>Fiyat:</strong> ₺<?php echo number_format((float)$listing['price'],2); ?></p>
<?php if ($auction): ?>
<p><strong>Durum:</strong> <span id="auction-status"><?php echo e($auction['status']); ?></span></p>
<p><strong>Aktif fiyat:</strong> <span id="auction-current">₺<?php echo number_format((float)$auction['current_price'],2); ?></span></p>
<p><strong>Kalan:</strong> <span id="auction-countdown">--:--:--</span></p>
<p><strong>Bitiş:</strong> <span id="auction-end"><?php echo e((string)$auction['end_time']); ?></span></p>
<p><strong>Teklif sayısı:</strong> <span id="auction-bid-count"><?php echo count($bids); ?></span></p>
<p><strong>Lider:</strong> <span id="auction-winner"><?php echo e($winnerMasked); ?></span></p>
<?php if ($ended): ?>
<div class="card" style="background:#fff6e9;color:#8a5d1f"><strong>Auction ended.</strong> Final: ₺<?php echo number_format($finalPrice,2); ?> • Kazanan: <?php echo e($winnerMasked); ?></div>
<?php endif; ?>
<form id="bid-form" class="form" method="post" <?php echo $ended ? 'style="opacity:.6;pointer-events:none"' : ''; ?>><?php echo csrf_input(); ?><input type="hidden" name="listing_id" value="<?php echo (int)$listing['id']; ?>"><input type="number" name="amount" step="0.01" min="0" required><button class="btn btn-primary" type="submit">Teklif Ver</button></form>
<?php endif; ?></div></div>
<h3>Son Teklifler</h3>
<?php foreach ($bids as $bid): ?><div class="comment-item"><div><strong><?php echo e($bid['name']); ?></strong> — ₺<?php echo number_format((float)$bid['amount'],2); ?> <span class="muted"><?php echo e((string)$bid['created_at']); ?></span></div></div><?php endforeach; ?>
<?php endif; ?>
</div></section>
<script>
let serverNow = null;
function tickCountdown(endTime){
  const node=document.getElementById('auction-countdown'); if(!node) return;
  const end = new Date(endTime.replace(' ','T')).getTime();
  const now = serverNow ? new Date(serverNow.replace(' ','T')).getTime() : Date.now();
  let diff = Math.max(0, Math.floor((end-now)/1000));
  const d=Math.floor(diff/86400); diff%=86400; const h=Math.floor(diff/3600); diff%=3600; const m=Math.floor(diff/60); const s=diff%60;
  node.textContent = `${d}g ${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}
setInterval(async () => {
  const id = <?php echo (int)$id; ?>;
  if (!id) return;
  try {
    const res = await fetch('<?php echo url_path('pages/auction_state.php'); ?>?id=' + id, {cache:'no-store'});
    if (!res.ok) return;
    const data = await res.json();
    serverNow = data.server_time;
    if (data.current_price !== undefined) document.getElementById('auction-current').textContent = '₺' + Number(data.current_price).toFixed(2);
    if (data.end_time) document.getElementById('auction-end').textContent = data.end_time;
    if (data.status) document.getElementById('auction-status').textContent = data.status;
    if (data.bid_count !== undefined) document.getElementById('auction-bid-count').textContent = data.bid_count;
    if (data.highest_bidder_masked) document.getElementById('auction-winner').textContent = data.highest_bidder_masked;
    if (data.end_time) tickCountdown(data.end_time);
    if (data.status && ['ended','ended_no_winner','cancelled'].includes(data.status)) {
      const form = document.getElementById('bid-form');
      if (form) { form.style.opacity='.6'; form.style.pointerEvents='none'; }
    }
  } catch (_) {}
}, 1000);
</script>
</body></html>
