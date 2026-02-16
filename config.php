<?php
$isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] === '443');
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_secure' => $isSecure,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

function default_site_settings(): array
{
    return [
        'logo' => '',
        'brand_name' => 'Artirup',
        'slides' => [
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
        'payment_methods' => [
            'credit_card' => ['enabled' => true, 'label' => 'Kredi / Banka Kartı'],
            'bank_transfer' => ['enabled' => true, 'label' => 'Havale / EFT'],
            'cash_on_delivery' => ['enabled' => false, 'label' => 'Kapıda Ödeme'],
            'paypal' => ['enabled' => false, 'label' => 'PayPal'],
        ],
        'bank_transfer_iban' => 'TR00 0000 0000 0000 0000 0000 00',
    ];
}

if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = default_site_settings();
} else {
    $_SESSION['settings'] = array_replace_recursive(default_site_settings(), $_SESSION['settings']);
}

if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        [
            'id' => 1,
            'title' => 'Retro Teknoloji Lotları',
            'seller' => 'Nova Tech',
            'status' => 'Yayında',
            'lots' => 42,
            'end_at' => date('Y-m-d H:i:s', strtotime('+3 days +4 hours')),
            'price' => 1250,
            'image' => 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80',
            'gallery' => [
                'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=80',
            ],
            'tags' => ['retro', 'teknoloji'],
        ],
        [
            'id' => 2,
            'title' => 'Sanat & Koleksiyon',
            'seller' => 'Studio 55',
            'status' => 'Onay Bekliyor',
            'lots' => 18,
            'end_at' => date('Y-m-d H:i:s', strtotime('+5 days +2 hours')),
            'price' => 980,
            'image' => 'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=900&q=80',
            'gallery' => [
                'https://images.unsplash.com/photo-1460661419201-fd4cecdf8a8b?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?auto=format&fit=crop&w=900&q=80',
            ],
            'tags' => ['sanat', 'koleksiyon'],
        ],
        [
            'id' => 3,
            'title' => 'Otomotiv Özel Lot',
            'seller' => 'DriveX',
            'status' => 'Yayında',
            'lots' => 8,
            'end_at' => date('Y-m-d H:i:s', strtotime('+1 days +9 hours')),
            'price' => 1850,
            'image' => 'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=900&q=80',
            'gallery' => [
                'https://images.unsplash.com/photo-1493238792000-8113da705763?auto=format&fit=crop&w=900&q=80',
                'https://images.unsplash.com/photo-1542282088-fe8426682b8f?auto=format&fit=crop&w=900&q=80',
            ],
            'tags' => ['otomotiv', 'özel lot'],
        ],
    ];
}

foreach ($_SESSION['products'] as &$product) {
    if (!isset($product['end_at']) && isset($product['end'])) {
        $product['end_at'] = date('Y-m-d H:i:s', strtotime('+2 days'));
    }
    if (!isset($product['image'])) {
        $product['image'] = 'https://images.unsplash.com/photo-1487412720507-e7ab37603c6f?auto=format&fit=crop&w=900&q=80';
    }
    if (!isset($product['gallery']) || !is_array($product['gallery']) || empty($product['gallery'])) {
        $product['gallery'] = [$product['image']];
    }
    if (!isset($product['tags']) || !is_array($product['tags'])) {
        $product['tags'] = [];
    }
}
unset($product);

if (!isset($_SESSION['comments'])) {
    $_SESSION['comments'] = [
        1 => [
            ['author' => 'Elif Demir', 'text' => 'Harika lotlar, şeffaf açıklama.'],
        ],
    ];
}

$adminAccount = [
    'email' => 'admin@artirup.com',
    'password' => 'Artirup2025!',
    'name' => 'Artirup Admin',
    'role' => 'Admin',
    'avatar' => 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=facearea&w=160&h=160&q=80',
];

