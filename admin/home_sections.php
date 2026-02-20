<?php
require_once __DIR__ . '/../config.php';
require_role(['admin']);
require_csrf();
$pdo = db();
$msg=''; $err='';
$types = ['ending_soon_auctions','new_listings','categories_grid','banner','html_block'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    try {
        $action = $_POST['action'] ?? '';
        if ($action === 'save') {
            $id = (int) ($_POST['id'] ?? 0);
            $type = $_POST['type'] ?? 'new_listings';
            if (!in_array($type, $types, true)) {
                $type = 'new_listings';
            }
            $title = trim($_POST['title'] ?? '');
            $settings = trim($_POST['settings_json'] ?? '{}');
            json_decode($settings, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new RuntimeException('settings_json geçerli JSON olmalı.');
            }
            $sort = (int) ($_POST['sort_order'] ?? 0);
            $active = !empty($_POST['is_active']) ? 1 : 0;

            if ($id > 0) {
                $st = $pdo->prepare('UPDATE home_sections SET type=:type,title=:title,settings_json=:settings_json,sort_order=:sort_order,is_active=:is_active WHERE id=:id');
                $st->execute(['type'=>$type,'title'=>$title,'settings_json'=>$settings,'sort_order'=>$sort,'is_active'=>$active,'id'=>$id]);
            } else {
                $st = $pdo->prepare('INSERT INTO home_sections (type,title,settings_json,sort_order,is_active,created_at) VALUES (:type,:title,:settings_json,:sort_order,:is_active,NOW())');
                $st->execute(['type'=>$type,'title'=>$title,'settings_json'=>$settings,'sort_order'=>$sort,'is_active'=>$active]);
                $id = (int) $pdo->lastInsertId();
            }
            log_audit('home_section_saved', 'home_sections', $id);
            $msg = 'Section kaydedildi.';
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM home_sections WHERE id=:id')->execute(['id' => $id]);
            log_audit('home_section_deleted', 'home_sections', $id);
            $msg = 'Section silindi.';
        }
    } catch (Throwable $e) {
        $err = $e->getMessage();
    }
}

$rows = $pdo ? $pdo->query('SELECT * FROM home_sections ORDER BY sort_order ASC, id ASC')->fetchAll() : [];
?>
<!doctype html><html lang="tr"><head><meta charset="utf-8"><title>Bölüm Yönetimi</title><link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>"></head><body>
<main style="margin:20px"><h1>Ana Sayfa Bölümleri</h1><p><a href="<?php echo url_path('admin.php'); ?>">Admin</a></p>
<?php if ($msg): ?><p style="color:green"><?php echo e($msg); ?></p><?php endif; ?>
<?php if ($err): ?><p style="color:red"><?php echo e($err); ?></p><?php endif; ?>
<form method="post"><?php echo csrf_input(); ?>
<input type="hidden" name="action" value="save">
<select name="type"><?php foreach($types as $t): ?><option value="<?php echo e($t); ?>"><?php echo e($t); ?></option><?php endforeach; ?></select>
<input name="title" placeholder="Başlık" required>
<textarea name="settings_json" placeholder='{"limit":6}'></textarea>
<input type="number" name="sort_order" value="0">
<label><input type="checkbox" name="is_active" checked> Aktif</label>
<button>Kaydet</button>
</form>
<table><tr><th>ID</th><th>Tür</th><th>Başlık</th><th>Sıra</th><th>Aktif</th><th></th></tr>
<?php foreach($rows as $r): ?><tr><td><?php echo (int)$r['id']; ?></td><td><?php echo e($r['type']); ?></td><td><?php echo e($r['title']); ?></td><td><?php echo (int)$r['sort_order']; ?></td><td><?php echo (int)$r['is_active']; ?></td><td><form method="post" style="display:inline"><?php echo csrf_input(); ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?php echo (int)$r['id']; ?>"><button>Sil</button></form></td></tr><?php endforeach; ?>
</table></main></body></html>
