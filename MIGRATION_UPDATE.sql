-- SQL Migration Script for Allergen Checker System
-- This script updates the database schema to support full profile management
-- Run this if you have an existing database without the new profile fields

-- Add missing columns to users table
ALTER TABLE `users` ADD COLUMN `first_name` VARCHAR(100) DEFAULT NULL AFTER `role`;
ALTER TABLE `users` ADD COLUMN `middle_name` VARCHAR(100) DEFAULT NULL AFTER `first_name`;
ALTER TABLE `users` ADD COLUMN `last_name` VARCHAR(100) DEFAULT NULL AFTER `middle_name`;
ALTER TABLE `users` ADD COLUMN `age` INT(3) DEFAULT NULL AFTER `last_name`;
ALTER TABLE `users` ADD COLUMN `contact_info` VARCHAR(20) DEFAULT NULL AFTER `age`;
ALTER TABLE `users` ADD COLUMN `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER `allergies`;

-- Optional: If upgrading from old schema, you can populate some test data
-- INSERT INTO users (username, password, role, first_name, last_name) VALUES 
-- ('testclient', '$2y$10$...', 'client', 'Test', 'Client'),
-- ('teststaff', '$2y$10$...', 'staff', 'Test', 'Staff');

-- Verify the update
DESCRIBE `users`;

-- Display the full users table structure
SHOW CREATE TABLE `users`\G