$moderatorAccount = [
    'email' => 'moderator@artirup.com',
    'password' => 'Moderator2025!',
    'name' => 'Artirup Moderator',
    'role' => 'Moderatör',
    'avatar' => 'https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=facearea&w=160&h=160&q=80',
];

$docRoot = realpath($_SERVER['DOCUMENT_ROOT'] ?? '');
$projectRoot = realpath(__DIR__);
if ($docRoot && $projectRoot && str_starts_with($projectRoot, $docRoot)) {
    $basePath = str_replace('\\', '/', substr($projectRoot, strlen($docRoot)));
    $basePath = rtrim($basePath, '/');
} else {
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    $basePath = $scriptDir;
    foreach (['pages', 'auth', 'seller'] as $segment) {
        if (preg_match('#/' . $segment . '$#', $basePath)) {
            $basePath = dirname($basePath);
        }
    }
    if ($basePath === '.' || $basePath === '/') {
        $basePath = '';
    }
}

function url_path(string $path): string
{
    global $basePath;
    return ($basePath === '' ? '' : $basePath) . '/' . ltrim($path, '/');
}

function site_brand_name(): string
{
    return $_SESSION['settings']['brand_name'] ?? 'Artirup';
}

function site_logo_url(): string
{
    return $_SESSION['settings']['logo'] ?? '';
}

