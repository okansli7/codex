<?php
require_once __DIR__ . '/../config.php';
$pdo = db();
$id = (int) ($_GET['id'] ?? $_POST['listing_id'] ?? 0);
$error = '';
$message = '';
$extendedMessage = '';
$listing = null;
$images = [];
$auction = null;
$bids = [];
$bidCount = 0;

if ($pdo && $id > 0) {
    $st = $pdo->prepare('SELECT * FROM listings WHERE id=:id AND status=\'published\' LIMIT 1');
    $st->execute(['id' => $id]);
    $listing = $st->fetch() ?: null;
    if ($listing) {
        $st = $pdo->prepare('SELECT path FROM listing_images WHERE listing_id=:id ORDER BY id ASC');
        $st->execute(['id' => $id]);
        $images = $st->fetchAll();

        $st = $pdo->prepare('SELECT a.*, (SELECT COUNT(*) FROM bids b WHERE b.listing_id=a.listing_id) AS bid_count FROM auctions a WHERE a.listing_id=:id LIMIT 1');
        $st->execute(['id' => $id]);
        $auction = $st->fetch() ?: null;

        $st = $pdo->prepare('SELECT b.amount,b.created_at,u.name FROM bids b JOIN users u ON u.id=b.user_id WHERE b.listing_id=:id ORDER BY b.id DESC LIMIT 20');
        $st->execute(['id' => $id]);
        $bids = $st->fetchAll();
        $bidCount = (int) ($auction['bid_count'] ?? count($bids));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_csrf();
    if (!$pdo || !$listing || !$auction) {
        $error = 'Açık artırma bulunamadı.';
    } else {
        $user = require_login();
        if (empty($user['id'])) {
            $error = 'Teklif vermek için giriş yapın.';
        } elseif ((int) $user['id'] === (int) ($listing['seller_id'] ?? 0)) {
            $error = 'Satıcı kendi ilanına teklif veremez.';
        } elseif (bid_rate_limited((int) $user['id'])) {
            $error = 'Çok hızlı teklif veriyorsunuz, lütfen bekleyin.';
            log_audit('bid_throttled', 'listing', $id);
        } else {
            try {
                $amountRaw = (float) ($_POST['amount'] ?? 0);
                if ($amountRaw <= 0) {
                    throw new RuntimeException('Teklif tutarı geçersiz.');
                }
                $amount = round($amountRaw, 2);

                $pdo->beginTransaction();
                $st = $pdo->prepare('SELECT a.*, l.seller_id,(SELECT COUNT(*) FROM bids b WHERE b.listing_id=a.listing_id) AS bid_count FROM auctions a JOIN listings l ON l.id=a.listing_id WHERE a.listing_id=:id FOR UPDATE');
                $st->execute(['id' => $id]);
                $locked = $st->fetch();
                if (!$locked) {
                    throw new RuntimeException('Açık artırma bulunamadı.');
                }
                if ((int) $locked['seller_id'] === (int) $user['id']) {
                    throw new RuntimeException('Satıcı kendi ilanına teklif veremez.');
                }

                $nowTs = time();
                $startTs = strtotime((string) $locked['start_time']) ?: 0;
                $endTs = strtotime((string) $locked['end_time']) ?: 0;
                $status = (string) $locked['status'];
                if ($nowTs < $startTs) {
                    $status = 'scheduled';
                } elseif ($nowTs >= $endTs) {
                    $status = in_array($status, ['ended_no_winner', 'cancelled'], true) ? $status : 'ended';
                } elseif (!in_array($status, ['cancelled', 'ended_no_winner'], true)) {
                    $status = 'active';
                }
                if ($status !== 'active') {
                    throw new RuntimeException('Açık artırma aktif değil.');
                }

                $bidCountLocked = (int) $locked['bid_count'];
                $minIncrement = round(max(0.01, (float) ($locked['min_increment'] ?: settings_get('default_min_increment', '10'))), 2);
                $hasBid = $bidCountLocked > 0 && !empty($locked['current_winner_id']);
                $baseMin = $hasBid ? ((float) $locked['current_price'] + $minIncrement) : (float) $locked['starting_price'];
                $minValid = round($baseMin, 2);
                if ($amount < $minValid) {
                    throw new RuntimeException('Minimum teklif: ₺' . number_format($minValid, 2, ',', '.'));
                }

                $pdo->prepare('INSERT INTO bids (listing_id,user_id,amount,created_at) VALUES (:listing_id,:user_id,:amount,NOW())')->execute([
                    'listing_id' => $id,
                    'user_id' => (int) $user['id'],
                    'amount' => $amount,
                ]);

                $extendWindow = max(0, (int) settings_get('extend_window_seconds', 120));
                $extendBy = max(0, (int) settings_get('extend_by_seconds', 120));
                $newEndTs = $endTs;
                if ($extendWindow > 0 && $extendBy > 0 && $nowTs >= ($endTs - $extendWindow)) {
                    $newEndTs = $endTs + $extendBy;
                    $extendedMessage = 'Süre uzatıldı.';
                }

                $pdo->prepare('UPDATE auctions SET current_price=:price,current_winner_id=:winner,status=\'active\',end_time=:end_time WHERE listing_id=:listing_id')
                    ->execute([
                        'price' => $amount,
                        'winner' => (int) $user['id'],
                        'end_time' => date('Y-m-d H:i:s', $newEndTs),
                        'listing_id' => $id,
                    ]);

                $pdo->commit();
                $message = 'Teklif başarıyla kaydedildi.';
                log_audit('bid_placed', 'listing', $id);
            } catch (Throwable $e) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $error = $e->getMessage();
            }
        }
    }
}

