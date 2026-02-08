<?php
require_once __DIR__ . '/../config.php';
if (!is_admin()) {
    header('Location: ' . url_path('auth/login.php'));
    exit;
}
$message = '';

if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = [
        'logo' => '',
        'slides' => [
            ['title' => 'Welcome to Auction House', 'subtitle' => 'Build, sell & collect dijital ürünler.'],
            ['title' => 'Açık artırma evine hoş geldiniz', 'subtitle' => 'Tek tıkla keşfet, artır ve kazan.'],
            ['title' => 'Premium koleksiyonlar', 'subtitle' => 'Nadir parçalar için canlı açık artırmalar.'],
        ],
        'categories' => ['Koleksiyon', 'Sanat', 'Teknoloji', 'Moda'],
        'posts' => [
            ['title' => 'Yeni açık artırma trendleri', 'image' => 'Blog görseli'],
            ['title' => 'Satıcılar için ipuçları', 'image' => 'Blog görseli'],
        ],
        'faqs' => [
            ['q' => 'Teklif nasıl verilir?', 'a' => 'Lot detayından teklif tutarını girerek.'],
            ['q' => 'Ödeme nasıl yapılır?', 'a' => 'Kazanan teklif sonrası escrow ile.'],
        ],
        'auctions' => [
            ['title' => 'Retro Teknoloji Lotları', 'image' => 'Teknoloji görseli', 'status' => 'Yayında'],
            ['title' => 'Sanat & Koleksiyon', 'image' => 'Sanat görseli', 'status' => 'Onay Bekliyor'],
            ['title' => 'Otomotiv Özel Lot', 'image' => 'Otomotiv görseli', 'status' => 'Yayında'],
        ],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'upload_logo' && !empty($_FILES['logo']['name']) && is_uploaded_file($_FILES['logo']['tmp_name'])) {
        $uploadsDir = __DIR__ . '/../uploads';
        if (!is_dir($uploadsDir)) {
            mkdir($uploadsDir, 0755, true);
        }
        $extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
        $safeExtension = preg_replace('/[^a-zA-Z0-9]/', '', $extension);
        $fileName = 'logo_' . time() . ($safeExtension ? '.' . $safeExtension : '');
        $destination = $uploadsDir . '/' . $fileName;
        if (move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) {
            $_SESSION['settings']['logo'] = url_path('uploads/' . $fileName);
            $message = 'Logo güncellendi.';
        }
    }
    if ($action === 'delete_logo') {
        $_SESSION['settings']['logo'] = '';
        $message = 'Logo silindi.';
    }

    if ($action === 'add_slide') {
        $title = trim($_POST['title'] ?? '');
        $subtitle = trim($_POST['subtitle'] ?? '');
        if ($title !== '') {
            $_SESSION['settings']['slides'][] = ['title' => $title, 'subtitle' => $subtitle];
            $message = 'Slider eklendi.';
        }
    }
    if ($action === 'update_slide') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['slides'][$index])) {
            $_SESSION['settings']['slides'][$index]['title'] = trim($_POST['title'] ?? $_SESSION['settings']['slides'][$index]['title']);
            $_SESSION['settings']['slides'][$index]['subtitle'] = trim($_POST['subtitle'] ?? $_SESSION['settings']['slides'][$index]['subtitle']);
            $message = 'Slider güncellendi.';
        }
    }
    if ($action === 'delete_slide') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['slides'][$index])) {
            unset($_SESSION['settings']['slides'][$index]);
            $_SESSION['settings']['slides'] = array_values($_SESSION['settings']['slides']);
            $message = 'Slider silindi.';
        }
    }

    if ($action === 'add_category') {
        $category = trim($_POST['category'] ?? '');
        if ($category !== '') {
            $_SESSION['settings']['categories'][] = $category;
            $message = 'Kategori eklendi.';
        }
    }
    if ($action === 'update_category') {
        $index = (int) ($_POST['index'] ?? -1);
        $category = trim($_POST['category'] ?? '');
        if ($category !== '' && isset($_SESSION['settings']['categories'][$index])) {
            $_SESSION['settings']['categories'][$index] = $category;
            $message = 'Kategori güncellendi.';
        }
    }
    if ($action === 'delete_category') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['categories'][$index])) {
            unset($_SESSION['settings']['categories'][$index]);
            $_SESSION['settings']['categories'] = array_values($_SESSION['settings']['categories']);
            $message = 'Kategori silindi.';
        }
    }

    if ($action === 'add_post') {
        $title = trim($_POST['title'] ?? '');
        $image = trim($_POST['image'] ?? '');
        if ($title !== '') {
            $_SESSION['settings']['posts'][] = ['title' => $title, 'image' => $image !== '' ? $image : 'Blog görseli'];
            $message = 'Blog yazısı eklendi.';
        }
    }
    if ($action === 'update_post') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['posts'][$index])) {
            $_SESSION['settings']['posts'][$index]['title'] = trim($_POST['title'] ?? $_SESSION['settings']['posts'][$index]['title']);
            $_SESSION['settings']['posts'][$index]['image'] = trim($_POST['image'] ?? $_SESSION['settings']['posts'][$index]['image']);
            $message = 'Blog yazısı güncellendi.';
        }
    }
    if ($action === 'delete_post') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['posts'][$index])) {
            unset($_SESSION['settings']['posts'][$index]);
            $_SESSION['settings']['posts'] = array_values($_SESSION['settings']['posts']);
            $message = 'Blog yazısı silindi.';
        }
    }

    if ($action === 'add_faq') {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        if ($question !== '') {
            $_SESSION['settings']['faqs'][] = ['q' => $question, 'a' => $answer];
            $message = 'SSS eklendi.';
        }
    }
    if ($action === 'update_faq') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['faqs'][$index])) {
            $_SESSION['settings']['faqs'][$index]['q'] = trim($_POST['question'] ?? $_SESSION['settings']['faqs'][$index]['q']);
            $_SESSION['settings']['faqs'][$index]['a'] = trim($_POST['answer'] ?? $_SESSION['settings']['faqs'][$index]['a']);
            $message = 'SSS güncellendi.';
        }
    }
    if ($action === 'delete_faq') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['faqs'][$index])) {
            unset($_SESSION['settings']['faqs'][$index]);
            $_SESSION['settings']['faqs'] = array_values($_SESSION['settings']['faqs']);
            $message = 'SSS silindi.';
        }
    }

    if ($action === 'add_home_auction') {
        $title = trim($_POST['title'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $status = trim($_POST['status'] ?? 'Yayında');
        if ($title !== '') {
            $_SESSION['settings']['auctions'][] = [
                'title' => $title,
                'image' => $image !== '' ? $image : 'Görsel',
                'status' => $status,
            ];
            $message = 'Ana sayfa açık artırması eklendi.';
        }
    }
    if ($action === 'update_home_auction') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['auctions'][$index])) {
            $_SESSION['settings']['auctions'][$index]['title'] = trim($_POST['title'] ?? $_SESSION['settings']['auctions'][$index]['title']);
            $_SESSION['settings']['auctions'][$index]['image'] = trim($_POST['image'] ?? $_SESSION['settings']['auctions'][$index]['image']);
            $_SESSION['settings']['auctions'][$index]['status'] = trim($_POST['status'] ?? $_SESSION['settings']['auctions'][$index]['status']);
            $message = 'Ana sayfa açık artırması güncellendi.';
        }
    }
    if ($action === 'delete_home_auction') {
        $index = (int) ($_POST['index'] ?? -1);
        if (isset($_SESSION['settings']['auctions'][$index])) {
            unset($_SESSION['settings']['auctions'][$index]);
            $_SESSION['settings']['auctions'] = array_values($_SESSION['settings']['auctions']);
            $message = 'Ana sayfa açık artırması silindi.';
        }
    }
}
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup | Admin Ayarları</title>
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
        <a href="<?php echo url_path('admin/moderators.php'); ?>">Moderatörler</a>
        <a href="#">Raporlar</a>
        <a class="active" href="<?php echo url_path('admin/settings.php'); ?>">Ayarlar</a>
    </div>
