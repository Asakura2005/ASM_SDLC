-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Server: 127.0.0.1
-- Generation Time: Aug 01, 2025 at 02:02 PM
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
-- Database: `food_ordering_system`
--
CREATE DATABASE IF NOT EXISTS `food_ordering_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `food_ordering_system`;

DELIMITER $$
--
-- Functions
--
DROP FUNCTION IF EXISTS `format_address`$$
CREATE DEFINER=`root`@`localhost` FUNCTION `format_address` (`address` TEXT, `city` VARCHAR(100), `state` VARCHAR(100), `postal_code` VARCHAR(20), `country` VARCHAR(100)) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE formatted_address TEXT DEFAULT '';
    
    IF address IS NOT NULL AND address != '' THEN
        SET formatted_address = CONCAT(formatted_address, address);
    END IF;
    
    IF city IS NOT NULL AND city != '' THEN
        SET formatted_address = CONCAT(formatted_address, 
            CASE WHEN formatted_address != '' THEN ', ' ELSE '' END, 
            city);
    END IF;
    
    IF state IS NOT NULL AND state != '' THEN
        SET formatted_address = CONCAT(formatted_address, 
            CASE WHEN formatted_address != '' THEN ', ' ELSE '' END, 
            state);
    END IF;
    
    IF postal_code IS NOT NULL AND postal_code != '' THEN
        SET formatted_address = CONCAT(formatted_address, 
            CASE WHEN formatted_address != '' THEN ' ' ELSE '' END, 
            postal_code);
    END IF;
    
    IF country IS NOT NULL AND country != '' THEN
        SET formatted_address = CONCAT(formatted_address, 
            CASE WHEN formatted_address != '' THEN ', ' ELSE '' END, 
            country);
    END IF;
    
    RETURN formatted_address;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`) VALUES
(2, 'Noodles'),
(1, 'Rice'),
(3, 'Drinks'),
(4, 'Desserts');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

DROP TABLE IF EXISTS `discounts`;
CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_type` enum('percent','fixed') DEFAULT 'percent',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order` decimal(10,2) DEFAULT 0.00,
  `max_discount` decimal(10,2) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`discount_id`, `code`, `description`, `discount_type`, `discount_value`, `min_order`, `max_discount`, `start_date`, `end_date`, `active`) VALUES
