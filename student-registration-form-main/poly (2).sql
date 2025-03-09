-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2024 at 04:03 PM
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
-- Database: `poly_info`
--

-- --------------------------------------------------------

--
-- Table structure for table `poly`
--

CREATE TABLE `poly` (
  `token` varchar(255) NOT NULL,
  `aadhaar` varchar(12) NOT NULL,
  `name` varchar(255) NOT NULL,
  `fatherName` varchar(255) NOT NULL,
  `institutename` varchar(255) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `email` varchar(255) NOT NULL,
  `rollno` varchar(255) NOT NULL,
  `dob` date NOT NULL,
  `year` varchar(12) NOT NULL,
  `branch` varchar(12) NOT NULL,
  `Photo` varchar(255) DEFAULT NULL,
  `signImage` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poly`
--

INSERT INTO `poly` (`token`, `aadhaar`, `name`, `fatherName`, `institutename`, `phone`, `email`, `rollno`, `dob`, `year`, `branch`, `Photo`, `signImage`) VALUES
('1', '555555555555', 'jon', 'thedon', 'dev bhoomi', '7412558656', 'nitinsainikdl@g', '8564555666566', '2024-02-08', 'First', 'cse', 'data/photo/photo_2023-11-19_12-42-59.jpg', 'data/sign/photo_2023-11-19_12-42-59.jpg'),
('2', '741852963741', 'Nitin saini', 'Ramavtar saini', 'Dev Bhoomi group of institutions', '9368776367', 'nitinsainikdl@outlook.com', '2105020100031', '2004-10-02', 'Third', 'cse', 'data/photo/photo_2023-11-19_12-42-59.jpg', 'data/sign/sing.jpg');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `poly`
--
ALTER TABLE `poly`
  ADD PRIMARY KEY (`aadhaar`),
  ADD UNIQUE KEY `token` (`token`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
