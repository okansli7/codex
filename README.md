# Artirup Marketplace

## Admin CMS & Premium Auction Davranışı

### Admin CMS
- **Slider Yönetimi:** `/admin/home_slider.php`
  - `home_slides` tablosundan slider CRUD, sıralama (`sort_order`) ve aktif/pasif (`is_active`) yönetimi.
- **Ana Sayfa Section Builder:** `/admin/home_sections.php`
  - `home_sections` üzerinden dinamik section tipleri: `featured_listings`, `auction_ending_soon`, `categories_grid`, `banner`, `html_block`.
- **Global Ayarlar:** `/admin/settings.php`
  - `settings` tablosunda şu anahtarlar yönetilir:
    - `commission_rate`
    - `default_min_increment`
    - `extend_window_seconds`
    - `extend_by_seconds`
    - `footer_text`
    - `social_links`
- **Static Page Manager:** `/admin/pages.php`
  - `pages_static` tablosunda slug bazlı içerik yönetimi (`about`, `privacy`, `terms`, `faq`).
  - Public render: `/pages/static.php?slug=...`

### Premium Auction
- **Canlı state endpoint:** `/pages/auction_state.php`
  - JSON: `current_price`, `bid_count`, `highest_bidder_masked`, `end_time`, `status`, `server_time`
- **Bid anti-sniping:** `/pages/listing.php`
  - Teklif, `end_time`a `extend_window_seconds` içinde geldiyse otomatik `extend_by_seconds` kadar uzatılır.
- **Auction kapanış scripti:** `/scripts/close_auctions.php`
  - `FOR UPDATE` + transaction ile güvenli kapanış.
  - Senaryolar:
    - teklif yoksa `ended_no_winner`
    - reserve geçilmemişse `ended_no_winner`
    - geçerliyse order + wallet credit oluşturulur.
