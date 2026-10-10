-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2025 at 05:17 AM
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
-- Database: `furniland`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cartID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `productID` int(10) NOT NULL,
  `quantity` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cartID`, `userID`, `productID`, `quantity`) VALUES
(14, 1, 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `productID` int(10) NOT NULL,
  `productName` varchar(30) NOT NULL,
  `description` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `image` varchar(100) NOT NULL,
  `vendorID` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`productID`, `productName`, `description`, `price`, `image`, `vendorID`) VALUES
(1, 'Modern Sofa', 'Comfortable 3-seater sofa with soft cushions', 500000, 'sofa.webp', 1),
(2, 'Wooden Dining Table', 'Solid oak dining table for 6 people', 300000, 'table1.webp', 2),
(3, 'Office Chair', 'Ergonomic office chair with lumbar support', 150000, 'chair1.webp', 3),
(4, 'King Size Bed', 'Luxurious king size bed frame with storage', 800000, 'build-your-bed.jpg', 4),
(5, 'Coffee Table', 'Modern glass coffee table with metal frame', 100000, 'table2.webp', 1),
(6, 'Bookshelf', '5-tier wooden bookshelf for storage', 200000, 'shelf.jpeg', 2),
(7, 'Recliner Chair', 'Leather recliner with footrest', 400000, 'chair2.jpg', 3),
(8, 'Nightstand', 'Compact nightstand with 2 drawers', 50000, 'nightstand.webp', 4),
(9, 'TV Stand', 'Entertainment center with cable management', 250000, 'tvstand1.jpg', 1),
(10, 'Wardrobe', 'Large wardrobe with mirror doors', 600000, 'wardrobe1.jpg', 2),
(11, 'Hampers', 'A Hampers', 999999, '1765731619_Hampiness Logo.png', 5);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionID` int(10) NOT NULL,
  `userID` int(10) NOT NULL,
  `totalPrice` float NOT NULL,
  `transactionDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transactionID`, `userID`, `totalPrice`, `transactionDate`) VALUES
(1, 2, 700000, '2025-12-01'),
(2, 3, 150000, '2025-12-05'),
(3, 2, 700000, '2025-12-13'),
(4, 5, 4500000, '2025-12-13'),
(5, 6, 500000, '2025-12-14'),
(6, 6, 5150000, '2025-12-14');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_details`
--

CREATE TABLE `transaction_details` (
  `detailID` int(10) NOT NULL,
  `transactionID` int(10) NOT NULL,
  `productID` int(10) NOT NULL,
  `quantity` int(5) NOT NULL,
  `subtotal` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_details`
--

INSERT INTO `transaction_details` (`detailID`, `transactionID`, `productID`, `quantity`, `subtotal`) VALUES
(1, 1, 1, 1, 500000),
(2, 1, 5, 2, 200000),
(3, 2, 3, 1, 150000),
(4, 3, 1, 1, 500000),
(5, 3, 5, 2, 200000),
(6, 4, 4, 2, 1600000),
(7, 4, 1, 1, 500000),
(8, 4, 10, 4, 2400000),
(9, 5, 1, 1, 500000),
(10, 6, 4, 1, 800000),
(11, 6, 3, 8, 1200000),
(12, 6, 1, 3, 1500000),
(13, 6, 9, 3, 750000),
(14, 6, 10, 1, 600000),
(15, 6, 2, 1, 300000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int(10) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(40) NOT NULL,
  `password` varchar(255) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `dob` date NOT NULL,
  `role` varchar(6) NOT NULL DEFAULT 'Member'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `username`, `email`, `password`, `gender`, `dob`, `role`) VALUES
(1, 'George', 'admin.george@gmail.com', 'admin123', 'Male', '1990-01-01', 'Admin'),
(2, 'steven', 'steven.gunawan010@gmail.com', 'password123', 'Male', '1995-05-15', 'Member'),
(3, 'christopher devin', 'chris.dev@gmail.com', 'password123', 'Male', '1998-08-20', 'Member'),
(4, 'joshua', 'joshua.kartadi@gmail.com', 'password123', 'Male', '2005-06-01', 'Member'),
(5, 'Lavreaux', 'lavreaux@gmail.com', 'password123', 'Male', '2006-08-11', 'Member'),
(6, 'Furina', 'furina.fontaine@gmail.com', 'password123', 'Female', '2006-10-13', 'Member'),
(7, 'Arlecchino', 'arlecchino.fatui@gmail.com', 'password123', 'Female', '2003-08-22', 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `vendorID` int(10) NOT NULL,
  `vendorName` varchar(20) NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`vendorID`, `vendorName`, `location`) VALUES
(1, 'IKEA', 'Stockholm, Sweden'),
(2, 'Ashley Furniture', 'Wisconsin, USA'),
(3, 'Wayfair', 'Boston, USA'),
(4, 'West Elm', 'New York, USA'),
(5, 'Hampiness', 'Jakarta, Indonesia');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cartID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`productID`),
  ADD KEY `vendorID` (`vendorID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD PRIMARY KEY (`detailID`),
  ADD KEY `transactionID` (`transactionID`),
  ADD KEY `productID` (`productID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`vendorID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cartID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `productID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaction_details`
--
ALTER TABLE `transaction_details`
  MODIFY `detailID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `vendorID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`vendorID`) REFERENCES `vendors` (`vendorID`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `transaction_details`
--
ALTER TABLE `transaction_details`
  ADD CONSTRAINT `transaction_details_ibfk_1` FOREIGN KEY (`transactionID`) REFERENCES `transactions` (`transactionID`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaction_details_ibfk_2` FOREIGN KEY (`productID`) REFERENCES `products` (`productID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
