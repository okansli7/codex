<?php
require_once __DIR__ . '/../config.php';
if (!is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$message = '';
if (!isset($_SESSION['admin_users'])) {
    $_SESSION['admin_users'] = [
        [
            'first_name' => 'Ayşe',
            'last_name' => 'Karaca',
            'email' => 'ayse@artirup.com',
            'phone' => '+90 532 222 3344',
            'country' => 'TR',
            'role' => 'Admin',
            'status' => 'Aktif',
        ],
        [
            'first_name' => 'Kemal',
            'last_name' => 'Yılmaz',
            'email' => 'kemal@artirup.com',
            'phone' => '+90 533 123 4455',
            'country' => 'TR',
            'role' => 'Moderatör',
            'status' => 'Aktif',
        ],
        [
            'first_name' => 'Elif',
            'last_name' => 'Demir',
            'email' => 'elif@artirup.com',
            'phone' => '+90 534 889 6677',
            'country' => 'TR',
            'role' => 'Kullanıcı',
            'status' => 'Askıda',
        ],
        [
            'first_name' => 'Mert',
            'last_name' => 'Aslan',
            'email' => 'mert@artirup.com',
            'phone' => '+90 535 777 9900',
            'country' => 'TR',
            'role' => 'Moderatör',
            'status' => 'Aktif',
        ],
        [
            'first_name' => 'Selin',
            'last_name' => 'Koç',
            'email' => 'selin@artirup.com',
            'phone' => '+90 536 444 5566',
            'country' => 'TR',
            'role' => 'Kullanıcı',
            'status' => 'Aktif',
        ],
    ];
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'update_user') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['admin_users'][$index])) {
            $_SESSION['admin_users'][$index]['first_name'] = trim($_POST['first_name'] ?? $_SESSION['admin_users'][$index]['first_name']);
            $_SESSION['admin_users'][$index]['last_name'] = trim($_POST['last_name'] ?? $_SESSION['admin_users'][$index]['last_name']);
            $_SESSION['admin_users'][$index]['email'] = trim($_POST['email'] ?? $_SESSION['admin_users'][$index]['email']);
            $_SESSION['admin_users'][$index]['phone'] = trim($_POST['phone'] ?? $_SESSION['admin_users'][$index]['phone']);
            $_SESSION['admin_users'][$index]['country'] = trim($_POST['country'] ?? $_SESSION['admin_users'][$index]['country']);
            $_SESSION['admin_users'][$index]['role'] = trim($_POST['role'] ?? $_SESSION['admin_users'][$index]['role']);
            $_SESSION['admin_users'][$index]['status'] = trim($_POST['status'] ?? $_SESSION['admin_users'][$index]['status']);
            $message = 'Kullanıcı bilgileri güncellendi.';
        }
    }
}
$users = $_SESSION['admin_users'];
$countries = country_options();
$editIndex = (int) ($_GET['edit'] ?? -1);
$editUser = $users[$editIndex] ?? null;
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

    <?php if ($message): ?>
        <div class="panel" style="background: #e4f9ef; color: #1f9d62;">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="panel">
        <h3>Kullanıcı Düzenle</h3>
        <form class="form" method="post">
            <input type="hidden" name="action" value="update_user" />
            <input type="hidden" name="index" value="<?php echo $editIndex >= 0 ? $editIndex : 0; ?>" />
            <input type="text" name="first_name" placeholder="İsim" value="<?php echo htmlspecialchars($editUser['first_name'] ?? ''); ?>" />
            <input type="text" name="last_name" placeholder="Soyisim" value="<?php echo htmlspecialchars($editUser['last_name'] ?? ''); ?>" />
            <input type="email" name="email" placeholder="E-posta" value="<?php echo htmlspecialchars($editUser['email'] ?? ''); ?>" />
            <input type="text" name="phone" placeholder="Telefon" value="<?php echo htmlspecialchars($editUser['phone'] ?? ''); ?>" />
            <select name="country">
                <?php foreach ($countries as $code => $country): ?>
                    <option value="<?php echo $code; ?>" <?php echo ($editUser && $editUser['country'] === $code) ? 'selected' : ''; ?>>
                        <?php echo $country['flag'] . ' ' . $country['name']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="role">
                <?php foreach (['Admin', 'Moderatör', 'Kullanıcı', 'Satıcı'] as $role): ?>
                    <option value="<?php echo $role; ?>" <?php echo ($editUser && $editUser['role'] === $role) ? 'selected' : ''; ?>>
                        <?php echo $role; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <select name="status">
                <?php foreach (['Aktif', 'Askıda', 'Pasif'] as $status): ?>
                    <option value="<?php echo $status; ?>" <?php echo ($editUser && $editUser['status'] === $status) ? 'selected' : ''; ?>>
                        <?php echo $status; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="actions">
                <button class="btn btn-primary" type="submit">Güncelle</button>
                <a class="btn btn-outline" href="<?php echo url_path('admin/users.php'); ?>">Sıfırla</a>
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
                <?php foreach ($users as $index => $user): ?>
                    <?php $fullName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')); ?>
                    <tr>
                        <td><?php echo htmlspecialchars($fullName); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <?php $country = $countries[$user['country']] ?? null; ?>
                            <?php if ($country): ?>
                                <?php echo $country['flag']; ?> <?php echo $country['name']; ?>
                            <?php endif; ?>
                        </td>
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
                                <a class="btn btn-outline" href="<?php echo url_path('admin/users.php?edit=' . $index); ?>">Düzenle</a>
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
