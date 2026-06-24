-- ============================================================
-- ConsumerConnect RSA - Database Schema
-- MySQL 8.0+
-- ============================================================

CREATE DATABASE IF NOT EXISTS consumerconnect_rsa
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE consumerconnect_rsa;

-- ─────────────────────────────────────────
-- ROLES
-- ─────────────────────────────────────────
CREATE TABLE roles (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name        VARCHAR(50) NOT NULL UNIQUE,
  description TEXT,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO roles (name, description) VALUES
  ('super_admin', 'Full platform control'),
  ('admin',       'General administration'),
  ('moderator',   'Content moderation'),
  ('seller',      'Can list and sell products'),
  ('buyer',       'Can browse and purchase products');

-- ─────────────────────────────────────────
-- USERS
-- ─────────────────────────────────────────
CREATE TABLE users (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  role_id       INT UNSIGNED NOT NULL DEFAULT 5,
  first_name    VARCHAR(80) NOT NULL,
  last_name     VARCHAR(80) NOT NULL,
  email         VARCHAR(191) NOT NULL UNIQUE,
  phone         VARCHAR(20),
  password_hash VARCHAR(255) NOT NULL,
  avatar        VARCHAR(255),
  province      VARCHAR(80),
  city          VARCHAR(80),
  status        ENUM('active','suspended','pending') DEFAULT 'pending',
  verified_at   TIMESTAMP NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (role_id) REFERENCES roles(id)
);

-- Default super-admin (password: Admin@1234)
INSERT INTO users (role_id, first_name, last_name, email, password_hash, status, verified_at)
VALUES (1, 'Super', 'Admin', 'admin@consumerconnect.co.za',
        '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- hashed 'Admin@1234'
        'active', NOW());

-- ─────────────────────────────────────────
-- CATEGORIES
-- ─────────────────────────────────────────
CREATE TABLE categories (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id   INT UNSIGNED NULL,
  name        VARCHAR(100) NOT NULL,
  slug        VARCHAR(100) NOT NULL UNIQUE,
  icon        VARCHAR(50) DEFAULT 'tag',
  sort_order  TINYINT UNSIGNED DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
);

INSERT INTO categories (name, slug, icon, sort_order) VALUES
  ('Electronics',      'electronics',      'cpu',         1),
  ('Clothing',         'clothing',         'shirt',       2),
  ('Home & Garden',    'home-garden',      'home',        3),
  ('Vehicles',         'vehicles',         'car',         4),
  ('Food & Groceries', 'food-groceries',   'shopping-bag',5),
  ('Sports & Leisure', 'sports-leisure',   'activity',    6),
  ('Books & Education','books-education',  'book',        7),
  ('Services',         'services',         'briefcase',   8);

-- ─────────────────────────────────────────
-- PRODUCTS
-- ─────────────────────────────────────────
CREATE TABLE products (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  seller_id     INT UNSIGNED NOT NULL,
  category_id   INT UNSIGNED NOT NULL,
  title         VARCHAR(255) NOT NULL,
  slug          VARCHAR(255) NOT NULL UNIQUE,
  description   TEXT,
  price         DECIMAL(10,2) NOT NULL,
  stock         INT UNSIGNED DEFAULT 0,
  condition_type ENUM('new','used','refurbished') DEFAULT 'new',
  province      VARCHAR(80),
  city          VARCHAR(80),
  status        ENUM('active','sold','pending','rejected') DEFAULT 'pending',
  views         INT UNSIGNED DEFAULT 0,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (seller_id)   REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- ─────────────────────────────────────────
-- PRODUCT IMAGES
-- ─────────────────────────────────────────
CREATE TABLE product_images (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  filename   VARCHAR(255) NOT NULL,
  is_primary TINYINT(1) DEFAULT 0,
  sort_order TINYINT UNSIGNED DEFAULT 0,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- ORDERS
-- ─────────────────────────────────────────
CREATE TABLE orders (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  buyer_id        INT UNSIGNED NOT NULL,
  total_amount    DECIMAL(10,2) NOT NULL,
  status          ENUM('pending','paid','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
  payment_method  VARCHAR(50) DEFAULT 'eft',
  payment_ref     VARCHAR(100),
  shipping_addr   TEXT,
  notes           TEXT,
  created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (buyer_id) REFERENCES users(id)
);

-- ─────────────────────────────────────────
-- ORDER ITEMS
-- ─────────────────────────────────────────
CREATE TABLE order_items (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id    INT UNSIGNED NOT NULL,
  product_id  INT UNSIGNED NOT NULL,
  seller_id   INT UNSIGNED NOT NULL,
  quantity    SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  unit_price  DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (seller_id)  REFERENCES users(id)
);

-- ─────────────────────────────────────────
-- CART
-- ─────────────────────────────────────────
CREATE TABLE cart (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NOT NULL,
  quantity   SMALLINT UNSIGNED DEFAULT 1,
  added_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_cart (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- ─────────────────────────────────────────
-- REVIEWS
-- ─────────────────────────────────────────
CREATE TABLE reviews (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id  INT UNSIGNED NOT NULL,
  reviewer_id INT UNSIGNED NOT NULL,
  rating      TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
  comment     TEXT,
  status      ENUM('visible','hidden') DEFAULT 'visible',
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (product_id)  REFERENCES products(id) ON DELETE CASCADE,
  FOREIGN KEY (reviewer_id) REFERENCES users(id)
);

-- ─────────────────────────────────────────
-- MESSAGES (Buyer ↔ Seller)
-- ─────────────────────────────────────────
CREATE TABLE messages (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  sender_id   INT UNSIGNED NOT NULL,
  receiver_id INT UNSIGNED NOT NULL,
  product_id  INT UNSIGNED,
  body        TEXT NOT NULL,
  is_read     TINYINT(1) DEFAULT 0,
  sent_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (sender_id)   REFERENCES users(id),
  FOREIGN KEY (receiver_id) REFERENCES users(id),
  FOREIGN KEY (product_id)  REFERENCES products(id) ON DELETE SET NULL
);

-- ─────────────────────────────────────────
-- AUDIT LOG
-- ─────────────────────────────────────────
CREATE TABLE audit_log (
  id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id    INT UNSIGNED,
  action     VARCHAR(100) NOT NULL,
  target     VARCHAR(100),
  target_id  INT UNSIGNED,
  details    TEXT,
  ip_address VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- ─────────────────────────────────────────
-- SETTINGS
-- ─────────────────────────────────────────
CREATE TABLE settings (
  key_name  VARCHAR(100) PRIMARY KEY,
  value     TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO settings (key_name, value) VALUES
  ('site_name',          'ConsumerConnect RSA'),
  ('site_tagline',       'Buy & Sell Across South Africa'),
  ('commission_rate',    '5.00'),
  ('maintenance_mode',   '0'),
  ('currency_symbol',    'R'),
  ('support_email',      'support@consumerconnect.co.za');

-- ──────────────────────────
-- CONTACT MESSAGES (Support / Contact Us form)
-- ─────────────────────────
CREATE TABLE contact_messages (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id     INT UNSIGNED NULL,
  name        VARCHAR(150) NOT NULL,
  email       VARCHAR(191) NOT NULL,
  subject     VARCHAR(150) NOT NULL,
  message     TEXT NOT NULL,
  status      ENUM("new","in_progress","resolved") DEFAULT "new",
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);
