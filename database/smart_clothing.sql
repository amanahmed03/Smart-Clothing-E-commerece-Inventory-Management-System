-- ====================================================================
-- Smart Clothing E-Commerce Management System
-- Database Schema & Sample Data Setup
-- Target Environment: MySQL 5.7+ / MariaDB 10.4+ (XAMPP) / PHP 8+
-- ====================================================================

-- 1. Create and select the database
CREATE DATABASE IF NOT EXISTS `smart_clothing`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `smart_clothing`;

-- Disable foreign key checks during drop & creation for clean re-imports
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Drop existing tables if they exist (in reverse dependency order)
DROP TABLE IF EXISTS `deliveries`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `cart_items`;
DROP TABLE IF EXISTS `cart`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- ====================================================================
-- Table: users
-- Purpose: User accounts for Admin, Staff, and Customers
-- ====================================================================
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'inventory_manager', 'delivery_manager', 'customer') NOT NULL DEFAULT 'customer',
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: categories
-- Purpose: Clothing product categories
-- ====================================================================
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL UNIQUE,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: products
-- Purpose: Smart clothing products with inventory tracking
-- ====================================================================
CREATE TABLE `products` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL UNIQUE,
  `description` TEXT DEFAULT NULL,
  `price` DECIMAL(10,2) NOT NULL,
  `stock_quantity` INT NOT NULL DEFAULT 0,
  `sizes` VARCHAR(100) DEFAULT 'S,M,L,XL',
  `color` VARCHAR(50) DEFAULT 'Standard',
  `smart_features` TEXT DEFAULT NULL,
  `image_url` VARCHAR(255) DEFAULT 'assets/images/products/default.jpg',
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_products_category`
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: cart
-- Purpose: Active user shopping carts
-- ====================================================================
CREATE TABLE `cart` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cart_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: cart_items
-- Purpose: Individual items contained in a shopping cart
-- ====================================================================
CREATE TABLE `cart_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `cart_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `selected_size` VARCHAR(20) DEFAULT 'M',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_cart_items_cart`
    FOREIGN KEY (`cart_id`) REFERENCES `cart` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_cart_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: orders
