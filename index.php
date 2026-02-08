<?php
require_once __DIR__ . '/config.php';
$slides = [
    [
        'eyebrow' => 'Açık artırma evine hoş geldiniz',
        'title' => 'Tek tıkla keşfet, artır ve kazan.',
        'desc' => 'Canlı müzayedeler, güvenli ödeme, anında bildirim ve kişiselleştirilmiş öneriler ile yeni nesil açık artırma deneyimi.',
        'cta' => 'Keşfetmeye Başla',
        'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
    ],
    [
        'eyebrow' => 'Premium koleksiyonlar',
        'title' => 'Nadir parçalar için canlı açık artırmalar.',
        'desc' => 'Koleksiyon ürünleri, sanat, teknoloji ve daha fazlası için gerçek zamanlı teklif ver.',
        'cta' => 'Canlı Artırmaları Gör',
        'image' => 'https://images.unsplash.com/photo-1524502397800-2eeaad7c3fe5?auto=format&fit=crop&w=900&q=80',
    ],
    [
        'eyebrow' => 'Satıcı olmak ister misin?',
        'title' => 'Ürünlerini listelerken yapay zekadan destek al.',
        'desc' => 'Akıllı fiyat önerisi, otomatik lot planlama ve raporlarla satışlarını büyüt.',
        'cta' => 'Satıcı Paneline Git',
        'image' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=900&q=80',
    ],
];

$liveAuctions = [
    [
        'title' => 'Alarm Clock 1990s',
        'bid' => '₺653.0',
        'seller' => 'Egens Lab',
        'time' => '12 Gün 13:38:33',
        'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80',
    ],
    [
        'title' => 'Premium 1998 Typewriter',
        'bid' => '₺732.0',
        'seller' => 'Retro Hub',
        'time' => '09 Gün 05:18:50',
        'image' => 'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80',
    ],
    [
        'title' => 'Macbook Pro 2018',
        'bid' => '₺1.220.0',
        'seller' => 'Nova Tech',
        'time' => '05 Gün 22:04:10',
        'image' => 'https://images.unsplash.com/photo-1517336714731-489689fd1ca8?auto=format&fit=crop&w=900&q=80',
    ],
];

$categories = [
    ['name' => 'Koleksiyon', 'count' => '684 Lot'],
    ['name' => 'Sanat', 'count' => '412 Lot'],
    ['name' => 'Teknoloji', 'count' => '980 Lot'],
    ['name' => 'Moda', 'count' => '356 Lot'],
    ['name' => 'Gayrimenkul', 'count' => '78 Lot'],
    ['name' => 'Araba & Araç', 'count' => '199 Lot'],
];

$features = [
    ['title' => 'Gerçek Zamanlı Teklif', 'desc' => 'Milisaniye gecikmeli artırma altyapısı ile canlı teklif ver.'],
    ['title' => 'Güvenli Ödeme', 'desc' => 'Escrow korumalı ödeme sistemi ve çoklu para birimi desteği.'],
    ['title' => 'Akıllı Bildirimler', 'desc' => 'Favori lotların kapanışını kaçırma, anlık bildirim al.'],
    ['title' => 'Satıcı Paneli', 'desc' => 'Lot yönetimi, raporlar ve AI fiyat önerileri.'],
];

$testimonials = [
    ['name' => 'Deniz A.', 'role' => 'Koleksiyoncu', 'quote' => 'Codex Auction ile 3 günde 12 lot kazandım. Bildirimler harika!'],
    ['name' => 'Elif M.', 'role' => 'Satıcı', 'quote' => 'Satıcı paneliyle stoklarımı yönetmek çok kolay.'],
    ['name' => 'Mert S.', 'role' => 'Yatırımcı', 'quote' => 'Premium lotlarda açık artırma deneyimi gerçekten akıcı.'],
];