$auctionStatus = 'scheduled';
$currentPrice = 0.0;
$minIncrement = (float) settings_get('default_min_increment', '10');
$minValidBid = null;
$winnerMasked = null;
$ended = true;
if ($auction) {
    $nowTs = time();
    $startTs = strtotime((string) $auction['start_time']) ?: 0;
    $endTs = strtotime((string) $auction['end_time']) ?: 0;
    $auctionStatus = (string) $auction['status'];
    if ($nowTs < $startTs) {
        $auctionStatus = 'scheduled';
    } elseif ($nowTs >= $endTs) {
        $auctionStatus = in_array($auctionStatus, ['ended_no_winner', 'cancelled'], true) ? $auctionStatus : 'ended';
    } elseif (!in_array($auctionStatus, ['cancelled', 'ended_no_winner'], true)) {
        $auctionStatus = 'active';
    }

    $currentPrice = (float) $auction['current_price'];
    $minIncrement = round(max(0.01, (float) ($auction['min_increment'] ?: settings_get('default_min_increment', '10'))), 2);
    $hasBid = ((int) ($auction['bid_count'] ?? 0) > 0) && !empty($auction['current_winner_id']);
    if ($auctionStatus === 'active') {
        $minValidBid = $hasBid ? round($currentPrice + $minIncrement, 2) : round((float) $auction['starting_price'], 2);
    }
    $ended = in_array($auctionStatus, ['ended', 'ended_no_winner', 'cancelled'], true);
    if ($ended && !empty($auction['current_winner_id'])) {
        $winnerMasked = masked_user_name((int) $auction['current_winner_id']);
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | İlan Detayı</title>
    <link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>" />
</head>
<body>
<section class="container">
    <div class="card">
        <?php if (!$pdo): ?>
            <p>MySQL bağlantısı yapılandırılmamış.</p>
        <?php elseif (!$listing): ?>
            <p>İlan bulunamadı veya yayında değil.</p>
        <?php else: ?>
            <h1><?php echo e($listing['title']); ?></h1>
            <?php if ($message): ?><div class="card" style="background:#e4f9ef;color:#1f9d62"><?php echo e($message); ?></div><?php endif; ?>
            <?php if ($extendedMessage): ?><div class="card" style="background:#fff6e9;color:#8a5d1f"><?php echo e($extendedMessage); ?></div><?php endif; ?>
            <?php if ($error): ?><div class="card" style="background:#ffe1e6;color:#b3283b"><?php echo e($error); ?></div><?php endif; ?>

            <div class="grid" style="grid-template-columns:1fr 1fr">
                <div>
                    <?php foreach ($images as $img): ?><img class="product-main-image" src="<?php echo e($img['path']); ?>" alt="image" /><?php endforeach; ?>
                    <p><?php echo nl2br(e((string) $listing['description'])); ?></p>
                </div>
                <div>
                    <h2 style="font-size:2rem;margin:0 0 8px">₺<span id="auction-current"><?php echo number_format($currentPrice, 2, '.', ''); ?></span></h2>
                    <p><strong>Durum:</strong> <span id="auction-status"><?php echo e($auctionStatus); ?></span></p>
                    <p><strong>Kalan Süre:</strong> <span id="auction-countdown">--:--:--</span></p>
                    <p><strong>Bitiş:</strong> <span id="auction-end"><?php echo e((string) ($auction['end_time'] ?? '')); ?></span></p>
                    <p><strong>Teklif Sayısı:</strong> <span id="auction-bid-count"><?php echo (int) $bidCount; ?></span></p>
                    <p><strong>Min. Artış:</strong> ₺<span id="auction-min-increment"><?php echo number_format($minIncrement, 2, '.', ''); ?></span></p>
                    <p><strong>Geçerli Min. Teklif:</strong> <span id="auction-min-valid"><?php echo $minValidBid !== null ? '₺' . number_format($minValidBid, 2, '.', '') : '-'; ?></span></p>
                    <?php if ($ended): ?>
                        <div class="card" style="background:#fff6e9;color:#8a5d1f">
                            <strong>Açık artırma bitti.</strong><br>
                            Final Fiyat: ₺<?php echo number_format($currentPrice, 2, ',', '.'); ?><br>
                            Kazanan: <span id="auction-winner"><?php echo e($winnerMasked ?? '-'); ?></span>
                        </div>
                    <?php else: ?>
                        <p><strong>Lider:</strong> <span id="auction-winner"><?php echo e($winnerMasked ?? '-'); ?></span></p>
                    <?php endif; ?>

                    <form id="bid-form" class="form" method="post" <?php echo in_array($auctionStatus, ['scheduled', 'ended', 'ended_no_winner', 'cancelled'], true) ? 'style="opacity:.6;pointer-events:none"' : ''; ?>>
                        <?php echo csrf_input(); ?>
                        <input type="hidden" name="listing_id" value="<?php echo (int) $id; ?>" />
                        <input type="number" name="amount" step="0.01" min="0" required placeholder="Teklif tutarı" />
                        <button class="btn btn-primary" type="submit">Teklif Ver</button>
                    </form>
                </div>
            </div>

            <h3>Teklif Geçmişi</h3>
            <?php foreach ($bids as $bid): ?>
                <div class="comment-item">
                    <div><strong><?php echo e(mask_name((string) $bid['name'])); ?></strong> — ₺<?php echo number_format((float) $bid['amount'], 2, ',', '.'); ?> <span class="muted"><?php echo e((string) $bid['created_at']); ?></span></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
<script>
let serverNow = null;
function updateCountdown(endTime, serverTime) {
  const node = document.getElementById('auction-countdown');
  if (!node || !endTime || !serverTime) return;
  const end = new Date(endTime.replace(' ', 'T')).getTime();
  const now = new Date(serverTime.replace(' ', 'T')).getTime();
  let diff = Math.max(0, Math.floor((end - now) / 1000));
  const d = Math.floor(diff / 86400); diff %= 86400;
  const h = Math.floor(diff / 3600); diff %= 3600;
  const m = Math.floor(diff / 60); const s = diff % 60;
  node.textContent = `${d}g ${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
}

setInterval(async () => {
  const id = <?php echo (int) $id; ?>;
  if (!id) return;
  try {
    const res = await fetch('<?php echo url_path('pages/auction_state.php'); ?>?id=' + id, { cache: 'no-store' });
    if (!res.ok) return;
    const data = await res.json();
    serverNow = data.server_time || serverNow;
    if (data.current_price !== undefined) document.getElementById('auction-current').textContent = Number(data.current_price).toFixed(2);
    if (data.end_time) document.getElementById('auction-end').textContent = data.end_time;
    if (data.status) document.getElementById('auction-status').textContent = data.status;
    if (data.bid_count !== undefined) document.getElementById('auction-bid-count').textContent = data.bid_count;
    if (data.min_increment !== undefined) document.getElementById('auction-min-increment').textContent = Number(data.min_increment).toFixed(2);
    document.getElementById('auction-min-valid').textContent = data.min_valid_bid ? ('₺' + Number(data.min_valid_bid).toFixed(2)) : '-';
    document.getElementById('auction-winner').textContent = data.winner_masked || '-';
    updateCountdown(data.end_time, data.server_time || serverNow);

    const form = document.getElementById('bid-form');
    if (form && data.status && ['scheduled','ended','ended_no_winner','cancelled'].includes(data.status)) {
      form.style.opacity = '.6';
      form.style.pointerEvents = 'none';
    }
  } catch (e) {}
}, 3000);
</script>
</body>
</html>
