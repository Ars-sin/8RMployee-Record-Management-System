-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 14, 2025 at 12:49 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u987478351_8rm_admin`
--

-- --------------------------------------------------------

--
-- Table structure for table `emergency_contacts`
--

CREATE TABLE `emergency_contacts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_no` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `emergency_contacts`
--

INSERT INTO `emergency_contacts` (`id`, `employee_id`, `name`, `address`, `contact_no`) VALUES
(24, 27, 'jkjk', 'khjk', '0989088'),
(25, 28, 'Meowo', 'Meow City', '09683858686'),
(26, 21, 'Meowo', 'Meow City', '09683858686'),
(27, 40, 'Meowo', 'Meow City', '09683858686'),
(28, 10, 'Erikson', 'naga cebu', '09134675948'),
(29, 90, 'Erikson', 'naga cebu', '09683858622'),
(30, 26, 'qwq', 'qw', 'qw`12'),
(31, 3, 'Meowo', 'Meow City', '09683858686'),
(32, 23, 'Meowo', 'be', '0989088'),
(33, 22, 'Meow', 'Cebu, Naga City', '09683858622'),
(35, 0, 'Dog', 'City Of Naga', '968385868'),
(36, 12345, 'Meow', 'Cebu, Naga City', '09134675948'),
(37, 24, 'Meowo', 'naga cebu', '09683858686'),
(38, 25, 'Meow', 'khjk', '09683858622'),
(40, 5, 'Meow', 'City Of Naga', '968385868'),
(41, 0, 'MELW', 'Meoww City', '0956786789'),
(42, 25121, 'Meow', 'Cebu, Naga City', '09134675948'),
(43, 0, 'ehe', 'Meow City', '09879675654'),
(44, 0, 'ahaaaaa', 'aha city', '09158673857'),
(45, 2, 'aha', 'aeaea', '09878765412'),
(46, 1, 'test', 'test', '0912312411'),
(47, 101, 'test1', 'twes1', '019181716151'),
(48, 111, 'Meow', 'Cebu, Naga City', '09134675948'),
(50, 1231, 'Meow', 'City Of Naga', '0968385868'),
(51, 18, 'Dog', 'City Of Naga', '0968385321'),
(52, 32, 'meow', 'Cebu', '098653264578'),
(53, 6, 'Charles AMbrad', 'urgello', '0917342567'),
(54, 77777, 'ambrad', 'sambag 2', '09177846801'),
(56, 191, 'Dog', 'Cebu, Naga City', '09134675948'),
(58, 9, 'Meowo', 'urgello', '09878765412'),
(60, 3122, 'Meow', 'Meow City', '09683858686'),
(61, 283, 'Meow', 'Cebu City', '09683858686'),
(63, 19, 'Meowooo', 'City Of Naga', '9683858621'),
(64, 33, 'Meow', 'City Of Naga', '968385868');

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `id` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `position` varchar(100) DEFAULT NULL,
  `date_hired` date DEFAULT NULL,
  `assigned_project` varchar(255) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sss_no` varchar(25) DEFAULT NULL,
  `pagibig_no` varchar(25) DEFAULT NULL,
  `philhealth_no` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`id`, `email`, `first_name`, `last_name`, `address`, `contact_no`, `birth_date`, `status`, `position`, `date_hired`, `assigned_project`, `daily_rate`, `created_at`, `sss_no`, `pagibig_no`, `philhealth_no`) VALUES
(1, 'arslandef1@gmail.com', 'test', 'test', 'test', '9878765641', '2025-09-28', 'Active', 'Engineer', '2025-09-28', 'suw', 2311.00, '2025-09-28 11:14:02', '1', '2', '3'),
(2, NULL, 'p', 'eq', 'qe', '9898787676', '2025-09-28', 'Active', 'Engineer', '2025-09-28', 'swu', 131.00, '2025-09-28 11:10:50', '1', '2', '3'),
(3, NULL, 'Laurence', 'Monares', 'Cebu City', '9683858684', '2016-01-23', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:49', '1111-11111-1111', '1111-11111-1111', '1111-11111-1111'),
(5, 'seyu.jerez.swu@phinmaed.com', 'Sean Michael', 'Jerez', 'Cebu, Kalunasan', '9876543210', '2025-09-26', 'Active', 'Project Engineer', '2025-09-26', 'BDO', 10000.00, '2025-09-26 11:46:45', '0111-111110-1010', '0111-111110-1019', '1111-11111-1111'),
(6, 'arslandef1@gmail.com', 'Horry', 'Belia', 'urgello', '9876543211', '2025-09-03', 'Active', 'Project Engineer', '2025-10-05', 'BDO', 1212.00, '2025-10-05 13:55:10', '1', '2', '4'),
(9, 'marykristinecaneda@gmail.com', 'Meow', 'Monares', 'Meow', '1958674759', '2025-10-04', 'Active', 'Lead-Man Technician', '2025-10-04', 'SWU', 1200.00, '2025-10-04 09:59:29', '44', '33', '22'),
(10, NULL, 'Reynier', 'Canaveral', 'Naga Cebu', '9683858684', '2025-09-20', 'Active', NULL, NULL, NULL, NULL, '2025-09-29 06:29:05', '1111-11111-1111', '1111-11111-1111', '1111-11111-1111'),
(15, NULL, 'Mary', 'Kristine Caneda', 'Bohol, Ubay', '+639683858680', '2002-12-30', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:42', '1111-11111-1112', '1111-11111-1111', '1111-11111-1113'),
(16, NULL, 'John', 'Laurence Monares', 'Cebu City', '+639683858642', '2004-08-18', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:44', '1111-11111-1100', '1111-11111-1100', '1111-11111-1100'),
(17, NULL, 'Reynier', 'Canaveral', 'Cebu City', '9125637849', '2006-09-01', 'Active', '', '0000-00-00', NULL, NULL, '2025-09-26 11:37:56', '2131-12314-1231', '2131-12314-1223', '2131-12314-1341'),
(18, 'arslandef1@gmail.com', 'Reynier', 'Canaveral', 'Cebu City', '9683858684', '2006-01-11', 'Active', 'Project In-Charge', '2025-09-28', 'SWU', 100.00, '2025-09-26 11:38:00', '1111-11111-1111', '1111-11111-1111', '1111-11111-1111'),
(19, 'arslandef1@gmail.com', 'Charles', 'Ambrad', 'Cebu City', '9683858684', '2011-03-02', 'Active', 'Project In-Charge', '2025-10-05', 'BPI', 1212.00, '2025-10-05 13:41:14', '1111-11111-1111', '1111-11111-1111', '1111-11111-1111'),
(20, NULL, 'Reynier', 'Canaveral', 'Cebu City', '9683858642', '2025-03-04', 'Active', 'Project Engineer', '2025-10-05', '0', 1000.00, '2025-09-25 14:43:28', '1111-11111-1111', '1111-11111-1111', '1111-11111-1111'),
(21, 'arslandef1@gmail.com', 'destura', 'jimuel', 'cebu', '1012634511', '0000-00-00', 'Active', 'Technician', '2025-10-07', 'SWWU', 100.00, '2025-09-25 14:48:40', '1111', '1111', '1111'),
(22, NULL, 'Meow', 'ehe', 'Meow City', '9134785624', '2024-02-04', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:35', '1314-131123-1231', '1234-412312-1231', '1235-623465-3421'),
(23, NULL, 'Kune', 'ehe', 'Meow City', '9458154785', '2016-04-07', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:43:32', '1111-111111-1111', '1111-111111-1111', '1111-111111-1111'),
(24, NULL, 'Klumsy', 'Monares', 'Meow City ', '9875641236', '2022-03-03', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:47', '2222-22222-2222', '2222-222222-2222', '2222-222222-2222'),
(25, NULL, 'Meow', 'Monares', 'Meow', '3958674759', '2023-02-03', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:52', '1', '1', '1'),
(26, 'arslandef1@gmail.com', 'ewewew', 'ewewe', 'wew', '2345678901', '2023-04-05', 'Active', 'Project In-Charge', '0000-00-00', 'BDO', 1313.00, '2025-09-25 14:48:38', '1111-11111-1111', '0111-111110-1010', '0111-111110-1010'),
(27, NULL, 'zxc', 'ehe', 'zxczxc', '9683858684', '2021-03-05', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:37', '78879', '890', '90809'),
(32, NULL, 'test e', 'test3', 'test city', '9865838462', '2025-09-29', 'Active', 'Engineer', '2025-09-29', 'seu', 100.00, '2025-09-29 00:43:02', '5', '5', '4'),
(33, 'arslandef1@gmail.com', 'Essss', 'Ambrad', 'Meow', '9878765641', '2025-10-04', 'Active', 'Project In-Charge', '2025-10-05', 'BDO', 1212.00, '2025-10-05 13:51:45', '1', '112', '1232'),
(40, NULL, 'Laurence', 'Monares', 'Cebu City', '9683858684', '2012-01-17', 'Active', NULL, NULL, NULL, NULL, '2025-09-25 14:48:51', '1111-11111-1100', '1111-11111-1100', '1111-11111-1100'),
(90, 'arslandef1@gmail.com', 'meeee', 'Canaveral', 'Cebu City', '9683858684', '2025-09-18', 'Active', 'Liaison Officer', '2025-10-05', 'BDO', 12121.00, '2025-10-05 13:51:50', '0111-111110-1016', '0111-111110-1111', '0111-111110-1111'),
(101, NULL, 'test1', 'test1', 'test1', '1818181818', '2025-09-28', 'Active', 'Engineer', '2025-09-28', 'swu', 131.00, '2025-09-28 11:16:25', '2', '1', '3'),
(111, NULL, 'aha', 'hala', 'aha  city', '9878765641', '2025-09-28', 'Active', 'Liaison Officer', '2025-09-28', 'BDO', 100.00, '2025-09-28 09:58:12', '1', '2', '3'),
(191, NULL, 'Meow', 'SWU', 'Cebu City', '1958674759', '2025-09-30', 'Active', 'Foreman', '2025-09-30', 'SWU', 166.00, '2025-09-30 10:33:18', '1', '2', '3'),
(241, 'arslandef1@gmail.com', 'Meow', 'Canaveral', 'Cebu City', '9683858684', '2025-10-03', 'Active', NULL, NULL, NULL, NULL, '2025-10-03 08:23:36', '3', '4', '2'),
(283, 'arslandef1@gmail.com', 'Cactus', 'Plant', 'Cebu City', '9683858686', '2025-10-05', 'Active', 'Engineer', '2025-10-05', 'SWU', 1000.00, '2025-10-05 12:54:11', '2', '3', '6'),
(1231, NULL, 'Meow', 'eheee', 'Cebu City', '9898765453', '2025-09-28', 'Active', 'Engineer', '2025-09-28', 'SWU', 100.00, '2025-09-28 04:24:49', '3', '2', '1'),
(3122, 'arslandef1@gmail.com', 'zzz', 'zzz', 'Meow', '9878765641', '2025-10-08', 'Active', 'Foreman', '2025-10-04', 'swu', 122.00, '2025-10-04 10:15:05', '131', '1231', '1231'),
(9681, NULL, 'Meow', 'Meow', 'Meow City', '7857378646', '2025-09-26', 'Active', 'Engineer', '2025-09-26', 'SWU', 800.00, '2025-09-26 12:28:03', '1', '11', ''),
(12345, 'arslandef1@gmail.com', 'Loren', 'Lorelai', 'Cebu City', '9987654321', '2025-09-26', 'Active', 'Lead-Man Technician', '2025-10-07', 'SWU', 900.00, '2025-09-25 14:48:45', '54', '45', '34'),
(25121, NULL, 'Meow', 'Monares', 'Meow', '0958674759', '2025-09-28', 'Active', 'Supervisor', '2025-09-28', 'swu', 190.00, '2025-09-27 23:41:19', '1', '2', '2'),
(67890, 'yoonya000@gmail.com', 'Maria', 'Clara', 'Cebu City', '9683858684', '2025-10-23', 'Active', NULL, NULL, NULL, NULL, '2025-10-05 14:35:01', '1111-11111-1111', '0111-111110-1010', '0111-111110-1010'),
(77777, 'arslandef1@gmail.com', 'Horry', 'Belia', 'urgello', '9876543211', '2025-09-04', 'Active', 'Office Manager', '2025-10-05', 'BDO', 1212.00, '2025-10-05 13:51:57', '0111-111110-1010', '121', '2121');

-- --------------------------------------------------------

--
-- Table structure for table `employee_archive`
--

CREATE TABLE `employee_archive` (
  `id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `position` varchar(100) NOT NULL,
  `date_hired` date NOT NULL,
  `assigned_project` varchar(255) DEFAULT NULL,
  `daily_rate` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `sss_no` varchar(25) DEFAULT NULL,
  `pagibig_no` varchar(25) DEFAULT NULL,
  `philhealth_no` varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_archive`
--

INSERT INTO `employee_archive` (`id`, `first_name`, `last_name`, `email`, `address`, `contact_no`, `birth_date`, `status`, `position`, `date_hired`, `assigned_project`, `daily_rate`, `created_at`, `sss_no`, `pagibig_no`, `philhealth_no`) VALUES
(55555, 'Joanne', 'Canaveral', 'arslandef1@gmail.com', 'urgello', '9345678999', '2023-06-29', 'Active', 'Technician', '2025-10-05', '0', 1313.00, '2025-09-29 06:29:48', '2222-22222-2222', '2222-222222-2222', '2222-222222-2222'),
(98765, 'Canaveral ', 'Demingoy', NULL, 'Mayana City of Naga Cebu', '9876543212', '2006-02-11', 'Active', 'Lead-Man Technician', '2025-09-29', '0', 100.00, '2025-09-25 14:43:30', '2', '2', '2');

-- --------------------------------------------------------

--
-- Table structure for table `modification_logs`
--

CREATE TABLE `modification_logs` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modification_logs`
--

