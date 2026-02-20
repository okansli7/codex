<?php
require_once __DIR__ . '/../config.php';
$user = require_role(['seller', 'admin']);
require_csrf();
$pdo = db();
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    try {
        $type = ($_POST['type'] ?? 'fixed_price') === 'auction' ? 'auction' : 'fixed_price';
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = max(0, (float) ($_POST['price'] ?? 0));
        if ($title === '' || $description === '') {
            throw new RuntimeException('Başlık ve açıklama zorunludur.');
        }

        $pdo->beginTransaction();
        $slug = slugify($title . '-' . bin2hex(random_bytes(2)));
        $st = $pdo->prepare('INSERT INTO listings (seller_id,type,title,slug,description,price,status,created_at,updated_at) VALUES (:sid,:type,:title,:slug,:description,:price,\'pending\',NOW(),NOW())');
        $st->execute([
            'sid' => (int) $user['id'],
            'type' => $type,
            'title' => $title,
            'slug' => $slug,
            'description' => $description,
            'price' => $price,
        ]);
        $listingId = (int) $pdo->lastInsertId();

        $main = upload_image('image', 'listing_');
        if ($main) {
            $st = $pdo->prepare('INSERT INTO listing_images (listing_id,path,created_at) VALUES (:lid,:path,NOW())');
            $st->execute(['lid' => $listingId, 'path' => $main]);
        }

        if (!empty($_FILES['gallery']['name']) && is_array($_FILES['gallery']['name'])) {
            foreach (array_keys($_FILES['gallery']['name']) as $i) {
                $_FILES['gallery_one'] = [
                    'name' => $_FILES['gallery']['name'][$i] ?? '',
                    'type' => $_FILES['gallery']['type'][$i] ?? '',
                    'tmp_name' => $_FILES['gallery']['tmp_name'][$i] ?? '',
                    'error' => $_FILES['gallery']['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                    'size' => $_FILES['gallery']['size'][$i] ?? 0,
                ];
                $path = upload_image('gallery_one', 'listing_');
                if ($path) {
                    $st = $pdo->prepare('INSERT INTO listing_images (listing_id,path,created_at) VALUES (:lid,:path,NOW())');
                    $st->execute(['lid' => $listingId, 'path' => $path]);
                }
            }
            unset($_FILES['gallery_one']);
        }

        if ($type === 'auction') {
            $start = trim($_POST['start_time'] ?? '');
            $end = trim($_POST['end_time'] ?? '');
            $starting = max(0, (float) ($_POST['starting_price'] ?? $price));
            $minInc = max(1, (float) ($_POST['min_increment'] ?? 1));
            $st = $pdo->prepare('INSERT INTO auctions (listing_id,start_time,end_time,starting_price,min_increment,current_price,status) VALUES (:lid,:start,:end,:starting,:inc,:starting,\'scheduled\')');
            $st->execute([
                'lid' => $listingId,
                'start' => $start !== '' ? date('Y-m-d H:i:s', strtotime($start)) : date('Y-m-d H:i:s'),
                'end' => $end !== '' ? date('Y-m-d H:i:s', strtotime($end)) : date('Y-m-d H:i:s', strtotime('+7 days')),
                'starting' => $starting,
                'inc' => $minInc,
            ]);
        }

        $pdo->commit();
        $message = 'İlan kaydedildi ve moderasyon için beklemeye alındı.';
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $e->getMessage();
    }
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>İlan Ekle</title><link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>"></head><body>
<section class="container"><div class="card"><h1>İlan Ekle</h1><p><a class="btn btn-outline" href="<?php echo url_path('seller/my-listings.php'); ?>">İlanlarım</a></p>
<?php if ($message): ?><div class="card" style="background:#e4f9ef;color:#1f9d62"><?php echo e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="card" style="background:#ffe1e6;color:#b3283b"><?php echo e($error); ?></div><?php endif; ?>
<form class="form" method="post" enctype="multipart/form-data"><?php echo csrf_input(); ?>
<select name="type"><option value="fixed_price">Sabit Fiyat</option><option value="auction">Açık Artırma</option></select>
<input name="title" placeholder="Başlık" required>
<textarea name="description" placeholder="Açıklama" required></textarea>
<input type="number" name="price" min="0" step="0.01" placeholder="Fiyat">
<label>Ana görsel</label><input type="file" name="image" accept="image/jpeg,image/png,image/webp">
<label>Galeri</label><input type="file" name="gallery[]" accept="image/jpeg,image/png,image/webp" multiple>
<label>Auction başlangıç</label><input type="datetime-local" name="start_time">
<label>Auction bitiş</label><input type="datetime-local" name="end_time">
<input type="number" name="starting_price" min="0" step="0.01" placeholder="Başlangıç fiyatı">
<input type="number" name="min_increment" min="1" step="0.01" placeholder="Minimum artış">
<button class="btn btn-primary" type="submit">Kaydet</button></form></div></section>
</body></html>
