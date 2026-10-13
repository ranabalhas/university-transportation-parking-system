-- SQL schema for the university_system project
-- Structure only: INSERT statements and all table records have been removed.
-- Review and configure database credentials locally before running the application.

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `university_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--
-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `audience` enum('students','drivers','all') DEFAULT 'all',
  `priority` enum('normal','important','urgent') DEFAULT 'normal',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcements`
--
-- --------------------------------------------------------

--
-- Table structure for table `bus_line`
--

CREATE TABLE `bus_line` (
  `id` int(11) NOT NULL,
  `line_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_line`
--
-- --------------------------------------------------------

--
-- Table structure for table `bus_subscriptions`
--

CREATE TABLE `bus_subscriptions` (
  `id` int(11) NOT NULL,
  `university_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bus_line_id` int(11) DEFAULT NULL,
  `bus_trip_id` int(11) DEFAULT NULL,
  `can_cancel` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_subscriptions`
--
-- --------------------------------------------------------

--
-- Table structure for table `bus_trips`
--

CREATE TABLE `bus_trips` (
  `id` int(11) NOT NULL,
  `arrival_time` time DEFAULT NULL,
  `departure_time` time DEFAULT NULL,
  `bus_line_id` int(11) DEFAULT NULL,
  `days` varchar(20) DEFAULT NULL,
  `capacity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_trips`
--
-- --------------------------------------------------------

--
-- Table structure for table `bus_villages`
--

CREATE TABLE `bus_villages` (
  `id` int(11) NOT NULL,
  `village_name` varchar(100) DEFAULT NULL,
  `bus_line_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_villages`
--
-- --------------------------------------------------------

--
-- Table structure for table `driver`
--

CREATE TABLE `driver` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `bus_line_id` int(11) DEFAULT NULL,
  `first_login` tinyint(4) DEFAULT 1,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `driver`
--
-- --------------------------------------------------------

--
-- Table structure for table `parking`
--

CREATE TABLE `parking` (
  `id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking`
--
-- --------------------------------------------------------

--
-- Table structure for table `parking_subscriptions`
--

CREATE TABLE `parking_subscriptions` (
  `id` int(11) NOT NULL,
  `university_id` int(11) DEFAULT NULL,
  `plan` varchar(50) DEFAULT NULL,
  `registration_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_subscriptions`
--
-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `university_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--
-- --------------------------------------------------------

--
-- Table structure for table `university_students`
--

CREATE TABLE `university_students` (
  `university_id` int(11) NOT NULL,
  `university_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `university_students`
--
--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_line`
--
ALTER TABLE `bus_line`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bus_subscriptions`
--
ALTER TABLE `bus_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `university_id` (`university_id`),
  ADD KEY `bus_line_id` (`bus_line_id`),
  ADD KEY `bus_trip_id` (`bus_trip_id`);

--
-- Indexes for table `bus_trips`
--
ALTER TABLE `bus_trips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_line_id` (`bus_line_id`);

--
-- Indexes for table `bus_villages`
--
ALTER TABLE `bus_villages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bus_line_id` (`bus_line_id`);

--
-- Indexes for table `driver`
--
ALTER TABLE `driver`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_driver_phone` (`phone`),
  ADD KEY `bus_line_id` (`bus_line_id`);

--
-- Indexes for table `parking`
--
ALTER TABLE `parking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parking_subscriptions`
--
ALTER TABLE `parking_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `university_id` (`university_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`university_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`);

--
-- Indexes for table `university_students`
--
ALTER TABLE `university_students`
  ADD PRIMARY KEY (`university_id`),
  ADD UNIQUE KEY `university_email` (`university_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `bus_line`
--
ALTER TABLE `bus_line`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `bus_subscriptions`
--
ALTER TABLE `bus_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `bus_trips`
--
ALTER TABLE `bus_trips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `bus_villages`
--
ALTER TABLE `bus_villages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `driver`
--
ALTER TABLE `driver`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `parking`
--
ALTER TABLE `parking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `parking_subscriptions`
--
ALTER TABLE `parking_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `university_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=723305655;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bus_subscriptions`
--
ALTER TABLE `bus_subscriptions`
  ADD CONSTRAINT `bus_subscriptions_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `students` (`university_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bus_subscriptions_ibfk_2` FOREIGN KEY (`bus_line_id`) REFERENCES `bus_line` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bus_subscriptions_ibfk_3` FOREIGN KEY (`bus_trip_id`) REFERENCES `bus_trips` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bus_trips`
--
ALTER TABLE `bus_trips`
  ADD CONSTRAINT `bus_trips_ibfk_1` FOREIGN KEY (`bus_line_id`) REFERENCES `bus_line` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bus_villages`
--
ALTER TABLE `bus_villages`
  ADD CONSTRAINT `bus_villages_ibfk_1` FOREIGN KEY (`bus_line_id`) REFERENCES `bus_line` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `driver`
--
ALTER TABLE `driver`
  ADD CONSTRAINT `driver_ibfk_1` FOREIGN KEY (`bus_line_id`) REFERENCES `bus_line` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `parking_subscriptions`
--
ALTER TABLE `parking_subscriptions`
  ADD CONSTRAINT `parking_subscriptions_ibfk_1` FOREIGN KEY (`university_id`) REFERENCES `students` (`university_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
