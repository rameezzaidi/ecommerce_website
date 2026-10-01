-- DRESSY Fashion Store  |  Fresh install schema (MySQL 5.7+ / MariaDB 10.3+)
-- Import this file first, then 02_seed.sql (demo data).
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS admins, settings, banners, categories, products, product_images, customers, coupons,
  orders, order_items, demand_orders, custom_parts, reviews, contact_messages, subscribers, pages,
  traffic_logs, geo_cache;

CREATE TABLE admins (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  last_login DATETIME NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE settings (
  skey VARCHAR(80) PRIMARY KEY,
  svalue TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE banners (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  eyebrow VARCHAR(120) NULL,
  title VARCHAR(160) NOT NULL,
  subtitle VARCHAR(255) NULL,
  button_text VARCHAR(60) NULL,
  link VARCHAR(255) NULL,
  image VARCHAR(255) NULL,
  position VARCHAR(20) NOT NULL DEFAULT 'hero',   -- hero | promo
  sort_order INT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE categories (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  parent_id INT UNSIGNED NOT NULL DEFAULT 0,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL UNIQUE,
  description VARCHAR(255) NULL,
  image VARCHAR(255) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  KEY (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE products (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  category_id INT UNSIGNED NOT NULL,
  name VARCHAR(180) NOT NULL,
  slug VARCHAR(200) NOT NULL UNIQUE,
  sku VARCHAR(60) NULL,
  short_description VARCHAR(300) NULL,
  description TEXT NULL,
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  sale_price DECIMAL(10,2) NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(255) NULL,
  sizes VARCHAR(255) NULL,           -- comma separated: S,M,L,XL
  colors VARCHAR(255) NULL,          -- Name|#hex,Name|#hex
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  is_new TINYINT(1) NOT NULL DEFAULT 0,
  allow_demand TINYINT(1) NOT NULL DEFAULT 1,   -- customer may "order on demand" when out of stock
  sold_count INT NOT NULL DEFAULT 0,
  views INT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  KEY (category_id), KEY (is_featured), KEY (sold_count), KEY (status),
  FULLTEXT KEY ft_search (name, short_description)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE product_images (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  image VARCHAR(255) NOT NULL,
  sort_order INT NOT NULL DEFAULT 0,
  KEY (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE customers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  phone VARCHAR(40) NULL,
  password VARCHAR(255) NOT NULL,
  address VARCHAR(255) NULL,
  city VARCHAR(80) NULL,
  country VARCHAR(80) NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE coupons (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(40) NOT NULL UNIQUE,
  type VARCHAR(10) NOT NULL DEFAULT 'percent',   -- percent | fixed
  value DECIMAL(10,2) NOT NULL,
  min_order DECIMAL(10,2) NOT NULL DEFAULT 0,
  max_uses INT NOT NULL DEFAULT 0,               -- 0 = unlimited
  used_count INT NOT NULL DEFAULT 0,
  expires_at DATE NULL,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_no VARCHAR(30) NOT NULL UNIQUE,
  customer_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  address VARCHAR(255) NOT NULL,
  city VARCHAR(80) NOT NULL,
  state VARCHAR(80) NULL,
  postal_code VARCHAR(20) NULL,
  country VARCHAR(80) NOT NULL,
  country_code CHAR(2) NOT NULL DEFAULT 'PK',
  payment_method VARCHAR(30) NOT NULL DEFAULT 'cod',
  payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid',  -- unpaid | paid | refunded
  status VARCHAR(20) NOT NULL DEFAULT 'pending',          -- pending|confirmed|processing|shipped|delivered|cancelled
  order_type VARCHAR(20) NOT NULL DEFAULT 'standard',     -- standard | custom | mixed
  subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
  discount DECIMAL(10,2) NOT NULL DEFAULT 0,
  coupon_code VARCHAR(40) NULL,
  shipping DECIMAL(10,2) NOT NULL DEFAULT 0,
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  notes TEXT NULL,
  admin_note TEXT NULL,
  source VARCHAR(30) NOT NULL DEFAULT 'direct',           -- traffic source that brought the buyer
  ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  KEY (customer_id), KEY (status), KEY (created_at), KEY (source)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE order_items (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  order_id INT UNSIGNED NOT NULL,
  product_id INT UNSIGNED NULL,
  item_type VARCHAR(12) NOT NULL DEFAULT 'product',       -- product | package
  name VARCHAR(220) NOT NULL,
  image VARCHAR(255) NULL,
  size VARCHAR(40) NULL,
  color VARCHAR(60) NULL,
  price DECIMAL(10,2) NOT NULL,
  qty INT NOT NULL DEFAULT 1,
  line_total DECIMAL(10,2) NOT NULL,
  meta TEXT NULL,                                         -- JSON (customised package selection)
  KEY (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE demand_orders (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  demand_no VARCHAR(30) NOT NULL UNIQUE,
  product_id INT UNSIGNED NULL,
  customer_id INT UNSIGNED NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  phone VARCHAR(40) NOT NULL,
  product_name VARCHAR(220) NOT NULL,
  description TEXT NULL,
  size VARCHAR(40) NULL,
  color VARCHAR(60) NULL,
  qty INT NOT NULL DEFAULT 1,
  budget DECIMAL(10,2) NULL,
  reference_image VARCHAR(255) NULL,
  address VARCHAR(255) NULL,
  city VARCHAR(80) NULL,
  country VARCHAR(80) NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pending',          -- pending|reviewing|quoted|accepted|confirmed|rejected|fulfilled
  quote_price DECIMAL(10,2) NULL,
  eta_days INT NULL,
  admin_note TEXT NULL,
  source VARCHAR(30) NOT NULL DEFAULT 'direct',
  created_at DATETIME NOT NULL,
  updated_at DATETIME NULL,
  KEY (status), KEY (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE custom_parts (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  part_type VARCHAR(10) NOT NULL,                         -- jeans | tshirt | shirt | shoes
  name VARCHAR(120) NOT NULL,
  style VARCHAR(30) NOT NULL,                             -- drawing style key used by the mannequin preview
  color_name VARCHAR(40) NOT NULL,
  color_hex CHAR(7) NOT NULL DEFAULT '#111111',
  price DECIMAL(10,2) NOT NULL DEFAULT 0,
  sort_order INT NOT NULL DEFAULT 0,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL,
  KEY (part_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE reviews (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  product_id INT UNSIGNED NOT NULL,
  name VARCHAR(120) NOT NULL,
  rating TINYINT NOT NULL DEFAULT 5,
  comment TEXT NULL,
  status TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  KEY (product_id), KEY (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_messages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL,
  subject VARCHAR(200) NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE subscribers (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(160) NOT NULL UNIQUE,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE pages (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(160) NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  content MEDIUMTEXT NULL,
  show_in_footer TINYINT(1) NOT NULL DEFAULT 1,
  status TINYINT(1) NOT NULL DEFAULT 1,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE traffic_logs (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  visit_token VARCHAR(32) NOT NULL,
  is_new_visit TINYINT(1) NOT NULL DEFAULT 0,
  source VARCHAR(30) NOT NULL DEFAULT 'direct',           -- facebook|instagram|tiktok|whatsapp|google|youtube|other|direct
  referrer VARCHAR(255) NULL,
  page VARCHAR(255) NULL,
  country VARCHAR(80) NULL,
  country_code CHAR(2) NULL,
  region VARCHAR(80) NULL,
  city VARCHAR(80) NULL,
  device VARCHAR(12) NULL,                                -- mobile | tablet | desktop
  browser VARCHAR(30) NULL,
  ip VARCHAR(45) NULL,
  created_at DATETIME NOT NULL,
  KEY (created_at), KEY (source), KEY (country_code), KEY (visit_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE geo_cache (
  ip VARCHAR(45) PRIMARY KEY,
  country VARCHAR(80) NULL,
  country_code CHAR(2) NULL,
  region VARCHAR(80) NULL,
  city VARCHAR(80) NULL,
  updated_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
