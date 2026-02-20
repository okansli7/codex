<?php
require_once __DIR__ . '/../config.php';
if (!is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$moderators = [
    ['name' => 'Kemal Yılmaz', 'area' => 'Açık Artırmalar', 'permissions' => 'Düzenle / Sil'],
    ['name' => 'Mert Aslan', 'area' => 'Lot İnceleme', 'permissions' => 'Onay / Reddet'],
    ['name' => 'Zeynep Altın', 'area' => 'Kullanıcı Destek', 'permissions' => 'Düzenle / Askıya Al'],
];
$requests = [
    ['name' => 'Sena U.', 'role' => 'Moderatör', 'status' => 'Onay bekliyor'],
    ['name' => 'Arda K.', 'role' => 'Moderatör', 'status' => 'Onay bekliyor'],
];
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Moderatör Yönetimi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>" />
</head>
<body>
<aside>
    <div class="brand"><span>A</span>Artirup Admin</div>
    <div class="menu">
        <a href="<?php echo url_path('admin.php'); ?>">Genel Bakış</a>
        <a href="<?php echo url_path('admin/auctions.php'); ?>">Açık Artırmalar</a>
        <a href="<?php echo url_path('admin/users.php'); ?>">Kullanıcılar</a>
        <a class="active" href="<?php echo url_path('admin/moderators.php'); ?>">Moderatörler</a>
        <a href="#">Raporlar</a>
        <a href="<?php echo url_path('admin/settings.php'); ?>">Ayarlar</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Moderatör Yetkileri</h2>
            <p style="color: var(--muted); margin: 0;">Adminin yetki verdiği moderatörleri yönet.</p>
        </div>
        <input type="search" placeholder="Moderatör ara..." />
        <a class="btn btn-primary" href="#">Moderatör ekle</a>
    </div>

    <div class="panel">
        <h3>Aktif Moderatörler</h3>
        <div class="card-list">
            <?php foreach ($moderators as $moderator): ?>
                <div class="lot-item">
                    <div>
                        <strong><?php echo $moderator['name']; ?></strong>
                        <div style="color: var(--muted);">Alan: <?php echo $moderator['area']; ?></div>
                    </div>
                    <div style="text-align: right;">
                        <div class="badge moderator"><?php echo $moderator['permissions']; ?></div>
                        <div class="actions" style="margin-top: 10px;">
                            <a class="btn btn-outline" href="#">Düzenle</a>
                            <a class="btn btn-danger" href="#">Sil</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>Yetki Talepleri</h3>
        <table>
            <thead>
                <tr>
                    <th>İsim</th>
                    <th>Rol</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $request): ?>
                    <tr>
                        <td><?php echo $request['name']; ?></td>
                        <td><?php echo $request['role']; ?></td>
                        <td><span class="status wait"><?php echo $request['status']; ?></span></td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-primary" href="#">Onayla</a>
                                <a class="btn btn-danger" href="#">Reddet</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
