-- Active: 1793543511624@@127.0.0.1@3306@vitemines
CREATE DATABASE IF NOT EXISTS vitemines
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE vitemines;


SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `vitemines`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_assistant_requests`
--

CREATE TABLE `ai_assistant_requests` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `handled_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_migrations`
--

CREATE TABLE `app_migrations` (
  `migration_key` varchar(190) NOT NULL,
  `applied_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_migrations`
--

INSERT INTO `app_migrations` (`migration_key`, `applied_at`) VALUES
('2026-11-01_jod_catalog_to_usd', '2026-11-01 12:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `created_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `name`, `logo`, `discount_percent`, `created_by_admin_id`, `created_at`) VALUES
(1, 'Nature Made', NULL, 0.00, NULL, '2026-10-31 19:54:52'),
(3, 'Nature\'s Bounty', NULL, 0.00, NULL, '2026-10-31 19:54:52'),
(4, 'Solgar', NULL, 0.00, NULL, '2026-10-31 19:54:52'),
(5, 'Garden of Life', NULL, 0.00, NULL, '2026-10-31 19:54:52'),
(9, 'NOW Foods', NULL, 0.00, NULL, '2026-10-31 19:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `image`, `is_active`, `created_by_admin_id`, `created_at`) VALUES
(1, 'Vitamins', 'Individual vitamin supplements.', NULL, 1, NULL, '2026-10-31 19:54:52'),
(2, 'Minerals', 'Mineral supplements.', NULL, 1, NULL, '2026-10-31 19:54:52'),
(3, 'Multivitamins', 'Multivitamin supplements.', NULL, 1, NULL, '2026-10-31 19:54:52'),
(4, 'Omega and Fish Oil', 'Omega and fish oil supplements.', NULL, 1, NULL, '2026-10-31 19:54:52'),
(5, 'Probiotics', 'Probiotic supplements.', NULL, 1, NULL, '2026-10-31 19:54:52'),
(6, 'Specialty Supplements', 'Other specialty supplements.', NULL, 1, NULL, '2026-10-31 19:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','paid','processing','shipped','delivered','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `total` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'usd',
  `discount_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `ship_name` varchar(100) NOT NULL,
  `ship_phone` varchar(30) NOT NULL,
  `ship_address` varchar(255) NOT NULL,
  `ship_city` varchar(80) NOT NULL,
  `shipping_time` datetime DEFAULT NULL,
  `processed_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `status`, `total`, `currency`, `discount_total`, `shipping_total`, `ship_name`, `ship_phone`, `ship_address`, `ship_city`, `shipping_time`, `processed_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 2, 'cancelled', 12.00, 'jod', 0.00, 2.00, 'Ahmad Batta', '0787010617', 'shafabadran', 'amman', NULL, NULL, '2026-11-01 12:11:10', '2026-11-01 12:16:33'),
(2, 2, 'cancelled', 12.69, 'usd', 0.00, 2.82, 'Ahmad Batta', '0787010617', 'shafabadran', 'amman', NULL, NULL, '2026-11-01 12:18:58', '2026-11-01 12:19:51'),
(3, 2, 'paid', 12.69, 'usd', 0.00, 2.82, 'Ahmad Batta', '0787010617', 'shafabadran', 'amman', NULL, NULL, '2026-11-01 12:21:40', '2026-11-01 12:27:03'),
(4, 2, 'paid', 14.10, 'usd', 0.00, 2.82, 'Ahmad Batta', '0787010617', 'shafabadran', 'amman', NULL, NULL, '2026-11-01 12:36:06', '2026-11-01 12:36:32');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED DEFAULT NULL,
  `product_name` varchar(150) NOT NULL,
  `size` varchar(20) DEFAULT NULL,
  `quantity` int(10) UNSIGNED NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `delivery_price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `size`, `quantity`, `order_total`, `delivery_price`) VALUES
(1, 1, 155, 'Vitamin D3 2000 IU', NULL, 1, 10.00, 0.00),
(2, 2, 156, 'Vitamin E 400 IU', NULL, 1, 9.87, 0.00),
(3, 3, 156, 'Vitamin E 400 IU', NULL, 1, 9.87, 0.00),
(4, 4, 153, 'Vitamin B1 100 mg', NULL, 1, 11.28, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `method` enum('card','paypal','cash_on_delivery') NOT NULL,
  `provider_transaction_id` varchar(120) DEFAULT NULL,
  `gateway_metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_metadata`)),
  `amount` decimal(10,2) NOT NULL,
  `currency` char(3) NOT NULL DEFAULT 'usd',
  `card_last4` char(4) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','succeeded','failed','refunded') NOT NULL DEFAULT 'pending',
  `reviewed_by_admin_id` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `method`, `provider_transaction_id`, `gateway_metadata`, `amount`, `currency`, `card_last4`, `paid_at`, `created_at`, `status`, `reviewed_by_admin_id`) VALUES
