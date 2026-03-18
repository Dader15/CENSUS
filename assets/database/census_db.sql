-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 12, 2026 at 01:34 AM
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
-- Database: `census_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `communitytaxcert_tbl`
--

CREATE TABLE `communitytaxcert_tbl` (
  `id` int(11) DEFAULT NULL,
  `q42a_ctcinformation` varchar(322) DEFAULT NULL,
  `q42b_ctcinformation` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `demochar_tbl`
--

CREATE TABLE `demochar_tbl` (
  `id` int(11) DEFAULT NULL,
  `q1_fullname` varchar(322) DEFAULT NULL,
  `q2_relationshiptohhh` varchar(322) DEFAULT NULL,
  `q3_sex` varchar(322) DEFAULT NULL,
  `q4_age` varchar(322) DEFAULT NULL,
  `q5_dob` varchar(322) DEFAULT NULL,
  `q6_placeofbirth` varchar(322) DEFAULT NULL,
  `q7_nationality` varchar(322) DEFAULT NULL,
  `q8_maritalstatus` varchar(322) DEFAULT NULL,
  `q9_religion` varchar(322) DEFAULT NULL,
  `q10_ethnicity` varchar(322) DEFAULT NULL,
  `q11_highesteduc` varchar(322) DEFAULT NULL,
  `q12_currentlyenrolled` varchar(322) DEFAULT NULL,
  `q13_typeofschool` varchar(322) DEFAULT NULL,
  `q14_placeofschool` varchar(322) DEFAULT NULL,
  `user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `economicactivity_tbl`
--

