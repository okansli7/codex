<?php
require_once __DIR__ . '/../config.php';
$user = require_role(['seller', 'admin']);
require_csrf();
$pdo = db();
$id = (int) ($_GET['id'] ?? $_POST['id'] ?? 0);
$error = '';
$message = '';
$listing = null;

if ($pdo && $id > 0) {
    $st = $pdo->prepare('SELECT * FROM listings WHERE id=:id AND seller_id=:sid LIMIT 1');
    $st->execute(['id' => $id, 'sid' => (int) $user['id']]);
    $listing = $st->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo && $listing) {
    try {
        $title = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = max(0, (float) ($_POST['price'] ?? 0));
        $st = $pdo->prepare('UPDATE listings SET title=:title, description=:description, price=:price, status=\'pending\', updated_at=NOW() WHERE id=:id AND seller_id=:sid');
        $st->execute(['title' => $title, 'description' => $description, 'price' => $price, 'id' => $id, 'sid' => (int) $user['id']]);

        $img = upload_image('image', 'listing_');
        if ($img) {
            $st = $pdo->prepare('INSERT INTO listing_images (listing_id,path,created_at) VALUES (:lid,:path,NOW())');
            $st->execute(['lid' => $id, 'path' => $img]);
        }
        $message = 'İlan güncellendi (yeniden moderasyona gönderildi).';
    } catch (Throwable $e) {
        $error = $e->getMessage();
    }
}
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>İlan Düzenle</title><link rel="stylesheet" href="<?php echo url_path('assets/css/secondary.css'); ?>"></head><body><section class="container"><div class="card"><h1>İlan Düzenle</h1>
<?php if (!$listing): ?><p>İlan bulunamadı.</p><?php else: ?>
<?php if ($message): ?><div class="card" style="background:#e4f9ef;color:#1f9d62"><?php echo e($message); ?></div><?php endif; ?>
<?php if ($error): ?><div class="card" style="background:#ffe1e6;color:#b3283b"><?php echo e($error); ?></div><?php endif; ?>
<form class="form" method="post" enctype="multipart/form-data"><?php echo csrf_input(); ?>
<input type="hidden" name="id" value="<?php echo (int)$listing['id']; ?>">
<input name="title" value="<?php echo e($listing['title']); ?>" required>
<textarea name="description" required><?php echo e($listing['description']); ?></textarea>
<input type="number" name="price" min="0" step="0.01" value="<?php echo e((string)$listing['price']); ?>">
<input type="file" name="image" accept="image/jpeg,image/png,image/webp">
<button class="btn btn-primary" type="submit">Güncelle</button></form><?php endif; ?></div></section></body></html>