(1, 'GIAM10', '10% off for orders from 100,000đ', 'percent', 10.00, 100000.00, 30000.00, '2025-07-01', '2025-08-01', 1),
(2, 'FIX20K', '20,000đ off for orders from 80,000đ', 'fixed', 20000.00, 80000.00, 20000.00, '2025-07-01', '2025-08-15', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `item_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` text DEFAULT NULL,
  `restaurant_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`item_id`, `name`, `description`, `price`, `image_url`, `restaurant_id`, `category_id`) VALUES
(1, 'Cơm sườn bì chả', 'Cơm sườn bì chả với trứng ốp la', 40000.00, 'https://icdn.one/upload/2020/11/13/20201113055510-faed832c.jpg', 1, 1),
(2, 'Bún bò Huế đặc biệt', 'Tô bún bò Huế đầy đủ chả, giò, bò', 45000.00, 'https://vivu.net/uploads/2022/05/Nuoc-dung-hao-hang-dem-lai-huong-vi-tuyet-voi-cho-mon-dac-san-Hue-noi-tieng-.jpeg', 2, 2),
(3, 'Trà đào cam sả', 'Thức uống mát lạnh, vị đào, cam sả', 20000.00, 'https://tse1.mm.bing.net/th/id/OIP.XKSyF_cRKO-rEkvtHA3s4QHaE8?r=0&rs=1&pid=ImgDetMain&o=7&rm=3', 1, 3),
(4, 'Chè khúc bạch', 'Chè khúc bạch trái cây', 25000.00, 'https://cdn.tgdd.vn/Files/2017/10/09/1031511/cach-lam-che-khuc-bach-mem-tan-mat-lanh-giai-nhiet-mua-he-202201101220264914.jpg', 4, 4),
(5, 'Cơm gà xối mỡ', 'Cơm gà xối mỡ giòn rụm, kèm đồ chua', 45000.00, 'https://tunaucom123.com.vn/wp-content/uploads/2022/12/mon-com-ga-xoi-mo.jpg', 1, 1),
(6, 'Cơm chiên Dương Châu', 'Cơm chiên trứng, lạp xưởng, tôm, đậu', 40000.00, 'https://img-global.cpcdn.com/recipes/5395b12659aaf368/1200x630cq70/photo.jpg', 1, 1),
(7, 'Cơm cá kho tộ', 'Cơm trắng với cá kho đậm đà, thơm lừng', 50000.00, 'https://i.ytimg.com/vi/D8UQywSuOBg/maxresdefault.jpg', 1, 1),
(8, 'Bún chả Hà Nội', 'Bún chả nướng truyền thống Hà Nội', 45000.00, 'https://cdn.tgdd.vn/Files/2017/04/12/971481/cach-lam-bun-cha-ha-noi-truyen-thong-202112211431417496.jpg', 2, 2),
(9, 'Bún riêu cua', 'Bún riêu đậm vị cua đồng, hành phi', 42000.00, 'https://tse2.mm.bing.net/th/id/OIP.NQHZtf8DCLePOZv59SPpVQHaE9?rs=1&pid=ImgDetMain&o=7&rm=3', 2, 2),
(10, 'Bún thịt nướng', 'Bún thịt nướng, rau sống, nước mắm chua ngọt', 40000.00, 'https://th.bing.com/th/id/OIP.osMxka1UY6y4AULqvN4iuAHaLH?o=7rm=3&rs=1&pid=ImgDetMain&o=7&rm=3', 2, 2),
(11, 'Sữa đậu nành đá', 'Sữa đậu nành nguyên chất thơm mát', 12000.00, 'https://tse1.explicit.bing.net/th/id/OIP.u68KVm-PzgEcPDu40aYF8AHaFE?rs=1&pid=ImgDetMain&o=7&rm=3', 1, 3),
(12, 'Trà sữa truyền thống', 'Trà sữa đen trân châu đường đen', 30000.00, 'https://www.bartender.edu.vn/wp-content/uploads/2015/11/tra-sua-truyen-thong.jpg', 1, 3),
(13, 'Nước mía tắc', 'Nước mía thêm tắc thơm ngon, mát lạnh', 15000.00, 'https://droh.co/wp-content/uploads/2020/02/9-tac-dung-cua-nuoc-mia-khien-ban.jpg', 1, 3),
(14, 'Bánh flan', 'Bánh flan trứng mềm, ngọt dịu', 15000.00, 'https://bepxua.vn/wp-content/uploads/2020/05/banhflan2.jpg', 2, 4),
(15, 'Kem dừa Thái', 'Kem dừa Thái với topping thạch, đậu phộng', 30000.00, 'https://s.elib.vn/images/fckeditor/upload/2020/20200917/images/th%C3%A0nh%20ph%E1%BA%A9m%20kem%20d%E1%BB%ABa%20th%C3%A1i%20lan.jpg', 2, 4),
(16, 'Rau câu dừa', 'Rau câu hai lớp cốt dừa và lá dứa', 18000.00, 'https://cdn.tgdd.vn/2021/11/CookRecipe/Avatar/rau-cau-dua-soi-thumbnail.jpg', 2, 4),
(17, 'Chè thái sầu riêng', 'Chè trái cây sầu riêng, thạch, hạt é', 25000.00, 'https://i.ytimg.com/vi/LjuecckV0gM/maxresdefault.jpg', 2, 4),
(18, 'Chè đậu xanh đánh đá', 'Chè đậu xanh mịn với đá xay, ngọt mát', 15000.00, 'https://cachnau.vn/wp-content/uploads/2021/11/cacnh-nau-che-dau-xanh-danh-ngon.jpg', 2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_price` decimal(10,2) DEFAULT 0.00,
  `delivery_address` text DEFAULT NULL,
  `delivery_city` varchar(100) DEFAULT NULL,
  `delivery_state` varchar(100) DEFAULT NULL,
  `delivery_postal_code` varchar(20) DEFAULT NULL,
  `delivery_country` varchar(100) DEFAULT NULL,
  `delivery_notes` text DEFAULT NULL,
  `status` enum('pending','confirmed','delivered','cancelled') DEFAULT 'pending',
  `discount_code` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `order_date`, `total_price`, `delivery_address`, `delivery_city`, `delivery_state`, `delivery_postal_code`, `delivery_country`, `delivery_notes`, `status`, `discount_code`) VALUES