CREATE TABLE `economicactivity_tbl` (
  `id` int(11) DEFAULT NULL,
  `q15_monthlyincome` varchar(322) DEFAULT NULL,
  `q16_sourceincome` varchar(322) DEFAULT NULL,
  `q17_statusofworkorbusiness` varchar(322) DEFAULT NULL,
  `q18_placeofworkorbusiness` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `healthinfo_tbl`
--

CREATE TABLE `healthinfo_tbl` (
  `id` int(11) DEFAULT NULL,
  `q19_placeofdelivery` varchar(322) DEFAULT NULL,
  `q20_birthattendant` varchar(322) DEFAULT NULL,
  `q21_immunization` varchar(322) DEFAULT NULL,
  `q22_livingchildren` varchar(322) DEFAULT NULL,
  `q23_familyplanninguse` varchar(322) DEFAULT NULL,
  `q24_sourceoffpmethod` varchar(322) DEFAULT NULL,
  `q25_intentionoffp` varchar(322) DEFAULT NULL,
  `q26_healthinsurance` varchar(322) DEFAULT NULL,
  `q27_facilityvisited` varchar(322) DEFAULT NULL,
  `q28_reasonforvisit` varchar(322) DEFAULT NULL,
  `q29_disability` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_tbl`
--

CREATE TABLE `interview_tbl` (
  `id` int(11) DEFAULT NULL,
  `datevisit` datetime DEFAULT NULL,
  `timestart` datetime DEFAULT NULL,
  `timeended` datetime DEFAULT NULL,
  `result` varchar(322) DEFAULT NULL,
  `dateofnextvisit` datetime DEFAULT NULL,
  `nameofencodersname` varchar(322) DEFAULT NULL,
  `nameofencoderfname` varchar(322) DEFAULT NULL,
  `nameofencodermiddleinitial` varchar(322) DEFAULT NULL,
  `nameofencodersuffix` varchar(322) DEFAULT NULL,
  `nameofsupervisorsname` varchar(322) DEFAULT NULL,
  `nameofsupervisorfname` varchar(322) DEFAULT NULL,
  `namenameofsupervisormiddleinitial` varchar(322) DEFAULT NULL,
  `namenameofsupervisorsuffix` varchar(322) DEFAULT NULL,
  `nameofinterviewersname` varchar(322) DEFAULT NULL,
  `nameofinterviewerfname` varchar(322) DEFAULT NULL,
  `nameofinterviewermiddlename` varchar(322) DEFAULT NULL,
  `nameofinterviewersuffix` varchar(322) DEFAULT NULL,
  `datencoded` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loginhistory_tbl`
--

CREATE TABLE `loginhistory_tbl` (
  `ID` int(11) NOT NULL,
  `UID` int(11) DEFAULT NULL,
  `session_id` varchar(322) DEFAULT NULL,
  `ip_address` varchar(322) DEFAULT NULL,
  `device_used` varchar(322) DEFAULT NULL,
  `created` timestamp NULL DEFAULT NULL,
  `updated` timestamp NULL DEFAULT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `is_active` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_tbl`
--

CREATE TABLE `master_tbl` (
  `id` int(11) NOT NULL,
  `number_code` varchar(322) DEFAULT NULL,
  `household` int(11) DEFAULT NULL,
  `intitutional_living` int(11) DEFAULT NULL,
  `nameofrespondentsname` varchar(322) DEFAULT NULL,
  `nameofrespondentfname` varchar(322) DEFAULT NULL,
  `nameofrespondentmiddleinitial` varchar(322) DEFAULT NULL,
  `nameofrespondentsuffix` varchar(322) DEFAULT NULL,
  `household_headsname` varchar(322) DEFAULT NULL,
  `household_headfname` varchar(322) DEFAULT NULL,
  `household_headmiddleinitial` varchar(322) DEFAULT NULL,
  `household_headsuffix` varchar(322) DEFAULT NULL,
  `totalnohouseholdmembers` varchar(322) DEFAULT NULL,
  `province` varchar(322) DEFAULT NULL,
  `city_municipality` varchar(322) DEFAULT NULL,
  `brgy` varchar(322) DEFAULT NULL,
  `unitno` varchar(322) DEFAULT NULL,
  `lotblockno` varchar(322) DEFAULT NULL,
  `streetname` varchar(322) DEFAULT NULL,
  `dateencoded` datetime DEFAULT NULL,
  `nameencoder` varchar(322) DEFAULT NULL,
  `nameofsupervisor` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrationinfo_tbl`
--

CREATE TABLE `migrationinfo_tbl` (
  `id` int(11) DEFAULT NULL,
  `q33_previousresidence` varchar(322) DEFAULT NULL,
  `q34_previousresidence` varchar(322) DEFAULT NULL,
  `q35_lengthofstayinbrgy` varchar(322) DEFAULT NULL,
  `q36_typeofresident` varchar(322) DEFAULT NULL,
  `q37_dateoftransfer` varchar(322) DEFAULT NULL,
  `q38a_reasonforleavingprevresidence` varchar(322) DEFAULT NULL,
  `q38b_reasonforleavingprevresidence` varchar(322) DEFAULT NULL,
  `q38c_reasonforleavingprevresidence` varchar(322) DEFAULT NULL,
  `q39a_returntoprevresidence` varchar(322) DEFAULT NULL,
  `q39b_returntoprevresidence` varchar(322) DEFAULT NULL,
  `q40a_reasonfortransferinbrgy` varchar(322) DEFAULT NULL,
  `q40b_reasonfortransferinbrgy` varchar(322) DEFAULT NULL,
  `q40c_reasonfortransferinbrgy` varchar(322) DEFAULT NULL,
  `q41a_durationofstayinbrgy` varchar(322) DEFAULT NULL,
  `q41b_durationofstayinbrgy` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questionforhousehold_tbl`
--

CREATE TABLE `questionforhousehold_tbl` (
  `id` int(11) DEFAULT NULL,
  `q45` varchar(322) DEFAULT NULL,
  `q46` varchar(322) DEFAULT NULL,
  `q47` varchar(322) DEFAULT NULL,
  `q48` varchar(322) DEFAULT NULL,
  `q49` varchar(322) DEFAULT NULL,
  `q50` varchar(322) DEFAULT NULL,
  `q51` varchar(322) DEFAULT NULL,
  `q52` varchar(322) DEFAULT NULL,
  `q53` varchar(322) DEFAULT NULL,
  `q54a` varchar(322) DEFAULT NULL,
  `q54b` varchar(322) DEFAULT NULL,
  `q55a` varchar(322) DEFAULT NULL,
  `q55b` varchar(322) DEFAULT NULL,
  `q55c` varchar(322) DEFAULT NULL,
  `q56a` varchar(322) DEFAULT NULL,
  `q56b` varchar(322) DEFAULT NULL,
  `q56c` varchar(322) DEFAULT NULL,
  `q57a` varchar(322) DEFAULT NULL,
  `q57b` varchar(322) DEFAULT NULL,
  `q57c` varchar(322) DEFAULT NULL,
  `q58a` varchar(322) DEFAULT NULL,
  `q58b` varchar(322) DEFAULT NULL,
  `q58c` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions_tbl`
--

CREATE TABLE `questions_tbl` (
  `id` int(11) NOT NULL,
  `description` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions_tbl`
--

INSERT INTO `questions_tbl` (`id`, `description`) VALUES
(1, 'Do you own or amortize this housing unit occupied by your household or do rent it, do you occupy it rent-free with consent of owner or rent-free without consent of owner?'),
(2, 'Do you own or amortize this lot occupied by your household or do rent it, do you occupy it rent-free with consent of owner or rent-free without consent of owner?'),
(3, 'What type of fuel does this housefold use for lighting?'),
(4, 'What kind of fuel does this household use most of the time for cooking?'),
(5, 'What is the household\'s main source of drinking water?'),
(6, 'How does your household usually dispose of your kitchen garbage such as leftover food, peeling of fruits and vegetables, fish and chicken entrails, and others?'),
(7, 'Do you segregate your garbage?'),
(8, 'What type of toilet facility does this household use?'),
(9, 'Type of building/house'),
(10, 'Construction materials of the outer wall'),
(11, 'Do you have any female members who died in the past 12 months? How old is she and what is the cause of her death?'),
(12, 'Do you have a child below 5 years old who died in the past 12 months? How old is she/he? What is the cause of her/his death?'),
(13, 'What are the common diseases that causes death in this barangay?'),
(14, 'What do you think are the primary needs of this barangay?'),
(15, 'Where does your household intent to stay in five years from now?');

-- --------------------------------------------------------

--
-- Table structure for table `skillsdevelopment_tbl`
--

CREATE TABLE `skillsdevelopment_tbl` (
  `id` int(11) DEFAULT NULL,
  `q43_skilldevelopment` varchar(322) DEFAULT NULL,
  `q44_skills` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sociocivicparticipation_tbl`
--

CREATE TABLE `sociocivicparticipation_tbl` (
  `id` int(11) DEFAULT NULL,
  `q30_soloparent` varchar(322) DEFAULT NULL,
  `q31_registeredsenior` varchar(322) DEFAULT NULL,
  `q32_registeredvoter` varchar(322) DEFAULT NULL,
  `user` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tbl`
--

CREATE TABLE `user_tbl` (
  `id` int(11) NOT NULL,
  `sname` varchar(322) DEFAULT NULL,
  `fname` varchar(322) DEFAULT NULL,
  `middleinitial` varchar(322) DEFAULT NULL,
  `suffix` varchar(322) DEFAULT NULL,
  `username` varchar(322) DEFAULT NULL,
  `password` varchar(322) DEFAULT NULL,
  `usertype` varchar(322) DEFAULT NULL,
  `brgy` varchar(322) DEFAULT NULL,
  `position` varchar(322) DEFAULT NULL,
  `delete_status` int(11) DEFAULT NULL,
  `date created` datetime DEFAULT NULL,
  `changedpassword` int(11) DEFAULT NULL,
  `updatedat` datetime DEFAULT NULL,
  `session_id` varchar(322) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_tbl`
--

INSERT INTO `user_tbl` (`id`, `sname`, `fname`, `middleinitial`, `suffix`, `username`, `password`, `usertype`, `brgy`, `position`, `delete_status`, `date created`, `changedpassword`, `updatedat`, `session_id`) VALUES
(1, 'ANG', 'ALEXANDER', 'B', NULL, 'admin', '$2y$12$vCUWidD64S0UNpwZHLoOaeq7ZczgSn5ecZ8JNf.VZ.TvdbTBJkvem', 'SUPERADMIN', '18', 'COMPUTER PROGRAMMER I', 0, '2026-03-04 08:23:01', 1, '2026-03-11 02:52:07', 'kjoni4o8gednb3hbs8g1ap42pf'),
(2, 'SAMPLE', 'SAMPLE', 'SAMPLE', NULL, 'sample', '$2y$12$0TmIh7gOzLwCuzIaQ7AtQOQC0rmMwCDlrYXYss3RAxHXYmKf7vZEW', 'ADMIN', '18', 'SAMPLE', 0, '2026-03-06 10:19:55', 0, '2026-03-09 11:26:03', NULL),
(7, 'SAMPLE1', 'SAMPLE1', 'SAMPLE1', NULL, 'sample1', '$2y$10$8DbhfhPfQd25krfuxZT6OOvHWWeO.iIdsZd.nXcxHp/Admth9HESG', 'USER', '16', 'Sample Account1', 0, '2026-03-06 13:46:56', 1, '2026-03-09 04:49:24', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `loginhistory_tbl`
--
ALTER TABLE `loginhistory_tbl`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `master_tbl`
--
ALTER TABLE `master_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `questions_tbl`
--
ALTER TABLE `questions_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_tbl`
--
ALTER TABLE `user_tbl`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `loginhistory_tbl`
--
ALTER TABLE `loginhistory_tbl`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions_tbl`
--
ALTER TABLE `questions_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_tbl`
--
ALTER TABLE `user_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