function render_site_logo(): string
{
    $brand = site_brand_name();
    $logo = site_logo_url();
    $brandEscaped = htmlspecialchars($brand, ENT_QUOTES, 'UTF-8');
    if ($logo !== '') {
        $logoEscaped = htmlspecialchars($logo, ENT_QUOTES, 'UTF-8');
        return '<div class="logo"><img src="' . $logoEscaped . '" alt="' . $brandEscaped . '" /></div>';
    }
    $badgeChar = function_exists('mb_substr') ? mb_substr($brand, 0, 1, 'UTF-8') : substr($brand, 0, 1);
    $badgeEscaped = htmlspecialchars($badgeChar, ENT_QUOTES, 'UTF-8');
    return '<div class="logo"><span class="logo-badge">' . $badgeEscaped . '</span>' . $brandEscaped . '</div>';
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_admin(): bool
{
    $user = current_user();
    return $user && ($user['role'] ?? '') === 'Admin';
}

function is_moderator(): bool
{
    $user = current_user();
    return $user && ($user['role'] ?? '') === 'Moderatör';
}

function user_dashboard_link(array $user): string
{
    $role = $user['role'] ?? 'Kullanıcı';
    if ($role === 'Admin') {
        return url_path('admin.php');
    }
    if ($role === 'Moderatör') {
        return url_path('moderator/index.php');
    }
    if ($role === 'Satıcı') {
        return url_path('seller/index.php');
    }
    return url_path('profile.php');
}

function user_dashboard_label(array $user): string
{
    $role = $user['role'] ?? 'Kullanıcı';
    if ($role === 'Admin') {
        return 'Admin Paneli';
    }
    if ($role === 'Moderatör') {
        return 'Moderatör Paneli';
    }
    if ($role === 'Satıcı') {
        return 'Satıcı Paneli';
    }
    return 'Profilim';
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

function cart_count(): int
{
    return array_sum(array_map(fn(array $item) => (int) ($item['qty'] ?? 0), $_SESSION['cart'] ?? []));
}

function cart_items(): array
{
    $items = [];
    foreach (($_SESSION['cart'] ?? []) as $productId => $entry) {
        foreach (($_SESSION['products'] ?? []) as $product) {
            if (($product['id'] ?? null) === (int) $productId) {
                $qty = max(1, (int) ($entry['qty'] ?? 1));
                $items[] = [
                    'product' => $product,
                    'qty' => $qty,
                    'subtotal' => $qty * (int) ($product['price'] ?? 0),
                ];
                break;
            }
        }
    }
    return $items;
}

function cart_total(): int
{
    return array_sum(array_map(fn(array $item) => (int) $item['subtotal'], cart_items()));
}

function add_to_cart(int $productId, int $qty = 1): void
{
    if ($qty < 1) {
        $qty = 1;
    }
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = ['qty' => 0];
    }
    $_SESSION['cart'][$productId]['qty'] += $qty;
}

function product_end_at(array $product): int
{
    $raw = $product['end_at'] ?? '';
    $timestamp = strtotime((string) $raw);
    if ($timestamp === false) {
        return time();
    }
    return $timestamp;
}

function product_deadline_label(array $product): string
{
    return date('d M Y H:i', product_end_at($product));
}

function product_countdown_label(array $product): string
{
    $remaining = product_end_at($product) - time();
    if ($remaining <= 0) {
        return 'Süre doldu';
    }
    $days = intdiv($remaining, 86400);
    $hours = intdiv($remaining % 86400, 3600);
    $minutes = intdiv($remaining % 3600, 60);
    return sprintf('%d gün %02d saat %02d dk', $days, $hours, $minutes);
}

function enabled_payment_methods(): array
{
    $methods = $_SESSION['settings']['payment_methods'] ?? [];
    return array_filter($methods, fn(array $method) => !empty($method['enabled']));
}

function is_seller(): bool
{
    $user = current_user();
    return $user && (($user['role'] ?? '') === 'Satıcı');
}

function country_options(): array
{
    return [
        'AF' => ['name' => 'Afganistan', 'flag' => '🇦🇫'],
        'AL' => ['name' => 'Arnavutluk', 'flag' => '🇦🇱'],
        'DZ' => ['name' => 'Cezayir', 'flag' => '🇩🇿'],
        'AS' => ['name' => 'Amerikan Samoası', 'flag' => '🇦🇸'],
        'AD' => ['name' => 'Andorra', 'flag' => '🇦🇩'],
        'AO' => ['name' => 'Angola', 'flag' => '🇦🇴'],
        'AG' => ['name' => 'Antigua ve Barbuda', 'flag' => '🇦🇬'],
        'AR' => ['name' => 'Arjantin', 'flag' => '🇦🇷'],
        'AM' => ['name' => 'Ermenistan', 'flag' => '🇦🇲'],
        'AU' => ['name' => 'Avustralya', 'flag' => '🇦🇺'],
        'AT' => ['name' => 'Avusturya', 'flag' => '🇦🇹'],
        'AZ' => ['name' => 'Azerbaycan', 'flag' => '🇦🇿'],
        'BS' => ['name' => 'Bahamalar', 'flag' => '🇧🇸'],
        'BH' => ['name' => 'Bahreyn', 'flag' => '🇧🇭'],
        'BD' => ['name' => 'Bangladeş', 'flag' => '🇧🇩'],
        'BB' => ['name' => 'Barbados', 'flag' => '🇧🇧'],
        'BY' => ['name' => 'Belarus', 'flag' => '🇧🇾'],
        'BE' => ['name' => 'Belçika', 'flag' => '🇧🇪'],
        'BZ' => ['name' => 'Belize', 'flag' => '🇧🇿'],
        'BJ' => ['name' => 'Benin', 'flag' => '🇧🇯'],
        'BT' => ['name' => 'Butan', 'flag' => '🇧🇹'],
        'BO' => ['name' => 'Bolivya', 'flag' => '🇧🇴'],
        'BA' => ['name' => 'Bosna Hersek', 'flag' => '🇧🇦'],
        'BW' => ['name' => 'Botsvana', 'flag' => '🇧🇼'],
        'BR' => ['name' => 'Brezilya', 'flag' => '🇧🇷'],
        'BN' => ['name' => 'Brunei', 'flag' => '🇧🇳'],
        'BG' => ['name' => 'Bulgaristan', 'flag' => '🇧🇬'],
        'BF' => ['name' => 'Burkina Faso', 'flag' => '🇧🇫'],
        'BI' => ['name' => 'Burundi', 'flag' => '🇧🇮'],
        'KH' => ['name' => 'Kamboçya', 'flag' => '🇰🇭'],
        'CM' => ['name' => 'Kamerun', 'flag' => '🇨🇲'],
        'CA' => ['name' => 'Kanada', 'flag' => '🇨🇦'],
        'CV' => ['name' => 'Yeşil Burun Adaları', 'flag' => '🇨🇻'],
        'CF' => ['name' => 'Orta Afrika Cumhuriyeti', 'flag' => '🇨🇫'],
        'TD' => ['name' => 'Çad', 'flag' => '🇹🇩'],
        'CL' => ['name' => 'Şili', 'flag' => '🇨🇱'],
        'CN' => ['name' => 'Çin', 'flag' => '🇨🇳'],
        'CO' => ['name' => 'Kolombiya', 'flag' => '🇨🇴'],
        'KM' => ['name' => 'Komorlar', 'flag' => '🇰🇲'],
        'CG' => ['name' => 'Kongo', 'flag' => '🇨🇬'],
        'CR' => ['name' => 'Kosta Rika', 'flag' => '🇨🇷'],
        'CI' => ['name' => 'Fildişi Sahili', 'flag' => '🇨🇮'],
        'HR' => ['name' => 'Hırvatistan', 'flag' => '🇭🇷'],
        'CU' => ['name' => 'Küba', 'flag' => '🇨🇺'],
        'CY' => ['name' => 'Kıbrıs', 'flag' => '🇨🇾'],
        'CZ' => ['name' => 'Çekya', 'flag' => '🇨🇿'],
        'DK' => ['name' => 'Danimarka', 'flag' => '🇩🇰'],
        'DJ' => ['name' => 'Cibuti', 'flag' => '🇩🇯'],
        'DM' => ['name' => 'Dominika', 'flag' => '🇩🇲'],
        'DO' => ['name' => 'Dominik Cumhuriyeti', 'flag' => '🇩🇴'],
        'EC' => ['name' => 'Ekvador', 'flag' => '🇪🇨'],
        'EG' => ['name' => 'Mısır', 'flag' => '🇪🇬'],
        'SV' => ['name' => 'El Salvador', 'flag' => '🇸🇻'],
        'GQ' => ['name' => 'Ekvator Ginesi', 'flag' => '🇬🇶'],
        'ER' => ['name' => 'Eritre', 'flag' => '🇪🇷'],
        'EE' => ['name' => 'Estonya', 'flag' => '🇪🇪'],
        'ET' => ['name' => 'Etiyopya', 'flag' => '🇪🇹'],
        'FJ' => ['name' => 'Fiji', 'flag' => '🇫🇯'],
        'FI' => ['name' => 'Finlandiya', 'flag' => '🇫🇮'],
        'FR' => ['name' => 'Fransa', 'flag' => '🇫🇷'],
        'GA' => ['name' => 'Gabon', 'flag' => '🇬🇦'],
        'GM' => ['name' => 'Gambiya', 'flag' => '🇬🇲'],
        'GE' => ['name' => 'Gürcistan', 'flag' => '🇬🇪'],
        'DE' => ['name' => 'Almanya', 'flag' => '🇩🇪'],
        'GH' => ['name' => 'Gana', 'flag' => '🇬🇭'],
        'GR' => ['name' => 'Yunanistan', 'flag' => '🇬🇷'],
        'GD' => ['name' => 'Grenada', 'flag' => '🇬🇩'],
        'GT' => ['name' => 'Guatemala', 'flag' => '🇬🇹'],
        'GN' => ['name' => 'Gine', 'flag' => '🇬🇳'],
        'GW' => ['name' => 'Gine-Bissau', 'flag' => '🇬🇼'],
        'GY' => ['name' => 'Guyana', 'flag' => '🇬🇾'],
        'HT' => ['name' => 'Haiti', 'flag' => '🇭🇹'],
        'HN' => ['name' => 'Honduras', 'flag' => '🇭🇳'],
        'HU' => ['name' => 'Macaristan', 'flag' => '🇭🇺'],
        'IS' => ['name' => 'İzlanda', 'flag' => '🇮🇸'],
        'IN' => ['name' => 'Hindistan', 'flag' => '🇮🇳'],
        'ID' => ['name' => 'Endonezya', 'flag' => '🇮🇩'],
        'IR' => ['name' => 'İran', 'flag' => '🇮🇷'],
        'IQ' => ['name' => 'Irak', 'flag' => '🇮🇶'],
        'IE' => ['name' => 'İrlanda', 'flag' => '🇮🇪'],
        'IL' => ['name' => 'İsrail', 'flag' => '🇮🇱'],
        'IT' => ['name' => 'İtalya', 'flag' => '🇮🇹'],
        'JM' => ['name' => 'Jamaika', 'flag' => '🇯🇲'],
        'JP' => ['name' => 'Japonya', 'flag' => '🇯🇵'],
        'JO' => ['name' => 'Ürdün', 'flag' => '🇯🇴'],
        'KZ' => ['name' => 'Kazakistan', 'flag' => '🇰🇿'],
        'KE' => ['name' => 'Kenya', 'flag' => '🇰🇪'],
        'KI' => ['name' => 'Kiribati', 'flag' => '🇰🇮'],
        'KW' => ['name' => 'Kuveyt', 'flag' => '🇰🇼'],
        'KG' => ['name' => 'Kırgızistan', 'flag' => '🇰🇬'],
        'LA' => ['name' => 'Laos', 'flag' => '🇱🇦'],
        'LV' => ['name' => 'Letonya', 'flag' => '🇱🇻'],
        'LB' => ['name' => 'Lübnan', 'flag' => '🇱🇧'],
        'LS' => ['name' => 'Lesotho', 'flag' => '🇱🇸'],
        'LR' => ['name' => 'Liberya', 'flag' => '🇱🇷'],
        'LY' => ['name' => 'Libya', 'flag' => '🇱🇾'],
        'LI' => ['name' => 'Lihtenştayn', 'flag' => '🇱🇮'],
        'LT' => ['name' => 'Litvanya', 'flag' => '🇱🇹'],
        'LU' => ['name' => 'Lüksemburg', 'flag' => '🇱🇺'],
        'MG' => ['name' => 'Madagaskar', 'flag' => '🇲🇬'],
        'MW' => ['name' => 'Malavi', 'flag' => '🇲🇼'],
        'MY' => ['name' => 'Malezya', 'flag' => '🇲🇾'],
        'MV' => ['name' => 'Maldivler', 'flag' => '🇲🇻'],
        'ML' => ['name' => 'Mali', 'flag' => '🇲🇱'],
        'MT' => ['name' => 'Malta', 'flag' => '🇲🇹'],
        'MH' => ['name' => 'Marshall Adaları', 'flag' => '🇲🇭'],
        'MR' => ['name' => 'Moritanya', 'flag' => '🇲🇷'],
        'MU' => ['name' => 'Mauritius', 'flag' => '🇲🇺'],
        'MX' => ['name' => 'Meksika', 'flag' => '🇲🇽'],
        'FM' => ['name' => 'Mikronezya', 'flag' => '🇫🇲'],
        'MD' => ['name' => 'Moldova', 'flag' => '🇲🇩'],
        'MC' => ['name' => 'Monako', 'flag' => '🇲🇨'],
        'MN' => ['name' => 'Moğolistan', 'flag' => '🇲🇳'],
        'ME' => ['name' => 'Karadağ', 'flag' => '🇲🇪'],
        'MA' => ['name' => 'Fas', 'flag' => '🇲🇦'],
        'MZ' => ['name' => 'Mozambik', 'flag' => '🇲🇿'],
        'MM' => ['name' => 'Myanmar', 'flag' => '🇲🇲'],
        'NA' => ['name' => 'Namibya', 'flag' => '🇳🇦'],
        'NP' => ['name' => 'Nepal', 'flag' => '🇳🇵'],
        'NL' => ['name' => 'Hollanda', 'flag' => '🇳🇱'],
        'NZ' => ['name' => 'Yeni Zelanda', 'flag' => '🇳🇿'],
        'NI' => ['name' => 'Nikaragua', 'flag' => '🇳🇮'],
        'NE' => ['name' => 'Nijer', 'flag' => '🇳🇪'],
        'NG' => ['name' => 'Nijerya', 'flag' => '🇳🇬'],
        'KP' => ['name' => 'Kuzey Kore', 'flag' => '🇰🇵'],
        'MK' => ['name' => 'Kuzey Makedonya', 'flag' => '🇲🇰'],
        'NO' => ['name' => 'Norveç', 'flag' => '🇳🇴'],
        'OM' => ['name' => 'Umman', 'flag' => '🇴🇲'],
        'PK' => ['name' => 'Pakistan', 'flag' => '🇵🇰'],
        'PA' => ['name' => 'Panama', 'flag' => '🇵🇦'],
        'PG' => ['name' => 'Papua Yeni Gine', 'flag' => '🇵🇬'],
        'PY' => ['name' => 'Paraguay', 'flag' => '🇵🇾'],
        'PE' => ['name' => 'Peru', 'flag' => '🇵🇪'],
        'PH' => ['name' => 'Filipinler', 'flag' => '🇵🇭'],
        'PL' => ['name' => 'Polonya', 'flag' => '🇵🇱'],
        'PT' => ['name' => 'Portekiz', 'flag' => '🇵🇹'],
        'QA' => ['name' => 'Katar', 'flag' => '🇶🇦'],
        'RO' => ['name' => 'Romanya', 'flag' => '🇷🇴'],
        'RU' => ['name' => 'Rusya', 'flag' => '🇷🇺'],
        'RW' => ['name' => 'Ruanda', 'flag' => '🇷🇼'],
        'SA' => ['name' => 'Suudi Arabistan', 'flag' => '🇸🇦'],
        'SN' => ['name' => 'Senegal', 'flag' => '🇸🇳'],
        'RS' => ['name' => 'Sırbistan', 'flag' => '🇷🇸'],
        'SC' => ['name' => 'Seyşeller', 'flag' => '🇸🇨'],
        'SL' => ['name' => 'Sierra Leone', 'flag' => '🇸🇱'],
        'SG' => ['name' => 'Singapur', 'flag' => '🇸🇬'],
        'SK' => ['name' => 'Slovakya', 'flag' => '🇸🇰'],
        'SI' => ['name' => 'Slovenya', 'flag' => '🇸🇮'],
        'SB' => ['name' => 'Solomon Adaları', 'flag' => '🇸🇧'],
        'SO' => ['name' => 'Somali', 'flag' => '🇸🇴'],
        'ZA' => ['name' => 'Güney Afrika', 'flag' => '🇿🇦'],
        'KR' => ['name' => 'Güney Kore', 'flag' => '🇰🇷'],
        'ES' => ['name' => 'İspanya', 'flag' => '🇪🇸'],
        'LK' => ['name' => 'Sri Lanka', 'flag' => '🇱🇰'],
        'SD' => ['name' => 'Sudan', 'flag' => '🇸🇩'],
        'SR' => ['name' => 'Surinam', 'flag' => '🇸🇷'],
        'SE' => ['name' => 'İsveç', 'flag' => '🇸🇪'],
        'CH' => ['name' => 'İsviçre', 'flag' => '🇨🇭'],
        'SY' => ['name' => 'Suriye', 'flag' => '🇸🇾'],
        'TW' => ['name' => 'Tayvan', 'flag' => '🇹🇼'],
        'TJ' => ['name' => 'Tacikistan', 'flag' => '🇹🇯'],
        'TZ' => ['name' => 'Tanzanya', 'flag' => '🇹🇿'],
        'TH' => ['name' => 'Tayland', 'flag' => '🇹🇭'],
        'TG' => ['name' => 'Togo', 'flag' => '🇹🇬'],
        'TT' => ['name' => 'Trinidad ve Tobago', 'flag' => '🇹🇹'],
        'TN' => ['name' => 'Tunus', 'flag' => '🇹🇳'],
        'TR' => ['name' => 'Türkiye', 'flag' => '🇹🇷'],
        'TM' => ['name' => 'Türkmenistan', 'flag' => '🇹🇲'],
        'UG' => ['name' => 'Uganda', 'flag' => '🇺🇬'],
        'UA' => ['name' => 'Ukrayna', 'flag' => '🇺🇦'],
        'AE' => ['name' => 'Birleşik Arap Emirlikleri', 'flag' => '🇦🇪'],
        'GB' => ['name' => 'Birleşik Krallık', 'flag' => '🇬🇧'],
        'US' => ['name' => 'Amerika Birleşik Devletleri', 'flag' => '🇺🇸'],
        'UY' => ['name' => 'Uruguay', 'flag' => '🇺🇾'],
        'UZ' => ['name' => 'Özbekistan', 'flag' => '🇺🇿'],
        'VE' => ['name' => 'Venezuela', 'flag' => '🇻🇪'],
        'VN' => ['name' => 'Vietnam', 'flag' => '🇻🇳'],
        'YE' => ['name' => 'Yemen', 'flag' => '🇾🇪'],
        'ZM' => ['name' => 'Zambiya', 'flag' => '🇿🇲'],
        'ZW' => ['name' => 'Zimbabve', 'flag' => '🇿🇼'],
    ];
}

// --- Marketplace (PDO + RBAC + CSRF + uploads) ---
const MARKETPLACE_COMMISSION_RATE = 0.10;
const UPLOAD_MAX_BYTES = 5242880; // 5MB

function db_config(): array
{
    return [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'name' => getenv('DB_NAME') ?: 'if0_41108134_index',
        'user' => getenv('DB_USER') ?: '',
        'pass' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ];
}

function db(): ?PDO
{
    static $pdo = false;
    if ($pdo !== false) {
        return $pdo;
    }

    $cfg = db_config();
    if ($cfg['user'] === '') {
        $pdo = null;
        return null;
    }

    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $cfg['host'], $cfg['port'], $cfg['name'], $cfg['charset']);
        $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (Throwable $e) {
        $pdo = null;
    }

    return $pdo;
}

