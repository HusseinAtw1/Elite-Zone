-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql101.infinityfree.com
-- Generation Time: Jan 04, 2026 at 03:15 AM
-- Server version: 11.4.9-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: if0_37995345_elite_zone
--

-- --------------------------------------------------------

--
-- Table structure for table accounts
--

CREATE TABLE accounts (
  ID int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Email varchar(255) NOT NULL,
  Password varchar(255) NOT NULL,
  activated tinyint(1) NOT NULL DEFAULT 1,
  is_admin tinyint(1) DEFAULT 0,
  date datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table brands
--

CREATE TABLE brands (
  Brand_ID int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Image longtext DEFAULT NULL,
  Description text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table cart
--

CREATE TABLE cart (
  cart_id int(11) NOT NULL,
  ID int(11) DEFAULT NULL,
  Product_ID int(11) DEFAULT NULL,
  cart_quantity int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table categories
--

CREATE TABLE categories (
  Category_ID int(11) NOT NULL,
  Name varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table chats
--

CREATE TABLE chats (
  chat_id int(11) NOT NULL,
  started_at datetime DEFAULT NULL,
  status varchar(255) DEFAULT 'ongoing',
  ID int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table chat_info
--

CREATE TABLE chat_info (
  chat_info_id int(11) NOT NULL,
  chat_id int(11) NOT NULL,
  sent_message text DEFAULT NULL,
  reply_message text DEFAULT NULL,
  time_of_message datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table contact_us
--

CREATE TABLE contact_us (
  contact_id int(12) NOT NULL,
  name varchar(255) NOT NULL,
  email varchar(255) NOT NULL,
  phone varchar(255) NOT NULL,
  message text NOT NULL,
  date datetime NOT NULL DEFAULT current_timestamp(),
  done tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table orderinfo
--

CREATE TABLE orderinfo (
  orderinfo_id int(11) NOT NULL,
  order_id int(11) DEFAULT NULL,
  Product_ID int(11) DEFAULT NULL,
  quantity int(11) DEFAULT NULL,
  price decimal(10,2) NOT NULL,
  bought_for decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table orders
--

CREATE TABLE orders (
  order_id int(11) NOT NULL,
  ID int(11) DEFAULT NULL,
  order_date datetime DEFAULT NULL,
  total decimal(10,2) DEFAULT NULL,
  status varchar(50) DEFAULT 'processing',
  first_name varchar(255) NOT NULL,
  last_name varchar(255) NOT NULL,
  mobile_phone varchar(20) NOT NULL,
  landline varchar(20) DEFAULT NULL,
  address text NOT NULL,
  city_town varchar(255) NOT NULL,
  email_address varchar(255) NOT NULL,
  additional_notes text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table products
--

CREATE TABLE products (
  Product_ID int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  product_img blob NOT NULL,
  Price decimal(10,2) NOT NULL,
  Description text DEFAULT NULL,
  Specifications text DEFAULT NULL,
  Quantity int(20) NOT NULL,
  Brand_ID int(11) DEFAULT NULL,
  Sub_ID int(11) DEFAULT NULL,
  bought_for decimal(10,2) DEFAULT NULL,
  net_price decimal(10,2) DEFAULT NULL,
  discount decimal(10,2) DEFAULT 0.00,
  selected tinyint(1) DEFAULT NULL,
  date_added timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table reviews
--

CREATE TABLE reviews (
  review_id bigint(20) UNSIGNED NOT NULL,
  ID int(11) NOT NULL,
  title varchar(255) NOT NULL,
  review_text text DEFAULT NULL,
  star_rating decimal(2,1) DEFAULT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table sub_categories
--

CREATE TABLE sub_categories (
  Sub_ID int(11) NOT NULL,
  Name varchar(255) NOT NULL,
  Category_ID int(11) DEFAULT NULL,
  selected tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table accounts
--
ALTER TABLE accounts
  ADD PRIMARY KEY (ID),
  ADD UNIQUE KEY Email (Email);

--
-- Indexes for table brands
--
ALTER TABLE brands
  ADD PRIMARY KEY (Brand_ID);

--
-- Indexes for table cart
--
ALTER TABLE cart
  ADD PRIMARY KEY (cart_id),
  ADD KEY ID (ID),
  ADD KEY Product_ID (Product_ID);

--
-- Indexes for table categories
--
ALTER TABLE categories
  ADD PRIMARY KEY (Category_ID);

--
-- Indexes for table chats
--
ALTER TABLE chats
  ADD PRIMARY KEY (chat_id),
  ADD KEY ID (ID);

--
-- Indexes for table chat_info
--
ALTER TABLE chat_info
  ADD PRIMARY KEY (chat_info_id),
  ADD KEY chat_id (chat_id);

--
-- Indexes for table contact_us
--
ALTER TABLE contact_us
  ADD PRIMARY KEY (contact_id);

--
-- Indexes for table orderinfo
--
ALTER TABLE orderinfo
  ADD PRIMARY KEY (orderinfo_id),
  ADD KEY order_id (order_id),
  ADD KEY Product_ID (Product_ID);

--
-- Indexes for table orders
--
ALTER TABLE orders
  ADD PRIMARY KEY (order_id),
  ADD KEY ID (ID);

--
-- Indexes for table products
--
ALTER TABLE products
  ADD PRIMARY KEY (Product_ID),
  ADD KEY Brand_ID (Brand_ID),
  ADD KEY Sub_ID (Sub_ID);

--
-- Indexes for table sub_categories
--
ALTER TABLE sub_categories
  ADD PRIMARY KEY (Sub_ID),
  ADD KEY Category_ID (Category_ID);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table accounts
--
ALTER TABLE accounts
  MODIFY ID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table brands
--
ALTER TABLE brands
  MODIFY Brand_ID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table cart
--
ALTER TABLE cart
  MODIFY cart_id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table categories
--
ALTER TABLE categories
  MODIFY Category_ID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table chats
--
ALTER TABLE chats
  MODIFY chat_id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table chat_info
--
ALTER TABLE chat_info
  MODIFY chat_info_id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table contact_us
--
ALTER TABLE contact_us
  MODIFY contact_id int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table orderinfo
--
ALTER TABLE orderinfo
  MODIFY orderinfo_id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table orders
--
ALTER TABLE orders
  MODIFY order_id int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table products
--
ALTER TABLE products
  MODIFY Product_ID int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table reviews
--
ALTER TABLE reviews
  MODIFY review_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table sub_categories
--
ALTER TABLE sub_categories
  MODIFY Sub_ID int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table cart
--
ALTER TABLE cart
  ADD CONSTRAINT cart_ibfk_1 FOREIGN KEY (ID) REFERENCES `accounts` (ID),
  ADD CONSTRAINT cart_ibfk_2 FOREIGN KEY (Product_ID) REFERENCES products (Product_ID);

--
-- Constraints for table chats
--
ALTER TABLE chats
  ADD CONSTRAINT chats_ibfk_1 FOREIGN KEY (ID) REFERENCES `accounts` (ID);

--
-- Constraints for table chat_info
--
ALTER TABLE chat_info
  ADD CONSTRAINT chat_info_ibfk_1 FOREIGN KEY (chat_id) REFERENCES chats (chat_id);

--
-- Constraints for table orderinfo
--
ALTER TABLE orderinfo
  ADD CONSTRAINT orderinfo_ibfk_1 FOREIGN KEY (order_id) REFERENCES `orders` (order_id),
  ADD CONSTRAINT orderinfo_ibfk_2 FOREIGN KEY (Product_ID) REFERENCES products (Product_ID);

--
-- Constraints for table orders
--
ALTER TABLE orders
  ADD CONSTRAINT orders_ibfk_1 FOREIGN KEY (ID) REFERENCES `accounts` (ID);

--
-- Constraints for table products
--
ALTER TABLE products
  ADD CONSTRAINT products_ibfk_1 FOREIGN KEY (Brand_ID) REFERENCES brands (Brand_ID);

--
-- Constraints for table sub_categories
--
ALTER TABLE sub_categories
  ADD CONSTRAINT sub_categories_ibfk_1 FOREIGN KEY (Category_ID) REFERENCES categories (Category_ID);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
