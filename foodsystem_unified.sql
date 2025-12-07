-- ===================================================================
-- ALLERGEN CHECKER SYSTEM - UNIFIED DATABASE SCHEMA
-- Version 1.0 - Complete and Production Ready
-- ===================================================================

-- Drop existing tables if they exist (clean slate)
DROP TABLE IF EXISTS `received_orders`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `food`;
DROP TABLE IF EXISTS `users`;

-- ===================================================================
-- CREATE USERS TABLE
-- ===================================================================
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','client') DEFAULT 'client',
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `age` int(3) DEFAULT NULL,
  `contact_info` varchar(20) DEFAULT NULL,
  `allergies` text DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `username_idx` (`username`),
  KEY `role_idx` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===================================================================
-- CREATE FOOD TABLE
-- ===================================================================
CREATE TABLE `food` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `ingredients` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `name_idx` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===================================================================
-- CREATE ORDERS TABLE (Pending Orders)
-- ===================================================================
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `food_id` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `status_idx` (`status`),
  KEY `created_at_idx` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===================================================================
-- CREATE RECEIVED_ORDERS TABLE (Accepted & Completed Orders)
-- ===================================================================
CREATE TABLE `received_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `food_id` varchar(255) NOT NULL,
  `status` varchar(50) DEFAULT 'Accepted',
  `payment_status` varchar(50) DEFAULT 'Pending',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id_idx` (`user_id`),
  KEY `status_idx` (`status`),
  KEY `payment_status_idx` (`payment_status`),
  KEY `created_at_idx` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ===================================================================
-- INSERT TEST DATA
-- ===================================================================

-- Test Admin Account
-- Username: admin
-- Password: password123 (hashed with bcrypt)
INSERT INTO `users` (`id`, `username`, `password`, `role`, `first_name`, `last_name`, `created_at`) VALUES
(1, 'admin', '$2y$10$TutMw/nose3w6Rqw4aKCSe2BeLO0NyDqVLArdiO9ov.CkVAo1UDMu', 'admin', 'Admin', 'User', CURRENT_TIMESTAMP);

-- Test Client Accounts
-- Username: testclient
-- Password: password123 (hashed with bcrypt)
INSERT INTO `users` (`id`, `username`, `password`, `role`, `first_name`, `middle_name`, `last_name`, `age`, `contact_info`, `allergies`, `created_at`) VALUES
(2, 'testclient', '$2y$10$TutMw/nose3w6Rqw4aKCSe2BeLO0NyDqVLArdiO9ov.CkVAo1UDMu', 'client', 'John', 'Michael', 'Doe', 25, '09123456789', 'Dairy,Peanuts', CURRENT_TIMESTAMP),
(3, 'Dominic', '$2y$10$Nbq253203/bCLVIyUInr1up83USXOPqMRoT2SPndrcHPhXgaiUHdq', 'client', 'Dominic', 'Mari', 'Con-ui', 22, '09987654321', 'Dairy,Gluten,Peanuts', CURRENT_TIMESTAMP);

-- Test Staff Accounts
-- Username: teststaff, Kim, cris
-- Password: password123 (hashed with bcrypt)
INSERT INTO `users` (`id`, `username`, `password`, `role`, `first_name`, `last_name`, `created_at`) VALUES
(4, 'teststaff', '$2y$10$TutMw/nose3w6Rqw4aKCSe2BeLO0NyDqVLArdiO9ov.CkVAo1UDMu', 'staff', 'Test', 'Staff', CURRENT_TIMESTAMP),
(5, 'Kim', '$2y$10$3BaTelfVtax4htJOwk7fo.7fDEyAuYbT/v.p2zBb/atH1rO7Ycuje', 'staff', 'Kim', 'Joshua', CURRENT_TIMESTAMP),
(6, 'cris', '$2y$10$CnWC1g5sg0w3jChM6SK4hewGK1D9gBVSLCIWVWcxkSNBTZht/7Efu', 'staff', 'Cris', 'Oroc', CURRENT_TIMESTAMP);

-- Test Food Items
INSERT INTO `food` (`id`, `name`, `ingredients`, `price`, `image`, `created_at`) VALUES
(1, 'Burger', 'Bun, Beef Patty, Lettuce, Tomato, Cheese, Onion', 150.00, NULL, CURRENT_TIMESTAMP),
(2, 'Fried Chicken', 'Chicken, Flour, Salt, Pepper, Oil', 120.00, NULL, CURRENT_TIMESTAMP),
(3, 'Halo-halo', 'Ice Cream, Leche Flan, Soy beans, Peanuts, Beans, Sugar', 80.00, NULL, CURRENT_TIMESTAMP),
(4, 'Steak', 'Beef, Garlic, Parsley, Butter', 350.00, NULL, CURRENT_TIMESTAMP),
(5, 'Vegetable Salad', 'Lettuce, Tomato, Cucumber, Carrots, Oil Dressing', 90.00, NULL, CURRENT_TIMESTAMP),
(6, 'Fish and Chips', 'Fish Fillet, Potato, Flour, Salt, Oil', 200.00, NULL, CURRENT_TIMESTAMP);

-- ===================================================================
-- SET AUTO_INCREMENT VALUES
-- ===================================================================
ALTER TABLE `users` AUTO_INCREMENT=7;
ALTER TABLE `food` AUTO_INCREMENT=7;
ALTER TABLE `orders` AUTO_INCREMENT=1;
ALTER TABLE `received_orders` AUTO_INCREMENT=1;

-- ===================================================================
-- DATABASE SETUP COMPLETE
-- ===================================================================
-- Test Accounts:
-- Admin: admin / password123
-- Client: testclient / password123
-- Staff: teststaff / password123
-- 
-- All passwords are hashed with bcrypt (PASSWORD_DEFAULT)
-- Feel free to create new accounts through the registration page
-- ===================================================================
