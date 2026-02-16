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
    if (role_slug($user['role'] ?? '') === 'seller' && ((int) ($listing['seller_id'] ?? 0) === (int) ($user['id'] ?? 0))) {
        $error = 'Kendi ilanınıza teklif veremezsiniz.';
    } else {
        try {
            $amount = (float) ($_POST['amount'] ?? 0);
            $pdo->beginTransaction();
            $st = $pdo->prepare('SELECT a.*, l.seller_id FROM auctions a JOIN listings l ON l.id=a.listing_id WHERE a.listing_id=:id FOR UPDATE');
            $st->execute(['id' => $id]);
            $locked = $st->fetch();
            if (!$locked) {
                throw new RuntimeException('Açık artırma bulunamadı.');
            }

            $now = time();
            $start = strtotime((string) $locked['start_time']);
            $end = strtotime((string) $locked['end_time']);
            if (($locked['status'] !== 'active' && $locked['status'] !== 'scheduled') || $now < $start || $now > $end) {
                throw new RuntimeException('Açık artırma aktif değil.');
            }

            $minValid = (float) $locked['current_price'] + (float) $locked['min_increment'];
            if ($amount < $minValid) {
                throw new RuntimeException('Teklif en az ' . number_format($minValid, 2) . ' olmalıdır.');
            }

            $pdo->prepare('INSERT INTO bids (listing_id,user_id,amount,created_at) VALUES (:listing_id,:user_id,:amount,NOW())')->execute([
                'listing_id' => $id,
                'user_id' => (int) $user['id'],
                'amount' => $amount,
            ]);

            $pdo->prepare('UPDATE auctions SET current_price=:price,current_winner_id=:winner,status=\'active\' WHERE listing_id=:listing_id')->execute([
                'price' => $amount,
                'winner' => (int) $user['id'],
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
<p><strong>Aktif fiyat:</strong> <span id="auction-current">₺<?php echo number_format((float)$auction['current_price'],2); ?></span></p>
<p><strong>Bitiş:</strong> <span id="auction-end"><?php echo e((string)$auction['end_time']); ?></span></p>
<form class="form" method="post"><?php echo csrf_input(); ?><input type="hidden" name="listing_id" value="<?php echo (int)$listing['id']; ?>"><input type="number" name="amount" step="0.01" min="0" required><button class="btn btn-primary" type="submit">Teklif Ver</button></form>
<?php endif; ?></div></div>
<h3>Son Teklifler</h3>
<?php foreach ($bids as $bid): ?><div class="comment-item"><div><strong><?php echo e($bid['name']); ?></strong> — ₺<?php echo number_format((float)$bid['amount'],2); ?> <span class="muted"><?php echo e((string)$bid['created_at']); ?></span></div></div><?php endforeach; ?>
<?php endif; ?>
</div></section>
<script>
setInterval(async () => {
  const id = <?php echo (int)$id; ?>;
  if (!id) return;
  try {
    const res = await fetch('<?php echo url_path('pages/auction_state.php'); ?>?id=' + id);
    if (!res.ok) return;
    const data = await res.json();
    if (data.current_price) document.getElementById('auction-current').textContent = '₺' + Number(data.current_price).toFixed(2);
    if (data.end_time) document.getElementById('auction-end').textContent = data.end_time;
  } catch (_) {}
}, 3000);
</script>
</body></html>
