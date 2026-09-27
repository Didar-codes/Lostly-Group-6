-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 03, 2026 at 08:25 AM
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
-- Database: `lostly`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `ID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Type` enum('Lost','Found') NOT NULL,
  `ItemName` varchar(100) NOT NULL,
  `Category` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL,
  `Division` varchar(50) NOT NULL,
  `District` varchar(50) NOT NULL,
  `EventDate` date NOT NULL,
  `ImagePath` varchar(255) DEFAULT NULL,
  `Status` enum('Active','Resolved') NOT NULL DEFAULT 'Active',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`ID`, `UserID`, `Type`, `ItemName`, `Category`, `Description`, `Division`, `District`, `EventDate`, `ImagePath`, `Status`, `CreatedAt`) VALUES
(5, 3, 'Lost', 'Mobile', 'Electronics', 'ADFS', 'Dhaka', 'Dhaka', '2026-08-28', 'uploads/items/item_6a9194451a9007.17548928.jpg', 'Resolved', '2026-08-28 13:59:33'),
(6, 4, 'Lost', 'Tom', 'Pets', 'Dekhte bhodro', 'Dhaka', 'Dhaka', '2026-09-01', 'uploads/items/item_6a9912ac22b8e4.01675223.jpg', 'Active', '2026-08-31 18:40:29');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `ID` int(11) NOT NULL,
  `Username` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Phone` varchar(20) NOT NULL,
  `Division` varchar(50) NOT NULL,
  `District` varchar(100) NOT NULL,
  `Password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`ID`, `Username`, `Email`, `Phone`, `Division`, `District`, `Password`) VALUES
(1, 'Didar', 'thisisshaifulislam@gmail.com', '01911031925', 'Dhaka', 'Mirpur', '123123'),
(3, 'dadsa', 'minditblablabla444@gmail.com', '191103', 'Dhaka', 'Dhaka', '$2y$10$mLInawIJpom0798OtHkSQeUoU9Qje42afzQ4BqBK4hQP6bRZa0X7a'),
(4, 'faah', 'asdasdad@gmail.com', '123123123123', 'Dhaka', 'Dhaka', '$2y$10$ncNzQ81qT1R/CEMnT/G1lejEuBUvl0evXeSRGeHBX3RJkR4wi0euS');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `fk_items_user` (`UserID`),
  ADD KEY `idx_items_division` (`Division`),
  ADD KEY `idx_items_type_status` (`Type`,`Status`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_items_user` FOREIGN KEY (`UserID`) REFERENCES `users` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
