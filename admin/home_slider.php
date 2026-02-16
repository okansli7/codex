<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);
require_csrf();
$pdo = db();
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    try {
        $action = $_POST['action'] ?? '';
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $data = [
                'eyebrow' => trim($_POST['eyebrow'] ?? ''),
                'title' => trim($_POST['title'] ?? ''),
                'desc' => trim($_POST['desc'] ?? ''),
                'cta' => trim($_POST['cta'] ?? ''),
                'button_url' => trim($_POST['button_url'] ?? ''),
                'image_path' => trim($_POST['image_path'] ?? ''),
                'sort_order' => (int) ($_POST['sort_order'] ?? 0),
                'is_active' => !empty($_POST['is_active']) ? 1 : 0,
            ];
            try { $up = upload_image('image_file', 'slide_'); if ($up) { $data['image_path'] = $up; } } catch (Throwable $e) {}
            if ($data['title'] === '') {
                throw new RuntimeException('Başlık zorunludur.');
            }
            if ($id > 0) {
                $st = $pdo->prepare('UPDATE home_slides SET eyebrow=:eyebrow,title=:title,`desc`=:desc,cta=:cta,button_url=:button_url,image_path=:image_path,sort_order=:sort_order,is_active=:is_active WHERE id=:id');
                $st->execute($data + ['id' => $id]);
            } else {
                $st = $pdo->prepare('INSERT INTO home_slides (eyebrow,title,`desc`,cta,button_url,image_path,sort_order,is_active,created_at) VALUES (:eyebrow,:title,:desc,:cta,:button_url,:image_path,:sort_order,:is_active,NOW())');
                $st->execute($data);
                $id = (int) $pdo->lastInsertId();
            }
            log_audit('home_slide_saved', 'home_slides', $id);
            $msg = 'Slider kaydedildi.';
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM home_slides WHERE id=:id')->execute(['id' => $id]);
            log_audit('home_slide_deleted', 'home_slides', $id);
            $msg = 'Slider silindi.';
        }
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$rows = $pdo ? $pdo->query('SELECT * FROM home_slides ORDER BY sort_order ASC, id ASC')->fetchAll() : [];
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>Slider Yönetimi</title><link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>"></head><body>
<main style="margin:20px"><h1>Ana Sayfa Slider Yönetimi</h1><p><a href="<?php echo url_path('admin.php'); ?>">Admin</a></p>
<?php if ($msg): ?><p style="color:green"><?php echo e($msg); ?></p><?php endif; ?>
<?php if ($err): ?><p style="color:red"><?php echo e($err); ?></p><?php endif; ?>
<form method="post" enctype="multipart/form-data">
<?php echo csrf_input(); ?>
<input type="hidden" name="action" value="save">
<input name="eyebrow" placeholder="Eyebrow">
<input name="title" placeholder="Başlık" required>
<textarea name="desc" placeholder="Açıklama"></textarea>
<input name="cta" placeholder="Buton metni">
<input name="button_url" placeholder="Buton URL">
<input name="image_path" placeholder="Görsel URL">
<input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
<input type="number" name="sort_order" value="0">
<label><input type="checkbox" name="is_active" checked> Aktif</label>
<button>Kaydet</button>
</form>
<table><tr><th>ID</th><th>Başlık</th><th>Sıra</th><th>Aktif</th><th></th></tr>
<?php foreach ($rows as $r): ?>
<tr><td><?php echo (int) $r['id']; ?></td><td><?php echo e($r['title']); ?></td><td><?php echo (int) $r['sort_order']; ?></td><td><?php echo (int) $r['is_active']; ?></td><td><form method="post" style="display:inline"><?php echo csrf_input(); ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int) $r['id']; ?>"><button>Sil</button></form></td></tr>
<?php endforeach; ?>
</table></main></body></html>
