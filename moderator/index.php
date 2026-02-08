<?php
require_once __DIR__ . '/../config.php';
if (!is_moderator() && !is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$tasks = [
    'Yeni açık artırma içeriklerini kontrol et',
    'Şikayet edilen lotları incele',
    'Kategorileri güncelle',
];
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Moderatör Paneli</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>" />
</head>
<body>
<aside>
    <div class="brand"><span>A</span>Artirup Moderasyon</div>
    <div class="menu">
        <a class="active" href="<?php echo url_path('moderator/index.php'); ?>">Genel Bakış</a>
        <a href="<?php echo url_path('admin/auctions.php'); ?>">Açık Artırmalar</a>
        <a href="<?php echo url_path('moderator/categories.php'); ?>">Kategoriler</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Moderatör Paneli</h2>
            <p style="color: var(--muted); margin: 0;">Açık artırmaları düzenle ve içerikleri kontrol et.</p>
        </div>
        <input type="search" placeholder="Görev veya lot ara..." />
    </div>

    <div class="panel">
        <h3>Günlük Görevler</h3>
        <div class="card-list">
            <?php foreach ($tasks as $task): ?>
                <div class="lot-item">
                    <div><?php echo $task; ?></div>
                    <div class="actions">
                        <a class="btn btn-outline" href="#">İncele</a>
                        <a class="btn btn-primary" href="#">Tamamla</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</main>
</body>
</html>
