-- phpMyAdmin SQL Dump
-- version 4.6.6deb4
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 27, 2018 at 06:26 PM
-- Server version: 10.1.23-MariaDB-9+deb9u1
-- PHP Version: 7.0.30-0+deb9u1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fcc_amateur`
--

-- --------------------------------------------------------

--
-- Table structure for table `am`
--

CREATE TABLE `am` (
  `fccid` int(11) NOT NULL,
  `callsign` varchar(8) NOT NULL,
  `class` varchar(1) DEFAULT NULL,
  `col4` varchar(1) DEFAULT NULL,
  `col5` varchar(2) DEFAULT NULL,
  `col6` varchar(3) DEFAULT NULL,
  `former_call` varchar(8) DEFAULT NULL,
  `former_class` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `en`
--

CREATE TABLE `en` (
  `fccid` int(11) NOT NULL,
  `callsign` varchar(8) NOT NULL,
  `full_name` varchar(32) DEFAULT NULL,
  `first` varchar(20) DEFAULT NULL,
  `middle` varchar(1) DEFAULT NULL,
  `last` varchar(20) DEFAULT NULL,
  `address1` varchar(32) DEFAULT NULL,
  `city` varchar(20) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `hd`
--

CREATE TABLE `hd` (
  `fccid` int(11) NOT NULL,
  `callsign` varchar(8) NOT NULL,
  `status` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `am`
--
ALTER TABLE `am`
  ADD PRIMARY KEY (`fccid`),
  ADD KEY `idx_callsign` (`callsign`),
  ADD KEY `idx_class` (`class`);

--
-- Indexes for table `en`
--
ALTER TABLE `en`
  ADD PRIMARY KEY (`fccid`),
  ADD KEY `idx_zip` (`zip`),
  ADD KEY `idx_callsign` (`callsign`);

--
-- Indexes for table `hd`
--
ALTER TABLE `hd`
  ADD PRIMARY KEY (`fccid`),
  ADD KEY `idx_callsign` (`callsign`),
  ADD KEY `idx_status` (`status`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
