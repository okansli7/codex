<?php
require_once __DIR__ . '/../config.php';
if (!is_moderator() && !is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$categories = [
    ['name' => 'Koleksiyon', 'status' => 'Aktif'],
    ['name' => 'Sanat', 'status' => 'Aktif'],
    ['name' => 'Teknoloji', 'status' => 'Aktif'],
    ['name' => 'Moda', 'status' => 'Askıda'],
];
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Kategori Yönetimi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>" />
</head>
<body>
<aside>
    <div class="brand"><span>A</span>Artirup Moderasyon</div>
    <div class="menu">
        <a href="<?php echo url_path('moderator/index.php'); ?>">Genel Bakış</a>
        <a href="<?php echo url_path('admin/auctions.php'); ?>">Açık Artırmalar</a>
        <a class="active" href="<?php echo url_path('moderator/categories.php'); ?>">Kategoriler</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Kategori Yönetimi</h2>
            <p style="color: var(--muted); margin: 0;">Kategorileri ekle, düzenle veya askıya al.</p>
        </div>
        <input type="search" placeholder="Kategori ara..." />
        <a class="btn btn-primary" href="#">Yeni kategori</a>
    </div>

    <div class="panel">
        <table>
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo $category['name']; ?></td>
                        <td>
                            <span class="status <?php echo $category['status'] === 'Aktif' ? 'ok' : 'wait'; ?>">
                                <?php echo $category['status']; ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-outline" href="#">Düzenle</a>
                                <a class="btn btn-primary" href="#">Fiyat güncelle</a>
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
