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
  `gender` enum('M','F') NOT NULL,
  `class` varchar(50) DEFAULT NULL COMMENT 'Class/Group',
  `status` enum('Active','Inactive','Graduated') DEFAULT 'Active',
  `image_path` varchar(255) DEFAULT NULL COMMENT 'Path to student image',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Insert sample data (optional - you can remove this section if you don't want sample data)
INSERT INTO `students` (`name_chi`, `name_eng`, `nickname`, `gender`, `class`, `status`, `image_path`) VALUES
('陳浩賢', 'Chan, Jayvon', 'NULL', 'M', '喜樂1', 'Active', 'images/stu/陳浩賢.jpg'),
('張俊生', 'Cheung, Chun Sang', 'NULL', 'M', '喜樂1', 'Active', 'images/stu/張俊生.jpg'),
('曾家業', 'Tsang, Ka Yip', 'NULL', 'M', '喜樂1', 'Active', 'images/stu/曾家業.jpg'),
('陳珮詩', 'Chan, Pui Sze', 'NULL', 'F', '喜樂1', 'Active', 'images/stu/陳珮詩.jpg'),
('劉華一', 'Liu, Huayi', 'NULL', 'M', '喜樂1', 'Active', 'images/stu/劉華一.jpg'),
('李俊凱', 'LI CHUN HOI', 'NULL', 'M', '喜樂1', 'Active', 'images/stu/李俊凱.jpg'),

('曾婉鈴', 'Tsang, Yuen Ling', 'NULL', 'F', '喜樂2', 'Active', 'images/stu/曾婉鈴.jpg'),
('林國謙', 'Lin, Kwok Him', 'NULL', 'M', '喜樂2', 'Active', 'images/stu/林國謙.jpg'),
('黃淳軒', 'Wong, Shun Hin', 'Aaron', 'M', '喜樂2', 'Active', 'images/stu/黃淳軒.jpg'),
('', 'Gurung Raya', 'NULL', 'F', '喜樂2', 'Active', 'images/stu/Raya.jpg'),
('', 'Bains Priyansh', 'NULL', 'M', '喜樂2', 'Active', 'images/stu/Priyansh.jpg'),
('吳倬維', 'Ng, Cheuk Wai', '維維', 'M', '喜樂2', 'Active', 'images/stu/吳倬維.jpg'),

('王梓雯', 'Wong, Tsz Man', 'NULL', 'F', '恩誠1', 'Active', 'images/stu/王梓雯.jpg'),
('吳欣蕾', 'Wu, Xinlei', 'NULL', 'F', '恩誠1', 'Active', 'images/stu/吳欣蕾.jpg'),
('袁梓琳', 'Yuen, Tsz Lam', 'NULL', 'F', '恩誠1', 'Active', 'images/stu/袁梓琳.jpg'),
('藍馨怡', 'Lam, Hing Yi', 'NULL', 'F', '恩誠1', 'Active', 'images/stu/藍馨怡.jpg'),
('阮文謙', 'Yuen, Man Him', 'Lucas', 'M', '恩誠1', 'Active', 'images/stu/阮文謙.jpg'),
('鄧宇彬', 'Tang, Yu Bin', 'NULL', 'M', '恩誠1', 'Active', 'images/stu/鄧宇彬.jpg'),
('陳思齊', 'Chan, Sze Chai', 'NULL', 'M', '恩誠1', 'Active', 'images/stu/陳思齊.jpg'),
('余守仁', 'Yu, Wallace Sau Yan', 'NULL', 'M', '恩誠1', 'Active', 'images/stu/余守仁.jpg'),
('', 'Rawat, Vania', 'NULL', 'F', '恩誠1', 'Active', 'images/stu/Vania.jpg'),

('陳明', 'David Wilson', 'Dave', 'Male', 'Class A', 'Graduated', 'images/stu/陳明.jpg');

 

