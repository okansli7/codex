# Artirup Marketplace

## Ready-to-Launch Notları

### 1) Cron (Açık artırma kapatma)
Aşağıdaki komutla kapanış scriptini periyodik çalıştırın:

```bash
php /path/to/project/scripts/close_auctions.php
```

- Log dosyası: `logs/cron_auctions.log`
- Script, `end_time <= NOW()` olan açık artırmaları transaction + `FOR UPDATE` ile güvenli şekilde finalize eder.

### 2) Admin CMS Kullanımı
- **Slider Yönetimi:** `/admin/home_slider.php`
  - tablo: `home_slides`
  - alanlar: `eyebrow, title, desc, cta, button_url, image_path, sort_order, is_active`
- **Section Builder:** `/admin/home_sections.php`
  - tablo: `home_sections`
  - tipler: `ending_soon_auctions`, `new_listings`, `categories_grid`, `banner`, `html_block`
- **Global Ayarlar:** `/admin/settings.php`
  - tablo: `settings(k,v)`
  - anahtarlar: `commission_rate`, `default_min_increment`, `extend_window_seconds`, `extend_by_seconds`, `support_phone`, `support_email`, `footer_text`, `site_logo`, `brand_name`
- **Static Pages:** `/admin/pages.php`
  - tablo: `pages_static`
  - public: `/pages/static.php?slug=about|privacy|terms|faq`

### 3) Auction Kuralları
- **Min teklif mantığı**
  - Teklif yoksa: minimum teklif = `starting_price`
  - Teklif varsa: minimum teklif = `current_price + min_increment`
- **Anti-sniping**
  - Son `extend_window_seconds` içinde teklif gelirse `end_time` otomatik `extend_by_seconds` kadar uzatılır.
- **Kazanan gizleme**
  - Endpoint/UI kazananı maskeli gösterir (`E*** Y***`), ham PII dönmez.
- **Canlı state endpoint**
  - `GET /pages/auction_state.php?id=LISTING_ID`
  - dönen alanlar: `status, current_price, end_time, server_time, bid_count, min_increment, min_valid_bid, winner_masked, reserve_met`
