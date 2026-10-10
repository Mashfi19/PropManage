
CREATE DATABASE IF NOT EXISTS `propmanage_db`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `propmanage_db`;

DROP TABLE IF EXISTS `audit_logs`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `maintenance_requests`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `bookings`;
DROP TABLE IF EXISTS `property_images`;
DROP TABLE IF EXISTS `rooms`;
DROP TABLE IF EXISTS `properties`;
DROP TABLE IF EXISTS `admin_profiles`;
DROP TABLE IF EXISTS `tenant_profiles`;
DROP TABLE IF EXISTS `landlord_profiles`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
    `user_id` INT AUTO_INCREMENT PRIMARY KEY,
    `email` VARCHAR(191) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('landlord', 'tenant', 'admin') NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(30) NULL,
    `profile_image` VARCHAR(255) DEFAULT 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=120&q=80',
    `status` ENUM('active', 'pending', 'suspended') NOT NULL DEFAULT 'active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `landlord_profiles` (
    `landlord_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `nid_number` VARCHAR(50) NULL,
    `tax_id` VARCHAR(50) NULL,
    `address` TEXT NULL,
    `business_name` VARCHAR(100) NULL,
    `verified_at` TIMESTAMP NULL,
    CONSTRAINT `fk_landlord_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `tenant_profiles` (
    `tenant_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `nid_number` VARCHAR(50) NULL,
    `date_of_birth` DATE NULL,
    `profession` VARCHAR(100) NULL,
    `emergency_contact` VARCHAR(50) NULL,
    CONSTRAINT `fk_tenant_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `admin_profiles` (
    `admin_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL UNIQUE,
    `access_level` VARCHAR(50) DEFAULT 'Super Admin',
    CONSTRAINT `fk_admin_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `properties` (
    `property_id` INT AUTO_INCREMENT PRIMARY KEY,
    `landlord_id` INT NOT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NULL,
    `rent_amount` DECIMAL(10, 2) NOT NULL,
    `address` VARCHAR(255) NOT NULL,
    `property_type` ENUM('Apartment', 'Family', 'Bachelor', 'Mansion', 'Commercial') NOT NULL DEFAULT 'Apartment',
    `bedrooms` INT NOT NULL DEFAULT 1,
    `bathrooms` INT NOT NULL DEFAULT 1,
    `size_sqft` INT NOT NULL DEFAULT 600,
    `status` ENUM('available', 'rented', 'pending_audit') NOT NULL DEFAULT 'available',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_properties_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rooms` (
    `room_id` INT AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT NOT NULL,
    `room_type` VARCHAR(50) NOT NULL,
    `rent_amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('available', 'occupied') DEFAULT 'available',
    CONSTRAINT `fk_rooms_property` FOREIGN KEY (`property_id`) REFERENCES `properties`(`property_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `property_images` (
    `image_id` INT AUTO_INCREMENT PRIMARY KEY,
    `property_id` INT NOT NULL,
    `image_url` VARCHAR(255) NOT NULL,
    `is_primary` TINYINT(1) DEFAULT 0,
    `uploaded_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_images_property` FOREIGN KEY (`property_id`) REFERENCES `properties`(`property_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `bookings` (
    `booking_id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `property_id` INT NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NULL,
    `monthly_rent` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_bookings_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bookings_property` FOREIGN KEY (`property_id`) REFERENCES `properties`(`property_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `payments` (
    `payment_id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NULL,
    `tenant_id` INT NOT NULL,
    `amount` DECIMAL(10, 2) NOT NULL,
    `payment_type` VARCHAR(50) DEFAULT 'Rent',
    `payment_method` ENUM('bkash', 'nagad', 'card', 'bank') NOT NULL DEFAULT 'bkash',
    `payment_status` ENUM('completed', 'pending', 'failed', 'overdue') NOT NULL DEFAULT 'completed',
    `payment_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_payments_booking` FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`booking_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_payments_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `maintenance_requests` (
    `request_id` INT AUTO_INCREMENT PRIMARY KEY,
    `tenant_id` INT NOT NULL,
    `property_id` INT NULL,
    `landlord_id` INT NULL,
    `title` VARCHAR(150) NOT NULL,
    `description` TEXT NOT NULL,
    `priority` ENUM('Urgent', 'Standard', 'Low') DEFAULT 'Standard',
    `category` VARCHAR(50) DEFAULT 'General',
    `status` ENUM('pending', 'progress', 'resolved') DEFAULT 'pending',
    `photo` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_maint_tenant` FOREIGN KEY (`tenant_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_maint_property` FOREIGN KEY (`property_id`) REFERENCES `properties`(`property_id`) ON DELETE SET NULL,
    CONSTRAINT `fk_maint_landlord` FOREIGN KEY (`landlord_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `messages` (
    `message_id` INT AUTO_INCREMENT PRIMARY KEY,
    `sender_id` INT NOT NULL,
    `receiver_id` INT NOT NULL,
    `content` TEXT NOT NULL,
    `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_msg_receiver` FOREIGN KEY (`receiver_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `audit_logs` (
    `log_id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL,
    `action_type` VARCHAR(100) NOT NULL,
    `details` TEXT NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT '127.0.0.1',
    `status` VARCHAR(20) DEFAULT 'SUCCESS',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_audit_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`user_id`, `email`, `password_hash`, `role`, `full_name`, `phone`, `profile_image`, `status`) VALUES
(1, 'admin@propmanage.com', '$2y$10$eACCYoNOHEqgkVE8aIWTmOn7Bq13o2STPFOvwSNpxLtxOK1GPxGri', 'admin', 'System Administrator', '01700-112233', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&q=80', 'active'),
(2, 'landlord@propmanage.com', '$2y$10$eACCYoNOHEqgkVE8aIWTmOn7Bq13o2STPFOvwSNpxLtxOK1GPxGri', 'landlord', 'Md Farhan Nadim', '01708-375128', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80', 'active'),
(3, 'tenant@propmanage.com', '$2y$10$eACCYoNOHEqgkVE8aIWTmOn7Bq13o2STPFOvwSNpxLtxOK1GPxGri', 'tenant', 'Mashfi Rahman', '01712-345678', 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100&q=80', 'active'),
(4, 'rifat@propmanage.com', '$2y$10$eACCYoNOHEqgkVE8aIWTmOn7Bq13o2STPFOvwSNpxLtxOK1GPxGri', 'landlord', 'Arafatullah Rifat', '01755-998877', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&q=80', 'active'),
(5, 'fariha@propmanage.com', '$2y$10$eACCYoNOHEqgkVE8aIWTmOn7Bq13o2STPFOvwSNpxLtxOK1GPxGri', 'tenant', 'Fariha Akter Prity', '01811-223344', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&q=80', 'active');

INSERT INTO `admin_profiles` (`admin_id`, `user_id`, `access_level`) VALUES
(1, 1, 'Super Admin');

INSERT INTO `landlord_profiles` (`landlord_id`, `user_id`, `nid_number`, `tax_id`, `address`, `business_name`, `verified_at`) VALUES
(1, 2, '1998269281000214', 'TAX-8891024', 'House 14, Road 7, Banani, Dhaka', 'Nadim Real Estate Ltd.', NOW()),
(2, 4, '1999269281000998', 'TAX-7729104', 'House 45, Gulshan Avenue, Dhaka', 'Rifat Properties & Homes', NOW());

INSERT INTO `tenant_profiles` (`tenant_id`, `user_id`, `nid_number`, `date_of_birth`, `profession`, `emergency_contact`) VALUES
(1, 3, '2001269281000319', '2001-05-14', 'Software Engineer', '01711-000111'),
(2, 5, '2002269281000452', '2002-08-20', 'Marketing Specialist', '01811-999888');

INSERT INTO `properties` (`property_id`, `landlord_id`, `title`, `description`, `rent_amount`, `address`, `property_type`, `bedrooms`, `bathrooms`, `size_sqft`, `status`) VALUES
(1, 2, 'Family Apartment', 'Spacious modern family apartment with open balcony, high speed lift, and generator backup.', 25000.00, '3/P, Banani, Dhaka', 'Family', 3, 3, 1100, 'available'),
(2, 2, 'Bachelor Unit', 'Convenient bachelor friendly flat located close to universities and shopping malls.', 22000.00, '101/F, Bashundhara, Dhaka', 'Bachelor', 3, 3, 1200, 'available'),
(3, 4, 'Bachelor Flat', 'Well-ventilated apartment in central Gulshan with 24/7 security and parking facility.', 19000.00, '1204 Gulshan, Dhaka', 'Bachelor', 2, 2, 1000, 'available'),
(4, 4, 'Luxury Mansion', 'Exclusive multi-level luxury mansion with garden, servant quarter, and premium interior.', 50000.00, '100H, Gulshan, Dhaka', 'Mansion', 4, 5, 5000, 'available'),
(5, 2, 'Bachelor Flat 2M', 'Cozy furnished flat with attached bathrooms, kitchen cabinets, and rooftop access.', 27000.00, '2M, Bashundhara, Dhaka', 'Bachelor', 3, 3, 1200, 'available');

INSERT INTO `property_images` (`property_id`, `image_url`, `is_primary`) VALUES
(1, 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=900&q=80', 1),
(2, 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=900&q=80', 1),
(3, 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=900&q=80', 1),
(4, 'https://images.unsplash.com/photo-1600607688969-a5bfcd646154?w=900&q=80', 1),
(5, 'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?w=900&q=80', 1);

INSERT INTO `bookings` (`booking_id`, `tenant_id`, `property_id`, `start_date`, `end_date`, `monthly_rent`, `status`) VALUES
(1, 3, 1, '2026-10-01', '2027-09-30', 25000.00, 'pending'),
(2, 5, 2, '2026-09-15', '2027-03-14', 22000.00, 'approved'),
(3, 3, 3, '2026-10-01', '2027-09-30', 19000.00, 'pending');

INSERT INTO `payments` (`payment_id`, `booking_id`, `tenant_id`, `amount`, `payment_type`, `payment_method`, `payment_status`) VALUES
(1, 2, 5, 22000.00, 'Monthly Rent', 'bkash', 'completed'),
(2, 1, 3, 25000.00, 'Advance Deposit', 'card', 'pending'),
(3, 2, 5, 1500.00, 'Maintenance Fee', 'nagad', 'completed');

INSERT INTO `maintenance_requests` (`request_id`, `tenant_id`, `property_id`, `landlord_id`, `title`, `description`, `priority`, `category`, `status`) VALUES
(1, 3, 1, 2, 'Water Leak in Unit 4B', 'Severe leakage under the kitchen sink. Water accumulating fast.', 'Urgent', 'Plumbing', 'pending'),
(2, 5, 2, 2, 'Electrical Trip in Master Bedroom', 'Circuit breaker tripping frequently when operating the water heater.', 'Standard', 'Electrical', 'progress'),
(3, 3, 3, 4, 'Door Lock Replacement', 'Front entrance digital lock malfunctioned. Serviced by technician.', 'Low', 'Carpentry', 'resolved');

INSERT INTO `messages` (`sender_id`, `receiver_id`, `content`, `sent_at`) VALUES
(4, 2, 'Hello! Is the 3-bed flat in Gulshan still available for rent?', '2026-09-02 10:15:00'),
(2, 4, 'Hi Rifat, yes it is currently available and ready for viewing.', '2026-09-02 10:18:00'),
(4, 2, 'Great! Can I schedule a visit this Friday afternoon?', '2026-09-02 10:20:00'),
(2, 4, 'Sure thing. 3:00 PM Friday works well. I will have the keys ready.', '2026-09-02 10:22:00');

INSERT INTO `audit_logs` (`user_id`, `action_type`, `details`, `ip_address`, `status`) VALUES
(1, 'Account Verification', 'Approved Landlord NID & Property Deeds for Farhan Nadim', '103.205.71.12', 'SUCCESS'),
(1, 'Payout Disbursed', 'Transfer of 24,500 BDT to Landlord Rifat Bank Account', '103.205.71.14', 'SUCCESS'),
(3, 'Rental Request', 'Tenant Mashfi Rahman submitted rent request for Property #1', '118.179.44.20', 'SUCCESS');