(1, 3, 'card', 'pi_3UWuxOENFUK6ZVzc0bOBcnZI', NULL, 12.69, 'usd', NULL, NULL, '2026-11-01 12:21:41', 'pending', NULL),
(2, 3, 'card', 'pi_3UWv1fENFUK6ZVzc078O25Eo', NULL, 12.69, 'usd', NULL, NULL, '2026-11-01 12:26:06', 'pending', NULL),
(3, 3, 'card', 'pi_3UWv1gENFUK6ZVzc0uh20G40', '{\"id\":\"pi_3UWv1gENFUK6ZVzc0uh20G40\",\"object\":\"payment_intent\",\"allowed_payment_method_types\":null,\"amount\":1269,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":1269,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3UWv1gENFUK6ZVzc0uh20G40_secret_OGzzVaTJWlek1qzKQiUUxqxl8\",\"confirmation_method\":\"automatic\",\"created\":1793525168,\"currency\":\"usd\",\"customer\":null,\"customer_account\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":\"ch_3UWv1gENFUK6ZVzc08ACexq5\",\"livemode\":false,\"managed_payments\":{\"enabled\":false},\"metadata\":{\"order_id\":\"3\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":\"pm_1UWv2ZENFUK6ZVzcd6cZfYJa\",\"payment_method_configuration_details\":{\"id\":\"pmc_1UWSNJENFUK6ZVzcxPhhxpdS\",\"parent\":null},\"payment_method_options\":{\"amazon_pay\":{\"express_checkout_element_session_id\":null},\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"cashapp\":[],\"klarna\":{\"preferred_locale\":null},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"klarna\",\"link\",\"cashapp\",\"amazon_pay\"],\"payment_record\":null,\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shared_payment_granted_token\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"succeeded\",\"transfer_data\":null,\"transfer_group\":null}', 12.69, 'usd', NULL, '2026-11-01 12:27:03', '2026-11-01 12:26:07', 'succeeded', NULL),
(4, 4, 'card', 'pi_3UWvBMENFUK6ZVzc1FGzDNIg', '{\"id\":\"pi_3UWvBMENFUK6ZVzc1FGzDNIg\",\"object\":\"payment_intent\",\"allowed_payment_method_types\":null,\"amount\":1410,\"amount_capturable\":0,\"amount_details\":{\"tip\":[]},\"amount_received\":1410,\"application\":null,\"application_fee_amount\":null,\"automatic_payment_methods\":{\"allow_redirects\":\"always\",\"enabled\":true},\"canceled_at\":null,\"cancellation_reason\":null,\"capture_method\":\"automatic_async\",\"client_secret\":\"pi_3UWvBMENFUK6ZVzc1FGzDNIg_secret_9LYxFQ9WIfeGssPMRlxXNtGFX\",\"confirmation_method\":\"automatic\",\"created\":1793525768,\"currency\":\"usd\",\"customer\":null,\"customer_account\":null,\"description\":null,\"excluded_payment_method_types\":null,\"last_payment_error\":null,\"latest_charge\":\"ch_3UWvBMENFUK6ZVzc1vdYOGY2\",\"livemode\":false,\"managed_payments\":{\"enabled\":false},\"metadata\":{\"order_id\":\"4\"},\"next_action\":null,\"on_behalf_of\":null,\"payment_method\":\"pm_1UWvBkENFUK6ZVzccLDDraMA\",\"payment_method_configuration_details\":{\"id\":\"pmc_1UWSNJENFUK6ZVzcxPhhxpdS\",\"parent\":null},\"payment_method_options\":{\"amazon_pay\":{\"express_checkout_element_session_id\":null},\"card\":{\"installments\":null,\"mandate_options\":null,\"network\":null,\"request_three_d_secure\":\"automatic\"},\"cashapp\":[],\"klarna\":{\"preferred_locale\":null},\"link\":{\"persistent_token\":null}},\"payment_method_types\":[\"card\",\"klarna\",\"link\",\"cashapp\",\"amazon_pay\"],\"payment_record\":null,\"processing\":null,\"receipt_email\":null,\"review\":null,\"setup_future_usage\":null,\"shared_payment_granted_token\":null,\"shipping\":null,\"source\":null,\"statement_descriptor\":null,\"statement_descriptor_suffix\":null,\"status\":\"succeeded\",\"transfer_data\":null,\"transfer_group\":null}', 14.10, 'usd', NULL, '2026-11-01 12:36:32', '2026-11-01 12:36:07', 'succeeded', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `brand_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text DEFAULT NULL,
  `warnings` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `stock` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `sizes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`sizes`)),
  `weight` decimal(8,3) DEFAULT NULL,
  `image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`image`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `updated_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `description`, `ingredients`, `warnings`, `price`, `discount_percent`, `stock`, `sizes`, `weight`, `image`, `is_active`, `created_by_admin_id`, `updated_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 'Vitamin A 5000 IU', 'Vitamin A 5000 IU by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'High-dose preformed vitamin A can be harmful in pregnancy; do not combine with other high-dose vitamin A products without professional advice. Follow the label.', 16.57, 0.00, 40, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-A-5000IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(2, 1, 4, 'Vitamin B-Complex 50', 'Vitamin B-Complex 50 by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 13.75, 0.00, 30, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-B-Complex-50-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(3, 1, 4, 'Vitamin B12 1000 mcg', 'Vitamin B12 1000 mcg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.10, 0.00, 37, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-B12-1000mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(4, 1, 4, 'Vitamin C 1000 mg', 'Vitamin C 1000 mg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 13.40, 0.00, 44, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-C-1000mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(5, 1, 4, 'Vitamin D3 5000 IU', 'Vitamin D3 5000 IU by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.81, 0.00, 34, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-D3-5000IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(6, 1, 4, 'Vitamin D3', 'Vitamin D3 by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.51, 0.00, 41, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-D3-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(7, 1, 4, 'Vitamin E 400 IU', 'Vitamin E 400 IU by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 12.69, 0.00, 31, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-E-400IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(8, 1, 4, 'Vitamin K2 100 mcg', 'Vitamin K2 100 mcg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Vitamin K may interact with warfarin and other anticoagulant treatment. Ask your clinician before use and keep intake consistent.', 16.22, 0.00, 38, NULL, NULL, '[\"/assets/products/Solgar/Vitamins/Solgar-Vitamin-K2-100mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(9, 2, 4, 'Calcium Citrate with Vitamin D3', 'Calcium Citrate with Vitamin D3 by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 14.46, 0.00, 39, NULL, NULL, '[\"/assets/products/Solgar/Minerals/Solgar-Calcium-Citrate-Vitamin-D3-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(10, 2, 4, 'Calcium Magnesium Plus Zinc', 'Calcium Magnesium Plus Zinc by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 18.34, 0.00, 29, NULL, NULL, '[\"/assets/products/Solgar/Minerals/Solgar-Calcium-Magnesium-Zinc-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(11, 2, 4, 'Chromium Picolinate 200 mcg', 'Chromium Picolinate 200 mcg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 13.75, 0.00, 36, NULL, NULL, '[\"/assets/products/Solgar/Minerals/Solgar-Chromium-Picolinate-200mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(12, 2, 4, 'Magnesium Citrate', 'Magnesium Citrate by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 15.16, 0.00, 26, NULL, NULL, '[\"/assets/products/Solgar/Minerals/Solgar-Magnesium-Citrate-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(13, 2, 4, 'Selenium 200 mcg', 'Selenium 200 mcg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 15.16, 0.00, 33, NULL, NULL, '[\"/assets/products/Solgar/Minerals/Solgar-Selenium-200mcg-Official-cutout.png\", \"/assets/products/Solgar/Minerals/Solgar-Selenium-200mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(14, 2, 4, 'Zinc Picolinate', 'Zinc Picolinate by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 13.40, 0.00, 40, NULL, NULL, '[\"/assets/products/Solgar/Minerals/Solgar-Zinc-Picolinate-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(15, 3, 4, 'Female Multiple', 'Female Multiple by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 16.57, 0.00, 31, NULL, NULL, '[\"/assets/products/Solgar/Multivitamins/Solgar-Female-Multiple-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(16, 3, 4, 'Male Multiple', 'Male Multiple by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.63, 0.00, 38, NULL, NULL, '[\"/assets/products/Solgar/Multivitamins/Solgar-Male-Multiple-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(17, 3, 4, 'Omnium Phytonutrient Complex', 'Omnium Phytonutrient Complex by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.63, 0.00, 28, NULL, NULL, '[\"/assets/products/Solgar/Multivitamins/Solgar-Omnium-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(18, 3, 4, 'Formula VM-2000', 'Formula VM-2000 by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 19.75, 0.00, 35, NULL, NULL, '[\"/assets/products/Solgar/Multivitamins/Solgar-VM-2000-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(19, 4, 4, 'Flaxseed Oil 1250 mg', 'Flaxseed Oil 1250 mg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 23.27, 0.00, 18, NULL, NULL, '[\"/assets/products/Solgar/Omega and Fish Oil/Solgar-Flaxseed-Oil-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(20, 4, 4, 'Omega 3-6-9 Fish, Flax and Borage', 'Omega 3-6-9 Fish, Flax and Borage by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 24.68, 0.00, 25, NULL, NULL, '[\"/assets/products/Solgar/Omega and Fish Oil/Solgar-Omega-3-6-9-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(21, 4, 4, 'Omega-3 Fish Oil', 'Omega-3 Fish Oil by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 24.33, 0.00, 32, NULL, NULL, '[\"/assets/products/Solgar/Omega and Fish Oil/Solgar-Omega-3-Fish-Oil-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(22, 4, 4, 'Triple Strength Omega-3 950 mg', 'Triple Strength Omega-3 950 mg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 31.73, 0.00, 22, NULL, NULL, '[\"/assets/products/Solgar/Omega and Fish Oil/Solgar-Triple-Strength-Omega-3-950mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(23, 5, 4, 'Acidophilus 50 Vegetable Capsules', 'Acidophilus 50 Vegetable Capsules by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 25.04, 0.00, 27, NULL, NULL, '[\"/assets/products/Solgar/Probiotics/Solgar-Acidophilus-50-Capsules-RedLabel-cutout.png\", \"/assets/products/Solgar/Probiotics/Solgar-Acidophilus-50-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(24, 5, 4, 'Advanced 40+ Acidophilus', 'Advanced 40+ Acidophilus by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 23.98, 0.00, 17, NULL, NULL, '[\"/assets/products/Solgar/Probiotics/Solgar-Advanced-40-Acidophilus-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(25, 5, 4, 'Advanced Acidophilus 100', 'Advanced Acidophilus 100 by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 25.39, 0.00, 24, NULL, NULL, '[\"/assets/products/Solgar/Probiotics/Solgar-Advanced-Acidophilus-100-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(26, 5, 4, 'Advanced Acidophilus Plus', 'Advanced Acidophilus Plus by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 23.62, 0.00, 31, NULL, NULL, '[\"/assets/products/Solgar/Probiotics/Solgar-Advanced-Acidophilus-Plus-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(27, 5, 4, 'Advanced Multi-Billion Dophilus', 'Advanced Multi-Billion Dophilus by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 22.21, 0.00, 21, NULL, NULL, '[\"/assets/products/Solgar/Probiotics/Solgar-Advanced-Multi-Billion-Dophilus-Alt-cutout.png\", \"/assets/products/Solgar/Probiotics/Solgar-Advanced-Multi-Billion-Dophilus-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(28, 6, 4, 'CoQ10 100 mg', 'CoQ10 100 mg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'CoQ10 may interact with warfarin and some blood pressure or glucose medicines. Ask a healthcare professional before use.', 17.98, 0.00, 24, NULL, NULL, '[\"/assets/products/Solgar/Specialty Supplements/Solgar-CoQ10-100mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(29, 6, 4, 'Digestive Enzymes', 'Digestive Enzymes by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 20.10, 0.00, 14, NULL, NULL, '[\"/assets/products/Solgar/Specialty Supplements/Solgar-Digestive-Enzymes-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(30, 6, 4, 'Full Spectrum Curcumin', 'Full Spectrum Curcumin by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 18.34, 0.00, 21, NULL, NULL, '[\"/assets/products/Solgar/Specialty Supplements/Solgar-Full-Spectrum-Curcumin-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(31, 6, 4, 'Grape Seed Extract 100 mg', 'Grape Seed Extract 100 mg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 23.27, 0.00, 28, NULL, NULL, '[\"/assets/products/Solgar/Specialty Supplements/Solgar-Grape-Seed-Extract-100mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(32, 6, 4, 'Lutein 20 mg', 'Lutein 20 mg by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 20.45, 0.00, 18, NULL, NULL, '[\"/assets/products/Solgar/Specialty Supplements/Solgar-Lutein-20mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(33, 6, 4, 'Milk Thistle', 'Milk Thistle by Solgar. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 22.57, 0.00, 25, NULL, NULL, '[\"/assets/products/Solgar/Specialty Supplements/Solgar-Milk-Thistle-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(34, 1, 1, 'Vitamin D3 1000 IU', 'Nature Made dietary supplement: Vitamin D3 1000 IU.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 33, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Gallery-Vitamin-D3-1000-IU-02.png\", \"/assets/products/Nature Made/Vitamins/Gallery-Vitamin-D3-1000-IU-03.png\", \"/assets/products/Nature Made/Vitamins/Gallery-Vitamin-D3-1000-IU-04.png\", \"/assets/products/Nature Made/Vitamins/Gallery-Vitamins-f11a9e862a1c4996bd21a773f0740216.png\", \"/assets/products/Nature Made/Vitamins/Nature-Made-Vitamin-D3-1000-IU.png\"]', 0, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(35, 2, 1, 'Magnesium 250 mg 200 Tablets', 'Nature Made dietary supplement: Magnesium 250 mg 200 Tablets.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 34, NULL, NULL, '[\"/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-02.png\", \"/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-03.png\", \"/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-04.png\", \"/assets/products/Nature Made/Minerals/Gallery-Magnesium-250-mg-200-Tablets-05.png\", \"/assets/products/Nature Made/Minerals/Nature-Made-Magnesium-250-mg-200-Tablets.png\"]', 0, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(36, 3, 1, 'Multi For Her 90 Tablets', 'Multi For Her 90 Tablets by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.81, 0.00, 25, NULL, NULL, '[\"/assets/products/Nature Made/Multivitamins/Multivitamin for Her-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(37, 4, 1, 'Fish Oil 1200 mg', 'Fish Oil 1200 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 37.73, 0.00, 25, NULL, NULL, '[\"/assets/products/Nature Made/Omega and Fish Oil/Fish Oil 1200 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:52', '2026-11-01 12:16:33'),
(39, 6, 1, 'CoQ10 200 mg 40 Softgels', 'Nature Made dietary supplement: CoQ10 200 mg 40 Softgels.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 16, NULL, NULL, '[\"/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-02.png\", \"/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-03.png\", \"/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-04.png\", \"/assets/products/Nature Made/Specialty Supplements/Gallery-CoQ10-200-mg-40-Softgels-05.png\", \"/assets/products/Nature Made/Specialty Supplements/Nature-Made-CoQ10-200-mg-40-Softgels.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(40, 1, 3, 'Vitamin D3 1000 IU', 'Nature\'s Bounty dietary supplement: Vitamin D3 1000 IU.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 41, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-02.png\", \"/assets/products/Nature\'s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-03.png\", \"/assets/products/Nature\'s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-04.png\", \"/assets/products/Nature\'s Bounty/Vitamins/Gallery-Vitamin-D3-1000-IU-05.png\", \"/assets/products/Nature\'s Bounty/Vitamins/Nature-s-Bounty-Vitamin-D3-1000-IU.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(41, 2, 3, 'Magnesium 500 mg', 'Magnesium 500 mg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 15.51, 0.00, 25, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Minerals/Magnesium 500 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(42, 3, 3, 'Daily Multi 100 Caplets', 'Nature\'s Bounty dietary supplement: Daily Multi 100 Caplets.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 33, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-02.png\", \"/assets/products/Nature\'s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-03.png\", \"/assets/products/Nature\'s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-04.png\", \"/assets/products/Nature\'s Bounty/Multivitamins/Gallery-Daily-Multi-100-Caplets-05.png\", \"/assets/products/Nature\'s Bounty/Multivitamins/Nature-s-Bounty-Daily-Multi-100-Caplets.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(43, 4, 3, 'Fish Oil 1200 mg', 'Fish Oil 1200 mg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 20.10, 0.00, 33, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Omega and Fish Oil/Fish Oil 1200 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(44, 5, 3, 'Acidophilus 100 Million 120 Tablets', 'Acidophilus 100 Million 120 Tablets by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 20.80, 0.00, 21, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Probiotics/acidophilus-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(45, 6, 3, 'Co Q-10 100 mg 45 Softgels', 'Nature\'s Bounty dietary supplement: Co Q-10 100 mg 45 Softgels.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 24, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-02.png\", \"/assets/products/Nature\'s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-03.png\", \"/assets/products/Nature\'s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-04.png\", \"/assets/products/Nature\'s Bounty/Specialty Supplements/Gallery-Co-Q-10-100-mg-45-Softgels-05.png\", \"/assets/products/Nature\'s Bounty/Specialty Supplements/Nature-s-Bounty-Co-Q-10-100-mg-45-Softgels.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(46, 1, 5, 'Vitamin D3 Vegan Spray', 'Garden of Life dietary supplement: Vitamin D3 Vegan Spray.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 32, NULL, NULL, '[\"/assets/products/Garden of Life/Vitamins/Gallery-Vitamin-D3-Vegan-Spray-02.png\", \"/assets/products/Garden of Life/Vitamins/Gallery-Vitamin-D3-Vegan-Spray-03.png\", \"/assets/products/Garden of Life/Vitamins/Gallery-Vitamins-765a5822852e4216b5ff0b74f0b3289d.png\", \"/assets/products/Garden of Life/Vitamins/Gallery-Vitamins-d82f649c3281443d9e88899fb28cc3be.png\", \"/assets/products/Garden of Life/Vitamins/Garden-of-Life-Vitamin-D3-Vegan-Spray.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(47, 2, 5, 'Vitamin Code Raw Calcium 120 Capsules', 'Garden of Life dietary supplement: Vitamin Code Raw Calcium 120 Capsules.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 33, NULL, NULL, '[\"/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-02.png\", \"/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-03.png\", \"/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-04.png\", \"/assets/products/Garden of Life/Minerals/Gallery-Vitamin-Code-Raw-Calcium-120-Capsules-05.png\", \"/assets/products/Garden of Life/Minerals/Garden-of-Life-Vitamin-Code-Raw-Calcium-120-Capsules.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(48, 3, 5, 'Vitamin Code RAW ONE Women', 'Garden of Life dietary supplement: Vitamin Code RAW ONE Women.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 41, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-02.png\", \"/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-03.png\", \"/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-04.png\", \"/assets/products/Garden of Life/Multivitamins/Gallery-Vitamin-Code-RAW-ONE-Women-05.png\", \"/assets/products/Garden of Life/Multivitamins/Garden-of-Life-Vitamin-Code-RAW-ONE-Women.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(49, 4, 5, 'Oceans 3 Beyond Omega-3', 'Oceans 3 Beyond Omega-3 by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 22.92, 0.00, 24, NULL, NULL, '[\"/assets/products/Garden of Life/Omega and Fish Oil/Oceans-3-Beyond-Omega-3-60-Softgels-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(50, 5, 5, 'RAW Probiotics Women 85 Billion', 'RAW Probiotics Women 85 Billion by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 55.36, 0.00, 29, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/RAW-Probiotics-Women-85-Billion-90-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(51, 6, 5, 'Organics Extra Strength Turmeric', 'Garden of Life dietary supplement: Organics Extra Strength Turmeric.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 15, NULL, NULL, '[\"/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-02.png\", \"/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-03.png\", \"/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-04.png\", \"/assets/products/Garden of Life/Specialty Supplements/Gallery-Organics-Extra-Strength-Turmeric-05.png\", \"/assets/products/Garden of Life/Specialty Supplements/Garden-of-Life-Organics-Extra-Strength-Turmeric.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(52, 1, 9, 'Vitamin D3 1000 IU', 'NOW Foods dietary supplement: Vitamin D3 1000 IU.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 40, NULL, NULL, '[\"/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-02.png\", \"/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-03.png\", \"/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-04.png\", \"/assets/products/NOW Foods/Vitamins/Gallery-Vitamin-D3-1000-IU-05.png\", \"/assets/products/NOW Foods/Vitamins/NOW-Foods-Vitamin-D3-1000-IU.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(53, 2, 9, 'Magnesium Citrate 200 mg', 'NOW Foods dietary supplement: Magnesium Citrate 200 mg.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 24, NULL, NULL, '[\"/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-02.png\", \"/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-03.png\", \"/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-04.png\", \"/assets/products/NOW Foods/Minerals/Gallery-Magnesium-Citrate-200-mg-05.png\", \"/assets/products/NOW Foods/Minerals/NOW-Foods-Magnesium-Citrate-200-mg.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(54, 3, 9, 'ADAM Superior Mens Multi 120 Tablets', 'NOW Foods dietary supplement: ADAM Superior Mens Multi 120 Tablets.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 32, NULL, NULL, '[\"/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-02.png\", \"/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-03.png\", \"/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-04.png\", \"/assets/products/NOW Foods/Multivitamins/Gallery-ADAM-Superior-Mens-Multi-120-Tablets-05.png\", \"/assets/products/NOW Foods/Multivitamins/NOW-Foods-ADAM-Superior-Mens-Multi-120-Tablets.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(55, 4, 9, 'Omega-3 Fish Oil 1000 mg', 'Omega-3 Fish Oil 1000 mg by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 14.10, 0.00, 32, NULL, NULL, '[\"/assets/products/NOW Foods/Omega and Fish Oil/1656_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(56, 5, 9, 'Probiotic-10 25 Billion 50 Capsules', 'NOW Foods dietary supplement: Probiotic-10 25 Billion 50 Capsules.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 20, NULL, NULL, '[\"/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-02.png\", \"/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-03.png\", \"/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-04.png\", \"/assets/products/NOW Foods/Probiotics/Gallery-Probiotic-10-25-Billion-50-Capsules-05.png\", \"/assets/products/NOW Foods/Probiotics/NOW-Foods-Probiotic-10-25-Billion-50-Capsules.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(57, 6, 9, 'CoQ10 100 mg 50 Softgels', 'NOW Foods dietary supplement: CoQ10 100 mg 50 Softgels.\nSee the product label for serving size and full product details.', NULL, 'Allergy warning: Review the complete ingredient label for allergens before use.\nConsult a healthcare professional before use if you have allergies, a medical condition, or take medication.', 7.76, 0.00, 23, NULL, NULL, '[\"/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-02.png\", \"/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-03.png\", \"/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-04.png\", \"/assets/products/NOW Foods/Specialty Supplements/Gallery-CoQ10-100-mg-50-Softgels-05.png\", \"/assets/products/NOW Foods/Specialty Supplements/NOW-Foods-CoQ10-100-mg-50-Softgels.png\"]', 0, NULL, NULL, '2026-10-31 19:54:53', '2026-11-01 12:16:33'),
(58, 2, 3, 'Calcium Magnesium Zinc', 'Calcium Magnesium Zinc by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 13.05, 0.00, 25, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Minerals/Calcium Magnesium Zinc-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(59, 2, 3, 'Calcium Plus Vitamin D3', 'Calcium Plus Vitamin D3 by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 11.28, 0.00, 32, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Minerals/Calcium Plus Vitamin D3-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(60, 2, 3, 'Gentle Iron 28 mg', 'Gentle Iron 28 mg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 13.75, 0.00, 39, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Minerals/Gentle Iron 28 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(61, 2, 3, 'High Absorption magnesium glycinate', 'High Absorption magnesium glycinate by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 15.51, 0.00, 29, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Minerals/high absorption magnesium glycinate-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(62, 3, 3, 'Adult Multivitamin Gummies', 'Adult Multivitamin Gummies by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.46, 0.00, 37, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Multivitamins/Adult Multivitamin Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(63, 3, 3, 'Essential Prenatal Gummies', 'Essential Prenatal Gummies by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 22.57, 0.00, 27, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Multivitamins/Essential Prenatal Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(64, 3, 3, 'Women\'s Multivitamin Gummies', 'Women\'s Multivitamin Gummies by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 16.22, 0.00, 34, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Multivitamins/Women\'s Multivitamin Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(65, 4, 3, 'Fish Oil 1000 mg', 'Fish Oil 1000 mg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 16.93, 0.00, 34, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Omega and Fish Oil/Fish Oil 1000 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(66, 4, 3, 'Fish Oil 2400 mg per serving', 'Fish Oil 2400 mg per serving by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 27.86, 0.00, 24, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Omega and Fish Oil/Fish Oil 2400 mg per serving-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(67, 4, 3, 'Flaxseed Oil 1200 mg', 'Flaxseed Oil 1200 mg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 18.69, 0.00, 31, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Omega and Fish Oil/Flaxseed Oil 1200 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(68, 4, 3, 'Mini Fish Oil', 'Mini Fish Oil by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 19.75, 0.00, 21, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Omega and Fish Oil/Mini Fish Oil-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(69, 5, 3, 'Probiotic 10', 'Probiotic 10 by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 25.39, 0.00, 26, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Probiotics/probiotic-10-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(70, 5, 3, 'Probiotic Gummies', 'Probiotic Gummies by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 23.62, 0.00, 16, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Probiotics/probiotic-Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(71, 6, 3, 'Active Mind Cognizin', 'Active Mind Cognizin by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.98, 0.00, 19, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Specialty Supplements/Active Mind Cognizin-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(72, 6, 3, 'Apple Cider Vinegar Gummies', 'Apple Cider Vinegar Gummies by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.63, 0.00, 26, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Specialty Supplements/Apple Cider Vinegar Gummiies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(73, 6, 3, 'Correct Collagen Booster', 'Correct Collagen Booster by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.16, 0.00, 16, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Specialty Supplements/CorrectCollagenBooster-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(74, 6, 3, 'Cranberry Softgels', 'Cranberry Softgels by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 20.10, 0.00, 23, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Specialty Supplements/Cranberry Softgels-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(75, 6, 3, 'Hair', 'Hair by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.16, 0.00, 13, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Specialty Supplements/Hair-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(76, 1, 3, 'Vitamin A 10000 IU', 'Vitamin A 10000 IU by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'High-dose preformed vitamin A can be harmful in pregnancy; do not combine with other high-dose vitamin A products without professional advice. Follow the label.', 14.81, 0.00, 38, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Vitamin A 10000 IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(77, 1, 3, 'Vitamin B12 1000 mcg', 'Vitamin B12 1000 mcg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 11.99, 0.00, 45, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Vitamin B12 1000 mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(78, 1, 3, 'Vitamin C 500 mg', 'Vitamin C 500 mg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 13.05, 0.00, 35, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Vitamin C 500 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(79, 1, 3, 'Vitamin D3 2000 IU', 'Vitamin D3 2000 IU by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.81, 0.00, 42, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Vitamin D3 2000 IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33');
INSERT INTO `products` (`id`, `category_id`, `brand_id`, `name`, `description`, `ingredients`, `warnings`, `price`, `discount_percent`, `stock`, `sizes`, `weight`, `image`, `is_active`, `created_by_admin_id`, `updated_by_admin_id`, `created_at`, `updated_at`) VALUES
(80, 1, 3, 'Vitamin E 400 IU', 'Vitamin E 400 IU by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 10.58, 0.00, 32, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Vitamin E 400 IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(81, 1, 3, 'Vitamin K2 100 mcg', 'Vitamin K2 100 mcg by Nature\'s Bounty. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Vitamin K may interact with warfarin and other anticoagulant treatment. Ask your clinician before use and keep intake consistent.', 14.10, 0.00, 39, NULL, NULL, '[\"/assets/products/Nature\'s Bounty/Vitamins/Vitamin K2 100 mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(82, 3, 5, 'Organics Men\'s 40+ Multi 60 Vegan Tablets', 'Organics Men\'s 40+ Multi 60 Vegan Tablets by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 21.16, 0.00, 41, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Organics-Mens-40-Plus-Multi-60-Vegan-Tablets-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(83, 2, 5, 'Vitamin Code Raw Zinc 30 mg 60 Vegan Capsules', 'Vitamin Code Raw Zinc 30 mg 60 Vegan Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 16.57, 0.00, 30, NULL, NULL, '[\"/assets/products/Garden of Life/Minerals/Vitamin-Code-Raw-Zinc-30-mg-60-Vegan-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(84, 3, 5, 'Vitamin Code Gummies Kids Multi with Iron 60 Gummies', 'Vitamin Code Gummies Kids Multi with Iron 60 Gummies by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 23.62, 0.00, 38, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Vitamin-Code-Gummies-Kids-Multi-with-Iron-60-Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(85, 6, 5, 'Raw Organic Perfect Food Chocolate 285 g', 'Raw Organic Perfect Food Chocolate 285 g by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 25.74, 0.00, 15, NULL, NULL, '[\"/assets/products/Garden of Life/Specialty Supplements/Raw-Organic-Perfect-Food-Chocolate-285-g-main-cutout.png\", \"/assets/products/Garden of Life/Specialty Supplements/Raw-Organic-Perfect-Food-Chocolate-285-g-gallery-02-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(86, 3, 5, 'Organics Women\'s Once Daily Multi 60 Vegan Tablets', 'Organics Women\'s Once Daily Multi 60 Vegan Tablets by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 23.98, 0.00, 35, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Organics-Womens-Once-Daily-Multi-60-Vegan-Tablets-main-cutout.png\", \"/assets/products/Garden of Life/Multivitamins/Organics-Womens-Once-Daily-Multi-60-Vegan-Tablets-gallery-02-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(87, 3, 5, 'Vitamin Code 50 & Wiser Women 120 Vegetarian Capsules', 'Vitamin Code 50 & Wiser Women 120 Vegetarian Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 22.21, 0.00, 25, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Vitamin-Code-50-and-Wiser-Women-120-Vegetarian-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(88, 3, 5, 'Organics Men\'s Once Daily Multi 60 Vegan Tablets', 'Organics Men\'s Once Daily Multi 60 Vegan Tablets by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 27.50, 0.00, 32, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Organics-Mens-Once-Daily-Multi-60-Vegan-Tablets-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(89, 3, 5, 'Organics Women\'s Multi 40+ Gummies 90 Gummies', 'Organics Women\'s Multi 40+ Gummies 90 Gummies by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 19.04, 0.00, 39, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Organics-Womens-Multi-40-Plus-Gummies-90-Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(90, 3, 5, 'Organics Women\'s Multi 40+ 120 Vegan Tablets', 'Organics Women\'s Multi 40+ 120 Vegan Tablets by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 27.50, 0.00, 29, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Organics-Womens-Multi-40-Plus-120-Vegan-Tablets-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(91, 4, 5, 'Dr. Formulated DHA 1000 mg 30 Softgels', 'Dr. Formulated DHA 1000 mg 30 Softgels by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 27.50, 0.00, 29, NULL, NULL, '[\"/assets/products/Garden of Life/Omega and Fish Oil/Dr-Formulated-DHA-1000-mg-30-Softgels-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(92, 4, 5, 'Dr. Formulated Norwegian Salmon Oil 60 Softgels', 'Dr. Formulated Norwegian Salmon Oil 60 Softgels by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 25.39, 0.00, 19, NULL, NULL, '[\"/assets/products/Garden of Life/Omega and Fish Oil/Dr-Formulated-Norwegian-Salmon-Oil-60-Softgels-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(93, 4, 5, 'Olde World Icelandic Cod Liver Oil Lemon Mint 8 fl oz', 'Olde World Icelandic Cod Liver Oil Lemon Mint 8 fl oz by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 26.09, 0.00, 26, NULL, NULL, '[\"/assets/products/Garden of Life/Omega and Fish Oil/Olde-World-Icelandic-Cod-Liver-Oil-Lemon-Mint-8-fl-oz-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(94, 4, 5, 'Minami Platinum Omega-3 Fish Oil 30 Softgels', 'Minami Platinum Omega-3 Fish Oil 30 Softgels by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 32.09, 0.00, 33, NULL, NULL, '[\"/assets/products/Garden of Life/Omega and Fish Oil/Minami-Platinum-Omega-3-Fish-Oil-30-Softgels-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(95, 5, 5, 'Dr. Formulated Probiotics 100 Billion 30 Vegetarian Capsules', 'Dr. Formulated Probiotics 100 Billion 30 Vegetarian Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 39.49, 0.00, 21, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/Dr-Formulated-Probiotics-100-Billion-30-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(96, 5, 5, 'Dr. Formulated Probiotics 30 Billion 30 Mini Capsules', 'Dr. Formulated Probiotics 30 Billion 30 Mini Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 25.74, 0.00, 28, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/Dr-Formulated-Probiotics-30-Billion-30-Mini-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(97, 5, 5, 'RAW Probiotics Colon Care 50 Billion 30 Vegetarian Capsules', 'RAW Probiotics Colon Care 50 Billion 30 Vegetarian Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 39.14, 0.00, 18, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/RAW-Probiotics-Colon-Care-50-Billion-30-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(98, 5, 5, 'Dr. Formulated Probiotics Microbiome Balance 50 Billion 30 Capsules', 'Dr. Formulated Probiotics Microbiome Balance 50 Billion 30 Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 36.67, 0.00, 25, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/Dr-Formulated-Probiotics-Microbiome-Balance-50-Billion-30-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(99, 5, 5, 'Dr. Formulated Digestive & Immune Care with Zinc 40 Billion 30 Capsules', 'Dr. Formulated Digestive & Immune Care with Zinc 40 Billion 30 Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 30.32, 0.00, 32, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/Dr-Formulated-Digestive-Immune-Care-with-Zinc-40-Billion-30-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(100, 5, 5, 'Dr. Formulated Once Daily Women\'s Probiotic 50 Billion 30 Capsules', 'Dr. Formulated Once Daily Women\'s Probiotic 50 Billion 30 Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 40.90, 0.00, 22, NULL, NULL, '[\"/assets/products/Garden of Life/Probiotics/Dr-Formulated-Once-Daily-Womens-Probiotic-50-Billion-30-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(101, 2, 5, 'Organics Plant Calcium 180 Vegan Tablets', 'Organics Plant Calcium 180 Vegan Tablets by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 22.92, 0.00, 37, NULL, NULL, '[\"/assets/products/Garden of Life/Minerals/Organics-Plant-Calcium-180-Vegan-Tablets-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(102, 6, 5, 'Organic Protein Vanilla 18 Servings 511 g', 'Organic Protein Vanilla 18 Servings 511 g by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 27.50, 0.00, 15, NULL, NULL, '[\"/assets/products/Garden of Life/Specialty Supplements/Organic-Protein-Vanilla-18-Servings-511-g-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(103, 3, 5, 'Vitamin Code Gummies Prenatal Multi with Iron & Folate 90 Gummies', 'Vitamin Code Gummies Prenatal Multi with Iron & Folate 90 Gummies by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 26.80, 0.00, 35, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Vitamin-Code-Gummies-Prenatal-Multi-Iron-Folate-90-Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(104, 3, 5, 'Vitamin Code Gummies Women\'s Multi with Iron 90 Gummies', 'Vitamin Code Gummies Women\'s Multi with Iron 90 Gummies by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 24.68, 0.00, 25, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Vitamin-Code-Gummies-Womens-Multi-with-Iron-90-Gummies-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(105, 3, 5, 'Organics Prenatal Multi 90 Vegan Tablets', 'Organics Prenatal Multi 90 Vegan Tablets by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 24.33, 0.00, 32, NULL, NULL, '[\"/assets/products/Garden of Life/Multivitamins/Organics-Prenatal-Multi-90-Vegan-Tablets-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(106, 2, 5, 'Vitamin Code Raw Iron 22 mg 30 Vegan Capsules', 'Vitamin Code Raw Iron 22 mg 30 Vegan Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains iron or is intended for pregnancy support: use only as directed. Keep iron products out of children\'s reach; accidental overdose can be fatal. Confirm suitability with an obstetrician or pediatrician.', 17.63, 0.00, 38, NULL, NULL, '[\"/assets/products/Garden of Life/Minerals/Vitamin-Code-Raw-Iron-22-mg-30-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(107, 1, 5, 'Vitamin Code Raw Vitamin E 60 Vegetarian Capsules', 'Vitamin Code Raw Vitamin E 60 Vegetarian Capsules by Garden of Life. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.98, 0.00, 34, NULL, NULL, '[\"/assets/products/Garden of Life/Vitamins/Vitamin-Code-Raw-Vitamin-E-60-Capsules-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(108, 2, 9, 'Cal-Mag Stress Formula 100 Tablets', 'Cal-Mag Stress Formula 100 Tablets by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.46, 0.00, 35, NULL, NULL, '[\"/assets/products/NOW Foods/Minerals/1275_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(109, 2, 9, 'Bone Meal Powder 454 g', 'Bone Meal Powder 454 g by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 11.99, 0.00, 25, NULL, NULL, '[\"/assets/products/NOW Foods/Minerals/1220_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(110, 2, 9, 'Calcium Citrate 180 Veg Capsules', 'Calcium Citrate 180 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 13.75, 0.00, 32, NULL, NULL, '[\"/assets/products/NOW Foods/Minerals/1235_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(111, 2, 9, 'Calcium & Magnesium with D3 and Zinc 120 Softgels', 'Calcium & Magnesium with D3 and Zinc 120 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 9.87, 0.00, 39, NULL, NULL, '[\"/assets/products/NOW Foods/Minerals/1251_v12-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(112, 2, 9, 'Calcium Hydroxyapatite 120 Capsules', 'Calcium Hydroxyapatite 120 Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 12.69, 0.00, 29, NULL, NULL, '[\"/assets/products/NOW Foods/Minerals/1253_v7-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(113, 3, 9, 'EVE Superior Women\'s Multi 120 Veg Capsules', 'EVE Superior Women\'s Multi 120 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 11.99, 0.00, 37, NULL, NULL, '[\"/assets/products/NOW Foods/Multivitamins/3798_v11-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(114, 3, 9, 'ADAM Superior Men\'s Multi 90 Veg Capsules', 'ADAM Superior Men\'s Multi 90 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.16, 0.00, 27, NULL, NULL, '[\"/assets/products/NOW Foods/Multivitamins/3878_v17-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(115, 3, 9, 'Liquid Multi Tropical Punch 473 ml', 'Liquid Multi Tropical Punch 473 ml by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.28, 0.00, 34, NULL, NULL, '[\"/assets/products/NOW Foods/Multivitamins/3772_v14-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(116, 3, 9, 'EcoGreen Multi 180 Veg Capsules', 'EcoGreen Multi 180 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.63, 0.00, 41, NULL, NULL, '[\"/assets/products/NOW Foods/Multivitamins/3788_v11-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(117, 3, 9, 'Daily Vits One Tablet Daily 120 Tablets', 'Daily Vits One Tablet Daily 120 Tablets by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.16, 0.00, 31, NULL, NULL, '[\"/assets/products/NOW Foods/Multivitamins/3770_v14-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(118, 4, 9, 'Molecularly Distilled Omega-3 Fish Oil 1000 mg 30 Softgels', 'Molecularly Distilled Omega-3 Fish Oil 1000 mg 30 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 19.75, 0.00, 31, NULL, NULL, '[\"/assets/products/NOW Foods/Omega and Fish Oil/1649_v8-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(119, 4, 9, 'Krill Oil 500 mg 60 Softgels', 'Krill Oil 500 mg 60 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 17.63, 0.00, 21, NULL, NULL, '[\"/assets/products/NOW Foods/Omega and Fish Oil/1625_v14-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(120, 4, 9, 'DHA-250 Fish Oil 120 Softgels', 'DHA-250 Fish Oil 120 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 17.28, 0.00, 28, NULL, NULL, '[\"/assets/products/NOW Foods/Omega and Fish Oil/1610_v16-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(121, 4, 9, 'Kids DHA Fish Oil Chewables 60 Softgels', 'Kids DHA Fish Oil Chewables 60 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 15.87, 0.00, 18, NULL, NULL, '[\"/assets/products/NOW Foods/Omega and Fish Oil/1607_v11_0-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(122, 5, 9, 'Probiotic-10 50 Billion Powder', 'Probiotic-10 50 Billion Powder by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 20.10, 0.00, 23, NULL, NULL, '[\"/assets/products/NOW Foods/Probiotics/2927_v5-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(123, 5, 9, 'Aloe 10,000 & Probiotics', 'Aloe 10,000 & Probiotics by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 22.57, 0.00, 30, NULL, NULL, '[\"/assets/products/NOW Foods/Probiotics/3029_v5-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(124, 5, 9, 'Probiotic-10 25 Billion 30 Veg Capsules', 'Probiotic-10 25 Billion 30 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 19.75, 0.00, 20, NULL, NULL, '[\"/assets/products/NOW Foods/Probiotics/2937_v6-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(125, 5, 9, 'Probiotic-10 50 Billion 50 Veg Capsules', 'Probiotic-10 50 Billion 50 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 20.45, 0.00, 27, NULL, NULL, '[\"/assets/products/NOW Foods/Probiotics/2928_v9-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(126, 5, 9, 'Clinical GI Probiotic 50+ Formula', 'Clinical GI Probiotic 50+ Formula by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Ask a healthcare professional before use if immunocompromised, critically ill, or treating a serious condition. Follow storage directions on the package.', 18.34, 0.00, 17, NULL, NULL, '[\"/assets/products/NOW Foods/Probiotics/2919_v8-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(127, 6, 9, 'MK-7 Vitamin K2 100 mcg 60 Veg Capsules', 'MK-7 Vitamin K2 100 mcg 60 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.81, 0.00, 20, NULL, NULL, '[\"/assets/products/NOW Foods/Specialty Supplements/0992_v14-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(128, 6, 9, 'DMG 125 mg 100 Veg Capsules', 'DMG 125 mg 100 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 13.75, 0.00, 27, NULL, NULL, '[\"/assets/products/NOW Foods/Specialty Supplements/0472_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(129, 6, 9, 'Coenzyme B-Complex 60 Veg Capsules', 'Coenzyme B-Complex 60 Veg Capsules by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.98, 0.00, 17, NULL, NULL, '[\"/assets/products/NOW Foods/Specialty Supplements/0406_v6-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(130, 6, 9, 'D3 + K2 Chewables 90 Tablets', 'D3 + K2 Chewables 90 Tablets by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 19.04, 0.00, 24, NULL, NULL, '[\"/assets/products/NOW Foods/Specialty Supplements/0389_v2-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(131, 6, 9, 'Vitamin A & D 10,000 IU / 400 IU 100 Softgels', 'Vitamin A & D 10,000 IU / 400 IU 100 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 17.63, 0.00, 14, NULL, NULL, '[\"/assets/products/NOW Foods/Specialty Supplements/0350_v11-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(132, 1, 9, 'Niacin 500 mg', 'Niacin 500 mg by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 9.17, 0.00, 39, NULL, NULL, '[\"/assets/products/NOW Foods/Vitamins/0481_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(133, 1, 9, 'Vitamin B-12 5000 mcg Lozenges', 'Vitamin B-12 5000 mcg Lozenges by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 9.87, 0.00, 46, NULL, NULL, '[\"/assets/products/NOW Foods/Vitamins/0462_v5-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(134, 1, 9, 'B-12 Shots 10,000 mcg 12 Packets', 'B-12 Shots 10,000 mcg 12 Packets by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 11.28, 0.00, 36, NULL, NULL, '[\"/assets/products/NOW Foods/Vitamins/0453_v9_Left Angle-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(135, 1, 9, 'B-50 B-Complex 100 Tablets', 'B-50 B-Complex 100 Tablets by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 9.52, 0.00, 43, NULL, NULL, '[\"/assets/products/NOW Foods/Vitamins/0426_mainimage-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(136, 1, 9, 'Vitamin A 10,000 IU 100 Softgels', 'Vitamin A 10,000 IU 100 Softgels by NOW Foods. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 11.28, 0.00, 33, NULL, NULL, '[\"/assets/products/NOW Foods/Vitamins/0330_v12-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(137, 2, 1, 'Calcium 600 mg with Vitamin D3', 'Calcium 600 mg with Vitamin D3 by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 13.05, 0.00, 34, NULL, NULL, '[\"/assets/products/Nature Made/Minerals/Calcium 600 mg with Vitamin D3-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(138, 2, 1, 'Calcium Magnesium Zinc with D3', 'Calcium Magnesium Zinc with D3 by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 14.46, 0.00, 24, NULL, NULL, '[\"/assets/products/Nature Made/Minerals/Calcium Magnesium Zinc with D3-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(139, 2, 1, 'Magnesium 250 mg Tablets', 'Magnesium 250 mg Tablets by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 11.99, 0.00, 31, NULL, NULL, '[\"/assets/products/Nature Made/Minerals/Magnesium 250 mg Tablets-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(140, 2, 1, 'Magnesium Citrate 250 mg', 'Magnesium Citrate 250 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 14.81, 0.00, 38, NULL, NULL, '[\"/assets/products/Nature Made/Minerals/Magnesium Citrate 250 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(141, 2, 1, 'Magnesium Complex with D3 and Zinc', 'Magnesium Complex with D3 and Zinc by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 12.34, 0.00, 28, NULL, NULL, '[\"/assets/products/Nature Made/Minerals/Magnesium Complex with D3 and Zinc-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(142, 3, 1, 'Advanced Multivitamin for Adults', 'Advanced Multivitamin for Adults by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.16, 0.00, 36, NULL, NULL, '[\"/assets/products/Nature Made/Multivitamins/Advanced Multivitamin for Adults-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(143, 3, 1, 'Multivitamin + Omega 3', 'Multivitamin + Omega 3 by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 19.75, 0.00, 26, NULL, NULL, '[\"/assets/products/Nature Made/Multivitamins/Multivitamin + Omega-3-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(144, 3, 1, 'Multivitamin with Iron', 'Multivitamin with Iron by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Minerals can interact with some medicines and may need to be taken separately. Ask a healthcare professional if you have kidney disease or take prescription medicine.', 16.22, 0.00, 33, NULL, NULL, '[\"/assets/products/Nature Made/Multivitamins/Multivitamin with Iron-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(145, 4, 1, 'Burp Less Fish Oil 1200 mg', 'Burp Less Fish Oil 1200 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 37.38, 0.00, 33, NULL, NULL, '[\"/assets/products/Nature Made/Omega and Fish Oil/Burp-Less Fish Oil 1200 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(146, 4, 1, 'Flaxseed Oil 1000 mg', 'Flaxseed Oil 1000 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Contains fish or seafood ingredients (check the label for the exact source). Avoid if allergic; ask a clinician if using anticoagulants or before surgery.', 16.93, 0.00, 23, NULL, NULL, '[\"/assets/products/Nature Made/Omega and Fish Oil/Flaxseed Oil 1000 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(147, 6, 1, 'CoQ10 200 mg', 'CoQ10 200 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'CoQ10 may interact with warfarin and some blood pressure or glucose medicines. Ask a healthcare professional before use.', 14.10, 0.00, 24, NULL, NULL, '[\"/assets/products/Nature Made/Specialty Supplements/CoQ10 200 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(148, 6, 1, 'Cranberry', 'Cranberry by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 16.93, 0.00, 14, NULL, NULL, '[\"/assets/products/Nature Made/Specialty Supplements/Cranberry-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(149, 6, 1, 'Triple Flex', 'Triple Flex by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.16, 0.00, 21, NULL, NULL, '[\"/assets/products/Nature Made/Specialty Supplements/Triple Flex-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(150, 6, 1, 'Turmeric Curcumin', 'Turmeric Curcumin by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 20.45, 0.00, 28, NULL, NULL, '[\"/assets/products/Nature Made/Specialty Supplements/Turmeric Curcumin-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(151, 1, 1, 'Biotin 2500 mcg', 'Biotin 2500 mcg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.10, 0.00, 36, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Biotin 2500 mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(152, 1, 1, 'Folic Acid 400 mcg', 'Folic Acid 400 mcg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 15.51, 0.00, 43, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Folic Acid 400 mcg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(153, 1, 1, 'Vitamin B1 100 mg', 'Vitamin B1 100 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 11.28, 0.00, 32, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Vitamin B1 100 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:36:06'),
(154, 1, 1, 'Vitamin C 500 mg', 'Vitamin C 500 mg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 12.34, 0.00, 40, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Vitamin C 500 mg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(155, 1, 1, 'Vitamin D3 2000 IU', 'Vitamin D3 2000 IU by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 14.10, 0.00, 30, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Vitamin D3 2000 IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33'),
(156, 1, 1, 'Vitamin E 400 IU', 'Vitamin E 400 IU by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Read the complete label and follow its directions. Check allergens and consult a healthcare professional if pregnant or breastfeeding, managing a condition, or taking medicines.', 9.87, 0.00, 36, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Vitamin E 400 IU-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:21:40'),
(157, 1, 1, 'Vitamin K2 100 mcg', 'Vitamin K2 100 mcg by Nature Made. Product details, serving size, ingredients, and directions can vary by package; check the label of the item supplied.', NULL, 'Vitamin K may interact with warfarin and other anticoagulant treatment. Ask your clinician before use and keep intake consistent.', 13.40, 0.00, 44, NULL, NULL, '[\"/assets/products/Nature Made/Vitamins/Vitamin K2 100 mcgg-cutout.png\"]', 1, NULL, NULL, '2026-10-31 23:43:27', '2026-11-01 12:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `star_rating` tinyint(3) UNSIGNED NOT NULL,
  `comment` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'approved',
  `image` varchar(255) DEFAULT NULL,
  `moderated_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rating_avg` decimal(3,2) NOT NULL DEFAULT 0.00,
  `rating_count` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `product_id`, `user_id`, `star_rating`, `comment`, `status`, `image`, `moderated_by_admin_id`, `created_at`, `updated_at`, `rating_avg`, `rating_count`) VALUES
(1, 141, 1, 4, 'very good product', 'approved', NULL, NULL, '2026-11-01 00:07:34', '2026-11-01 00:07:34', 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `google_id` varchar(100) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(80) DEFAULT NULL,
  `is_blocked_from_commenting` tinyint(1) NOT NULL DEFAULT 0,
  `is_blocked_from_rating` tinyint(1) NOT NULL DEFAULT 0,
  `blocked_by_admin_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `google_id`, `phone`, `address`, `city`, `is_blocked_from_commenting`, `is_blocked_from_rating`, `blocked_by_admin_id`, `created_at`, `updated_at`) VALUES