(1, 2, '2025-07-22 21:09:19', 65000.00, NULL, NULL, NULL, NULL, NULL, NULL, 'confirmed', 'GIAM10'),
(2, 5, '2025-07-29 21:41:12', 115000.00, 'asdasd', 'Bà Rịa - Vũng Tàu', 'asd', '', 'Vietnam', '', 'confirmed', ''),
(3, 6, '2025-07-30 01:00:12', 175500.00, 'ádasfsdgsfgergergr', 'An Giang', 'adfasfegfew', '', 'Vietnam', '', 'confirmed', 'GIAM10');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `item_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`order_item_id`, `order_id`, `item_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 40000.00),
(2, 1, 3, 1, 20000.00),
(3, 2, 2, 1, 45000.00),
(4, 2, 4, 1, 25000.00),
(5, 2, 8, 1, 45000.00),
(6, 3, 2, 1, 45000.00),
(7, 3, 14, 10, 15000.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('cash','credit_card','e_wallet') NOT NULL,
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `amount` decimal(10,2) NOT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_method`, `payment_date`, `amount`, `status`) VALUES
(1, 1, 'credit_card', '2025-07-22 21:09:19', 65000.00, 'paid'),
(2, 2, 'e_wallet', '2025-07-29 21:41:12', 115000.00, 'paid'),
(3, 3, 'cash', '2025-07-30 01:00:12', 195000.00, 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
CREATE TABLE `ratings` (
  `rating_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `score` int(11) DEFAULT NULL CHECK (`score` between 1 and 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`rating_id`, `user_id`, `item_id`, `score`, `comment`, `created_at`) VALUES
(1, 2, 1, 5, 'The rice is very delicious, the meat is well-marinated', '2025-07-22 21:09:19'),
(2, 2, 3, 4, 'The peach tea is cool, a bit sweet for me', '2025-07-22 21:09:19');

-- --------------------------------------------------------

--
-- Table structure for table `restaurants`
--

DROP TABLE IF EXISTS `restaurants`;
CREATE TABLE `restaurants` (
  `restaurant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `owner_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurants`
--

INSERT INTO `restaurants` (`restaurant_id`, `name`, `address`, `phone`, `owner_id`) VALUES
(1, 'Cơm Tấm Hưng Phát', '123 Đường Lê Lợi, Quận 1, TP.HCM', '0909123456', 1),
(2, 'Bún Bò Huế 79', '79 Nguyễn Trãi, Quận 5, TP.HCM', '0911223344', 1),
(3, 'Tea and Coffee', '79 Lê Lợi', '0912742348', NULL),
(4, 'Chè Thúy Phong', '75 Minh Khai', '0375297888', 5),
(5, 'hung', '1231231', 'asdasdasdas', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `email`, `full_name`, `phone`, `role`, `created_at`) VALUES
(1, 'admin1', 'hashed_password_123', 'admin@example.com', 'Admin One', '0123456789', 'admin', '2025-07-22 21:09:19'),
(2, 'hung123', 'hashed_password_abc', 'hung@example.com', 'Nguyễn Tiến Hưng', '0987654321', 'customer', '2025-07-22 21:09:19'),
(3, 'duong22', '$2y$10$zvzvKFwXYhHIH1OCI/ilT.PbwQJs06UBclAGuhTLJjyRxtQPrl6J6', 'ntduongc@gmail.com', 'Nguyen Tien Duong', '0102031203', 'admin', '2025-07-28 05:41:56'),
(4, 'duong33', '$2y$10$Tm0rmwFO9S/cooVDNyN2B.zwzcf6KAOo.QEARXYi0WuKdhWljdbPe', 'ntduongb@gmail.com', 'Nguyen Tien Duong', '0102031203', 'customer', '2025-07-28 06:06:21'),
(5, 'admin', '$2y$10$BlZUdVTh7AtRiIpBMQODJOBoY5qG4jkh2QPIJJDMDz0iqFGmu1v.y', 'admin@admin.com', 'admin', '0396944022', 'admin', '2025-07-28 07:51:52'),
(6, 'duc', '$2y$10$XbyvULwb3CpHZHV0tYjLP.agfWVQhPMjvhb5UB2yPlXbhjupxqVqy', 'duc@gmail.com', 'duc', '12348335486', 'customer', '2025-07-30 00:57:19');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

DROP TABLE IF EXISTS `user_addresses`;
CREATE TABLE `user_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_name` varchar(100) NOT NULL DEFAULT 'Home',
  `full_address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) NOT NULL DEFAULT 'Vietnam',
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`address_id`, `user_id`, `address_name`, `full_address`, `city`, `state`, `postal_code`, `country`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 2, 'Home', '123 Nguyen Trai Street, District 1', 'Ho Chi Minh City', 'Ho Chi Minh', '70000', 'Vietnam', 1, '2025-07-28 07:47:17', '2025-07-28 07:47:17'),
(2, 2, 'Office', '456 Le Loi Boulevard, District 3', 'Ho Chi Minh City', 'Ho Chi Minh', '70000', 'Vietnam', 0, '2025-07-28 07:47:17', '2025-07-28 07:47:17'),
(3, 5, '109 antrai', 'asdasd', 'Bà Rịa - Vũng Tàu', 'asd', '', 'Vietnam', 0, '2025-07-29 21:40:45', '2025-07-29 21:40:45'),
(4, 6, 'nha duc', 'ádasfsdgsfgergergr', 'An Giang', 'adfasfegfew', '', 'Vietnam', 0, '2025-07-30 00:59:46', '2025-07-30 00:59:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`item_id`),
  ADD KEY `restaurant_id` (`restaurant_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`rating_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD PRIMARY KEY (`restaurant_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `idx_user_default` (`user_id`,`is_default`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `rating_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `restaurants`
--
ALTER TABLE `restaurants`
  MODIFY `restaurant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_ibfk_1` FOREIGN KEY (`restaurant_id`) REFERENCES `restaurants` (`restaurant_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `menu_items_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`item_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menu_items` (`item_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `restaurants`
--
ALTER TABLE `restaurants`
  ADD CONSTRAINT `restaurants_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;