-- Purpose: Customer orders placed in the system
-- ====================================================================
CREATE TABLE `orders` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT UNSIGNED NOT NULL,
  `total_amount` DECIMAL(10,2) NOT NULL,
  `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
  `shipping_address` TEXT NOT NULL,
  `shipping_phone` VARCHAR(20) NOT NULL,
  `notes` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_orders_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: order_items
-- Purpose: Items and historical prices captured in each order
-- ====================================================================
CREATE TABLE `order_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL,
  `product_id` INT UNSIGNED NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `price` DECIMAL(10,2) NOT NULL,
  `selected_size` VARCHAR(20) DEFAULT 'M',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_order_items_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_order_items_product`
    FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: payments
-- Purpose: Payment records corresponding to orders
-- ====================================================================
CREATE TABLE `payments` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL UNIQUE,
  `payment_method` ENUM('cash_on_delivery', 'card', 'mobile_banking') NOT NULL DEFAULT 'cash_on_delivery',
  `payment_status` ENUM('pending', 'completed', 'failed', 'refunded') NOT NULL DEFAULT 'pending',
  `transaction_id` VARCHAR(100) DEFAULT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_payments_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- Table: deliveries
-- Purpose: Tracking assignments for Delivery Managers
-- ====================================================================
CREATE TABLE `deliveries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT UNSIGNED NOT NULL UNIQUE,
  `delivery_manager_id` INT UNSIGNED DEFAULT NULL,
  `delivery_status` ENUM('pending_assignment', 'assigned', 'out_for_delivery', 'delivered', 'returned') NOT NULL DEFAULT 'pending_assignment',
  `tracking_number` VARCHAR(100) DEFAULT NULL,
  `delivery_notes` TEXT DEFAULT NULL,
  `estimated_delivery` DATE DEFAULT NULL,
  `delivered_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_deliveries_order`
    FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_deliveries_manager`
    FOREIGN KEY (`delivery_manager_id`) REFERENCES `users` (`id`)
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- ====================================================================
-- SAMPLE DATA INSERTION
-- Password for all sample users: 'password123'
-- Hash: $2y$10$foUJX6Az.mLrj1pcf1arbeEunGNvs356QBMRW8inNRIjm64tK.fC2
-- ====================================================================

-- 1. Insert Users (1 Admin, 1 Inventory Manager, 1 Delivery Manager, 2 Customers)
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `address`) VALUES
(1, 'System Administrator', 'admin@smartclothing.com', '$2y$10$foUJX6Az.mLrj1pcf1arbeEunGNvs356QBMRW8inNRIjm64tK.fC2', 'admin', '+1-555-0100', '100 Tech Park, Headquarters'),
(2, 'Sarah Inventory', 'inventory@smartclothing.com', '$2y$10$foUJX6Az.mLrj1pcf1arbeEunGNvs356QBMRW8inNRIjm64tK.fC2', 'inventory_manager', '+1-555-0101', 'Logistics Center, Warehouse 4'),
(3, 'David Delivery', 'delivery@smartclothing.com', '$2y$10$foUJX6Az.mLrj1pcf1arbeEunGNvs356QBMRW8inNRIjm64tK.fC2', 'delivery_manager', '+1-555-0102', 'Distribution Hub 2'),
(4, 'John Doe', 'john@example.com', '$2y$10$foUJX6Az.mLrj1pcf1arbeEunGNvs356QBMRW8inNRIjm64tK.fC2', 'customer', '+1-555-0201', '742 Evergreen Terrace, Springfield'),
(5, 'Emma Watson', 'emma@example.com', '$2y$10$foUJX6Az.mLrj1pcf1arbeEunGNvs356QBMRW8inNRIjm64tK.fC2', 'customer', '+1-555-0202', '456 Oxford Street, London');

-- 2. Insert 5 Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Smart Jackets', 'smart-jackets', 'Weatherproof outer garments equipped with thermal regulation, haptic feedback, and connectivity sensors.'),
(2, 'Fitness & Athletic Wear', 'fitness-athletic-wear', 'High-performance activewear with bio-sensing capabilities, muscle monitoring, and breathability analytics.'),
(3, 'Heated Apparel', 'heated-apparel', 'Apparel featuring integrated carbon-fiber heating elements and multi-level rechargeable temperature control.'),
(4, 'Biometric Tops & Shirts', 'biometric-tops-shirts', 'Everyday and professional tops embedded with ECG, respiratory, and ergonomic posture sensors.'),
(5, 'Smart Sleepwear & Loungewear', 'smart-sleepwear-loungewear', 'Nightwear made with adaptive phase-change fabrics for circadian rhythm and skin temperature tracking.');

-- 3. Insert 10 Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `price`, `stock_quantity`, `sizes`, `color`, `smart_features`, `image_url`, `is_active`) VALUES
(1, 1, 'Smart Thermal Winter Jacket', 'smart-thermal-winter-jacket', 'Advanced cold-climate parka with three customizable heating zones controlled via Bluetooth smartphone app.', 189.99, 25, 'S,M,L,XL', 'Obsidian Black', 'Triple-zone heating, Bluetooth 5.2, Water-resistant battery compartment', 'assets/images/products/thermal_jacket.jpg', 1),
(2, 2, 'Pulse-Tracking Athletic Tee', 'pulse-tracking-athletic-tee', 'Seamless lightweight running shirt embedded with medical-grade ECG electrodes and sweat-rate detection.', 69.99, 50, 'S,M,L,XL,XXL', 'Electric Blue', 'Optical heart-rate sensor, Sweat-rate analytics, Ultra-breathable fabric', 'assets/images/products/athletic_tee.jpg', 1),
(3, 3, 'Heated Windproof Parka', 'heated-windproof-parka', 'Heavy-duty insulated parka with carbon fiber heat conductors providing up to 10 hours of continuous warmth.', 219.00, 15, 'M,L,XL', 'Graphite Grey', 'Carbon-fiber heating grids, USB-C fast charging, Wind-shield rating 10K', 'assets/images/products/heated_parka.jpg', 1),
(4, 4, 'Smart Posture-Correcting Shirt', 'smart-posture-correcting-shirt', 'Form-fitting daily shirt that provides subtle micro-vibration alerts whenever slouching is detected.', 89.50, 40, 'S,M,L,XL', 'Crisp White', 'Spine alignment sensors, Gentle haptic vibration, Machine washable', 'assets/images/products/posture_shirt.jpg', 1),
(5, 5, 'Circadian Sleep Monitoring Pajamas', 'circadian-sleep-monitoring-pajamas', 'Premium bamboo-blend sleep set that continuously records sleep stages, restlessness, and skin microclimate.', 79.99, 30, 'S,M,L', 'Midnight Navy', 'Skin temperature sensor, Sleep cycle tracking, Hypoallergenic fabric', 'assets/images/products/sleep_pajamas.jpg', 1),
(6, 1, 'Smart All-Weather Cycling Vest', 'smart-all-weather-cycling-vest', 'Ultra-lightweight cycling vest with integrated automatic deceleration brake lights and turn signals.', 129.00, 20, 'S,M,L,XL', 'Neon Hi-Vis Yellow', 'Built-in LED indicators, Gyroscope turn detection, Reflective micro-piping', 'assets/images/products/cycling_vest.jpg', 1),
(7, 2, 'Bio-Sensing Compression Leggings', 'bio-sensing-compression-leggings', 'Targeted compression tights that track lactic acid threshold buildup and muscle fatigue in real time.', 74.99, 45, 'XS,S,M,L', 'Matte Black', 'EMG muscle activation sensors, Gradient compression, 4-way stretch', 'assets/images/products/compression_leggings.jpg', 1),
(8, 3, 'Rechargeable Heated Fleece Hoodie', 'rechargeable-heated-fleece-hoodie', 'Cozy thermal fleece with chest and hand-warmer heating elements, powered by an ultra-thin 5000mAh battery.', 110.00, 35, 'S,M,L,XL', 'Heather Charcoal', 'Dual front & back warming panels, 3 heat settings, Magnetic cable ports', 'assets/images/products/fleece_hoodie.jpg', 1),
(9, 4, 'ECG Vitality Smart Undershirt', 'ecg-vitality-smart-undershirt', 'Discreet daily base layer monitoring continuous cardiac rhythm, heart-rate variability (HRV), and stress index.', 95.00, 28, 'S,M,L,XL', 'Classic Black', 'Continuous ECG tracing, Stress index calculation, Conductive yarn weaving', 'assets/images/products/ecg_undershirt.jpg', 1),
(10, 5, 'Adaptive Temperature Lounge Pants', 'adaptive-temperature-lounge-pants', 'Comfortable lounge trousers utilizing NASA-certified phase change materials that absorb or release heat as needed.', 65.00, 38, 'S,M,L,XL', 'Stone Grey', 'Phase change micro-capsules, Active thermal equilibrium, Soft touch cotton blend', 'assets/images/products/lounge_pants.jpg', 1);
