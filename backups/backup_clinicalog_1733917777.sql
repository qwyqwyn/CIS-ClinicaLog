

CREATE TABLE IF NOT EXISTS `adminnotifs` (
  `notif_id` int(11) NOT NULL AUTO_INCREMENT,
  `notif_patid` int(11) NOT NULL,
  `notif_message` text NOT NULL,
  `notif_status` enum('unread','read') DEFAULT 'unread',
  `notif_date_added` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`notif_id`),
  KEY `notif_patid` (`notif_patid`),
  CONSTRAINT `adminnotifs_ibfk_1` FOREIGN KEY (`notif_patid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `adminnotifs` (`notif_id`, `notif_patid`, `notif_message`, `notif_status`, `notif_date_added`) VALUES ('18','73','Inserted New Medical Record 5 cybersecurity - cryptography.pdf','unread','2024-12-08 20:44:38');
INSERT IGNORE INTO `adminnotifs` (`notif_id`, `notif_patid`, `notif_message`, `notif_status`, `notif_date_added`) VALUES ('19','73','Inserted New Medical Record bWAPP_intro.pdf','unread','2024-12-08 20:52:06');
INSERT IGNORE INTO `adminnotifs` (`notif_id`, `notif_patid`, `notif_message`, `notif_status`, `notif_date_added`) VALUES ('20','73','Inserted New Medical Record information-security-plan.pdf','unread','2024-12-08 20:53:30');
INSERT IGNORE INTO `adminnotifs` (`notif_id`, `notif_patid`, `notif_message`, `notif_status`, `notif_date_added`) VALUES ('21','73','Inserted New Medical Record Arduino_Sensors.pdf','unread','2024-12-08 21:46:28');



CREATE TABLE IF NOT EXISTS `adminusers` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_idnum` char(15) NOT NULL,
  `user_fname` varchar(30) NOT NULL,
  `user_lname` varchar(30) NOT NULL,
  `user_mname` varchar(30) DEFAULT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_position` varchar(50) NOT NULL,
  `user_role` enum('Super Admin','Admin') NOT NULL,
  `user_status` enum('Active','Inactive') NOT NULL,
  `user_dateadded` date DEFAULT NULL,
  `user_profile` varchar(255) DEFAULT NULL,
  `user_password` char(60) NOT NULL,
  `user_code` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_idnum` (`user_idnum`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `adminusers` (`user_id`, `user_idnum`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_position`, `user_role`, `user_status`, `user_dateadded`, `user_profile`, `user_password`, `user_code`) VALUES ('5','ADMIN001','Admin','User','','admin@clinicalog.com','Administrator','Super Admin','Active','2024-12-06','35e3a37ab3e5a98b04b63f4b4c3697fd.jpg','$2y$10$gU4QzVSE93WOAPgem.LQnel4oopFQoMJDfCIhrf7LkqRnmU7EY5RK','0');
INSERT IGNORE INTO `adminusers` (`user_id`, `user_idnum`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_position`, `user_role`, `user_status`, `user_dateadded`, `user_profile`, `user_password`, `user_code`) VALUES ('6','2022-00136','Gwyneth Marie','Hong','Casia','casiagwynethmarie@gmail.com','Campus Nurse','Super Admin','Active','2024-12-06','bb1a00b18f98aee2d51831a851d59c5f.jfif','$2y$10$FqXJ4hgffu15ojo/8dOXRO3t5Q3TRVUQ8EOXv1E0HwuED4GJ8dGB.','0');
INSERT IGNORE INTO `adminusers` (`user_id`, `user_idnum`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_position`, `user_role`, `user_status`, `user_dateadded`, `user_profile`, `user_password`, `user_code`) VALUES ('7','2022-00409','Ashley','Bughao','Buladaco','ashley@gmail.com','Campus Physician','Admin','Active','2024-12-06','bb1a00b18f98aee2d51831a851d59c5f.jfif','$2y$10$UeLGBjgLbcs1MxFXVrJ59OaQ1cirzZGi3xsFXn7Z5OwPpDZF3X1f6','0');
INSERT IGNORE INTO `adminusers` (`user_id`, `user_idnum`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_position`, `user_role`, `user_status`, `user_dateadded`, `user_profile`, `user_password`, `user_code`) VALUES ('8','2022-00473','Jackilyn','Furog','Mancao','jackilyn@gmail.com','Campus Nurse','Admin','Active','2024-12-06','cb043dbdb9180cbcbf78b2d3976db2ac.jfif','$2y$10$BwSzVlL7Cq4vBAfVidgr3O0amwp6qZg3dcx3Cfz49BJXQn2vqyWCm','0');
INSERT IGNORE INTO `adminusers` (`user_id`, `user_idnum`, `user_fname`, `user_lname`, `user_mname`, `user_email`, `user_position`, `user_role`, `user_status`, `user_dateadded`, `user_profile`, `user_password`, `user_code`) VALUES ('9','2022-12121','Christina','Dawa','Dawa','tina@gmail.com','Campus Nurse','Admin','Active','2024-12-08','bb1a00b18f98aee2d51831a851d59c5f.jfif','$2y$10$oYqgEs0/Q3SUVLFRWL1xF.CBJqSq3JqC4ypzgPoFh4ORByWo9sdle','0');



CREATE TABLE IF NOT EXISTS `consultations` (
  `consult_id` int(11) NOT NULL AUTO_INCREMENT,
  `consult_patientid` int(11) NOT NULL,
  `consult_clinician` char(15) DEFAULT NULL,
  `consult_diagnosis` varchar(255) NOT NULL,
  `consult_treatmentnotes` varchar(255) NOT NULL,
  `consult_remark` varchar(255) NOT NULL,
  `consult_date` date DEFAULT NULL,
  PRIMARY KEY (`consult_id`),
  KEY `consult_patientid` (`consult_patientid`),
  CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`consult_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `consultations` (`consult_id`, `consult_patientid`, `consult_clinician`, `consult_diagnosis`, `consult_treatmentnotes`, `consult_remark`, `consult_date`) VALUES ('10','81','','Test','Test','Test','2024-12-07');
INSERT IGNORE INTO `consultations` (`consult_id`, `consult_patientid`, `consult_clinician`, `consult_diagnosis`, `consult_treatmentnotes`, `consult_remark`, `consult_date`) VALUES ('11','75','','test','Test','Test','2024-12-07');
INSERT IGNORE INTO `consultations` (`consult_id`, `consult_patientid`, `consult_clinician`, `consult_diagnosis`, `consult_treatmentnotes`, `consult_remark`, `consult_date`) VALUES ('12','73','ADMIN001','test','test','yest','2024-12-08');
INSERT IGNORE INTO `consultations` (`consult_id`, `consult_patientid`, `consult_clinician`, `consult_diagnosis`, `consult_treatmentnotes`, `consult_remark`, `consult_date`) VALUES ('13','73','ADMIN001','test','test','ntes','2024-12-08');



CREATE TABLE IF NOT EXISTS `medicalrec` (
  `medicalrec_id` int(11) NOT NULL AUTO_INCREMENT,
  `medicalrec_patientid` int(11) NOT NULL,
  `medicalrec_filename` varchar(255) NOT NULL,
  `medicalrec_file` varchar(255) DEFAULT NULL,
  `medicalrec_comment` varchar(255) DEFAULT NULL,
  `medicalrec_dateadded` date DEFAULT NULL,
  `medicalrec_timeadded` time DEFAULT NULL,
  PRIMARY KEY (`medicalrec_id`),
  KEY `medicalrec_patientid` (`medicalrec_patientid`),
  CONSTRAINT `medicalrec_ibfk_1` FOREIGN KEY (`medicalrec_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `medicalrec` (`medicalrec_id`, `medicalrec_patientid`, `medicalrec_filename`, `medicalrec_file`, `medicalrec_comment`, `medicalrec_dateadded`, `medicalrec_timeadded`) VALUES ('73','73','Arduino_Sensors.pdf','fc40bd77885c00de76aae2006e2fb22a','No Comment','2024-12-08','14:46:28');



CREATE TABLE IF NOT EXISTS `medicine` (
  `medicine_id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_name` varchar(100) NOT NULL,
  `medicine_category` varchar(50) NOT NULL,
  PRIMARY KEY (`medicine_id`),
  UNIQUE KEY `medicine_name` (`medicine_name`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `medicine` (`medicine_id`, `medicine_name`, `medicine_category`) VALUES ('43','Acetylcisteine','Respiratory');



CREATE TABLE IF NOT EXISTS `medissued` (
  `mi_id` int(11) NOT NULL AUTO_INCREMENT,
  `mi_medstockid` int(11) NOT NULL,
  `mi_medqty` int(11) NOT NULL,
  `mi_date` date DEFAULT NULL,
  PRIMARY KEY (`mi_id`),
  KEY `mi_medstockid` (`mi_medstockid`),
  CONSTRAINT `medissued_ibfk_1` FOREIGN KEY (`mi_medstockid`) REFERENCES `medstock` (`medstock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `medissued` (`mi_id`, `mi_medstockid`, `mi_medqty`, `mi_date`) VALUES ('19','7','2','2024-12-07');
INSERT IGNORE INTO `medissued` (`mi_id`, `mi_medstockid`, `mi_medqty`, `mi_date`) VALUES ('20','7','1','2024-12-07');
INSERT IGNORE INTO `medissued` (`mi_id`, `mi_medstockid`, `mi_medqty`, `mi_date`) VALUES ('21','8','2','2024-12-08');



CREATE TABLE IF NOT EXISTS `medstock` (
  `medstock_id` int(11) NOT NULL AUTO_INCREMENT,
  `medicine_id` int(11) NOT NULL,
  `medstock_unit` varchar(10) DEFAULT NULL,
  `medstock_qty` int(11) NOT NULL,
  `medstock_dosage` varchar(50) DEFAULT NULL,
  `medstock_dateadded` date DEFAULT NULL,
  `medstock_timeadded` time DEFAULT NULL,
  `medstock_expirationdt` date DEFAULT NULL,
  `medstock_disable` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`medstock_id`),
  KEY `medicine_id` (`medicine_id`),
  CONSTRAINT `medstock_ibfk_1` FOREIGN KEY (`medicine_id`) REFERENCES `medicine` (`medicine_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `medstock` (`medstock_id`, `medicine_id`, `medstock_unit`, `medstock_qty`, `medstock_dosage`, `medstock_dateadded`, `medstock_timeadded`, `medstock_expirationdt`, `medstock_disable`) VALUES ('7','43','Sachet','168','500','2024-12-07','05:45:12','2024-12-30','0');
INSERT IGNORE INTO `medstock` (`medstock_id`, `medicine_id`, `medstock_unit`, `medstock_qty`, `medstock_dosage`, `medstock_dateadded`, `medstock_timeadded`, `medstock_expirationdt`, `medstock_disable`) VALUES ('8','43','Sachet','23','500','2024-12-07','22:43:27','2025-06-20','0');



CREATE TABLE IF NOT EXISTS `pataddresses` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `address_patientid` int(11) NOT NULL,
  `address_region` varchar(100) NOT NULL,
  `address_province` varchar(100) NOT NULL,
  `address_municipality` varchar(100) NOT NULL,
  `address_barangay` varchar(100) NOT NULL,
  `address_prkstrtadd` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `address_patientid` (`address_patientid`),
  CONSTRAINT `pataddresses_ibfk_1` FOREIGN KEY (`address_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('29','72','Region XI','Davao del Norte','Tagum City','Apokon','Prk. White ');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('30','73','Region XI','Davao del Norte','Tagum City','Apokon','Prk. White ');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('31','74','Region XI','Davao de Oro','Nabunturan','Anislagan','Prk. White ');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('32','75','Region XI','Davao del Norte','Tagum City','Pagsabangan','Prk. White ');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('33','76','Region XI','Davao del Norte','Tagum City','Apokon','Prk. White ');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('35','79','Region XI','Davao de Oro','Pantukan','Magnaga','Purok 9 Kawayan');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('36','81','Region XI','Davao de Oro','Pantukan','Kingking','Purok 9 Kawayan, Magnaga');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('37','82','Region XI','Davao del Norte','Tagum City','Apokon','Purok 9 Kawayan, Magnaga');
INSERT IGNORE INTO `pataddresses` (`address_id`, `address_patientid`, `address_region`, `address_province`, `address_municipality`, `address_barangay`, `address_prkstrtadd`) VALUES ('38','83','Region II','Isabela','Maconacon','Eleonor (Pob.)','Purok 9 Kawayan, Magnaga');



CREATE TABLE IF NOT EXISTS `patemergencycontacts` (
  `emcon_contactid` int(11) NOT NULL AUTO_INCREMENT,
  `emcon_patientid` int(11) NOT NULL,
  `emcon_conname` varchar(100) NOT NULL,
  `emcon_relationship` varchar(50) NOT NULL,
  `emcon_connum` varchar(12) NOT NULL,
  PRIMARY KEY (`emcon_contactid`),
  KEY `emcon_patientid` (`emcon_patientid`),
  CONSTRAINT `patemergencycontacts_ibfk_1` FOREIGN KEY (`emcon_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('26','72','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('27','73','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('28','74','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('29','75','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('30','76','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('32','79','Mary Venusa D Casia','Mother','09067796197');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('33','81','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('34','82','','','');
INSERT IGNORE INTO `patemergencycontacts` (`emcon_contactid`, `emcon_patientid`, `emcon_conname`, `emcon_relationship`, `emcon_connum`) VALUES ('35','83','','','');



CREATE TABLE IF NOT EXISTS `patextensions` (
  `exten_id` int(11) NOT NULL AUTO_INCREMENT,
  `exten_patientid` int(11) NOT NULL,
  `exten_idnum` char(11) NOT NULL,
  `exten_role` varchar(100) NOT NULL,
  PRIMARY KEY (`exten_id`),
  UNIQUE KEY `exten_idnum` (`exten_idnum`),
  KEY `exten_patientid` (`exten_patientid`),
  CONSTRAINT `patextensions_ibfk_1` FOREIGN KEY (`exten_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE IF NOT EXISTS `patfaculties` (
  `faculty_id` int(11) NOT NULL AUTO_INCREMENT,
  `faculty_patientid` int(11) NOT NULL,
  `faculty_idnum` char(11) NOT NULL,
  `faculty_college` varchar(100) NOT NULL,
  `faculty_depart` varchar(100) NOT NULL,
  `faculty_role` varchar(100) NOT NULL,
  PRIMARY KEY (`faculty_id`),
  UNIQUE KEY `faculty_idnum` (`faculty_idnum`),
  KEY `faculty_patientid` (`faculty_patientid`),
  CONSTRAINT `patfaculties_ibfk_1` FOREIGN KEY (`faculty_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `patfaculties` (`faculty_id`, `faculty_patientid`, `faculty_idnum`, `faculty_college`, `faculty_depart`, `faculty_role`) VALUES ('23','79','2022-00136','College of Teacher Education and Technology','N/A','Instructor');



CREATE TABLE IF NOT EXISTS `patients` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_lname` varchar(50) NOT NULL,
  `patient_fname` varchar(50) NOT NULL,
  `patient_mname` varchar(50) DEFAULT NULL,
  `patient_dob` date NOT NULL,
  `patient_email` varchar(255) NOT NULL,
  `patient_connum` varchar(12) NOT NULL,
  `patient_sex` enum('Male','Female') NOT NULL,
  `patient_profile` varchar(255) DEFAULT NULL,
  `patient_patienttype` enum('Student','Faculty','Staff','Extension') NOT NULL,
  `patient_dateadded` date DEFAULT NULL,
  `patient_password` varchar(60) NOT NULL,
  `patient_status` enum('Active','Inactive') NOT NULL,
  `patient_code` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('72','Suico','Precious Lyn','M','2003-12-06','plmsuico00102@usep.edu.ph','09533760327','Female','5e92da7644769d2fa5a812a00b1b3700.png','Student','2024-12-06','$2y$10$qFHwlywNQH66pwdQK9Nx2.Jm8/1.DckNL8ExKOmJAjX15dbL4Y4mC','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('73','Diva','Katrina Arwen Trinity ','L','2003-12-06','katldiva00248@usep.edu.ph','09533760327','Female','3a645ff777c26f9191c17e290005a132.jfif','Student','2024-12-06','$2y$10$yX0DTupTIQgazjmYuwsDMuAYPLj.uR.CEqPebxSeFBlvYfgZgbLkW','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('74','Tabacon','Christeline Jane','Maluya','2003-12-06','cjmtabacon00103@usep.edu.ph','09533760327','Female','','Student','2024-12-06','$2y$10$7IXLi1ud9VS7DQ2swsCM3OLos5wmdJ.dqoIX3A7Rdxd40.mnUk2pu','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('75','Bughao','Alexis Nicole','Mission','2004-12-06','anmbughao00111@usep.edu.ph','09533760327','Female','','Student','2024-12-06','$2y$10$g.eg0b1a89bm6VYU6iaRterset0pqfKcnMTpTjs6FJhEaux/Vd8rq','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('76','Misa','Khizzea Zoe','T','2002-12-06','kztmisa00275@usep.edu.ph','09533760327','Female','','Student','2024-12-06','$2y$10$alvu32iWmbwcoCvRFDU.ceh6K1qjUXAbfn4zxpPY1JcDzB5ZApU8i','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('79','Casia','Gwyneth Marie','Dawa','2003-02-12','gmdcasia00136@usep.edu.ph','09533760327','Female','37667f598c33037bcc945f4cefdb4550.png','Faculty','2024-12-06','$2y$10$QkTEsnJgdUFCt/guZMIcouIT90Z3TQgm9Ot8sk5njoYPF/mHGi7iG','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('80','Casia','Gwyneth Marie','','2003-12-22','casiagwyneth@gmail.com','09533760327','Female','','Faculty','2024-12-06','$2y$10$32ej2N/Je65d82yAv/h/cOQLftx5ZKaI5Gf9e8PhtjFb6q.1/l5HO','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('81','Casia','Gwyneth Marie','Buladaco','2001-12-12','casiagwyneth@gmail.com','09533760327','Female','','Student','2024-12-06','$2y$10$REgkAz7SazM1l.BulIWTye9enOzURTKUn8Bx5tyXwHt/0qOvuujWi','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('82','Casia','Gwyneth Marie','Buladaco','2001-02-21','casiagwy@gmail.com','09533760327','Female','','Student','2024-12-07','$2y$10$9coSbtozxILsEgRI7Tax/uryieBZu98SFG2Dnn6yeRxv9X/DB8DMK','Active','0');
INSERT IGNORE INTO `patients` (`patient_id`, `patient_lname`, `patient_fname`, `patient_mname`, `patient_dob`, `patient_email`, `patient_connum`, `patient_sex`, `patient_profile`, `patient_patienttype`, `patient_dateadded`, `patient_password`, `patient_status`, `patient_code`) VALUES ('83','Casia','Gwyneth Marie','Buladaco','2001-02-02','casianeth@gmail.com','09533760327','Female','597795d8f380573b48cc4c5ec3c1c09b.jpg','Student','2024-12-08','$2y$10$VycB2RXyjpFgmng4D93OVemS1id3ag.fu68tE9pBj.HNvrQ1vb5Z2','Active','0');



CREATE TABLE IF NOT EXISTS `patstaffs` (
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_patientid` int(11) NOT NULL,
  `staff_idnum` char(11) NOT NULL,
  `staff_office` varchar(100) NOT NULL,
  `staff_role` varchar(100) NOT NULL,
  PRIMARY KEY (`staff_id`),
  UNIQUE KEY `staff_idnum` (`staff_idnum`),
  KEY `staff_patientid` (`staff_patientid`),
  CONSTRAINT `patstaffs_ibfk_1` FOREIGN KEY (`staff_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE IF NOT EXISTS `patstudents` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_idnum` char(11) NOT NULL,
  `student_patientid` int(11) NOT NULL,
  `student_program` varchar(100) NOT NULL,
  `student_major` varchar(100) DEFAULT NULL,
  `student_year` int(11) NOT NULL,
  `student_section` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  UNIQUE KEY `student_idnum` (`student_idnum`),
  KEY `student_patientid` (`student_patientid`),
  CONSTRAINT `patstudents_ibfk_1` FOREIGN KEY (`student_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('11','2022-00102','72','Bachelor of Science in Information Technology (BSIT)','Information Security','3','3IT');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('12','2022-00248','73','Bachelor of Science in Information Technology (BSIT)','Information Security','3','3IT');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('13','2022-00103','74','Bachelor of Science in Information Technology (BSIT)','Information Security','3','3IT');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('14','2022-00111','75','Bachelor of Science in Information Technology (BSIT)','Information Security','3','3IT');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('15','2022-00275','76','Bachelor of Science in Information Technology (BSIT)','Information Security','3','3IT');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('16','2022-00136','81','Bachelor of Science in Information Technology (BSIT)','Information Security','3','3IT');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('17','2022-001','82','Bachelor of Secondary Education (BSEd)','English','2','N/A');
INSERT IGNORE INTO `patstudents` (`student_id`, `student_idnum`, `student_patientid`, `student_program`, `student_major`, `student_year`, `student_section`) VALUES ('18','2021211','83','Bachelor of Secondary Education (BSEd)','Filipino','1','2it');



CREATE TABLE IF NOT EXISTS `prescribemed` (
  `pm_id` int(11) NOT NULL AUTO_INCREMENT,
  `pm_consultid` int(11) NOT NULL,
  `pm_medstockid` int(11) NOT NULL,
  `pm_medqty` int(11) NOT NULL,
  PRIMARY KEY (`pm_id`),
  KEY `pm_consultid` (`pm_consultid`),
  KEY `pm_medstockid` (`pm_medstockid`),
  CONSTRAINT `prescribemed_ibfk_1` FOREIGN KEY (`pm_consultid`) REFERENCES `consultations` (`consult_id`),
  CONSTRAINT `prescribemed_ibfk_2` FOREIGN KEY (`pm_medstockid`) REFERENCES `medstock` (`medstock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `prescribemed` (`pm_id`, `pm_consultid`, `pm_medstockid`, `pm_medqty`) VALUES ('10','10','7','2');
INSERT IGNORE INTO `prescribemed` (`pm_id`, `pm_consultid`, `pm_medstockid`, `pm_medqty`) VALUES ('11','11','7','11');
INSERT IGNORE INTO `prescribemed` (`pm_id`, `pm_consultid`, `pm_medstockid`, `pm_medqty`) VALUES ('12','13','8','2');



CREATE TABLE IF NOT EXISTS `systemlog` (
  `syslog_id` int(11) NOT NULL AUTO_INCREMENT,
  `syslog_userid` varchar(50) DEFAULT NULL,
  `syslog_date` date DEFAULT NULL,
  `syslog_time` time DEFAULT NULL,
  `syslog_action` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`syslog_id`),
  KEY `syslog_userid` (`syslog_userid`),
  CONSTRAINT `systemlog_ibfk_1` FOREIGN KEY (`syslog_userid`) REFERENCES `adminusers` (`user_idnum`)
) ENGINE=InnoDB AUTO_INCREMENT=288 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('1','','2024-12-11','18:29:26','Inserted a new record for user: ADMIN001 admin@clinicalog.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('2','','2024-12-11','18:29:26','Inserted a new record for user: 2022-00136 casiagwynethmarie@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('3','','2024-12-11','18:29:26','Inserted a new record for user: 2022-00409 ashley@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('4','','2024-12-11','18:29:26','Inserted a new record for user: 2022-00473 jackilyn@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('5','','2024-12-11','18:29:26','Inserted a new record for user: 2022-12121 tina@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('6','','2024-12-11','18:29:26','Inserted new medicine: Acetylcisteine (Category: Respiratory)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('7','','2024-12-11','18:29:26','Inserted stock for Medicine ID: 43 (Qty: 168, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('8','','2024-12-11','18:29:26','Inserted stock for Medicine ID: 43 (Qty: 23, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('9','','2024-12-11','18:29:26','Added new patient: Precious Lyn Suico (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('10','','2024-12-11','18:29:26','Added new patient: Katrina Arwen Trinity  Diva (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('11','','2024-12-11','18:29:26','Added new patient: Christeline Jane Tabacon (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('12','','2024-12-11','18:29:26','Added new patient: Alexis Nicole Bughao (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('13','','2024-12-11','18:29:26','Added new patient: Khizzea Zoe Misa (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('14','','2024-12-11','18:29:26','Added new patient: Gwyneth Marie Casia (Type: Faculty)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('15','','2024-12-11','18:29:26','Added new patient: Gwyneth Marie Casia (Type: Faculty)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('16','','2024-12-11','18:29:26','Added new patient: Gwyneth Marie Casia (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('17','','2024-12-11','18:29:26','Added new patient: Gwyneth Marie Casia (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('18','','2024-12-11','18:29:26','Added new patient: Gwyneth Marie Casia (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('215','ADMIN001','2024-12-06','21:57:25','Added new patient: Precious Suico (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('216','ADMIN001','2024-12-06','21:58:25','Updated patient: Precious Lyn N/A Suico (ID: 71)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('217','ADMIN001','2024-12-06','22:00:30','Updated patient: Precious Lyn N/A Suico (ID: 71)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('218','ADMIN001','2024-12-06','22:13:42','Added new patient: Precious Lyn Suico (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('219','ADMIN001','2024-12-06','22:16:11','Added new patient: Katrina Arwen Trinity  Diva (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('220','ADMIN001','2024-12-06','22:18:51','Added new patient: Christeline Jane Tabacon (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('221','ADMIN001','2024-12-06','22:20:37','Added new patient: Alexis Nicole Bughao (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('222','ADMIN001','2024-12-06','22:24:32','Added new patient: Khizzea Zoe Misa (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('223','ADMIN001','2024-12-06','22:35:37','Inserted a new record for user: 2022-00136 casiagwynethmarie@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('224','ADMIN001','2024-12-06','22:40:16','Inserted a new record for user: 2022-00409 ashley@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('225','ADMIN001','2024-12-06','22:42:32','Inserted a new record for user: 2022-00473 jackilyn@gmail.com');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('226','ADMIN001','2024-12-06','22:47:10','Added new patient: Gwyneth Marie Casia (Type: Staff)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('227','ADMIN001','2024-12-06','22:47:46','Added new patient: Gwyneth Marie Casia (Type: Staff)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('228','ADMIN001','2024-12-06','22:57:34','Added new patient: Gwyneth Marie Casia (Type: Faculty)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('229','ADMIN001','2024-12-06','22:58:57','Added new patient: Gwyneth Marie Casia (Type: Faculty)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('234','ADMIN001','2024-12-06','23:31:25','Added new patient: Gwyneth Marie Casia (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('235','ADMIN001','2024-12-07','10:13:59','Created transaction for patient Gwyneth Marie Casia with purpose: Medical Certificate Issuance');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('236','ADMIN001','2024-12-07','10:14:05','Updated transaction for patient Gwyneth Marie Casia with purpose: Medical Certificate Issuance');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('237','ADMIN001','2024-12-07','10:14:10','Updated transaction for patient Gwyneth Marie Casia with purpose: Medical Certificate Issuance');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('238','ADMIN001','2024-12-07','10:14:53','Created transaction for patient Gwyneth Marie Casia with purpose: Dental Check Up & Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('239','ADMIN001','2024-12-07','10:14:57','Updated transaction for patient Gwyneth Marie Casia with purpose: Dental Check Up & Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('240','ADMIN001','2024-12-07','10:15:01','Updated transaction for patient Gwyneth Marie Casia with purpose: Dental Check Up & Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('241','ADMIN001','2024-12-07','12:44:45','Inserted new medicine: Acetylcisteine (Category: Respiratory)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('242','ADMIN001','2024-12-07','12:45:12','Inserted stock for Medicine ID: 43 (Qty: 200, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('243','ADMIN001','2024-12-07','12:45:57','Created transaction for patient Katrina Arwen Trinity  Diva with purpose: Medical Consultation and Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('244','ADMIN001','2024-12-07','12:46:03','Updated transaction for patient Katrina Arwen Trinity  Diva with purpose: Medical Consultation and Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('245','ADMIN001','2024-12-07','12:47:17','Issued 2 units of Acetylcisteine (Stock ID: 7)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('246','ADMIN001','2024-12-07','14:06:40','Inserted Consultation Record (ID: 10) for Patient: Gwyneth Marie Casia');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('247','ADMIN001','2024-12-07','14:07:24','Inserted Consultation Record (ID: 11) for Patient: Alexis Nicole Bughao');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('248','ADMIN001','2024-12-07','14:27:34','Issued 1 units of Acetylcisteine (Stock ID: 7)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('250','ADMIN001','2024-12-07','15:20:25','Updated stock (ID: 7) for Medicine ID: 43 (Qty: 184, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('251','ADMIN001','2024-12-07','15:21:42','Updated stock (ID: 7) for Medicine ID: 43 (Qty: 168, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('252','ADMIN001','2024-12-07','16:12:54','Updated stock (ID: 7) for Medicine ID: 43 (Qty: 168, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('253','ADMIN001','2024-12-07','16:16:53','Updated stock (ID: 7) for Medicine ID: 43 (Qty: 168, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('254','ADMIN001','2024-12-07','16:18:10','Updated transaction for patient Katrina Arwen Trinity  Diva with purpose: Medical Consultation and Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('255','ADMIN001','2024-12-07','20:28:57','New Inserted Medical Records for Patient: Precious Lyn Suico M');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('256','ADMIN001','2024-12-07','22:31:33','Added new patient: Gwyneth Marie Casia (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('257','ADMIN001','2024-12-07','22:43:27','Inserted stock for Medicine ID: 43 (Qty: 23, Dosage: 500)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('258','ADMIN001','2024-12-08','10:05:51','Issued 2 units of Acetylcisteine (Stock ID: 8)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('260','ADMIN001','2024-12-08','15:59:52','Inserted Consultation Record (ID: 12) for Patient: Katrina Arwen Trinity  Diva');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('261','ADMIN001','2024-12-08','16:01:42','Inserted Consultation Record (ID: 13) for Patient: Katrina Arwen Trinity  Diva');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('262','ADMIN001','2024-12-08','20:15:29','Updated admin user: Christina Dawa (Changes: )');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('263','ADMIN001','2024-12-08','20:15:29','Updated admin user: Christina Dawa (Changes: )');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('264','ADMIN001','2024-12-08','20:20:43','Updated admin user: Christina Dawa (Changes: )');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('265','ADMIN001','2024-12-08','20:20:43','Updated admin user: Christina Dawa (Changes: )');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('266','ADMIN001','2024-12-08','20:21:22','Updated patient: Gwyneth Marie Dawa Casia (ID: 79)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('267','ADMIN001','2024-12-08','20:21:22','Updated patient: Gwyneth Marie Dawa Casia (ID: 79)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('269','ADMIN001','2024-12-08','21:32:45','Updated patient: Precious Lyn M Suico (ID: 72)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('270','ADMIN001','2024-12-08','21:32:45','Updated patient: Precious Lyn M Suico (ID: 72)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('275','2022-00136','2024-12-08','22:45:58','Added new patient: Gwyneth Marie Casia (Type: Student)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('276','ADMIN001','2024-12-08','22:59:06','Updated admin user: Christina Dawa (Changes: )');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('277','ADMIN001','2024-12-08','22:59:06','Updated admin user: Christina Dawa (Changes: )');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('278','','2024-12-11','18:29:26','Created transaction for patient Gwyneth Marie Casia with purpose: Medical Certificate Issuance');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('279','','2024-12-11','18:29:26','Created transaction for patient Gwyneth Marie Casia with purpose: Dental Check Up & Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('280','','2024-12-11','18:29:26','Created transaction for patient Katrina Arwen Trinity  Diva with purpose: Medical Consultation and Treatment');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('281','','2024-12-11','18:29:36','Inserted Consultation Record (ID: 10) for Patient: Gwyneth Marie Casia');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('282','','2024-12-11','18:29:36','Inserted Consultation Record (ID: 11) for Patient: Alexis Nicole Bughao');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('283','','2024-12-11','18:29:36','Inserted Consultation Record (ID: 12) for Patient: Katrina Arwen Trinity  Diva');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('284','','2024-12-11','18:29:36','Inserted Consultation Record (ID: 13) for Patient: Katrina Arwen Trinity  Diva');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('285','','2024-12-11','18:29:36','Issued 2 units of Acetylcisteine (Stock ID: 7)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('286','','2024-12-11','18:29:36','Issued 1 units of Acetylcisteine (Stock ID: 7)');
INSERT IGNORE INTO `systemlog` (`syslog_id`, `syslog_userid`, `syslog_date`, `syslog_time`, `syslog_action`) VALUES ('287','','2024-12-11','18:29:36','Issued 2 units of Acetylcisteine (Stock ID: 8)');



CREATE TABLE IF NOT EXISTS `transactions` (
  `transac_id` int(11) NOT NULL AUTO_INCREMENT,
  `transac_patientid` int(11) NOT NULL,
  `transac_purpose` varchar(50) DEFAULT NULL,
  `transac_date` date DEFAULT NULL,
  `transac_in` time DEFAULT NULL,
  `transac_out` time DEFAULT NULL,
  `transac_spent` int(11) DEFAULT NULL,
  `transac_status` enum('Pending','Progress','Done') NOT NULL,
  PRIMARY KEY (`transac_id`),
  KEY `transac_patientid` (`transac_patientid`),
  CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`transac_patientid`) REFERENCES `patients` (`patient_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT IGNORE INTO `transactions` (`transac_id`, `transac_patientid`, `transac_purpose`, `transac_date`, `transac_in`, `transac_out`, `transac_spent`, `transac_status`) VALUES ('11','81','Medical Certificate Issuance','2024-12-07','10:14:05','10:14:10','5','Done');
INSERT IGNORE INTO `transactions` (`transac_id`, `transac_patientid`, `transac_purpose`, `transac_date`, `transac_in`, `transac_out`, `transac_spent`, `transac_status`) VALUES ('12','79','Dental Check Up & Treatment','2024-12-07','10:14:57','10:15:01','4','Done');
INSERT IGNORE INTO `transactions` (`transac_id`, `transac_patientid`, `transac_purpose`, `transac_date`, `transac_in`, `transac_out`, `transac_spent`, `transac_status`) VALUES ('13','73','Medical Consultation and Treatment','2024-12-07','12:46:03','16:18:10','12727','Done');