</aside>
<main>
    <div class="topbar">
        <div>
            <h2>Site Ayarları</h2>
            <p style="color: var(--muted); margin: 0;">Logo, slider, kategori ve içerik yönetimi.</p>
        </div>
        <a class="btn btn-primary" href="#">Kaydet</a>
    </div>

    <?php if ($message): ?>
        <div class="panel" style="background: #e4f9ef; color: #1f9d62;">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="panel" style="margin-top: 16px;">
        <h3>Logo & Marka</h3>
        <div class="card-list">
            <div class="lot-item">
                <div>
                    <strong>Logo dosyası</strong>
                    <div style="color: var(--muted);">PNG / SVG yükle</div>
                    <?php if (!empty($_SESSION['settings']['logo'])): ?>
                        <div style="margin-top: 8px;">
                            <img src="<?php echo $_SESSION['settings']['logo']; ?>" alt="Logo" style="height: 40px;" />
                        </div>
                    <?php endif; ?>
                </div>
                <div class="actions">
                    <form class="actions" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="upload_logo" />
                        <input type="file" name="logo" accept="image/*" />
                        <button class="btn btn-outline" type="submit">Logo yükle</button>
                    </form>
                    <form class="actions" method="post">
                        <input type="hidden" name="action" value="delete_logo" />
                        <button class="btn btn-danger" type="submit">Sil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>Hero Slider İçerikleri</h3>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Alt Başlık</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['settings']['slides'] as $index => $slide): ?>
                    <tr>
                        <td>
                            <form class="form" method="post">
                                <input type="hidden" name="action" value="update_slide" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <input type="text" name="title" value="<?php echo htmlspecialchars($slide['title']); ?>" />
                        </td>
                        <td>
                                <input type="text" name="subtitle" value="<?php echo htmlspecialchars($slide['subtitle']); ?>" />
                        </td>
                        <td class="actions">
                                <button class="btn btn-outline" type="submit">Düzenle</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="action" value="delete_slide" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <button class="btn btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top: 12px;">
            <form class="form" method="post">
                <input type="hidden" name="action" value="add_slide" />
                <input type="text" name="title" placeholder="Başlık" />
                <input type="text" name="subtitle" placeholder="Alt başlık" />
                <button class="btn btn-primary" type="submit">Yeni slider ekle</button>
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>Kategoriler</h3>
        <div class="card-list">
            <?php foreach ($_SESSION['settings']['categories'] as $index => $category): ?>
                <div class="lot-item">
                    <div>
                        <form class="form" method="post">
                            <input type="hidden" name="action" value="update_category" />
                            <input type="hidden" name="index" value="<?php echo $index; ?>" />
                            <input type="text" name="category" value="<?php echo htmlspecialchars($category); ?>" />
                            <button class="btn btn-outline" type="submit">Düzenle</button>
                        </form>
                    </div>
                    <div class="actions">
                        <form method="post">
                            <input type="hidden" name="action" value="delete_category" />
                            <input type="hidden" name="index" value="<?php echo $index; ?>" />
                            <button class="btn btn-danger" type="submit">Sil</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top: 12px;">
            <form class="form" method="post">
                <input type="hidden" name="action" value="add_category" />
                <input type="text" name="category" placeholder="Yeni kategori" />
                <button class="btn btn-primary" type="submit">Kategori ekle</button>
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>Blog & Haberler</h3>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Görsel</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['settings']['posts'] as $index => $post): ?>
                    <tr>
                        <td>
                            <form class="form" method="post">
                                <input type="hidden" name="action" value="update_post" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" />
                        </td>
                        <td>
                                <input type="text" name="image" value="<?php echo htmlspecialchars($post['image']); ?>" />
                        </td>
                        <td class="actions">
                                <button class="btn btn-outline" type="submit">Düzenle</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="action" value="delete_post" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <button class="btn btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top: 12px;">
            <form class="form" method="post">
                <input type="hidden" name="action" value="add_post" />
                <input type="text" name="title" placeholder="Blog başlığı" />
                <input type="text" name="image" placeholder="Görsel açıklaması" />
                <button class="btn btn-primary" type="submit">Yeni blog yazısı</button>
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>Açık Artırmalar (Ana Sayfa)</h3>
        <table>
            <thead>
                <tr>
                    <th>Başlık</th>
                    <th>Görsel</th>
                    <th>Durum</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['settings']['auctions'] as $index => $auction): ?>
                    <tr>
                        <td>
                            <form class="form" method="post">
                                <input type="hidden" name="action" value="update_home_auction" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <input type="text" name="title" value="<?php echo htmlspecialchars($auction['title']); ?>" />
                        </td>
                        <td>
                                <input type="text" name="image" value="<?php echo htmlspecialchars($auction['image']); ?>" />
                        </td>
                        <td>
                                <input type="text" name="status" value="<?php echo htmlspecialchars($auction['status']); ?>" />
                        </td>
                        <td class="actions">
                                <button class="btn btn-outline" type="submit">Düzenle</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="action" value="delete_home_auction" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <button class="btn btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top: 12px;">
            <form class="form" method="post">
                <input type="hidden" name="action" value="add_home_auction" />
                <input type="text" name="title" placeholder="Açık artırma başlığı" />
                <input type="text" name="image" placeholder="Görsel açıklaması" />
                <input type="text" name="status" placeholder="Durum" value="Yayında" />
                <button class="btn btn-primary" type="submit">Yeni açık artırma ekle</button>
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>SSS</h3>
        <table>
            <thead>
                <tr>
                    <th>Soru</th>
                    <th>Cevap</th>
                    <th>İşlem</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['settings']['faqs'] as $index => $faq): ?>
                    <tr>
                        <td>
                            <form class="form" method="post">
                                <input type="hidden" name="action" value="update_faq" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <input type="text" name="question" value="<?php echo htmlspecialchars($faq['q']); ?>" />
                        </td>
                        <td>
                                <input type="text" name="answer" value="<?php echo htmlspecialchars($faq['a']); ?>" />
                        </td>
                        <td class="actions">
                                <button class="btn btn-outline" type="submit">Düzenle</button>
                            </form>
                            <form method="post">
                                <input type="hidden" name="action" value="delete_faq" />
                                <input type="hidden" name="index" value="<?php echo $index; ?>" />
                                <button class="btn btn-danger" type="submit">Sil</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div style="margin-top: 12px;">
            <form class="form" method="post">
                <input type="hidden" name="action" value="add_faq" />
                <input type="text" name="question" placeholder="Soru" />
                <input type="text" name="answer" placeholder="Cevap" />
                <button class="btn btn-primary" type="submit">SSS ekle</button>
            </form>
        </div>
    </div>

    <div class="panel" style="margin-top: 24px;">
        <h3>Hakkımızda & İletişim</h3>
        <div class="card-list">
            <div class="lot-item">
                <div>
                    <strong>Hakkımızda Metni</strong>
                    <div style="color: var(--muted);">Kurumsal açıklama düzenleme</div>
                </div>
                <div class="actions">
                    <a class="btn btn-outline" href="#">Düzenle</a>
                </div>
            </div>
            <div class="lot-item">
                <div>
                    <strong>İletişim Bilgileri</strong>
                    <div style="color: var(--muted);">Adres, telefon, e-posta</div>
                </div>
                <div class="actions">
                    <a class="btn btn-outline" href="#">Düzenle</a>
                </div>
            </div>
        </div>
    </div>
</main>
</body>
</html>
