-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 19, 2026 at 02:32 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `memoria_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, 1, 'System Init', 'System deployed and initialized.', '127.0.0.1', '2026-03-16 01:27:37'),
(2, 3, 'Add Inventory', 'Added Casket: St. Peter Classic', '127.0.0.1', '2026-03-16 01:27:37'),
(3, 2, 'Payment Received', 'Recorded PHP 50000.00 for Invoice #2', '127.0.0.1', '2026-03-19 01:27:37'),
(4, 1, 'Login', 'User logged into the system.', '127.0.0.1', '2026-03-19 01:27:55');

-- --------------------------------------------------------

--
-- Table structure for table `dispatches`
--

CREATE TABLE `dispatches` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `location_from` varchar(255) DEFAULT NULL,
  `location_to` varchar(255) DEFAULT NULL,
  `dispatch_time` datetime NOT NULL,
  `status` enum('Scheduled','Completed') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dispatches`
--

INSERT INTO `dispatches` (`id`, `reservation_id`, `vehicle_id`, `driver_id`, `location_from`, `location_to`, `dispatch_time`, `status`) VALUES
(1, 1, 1, 5, 'St. Luke\'s Medical Center', 'Heritage Park Taguig', '2026-03-17 09:27:37', 'Completed'),
(2, 2, 2, 6, 'Taguig District Hospital', 'Libingan ng mga Bayani', '2026-03-19 09:27:37', 'Scheduled');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `document_type` enum('Death Certificate','Burial Permit','Transfer Permit') NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Verified') DEFAULT 'Pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `reservation_id`, `document_type`, `reference_number`, `file_path`, `status`, `uploaded_at`) VALUES
(1, 1, 'Death Certificate', 'DC-2026-0991', NULL, 'Verified', '2026-03-19 01:27:37'),
(2, 1, 'Burial Permit', 'BP-TAGUIG-441', NULL, 'Verified', '2026-03-19 01:27:37'),
(3, 2, 'Death Certificate', 'DC-2026-1002', NULL, 'Verified', '2026-03-19 01:27:37'),
(4, 2, 'Burial Permit', 'BP-TAGUIG-445', NULL, 'Pending', '2026-03-19 01:27:37');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Unpaid','Partial','Paid','Cancelled') DEFAULT 'Unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `reservation_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 1, 62000.00, 'Paid', '2026-03-19 01:27:37'),
(2, 2, 137000.00, 'Partial', '2026-03-19 01:27:37');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `description`, `amount`) VALUES
(1, 1, 'Casket: St. Peter Classic Wooden', 45000.00),
(2, 1, 'Chapel Rental', 12000.00),
(3, 1, 'Hearse Service', 5000.00),
(4, 2, 'Casket: Heritage Bronze Premium', 120000.00),
(5, 2, 'Chapel Rental', 12000.00),
(6, 2, 'Consumable: Standard Standing Spray', 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `category` enum('Casket','Urn','Rental','Consumable','Religious') DEFAULT 'Casket',
  `item_name` varchar(100) NOT NULL,
  `material` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category`, `item_name`, `material`, `price`, `stock_quantity`, `min_stock_level`) VALUES
(1, 'Casket', 'St. Peter Classic Wooden', 'Mahogany', 45000.00, 12, 5),
(2, 'Casket', 'Heritage Bronze Premium', 'Bronze Metal', 120000.00, 3, 2),
(3, 'Casket', 'Eternal Peace Standard', 'Oak Wood', 65000.00, 8, 4),
(4, 'Casket', 'Economy Metal Casket', 'Steel', 35000.00, 4, 5),
(5, 'Urn', 'Carrara Marble Urn', 'Marble', 15000.00, 20, 10),
(6, 'Urn', 'Classic Brass Urn', 'Brass', 8500.00, 15, 5),
(7, 'Consumable', 'Premium Floral Wreath', 'Fresh Orchids/Roses', 12000.00, 8, 3),
(8, 'Consumable', 'Standard Standing Spray', 'Mixed Local Flowers', 5000.00, 15, 5),
(9, 'Consumable', 'Vigil Candles (Box of 50)', 'Wax', 800.00, 30, 10),
(10, 'Rental', 'Aircon Unit (Floor Standing)', 'Appliance', 3500.00, 5, 2),
(11, 'Rental', 'Outdoor Tent (Standard)', 'Canvas/Metal', 2500.00, 10, 3),
(12, 'Religious', 'Large Wood Crucifix', 'Mahogany', 1500.00, 12, 5);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_date` datetime DEFAULT current_timestamp(),
  `received_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `amount`, `payment_date`, `received_by`) VALUES
(1, 1, 62000.00, '2026-03-17 09:27:37', 2),
(2, 2, 50000.00, '2026-03-19 09:27:37', 2);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `chapel_name` varchar(50) NOT NULL,
  `deceased_name` varchar(100) NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `reserved_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `chapel_name`, `deceased_name`, `start_date`, `end_date`, `reserved_by`) VALUES
(1, 'Chapel A (St. Peter)', 'Eduardo Macapagal', '2026-03-17 09:27:37', '2026-03-18 09:27:37', 2),
(2, 'Chapel B (St. Mary)', 'Corazon Aquino', '2026-03-19 09:27:37', '2026-03-21 09:27:37', 2),
(3, 'Chapel C (Grand Hall)', 'Fidel V. Ramos', '2026-03-20 09:27:37', '2026-03-22 09:27:37', 2),
(4, 'Chapel A (St. Peter)', 'Miriam Defensor', '2026-03-23 09:27:37', '2026-03-25 09:27:37', 2);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('Administrator','Front Desk Staff','Inventory Clerk','Fleet Coordinator','Driver') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Dean Cabarles', 'Administrator', '2026-03-19 01:27:36'),
(2, 'frontdesk', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Hannah Nariz', 'Front Desk Staff', '2026-03-19 01:27:36'),
(3, 'inventory', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Glaiza Reblinca', 'Inventory Clerk', '2026-03-19 01:27:36'),
(4, 'fleet', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Ron Primavera', 'Fleet Coordinator', '2026-03-19 01:27:36'),
(5, 'driver_carlos', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Carlos Mendoza', 'Driver', '2026-03-19 01:27:36'),
(6, 'driver_miguel', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Miguel Santos', 'Driver', '2026-03-19 01:27:36'),
(7, 'driver_jose', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Jose Reyes', 'Driver', '2026-03-19 01:27:36'),
(8, 'driver_arthur', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Arthur Villanueva', 'Driver', '2026-03-19 01:27:36');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `vehicle_name` varchar(100) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `status` enum('Available','In Use','Maintenance') DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `vehicle_name`, `plate_number`, `status`) VALUES
(1, 'Mercedes-Benz Hearse (Black)', 'ABC-1234', 'Available'),
(2, 'Cadillac Premium Hearse (White)', 'XYZ-9876', 'In Use'),
(3, 'Toyota HiAce Service Van', 'NCA-5542', 'Available'),
(4, 'Nissan Urvan Estate', 'TQX-8811', 'Maintenance');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `dispatches`
--
ALTER TABLE `dispatches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservation_id` (`reservation_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reserved_by` (`reserved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dispatches`
--
ALTER TABLE `dispatches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `dispatches`
--
ALTER TABLE `dispatches`
  ADD CONSTRAINT `dispatches_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`),
  ADD CONSTRAINT `dispatches_ibfk_2` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`),
  ADD CONSTRAINT `dispatches_ibfk_3` FOREIGN KEY (`driver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `documents`
--
ALTER TABLE `documents`
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`reserved_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