function db_available(): bool
{
    return db() instanceof PDO;
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_validate(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    $current = $_SESSION['_csrf'] ?? '';
    return is_string($token) && is_string($current) && $token !== '' && hash_equals($current, $token);
}

function require_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_validate()) {
        http_response_code(419);
        exit('Geçersiz CSRF token.');
    }
}

function role_slug(?string $role): string
{
    $r = mb_strtolower((string) $role);
    return match ($r) {
        'admin' => 'admin',
        'satıcı', 'satici', 'seller', 'vendor' => 'seller',
        default => 'buyer',
    };
}

function has_role(string $role): bool
{
    $user = current_user();
    return role_slug($user['role'] ?? null) === $role;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        header('Location: ' . url_path('auth/login.php'));
        exit;
    }
    return $user;
}

function require_role(array $allowed): array
{
    $user = require_login();
    $slug = role_slug($user['role'] ?? null);
    if (!in_array($slug, $allowed, true)) {
        http_response_code(403);
        exit('Bu işlem için yetkiniz yok.');
    }
    return $user;
}

function slugify(string $text): string
{
    $text = trim(mb_strtolower($text));
    $text = preg_replace('/[^\pL\pN]+/u', '-', $text) ?? '';
    $text = trim($text, '-');
    return $text !== '' ? $text : 'listing-' . bin2hex(random_bytes(3));
}

