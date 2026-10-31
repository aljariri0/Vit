CREATE DATABASE IF NOT EXISTS vitemines
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE vitemines;
CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
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

    CONSTRAINT fk_rev_userA
        FOREIGN KEY (user_id)
        REFERENCES users(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_reviews_moderated_by_admin
        FOREIGN KEY (moderated_by_admin_id)
        REFERENCES admins(id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;
CREATE TABLE payments (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,

    method ENUM('card', 'paypal', 'cash_on_delivery') NOT NULL,

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

ALTER TABLE payments
    ADD COLUMN gateway_metadata JSON NULL AFTER provider_transaction_id;


ALTER TABLE reviews
ADD COLUMN status ENUM('pending', 'approved', 'rejected')
NOT NULL DEFAULT 'approved'
AFTER comment;

-- Screenshot category IDs; safe for the supplied empty schema or matching reference rows.
INSERT INTO categories (id, name, description, is_active) VALUES
(1, 'Vitamins', 'Individual vitamin supplements.', 1),
(2, 'Minerals', 'Mineral supplements.', 1),
(3, 'Multivitamins', 'Multivitamin supplements.', 1),
(4, 'Omega and Fish Oil', 'Omega and fish oil supplements.', 1),
(5, 'Probiotics', 'Probiotic supplements.', 1),
(6, 'Specialty Supplements', 'Other specialty supplements.', 1)
ON DUPLICATE KEY UPDATE id=id;

-- Screenshot brand IDs (including NOW Foods = 9).
INSERT INTO brands (id, name, logo, discount_percent) VALUES
(1, 'Nature Made', NULL, 0.00),
(3, 'Nature''s Bounty', NULL, 0.00),
(4, 'Solgar', NULL, 0.00),
(5, 'Garden of Life', NULL, 0.00),
(9, 'NOW Foods', NULL, 0.00)
ON DUPLICATE KEY UPDATE id=id;

-- Product inserts are idempotent by category, brand, and name.
INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin A 5000 IU', CONCAT('Solgar dietary supplement featuring Vitamin A 5000 IU.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-A-5000IU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin A 5000 IU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin B-Complex 50', CONCAT('Solgar dietary supplement featuring Vitamin B-Complex 50.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-B-Complex-50.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin B-Complex 50');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin B12 1000 mcg', CONCAT('Solgar dietary supplement featuring Vitamin B12 1000 mcg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-B12-1000mcg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin B12 1000 mcg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin C 1000 mg', CONCAT('Solgar dietary supplement featuring Vitamin C 1000 mg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-C-1000mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin C 1000 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin D3 5000 IU', CONCAT('Solgar dietary supplement featuring Vitamin D3 5000 IU.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-D3-5000IU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin D3 5000 IU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin D3', CONCAT('Solgar dietary supplement featuring Vitamin D3.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-D3.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin D3');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin E 400 IU', CONCAT('Solgar dietary supplement featuring Vitamin E 400 IU.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-E-400IU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin E 400 IU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 4, 'Vitamin K2 100 mcg', CONCAT('Solgar dietary supplement featuring Vitamin K2 100 mcg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Vitamins/Solgar-Vitamin-K2-100mcg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=4 AND p.name='Vitamin K2 100 mcg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 4, 'Calcium Citrate with Vitamin D3', CONCAT('Solgar dietary supplement featuring Calcium Citrate with Vitamin D3.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Minerals/Solgar-Calcium-Citrate-Vitamin-D3.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=4 AND p.name='Calcium Citrate with Vitamin D3');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 4, 'Calcium Magnesium Plus Zinc', CONCAT('Solgar dietary supplement featuring Calcium Magnesium Plus Zinc.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Minerals/Solgar-Calcium-Magnesium-Zinc.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=4 AND p.name='Calcium Magnesium Plus Zinc');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 4, 'Chromium Picolinate 200 mcg', CONCAT('Solgar dietary supplement featuring Chromium Picolinate 200 mcg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Minerals/Solgar-Chromium-Picolinate-200mcg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=4 AND p.name='Chromium Picolinate 200 mcg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 4, 'Magnesium Citrate', CONCAT('Solgar dietary supplement featuring Magnesium Citrate.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Minerals/Solgar-Magnesium-Citrate.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=4 AND p.name='Magnesium Citrate');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 4, 'Selenium 200 mcg', CONCAT('Solgar dietary supplement featuring Selenium 200 mcg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Minerals/Solgar-Selenium-200mcg.png', '/assets/products/Solgar/Minerals/Solgar-Selenium-200mcg-Official.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=4 AND p.name='Selenium 200 mcg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 4, 'Zinc Picolinate', CONCAT('Solgar dietary supplement featuring Zinc Picolinate.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Minerals/Solgar-Zinc-Picolinate.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=4 AND p.name='Zinc Picolinate');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 4, 'Female Multiple', CONCAT('Solgar dietary supplement featuring Female Multiple.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Multivitamins/Solgar-Female-Multiple.png', '/assets/products/Solgar/Multivitamins/Gallery-Female-Multiple-02.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=4 AND p.name='Female Multiple');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 4, 'Male Multiple', CONCAT('Solgar dietary supplement featuring Male Multiple.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Multivitamins/Solgar-Male-Multiple.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=4 AND p.name='Male Multiple');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 4, 'Omnium Phytonutrient Complex', CONCAT('Solgar dietary supplement featuring Omnium Phytonutrient Complex.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Multivitamins/Solgar-Omnium.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=4 AND p.name='Omnium Phytonutrient Complex');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 4, 'Formula VM-2000', CONCAT('Solgar dietary supplement featuring Formula VM-2000.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Multivitamins/Solgar-VM-2000.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=4 AND p.name='Formula VM-2000');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 4, 'Flaxseed Oil 1250 mg', CONCAT('Solgar dietary supplement featuring Flaxseed Oil 1250 mg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Omega and Fish Oil/Solgar-Flaxseed-Oil.png', '/assets/products/Solgar/Omega and Fish Oil/Gallery-Flaxseed-Oil-1250-mg-02.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=4 AND p.name='Flaxseed Oil 1250 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 4, 'Omega 3-6-9 Fish, Flax and Borage', CONCAT('Solgar dietary supplement featuring Omega 3-6-9 Fish, Flax and Borage.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Omega and Fish Oil/Solgar-Omega-3-6-9.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=4 AND p.name='Omega 3-6-9 Fish, Flax and Borage');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 4, 'Omega-3 Fish Oil', CONCAT('Solgar dietary supplement featuring Omega-3 Fish Oil.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Omega and Fish Oil/Solgar-Omega-3-Fish-Oil.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=4 AND p.name='Omega-3 Fish Oil');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 4, 'Triple Strength Omega-3 950 mg', CONCAT('Solgar dietary supplement featuring Triple Strength Omega-3 950 mg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Omega and Fish Oil/Solgar-Triple-Strength-Omega-3-950mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=4 AND p.name='Triple Strength Omega-3 950 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 4, 'Acidophilus 50 Vegetable Capsules', CONCAT('Solgar dietary supplement featuring Acidophilus 50 Vegetable Capsules.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Probiotics/Solgar-Acidophilus-50-Capsules.png', '/assets/products/Solgar/Probiotics/Solgar-Acidophilus-50-Capsules-RedLabel.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=4 AND p.name='Acidophilus 50 Vegetable Capsules');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 4, 'Advanced 40+ Acidophilus', CONCAT('Solgar dietary supplement featuring Advanced 40+ Acidophilus.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Probiotics/Solgar-Advanced-40-Acidophilus.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=4 AND p.name='Advanced 40+ Acidophilus');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 4, 'Advanced Acidophilus 100', CONCAT('Solgar dietary supplement featuring Advanced Acidophilus 100.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Probiotics/Solgar-Advanced-Acidophilus-100.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=4 AND p.name='Advanced Acidophilus 100');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 4, 'Advanced Acidophilus Plus', CONCAT('Solgar dietary supplement featuring Advanced Acidophilus Plus.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Probiotics/Solgar-Advanced-Acidophilus-Plus.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=4 AND p.name='Advanced Acidophilus Plus');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 4, 'Advanced Multi-Billion Dophilus', CONCAT('Solgar dietary supplement featuring Advanced Multi-Billion Dophilus.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Probiotics/Solgar-Advanced-Multi-Billion-Dophilus.png', '/assets/products/Solgar/Probiotics/Solgar-Advanced-Multi-Billion-Dophilus-Alt.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=4 AND p.name='Advanced Multi-Billion Dophilus');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 4, 'CoQ10 100 mg', CONCAT('Solgar dietary supplement featuring CoQ10 100 mg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Specialty Supplements/Solgar-CoQ10-100mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=4 AND p.name='CoQ10 100 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 4, 'Digestive Enzymes', CONCAT('Solgar dietary supplement featuring Digestive Enzymes.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Specialty Supplements/Solgar-Digestive-Enzymes.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=4 AND p.name='Digestive Enzymes');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 4, 'Full Spectrum Curcumin', CONCAT('Solgar dietary supplement featuring Full Spectrum Curcumin.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Specialty Supplements/Solgar-Full-Spectrum-Curcumin.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=4 AND p.name='Full Spectrum Curcumin');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 4, 'Grape Seed Extract 100 mg', CONCAT('Solgar dietary supplement featuring Grape Seed Extract 100 mg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Specialty Supplements/Solgar-Grape-Seed-Extract-100mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=4 AND p.name='Grape Seed Extract 100 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 4, 'Lutein 20 mg', CONCAT('Solgar dietary supplement featuring Lutein 20 mg.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Specialty Supplements/Solgar-Lutein-20mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=4 AND p.name='Lutein 20 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 4, 'Milk Thistle', CONCAT('Solgar dietary supplement featuring Milk Thistle.', CHAR(10), 'Check the product label for serving instructions and product details.'), NULL, CONCAT('Allergy warning: Check the full ingredient list for allergens before use.', CHAR(10), 'Ask a healthcare professional before use if you have a known allergy, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Solgar/Specialty Supplements/Solgar-Milk-Thistle.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=4 AND p.name='Milk Thistle');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 1, 'Vitamin D3 1000 IU', CONCAT('Nature Made dietary supplement: Vitamin D3 1000 IU.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature Made/Vitamins/Gallery-Vitamin-D3-1000-IU-02.png', '/assets/products/Nature Made/Vitamins/Gallery-Vitamin-D3-1000-IU-03.png', '/assets/products/Nature Made/Vitamins/Gallery-Vitamin-D3-1000-IU-04.png', '/assets/products/Nature Made/Vitamins/Gallery-Vitamins-f11a9e862a1c4996bd21a773f0740216.png', '/assets/products/Nature Made/Vitamins/Nature-Made-Vitamin-D3-1000-IU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=1 AND p.name='Vitamin D3 1000 IU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 1, 'Magnesium 250 mg 200 Tablets', CONCAT('Nature Made dietary supplement: Magnesium 250 mg 200 Tablets.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-02.png', '/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-03.png', '/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-04.png', '/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-05.png', '/assets/products/Nature Made/Minerals/Nature-Made-Magnesium-250-mg-200-Tablets.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=1 AND p.name='Magnesium 250 mg 200 Tablets');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 1, 'Multi For Her 90 Tablets', CONCAT('Nature Made dietary supplement: Multi For Her 90 Tablets.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature Made/Multivitamins/Gallery-Multi-For-Her-90-Tablets-02.png', '/assets/products/Nature Made/Multivitamins/Gallery-Multi-For-Her-90-Tablets-03.png', '/assets/products/Nature Made/Multivitamins/Gallery-Multi-For-Her-90-Tablets-04.png', '/assets/products/Nature Made/Multivitamins/Gallery-Multi-For-Her-90-Tablets-05.png', '/assets/products/Nature Made/Multivitamins/Nature-Made-Multi-For-Her-90-Tablets.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=1 AND p.name='Multi For Her 90 Tablets');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 1, 'Fish Oil 1200 mg', CONCAT('Nature Made dietary supplement: Fish Oil 1200 mg.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature Made/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-02.png', '/assets/products/Nature Made/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-03.png', '/assets/products/Nature Made/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-04.png', '/assets/products/Nature Made/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-05.png', '/assets/products/Nature Made/Omega and Fish Oil/Nature-Made-Fish-Oil-1200-mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=1 AND p.name='Fish Oil 1200 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 1, 'Daily Probiotic 1 Billion CFU', CONCAT('Nature Made dietary supplement: Daily Probiotic 1 Billion CFU.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature Made/Probiotics/Gallery-Daily-Probiotic-1-Billion-CFU-02.png', '/assets/products/Nature Made/Probiotics/Gallery-Daily-Probiotic-1-Billion-CFU-03.png', '/assets/products/Nature Made/Probiotics/Gallery-Daily-Probiotic-1-Billion-CFU-04.png', '/assets/products/Nature Made/Probiotics/Gallery-Daily-Probiotic-1-Billion-CFU-05.png', '/assets/products/Nature Made/Probiotics/Nature-Made-Daily-Probiotic-1-Billion-CFU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=1 AND p.name='Daily Probiotic 1 Billion CFU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 1, 'CoQ10 200 mg 40 Softgels', CONCAT('Nature Made dietary supplement: CoQ10 200 mg 40 Softgels.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-02.png', '/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-03.png', '/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-04.png', '/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-05.png', '/assets/products/Nature Made/Specialty Supplements/Nature-Made-CoQ10-200-mg-40-Softgels.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=1 AND p.name='CoQ10 200 mg 40 Softgels');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 3, 'Vitamin D3 1000 IU', CONCAT('Nature''s Bounty dietary supplement: Vitamin D3 1000 IU.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature''s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-02.png', '/assets/products/Nature''s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-03.png', '/assets/products/Nature''s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-04.png', '/assets/products/Nature''s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-05.png', '/assets/products/Nature''s Bounty/Vitamins/Nature-s-Bounty-Vitamin-D3-1000-IU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=3 AND p.name='Vitamin D3 1000 IU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 3, 'Magnesium 500 mg', CONCAT('Nature''s Bounty dietary supplement: Magnesium 500 mg.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature''s Bounty/Minerals/Gallery-Magnesium-500-mg-02.png', '/assets/products/Nature''s Bounty/Minerals/Gallery-Magnesium-500-mg-03.png', '/assets/products/Nature''s Bounty/Minerals/Gallery-Magnesium-500-mg-04.png', '/assets/products/Nature''s Bounty/Minerals/Gallery-Magnesium-500-mg-05.png', '/assets/products/Nature''s Bounty/Minerals/Nature-s-Bounty-Magnesium-500-mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=3 AND p.name='Magnesium 500 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 3, 'Daily Multi 100 Caplets', CONCAT('Nature''s Bounty dietary supplement: Daily Multi 100 Caplets.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature''s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-02.png', '/assets/products/Nature''s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-03.png', '/assets/products/Nature''s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-04.png', '/assets/products/Nature''s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-05.png', '/assets/products/Nature''s Bounty/Multivitamins/Nature-s-Bounty-Daily-Multi-100-Caplets.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=3 AND p.name='Daily Multi 100 Caplets');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 3, 'Fish Oil 1200 mg', CONCAT('Nature''s Bounty dietary supplement: Fish Oil 1200 mg.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature''s Bounty/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-02.png', '/assets/products/Nature''s Bounty/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-03.png', '/assets/products/Nature''s Bounty/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-04.png', '/assets/products/Nature''s Bounty/Omega and Fish Oil/Gallery-Fish-Oil-1200-mg-05.png', '/assets/products/Nature''s Bounty/Omega and Fish Oil/Nature-s-Bounty-Fish-Oil-1200-mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=3 AND p.name='Fish Oil 1200 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 3, 'Acidophilus 100 Million 120 Tablets', CONCAT('Nature''s Bounty dietary supplement: Acidophilus 100 Million 120 Tablets.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature''s Bounty/Probiotics/Gallery-Acidophilus-100-Million-120-Tablets-02.png', '/assets/products/Nature''s Bounty/Probiotics/Gallery-Acidophilus-100-Million-120-Tablets-03.png', '/assets/products/Nature''s Bounty/Probiotics/Gallery-Acidophilus-100-Million-120-Tablets-04.png', '/assets/products/Nature''s Bounty/Probiotics/Gallery-Acidophilus-100-Million-120-Tablets-05.png', '/assets/products/Nature''s Bounty/Probiotics/Nature-s-Bounty-Acidophilus-100-Million-120-Tablets.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=3 AND p.name='Acidophilus 100 Million 120 Tablets');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 3, 'Co Q-10 100 mg 45 Softgels', CONCAT('Nature''s Bounty dietary supplement: Co Q-10 100 mg 45 Softgels.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Nature''s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-02.png', '/assets/products/Nature''s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-03.png', '/assets/products/Nature''s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-04.png', '/assets/products/Nature''s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-05.png', '/assets/products/Nature''s Bounty/Specialty Supplements/Nature-s-Bounty-Co-Q-10-100-mg-45-Softgels.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=3 AND p.name='Co Q-10 100 mg 45 Softgels');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 5, 'Vitamin D3 Vegan Spray', CONCAT('Garden of Life dietary supplement: Vitamin D3 Vegan Spray.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Garden of Life/Vitamins/Gallery-Vitamin-D3-Vegan-Spray-02.png', '/assets/products/Garden of Life/Vitamins/Gallery-Vitamin-D3-Vegan-Spray-03.png', '/assets/products/Garden of Life/Vitamins/Gallery-Vitamins-765a5822852e4216b5ff0b74f0b3289d.png', '/assets/products/Garden of Life/Vitamins/Gallery-Vitamins-d82f649c3281443d9e88899fb28cc3be.png', '/assets/products/Garden of Life/Vitamins/Garden-of-Life-Vitamin-D3-Vegan-Spray.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=5 AND p.name='Vitamin D3 Vegan Spray');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 5, 'Vitamin Code Raw Calcium 120 Capsules', CONCAT('Garden of Life dietary supplement: Vitamin Code Raw Calcium 120 Capsules.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-02.png', '/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-03.png', '/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-04.png', '/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-05.png', '/assets/products/Garden of Life/Minerals/Garden-of-Life-Vitamin-Code-Raw-Calcium-120-Capsules.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=5 AND p.name='Vitamin Code Raw Calcium 120 Capsules');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 5, 'Vitamin Code RAW ONE Women', CONCAT('Garden of Life dietary supplement: Vitamin Code RAW ONE Women.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-02.png', '/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-03.png', '/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-04.png', '/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-05.png', '/assets/products/Garden of Life/Multivitamins/Garden-of-Life-Vitamin-Code-RAW-ONE-Women.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=5 AND p.name='Vitamin Code RAW ONE Women');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 5, 'Oceans 3 Beyond Omega-3', CONCAT('Garden of Life dietary supplement: Oceans 3 Beyond Omega-3.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Garden of Life/Omega and Fish Oil/Gallery-Oceans-3-Beyond-Omega-3-02.png', '/assets/products/Garden of Life/Omega and Fish Oil/Gallery-Oceans-3-Beyond-Omega-3-03.png', '/assets/products/Garden of Life/Omega and Fish Oil/Gallery-Oceans-3-Beyond-Omega-3-04.png', '/assets/products/Garden of Life/Omega and Fish Oil/Gallery-Oceans-3-Beyond-Omega-3-05.png', '/assets/products/Garden of Life/Omega and Fish Oil/Garden-of-Life-Oceans-3-Beyond-Omega-3.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=5 AND p.name='Oceans 3 Beyond Omega-3');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 5, 'RAW Probiotics Women 85 Billion', CONCAT('Garden of Life dietary supplement: RAW Probiotics Women 85 Billion.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Garden of Life/Probiotics/Gallery-Probiotics-05e1bb0a1ef9413bb8de531aa59a0740.png', '/assets/products/Garden of Life/Probiotics/Gallery-RAW-Probiotics-Women-85-Billion-02.png', '/assets/products/Garden of Life/Probiotics/Gallery-RAW-Probiotics-Women-85-Billion-03.png', '/assets/products/Garden of Life/Probiotics/Gallery-RAW-Probiotics-Women-85-Billion-04.png', '/assets/products/Garden of Life/Probiotics/Garden-of-Life-RAW-Probiotics-Women-85-Billion.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=5 AND p.name='RAW Probiotics Women 85 Billion');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 5, 'Organics Extra Strength Turmeric', CONCAT('Garden of Life dietary supplement: Organics Extra Strength Turmeric.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-02.png', '/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-03.png', '/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-04.png', '/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-05.png', '/assets/products/Garden of Life/Specialty Supplements/Garden-of-Life-Organics-Extra-Strength-Turmeric.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=5 AND p.name='Organics Extra Strength Turmeric');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 1, 9, 'Vitamin D3 1000 IU', CONCAT('NOW Foods dietary supplement: Vitamin D3 1000 IU.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-02.png', '/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-03.png', '/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-04.png', '/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-05.png', '/assets/products/NOW Foods/Vitamins/NOW-Foods-Vitamin-D3-1000-IU.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=1 AND p.brand_id=9 AND p.name='Vitamin D3 1000 IU');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 2, 9, 'Magnesium Citrate 200 mg', CONCAT('NOW Foods dietary supplement: Magnesium Citrate 200 mg.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-02.png', '/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-03.png', '/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-04.png', '/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-05.png', '/assets/products/NOW Foods/Minerals/NOW-Foods-Magnesium-Citrate-200-mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=2 AND p.brand_id=9 AND p.name='Magnesium Citrate 200 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 3, 9, 'ADAM Superior Mens Multi 120 Tablets', CONCAT('NOW Foods dietary supplement: ADAM Superior Mens Multi 120 Tablets.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-02.png', '/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-03.png', '/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-04.png', '/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-05.png', '/assets/products/NOW Foods/Multivitamins/NOW-Foods-ADAM-Superior-Mens-Multi-120-Tablets.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=3 AND p.brand_id=9 AND p.name='ADAM Superior Mens Multi 120 Tablets');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 4, 9, 'Omega-3 Fish Oil 1000 mg', CONCAT('NOW Foods dietary supplement: Omega-3 Fish Oil 1000 mg.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/NOW Foods/Omega and Fish Oil/Gallery-Omega-3-Fish-Oil-1000-mg-02.png', '/assets/products/NOW Foods/Omega and Fish Oil/Gallery-Omega-3-Fish-Oil-1000-mg-03.png', '/assets/products/NOW Foods/Omega and Fish Oil/Gallery-Omega-3-Fish-Oil-1000-mg-04.png', '/assets/products/NOW Foods/Omega and Fish Oil/Gallery-Omega-3-Fish-Oil-1000-mg-05.png', '/assets/products/NOW Foods/Omega and Fish Oil/NOW-Foods-Omega-3-Fish-Oil-1000-mg.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=4 AND p.brand_id=9 AND p.name='Omega-3 Fish Oil 1000 mg');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 5, 9, 'Probiotic-10 25 Billion 50 Capsules', CONCAT('NOW Foods dietary supplement: Probiotic-10 25 Billion 50 Capsules.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-02.png', '/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-03.png', '/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-04.png', '/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-05.png', '/assets/products/NOW Foods/Probiotics/NOW-Foods-Probiotic-10-25-Billion-50-Capsules.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=5 AND p.brand_id=9 AND p.name='Probiotic-10 25 Billion 50 Capsules');

INSERT INTO products (category_id, brand_id, name, description, ingredients, warnings, price, discount_percent, stock, sizes, weight, image, is_active, created_by_admin_id, updated_by_admin_id)
SELECT 6, 9, 'CoQ10 100 mg 50 Softgels', CONCAT('NOW Foods dietary supplement: CoQ10 100 mg 50 Softgels.', CHAR(10), 'See the product label for serving size and full product details.'), NULL, CONCAT('Allergy warning: Review the complete ingredient label for allergens before use.', CHAR(10), 'Consult a healthcare professional before use if you have allergies, a medical condition, or take medication.'), 0.00, 0.00, 0, NULL, NULL, JSON_ARRAY('/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-02.png', '/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-03.png', '/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-04.png', '/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-05.png', '/assets/products/NOW Foods/Specialty Supplements/NOW-Foods-CoQ10-100-mg-50-Softgels.png'), 0, NULL, NULL
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM products p WHERE p.category_id=6 AND p.brand_id=9 AND p.name='CoQ10 100 mg 50 Softgels');

-- Review row counts and set a real price/stock before setting is_active=1.
SELECT brand_id, category_id, COUNT(*) AS product_count FROM products GROUP BY brand_id, category_id ORDER BY brand_id, category_id;