INSERT INTO `modification_logs` (`id`, `user_name`, `action_type`, `description`, `created_at`) VALUES
(37, 'Admin', 'Add Employee', 'Added new employee: Mary Kristine Caneda', '2025-08-31 11:45:17'),
(38, 'Admin', 'Add Remittance', 'Added Pag-IBIG remittance for Mary Kristine Caneda for August 2025. Amount: 1000.00, Status: Paid.', '2025-08-31 11:48:52'),
(39, 'Admin', 'Add Remittance', 'Added PhilHealth remittance for Mary Kristine Caneda for August 2025. Amount: 990.00, Status: Unpaid.', '2025-08-31 11:49:02'),
(40, 'Admin', 'Add Employee', 'Added new employee: John Laurence Monares', '2025-08-31 11:59:03'),
(41, 'Admin', 'Add Employee', 'Added new employee: Reynier Canaveral', '2025-09-01 02:51:52'),
(42, 'Admin', 'Add Remittance', 'Added Pag-IBIG remittance for Reynier Canaveral for September 2025. Amount: 800.00, Status: Paid.', '2025-09-01 02:52:17'),
(43, 'Admin', 'Add Remittance', 'Added Pag-IBIG remittance for Mary Kristine Caneda for September 2025. Amount: 500.00, Status: Paid.', '2025-09-02 07:18:30'),
(44, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-02 07:21:07'),
(45, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-02 07:21:07'),
(46, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-02 07:21:07'),
(47, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-02 07:21:07'),
(48, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-02 07:21:07'),
(49, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-02 07:21:07'),
(50, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-05 03:01:23'),
(51, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 400.00, New Status: Paid.', '2025-09-05 03:01:35'),
(52, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 9400.00, New Status: Paid.', '2025-09-05 03:04:41'),
(53, 'Admin', 'Add Remittance', 'Added Pag-IBIG remittance for Mary Kristine Caneda for February 2024. Amount: 110.00, Status: Paid.', '2025-09-05 03:48:03'),
(54, 'Admin', 'Add Remittance', 'Added Pag-IBIG remittance for Reynier Canaveral for February 2024. Amount: 0.00, Status: Unpaid.', '2025-09-05 03:48:06'),
(55, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 9400.00, New Status: Paid.', '2025-09-05 03:51:57'),
(56, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 800.00, New Status: Paid.', '2025-09-05 03:52:10'),
(57, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 9800.00, New Status: Paid.', '2025-09-05 03:58:09'),
(58, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 9800.00, New Status: Paid.', '2025-09-05 03:58:09'),
(59, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 9800.00, New Status: Paid.', '2025-09-05 03:58:09'),
(60, 'Admin', 'Add Remittance', 'Added Pag-IBIG remittance for Mary Kristine Caneda for June 2022. Amount: 0.00, Status: Unpaid.', '2025-09-05 03:59:07'),
(61, 'Admin', 'Update Remittance', 'Updated Pag-IBIG remittance for Mary Kristine Caneda for September 2025. New Amount: 600.00, New Status: Paid.', '2025-09-05 04:05:01'),
(62, 'Admin', 'Add Employee', 'Added new employee: Reynier Canaveral', '2025-09-05 04:16:23'),
(63, 'Admin', 'Update Employee', 'Updated details for employee: John Laurence Monares (ID: 16)', '2025-09-05 04:20:12'),
(64, 'Admin', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 17)', '2025-09-05 04:22:39'),
(65, 'Admin', 'Update Employee', 'Updated details for employee: John Laurence Monares (ID: 16)', '2025-09-11 00:42:57'),
(66, 'Admin', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 18)', '2025-09-11 03:36:04'),
(67, 'Admin', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 18)', '2025-09-11 04:05:37'),
(68, 'Admin', 'Add Employee', 'Added new employee: Charles Ambrad', '2025-09-15 03:20:56'),
(69, 'Admin', 'Add Employee', 'Added new employee: Reynier Canaveral', '2025-09-15 03:28:16'),
(70, 'Admin', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 17)', '2025-09-16 03:31:22'),
(71, 'Admin', 'Add Employee', 'Added new employee: destura jimuel', '2025-09-16 05:35:47'),
(72, 'Admin', 'Add Employee', 'Added new employee: Meow', '2025-09-16 05:56:51'),
(73, 'Admin', 'Add Employee', 'Added new employee: Kune', '2025-09-16 05:58:59'),
(74, 'Admin', 'Add Employee', 'Added new employee: Klumsy ', '2025-09-16 06:00:28'),
(75, 'Admin', 'Add Employee', 'Added new employee: Meow', '2025-09-16 06:07:20'),
(76, 'Admin', 'Add Employee', 'Added new employee: ewewew', '2025-09-16 09:08:15'),
(77, 'Admin', 'Add Employee', 'Added new employee: zxc', '2025-09-16 10:29:51'),
(78, 'Admin', 'Add Employee', 'Added new employee: Reynier Canaveral', '2025-09-17 08:48:26'),
(79, 'Admin', 'Update Employee', 'Updated details for employee: destura jimuel (ID: 21)', '2025-09-17 09:32:23'),
(80, 'Admin', 'Update Employee', 'Updated details for employee: destura jimuel (ID: 21)', '2025-09-17 09:42:25'),
(81, 'System', 'Archive Employee', 'Archived employee with ID: 40', '2025-09-17 14:35:22'),
(82, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 40', '2025-09-17 14:35:25'),
(83, 'System', 'Add Remittance', 'Added 3 remittance(s) for Mary Kristine Caneda for the period of September 2025, totaling 7,100.00', '2025-09-17 14:42:48'),
(84, 'System', 'Add Employee', 'Added new employee: meeee Canaveral (ID: 90)', '2025-09-18 04:07:51'),
(85, 'System', 'Archive Employee', 'Archived employee with ID: 19', '2025-09-18 04:08:01'),
(86, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 26', '2025-09-18 04:08:24'),
(87, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 24', '2025-09-18 04:08:31'),
(88, 'System', 'Add Remittance', 'Added 1 remittance(s) for Laurence Monares for the period of December 2025, totaling 100.00', '2025-09-18 04:17:20'),
(89, 'System', 'Add Remittance', 'Added 3 remittance(s) for Mary Kristine Caneda for the period of September 2025, totaling 15,000.00', '2025-09-18 21:09:39'),
(90, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 23', '2025-09-19 01:17:48'),
(91, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 25', '2025-09-19 01:17:50'),
(92, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 22', '2025-09-19 01:17:51'),
(93, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 27', '2025-09-19 01:17:52'),
(94, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 19', '2025-09-19 01:17:54'),
(95, 'System', 'Update Employee', 'Updated details for employee: ewewew ewewe (ID: 26)', '2025-09-19 01:18:27'),
(96, 'System', 'Update Employee', 'Updated details for employee: destura jimuel (ID: 21)', '2025-09-19 01:28:35'),
(97, 'System', 'Archive Employee', 'Archived employee with ID: 27', '2025-09-23 02:17:00'),
(98, 'System', 'Archive Employee', 'Archived employee with ID: 24', '2025-09-23 05:56:00'),
(99, 'System', 'Archive Employee', 'Archived employee with ID: 21', '2025-09-23 06:20:54'),
(100, 'System', 'Add Employee', 'Added new employee: Laurence Monares (ID: 00003)', '2025-09-23 14:27:54'),
(101, 'System', 'Archive Employee', 'Archived employee with ID: 26', '2025-09-23 14:30:48'),
(102, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 10', '2025-09-23 14:30:56'),
(103, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 90', '2025-09-23 14:30:59'),
(104, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 17', '2025-09-23 14:31:01'),
(105, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 26', '2025-09-23 14:31:03'),
(106, 'System', 'Update Employee', 'Updated details for employee: Kune ehe (ID: 23)', '2025-09-23 14:35:52'),
(107, 'System', 'Update Employee', 'Updated details for employee: Meow ehe (ID: 22)', '2025-09-23 14:36:15'),
(108, 'System', 'Archive Employee', 'Archived employee with ID: 25', '2025-09-23 16:42:28'),
(109, 'System', 'Add Employee', 'Added new employee: Canaveral  Demingoy (ID: 098765)', '2025-09-23 16:46:15'),
(110, 'System', 'Archive Employee', 'Archived employee with ID: 98765', '2025-09-23 16:46:45'),
(111, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 98765', '2025-09-23 16:47:07'),
(112, 'System', 'Update Employee', 'Updated details for employee: Canaveral  Demingoy (ID: 98765)', '2025-09-23 16:49:12'),
(113, 'System', 'Update Employee', 'Updated details for employee: Canaveral  Demingoy (ID: 98765)', '2025-09-23 16:50:48'),
(114, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 19', '2025-09-23 17:17:58'),
(115, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 25', '2025-09-24 07:02:43'),
(116, 'System', 'Add Remittance', 'Added 1 remittance(s) for Klumsy  for the period of September 2025, totaling 1,000.00', '2025-09-24 08:57:53'),
(117, 'System', 'Add Remittance', 'Added 1 remittance(s) for Canaveral  Demingoy for the period of September 2025, totaling 50,000.00', '2025-09-24 08:58:26'),
(118, 'System', 'Add Remittance', 'Added 1 remittance(s) for Canaveral  Demingoy for the period of September 2025, totaling 50,000.00', '2025-09-24 08:58:28'),
(119, 'System', 'Add Remittance', 'Added 3 remittance(s) for Laurence Monares for the period of September 2025, totaling 300,000.00', '2025-09-24 08:59:50'),
(120, 'System', 'Add Remittance', 'Added 3 remittance(s) for Klumsy  for the period of September 2025, totaling 321,211.00', '2025-09-24 09:29:51'),
(121, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 27', '2025-09-24 09:30:29'),
(122, 'System', 'Archive Employee', 'Archived employee with ID: 24', '2025-09-24 09:30:34'),
(123, 'System', 'Update Employee', 'Updated details for employee: Canaveral  Demingoy (ID: )', '2025-09-24 17:37:33'),
(124, 'System', 'Add Employee', 'Added new employee: Loren Lorelai (ID: 12345)', '2025-09-24 18:24:58'),
(125, 'System', 'Update Employee', 'Updated details for employee: Klumsy Monares (ID: 24)', '2025-09-25 06:56:00'),
(126, 'System', 'Update Employee', 'Updated details for employee: Meow Monares (ID: 25)', '2025-09-25 06:58:48'),
(127, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 90', '2025-09-25 07:05:26'),
(128, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 22', '2025-09-25 07:05:34'),
(129, 'System', 'Add Remittance', 'Added 1 remittance(s) for Meow ehe for the period of September 2025, totaling 2,313,123.00', '2025-09-25 07:06:29'),
(130, 'System', 'Archive Employee', 'Archived employee with ID: 22', '2025-09-25 07:06:47'),
(131, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 22', '2025-09-25 07:06:57'),
(132, 'System', 'Update Employee', 'Updated details for employee: zxc ehe (ID: 27)', '2025-09-25 07:56:45'),
(133, 'System', 'Add Remittance', 'Added 1 remittance(s) for Laurence Monares for the period of September 2025, totaling 99,999.00', '2025-09-25 14:15:21'),
(134, 'System', 'Add Remittance', 'Added 1 remittance(s) for Charles Ambrad for the period of September 2025, totaling 7,878.00', '2025-09-25 14:15:44'),
(135, 'System', 'Archive Employee', 'Archived employee with ID: 19', '2025-09-25 14:16:00'),
(136, 'System', 'Archive Employee', 'Archived employee with ID: 90', '2025-09-25 14:16:02'),
(137, 'System', 'Archive Employee', 'Archived employee with ID: 10', '2025-09-25 14:16:03'),
(138, 'System', 'Archive Employee', 'Archived employee with ID: 17', '2025-09-25 14:16:05'),
(139, 'System', 'Archive Employee', 'Archived employee with ID: 98765', '2025-09-25 14:16:07'),
(140, 'System', 'Archive Employee', 'Archived employee with ID: 22', '2025-09-25 14:16:09'),
(141, 'System', 'Archive Employee', 'Archived employee with ID: 27', '2025-09-25 14:16:11'),
(142, 'System', 'Archive Employee', 'Archived employee with ID: 26', '2025-09-25 14:16:13'),
(143, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 19', '2025-09-25 14:16:19'),
(144, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 90', '2025-09-25 14:16:21'),
(145, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 10', '2025-09-25 14:16:22'),
(146, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 17', '2025-09-25 14:16:24'),
(147, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 18', '2025-09-25 14:16:26'),
(148, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 20', '2025-09-25 14:38:02'),
(149, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 98765', '2025-09-25 14:38:03'),
(150, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 23', '2025-09-25 14:38:05'),
(151, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 22', '2025-09-25 14:38:06'),
(152, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 27', '2025-09-25 14:38:08'),
(153, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 26', '2025-09-25 14:38:10'),
(154, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 15', '2025-09-25 14:38:11'),
(155, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 16', '2025-09-25 14:38:13'),
(156, 'System', 'Archive Employee', 'Archived employee with ID: 19', '2025-09-25 14:42:17'),
(157, 'System', 'Archive Employee', 'Archived employee with ID: 90', '2025-09-25 14:42:19'),
(158, 'System', 'Archive Employee', 'Archived employee with ID: 10', '2025-09-25 14:42:21'),
(159, 'System', 'Archive Employee', 'Archived employee with ID: 17', '2025-09-25 14:42:23'),
(160, 'System', 'Archive Employee', 'Archived employee with ID: 18', '2025-09-25 14:42:25'),
(161, 'System', 'Archive Employee', 'Archived employee with ID: 20', '2025-09-25 14:42:27'),
(162, 'System', 'Archive Employee', 'Archived employee with ID: 98765', '2025-09-25 14:42:29'),
(163, 'System', 'Archive Employee', 'Archived employee with ID: 23', '2025-09-25 14:42:31'),
(164, 'System', 'Archive Employee', 'Archived employee with ID: 22', '2025-09-25 14:42:33'),
(165, 'System', 'Archive Employee', 'Archived employee with ID: 27', '2025-09-25 14:42:35'),
(166, 'System', 'Archive Employee', 'Archived employee with ID: 26', '2025-09-25 14:42:37'),
(167, 'System', 'Archive Employee', 'Archived employee with ID: 21', '2025-09-25 14:42:38'),
(168, 'System', 'Archive Employee', 'Archived employee with ID: 15', '2025-09-25 14:42:41'),
(169, 'System', 'Archive Employee', 'Archived employee with ID: 16', '2025-09-25 14:42:43'),
(170, 'System', 'Archive Employee', 'Archived employee with ID: 12345', '2025-09-25 14:42:45'),
(171, 'System', 'Archive Employee', 'Archived employee with ID: 24', '2025-09-25 14:42:47'),
(172, 'System', 'Archive Employee', 'Archived employee with ID: 3', '2025-09-25 14:42:49'),
(173, 'System', 'Archive Employee', 'Archived employee with ID: 40', '2025-09-25 14:42:50'),
(174, 'System', 'Archive Employee', 'Archived employee with ID: 25', '2025-09-25 14:42:52'),
(175, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 19', '2025-09-25 14:43:20'),
(176, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 90', '2025-09-25 14:43:22'),
(177, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 10', '2025-09-25 14:43:24'),
(178, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 17', '2025-09-25 14:43:25'),
(179, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 18', '2025-09-25 14:43:27'),
(180, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 20', '2025-09-25 14:43:28'),
(181, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 98765', '2025-09-25 14:43:30'),
(182, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 23', '2025-09-25 14:43:32'),
(183, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 22', '2025-09-25 14:48:35'),
(184, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 27', '2025-09-25 14:48:37'),
(185, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 26', '2025-09-25 14:48:38'),
(186, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 21', '2025-09-25 14:48:40'),
(187, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 15', '2025-09-25 14:48:42'),
(188, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 16', '2025-09-25 14:48:44'),
(189, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 12345', '2025-09-25 14:48:45'),
(190, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 24', '2025-09-25 14:48:47'),
(191, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 3', '2025-09-25 14:48:49'),
(192, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 40', '2025-09-25 14:48:51'),
(193, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 25', '2025-09-25 14:48:52'),
(194, 'System', 'Archive Employee', 'Archived employee with ID: 19', '2025-09-26 06:32:00'),
(195, 'System', 'Archive Employee', 'Archived employee with ID: 90', '2025-09-26 06:32:02'),
(196, 'System', 'Archive Employee', 'Archived employee with ID: 10', '2025-09-26 11:37:52'),
(197, 'System', 'Archive Employee', 'Archived employee with ID: 17', '2025-09-26 11:37:56'),
(198, 'System', 'Archive Employee', 'Archived employee with ID: 18', '2025-09-26 11:38:00'),
(199, 'System', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 20)', '2025-09-26 11:40:13'),
(200, 'System', 'Add Employee', 'Added new employee: Sean Michael Jerez (ID: 00005)', '2025-09-26 11:46:45'),
(201, 'System', 'Add Employee', 'Added new employee: Meow Monares (ID: 25121)', '2025-09-27 23:41:19'),
(202, 'System', 'Update Employee', 'Updated details for employee: aha hala (ID: 111)', '2025-09-28 11:44:53'),
(203, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:35:59'),
(204, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:36:22'),
(205, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:36:40'),
(206, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:41:05'),
(207, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:46:37'),
(208, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:50:28'),
(209, 'System', 'Update Employee', 'Updated details for employee: Meow eheee (ID: 1231)', '2025-09-28 14:51:03'),
(210, 'System', 'Update Employee', 'Updated details for employee: Meow eheee (ID: 1231)', '2025-09-28 14:51:11'),
(211, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:51:59'),
(212, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:52:06'),
(213, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:53:24'),
(214, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 14:59:11'),
(215, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-09-28 15:02:43'),
(216, 'System', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 18)', '2025-09-28 15:03:13'),
(217, 'System', 'Add Employee', 'Added new employee: Horry Belia (ID: 00006)', '2025-09-29 05:24:19'),
(218, 'System', 'Add Employee', 'Added new employee: Horry Belia (ID: 77777)', '2025-09-29 05:25:30'),
(219, 'System', 'Archive Employee', 'Archived employee with ID: 77777', '2025-09-29 05:25:51'),
(220, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 77777', '2025-09-29 05:26:02'),
(221, 'System', 'Add Employee', 'Added new employee: Joanne Canaveral (ID: 55555)', '2025-09-29 06:23:04'),
(222, 'System', 'Update Employee', 'Updated details for employee: Joanne Canaveral (ID: 55555)', '2025-09-29 06:23:51'),
(223, 'System', 'Add Remittance', 'Added 1 remittance(s) for Joanne Canaveral for the period of October 2023, totaling 500.00', '2025-09-29 06:26:21'),
(224, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 10', '2025-09-29 06:29:05'),
(225, 'System', 'Archive Employee', 'Archived employee with ID: 55555', '2025-09-29 06:29:35'),
(226, 'System', 'Retrieve Employee', 'Retrieved employee with ID: 55555', '2025-09-29 06:29:48'),
(227, 'System', 'Add Remittance', 'Added 1 remittance(s) for Canaveral  Demingoy for the period of September 2025, totaling 900.00', '2025-09-29 07:03:14'),
(228, 'System', 'Update Employee', 'Updated details for employee: Canaveral  Demingoy (ID: 98765)', '2025-09-29 07:09:44'),
(229, 'System', 'Add Employee', 'Added new employee: Meow SWU (ID: 191)', '2025-09-30 10:33:18'),
(230, 'System', 'Archive Employee', 'Archived employee with ID: 77777', '2025-09-30 10:34:54'),
(231, 'System', 'Update Employee', 'Updated details for employee: Horry Belia (ID: 6)', '2025-09-30 11:51:41'),
(232, 'System', 'Add Remittance', 'Added 2 remittance(s) for 2 employee(s) for the period of October 2025, totaling ₱400.00 - Details: Joanne Canaveral (1 remittance(s), ₱200.00); ewewew ewewe (1 remittance(s), ₱200.00)', '2025-10-01 04:08:21'),
(233, 'System', 'Add Employee', 'Added new employee: Meow Canaveral (ID: 241)', '2025-10-03 06:04:23'),
(234, 'System', 'Archive Employee', 'Archived employee with ID: 6', '2025-10-03 06:05:10'),
(235, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-10-03 07:01:42'),
(236, 'System', 'Archive Employee', 'Archived employee with ID: 19', '2025-10-03 07:01:49'),
(237, 'System', 'Update Employee', 'Updated details for employee: Meow Canaveral (ID: 241)', '2025-10-03 07:14:56'),
(238, 'System', 'Update Employee', 'Updated details for employee: Meow Canaveral (ID: 241)', '2025-10-03 07:26:27'),
(239, 'System', 'Retrieve Employee', 'Retrieved employee: Charles Ambrad (ID: 19)', '2025-10-03 08:23:11'),
(240, 'System', 'Archive Employee', 'Archived employee: Meow Canaveral (ID: 241)', '2025-10-03 08:23:25'),
(241, 'System', 'Retrieve Employee', 'Retrieved employee: Meow Canaveral (ID: 241)', '2025-10-03 08:23:36'),
(242, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-10-03 08:35:37'),
(243, 'System', 'Add Remittance', 'Added 2 remittance(s) for 1 employee(s) for the period of October 2025, totaling ₱2,000.00 - Details: Charles Ambrad (2 remittance(s), ₱2,000.00) | Email notifications: 1 sent, 0 failed', '2025-10-03 08:36:00'),
(244, 'System', 'Add Remittance', 'Added 1 remittance(s) for 1 employee(s) for the period of October 2025, totaling ₱2,000.00 - Details: Charles Ambrad (1 remittance(s), ₱2,000.00) | Email notifications: 1 sent, 0 failed', '2025-10-03 08:37:33'),
(245, 'System', 'Add Employee', 'Added new employee: Meow Monares (ID: 00009)', '2025-10-04 09:59:29'),
(246, 'System', 'Update Employee', 'Updated details for employee: Meow Monares (ID: 9)', '2025-10-04 09:59:47'),
(247, 'System', 'Add Employee', 'Added new employee: Essss Ambrad (ID: 033)', '2025-10-04 10:11:54'),
(248, 'System', 'Add Employee', 'Added new employee: zzz zzz (ID: 3122)', '2025-10-04 10:15:05'),
(249, 'System', 'Add Employee', 'Added new employee: Maria Clara (ID: 67890)', '2025-10-05 13:09:33'),
(250, 'System', 'Archive Employee', 'Archived employee: Charles Ambrad (ID: 19)', '2025-10-05 13:12:01'),
(251, 'System', 'Archive Employee', 'Archived employee: Essss Ambrad (ID: 33)', '2025-10-05 13:12:07'),
(252, 'System', 'Add Remittance', 'Added 1 remittance(s) for 1 employee(s) for the period of October 2025, totaling ₱1,000.00 - Details: Maria Clara (1 remittance(s), ₱1,000.00) | Email notifications: 1 sent, 0 failed', '2025-10-05 13:29:14'),
(253, 'System', 'Update Employee', 'Updated details for employee: Joanne Canaveral (ID: 55555)', '2025-10-05 13:37:33'),
(254, 'System', 'Update Employee', 'Updated details for employee: Sean Michael Jerez (ID: 5)', '2025-10-05 13:37:59'),
(255, 'System', 'Update Employee', 'Updated details for employee: Sean Michael Jerez (ID: 5)', '2025-10-05 13:38:49'),
(256, 'System', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 18)', '2025-10-05 13:39:09'),
(257, 'System', 'Retrieve Employee', 'Retrieved employee: Charles Ambrad (ID: 19)', '2025-10-05 13:41:14'),
(258, 'System', 'Update Employee', 'Updated details for employee: Maria Clara (ID: 67890)', '2025-10-05 13:41:38'),
(259, 'System', 'Update Employee', 'Updated details for employee: Sean Michael Jerez (ID: 5)', '2025-10-05 13:42:05'),
(260, 'System', 'Retrieve Employee', 'Retrieved employee: Essss Ambrad (ID: 33)', '2025-10-05 13:51:45'),
(261, 'System', 'Retrieve Employee', 'Retrieved employee: meeee Canaveral (ID: 90)', '2025-10-05 13:51:50'),
(262, 'System', 'Retrieve Employee', 'Retrieved employee: Horry Belia (ID: 77777)', '2025-10-05 13:51:57'),
(263, 'System', 'Retrieve Employee', 'Retrieved employee: Horry Belia (ID: 6)', '2025-10-05 13:55:10'),
(264, 'System', 'Update Employee', 'Updated details for employee: ewewew ewewe (ID: 26)', '2025-10-05 13:58:54'),
(265, 'System', 'Add Remittance', 'Added 2 remittance(s) for 1 employee(s) for the period of October 2025, totaling ₱2,000.00 - Details: Essss Ambrad (2 remittance(s), ₱2,000.00) | Email notifications: 0 sent, 1 failed', '2025-10-05 14:21:36'),
(266, 'System', 'Add Remittance', 'Added 1 remittance(s) for 1 employee(s) for the period of October 2025, totaling ₱1,000.00 - Details: John Laurence Monares (1 remittance(s), ₱1,000.00) | Email notifications: 0 sent, 1 failed', '2025-10-05 14:25:43'),
(267, 'System', 'Archive Employee', 'Archived employee: Canaveral  Demingoy (ID: 98765)', '2025-10-05 14:33:11'),
(268, 'System', 'Archive Employee', 'Archived employee: Maria Clara (ID: 67890)', '2025-10-05 14:33:15'),
(269, 'System', 'Update Employee', 'Updated details for employee: Reynier Canaveral (ID: 20)', '2025-10-05 14:33:32'),
(270, 'System', 'Archive Employee', 'Archived employee: Reynier Canaveral (ID: 20)', '2025-10-05 14:33:36'),
(271, 'System', 'Archive Employee', 'Archived employee: Joanne Canaveral (ID: 55555)', '2025-10-05 14:34:53'),
(272, 'System', 'Retrieve Employee', 'Retrieved employee: Maria Clara (ID: 67890)', '2025-10-05 14:35:01'),
(273, 'System', 'Update Employee', 'Updated details for employee: Charles Ambrad (ID: 19)', '2025-10-05 14:35:27'),
(274, 'System', 'Update Employee', 'Updated details for employee: Essss Ambrad (ID: 33)', '2025-10-05 14:35:48'),
(275, 'System', 'Update Employee', 'Updated details for employee: Horry Belia (ID: 6)', '2025-10-05 14:36:02'),
(276, 'System', 'Update Employee', 'Updated details for employee: Horry Belia (ID: 77777)', '2025-10-05 14:36:19'),
(277, 'System', 'Update Employee', 'Updated details for employee: meeee Canaveral (ID: 90)', '2025-10-05 14:36:44'),
(278, 'System', 'Update Employee', 'Updated details for employee: Loren Lorelai (ID: 12345)', '2025-10-07 08:56:18'),
(279, 'System', 'Update Employee', 'Updated details for employee: destura jimuel (ID: 21)', '2025-10-07 08:57:20'),
(280, 'System', 'Update Employee', 'Updated details for employee: test test (ID: 1)', '2025-10-07 11:40:03'),
(281, 'System', 'Add Remittance', 'Added 1 remittance(s) for 1 employee(s) for the period of December 2025, totaling ₱800.00 - Details: Loren Lorelai (1 remittance(s), ₱800.00) | Email notifications: 1 sent, 0 failed', '2025-10-08 09:42:58');

-- --------------------------------------------------------

--
-- Table structure for table `remittances`
--

CREATE TABLE `remittances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `remittance_type` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `remittance_month` int(11) NOT NULL,
  `remittance_year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remittances`
--

INSERT INTO `remittances` (`id`, `employee_id`, `remittance_type`, `amount`, `status`, `payment_date`, `reference_no`, `remittance_month`, `remittance_year`) VALUES
(38, 19, 'Pag-IBIG', 111111.00, 'Paid', NULL, NULL, 9, 2025),
(39, 21, 'Pag-IBIG', 1111111.00, 'Paid', NULL, NULL, 9, 2025),
(40, 40, 'Pag-IBIG', 1000.00, 'Paid', NULL, NULL, 9, 2025),
(41, 40, 'SSS', 1000.00, 'Paid', NULL, NULL, 9, 2025),
(42, 40, 'PhilHealth', 1000.00, 'Paid', NULL, NULL, 9, 2025),
(46, 40, 'SSS', 100.00, 'Paid', NULL, NULL, 12, 2025),
(50, 24, 'Pag-IBIG', 1000.00, 'Paid', NULL, NULL, 9, 2025),
(53, 3, 'PhilHealth', 100000.00, 'Paid', NULL, NULL, 9, 2025),
(54, 3, 'SSS', 100000.00, 'Paid', NULL, NULL, 9, 2025),
(55, 3, 'Pag-IBIG', 100000.00, 'Paid', NULL, NULL, 9, 2025),
(56, 24, 'Pag-IBIG', 54545.00, 'Paid', NULL, NULL, 9, 2025),
(57, 24, 'SSS', 54545.00, 'Paid', NULL, NULL, 9, 2025),
(58, 24, 'PhilHealth', 212121.00, 'Paid', NULL, NULL, 9, 2025),
(59, 22, 'PhilHealth', 2313123.00, 'Paid', NULL, NULL, 9, 2025),
(60, 3, 'Pag-IBIG', 99999.00, 'Paid', NULL, NULL, 9, 2025),
(61, 19, 'Pag-IBIG', 7878.00, 'Paid', NULL, NULL, 9, 2025),
(62, 24, 'Pag-IBIG', 1000.00, 'Paid', '2025-09-26', NULL, 9, 2025),
(63, 40, 'Pag-IBIG', 100.00, 'Paid', '2025-09-27', NULL, 11, 2025),
(64, 16, 'SSS', 100.00, 'Paid', '2025-09-28', NULL, 3, 2025),
(65, 40, 'Pag-IBIG', 100.00, 'Paid', '2025-09-28', NULL, 10, 2025),
(66, 19, 'PhilHealth', 100.00, 'Paid', '2025-09-28', NULL, 10, 2025),
(67, 19, 'SSS', 100.00, 'Paid', '2025-09-28', NULL, 10, 2025),
(68, 111, 'PhilHealth', 100.00, 'Paid', '2025-09-29', NULL, 6, 2025),
(69, 111, 'Pag-IBIG', 100.00, 'Paid', '2025-09-29', NULL, 6, 2025),
(70, 111, 'SSS', 100.00, 'Paid', '2025-09-29', NULL, 6, 2025),
(72, 40, 'SSS', 500.00, 'Paid', '2025-09-29', NULL, 8, 2025),
(73, 40, 'Pag-IBIG', 500.00, 'Paid', '2025-09-29', NULL, 8, 2025),
(74, 40, 'PhilHealth', 200.00, 'Paid', '2025-09-29', NULL, 8, 2025),
(76, 6, 'Pag-IBIG', 500.00, 'Paid', NULL, NULL, 9, 2025),
(77, 1, 'Pag-IBIG', 500.00, 'Paid', NULL, NULL, 9, 2025),
(78, 101, 'Pag-IBIG', 500.00, 'Paid', NULL, NULL, 9, 2025),
(83, 26, 'Pag-IBIG', 200.00, 'Paid', NULL, NULL, 10, 2025),
(85, 1231, 'PhilHealth', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(86, 26, 'PhilHealth', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(87, 19, 'Pag-IBIG', 1000.00, 'Paid', NULL, NULL, 10, 2025),
(88, 19, 'SSS', 1000.00, 'Paid', NULL, NULL, 10, 2025),
(89, 19, 'PhilHealth', 2000.00, 'Paid', NULL, NULL, 10, 2025),
(90, 67890, 'Pag-IBIG', 1000.00, 'Paid', NULL, NULL, 10, 2025),
(91, 18, 'Pag-IBIG', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(92, 67890, 'Pag-IBIG', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(93, 5, 'Pag-IBIG', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(94, 67890, 'SSS', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(95, 19, 'SSS', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(96, 5, 'SSS', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(97, 67890, 'PhilHealth', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(98, 19, 'PhilHealth', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(99, 5, 'PhilHealth', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(100, 33, 'PhilHealth', 1000.00, 'Paid', NULL, NULL, 10, 2025),
(101, 33, 'SSS', 1000.00, 'Paid', NULL, NULL, 10, 2025),
(102, 16, 'SSS', 1000.00, 'Paid', NULL, NULL, 10, 2025),
(103, 3122, 'SSS', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(104, 241, 'SSS', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(105, 26, 'SSS', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(106, 3122, 'Pag-IBIG', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(107, 241, 'Pag-IBIG', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(108, 26, 'Pag-IBIG', 5000.00, 'Paid', NULL, NULL, 10, 2025),
(109, 5, 'Pag-IBIG', 1000.00, 'Paid', '2025-10-05', NULL, 11, 2025),
(110, 27, 'SSS', 1000.00, 'Paid', '2025-10-07', NULL, 10, 2025),
(111, 27, 'PhilHealth', 1000.00, 'Paid', '2025-10-07', NULL, 10, 2025),
(112, 12345, 'PhilHealth', 1000.00, 'Paid', '2025-10-07', NULL, 10, 2025),
(113, 12345, 'SSS', 1000.00, 'Paid', '2025-10-07', NULL, 10, 2025),
(114, 21, 'Pag-IBIG', 1000.00, 'Paid', '2025-10-07', NULL, 10, 2025),
(115, 12345, 'Pag-IBIG', 2000.00, 'Paid', '2025-10-07', NULL, 10, 2025),
(116, 12345, 'SSS', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(117, 12345, 'SSS', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(118, 12345, 'Pag-IBIG', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(119, 12345, 'Pag-IBIG', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(120, 12345, 'PhilHealth', 1000.00, 'Paid', '2025-10-07', NULL, 12, 2025),
(121, 12345, 'SSS', 900.00, 'Paid', '2025-10-07', NULL, 12, 2025),
(122, 21, 'SSS', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(123, 21, 'Pag-IBIG', 1000.00, 'Paid', '2025-10-07', NULL, 12, 2025),
(124, 19, 'Pag-IBIG', 1000.00, 'Paid', '2025-10-07', NULL, 12, 2025),
(125, 12345, 'PhilHealth', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(126, 21, 'PhilHealth', 1000.00, 'Paid', '2025-10-07', NULL, 11, 2025),
(127, 12345, 'SSS', 1000.00, 'Paid', '2025-10-08', NULL, 12, 2025),
(128, 12345, 'PhilHealth', 1000.00, 'Paid', '2025-10-08', NULL, 12, 2025),
(129, 12345, 'Pag-IBIG', 10000.00, 'Paid', '2025-10-08', NULL, 12, 2025),
(130, 12345, 'Pag-IBIG', 800.00, 'Paid', NULL, NULL, 12, 2025),
(131, 1, 'SSS', 1000.00, 'Paid', '2025-10-08', NULL, 12, 2025),
(132, 1, 'PhilHealth', 9000.00, 'Paid', '2025-10-08', NULL, 12, 2025),
(133, 19, 'SSS', 1000.00, 'Paid', '2025-10-09', NULL, 12, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `remittances_archive`
--

CREATE TABLE `remittances_archive` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `remittance_type` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` varchar(20) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `remittance_month` int(11) NOT NULL,
  `remittance_year` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `remittances_archive`
--

INSERT INTO `remittances_archive` (`id`, `employee_id`, `remittance_type`, `amount`, `status`, `payment_date`, `reference_no`, `remittance_month`, `remittance_year`) VALUES
(51, 98765, 'SSS', 50000.00, 'Paid', NULL, NULL, 9, 2025),
(52, 98765, 'SSS', 50000.00, 'Paid', NULL, NULL, 9, 2025),
(75, 98765, 'Pag-IBIG', 900.00, 'Paid', NULL, NULL, 9, 2025),
(82, 55555, 'PhilHealth', 200.00, 'Paid', NULL, NULL, 10, 2025),
(84, 55555, 'PhilHealth', 5000.00, 'Paid', NULL, NULL, 10, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(50) NOT NULL,
  `setting_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`) VALUES
(1, 'admin_password_hash', '$2y$10$lRCtfHDF1A/S1UBh5RH7Qu3NmuYeAIy3OTEwM5I8j5ccLwvqp0WkC');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_archive`
--
ALTER TABLE `employee_archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modification_logs`
--
ALTER TABLE `modification_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remittances`
--
ALTER TABLE `remittances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `remittances_archive`
--
ALTER TABLE `remittances_archive`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
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
-- AUTO_INCREMENT for table `emergency_contacts`
--
ALTER TABLE `emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `modification_logs`
--
ALTER TABLE `modification_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=282;

--
-- AUTO_INCREMENT for table `remittances`
--
ALTER TABLE `remittances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `remittances_archive`
--
ALTER TABLE `remittances_archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `remittances`
--
ALTER TABLE `remittances`
  ADD CONSTRAINT `remittances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