function upload_image(string $field, string $prefix = 'img_'): ?string
{
    if (empty($_FILES[$field]['name']) || !is_uploaded_file($_FILES[$field]['tmp_name'])) {
        return null;
    }

    if ((int) ($_FILES[$field]['size'] ?? 0) > UPLOAD_MAX_BYTES) {
        throw new RuntimeException('Dosya boyutu çok büyük.');
    }

    $tmp = $_FILES[$field]['tmp_name'];
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = (string) $finfo->file($tmp);

    $allowed = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($allowed[$mime])) {
        throw new RuntimeException('Sadece jpg/png/webp yüklenebilir.');
    }

    $uploadsDir = __DIR__ . '/uploads';
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0755, true);
    }

    $name = $prefix . bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $dest = $uploadsDir . '/' . $name;
    if (!move_uploaded_file($tmp, $dest)) {
        throw new RuntimeException('Dosya yüklenemedi.');
    }

    return url_path('uploads/' . $name);
}

function db_user_by_email(string $email): ?array
{
    $pdo = db();
    if (!$pdo) {
        return null;
    }
    $st = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
    $st->execute(['email' => $email]);
    $row = $st->fetch();
    return $row ?: null;
}

function db_sync_session_user(array $dbUser): void
{
    $_SESSION['user'] = [
        'id' => (int) $dbUser['id'],
        'name' => $dbUser['name'],
        'email' => $dbUser['email'],
        'role' => $dbUser['role'] === 'Seller' ? 'Satıcı' : ($dbUser['role'] === 'Admin' ? 'Admin' : 'Kullanıcı'),
        'avatar' => $dbUser['avatar_url'] ?: 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=facearea&w=160&h=160&q=80',
        'phone' => $dbUser['phone'] ?? '',
        'country' => $dbUser['country_code'] ?? 'TR',
        'vip' => !empty($dbUser['vip']),
        'purchases' => (int) ($dbUser['purchases'] ?? 0),
    ];
}

