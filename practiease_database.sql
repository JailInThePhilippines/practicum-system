-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2024 at 04:08 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `practiease_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_credentials_tbl`
--

CREATE TABLE `admin_credentials_tbl` (
  `admin_id` int(11) NOT NULL,
  `admin_username` varchar(50) DEFAULT NULL,
  `admin_password` varchar(255) NOT NULL,
  `admin_email` varchar(100) NOT NULL,
  `admin_name` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_credentials_tbl`
--

INSERT INTO `admin_credentials_tbl` (`admin_id`, `admin_username`, `admin_password`, `admin_email`, `admin_name`, `role`) VALUES
(2, 'admin1', 'password', 'admin1@gordoncollege.edu.ph', 'John Lloyd Merie', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `certificate_of_completion_tbl`
--

CREATE TABLE `certificate_of_completion_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `certificate_of_completion_tbl`
--

INSERT INTO `certificate_of_completion_tbl` (`file_id`, `file_name`, `file_path`, `employer_id`, `student_id`) VALUES
(69, 'WEEK1.Jose.LeeLeighnard.pdf', 'certificate_of_completion/WEEK1.Jose.LeeLeighnard.pdf', 35, 62);

-- --------------------------------------------------------

--
-- Table structure for table `employer_credentials_tbl`
--

CREATE TABLE `employer_credentials_tbl` (
  `employer_id` int(11) NOT NULL,
  `employer_name` varchar(255) NOT NULL,
  `employer_email` varchar(255) NOT NULL,
  `employer_password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `employer_position` varchar(100) DEFAULT NULL,
  `company_number` varchar(20) DEFAULT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `company_email` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employer_credentials_tbl`
--

INSERT INTO `employer_credentials_tbl` (`employer_id`, `employer_name`, `employer_email`, `employer_password`, `role`, `company_name`, `employer_position`, `company_number`, `company_address`, `company_email`) VALUES
(35, 'Emma Watson', 'watsonemma@yahoo.com', '$2y$10$lZ8Drur4DyqYeG0QTYCCiudBshSr1cR5e/TNApZ426jAKXzCMgsOu', 'employer', 'StructureSync IT', 'Head Manager', '09198700932', 'Olongapo City, Madalaga Street, Structure Sync IT', 'StructureSyncIT@tech.com');

-- --------------------------------------------------------

--
-- Table structure for table `employer_feedback_tbl`
--

CREATE TABLE `employer_feedback_tbl` (
  `feedback_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `office_department_branch` varchar(255) NOT NULL,
  `supervisor` varchar(255) NOT NULL,
  `hours_worked` int(11) NOT NULL,
  `knowledge_criteria_1` int(11) DEFAULT NULL,
  `knowledge_criteria_2` int(11) DEFAULT NULL,
  `knowledge_criteria_3` int(11) DEFAULT NULL,
  `knowledge_criteria_4` int(11) DEFAULT NULL,
  `knowledge_criteria_5` int(11) DEFAULT NULL,
  `skills_criteria_1` int(11) DEFAULT NULL,
  `skills_criteria_2` int(11) DEFAULT NULL,
  `skills_criteria_3` int(11) DEFAULT NULL,
  `skills_criteria_4` int(11) DEFAULT NULL,
  `skills_criteria_5` int(11) DEFAULT NULL,
  `skills_criteria_6` int(11) DEFAULT NULL,
  `skills_criteria_7` int(11) DEFAULT NULL,
  `skills_criteria_8` int(11) DEFAULT NULL,
  `attitude_criteria_1` int(11) DEFAULT NULL,
  `attitude_criteria_2` int(11) DEFAULT NULL,
  `attitude_criteria_3` int(11) DEFAULT NULL,
  `attitude_criteria_4` int(11) DEFAULT NULL,
  `attitude_criteria_5` int(11) DEFAULT NULL,
  `attitude_criteria_6` int(11) DEFAULT NULL,
  `attitude_criteria_7` int(11) DEFAULT NULL,
  `attitude_criteria_8` int(11) DEFAULT NULL,
  `attitude_criteria_9` int(11) DEFAULT NULL,
  `attitude_criteria_10` int(11) DEFAULT NULL,
  `attitude_criteria_11` int(11) DEFAULT NULL,
  `attitude_criteria_12` int(11) DEFAULT NULL,
  `attitude_criteria_13` int(11) DEFAULT NULL,
  `overall_performance` enum('Excellent','Very Good','Good','Fair','Poor') DEFAULT NULL,
  `major_strongpoints` varchar(1000) DEFAULT NULL,
  `major_weakpoints` varchar(1000) DEFAULT NULL,
  `other_comments` varchar(1000) DEFAULT NULL,
  `suggestions_strongpoints` varchar(1000) DEFAULT NULL,
  `suggestions_weakpoints` varchar(1000) DEFAULT NULL,
  `recommendation` varchar(1000) DEFAULT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `knowledge_score` int(11) DEFAULT NULL,
  `skills_score` int(11) DEFAULT NULL,
  `attitude_score` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employer_feedback_tbl`
--

INSERT INTO `employer_feedback_tbl` (`feedback_id`, `student_name`, `office_department_branch`, `supervisor`, `hours_worked`, `knowledge_criteria_1`, `knowledge_criteria_2`, `knowledge_criteria_3`, `knowledge_criteria_4`, `knowledge_criteria_5`, `skills_criteria_1`, `skills_criteria_2`, `skills_criteria_3`, `skills_criteria_4`, `skills_criteria_5`, `skills_criteria_6`, `skills_criteria_7`, `skills_criteria_8`, `attitude_criteria_1`, `attitude_criteria_2`, `attitude_criteria_3`, `attitude_criteria_4`, `attitude_criteria_5`, `attitude_criteria_6`, `attitude_criteria_7`, `attitude_criteria_8`, `attitude_criteria_9`, `attitude_criteria_10`, `attitude_criteria_11`, `attitude_criteria_12`, `attitude_criteria_13`, `overall_performance`, `major_strongpoints`, `major_weakpoints`, `other_comments`, `suggestions_strongpoints`, `suggestions_weakpoints`, `recommendation`, `employer_id`, `student_id`, `knowledge_score`, `skills_score`, `attitude_score`) VALUES
(50, 'Janna Rolls', 'IT Section - Ayala Malls', 'Emma Watson', 300, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 5, 'Excellent', 'Strong', 'Strong', 'Strong', 'Strong', 'Strong', 'Strong', 35, 62, 25, 40, 65);

-- --------------------------------------------------------

--
-- Table structure for table `instructor_announcement_tbl`
--

CREATE TABLE `instructor_announcement_tbl` (
  `announcement_id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `body` varchar(255) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `announcement_timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_announcement_tbl`
--

INSERT INTO `instructor_announcement_tbl` (`announcement_id`, `title`, `body`, `instructor_id`, `announcement_timestamp`) VALUES
(17, 'Announcement', 'Announcement', 22, '2024-06-06 07:21:47'),
(19, 'Title', 'Body', 22, '2024-08-05 12:05:15');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_credentials_tbl`
--

CREATE TABLE `instructor_credentials_tbl` (
  `instructor_id` int(11) NOT NULL,
  `instructor_email` varchar(255) NOT NULL,
  `instructor_password` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `block_handled` varchar(255) DEFAULT NULL,
  `program_handled` varchar(255) DEFAULT NULL,
  `instructor_name` varchar(255) DEFAULT NULL,
  `year_handled` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_credentials_tbl`
--

INSERT INTO `instructor_credentials_tbl` (`instructor_id`, `instructor_email`, `instructor_password`, `role`, `block_handled`, `program_handled`, `instructor_name`, `year_handled`) VALUES
(22, 'martinez.armilyn@gordoncollege.edu.ph', '$2y$10$qh/4hpiWxFM1bI4H6UrfSerL0iwgMFHAol8nXPm5153DvDtWYAYBG', 'instructor', 'A,B,D', 'BSIT', 'Armilyn Martinez', '1,2,3'),
(23, 'manaloto.loudel@gordoncollege.edu.ph', '$2y$10$jQFTpP.CcVo04skkuUj1euCZI1vdRULo709T6cSFJPpD1jA6FbGuG', 'instructor', 'A,B', 'BSEMC', 'Loudel Manaloto', '2,3,4');

-- --------------------------------------------------------

--
-- Table structure for table `instructor_requirement_checking_tbl`
--

CREATE TABLE `instructor_requirement_checking_tbl` (
  `id` int(11) NOT NULL,
  `endorsement_status` varchar(50) DEFAULT NULL,
  `application_status` varchar(50) DEFAULT NULL,
  `consent_status` varchar(50) DEFAULT NULL,
  `ccs_status` varchar(50) DEFAULT NULL,
  `seminar_status` varchar(50) DEFAULT NULL,
  `sportsfest_status` varchar(50) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `acceptance_status` varchar(255) DEFAULT NULL,
  `moa_status` varchar(255) DEFAULT NULL,
  `resume_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `instructor_requirement_checking_tbl`
--

INSERT INTO `instructor_requirement_checking_tbl` (`id`, `endorsement_status`, `application_status`, `consent_status`, `ccs_status`, `seminar_status`, `sportsfest_status`, `instructor_id`, `student_id`, `acceptance_status`, `moa_status`, `resume_status`) VALUES
(20, 'Cleared', 'Cleared', 'Cleared', 'Cleared', 'Cleared', 'Cleared', 22, 62, 'Cleared', 'Currently Verifying', 'Cleared'),
(23, 'Not Cleared', 'Not Cleared', 'Not Cleared', 'Not Cleared', 'Not Cleared', 'Not Cleared', 22, 66, 'Not Cleared', 'Not Yet Cleared', 'Not Cleared'),
(24, 'Not Cleared', 'Not Cleared', 'Not Cleared', 'Not Cleared', 'Not Cleared', 'Not Cleared', 22, 67, 'Not Cleared', 'Not Yet Cleared', 'Not Cleared'),
(25, 'Not Yet Cleared', 'Not Yet Cleared', 'Not Yet Cleared', 'Not Yet Cleared', 'Not Yet Cleared', 'Not Yet Cleared', 22, 68, 'Not Yet Cleared', 'Not Yet Cleared', 'Not Yet Cleared');

-- --------------------------------------------------------

--
-- Table structure for table `student_acquaintance_picture_tbl`
--

CREATE TABLE `student_acquaintance_picture_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_barangay_clearance_tbl`
--

CREATE TABLE `student_barangay_clearance_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_ccs_picture_tbl`
--

CREATE TABLE `student_ccs_picture_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_ccs_picture_tbl`
--

INSERT INTO `student_ccs_picture_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(86, 'WEEK5.Jose.LeeLeighnard.pdf', 'student_proof_of_evidences/student_ccs_picture/WEEK5.Jose.LeeLeighnard.pdf', '2024-06-05 13:07:54', 66, 202210312),
(91, 'BALLOT.pdf', 'student_proof_of_evidences/student_ccs_picture/BALLOT.pdf', '2024-08-06 02:44:00', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_certificate_of_completion_tbl`
--

CREATE TABLE `student_certificate_of_completion_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `instructor_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_credentials_tbl`
--

CREATE TABLE `student_credentials_tbl` (
  `user_id` int(11) NOT NULL,
  `student_name` varchar(255) NOT NULL,
  `student_password` varchar(255) NOT NULL,
  `school_id` int(9) NOT NULL,
  `student_email` varchar(255) NOT NULL,
  `role` varchar(255) DEFAULT NULL,
  `student_mobile_number` varchar(255) DEFAULT NULL,
  `block` varchar(255) DEFAULT NULL,
  `program` varchar(255) DEFAULT NULL,
  `company_address` varchar(255) DEFAULT NULL,
  `student_year` varchar(255) DEFAULT NULL,
  `ojt_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_credentials_tbl`
--

INSERT INTO `student_credentials_tbl` (`user_id`, `student_name`, `student_password`, `school_id`, `student_email`, `role`, `student_mobile_number`, `block`, `program`, `company_address`, `student_year`, `ojt_status`) VALUES
(62, 'Janna Lyn Rolls', '$2y$10$IdZjwTZC47Go3s6x4EteLe446jMWwiMhCCC2O0kcyRM9TczVf8WOu', 202210256, '202210256@gordoncollege.edu.ph', 'student', '09327586734', 'B', 'BSIT', 'N/A', '3', 'done'),
(66, 'Xanthei Iona', '$2y$10$nAYsBNmOIzeOB4iLqErfj.NKtPlWTCm67zvVQj8254mbHz5d2utyq', 202210312, '202210312@gordoncollege.edu.ph', 'student', '09192933206', 'B', 'BSIT', 'N/A', '2', 'done'),
(67, 'Onika Mariel Umayam', '$2y$10$8/qyH1mf4.wKGy1VyimoYO0T0Yamxw9qxvEAfwX8UFaKqYfyhLRE.', 202210459, 'onikamariel@gmail.com', 'student', '09433877456', 'B', 'BSIT', 'N/A', '2', 'done'),
(68, 'Lee Leighnard Jose', '$2y$10$KzZNb.VdmJMAPct.9WDvZuzBsKsoEfXdlA2q3j5QPnOxINbP9gr8i', 202210212, '202210212@gordoncollege.edu.ph', 'student', '09692933206', 'A', 'BSIT', 'Olongapo City, Madalaga Street, Structure Sync IT', '2', 'Not Yet Done');

-- --------------------------------------------------------

--
-- Table structure for table `student_daily_accomplishments_tbl`
--

CREATE TABLE `student_daily_accomplishments_tbl` (
  `daily_accomplishments_id` int(11) NOT NULL,
  `description_of_activities` varchar(255) DEFAULT NULL,
  `start_time` varchar(255) DEFAULT NULL,
  `end_time` varchar(255) DEFAULT NULL,
  `number_of_hours` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `accomplishment_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_daily_accomplishments_tbl`
--

INSERT INTO `student_daily_accomplishments_tbl` (`daily_accomplishments_id`, `description_of_activities`, `start_time`, `end_time`, `number_of_hours`, `user_id`, `date`, `school_id`, `accomplishment_status`) VALUES
(44, 'HAAHAAHA', '08:00 AM', '05:00 PM', '9', 62, '2024-06-02', 202210256, 'Rejected'),
(45, 'DASDSADSA', '08:00 AM', '05:00 PM', '9', 62, '2024-06-02', 202210256, 'Rejected'),
(48, 'asdsd', '09:00 AM', '10:00 AM', '1', 62, '2024-06-07', 202210256, 'Unverified'),
(49, 'asasa', '10:00 AM', '11:00 AM', '1', 62, '2024-06-07', 202210256, 'Unverified');

-- --------------------------------------------------------

--
-- Table structure for table `student_documentation_tbl`
--

CREATE TABLE `student_documentation_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `documentation_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_documentation_tbl`
--

INSERT INTO `student_documentation_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`, `documentation_status`) VALUES
(8, 'WEEK2.Jose.LeeLeighnard.pdf', 'student_documentation/WEEK2.Jose.LeeLeighnard.pdf', '2024-06-17 09:14:40', 62, 202210256, 'Not Cleared');

-- --------------------------------------------------------

--
-- Table structure for table `student_dtr_tbl`
--

CREATE TABLE `student_dtr_tbl` (
  `dtr_id` int(11) NOT NULL,
  `time_in` varchar(255) DEFAULT NULL,
  `time_out` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `dtr_status` varchar(255) DEFAULT NULL,
  `hours_worked` decimal(5,2) DEFAULT NULL,
  `remarks` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_dtr_tbl`
--

INSERT INTO `student_dtr_tbl` (`dtr_id`, `time_in`, `time_out`, `user_id`, `school_id`, `dtr_status`, `hours_worked`, `remarks`) VALUES
(178, '2024-06-19 13:47:00', '2024-06-19 20:47:00', 62, 202210256, 'Approved', 7.00, 'Late'),
(179, '2024-06-20 03:25:00', '2024-06-20 12:28:00', 62, 202210256, 'Rejected', 9.05, 'Late'),
(181, '2024-06-27 15:25:40', '2024-06-27 15:27:32', 62, 202210256, 'Unverified', 0.02, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_employer_relationship_tbl`
--

CREATE TABLE `student_employer_relationship_tbl` (
  `relationship_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `employer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_employer_relationship_tbl`
--

INSERT INTO `student_employer_relationship_tbl` (`relationship_id`, `student_id`, `employer_id`) VALUES
(16, 62, 35);

-- --------------------------------------------------------

--
-- Table structure for table `student_exitpoll_tbl`
--

CREATE TABLE `student_exitpoll_tbl` (
  `exitpoll_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `student_name` varchar(100) DEFAULT NULL,
  `course_and_year` varchar(50) DEFAULT NULL,
  `name_of_company` varchar(100) DEFAULT NULL,
  `assigned_position` varchar(100) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `supervisor_name` varchar(100) DEFAULT NULL,
  `ojt_duration` varchar(50) DEFAULT NULL,
  `total_hours` int(11) DEFAULT NULL,
  `work_related_to_academic_program` tinyint(1) DEFAULT NULL,
  `orientation_on_company_organization` tinyint(1) DEFAULT NULL,
  `given_job_description` tinyint(1) DEFAULT NULL,
  `work_hours_clear` tinyint(1) DEFAULT NULL,
  `felt_safe_and_secure` tinyint(1) DEFAULT NULL,
  `no_difficulty_going_to_and_from_work` tinyint(1) DEFAULT NULL,
  `provided_with_allowance` tinyint(1) DEFAULT NULL,
  `allowance_amount` varchar(50) DEFAULT NULL,
  `achievement_1_description` text DEFAULT NULL,
  `achievement_1_rating` int(11) DEFAULT NULL,
  `achievement_2_description` text DEFAULT NULL,
  `achievement_2_rating` int(11) DEFAULT NULL,
  `achievement_3_description` text DEFAULT NULL,
  `achievement_3_rating` int(11) DEFAULT NULL,
  `achievement_4_description` text DEFAULT NULL,
  `achievement_4_rating` int(11) DEFAULT NULL,
  `achievement_5_description` text DEFAULT NULL,
  `achievement_5_rating` int(11) DEFAULT NULL,
  `overall_training_experience` varchar(50) DEFAULT NULL,
  `improvement_suggestion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_exitpoll_tbl`
--

INSERT INTO `student_exitpoll_tbl` (`exitpoll_id`, `user_id`, `student_name`, `course_and_year`, `name_of_company`, `assigned_position`, `department`, `job_description`, `supervisor_name`, `ojt_duration`, `total_hours`, `work_related_to_academic_program`, `orientation_on_company_organization`, `given_job_description`, `work_hours_clear`, `felt_safe_and_secure`, `no_difficulty_going_to_and_from_work`, `provided_with_allowance`, `allowance_amount`, `achievement_1_description`, `achievement_1_rating`, `achievement_2_description`, `achievement_2_rating`, `achievement_3_description`, `achievement_3_rating`, `achievement_4_description`, `achievement_4_rating`, `achievement_5_description`, `achievement_5_rating`, `overall_training_experience`, `improvement_suggestion`) VALUES
(5, 62, 'Janna Lyn Rolls', 'BS Information Technology - 3rd Year', 'StructureSync IT', 'OJT - Intern', 'IT Section', 'Software Development', 'Jane Smith', '6 months', 4383, 1, 1, 1, 1, 1, 1, 0, '', 'Completed project A', 100, 'Assisted in project B', 100, 'Learned new technologies', 100, 'Improved coding skills', 100, 'Participated in team meetings', 100, 'Excellent', 'More hands-on tasks');

-- --------------------------------------------------------

--
-- Table structure for table `student_file_dtr_tbl`
--

CREATE TABLE `student_file_dtr_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `dtr_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_file_dtr_tbl`
--

INSERT INTO `student_file_dtr_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`, `dtr_status`) VALUES
(10, 'WEEK15.Jose.LeeLeighnard.pdf', 'student_dtr/WEEK15.Jose.LeeLeighnard.pdf', '2024-06-13 02:11:37', 62, 202210256, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `student_final_report_tbl`
--

CREATE TABLE `student_final_report_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `report_status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_final_report_tbl`
--

INSERT INTO `student_final_report_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`, `report_status`) VALUES
(10, 'WEEK11.Jose.LeeLeighnard.pdf', 'student_final_report/WEEK11.Jose.LeeLeighnard.pdf', '2024-06-09 06:46:13', 62, 202210256, 'Cleared');

-- --------------------------------------------------------

--
-- Table structure for table `student_foundation_week_picture_tbl`
--

CREATE TABLE `student_foundation_week_picture_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_instructor_relationship_tbl`
--

CREATE TABLE `student_instructor_relationship_tbl` (
  `relationship_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `instructor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_instructor_relationship_tbl`
--

INSERT INTO `student_instructor_relationship_tbl` (`relationship_id`, `student_id`, `instructor_id`) VALUES
(35, 62, 22),
(38, 66, 22),
(39, 67, 22),
(41, 68, 22);

-- --------------------------------------------------------

--
-- Table structure for table `student_medical_certificate_tbl`
--

CREATE TABLE `student_medical_certificate_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_medical_certificate_tbl`
--

INSERT INTO `student_medical_certificate_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(2, 'Jose.LeeLeighnard.Exam.pdf', 'student_signed_documents/student_medical_certificate/Jose.LeeLeighnard.Exam.pdf', '2024-06-05 02:35:08', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_portfolio_education_tbl`
--

CREATE TABLE `student_portfolio_education_tbl` (
  `portfolio_education_id` int(11) NOT NULL,
  `education` varchar(255) DEFAULT NULL,
  `school` varchar(255) DEFAULT NULL,
  `year` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_portfolio_skills_tbl`
--

CREATE TABLE `student_portfolio_skills_tbl` (
  `portfolio_skills_id` int(11) NOT NULL,
  `skills` varchar(255) DEFAULT NULL,
  `proficiency` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_profile_picture_tbl`
--

CREATE TABLE `student_profile_picture_tbl` (
  `profile_id` int(11) NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(100) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_profile_picture_tbl`
--

INSERT INTO `student_profile_picture_tbl` (`profile_id`, `image_path`, `file_name`, `user_id`, `timestamp`, `school_id`) VALUES
(31, 'student_profile_photo/pexels-photo-771742.jpeg', 'pexels-photo-771742.jpeg', 62, '2024-06-02 08:22:16', 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_resume_tbl`
--

CREATE TABLE `student_resume_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_resume_tbl`
--

INSERT INTO `student_resume_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(2, 'practiease.png', 'student_signed_documents/resume/practiease.png', '2024-08-07 01:18:29', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_seminar_certificate_tbl`
--

CREATE TABLE `student_seminar_certificate_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_seminar_certificate_tbl`
--

INSERT INTO `student_seminar_certificate_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(29, 'PractiEase-SOW.pdf', 'student_proof_of_evidences/student_seminar_certificate/PractiEase-SOW.pdf', '2024-06-05 13:03:46', 66, 202210312),
(32, 'FLYERS - ComeBack.pdf', 'student_proof_of_evidences/student_seminar_certificate/FLYERS - ComeBack.pdf', '2024-08-06 02:44:09', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_signed_acceptance_letter_tbl`
--

CREATE TABLE `student_signed_acceptance_letter_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_signed_acceptance_letter_tbl`
--

INSERT INTO `student_signed_acceptance_letter_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(2, 'Letter of Acceptance.pdf', 'student_signed_documents/signed_acceptance_letter/Letter of Acceptance.pdf', '2024-08-07 01:21:23', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_signed_application_letter_tbl`
--

CREATE TABLE `student_signed_application_letter_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_signed_application_letter_tbl`
--

INSERT INTO `student_signed_application_letter_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(3, 'Letter of Application.pdf', 'student_signed_documents/signed_application_letter/Letter of Application.pdf', '2024-08-07 01:21:12', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_signed_endorsement_letter_tbl`
--

CREATE TABLE `student_signed_endorsement_letter_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_signed_endorsement_letter_tbl`
--

INSERT INTO `student_signed_endorsement_letter_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(15, 'Letter of Endorsement.pdf', 'student_signed_documents/signed_endorsement_letter/Letter of Endorsement.pdf', '2024-06-05 03:16:43', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_signed_moa_letter_tbl`
--

CREATE TABLE `student_signed_moa_letter_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_signed_parents_consent_letter_tbl`
--

CREATE TABLE `student_signed_parents_consent_letter_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_signed_parents_consent_letter_tbl`
--

INSERT INTO `student_signed_parents_consent_letter_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(10, 'Parent Consent.pdf', 'student_signed_documents/signed_parents_consent_letter/Parent Consent.pdf', '2024-06-05 03:16:25', 62, 202210256);

-- --------------------------------------------------------

--
-- Table structure for table `student_sportsfest_picture_tbl`
--

CREATE TABLE `student_sportsfest_picture_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_sportsfest_picture_tbl`
--

INSERT INTO `student_sportsfest_picture_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`) VALUES
(38, 'WEEK10.Jose.LeeLeighnard.pdf', 'student_proof_of_evidences/student_sportsfest_picture/WEEK10.Jose.LeeLeighnard.pdf', '2024-06-05 13:03:49', 66, 202210312);

-- --------------------------------------------------------

--
-- Table structure for table `student_vaccination_card_tbl`
--

CREATE TABLE `student_vaccination_card_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_weekly_accomplishments_tbl`
--

CREATE TABLE `student_weekly_accomplishments_tbl` (
  `file_id` int(11) NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `weekly_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_weekly_accomplishments_tbl`
--

INSERT INTO `student_weekly_accomplishments_tbl` (`file_id`, `file_name`, `file_path`, `timestamp`, `user_id`, `school_id`, `weekly_status`) VALUES
(3, 'Jose.LeeLeighnard.Exam.pdf', 'student_weekly_accomplishments/Jose.LeeLeighnard.Exam.pdf', '2024-06-09 06:55:32', 62, 202210256, 'Approved'),
(4, 'WEEK2.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK2.Jose.LeeLeighnard.pdf', '2024-06-09 06:56:43', 62, 202210256, 'Approved'),
(5, 'WEEK4.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK4.Jose.LeeLeighnard.pdf', '2024-06-09 06:57:55', 62, 202210256, 'Approved'),
(6, 'WEEK16.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK16.Jose.LeeLeighnard.pdf', '2024-06-09 06:58:43', 62, 202210256, 'Approved'),
(7, 'WEEK3.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK3.Jose.LeeLeighnard.pdf', '2024-06-09 06:59:57', 62, 202210256, 'Approved'),
(8, 'WEEK17.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK17.Jose.LeeLeighnard.pdf', '2024-06-09 07:02:11', 62, 202210256, 'Approved'),
(9, 'WEEK15.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK15.Jose.LeeLeighnard.pdf', '2024-06-09 07:03:09', 62, 202210256, 'Approved'),
(10, 'WEEK1.Jose.LeeLeighnard.pdf', 'student_weekly_accomplishments/WEEK1.Jose.LeeLeighnard.pdf', '2024-06-17 05:41:29', 62, 202210256, 'Approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_credentials_tbl`
--
ALTER TABLE `admin_credentials_tbl`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `certificate_of_completion_tbl`
--
ALTER TABLE `certificate_of_completion_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `employer_id` (`employer_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `employer_credentials_tbl`
--
ALTER TABLE `employer_credentials_tbl`
  ADD PRIMARY KEY (`employer_id`);

--
-- Indexes for table `employer_feedback_tbl`
--
ALTER TABLE `employer_feedback_tbl`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `employer_feedback_tbl_ibfk_1` (`employer_id`),
  ADD KEY `fk_student_id` (`student_id`);

--
-- Indexes for table `instructor_announcement_tbl`
--
ALTER TABLE `instructor_announcement_tbl`
  ADD PRIMARY KEY (`announcement_id`),
  ADD KEY `instructor_announcement_tbl_ibfk_1` (`instructor_id`);

--
-- Indexes for table `instructor_credentials_tbl`
--
ALTER TABLE `instructor_credentials_tbl`
  ADD PRIMARY KEY (`instructor_id`);

--
-- Indexes for table `instructor_requirement_checking_tbl`
--
ALTER TABLE `instructor_requirement_checking_tbl`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_requirement_checking_tbl_ibfk_1` (`instructor_id`),
  ADD KEY `instructor_requirement_checking_tbl_ibfk_2` (`student_id`);

--
-- Indexes for table `student_acquaintance_picture_tbl`
--
ALTER TABLE `student_acquaintance_picture_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_acquaintance_picture_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_barangay_clearance_tbl`
--
ALTER TABLE `student_barangay_clearance_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_barangay_clearance_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_ccs_picture_tbl`
--
ALTER TABLE `student_ccs_picture_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_ccs_picture_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_certificate_of_completion_tbl`
--
ALTER TABLE `student_certificate_of_completion_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_certificate_of_completion_tbl_ibfk_1` (`student_id`),
  ADD KEY `student_certificate_of_completion_tbl_ibfk_2` (`instructor_id`);

--
-- Indexes for table `student_credentials_tbl`
--
ALTER TABLE `student_credentials_tbl`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `student_daily_accomplishments_tbl`
--
ALTER TABLE `student_daily_accomplishments_tbl`
  ADD PRIMARY KEY (`daily_accomplishments_id`),
  ADD KEY `student_daily_accomplishments_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_documentation_tbl`
--
ALTER TABLE `student_documentation_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_dtr_tbl`
--
ALTER TABLE `student_dtr_tbl`
  ADD PRIMARY KEY (`dtr_id`),
  ADD KEY `student_dtr_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_employer_relationship_tbl`
--
ALTER TABLE `student_employer_relationship_tbl`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `student_employer_relationship_tbl_ibfk_1` (`student_id`),
  ADD KEY `student_employer_relationship_tbl_ibfk_2` (`employer_id`);

--
-- Indexes for table `student_exitpoll_tbl`
--
ALTER TABLE `student_exitpoll_tbl`
  ADD PRIMARY KEY (`exitpoll_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `student_file_dtr_tbl`
--
ALTER TABLE `student_file_dtr_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_final_report_tbl`
--
ALTER TABLE `student_final_report_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `student_foundation_week_picture_tbl`
--
ALTER TABLE `student_foundation_week_picture_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_foundation_week_picture_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_instructor_relationship_tbl`
--
ALTER TABLE `student_instructor_relationship_tbl`
  ADD PRIMARY KEY (`relationship_id`),
  ADD KEY `student_instructor_relationship_tbl_ibfk_1` (`student_id`),
  ADD KEY `student_instructor_relationship_tbl_ibfk_2` (`instructor_id`);

--
-- Indexes for table `student_medical_certificate_tbl`
--
ALTER TABLE `student_medical_certificate_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_medical_certificate_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_portfolio_education_tbl`
--
ALTER TABLE `student_portfolio_education_tbl`
  ADD PRIMARY KEY (`portfolio_education_id`),
  ADD KEY `student_portfolio_education_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_portfolio_skills_tbl`
--
ALTER TABLE `student_portfolio_skills_tbl`
  ADD PRIMARY KEY (`portfolio_skills_id`),
  ADD KEY `student_portfolio_skills_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_profile_picture_tbl`
--
ALTER TABLE `student_profile_picture_tbl`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `student_profile_picture_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_resume_tbl`
--
ALTER TABLE `student_resume_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_resume_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_seminar_certificate_tbl`
--
ALTER TABLE `student_seminar_certificate_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_seminar_certificate_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_signed_acceptance_letter_tbl`
--
ALTER TABLE `student_signed_acceptance_letter_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_signed_acceptance_letter_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_signed_application_letter_tbl`
--
ALTER TABLE `student_signed_application_letter_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_signed_application_letter_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_signed_endorsement_letter_tbl`
--
ALTER TABLE `student_signed_endorsement_letter_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_signed_endorsement_letter_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_signed_moa_letter_tbl`
--
ALTER TABLE `student_signed_moa_letter_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_signed_moa_letter_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_signed_parents_consent_letter_tbl`
--
ALTER TABLE `student_signed_parents_consent_letter_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_signed_parents_consent_letter_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_sportsfest_picture_tbl`
--
ALTER TABLE `student_sportsfest_picture_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_sportsfest_picture_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_vaccination_card_tbl`
--
ALTER TABLE `student_vaccination_card_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `student_vaccination_card_tbl_ibfk_1` (`user_id`);

--
-- Indexes for table `student_weekly_accomplishments_tbl`
--
ALTER TABLE `student_weekly_accomplishments_tbl`
  ADD PRIMARY KEY (`file_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_credentials_tbl`
--
ALTER TABLE `admin_credentials_tbl`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certificate_of_completion_tbl`
--
ALTER TABLE `certificate_of_completion_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `employer_credentials_tbl`
--
ALTER TABLE `employer_credentials_tbl`
  MODIFY `employer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `employer_feedback_tbl`
--
ALTER TABLE `employer_feedback_tbl`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `instructor_announcement_tbl`
--
ALTER TABLE `instructor_announcement_tbl`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `instructor_credentials_tbl`
--
ALTER TABLE `instructor_credentials_tbl`
  MODIFY `instructor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `instructor_requirement_checking_tbl`
--
ALTER TABLE `instructor_requirement_checking_tbl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `student_acquaintance_picture_tbl`
--
ALTER TABLE `student_acquaintance_picture_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_barangay_clearance_tbl`
--
ALTER TABLE `student_barangay_clearance_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_ccs_picture_tbl`
--
ALTER TABLE `student_ccs_picture_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `student_certificate_of_completion_tbl`
--
ALTER TABLE `student_certificate_of_completion_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_credentials_tbl`
--
ALTER TABLE `student_credentials_tbl`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `student_daily_accomplishments_tbl`
--
ALTER TABLE `student_daily_accomplishments_tbl`
  MODIFY `daily_accomplishments_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `student_documentation_tbl`
--
ALTER TABLE `student_documentation_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `student_dtr_tbl`
--
ALTER TABLE `student_dtr_tbl`
  MODIFY `dtr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `student_employer_relationship_tbl`
--
ALTER TABLE `student_employer_relationship_tbl`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `student_exitpoll_tbl`
--
ALTER TABLE `student_exitpoll_tbl`
  MODIFY `exitpoll_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `student_file_dtr_tbl`
--
ALTER TABLE `student_file_dtr_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `student_final_report_tbl`
--
ALTER TABLE `student_final_report_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_foundation_week_picture_tbl`
--
ALTER TABLE `student_foundation_week_picture_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `student_instructor_relationship_tbl`
--
ALTER TABLE `student_instructor_relationship_tbl`
  MODIFY `relationship_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `student_medical_certificate_tbl`
--
ALTER TABLE `student_medical_certificate_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_portfolio_education_tbl`
--
ALTER TABLE `student_portfolio_education_tbl`
  MODIFY `portfolio_education_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `student_portfolio_skills_tbl`
--
ALTER TABLE `student_portfolio_skills_tbl`
  MODIFY `portfolio_skills_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `student_profile_picture_tbl`
--
ALTER TABLE `student_profile_picture_tbl`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `student_resume_tbl`
--
ALTER TABLE `student_resume_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_seminar_certificate_tbl`
--
ALTER TABLE `student_seminar_certificate_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `student_signed_acceptance_letter_tbl`
--
ALTER TABLE `student_signed_acceptance_letter_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_signed_application_letter_tbl`
--
ALTER TABLE `student_signed_application_letter_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `student_signed_endorsement_letter_tbl`
--
ALTER TABLE `student_signed_endorsement_letter_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `student_signed_moa_letter_tbl`
--
ALTER TABLE `student_signed_moa_letter_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_signed_parents_consent_letter_tbl`
--
ALTER TABLE `student_signed_parents_consent_letter_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `student_sportsfest_picture_tbl`
--
ALTER TABLE `student_sportsfest_picture_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `student_vaccination_card_tbl`
--
ALTER TABLE `student_vaccination_card_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_weekly_accomplishments_tbl`
--
ALTER TABLE `student_weekly_accomplishments_tbl`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `certificate_of_completion_tbl`
--
ALTER TABLE `certificate_of_completion_tbl`
  ADD CONSTRAINT `certificate_of_completion_tbl_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `employer_credentials_tbl` (`employer_id`),
  ADD CONSTRAINT `student_id` FOREIGN KEY (`student_id`) REFERENCES `student_credentials_tbl` (`user_id`);

--
-- Constraints for table `employer_feedback_tbl`
--
ALTER TABLE `employer_feedback_tbl`
  ADD CONSTRAINT `employer_feedback_tbl_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `employer_credentials_tbl` (`employer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_id` FOREIGN KEY (`student_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `instructor_announcement_tbl`
--
ALTER TABLE `instructor_announcement_tbl`
  ADD CONSTRAINT `instructor_announcement_tbl_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructor_credentials_tbl` (`instructor_id`) ON DELETE CASCADE;

--
-- Constraints for table `instructor_requirement_checking_tbl`
--
ALTER TABLE `instructor_requirement_checking_tbl`
  ADD CONSTRAINT `instructor_requirement_checking_tbl_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `instructor_credentials_tbl` (`instructor_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `instructor_requirement_checking_tbl_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_acquaintance_picture_tbl`
--
ALTER TABLE `student_acquaintance_picture_tbl`
  ADD CONSTRAINT `student_acquaintance_picture_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_barangay_clearance_tbl`
--
ALTER TABLE `student_barangay_clearance_tbl`
  ADD CONSTRAINT `student_barangay_clearance_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_ccs_picture_tbl`
--
ALTER TABLE `student_ccs_picture_tbl`
  ADD CONSTRAINT `student_ccs_picture_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_certificate_of_completion_tbl`
--
ALTER TABLE `student_certificate_of_completion_tbl`
  ADD CONSTRAINT `student_certificate_of_completion_tbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_certificate_of_completion_tbl_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructor_credentials_tbl` (`instructor_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_daily_accomplishments_tbl`
--
ALTER TABLE `student_daily_accomplishments_tbl`
  ADD CONSTRAINT `student_daily_accomplishments_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_documentation_tbl`
--
ALTER TABLE `student_documentation_tbl`
  ADD CONSTRAINT `student_documentation_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`);

--
-- Constraints for table `student_dtr_tbl`
--
ALTER TABLE `student_dtr_tbl`
  ADD CONSTRAINT `student_dtr_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_employer_relationship_tbl`
--
ALTER TABLE `student_employer_relationship_tbl`
  ADD CONSTRAINT `student_employer_relationship_tbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_employer_relationship_tbl_ibfk_2` FOREIGN KEY (`employer_id`) REFERENCES `employer_credentials_tbl` (`employer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_exitpoll_tbl`
--
ALTER TABLE `student_exitpoll_tbl`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`);

--
-- Constraints for table `student_file_dtr_tbl`
--
ALTER TABLE `student_file_dtr_tbl`
  ADD CONSTRAINT `student_file_dtr_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`);

--
-- Constraints for table `student_final_report_tbl`
--
ALTER TABLE `student_final_report_tbl`
  ADD CONSTRAINT `student_final_report_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`);

--
-- Constraints for table `student_foundation_week_picture_tbl`
--
ALTER TABLE `student_foundation_week_picture_tbl`
  ADD CONSTRAINT `student_foundation_week_picture_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_instructor_relationship_tbl`
--
ALTER TABLE `student_instructor_relationship_tbl`
  ADD CONSTRAINT `student_instructor_relationship_tbl_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `student_instructor_relationship_tbl_ibfk_2` FOREIGN KEY (`instructor_id`) REFERENCES `instructor_credentials_tbl` (`instructor_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `student_medical_certificate_tbl`
--
ALTER TABLE `student_medical_certificate_tbl`
  ADD CONSTRAINT `student_medical_certificate_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_portfolio_education_tbl`
--
ALTER TABLE `student_portfolio_education_tbl`
  ADD CONSTRAINT `student_portfolio_education_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_portfolio_skills_tbl`
--
ALTER TABLE `student_portfolio_skills_tbl`
  ADD CONSTRAINT `student_portfolio_skills_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_profile_picture_tbl`
--
ALTER TABLE `student_profile_picture_tbl`
  ADD CONSTRAINT `student_profile_picture_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_resume_tbl`
--
ALTER TABLE `student_resume_tbl`
  ADD CONSTRAINT `student_resume_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_seminar_certificate_tbl`
--
ALTER TABLE `student_seminar_certificate_tbl`
  ADD CONSTRAINT `student_seminar_certificate_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_signed_acceptance_letter_tbl`
--
ALTER TABLE `student_signed_acceptance_letter_tbl`
  ADD CONSTRAINT `student_signed_acceptance_letter_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_signed_application_letter_tbl`
--
ALTER TABLE `student_signed_application_letter_tbl`
  ADD CONSTRAINT `student_signed_application_letter_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_signed_endorsement_letter_tbl`
--
ALTER TABLE `student_signed_endorsement_letter_tbl`
  ADD CONSTRAINT `student_signed_endorsement_letter_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_signed_moa_letter_tbl`
--
ALTER TABLE `student_signed_moa_letter_tbl`
  ADD CONSTRAINT `student_signed_moa_letter_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_signed_parents_consent_letter_tbl`
--
ALTER TABLE `student_signed_parents_consent_letter_tbl`
  ADD CONSTRAINT `student_signed_parents_consent_letter_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_sportsfest_picture_tbl`
--
ALTER TABLE `student_sportsfest_picture_tbl`
  ADD CONSTRAINT `student_sportsfest_picture_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_vaccination_card_tbl`
--
ALTER TABLE `student_vaccination_card_tbl`
  ADD CONSTRAINT `student_vaccination_card_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_weekly_accomplishments_tbl`
--
ALTER TABLE `student_weekly_accomplishments_tbl`
  ADD CONSTRAINT `student_weekly_accomplishments_tbl_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `student_credentials_tbl` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
