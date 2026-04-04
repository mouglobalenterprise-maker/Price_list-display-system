-- ============================================================
-- PHONE & ACCESSORY PRICE LOOKUP SYSTEM
-- Phase 1: Database Setup
-- ============================================================

-- Create and select the database
CREATE DATABASE IF NOT EXISTS phone_store
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE phone_store;

-- ============================================================
-- PRODUCTS TABLE (single table design - do not alter)
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    category    ENUM('iPhone','Android','Accessories') NOT NULL,
    brand       VARCHAR(100)    NOT NULL,
    series      VARCHAR(100)    DEFAULT NULL,          -- NULL for some accessories
    type        VARCHAR(100)    DEFAULT NULL,          -- mainly for accessories
    variant     VARCHAR(150)    DEFAULT NULL,
    full_name   VARCHAR(400)    NOT NULL,              -- AUTO GENERATED, never typed manually
    price       DECIMAL(12, 2)  NOT NULL,
    quantity    INT UNSIGNED    NOT NULL DEFAULT 0,
    keywords    VARCHAR(500)    DEFAULT NULL,          -- optional flexible search tags
    created_at  TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- PERFORMANCE INDEX (mandatory)
-- ============================================================
CREATE INDEX idx_search
    ON products (full_name(200), brand(100), series(100), type(100));

-- ============================================================
-- SAMPLE DATA (for testing search)
-- ============================================================

-- iPhones
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('iPhone', 'Apple', 'iPhone 13', NULL, 'Standard',  'Apple iPhone 13 Standard',  45000, 10, 'iphone13 apple 13'),
('iPhone', 'Apple', 'iPhone 13', NULL, 'Pro',        'Apple iPhone 13 Pro',        55000,  8, 'iphone13pro apple 13 pro'),
('iPhone', 'Apple', 'iPhone 13', NULL, 'Pro Max',    'Apple iPhone 13 Pro Max',    65000,  5, 'iphone13promax apple 13 max'),
('iPhone', 'Apple', 'iPhone 14', NULL, 'Standard',  'Apple iPhone 14 Standard',  75000,  7, 'iphone14 apple 14'),
('iPhone', 'Apple', 'iPhone 14', NULL, 'Pro',        'Apple iPhone 14 Pro',        90000,  4, 'iphone14pro'),
('iPhone', 'Apple', 'iPhone 14', NULL, 'Pro Max',    'Apple iPhone 14 Pro Max',   105000,  3, 'iphone14promax'),
('iPhone', 'Apple', 'iPhone 15', NULL, 'Standard',  'Apple iPhone 15 Standard',  95000,  6, 'iphone15 apple 15'),
('iPhone', 'Apple', 'iPhone 15', NULL, 'Pro',        'Apple iPhone 15 Pro',       115000,  3, 'iphone15pro'),
('iPhone', 'Apple', 'iPhone 15', NULL, 'Pro Max',    'Apple iPhone 15 Pro Max',   135000,  2, 'iphone15promax');

-- Android – Samsung
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('Android', 'Samsung', 'Galaxy S23', NULL, 'Standard', 'Samsung Galaxy S23 Standard', 55000, 6, 'samsung s23 galaxy'),
('Android', 'Samsung', 'Galaxy S23', NULL, 'Plus',     'Samsung Galaxy S23 Plus',      65000, 4, 'samsung s23 plus'),
('Android', 'Samsung', 'Galaxy S23', NULL, 'Ultra',    'Samsung Galaxy S23 Ultra',     85000, 3, 'samsung s23 ultra'),
('Android', 'Samsung', 'Galaxy A54', NULL, 'Standard', 'Samsung Galaxy A54 Standard',  35000, 9, 'samsung a54 galaxy a');

-- Android – Tecno
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('Android', 'Tecno', 'Camon 20', NULL, 'Standard', 'Tecno Camon 20 Standard', 18000, 12, 'tecno camon20'),
('Android', 'Tecno', 'Camon 20', NULL, 'Pro',      'Tecno Camon 20 Pro',       22000,  8, 'tecno camon20 pro'),
('Android', 'Tecno', 'Spark 20', NULL, 'Standard', 'Tecno Spark 20 Standard',  12000, 15, 'tecno spark20');

-- Accessories – Power Banks
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('Accessories', 'Oraimo', NULL, 'Power Bank', '10000mAh',         'Oraimo Power Bank 10000mAh',         1200, 20, 'oraimo powerbank 10000'),
('Accessories', 'Oraimo', NULL, 'Power Bank', '20000mAh',         'Oraimo Power Bank 20000mAh',         1800, 15, 'oraimo powerbank 20000'),
('Accessories', 'Oraimo', NULL, 'Power Bank', '20000mAh Fast Charge', 'Oraimo Power Bank 20000mAh Fast Charge', 2200, 10, 'oraimo powerbank fast charge');

-- Accessories – Chargers
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('Accessories', 'Apple',  NULL, 'Charger', '20W',         'Apple Charger 20W',         800,  12, 'apple charger 20w usb-c'),
('Accessories', 'Samsung',NULL, 'Charger', '25W Fast Charge', 'Samsung Charger 25W Fast Charge', 950, 10, 'samsung charger 25w fast'),
('Accessories', 'Oraimo', NULL, 'Charger', '18W',         'Oraimo Charger 18W',         600,  18, 'oraimo charger 18w');

-- Accessories – Cases
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('Accessories', 'Apple',  NULL, 'Case', 'iPhone 15 Silicone', 'Apple Case iPhone 15 Silicone', 500, 20, 'apple case iphone15 silicone cover'),
('Accessories', 'Samsung',NULL, 'Case', 'S23 Ultra Clear',    'Samsung Case S23 Ultra Clear',  400, 15, 'samsung case s23 clear cover');

-- Accessories – Earphones / TWS
INSERT INTO products (category, brand, series, type, variant, full_name, price, quantity, keywords) VALUES
('Accessories', 'Oraimo', NULL, 'Earbuds', 'FreePods 4', 'Oraimo Earbuds FreePods 4', 1500, 10, 'oraimo earbuds tws wireless freepods'),
('Accessories', 'Apple',  NULL, 'Earbuds', 'AirPods Pro 2', 'Apple Earbuds AirPods Pro 2', 8500, 5, 'apple airpods pro tws wireless');