function create_order_and_wallet_credit(PDO $pdo, array $auction): void
{
    $commission = round((float) $auction['current_price'] * MARKETPLACE_COMMISSION_RATE, 2);
    $sellerAmount = round((float) $auction['current_price'] - $commission, 2);

    $pdo->prepare('INSERT INTO orders (listing_id, seller_id, buyer_id, total_amount, commission_amount, seller_amount, status, created_at) VALUES (:listing_id,:seller_id,:buyer_id,:total,:commission,:seller_amount,\'pending_payment\',NOW())')
        ->execute([
            'listing_id' => $auction['listing_id'],
            'seller_id' => $auction['seller_id'],
            'buyer_id' => $auction['current_winner_id'],
            'total' => $auction['current_price'],
            'commission' => $commission,
            'seller_amount' => $sellerAmount,
        ]);

    $orderId = (int) $pdo->lastInsertId();

    $pdo->prepare('INSERT INTO seller_wallets (seller_id, balance, updated_at) VALUES (:seller_id, :amount, NOW()) ON DUPLICATE KEY UPDATE balance = balance + VALUES(balance), updated_at = NOW()')
        ->execute(['seller_id' => $auction['seller_id'], 'amount' => $sellerAmount]);

    $pdo->prepare('INSERT INTO wallet_transactions (seller_id, order_id, amount, type, created_at) VALUES (:seller_id,:order_id,:amount,\'credit\',NOW())')
        ->execute([
            'seller_id' => $auction['seller_id'],
            'order_id' => $orderId,
            'amount' => $sellerAmount,
        ]);
}