(1, 'ahmad', 'ahmad@gmail.com', '$2y$10$HJo0IjJA0hNd/s2lwFg0EOPXJZUfJABLVIB5GHVUf921EbIwsuM/m', NULL, NULL, NULL, NULL, 0, 0, NULL, '2026-10-31 20:48:58', '2026-10-31 20:48:58'),
(2, 'Ahmad Batta', 'ahmadbatta234@gmail.com', NULL, '111760218247564869854', NULL, NULL, NULL, 0, 0, NULL, '2026-11-01 12:07:53', '2026-11-01 12:07:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `ai_assistant_requests`
--
ALTER TABLE `ai_assistant_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ai_requests_user` (`user_id`),
  ADD KEY `idx_ai_requests_handled_by_admin` (`handled_by_admin_id`);

--
-- Indexes for table `app_migrations`
--
ALTER TABLE `app_migrations`
  ADD PRIMARY KEY (`migration_key`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_brands_created_by_admin` (`created_by_admin_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `idx_categories_created_by_admin` (`created_by_admin_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`),
  ADD KEY `idx_orders_processed_by_admin` (`processed_by_admin_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_items_order` (`order_id`),
  ADD KEY `idx_order_items_product` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `provider_transaction_id` (`provider_transaction_id`),
  ADD KEY `idx_payments_order` (`order_id`),
  ADD KEY `idx_payments_reviewed_by_admin` (`reviewed_by_admin_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category` (`category_id`),
  ADD KEY `idx_products_brand` (`brand_id`),
  ADD KEY `idx_products_created_by_admin` (`created_by_admin_id`),
  ADD KEY `idx_products_updated_by_admin` (`updated_by_admin_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_review` (`product_id`,`user_id`),
  ADD KEY `idx_reviews_product` (`product_id`),
  ADD KEY `idx_reviews_user` (`user_id`),
  ADD KEY `idx_reviews_moderated_by_admin` (`moderated_by_admin_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD KEY `idx_users_blocked_by_admin` (`blocked_by_admin_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_assistant_requests`
--
ALTER TABLE `ai_assistant_requests`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ai_assistant_requests`
--
ALTER TABLE `ai_assistant_requests`
  ADD CONSTRAINT `fk_ai_requests_handled_by_admin` FOREIGN KEY (`handled_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_air_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `brands`
--
ALTER TABLE `brands`
  ADD CONSTRAINT `fk_brands_created_by_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `fk_categories_created_by_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_processed_by_admin` FOREIGN KEY (`processed_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_oi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_pay_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_payments_reviewed_by_admin` FOREIGN KEY (`reviewed_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_prod_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_products_created_by_admin` FOREIGN KEY (`created_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_products_updated_by_admin` FOREIGN KEY (`updated_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_rev_userA` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_moderated_by_admin` FOREIGN KEY (`moderated_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_blocked_by_admin` FOREIGN KEY (`blocked_by_admin_id`) REFERENCES `admins` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
-- Constraint carried over from adel/adel-db.sql.
ALTER TABLE `users`
  ADD CONSTRAINT `chk_users_auth`
  CHECK (`password_hash` IS NOT NULL OR `google_id` IS NOT NULL);

-- Idempotent catalog seeds from adel/adel-db.sql; matching products are left as-is.
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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- Synthetic demonstration reviews: clearly labeled and repeated for every catalog product.
INSERT IGNORE INTO users (name, email, password_hash) VALUES
('Sample Reviewer One', 'sample-reviewer-one@example.test', '!disabled-demo-account!'),
('Sample Reviewer Two', 'sample-reviewer-two@example.test', '!disabled-demo-account!'),
('Sample Reviewer Three', 'sample-reviewer-three@example.test', '!disabled-demo-account!');

INSERT INTO reviews (product_id, user_id, star_rating, comment, status, created_at)
SELECT p.id, u.id, sample.star_rating,
       CONCAT('[Sample review] ', p.name, CASE sample.star_rating
           WHEN 1 THEN CASE MOD(p.id, 4)
               WHEN 0 THEN ' arrived later than expected and the packaging was damaged.'
               WHEN 1 THEN ' was difficult to open, and delivery took longer than expected.'
               WHEN 2 THEN ' did not match what I expected from the listing.'
               ELSE ' arrived in a damaged box and was disappointing.'
           END
           WHEN 3 THEN CASE MOD(p.id, 4)
               WHEN 0 THEN ' was acceptable overall, but the product details could be clearer.'
               WHEN 1 THEN ' is okay, though delivery and packaging were average.'
               WHEN 2 THEN ' met basic expectations, but I am unsure I would order it again.'
               ELSE ' was neither great nor poor; the product listing could include more details.'
           END
           ELSE CASE MOD(p.id, 4)
               WHEN 0 THEN ' arrived in secure packaging and matched the listing.'
               WHEN 1 THEN ' was easy to order, and the package arrived in good condition.'
               WHEN 2 THEN ' was a positive purchase, with clear product details and prompt delivery.'
               ELSE ' arrived as described, and the overall shopping experience was smooth.'
           END
       END),
       'approved', UTC_TIMESTAMP() - INTERVAL sample.days_ago DAY
FROM products p
CROSS JOIN (
    SELECT 'sample-reviewer-one@example.test' AS reviewer_email, 1 AS star_rating, 24 AS days_ago
    UNION ALL
    SELECT 'sample-reviewer-two@example.test', 3, 17
    UNION ALL
    SELECT 'sample-reviewer-three@example.test', 5, 9
) AS sample
JOIN users u ON u.email = sample.reviewer_email
WHERE NOT EXISTS (
    SELECT 1 FROM reviews existing
    WHERE existing.product_id = p.id AND existing.user_id = u.id
);

CREATE TABLE IF NOT EXISTS product_review_summaries (
    product_id INT(10) UNSIGNED NOT NULL,
    summary_text TEXT NOT NULL,
    review_count INT(10) UNSIGNED NOT NULL DEFAULT 0,
    generated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (product_id),
    CONSTRAINT fk_product_review_summaries_product
        FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO admins (name, email, password_hash)
VALUES (
  'Admin',
  'admin@mail.com',
  '$2y$10$f2VY04WaCA3inRFON9BPzuAULztZXHJJnUUsVKHkiN.v5.o9MbWAG'
)
ON DUPLICATE KEY UPDATE
  name = VALUES(name),
  password_hash = VALUES(password_hash);
