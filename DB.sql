-- CrsEdu Database Setup

-- Create database
CREATE DATABASE IF NOT EXISTS `CrsEdu` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `CrsEdu`;

-- Create students table
CREATE TABLE IF NOT EXISTS `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name_chi` varchar(100) NOT NULL COMMENT 'Chinese Name',
  `name_eng` varchar(100) NOT NULL COMMENT 'English Name',
  `nickname` varchar(50) DEFAULT NULL,
  `short_name` varchar(50) DEFAULT NULL COMMENT 'Short name or symbol',
  `gender` enum('M','F') NOT NULL,
  `class` varchar(50) DEFAULT NULL COMMENT 'Class/Group',
  `status` enum('Active','Inactive','Graduated') DEFAULT 'Active',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'Path to student image',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- Create daily reports table
CREATE TABLE IF NOT EXISTS `daily_reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL COMMENT 'Reference to student',
  `report_date` date NOT NULL COMMENT 'Date of the report',
  `content` text NOT NULL COMMENT 'Daily description from teacher',
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `report_date` (`report_date`),
  CONSTRAINT `fk_daily_reports_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Daily student reports from teachers';

-- Create absence records table
CREATE TABLE IF NOT EXISTS `absence_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL COMMENT 'Reference to student',
  `absence_date` date NOT NULL COMMENT 'Date of absence',
  `absence_type` enum('Sick Leave','Personal Leave','Medical Appointment','Outside Hong Kong') NOT NULL COMMENT 'Type of absence',
  PRIMARY KEY (`id`),
  KEY `student_id` (`student_id`),
  KEY `absence_date` (`absence_date`),
  KEY `absence_type` (`absence_type`),
  CONSTRAINT `fk_absence_records_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Student absence records';

