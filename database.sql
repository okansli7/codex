-- Artirup Multi Vendor + Auction Schema (MySQL InnoDB)
CREATE DATABASE IF NOT EXISTS `if0_41108134_index`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `if0_41108134_index`;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('Admin','Seller','Buyer') NOT NULL DEFAULT 'Buyer',
  avatar_url VARCHAR(255) NULL,
  phone VARCHAR(40) NULL,
  country_code CHAR(2) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS seller_requests (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS seller_profiles (
  user_id BIGINT UNSIGNED PRIMARY KEY,
  store_name VARCHAR(160) NOT NULL,
  slug VARCHAR(180) NOT NULL UNIQUE,
  logo VARCHAR(255) NULL,
  banner VARCHAR(255) NULL,
  description TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS listings (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  seller_id BIGINT UNSIGNED NOT NULL,
  type ENUM('fixed_price','auction') NOT NULL,
  title VARCHAR(220) NOT NULL,
  slug VARCHAR(240) NOT NULL UNIQUE,
  description TEXT NOT NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  status ENUM('pending','published','rejected') NOT NULL DEFAULT 'pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_listings_status (status),
  INDEX idx_listings_seller (seller_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS listing_images (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  listing_id BIGINT UNSIGNED NOT NULL,
  path VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS auctions (
  listing_id BIGINT UNSIGNED PRIMARY KEY,
  start_time DATETIME NOT NULL,
  end_time DATETIME NOT NULL,
  starting_price DECIMAL(12,2) NOT NULL,
  min_increment DECIMAL(12,2) NOT NULL,
  reserve_price DECIMAL(12,2) NULL,
  current_price DECIMAL(12,2) NOT NULL,
  current_winner_id BIGINT UNSIGNED NULL,
  status ENUM('scheduled','active','ended','ended_no_winner','cancelled') NOT NULL DEFAULT 'scheduled',
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
  FOREIGN KEY (current_winner_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_auctions_status_time (status, end_time),
  INDEX idx_auctions_end_time (end_time),
  INDEX idx_auctions_status (status)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS bids (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  listing_id BIGINT UNSIGNED NOT NULL,
  user_id BIGINT UNSIGNED NOT NULL,
  amount DECIMAL(12,2) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_bids_listing_created (listing_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  listing_id BIGINT UNSIGNED NOT NULL,
  seller_id BIGINT UNSIGNED NOT NULL,
  buyer_id BIGINT UNSIGNED NOT NULL,
  total_amount DECIMAL(12,2) NOT NULL,
  commission_amount DECIMAL(12,2) NOT NULL,
  seller_amount DECIMAL(12,2) NOT NULL,
  status ENUM('pending_payment','paid','cancelled','refunded') NOT NULL DEFAULT 'pending_payment',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (buyer_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_orders_seller (seller_id),
  INDEX idx_orders_buyer (buyer_id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS seller_wallets (
  seller_id BIGINT UNSIGNED PRIMARY KEY,
  balance DECIMAL(12,2) NOT NULL DEFAULT 0,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wallet_transactions (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  seller_id BIGINT UNSIGNED NOT NULL,
  order_id BIGINT UNSIGNED NULL,
  amount DECIMAL(12,2) NOT NULL,
  type ENUM('credit','debit') NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
  INDEX idx_wallet_tx_seller (seller_id, created_at)
) ENGINE=InnoDB;

INSERT INTO users (name, email, password_hash, role)
VALUES
  ('Artirup Admin', 'admin@artirup.com', '$2y$10$replace_with_bcrypt_hash', 'Admin'),
  ('Demo Buyer', 'buyer@artirup.com', '$2y$10$replace_with_bcrypt_hash', 'Buyer')
ON DUPLICATE KEY UPDATE name = VALUES(name);


CREATE TABLE IF NOT EXISTS settings (
  k VARCHAR(64) PRIMARY KEY,
  v TEXT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS home_slides (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  eyebrow VARCHAR(140) NULL,
  title VARCHAR(180) NOT NULL,
  `desc` TEXT NULL,
  cta VARCHAR(80) NULL,
  button_url VARCHAR(255) NULL,
  image_path VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS home_sections (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  type ENUM('ending_soon_auctions','new_listings','categories_grid','banner','html_block') NOT NULL,
  title VARCHAR(180) NOT NULL,
  settings_json JSON NULL,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pages_static (
  slug VARCHAR(120) PRIMARY KEY,
  title VARCHAR(180) NOT NULL,
  body_html MEDIUMTEXT NOT NULL,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL UNIQUE,
  sort_order INT NOT NULL DEFAULT 0,
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (k, v) VALUES
('commission_rate','0.10'),
('extend_window_seconds','120'),
('extend_by_seconds','120'),
('default_min_increment','10'),
('support_phone','+90 850 840 00 00'),
('support_email','destek@artirup.com'),
('footer_text','Artirup © 2050'),
('site_logo',''),
('brand_name','Artirup')
ON DUPLICATE KEY UPDATE v = VALUES(v);

INSERT INTO pages_static (slug,title,body_html) VALUES
('about','Hakkımızda','<p>Artirup hakkında sayfa içeriği.</p>'),
('privacy','Gizlilik Politikası','<p>Gizlilik metni.</p>'),
('terms','Kullanım Şartları','<p>Kullanım şartları.</p>'),
('faq','Sık Sorulan Sorular','<p>SSS içeriği.</p>')
ON DUPLICATE KEY UPDATE title=VALUES(title), body_html=VALUES(body_html);


CREATE TABLE IF NOT EXISTS audit_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  action VARCHAR(80) NOT NULL,
  entity VARCHAR(80) NOT NULL,
  entity_id BIGINT UNSIGNED NULL,
  ip VARCHAR(64) NOT NULL,
  ua VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_audit_user (user_id),
  INDEX idx_audit_action (action),
  INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;
