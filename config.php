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
    ];
}

if (!isset($_SESSION['settings'])) {
    $_SESSION['settings'] = default_site_settings();
}

if (!isset($_SESSION['products'])) {
    $_SESSION['products'] = [
        [
            'id' => 1,
            'title' => 'Retro Teknoloji Lotları',
            'seller' => 'Nova Tech',
            'status' => 'Yayında',
            'lots' => 42,
            'end' => '12 Mar 21:00',
            'price' => 1250,
        ],
        [
            'id' => 2,
            'title' => 'Sanat & Koleksiyon',
            'seller' => 'Studio 55',
            'status' => 'Onay Bekliyor',
            'lots' => 18,
            'end' => '13 Mar 20:15',
            'price' => 980,
        ],
        [
            'id' => 3,
            'title' => 'Otomotiv Özel Lot',
            'seller' => 'DriveX',
            'status' => 'Yayında',
            'lots' => 8,
            'end' => '14 Mar 18:00',
            'price' => 1850,
        ],
    ];
}

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
