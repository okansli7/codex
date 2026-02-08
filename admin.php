<?php
require_once __DIR__ . '/config.php';
if (!is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$kpis = [
    ['label' => 'Aktif artırmalar', 'value' => '248'],
    ['label' => 'Bugün kapanan lot', 'value' => '1.420'],
    ['label' => 'Toplam teklif', 'value' => '₺12.4M'],
    ['label' => 'Yeni kullanıcı', 'value' => '542'],
];

$recentBids = [
    ['lot' => 'Vintage Camera Set', 'user' => 'Ayşe T.', 'amount' => '₺2.450', 'status' => 'Onaylandı'],
    ['lot' => 'Retro Console 2030', 'user' => 'Kemal Y.', 'amount' => '₺6.700', 'status' => 'Beklemede'],
    ['lot' => 'Art Deco Lamp', 'user' => 'Selin K.', 'amount' => '₺1.280', 'status' => 'Onaylandı'],
    ['lot' => 'Crypto Sculpture', 'user' => 'Deniz A.', 'amount' => '₺9.100', 'status' => 'Reddedildi'],
];

$lots = [
    ['name' => 'Holo Watch Series', 'seller' => 'Nova Tech', 'bids' => '58', 'end' => '12 Mar 21:00'],
    ['name' => 'Neo Art Canvas', 'seller' => 'Studio 55', 'bids' => '112', 'end' => '12 Mar 23:30'],
    ['name' => 'Retro Game Pack', 'seller' => 'PixelWorks', 'bids' => '76', 'end' => '13 Mar 20:15'],
    ['name' => 'Luxury Car Slot', 'seller' => 'DriveX', 'bids' => '14', 'end' => '14 Mar 18:00'],
];

$alerts = [
    '3 açık artırma onay bekliyor',
    '2 kullanıcı doğrulama talebi var',
    '1 şikayet bildirimi yeni geldi',
];
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Admin Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?php echo url_path('assets/css/admin.css'); ?>" />
</head>
<body>
<aside>
    <div class="brand"><span>A</span>Artirup Admin</div>
    <div class="menu">
        <a class="active" href="<?php echo url_path('admin.php'); ?>">Genel Bakış</a>
        <a href="<?php echo url_path('admin/auctions.php'); ?>">Açık Artırmalar</a>
        <a href="<?php echo url_path('admin/users.php'); ?>">Kullanıcılar</a>
        <a href="<?php echo url_path('admin/moderators.php'); ?>">Moderatörler</a>
        <a href="#">Raporlar</a>
        <a href="<?php echo url_path('admin/settings.php'); ?>">Ayarlar</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Admin Panel</h2>
            <p style="color: var(--muted); margin: 0;">Günlük operasyonları buradan yönet.</p>
        </div>
        <input type="search" placeholder="Lot, kullanıcı, satıcı ara..." />
        <div class="admin">Admin • Ayşe Karaca</div>
    </div>

    <div class="kpi-grid">
        <?php foreach ($kpis as $kpi): ?>
            <div class="kpi">
                <h3><?php echo $kpi['value']; ?></h3>
                <span><?php echo $kpi['label']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="content-grid">
        <div class="panel">
            <h3>Teklif hareketleri</h3>
            <div class="chart">Grafik alanı (satış & teklif trendi)</div>
            <h4>Son teklifler</h4>
            <table>
                <thead>
                    <tr>
                        <th>Lot</th>
                        <th>Kullanıcı</th>
                        <th>Tutar</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBids as $bid): ?>
                        <tr>
                            <td><?php echo $bid['lot']; ?></td>
                            <td><?php echo $bid['user']; ?></td>
                            <td><?php echo $bid['amount']; ?></td>
                            <td>
                                <span class="status <?php echo $bid['status'] === 'Onaylandı' ? 'ok' : ($bid['status'] === 'Beklemede' ? 'wait' : 'no'); ?>">
                                    <?php echo $bid['status']; ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="panel">
            <h3>Yaklaşan lotlar</h3>
            <div class="lot-list">
                <?php foreach ($lots as $lot): ?>
                    <div class="lot-item">
                        <div>
                            <strong><?php echo $lot['name']; ?></strong>
                            <div style="color: var(--muted);"><?php echo $lot['seller']; ?></div>
                        </div>
                        <div style="text-align: right;">
                            <div><?php echo $lot['bids']; ?> teklif</div>
                            <div style="color: var(--muted);"><?php echo $lot['end']; ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <h3 style="margin-top: 24px;">Günlük uyarılar</h3>
            <div class="lot-list">
                <?php foreach ($alerts as $alert): ?>
                    <div class="lot-item"><?php echo $alert; ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
</body>
</html>