$blogPosts = [
    ['title' => 'Açık artırmada doğru teklif stratejisi', 'date' => '05 Mar 2050'],
    ['title' => 'Koleksiyon yatırımlarında 5 kritik kural', 'date' => '28 Feb 2050'],
    ['title' => 'Satıcı paneli ile satışları artırma', 'date' => '12 Feb 2050'],
];
$topRated = [
    ['title' => 'Galaksi Antika Saat', 'rating' => 5, 'reviews' => 214],
    ['title' => 'Retro Kamera Seti', 'rating' => 4, 'reviews' => 158],
    ['title' => 'Neon Tasarım Kolye', 'rating' => 5, 'reviews' => 302],
];
$currentUser = current_user();
$dashboardLink = $currentUser ? user_dashboard_link($currentUser) : '';
$dashboardLabel = $currentUser ? user_dashboard_label($currentUser) : '';
$avatar = $currentUser['avatar'] ?? 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=120&h=120&q=80';
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Artirup - Açık Artırma Platformu</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg: #f6f7fb;
            --card: #ffffff;
            --dark: #1f2335;
            --text: #5a627a;
            --accent: #ff3d6e;
            --accent-soft: #ffe5ec;
            --line: #eef0f6;
            --shadow: 0 20px 50px rgba(22, 27, 45, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Manrope", system-ui, -apple-system, sans-serif;
            background: var(--bg);
            color: var(--dark);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--line);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 8vw;
            gap: 20px;
        }

        .logo {
            font-weight: 800;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-badge {
            width: 36px;
            height: 36px;
            border-radius: 14px;
            background: var(--accent);
            color: #fff;
            display: grid;
            place-items: center;
            font-weight: 700;
        }

        nav ul {
            list-style: none;
            display: flex;
            gap: 22px;
            padding: 0;
            margin: 0;
            color: var(--text);
            font-weight: 600;
        }

        .nav-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .profile-menu {
            position: relative;
        }

        .profile-trigger {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 6px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
            cursor: pointer;
        }

        .profile-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--accent);
        }

        .profile-name {
            font-weight: 700;
            color: var(--dark);
        }

        .profile-dropdown {
            position: absolute;
            top: 52px;
            right: 0;
            background: #fff;
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 10px;
            min-width: 180px;
            display: none;
            z-index: 5;
        }

        .profile-menu:hover .profile-dropdown {
            display: grid;
            gap: 6px;
        }

        .profile-dropdown a {
            text-decoration: none;
            color: var(--dark);
            padding: 8px 12px;
            border-radius: 10px;
        }

        .profile-dropdown a:hover {
            background: #f1f2f8;
        }

        .btn {
            border-radius: 999px;
            padding: 10px 20px;
            font-weight: 700;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-outline {
            border-color: var(--accent);
            color: var(--accent);
            background: transparent;
        }

        .btn-primary {
            background: var(--dark);
            color: #fff;
        }

        .hero {
            padding: 40px 8vw 70px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 40px;
            align-items: center;
        }

        .hero-card {
            background: var(--card);
            border-radius: 26px;
            padding: 40px;
            box-shadow: var(--shadow);
        }

        .hero-card span {
            color: var(--accent);
            font-weight: 600;
            letter-spacing: 0.02em;
        }

        .hero-card h1 {
            font-size: clamp(2.4rem, 4vw, 3.2rem);
            margin: 16px 0;
        }

        .hero-card p {
            color: var(--text);
            line-height: 1.6;
        }

        .hero-actions {
            margin-top: 24px;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
        }

        .slider {
            position: relative;
            overflow: hidden;
            border-radius: 32px;
            min-height: 420px;
            box-shadow: var(--shadow);
        }

        .slide {
            position: absolute;
            inset: 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            padding: 40px;
            gap: 24px;
            background: linear-gradient(135deg, #fff, #fef2f6);
            opacity: 0;
            transform: translateX(40px);
            transition: all 0.6s ease;
        }

        .slide.active {
            opacity: 1;
            transform: translateX(0);
            z-index: 1;
        }

        .slide img {
            width: 100%;
            height: 320px;
            object-fit: cover;
            border-radius: 26px;
        }

        .slide h2 {
            font-size: 2rem;
            margin: 12px 0;
        }

        .slide p {
            color: var(--text);
        }

        .slide-controls {
            position: absolute;
            bottom: 20px;
            left: 40px;
            display: flex;
            gap: 10px;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #d7dbe7;
        }

        .dot.active {
            background: var(--accent);
        }

        .section {
            padding: 0 8vw 70px;
        }

        .section h3 {
            font-size: 1.8rem;
            margin-bottom: 8px;
        }

        .section p.lead {
            color: var(--text);
            margin-bottom: 28px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
        }

        .category-card,
        .feature-card,
        .testimonial-card,
        .blog-card {
            background: var(--card);
            border-radius: 20px;
            padding: 22px;
            box-shadow: var(--shadow);
        }

        .category-card strong {
            display: block;
            margin-top: 18px;
        }

        .auction-card {
            background: var(--card);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
        }

        .auction-card img {
            height: 220px;
            object-fit: cover;
        }

        .auction-body {
            padding: 20px;
        }

        .auction-body h4 {
            margin: 0 0 10px;
        }

        .auction-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: var(--text);
            font-size: 0.9rem;
        }

        .auction-footer {
            margin-top: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .pill {
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700;
            font-size: 0.85rem;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #d4a017;
            font-weight: 700;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
        }

        .stat {
            background: var(--dark);
            color: #fff;
            border-radius: 20px;
            padding: 24px;
        }

        .stat span {
            color: #bbc3db;
            font-size: 0.9rem;
        }

        .cta-banner {
            background: linear-gradient(135deg, #1f2335, #3b4160);
            color: #fff;
            border-radius: 32px;
            padding: 40px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            align-items: center;
            gap: 24px;
            box-shadow: var(--shadow);
        }

        .cta-banner p {
            color: #d2d7ea;
        }

        footer {
            padding: 40px 8vw;
            background: #fff;
            border-top: 1px solid var(--line);
        }

        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
        }

        .footer-grid h4 {
            margin-bottom: 12px;
        }

        .footer-grid ul {
            list-style: none;
            padding: 0;
            margin: 0;
            color: var(--text);
        }

        .newsletter {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }

        .newsletter input {
            flex: 1;
            border-radius: 999px;
            border: 1px solid var(--line);
            padding: 10px 14px;
        }

        @media (max-width: 900px) {
            nav ul {
                flex-wrap: wrap;
                justify-content: center;
            }

            .slide {
                grid-template-columns: 1fr;
            }

            .slide img {
                height: 240px;
            }

            .hero-card {
                order: 2;
            }
        }
    </style>
</head>
<body>
<header>
    <div class="nav">
        <div class="logo"><span class="logo-badge">A</span>Artirup</div>
        <nav>
            <ul>
                <li><a href="<?php echo url_path('index.php'); ?>">Anasayfa</a></li>
                <li><a href="<?php echo url_path('pages/about.php'); ?>">Hakkımızda</a></li>
                <li><a href="<?php echo url_path('pages/auctions.php'); ?>">Açık Artırmalar</a></li>
                <li><a href="<?php echo url_path('pages/stores.php'); ?>">Mağazalar</a></li>
                <li><a href="<?php echo url_path('pages/blog.php'); ?>">Blog</a></li>
                <li><a href="<?php echo url_path('pages/contact.php'); ?>">İletişim</a></li>
            </ul>
        </nav>
        <div class="nav-actions">
            <?php if ($currentUser): ?>
                <div class="profile-menu">
                    <div class="profile-trigger">
                        <img class="profile-avatar" src="<?php echo htmlspecialchars($avatar); ?>" alt="Profil" />
                        <span class="profile-name"><?php echo htmlspecialchars($currentUser['name'] ?? 'Profilim'); ?></span>
                    </div>
                    <div class="profile-dropdown">
                        <a href="<?php echo url_path('profile.php'); ?>">Profilim</a>
                        <a href="<?php echo $dashboardLink; ?>"><?php echo $dashboardLabel; ?></a>
                        <a href="<?php echo url_path('auth/logout.php'); ?>">Çıkış Yap</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="btn btn-outline" href="<?php echo url_path('seller/index.php'); ?>">Satıcı</a>
                <a class="btn btn-outline" href="<?php echo url_path('auth/login.php'); ?>">Giriş Yap</a>
                <a class="btn btn-primary" href="<?php echo url_path('auth/register.php'); ?>">Kayıt Ol</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<section class="hero">
    <div class="hero-card">
        <span>Welcome to Auction House</span>
        <h1>Build, sell & collect dijital ürünler.</h1>
        <p>Artirup; açık artırma, sabit fiyat ve canlı yayın modüllerini tek platformda birleştirir. Kategoriye özel filtreler, akıllı teklif önerileri ve güvenli escrow altyapısı hazır.</p>
        <div class="hero-actions">
            <button class="btn btn-primary">Start Exploring</button>
            <button class="btn btn-outline">Açık Artırma Başlat</button>
        </div>
    </div>
    <div class="slider" id="slider">
        <?php foreach ($slides as $index => $slide): ?>
            <div class="slide<?php echo $index === 0 ? ' active' : ''; ?>">
                <div>
                    <span><?php echo $slide['eyebrow']; ?></span>
                    <h2><?php echo $slide['title']; ?></h2>
                    <p><?php echo $slide['desc']; ?></p>
                    <button class="btn btn-primary"><?php echo $slide['cta']; ?></button>
                </div>
                <img src="<?php echo $slide['image']; ?>" alt="<?php echo $slide['title']; ?>" />
            </div>
        <?php endforeach; ?>
        <div class="slide-controls">
            <?php foreach ($slides as $index => $slide): ?>
                <div class="dot<?php echo $index === 0 ? ' active' : ''; ?>"></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <h3>Kategoriler</h3>
    <p class="lead">Popüler kategorilerden seç, ihtiyaçlarına uygun lotları keşfet.</p>
    <div class="grid">
        <?php foreach ($categories as $category): ?>
            <div class="category-card">
                <div class="pill">Trend</div>
                <strong><?php echo $category['name']; ?></strong>
                <p><?php echo $category['count']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h3>Canlı açık artırmalar</h3>
    <p class="lead">Geri sayım devam ediyor. Şimdi teklif ver.</p>
    <div class="grid">
        <?php foreach ($liveAuctions as $auction): ?>
            <div class="auction-card">
                <img src="<?php echo $auction['image']; ?>" alt="<?php echo $auction['title']; ?>" />
                <div class="auction-body">
                    <div class="pill"><?php echo $auction['time']; ?></div>
                    <h4><?php echo $auction['title']; ?></h4>
                    <div class="auction-meta">
                        <span><?php echo $auction['seller']; ?></span>
                        <span>Current bid: <strong><?php echo $auction['bid']; ?></strong></span>
                    </div>
                    <div class="auction-footer">
                        <button class="btn btn-outline">Place a Bid</button>
                        <span class="pill">Live</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h3>En Çok Değerlendirilenler</h3>
    <p class="lead">Kullanıcılarımızın en yüksek puan verdiği lotlar.</p>
    <div class="grid">
        <?php foreach ($topRated as $item): ?>
            <div class="category-card">
                <h4><?php echo $item['title']; ?></h4>
                <div class="rating">
                    <?php for ($i = 0; $i < $item['rating']; $i++): ?>
                        ★
                    <?php endfor; ?>
                    <span style="color: var(--text); font-weight: 500;">(<?php echo $item['reviews']; ?>)</span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h3>Platform özellikleri</h3>
    <p class="lead">Açık artırma sitesinde olması gereken tüm modüller hazır.</p>
    <div class="grid">
        <?php foreach ($features as $feature): ?>
            <div class="feature-card">
                <h4><?php echo $feature['title']; ?></h4>
                <p><?php echo $feature['desc']; ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h3>İstatistikler</h3>
    <div class="stats">
        <div class="stat"><h2>12.4K</h2><span>Aktif kullanıcı</span></div>
        <div class="stat"><h2>3.2K</h2><span>Canlı lot</span></div>
        <div class="stat"><h2>984</h2><span>Satıcı mağaza</span></div>
        <div class="stat"><h2>₺82M</h2><span>Toplam hacim</span></div>
    </div>
</section>

<section class="section">
    <div class="cta-banner">
        <div>
            <h3>Satıcı panelini aktif et</h3>
            <p>Lot oluşturma, canlı artırma planlama, teklif geçmişi ve raporlar tek panelde.</p>
            <button class="btn btn-outline">Satıcı Paneli</button>
        </div>
        <div>
            <h3>Mobil uygulama</h3>
            <p>Anında bildirimler, favori listeleri ve QR doğrulama ile her yerden açık artırma.</p>
            <button class="btn btn-primary">Uygulamayı İndir</button>
        </div>
    </div>
</section>

<section class="section">
    <h3>Kullanıcı yorumları</h3>
    <p class="lead">Topluluğumuz ne diyor?</p>
    <div class="grid">
        <?php foreach ($testimonials as $testimonial): ?>
            <div class="testimonial-card">
                <p>“<?php echo $testimonial['quote']; ?>”</p>
                <strong><?php echo $testimonial['name']; ?></strong>
                <span><?php echo $testimonial['role']; ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="section">
    <h3>Blog & Haberler</h3>
    <div class="grid">
        <?php foreach ($blogPosts as $post): ?>
            <div class="blog-card">
                <span class="pill"><?php echo $post['date']; ?></span>
                <h4><?php echo $post['title']; ?></h4>
                <button class="btn btn-outline">Devamını oku</button>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<footer>
    <div class="footer-grid">
        <div>
            <h4>Artirup</h4>
            <p>Yeni nesil açık artırma platformu. Canlı artırma, sabit fiyat ve premium lotlar bir arada.</p>
        </div>
        <div>
            <h4>Kurumsal</h4>
            <ul>
                <li>Hakkımızda</li>
                <li>Kariyer</li>
                <li>Gizlilik</li>
                <li>KVKK</li>
            </ul>
        </div>
        <div>
            <h4>Destek</h4>
            <ul>
                <li>SSS</li>
                <li>Canlı destek</li>
                <li>İletişim</li>
                <li>Durum</li>
            </ul>
        </div>
        <div>
            <h4>Bülten</h4>
            <p>Yeni açık artırmalardan ilk sen haberdar ol.</p>
            <div class="newsletter">
                <input type="email" placeholder="E-posta" />
                <button class="btn btn-primary">Katıl</button>
            </div>
        </div>
    </div>
</footer>

<script>
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    let index = 0;

    setInterval(() => {
        slides[index].classList.remove('active');
        dots[index].classList.remove('active');
        index = (index + 1) % slides.length;
        slides[index].classList.add('active');
        dots[index].classList.add('active');
    }, 5000);
</script>
</body>
</html>
