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

('吳庭軒', 'Ung, Ting Hin', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/吳庭軒.jpg'),
('楊天朗', 'Yeung, Tin Long', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/楊天朗.jpg'),
('鄭善瑜', 'Cheng, Sin Yu', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/鄭善瑜.jpg'),
('曾玄彬', 'Tsang, Alan', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/曾玄彬.jpg'),
('黃俊熹', 'Wong, Chun Hei', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/黃俊熹.jpg'),
('鄭保怡', 'Cheng, Po Yee', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/鄭保怡.jpg'),
('李卓謙', 'Lee, Cheuk Him', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/李卓謙.jpg'),
('鄭喬澧', 'Cheng, Kiu Lai', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/鄭喬澧.jpg'),
('黃志言', 'Wong, Chi Yin', 'NULL', 'M', '恩誠2', 'Active', 'images/stu/黃志言.jpg'),

('', 'Hyland, Alexander Carl Casagan', 'NULL', 'M', '信望1', 'Active', 'images/stu/Alexander.jpg'),
('陳枳如', 'Chan, Chi Yu', 'NULL', 'F', '信望1', 'Active', 'images/stu/陳枳如.jpg'),
('周曉瑜', 'Chow, Hiu Yu', 'NULL', 'F', '信望1', 'Active', 'images/stu/周曉瑜.jpg'),
('徐浚熙', 'Tsui, Chun Hei', 'NULL', 'M', '信望1', 'Active', 'images/stu/徐浚熙.jpg'),
('陳泰軒', 'Chen, Tai Hin', 'NULL', 'M', '信望1', 'Active', 'images/stu/陳泰軒.jpg'),
('王樂林', 'Wong, Lok Lam', 'NULL', 'M', '信望1', 'Active', 'images/stu/王樂林.jpg'),
('盧斯嵐', 'Lo, Sze Nam', 'NULL', 'F', '信望1', 'Active', 'images/stu/盧斯嵐.jpg'),
('吳卓彥', 'Ng, Cheuk Yin', 'NULL', 'M', '信望1', 'Active', 'images/stu/吳卓彥.jpg'),
('陳映藍', 'Chan, Ying Laam', 'NULL', 'F', '信望1', 'Active', 'images/stu/陳映藍.jpg'),

('郭祐男', 'Kwok, Yau Nam', 'NULL', 'M', '信望2', 'Active', 'images/stu/郭祐男.jpg'),
('林家樂', 'Lam, Ka Lok', 'NULL', 'M', '信望2', 'Active', 'images/stu/林家樂.jpg'),
('容景熹', 'Yung, King Hey', 'NULL', 'M', '信望2', 'Active', 'images/stu/容景熹.jpg'),
('于靜妍', 'Yu, Ching Yin', 'NULL', 'F', '信望2', 'Active', 'images/stu/于靜妍.jpg'),
('郭俊賢', 'Kwok, Chun Yin', 'NULL', 'M', '信望2', 'Active', 'images/stu/郭俊賢.jpg'),
('余信堯', 'Yu, Shun Yiu', 'NULL', 'M', '信望2', 'Active', 'images/stu/余信堯.jpg'),
('劉惜緣', 'Lau, Sik Yuen', 'NULL', 'F', '信望2', 'Active', 'images/stu/劉惜緣.jpg'),
('劉倬熙', 'Lau, Cheuk Hei', 'NULL', 'M', '信望2', 'Active', 'images/stu/劉倬熙.jpg'),
('何沁悠', 'Ho, Sum Yau Alice', 'NULL', 'F', '信望2', 'Active', 'images/stu/何沁悠.jpg'),

('徐泓智', 'Tsui, Wang Chi', 'NULL', 'M', '信望3', 'Active', 'images/stu/徐泓智.jpg'),
('袁珮淇', 'Yuen, Pui Ki', 'NULL', 'F', '信望3', 'Active', 'images/stu/袁珮淇.jpg'),
('蘇楚然', 'So, Cho Yin', 'NULL', 'M', '信望3', 'Active', 'images/stu/蘇楚然.jpg'),
('梁栢豪', 'Leung, Pak Ho', 'NULL', 'M', '信望3', 'Active', 'images/stu/梁栢豪.jpg'),
('梁恬朗', 'leung, Tim Long', 'NULL', 'M', '信望3', 'Active', 'images/stu/梁恬朗.jpg'),
('鍾思博', 'Chung, Sze Pok', 'NULL', 'M', '信望3', 'Active', 'images/stu/鍾思博.jpg'),
('陳柏穎', 'Chan, Pak  Wing', 'NULL', 'F', '信望3', 'Active', 'images/stu/陳柏穎.jpg'),
('陳泓迪', 'Chan, Wang Dik', 'NULL', 'M', '信望3', 'Active', 'images/stu/陳泓迪.jpg'),

('麥爾諾', 'Mak, Yi Nok', 'NULL', 'M', '信望4', 'Active', 'images/stu/吳庭軒.jpg'),
('高戩廷', 'Ko, Chin Ting', 'NULL', 'M', '信望4', 'Active', 'images/stu/楊天朗.jpg'),
('許羽軒', 'Hui, Yu Hin', 'NULL', 'M', '信望4', 'Active', 'images/stu/鄭善瑜.jpg'),
('吳承臻', 'Ng, Shing Chun', 'NULL', 'M', '信望4', 'Active', 'images/stu/曾玄彬.jpg'),
('呂曉峰', 'Lui, Hiu Fung', 'NULL', 'M', '信望4', 'Active', 'images/stu/黃俊熹.jpg'),
('蔡瑩', 'Choy, Ying', 'NULL', 'F', '信望4', 'Active', 'images/stu/鄭保怡.jpg'),
('陳樂兒', 'Chan, Lok Yee', 'NULL', 'F', '信望4', 'Active', 'images/stu/李卓謙.jpg'),
('冼俊賢', 'Sin, Chun Yin', 'NULL', 'M', '信望4', 'Active', 'images/stu/鄭喬澧.jpg'),
('麥仲諭', 'Mak, Chung Yu', 'NULL', 'M', '信望4', 'Active', 'images/stu/黃志言.jpg'),

('李昫賢', 'Lee, Hui Yin Ethan', 'NULL', 'M', '和平1', 'Active', 'images/stu/李昫賢.jpg'),
('', 'Stephens, Viliame Lutumailagi', 'NULL', 'M', '和平1', 'Active', 'images/stu/Stephens.jpg'),
('李浠鉖', 'Li, Hei Tung', 'NULL', 'F', '和平1', 'Active', 'images/stu/李浠鉖.jpg'),
('陳逸朗', 'Chan, Yat Long', 'NULL', 'M', '和平1', 'Active', 'images/stu/陳逸朗.jpg'),
('陳治仁', 'Chen, Chi Yan Jerry', 'NULL', 'M', '和平1', 'Active', 'images/stu/陳治仁.jpg'),
('李昆達', 'Li, Kunda Linda', 'NULL', 'F', '和平1', 'Active', 'images/stu/李昆達.jpg'),
('莊雅晴', 'Chuang, Nga Ching', 'NULL', 'F', '和平1', 'Active', 'images/stu/莊雅晴.jpg'),
('謝元暘', 'Tse Yuen Yeung', 'NULL', 'M', '和平1', 'Active', 'images/stu/謝元暘.jpg'),
('李浚弘', 'Li, Jun Hong', 'NULL', 'M', '和平1', 'Active', 'images/stu/李浚弘.jpg'),
('單妙蓮', 'SIN, MIU LIN', 'NULL', 'F', '和平1', 'Active', 'images/stu/單妙蓮.jpg'),

('', 'Naurin, Ullah Zaina Naurin, Ullah Zaina ', 'NULL', 'F', '和平2', 'Active', 'images/stu/Naurin.jpg'),
('林鉦喬', 'Lam, Ching Kiu', 'NULL', 'M', '和平2', 'Active', 'images/stu/林鉦喬.jpg'),
('肖詩樺', 'Xiao, Sze Wa', 'NULL', 'F', '和平2', 'Active', 'images/stu/肖詩樺.jpg'),
('鄭曉彤', 'Cheng, Hiu Tung', 'NULL', 'F', '和平2', 'Active', 'images/stu/鄭曉彤.jpg'),
('朱雪琳', 'Chu, Suet Lam Shirlyn', 'NULL', 'F', '和平2', 'Active', 'images/stu/朱雪琳.jpg'),
('黃弘瀚', 'Wong, Wang Hon Wesley', 'NULL', 'M', '和平2', 'Active', 'images/stu/黃弘瀚.jpg'),
('胡亦琛', 'Wu, Yik Sum Dixon', 'NULL', 'M', '和平2', 'Active', 'images/stu/胡亦琛.jpg'),
('孔政鵬', 'Hung, Ching Pang', 'Pang Pang', 'M', '和平2', 'Active', 'images/stu/孔政鵬.jpg'),
('巢永漮', 'Chau, Brendon Wing Hong', '巢巢', 'M', '和平2', 'Active', 'images/stu/巢永漮.jpg'),
('鍾皓霖', 'Zhong, Haolin', 'NULL', 'M', '和平2', 'Active', 'images/stu/鍾皓霖.jpg'),

('高曉澄', 'Ko, Hiu Ching', 'NULL', 'F', '和平3', 'Active', 'images/stu/高曉澄.jpg'),
('葉思行', 'Yip, Sze Hang', 'NULL', 'M', '和平3', 'Active', 'images/stu/葉思行.jpg'),
('梁月怡', 'Leung, Yuet Yi', 'NULL', 'F', '和平3', 'Active', 'images/stu/梁月怡.jpg'),
('鄭彥滔', 'Cheng, Yin To', 'NULL', 'M', '和平3', 'Active', 'images/stu/鄭彥滔.jpg'),
('余恩誠', 'Yu, Yan Shin', 'NULL', 'M', '和平3', 'Active', 'images/stu/余恩誠.jpg'),
('陳槺霖', 'Chan, Hong Lam', 'NULL', 'M', '和平3', 'Active', 'images/stu/陳槺霖.jpg'),
('陳紫縈', 'Chan, Tsz Ying', 'NULL', 'F', '和平3', 'Active', 'images/stu/陳紫縈.jpg'),
('李偉豪', 'Li, Wai Ho Sam', 'NULL', 'M', '和平3', 'Active', 'images/stu/李偉豪.jpg'),
('吳浩坤', 'Ng, Ho Kwan Jesse', 'NULL', 'M', '和平3', 'Active', 'images/stu/吳浩坤.jpg'),
('黃信一', 'Wong, Elijah', 'NULL', 'M', '和平3', 'Active', 'images/stu/黃信一.jpg'),

('各山', 'Singh, Sangha Gurshan', 'NULL', 'M', '禮智1', 'Active', 'images/stu/各山.jpg'),
('曾慶淳', 'Tsang, Hing Shun', 'NULL', 'M', '禮智1', 'Active', 'images/stu/曾慶淳.jpg'),
('李凱彤', 'Lee, Hoi Tung Alicia', 'NULL', 'F', '禮智1', 'Active', 'images/stu/李凱彤.jpg'),
('張晉硯', 'Cheung, Henry Chun Yin', 'NULL', 'M', '禮智1', 'Active', 'images/stu/張晉硯.jpg'),
('劉彥廷', 'Lau, Clinton', 'NULL', 'M', '禮智1', 'Active', 'images/stu/劉彥廷.jpg'),
('陳昉得', 'Chan, Fong Tak', 'NULL', 'M', '禮智1', 'Active', 'images/stu/陳昉得.jpg'),
('許鵬程', 'Hui, Kurt Martin Robles', 'NULL', 'M', '禮智1', 'Active', 'images/stu/許鵬程.jpg'),
('梁均烺', 'Leung, Kwan Long', 'NULL', 'M', '禮智1', 'Active', 'images/stu/梁均烺.jpg'),
('韓一一', 'Han, Yiyi', 'NULL', 'F', '禮智1', 'Active', 'images/stu/韓一一.jpg'),
('馬嘉駺', 'Ma, Ka Leung', 'NULL', 'M', '禮智1', 'Active', 'images/stu/馬嘉駺.jpg'),

('阮天行', 'Yuen, Tin Hang', 'NULL', 'M', '愛德1', 'Active', 'images/stu/阮天行.jpg'),
('鄺明得', 'Kwong, Ming Tak', 'NULL', 'M', '愛德1', 'Active', 'images/stu/鄺明得.jpg'),
('張誥麟', 'Cheung, Ko Lun', 'NULL', 'M', '愛德1', 'Active', 'images/stu/張誥麟.jpg'),
('鄭靜文', 'Cheng, Ching Man', 'NULL', 'F', '愛德1', 'Active', 'images/stu/鄭靜文.jpg'),
('鄧焯琦', 'Tang, Cheuk Kei', 'NULL', 'F', '愛德1', 'Active', 'images/stu/鄧焯琦.jpg'),
('陳熹橋', 'Chan, Hei Kiu Ethan', 'NULL', 'M', '愛德1', 'Active', 'images/stu/陳熹橋.jpg'),

('陳頌仁', 'Chan, Chung Yan', 'NULL', 'M', '愛德2', 'Active', 'images/stu/陳頌仁.jpg'),
('葉凱琳', 'Yip, Danika', 'NULL', 'F', '愛德2', 'Active', 'images/stu/葉凱琳.jpg'),
('陳樹兆', 'Tran, Shu Shau', '樹兆', 'M', '愛德2', 'Active', 'images/stu/陳樹兆.jpg'),
('曾振軒', 'Tsang, Henry', 'NULL', 'M', '愛德2', 'Active', 'images/stu/曾振軒.jpg'),
('陳思龍', 'Chan, Sze Lung', 'NULL', 'M', '愛德2', 'Active', 'images/stu/陳思龍.jpg'),
('吳梓宇', 'Ng, Pierson Sy', 'NULL', 'M', '愛德2', 'Active', 'images/stu/吳梓宇.jpg'),
('唐子濂', 'Tong, Tsz Lim', 'NULL', 'M', '愛德2', 'Active', 'images/stu/唐子濂.jpg'),
('叶子睿', 'Ye, Zirui', 'NULL', 'M', '愛德2', 'Active', 'images/stu/叶子睿.jpg'),

('陳明', 'David Wilson', 'Dave', 'Male', 'Class A', 'Graduated', 'images/stu/陳明.jpg');



