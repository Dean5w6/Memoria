-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 21, 2026 at 07:55 AM
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
(1, 1, 'Login', 'User logged into the system.', '::1', '2026-01-28 17:21:07'),
(2, 1, 'Login', 'User logged into the system.', '::1', '2026-01-28 17:23:50'),
(3, 1, 'Login', 'User logged into the system.', '::1', '2026-01-29 13:56:45'),
(4, 1, 'Login', 'User logged into the system.', '::1', '2026-01-29 13:58:38'),
(5, 1, 'Login', 'User logged into the system.', '127.0.0.1', '2026-01-30 00:16:30'),
(6, 1, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-04 06:59:15'),
(7, 1, 'Add Inventory', 'Added Urn: Test Urn', '127.0.0.1', '2026-02-04 07:00:06'),
(8, 3, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:21:13'),
(9, 1, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:24:21'),
(10, 4, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:24:41'),
(11, 5, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:24:54'),
(12, 3, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:26:08'),
(13, 3, 'New Reservation', 'Booked Chapel C (Grand Hall) for John Doe', '127.0.0.1', '2026-02-21 06:26:43'),
(14, 4, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:29:52'),
(15, 5, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:30:14'),
(16, 5, 'Fleet Dispatch', 'Dispatched Vehicle ID 2', '127.0.0.1', '2026-02-21 06:30:59'),
(17, 5, 'Fleet Dispatch', 'Dispatched Vehicle ID 1', '127.0.0.1', '2026-02-21 06:32:36'),
(18, 1, 'Login', 'User logged into the system.', '127.0.0.1', '2026-02-21 06:33:46'),
(19, 1, 'Payment Received', 'Recorded PHP 1000 for Invoice #2', '127.0.0.1', '2026-02-21 06:37:25'),
(20, 1, 'Payment Received', 'Recorded PHP 4000 for Invoice #2', '127.0.0.1', '2026-02-21 06:37:46');

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
  `status` enum('Scheduled','In Transit','Completed') DEFAULT 'Scheduled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dispatches`
--

INSERT INTO `dispatches` (`id`, `reservation_id`, `vehicle_id`, `driver_id`, `location_from`, `location_to`, `dispatch_time`, `status`) VALUES
(1, 1, 1, 2, 'Hospital', 'Church', '2026-01-29 14:00:00', 'Completed'),
(2, 2, 2, 2, 'Hospital', 'Test', '2026-02-20 14:30:00', 'Completed'),
(3, 2, 1, 2, 'Hospital', 'Test', '2026-02-21 14:32:00', 'Completed');

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `document_type` enum('Death Certificate','Burial Permit','Transfer Permit') NOT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `documents`
--

INSERT INTO `documents` (`id`, `reservation_id`, `document_type`, `reference_number`, `status`, `uploaded_at`, `file_path`) VALUES
(1, 1, 'Death Certificate', '12345', 'Verified', '2026-01-29 14:06:10', NULL),
(2, 2, 'Death Certificate', '123456', 'Verified', '2026-02-21 06:27:29', '69995051301f3.png'),
(3, 2, 'Burial Permit', '123456', 'Verified', '2026-02-21 06:27:53', '69995069bcd17.png');

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
(1, 1, 5000.00, 'Paid', '2026-01-29 14:07:04'),
(2, 2, 5000.00, 'Paid', '2026-02-21 06:36:58');

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
(1, 1, 'Casket', 5000.00),
(2, 2, 'Casket', 5000.00);

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `category` enum('Casket','Urn','Rental','Consumable','Religious') DEFAULT 'Casket',
  `item_name` varchar(100) DEFAULT NULL,
  `material` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `min_stock_level` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category`, `item_name`, `material`, `price`, `stock_quantity`, `min_stock_level`) VALUES
(1, 'Casket', 'Eternal Peace', 'Oak Wood', 45000.00, 10, 5),
(2, 'Casket', 'Silver Lining', 'Metal', 35000.00, 3, 5);

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
(1, 1, 2000.00, '2026-01-29 22:07:46', 1),
(2, 1, 3000.00, '2026-01-29 22:08:14', 1),
(3, 2, 1000.00, '2026-02-21 14:37:25', 1),
(4, 2, 4000.00, '2026-02-21 14:37:46', 1);

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
(1, 'Chapel A (St. Peter)', 'John Doe', '2026-01-29 14:00:00', '2026-01-29 16:00:00', 1),
(2, 'Chapel C (Grand Hall)', 'John Doe', '2026-02-22 14:26:00', '2026-02-22 17:26:00', 3);

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
  `contact_info` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `contact_info`, `created_at`) VALUES
(1, 'admin', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', 'System Administrator', 'Administrator', NULL, '2026-01-21 16:39:26'),
(2, 'driver1', '494d022492052a06f8f81949639a1d148c1051fa3d4e4688fbd96efe649cd382', 'Juan Dela Cruz', 'Driver', NULL, '2026-01-21 17:15:11'),
(3, 'frontdesk', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Sarah (Front Desk)', 'Front Desk Staff', NULL, '2026-02-21 06:11:25'),
(4, 'inventory', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Mark (Inventory)', 'Inventory Clerk', NULL, '2026-02-21 06:11:25'),
(5, 'fleet', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'David (Fleet)', 'Fleet Coordinator', NULL, '2026-02-21 06:11:25'),
(6, 'driver_mike', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Michael (Driver)', 'Driver', NULL, '2026-02-21 06:49:45'),
(7, 'driver_carlos', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Carlos (Driver)', 'Driver', NULL, '2026-02-21 06:49:45'),
(8, 'driver_ramon', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Ramon (Driver)', 'Driver', NULL, '2026-02-21 06:49:45'),
(9, 'driver_luis', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', 'Luis (Driver)', 'Driver', NULL, '2026-02-21 06:49:45'),
(10, 'driver_699956996ae4c', '9d3e7db98ac4e367f2e608e87ffc2ddd11fe1c1f74328a5f466853f5d1277b5e', 'Lexes', 'Driver', NULL, '2026-02-21 06:54:17');

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
(1, 'Mercedes Hearse', 'ABC-1234', 'Available'),
(2, 'Toyota HiAce Van', 'XYZ-9876', 'Available');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `dispatches`
--
ALTER TABLE `dispatches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  ADD CONSTRAINT `documents_ibfk_1` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE;

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
