CREATE DATABASE IF NOT EXISTS vitemines
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE vitemines;


-- ============================================================================
-- 1. ADMINS TABLE
-- ============================================================================
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;


-- ============================================================================
-- 2. USERS TABLE
-- ============================================================================
CREATE TABLE users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NULL,
    google_id VARCHAR(100) NULL UNIQUE,
    phone VARCHAR(30) NULL,
    address VARCHAR(255) NULL,
    city VARCHAR(80) NULL,
    is_blocked_from_commenting TINYINT(1) NOT NULL DEFAULT 0,
    is_blocked_from_rating TINYINT(1) NOT NULL DEFAULT 0,

    blocked_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_users_blocked_by_admin (blocked_by_admin_id),

    CONSTRAINT chk_users_auth
        CHECK (password_hash IS NOT NULL OR google_id IS NOT NULL),

    CONSTRAINT fk_users_blocked_by_admin
        FOREIGN KEY (blocked_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 3. CATEGORIES TABLE
-- ============================================================================
CREATE TABLE categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_categories_created_by_admin (created_by_admin_id),

    CONSTRAINT fk_categories_created_by_admin
        FOREIGN KEY (created_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 4. BRANDS TABLE
-- ============================================================================
CREATE TABLE brands (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    logo VARCHAR(255) NULL,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,

    created_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_brands_created_by_admin (created_by_admin_id),

    CONSTRAINT fk_brands_created_by_admin
        FOREIGN KEY (created_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 5. PRODUCTS TABLE
-- ============================================================================
CREATE TABLE products (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    brand_id INT UNSIGNED NULL,

    name VARCHAR(150) NOT NULL,
    description TEXT NULL,
    ingredients TEXT NULL,
    warnings TEXT NULL,

    price DECIMAL(10,2) NOT NULL,
    discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    stock INT UNSIGNED NOT NULL DEFAULT 0,

    sizes JSON NULL,
    weight DECIMAL(8,3) NULL,
    image JSON NULL,

    is_active TINYINT(1) NOT NULL DEFAULT 1,

    created_by_admin_id INT UNSIGNED NULL,
    updated_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_products_category (category_id),
    INDEX idx_products_brand (brand_id),
    INDEX idx_products_created_by_admin (created_by_admin_id),
    INDEX idx_products_updated_by_admin (updated_by_admin_id),

    CONSTRAINT fk_prod_cat
        FOREIGN KEY (category_id)
        REFERENCES categories(id),

    CONSTRAINT fk_prod_brand
        FOREIGN KEY (brand_id)
        REFERENCES brands(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_products_created_by_admin
        FOREIGN KEY (created_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,

    CONSTRAINT fk_products_updated_by_admin
        FOREIGN KEY (updated_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 6. ORDERS TABLE
-- ============================================================================
CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,

    status ENUM(
        'pending',
        'paid',
        'processing',
        'shipped',
        'delivered',
        'cancelled',
        'refunded'
    ) NOT NULL DEFAULT 'pending',

    total DECIMAL(10,2) NOT NULL,
    discount_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    shipping_total DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    ship_name VARCHAR(100) NOT NULL,
    ship_phone VARCHAR(30) NOT NULL,
    ship_address VARCHAR(255) NOT NULL,
    ship_city VARCHAR(80) NOT NULL,

    shipping_time DATETIME NULL,

    processed_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_orders_user (user_id),
    INDEX idx_orders_processed_by_admin (processed_by_admin_id),

    CONSTRAINT fk_orders_user
        FOREIGN KEY (user_id)
        REFERENCES users(id),

    CONSTRAINT fk_orders_processed_by_admin
        FOREIGN KEY (processed_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 7. ORDER ITEMS TABLE
-- ============================================================================
CREATE TABLE order_items (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    product_id INT UNSIGNED NULL,

    product_name VARCHAR(150) NOT NULL,
    size VARCHAR(20) NULL,
    quantity INT UNSIGNED NOT NULL,

    order_total DECIMAL(10,2) NOT NULL,
    delivery_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,

    INDEX idx_order_items_order (order_id),
    INDEX idx_order_items_product (product_id),

    CONSTRAINT fk_oi_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_oi_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE SET NULL
) ENGINE=InnoDB;


-- ============================================================================
-- 8. REVIEWS TABLE
-- ============================================================================
CREATE TABLE reviews (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    product_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,

    star_rating TINYINT UNSIGNED NOT NULL,
    comment TEXT NULL,
    image VARCHAR(255) NULL,

    moderated_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    rating_avg DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    rating_count INT UNSIGNED NOT NULL DEFAULT 0,

    UNIQUE KEY uq_review (product_id, user_id),

    INDEX idx_reviews_product (product_id),
    INDEX idx_reviews_user (user_id),
    INDEX idx_reviews_moderated_by_admin (moderated_by_admin_id),

    CONSTRAINT fk_rev_product
        FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_rev_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reviews_moderated_by_admin
        FOREIGN KEY (moderated_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 9. PAYMENTS TABLE
-- ============================================================================
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,

    method ENUM('visa', 'paypal', 'cash_on_delivery') NOT NULL,

    provider_transaction_id VARCHAR(120) NULL UNIQUE,
    amount DECIMAL(10,2) NOT NULL,
    card_last4 CHAR(4) NULL,
    paid_at DATETIME NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    status ENUM(
        'pending',
        'succeeded',
        'failed',
        'refunded'
    ) NOT NULL DEFAULT 'pending',

    reviewed_by_admin_id INT UNSIGNED NULL,

    INDEX idx_payments_order (order_id),
    INDEX idx_payments_reviewed_by_admin (reviewed_by_admin_id),

    CONSTRAINT fk_pay_order
        FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_payments_reviewed_by_admin
        FOREIGN KEY (reviewed_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;


-- ============================================================================
-- 10. AI ASSISTANT REQUESTS TABLE
-- ============================================================================
CREATE TABLE ai_assistant_requests (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NULL,

    handled_by_admin_id INT UNSIGNED NULL,

    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX idx_ai_requests_user (user_id),
    INDEX idx_ai_requests_handled_by_admin (handled_by_admin_id),

    CONSTRAINT fk_air_user
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE SET NULL,

    CONSTRAINT fk_ai_requests_handled_by_admin
        FOREIGN KEY (handled_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;