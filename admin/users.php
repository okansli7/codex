<?php
require_once __DIR__ . '/../config.php';
if (!is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$users = [
    ['name' => 'Ayşe Karaca', 'email' => 'ayse@artirup.com', 'role' => 'Admin', 'status' => 'Aktif'],
    ['name' => 'Kemal Yılmaz', 'email' => 'kemal@artirup.com', 'role' => 'Moderatör', 'status' => 'Aktif'],
    ['name' => 'Elif Demir', 'email' => 'elif@artirup.com', 'role' => 'Kullanıcı', 'status' => 'Askıda'],
    ['name' => 'Mert Aslan', 'email' => 'mert@artirup.com', 'role' => 'Moderatör', 'status' => 'Aktif'],
    ['name' => 'Selin Koç', 'email' => 'selin@artirup.com', 'role' => 'Kullanıcı', 'status' => 'Aktif'],
];
$countries = country_options();
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Kullanıcı Yönetimi</title>
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
        <a class="active" href="<?php echo url_path('admin/users.php'); ?>">Kullanıcılar</a>
        <a href="<?php echo url_path('admin/moderators.php'); ?>">Moderatörler</a>
        <a href="#">Raporlar</a>
        <a href="<?php echo url_path('admin/settings.php'); ?>">Ayarlar</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Kullanıcı Yönetimi</h2>
            <p style="color: var(--muted); margin: 0;">Tüm kullanıcıları incele, rol ve durumlarını yönet.</p>
        </div>
        <input type="search" placeholder="Kullanıcı ara..." />
        <a class="btn btn-primary" href="#">Yeni kullanıcı ekle</a>
    </div>

    <div class="panel">
        <h3>Kullanıcı Düzenle</h3>
        <form class="form" method="post">
            <input type="text" placeholder="İsim Soyisim" />
            <select>
                <?php foreach ($countries as $code => $country): ?>
                    <option value="<?php echo $code; ?>">
                        <?php echo $country['flag'] . ' ' . $country['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="actions">
                <button class="btn btn-primary" type="button">Güncelle</button>
                <button class="btn btn-outline" type="button">Sıfırla</button>
            </div>
        </form>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <table>
            <thead>
                <tr>
                    <th>Ad Soyad</th>
                    <th>E-posta</th>
                    <th>Ülke</th>
                    <th>Rol</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $countries['TR']['flag']; ?> <?php echo $countries['TR']['name']; ?></td>
                        <td>
                            <span class="badge <?php echo $user['role'] === 'Admin' ? 'admin' : ($user['role'] === 'Moderatör' ? 'moderator' : 'user'); ?>">
                                <?php echo $user['role']; ?>
                            </span>
                        </td>
                        <td>
                            <span class="status <?php echo $user['status'] === 'Aktif' ? 'ok' : 'wait'; ?>">
                                <?php echo $user['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-outline" href="#">Düzenle</a>
                                <a class="btn btn-primary" href="#">Yetki ver</a>
                                <a class="btn btn-danger" href="#">Sil</a>
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
