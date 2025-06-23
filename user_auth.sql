-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2025 at 06:16 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `user_auth`
--

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `created_at`) VALUES
(10, 'iPhone 12 Pro', 'Apple iPhone 12 Pro with 6.1-inch Super Retina XDR display, A14 Bionic chip, triple 12MP camera system with LiDAR scanner, 5G support, and up to 512GB storage options.', 999.00, '6858d3555cc3c.jpeg', '2025-06-23 04:08:53'),
(11, 'iPhone 13 Pro', 'Apple iPhone 13 Pro with 6.1-inch Super Retina XDR display, A15 Bionic chip, ProMotion technology with adaptive refresh rates up to 120Hz, triple 12MP camera system with improved low-light performance, Cinematic mode video recording, 5G support, and storage options up to 1TB.', 999.00, '6858d3c072f63.jpeg', '2025-06-23 04:10:40'),
(12, 'iPhone 14 Pro', 'Apple iPhone 14 Pro with 6.1-inch Super Retina XDR display featuring Always-On display and Dynamic Island, A16 Bionic chip, advanced 48MP main camera with ProRAW support, improved low-light photography, 5G connectivity, and storage options up to 1TB.', 999.00, '6858d3db192da.jpeg', '2025-06-23 04:11:07'),
(13, 'iPhone 15 Pro', 'Apple iPhone 15 Pro with 6.1-inch Super Retina XDR display, A17 Bionic chip, titanium frame, enhanced camera system with improved optical zoom, USB-C connectivity, advanced AI features, and up to 1TB storage.', 999.00, '6858d3fbc67d3.jpeg', '2025-06-23 04:11:39'),
(14, 'iPhone 16 Pro', 'Apple iPhone 16 Pro (expected) with 6.1-inch ProMotion LTPO OLED display, next-gen A18 Bionic chip, under-display Face ID, enhanced camera with periscope zoom, improved battery life, USB-C with faster charging, and up to 2TB storage options.', 1099.00, '6858d410b223e.jpeg', '2025-06-23 04:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`) VALUES
(1, 'Nimal', 'Nimal', 'Nimal@gmail.com', '$2y$10$2YujLlEFYwlNpDJ9HnGulu/4kpvCh/1aTtnza8KSqSvWOEskhs4nW'),
(2, 'Yashitha', 'yash', 'yashitha@gmail.com', '$2y$10$jTRje/Bc20vfnSemuVk7Y.Or4LZABURHjZDhWAVumxQ4/SYziHFgS'),
(3, 'amal', 'amal', 'amal@gmail.com', '$2y$10$2anVabchORZ.9o7Vg0acv.sbRWewECrGI3gOCaC0cKzO0qK4OkhDO'),
(5, 'kamal', 'kamal', 'yashithadissanayaka6@gmail.com', '$2y$10$VWH3QL1..cBUV2CqMYDs1.xHk6pr1ekzDABmH9J.Oh8wqMYGyCf52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
