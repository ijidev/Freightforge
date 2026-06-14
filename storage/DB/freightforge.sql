-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2026 at 09:07 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `freightforge`
--

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `page` varchar(100) NOT NULL,
  `section_key` varchar(100) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `subtitle` varchar(255) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `page`, `section_key`, `title`, `subtitle`, `content`, `image_path`, `created_at`, `updated_at`) VALUES
(1, 'home', 'hero', 'Your Shipments, Always in Sight', 'Send packages anywhere with confidence. Real-time tracking, instant updates, and delivery notifications — so you never have to wonder where your cargo is.', '', NULL, '2026-06-11 01:32:51', '2026-06-11 01:32:51'),
(2, 'home', 'how_it_works_intro', 'How It Works', 'Three simple steps to ship with confidence', '', NULL, '2026-06-11 01:32:51', '2026-06-11 01:32:51'),
(3, 'home', 'features_intro', 'Why Choose Us', 'Built for people who send and receive shipments every day', '', NULL, '2026-06-11 01:32:51', '2026-06-11 01:32:51'),
(4, 'home', 'stats', 'Reliable Shipping, Worldwide', 'From small parcels to full freight loads — we connect you with trusted carriers across road, sea, and air networks.', '500+,Routes Covered,99.2%,On-Time Delivery,50K+,Shipments Delivered', NULL, '2026-06-11 01:32:51', '2026-06-11 01:32:51'),
(5, 'home', 'track_intro', 'Track a Shipment', 'Have a tracking number? Check your shipment status in seconds.', '', NULL, '2026-06-11 01:32:51', '2026-06-11 01:32:51'),
(6, 'home', 'cta', 'Ready to Get Started?', 'Join thousands of satisfied customers — reliable shipping starts here.', '', NULL, '2026-06-11 01:32:51', '2026-06-11 01:32:51'),
(7, 'about', 'hero', 'Shipping Made Simple', 'We connect people with the shipments that matter — across town or across the ocean.', '', NULL, '2026-06-11 01:32:52', '2026-06-11 01:32:52'),
(8, 'about', 'story', 'Our Promise', '', 'Every day, thousands of packages move between businesses, families, and communities. We believe every shipment should be trackable, predictable, and worry-free.\n\nWhether you\'re sending a single parcel or managing frequent freight, our platform gives you the visibility you need — without the complexity.', NULL, '2026-06-11 01:32:52', '2026-06-11 01:32:52'),
(9, 'about', 'offerings_intro', 'What We Offer', 'Clear, reliable features that make shipping easier', '', NULL, '2026-06-11 01:32:52', '2026-06-11 01:32:52'),
(10, 'about', 'trust', 'Trusted by Businesses Big and Small', 'From local shops to global enterprises, companies rely on us to keep their shipments moving and their customers informed.', '50K+,Shipments Delivered,500+,Cities Covered,98%,Satisfaction Rate', NULL, '2026-06-11 01:32:52', '2026-06-11 01:32:52'),
(11, 'about', 'cta', 'Have a Question?', 'We\'re here to help with your shipments, tracking, or anything you need.', '', NULL, '2026-06-11 01:32:52', '2026-06-11 01:32:52');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'TrustyCourierExpress', '2026-06-09 18:55:30', '2026-06-09 18:55:30'),
(2, 'site_email', '', '2026-06-09 18:55:30', '2026-06-09 18:55:30'),
(3, 'site_description', '', '2026-06-09 18:55:30', '2026-06-09 18:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL,
  `tracking_number` varchar(50) NOT NULL,
  `origin` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `sender_name` varchar(255) NOT NULL,
  `sender_email` varchar(255) NOT NULL,
  `sender_phone` varchar(50) DEFAULT NULL,
  `recipient_name` varchar(255) NOT NULL,
  `recipient_email` varchar(255) NOT NULL,
  `recipient_phone` varchar(50) DEFAULT NULL,
  `weight` decimal(10,2) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipments`
--

INSERT INTO `shipments` (`id`, `tracking_number`, `origin`, `destination`, `status`, `sender_name`, `sender_email`, `sender_phone`, `recipient_name`, `recipient_email`, `recipient_phone`, `weight`, `description`, `created_at`, `updated_at`) VALUES
(1, 'FF-1781024343-E0C1', 'new york', 'toronto', 'in_transit', 'mark john', 'iheanyichukwui94@gmail.com', '957427', 'rio uscer', 'info@email.com', '12345678', 5.00, 'now in movement\r\n', '2026-06-09 17:59:03', '2026-06-11 01:55:47'),
(2, 'FF-1781035201-2AD5', 'New York', 'Los Angeles', 'pending', 'John Doe', 'john@test.com', '', 'Jane Doe', 'jane@test.com', '', NULL, 'Test shipment', '2026-06-09 21:00:01', '2026-06-09 21:00:01');

-- --------------------------------------------------------

--
-- Table structure for table `shipment_statuses`
--

CREATE TABLE `shipment_statuses` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `status` varchar(100) NOT NULL,
  `remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipment_statuses`
--

INSERT INTO `shipment_statuses` (`id`, `shipment_id`, `status`, `remark`, `created_at`) VALUES
(1, 1, 'in_transit', 'Initial status', '2026-06-09 17:59:03'),
(2, 2, 'pending', 'Initial status', '2026-06-09 21:00:01'),
(3, 1, 'in_transit', 'item moved ', '2026-06-11 00:44:45'),
(4, 1, 'in_transit', 'under inspection', '2026-06-11 00:55:47');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `color` varchar(50) NOT NULL DEFAULT 'blue',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `name`, `slug`, `color`, `sort_order`, `created_at`) VALUES
(1, 'Pending', 'pending', 'yellow', 1, '2026-06-11 00:38:03'),
(2, 'Picked Up', 'picked_up', 'blue', 2, '2026-06-11 00:38:03'),
(3, 'In Transit', 'in_transit', 'blue', 3, '2026-06-11 00:38:04'),
(4, 'Out for Delivery', 'out_for_delivery', 'indigo', 4, '2026-06-11 00:38:04'),
(5, 'Delivered', 'delivered', 'green', 5, '2026-06-11 00:38:04'),
(6, 'Cancelled', 'cancelled', 'red', 6, '2026-06-11 00:38:05'),
(7, 'On Hold', 'on_hold', 'orange', 7, '2026-06-11 00:38:05'),
(8, 'Held by custom', 'held_by_custom', 'yellow', 5, '2026-06-11 02:07:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

-- INSERT INTO `users` (`id`, `name`, `email`, `password`, `created_at`) VALUES
-- (1, 'Admin', 'admin@freightforge.test', '$2y$10$2.dndZsPPLRuFEulwGYJveLijfcorE.Lylg67m1zQyHzkLXBNxcE6', '2026-06-09 15:34:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `key` (`key`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tracking_number` (`tracking_number`);

--
-- Indexes for table `shipment_statuses`
--
ALTER TABLE `shipment_statuses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipment_id` (`shipment_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `shipment_statuses`
--
ALTER TABLE `shipment_statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `shipment_statuses`
--
ALTER TABLE `shipment_statuses`
  ADD CONSTRAINT `shipment_statuses_ibfk_1` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
