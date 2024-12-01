<?php
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ClinicaLog";

    $conn = mysqli_connect($servername, $username, $password);

    if(!$conn){
        die("Connection failed: " . mysqli_connect_error()); 
    }

    $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
    if(mysqli_query($conn, $sql)){
        echo "Database created successfully<br>"; 
    }else{   
        echo "Error creating database: " . mysqli_error($conn) . "<br>";
    } 

    mysqli_close($conn); 

    $conn = mysqli_connect($servername, $username, $password, $dbname);  
 
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());  
    } else {
        echo "Connected to database $dbname successfully<br>";
    }

    $sql_adminusers = "CREATE TABLE IF NOT EXISTS adminusers (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        user_idnum CHAR(15) NOT NULL UNIQUE,            
        user_fname VARCHAR(30) NOT NULL,         
        user_lname VARCHAR(30) NOT NULL,         
        user_mname VARCHAR(30),                  
        user_email VARCHAR(100) NOT NULL UNIQUE,   
        user_position VARCHAR(50) NOT NULL,       
        user_role ENUM('Super Admin', 'Admin') NOT NULL, 
        user_status ENUM('Active', 'Inactive') NOT NULL,    
        user_dateadded DATE,                     
        user_profile VARCHAR(255),               
        user_password CHAR(60) NOT NULL,         
        user_code MEDIUMINT UNSIGNED NOT NULL     
    );";  

    if (mysqli_query($conn, $sql_adminusers)) { 
        echo "Table 'adminusers' created successfully<br>";

        $admin_id = 'ADMIN001'; 
        $admin_fname = 'Admin'; 
        $admin_lname = 'User';
        $admin_mname = '';
        $admin_email = 'admin@clinicalog.com';
        $admin_position = 'Administrator';
        $admin_role = 'Super Admin'; 
        $admin_status = 'Active'; 
        $admin_dateadded = date('Y-m-d');
        $admin_profile = '35e3a37ab3e5a98b04b63f4b4c3697fd.jpg';  
        $admin_password = password_hash('admin123', PASSWORD_BCRYPT); 
        $admin_code = 0;  

        $checkAdmin = "SELECT * FROM adminusers WHERE user_idnum = '$admin_id' OR user_email = '$admin_email'";
        $result = mysqli_query($conn, $checkAdmin);

        if (mysqli_num_rows($result) == 0) {  
            $insertAdmin = "INSERT INTO adminusers 
            (user_idnum, user_fname, user_lname, user_mname, user_email, user_position, 
             user_role, user_status, user_dateadded, user_profile, user_password, user_code) 
            VALUES ('$admin_id', '$admin_fname', '$admin_lname', '$admin_mname', 
                    '$admin_email', '$admin_position', '$admin_role', 
                    '$admin_status', '$admin_dateadded', '$admin_profile', 
                    '$admin_password', '$admin_code')"; 

            if (mysqli_query($conn, $insertAdmin)) {
                echo "Default admin account created successfully<br>";
            } else {
                echo "Error creating admin account: " . mysqli_error($conn) . "<br>";
            } 

        } else { 
            echo "Admin account already exists<br>";
        }
    } else {
        echo "Error creating table 'adminusers': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'admin logs'
    $sql_adminlog = "CREATE TABLE IF NOT EXISTS systemlog (
        syslog_id INT AUTO_INCREMENT PRIMARY KEY,
        syslog_userid VARCHAR(50),
        syslog_date DATE,
        syslog_time TIME,
        syslog_action VARCHAR(255),
        FOREIGN KEY (syslog_userid) REFERENCES adminusers(user_idnum) 
    )";

    if(mysqli_query($conn, $sql_adminlog)){
        echo "Table 'system logs' created successfully<br>";
    }else{
        echo "Error creating table 'medicine': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'medicine' table
    $sql_medicine = "CREATE TABLE medicine (
        medicine_id INT AUTO_INCREMENT PRIMARY KEY,
        medicine_name VARCHAR(100) NOT NULL UNIQUE, 
        medicine_category VARCHAR(50) NOT NULL
    )";
 
    if (mysqli_query($conn, $sql_medicine)) {
        echo "Table 'medicine' created successfully<br>";
    } else { 
        echo "Error creating table 'medicine': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'medstock' table with foreign key to 'medicine'
    $sql_medstock = "CREATE TABLE medstock (
        medstock_id INT AUTO_INCREMENT PRIMARY KEY,  
        medicine_id INT NOT NULL, 
        medstock_unit VARCHAR(10),          
        medstock_qty INT NOT NULL,
        medstock_dosage VARCHAR(50),
        medstock_dateadded DATE,
        medstock_timeadded TIME,
        medstock_expirationdt DATE,
        medstock_disable TINYINT(1) NOT NULL DEFAULT 0,       
        FOREIGN KEY (medicine_id) REFERENCES medicine(medicine_id) 
    )";    

    if (mysqli_query($conn, $sql_medstock)) {
        echo "Table 'medstock' created successfully<br>";
    } else {
        echo "Error creating table 'medstock': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'patients' table
    $sql_patients = "CREATE TABLE IF NOT EXISTS patients (
        patient_id INT AUTO_INCREMENT PRIMARY KEY,
        patient_lname VARCHAR(50) NOT NULL,
        patient_fname VARCHAR(50) NOT NULL,
        patient_mname VARCHAR(50),
        patient_dob DATE NOT NULL,
        patient_email VARCHAR(255) NOT NULL,
        patient_connum int(12) NOT NULL,
        patient_sex ENUM('Male', 'Female') NOT NULL,   
        patient_profile VARCHAR(255), 
        patient_patienttype ENUM('Student', 'Faculty', 'Staff', 'Extension') NOT NULL,
        patient_dateadded DATE,
        patient_password VARCHAR(60) NOT NULL,    
        patient_status ENUM('Active', 'Inactive') NOT NULL,                   
        patient_code MEDIUMINT UNSIGNED NOT NULL  
    )";

    if (mysqli_query($conn, $sql_patients)) {
        echo "Table 'patients' created successfully<br>";
    } else {
        echo "Error creating table 'patients': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'students' table
    $sql_students = "CREATE TABLE IF NOT EXISTS patstudents (
        student_id INT AUTO_INCREMENT PRIMARY KEY,
        student_idnum INT NOT NULL UNIQUE, 
        student_patientid INT NOT NULL,
        student_program VARCHAR(100) NOT NULL,
        student_major VARCHAR(100),
        student_year INT NOT NULL,
        student_section VARCHAR(10),
        FOREIGN KEY (student_patientid) REFERENCES patients(patient_id)
    )";
    if (mysqli_query($conn, $sql_students)) {
        echo "Table 'students' created successfully<br>";
    } else {
        echo "Error creating table 'students': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'faculties' table for staff and faculty
    $sql_personnel = "CREATE TABLE IF NOT EXISTS patfaculties (
        faculty_id INT AUTO_INCREMENT PRIMARY KEY,
        faculty_patientid INT NOT NULL,
        faculty_idnum INT NOT NULL UNIQUE,
        faculty_college VARCHAR(100) NOT NULL,
        faculty_depart VARCHAR(100) NOT NULL,
        faculty_role VARCHAR(100) NOT NULL,
        FOREIGN KEY (faculty_patientid) REFERENCES patients(patient_id)
    )";
    if (mysqli_query($conn, $sql_personnel)) {
        echo "Table 'faculty' created successfully<br>";
    } else {
        echo "Error creating table 'faculty': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'staff' table for staff and faculty
    $sql_personnel = "CREATE TABLE IF NOT EXISTS patstaffs (
        staff_id INT AUTO_INCREMENT PRIMARY KEY,
        staff_patientid INT NOT NULL,
        staff_idnum INT NOT NULL UNIQUE,
        staff_office VARCHAR(100) NOT NULL,
        staff_role VARCHAR(100) NOT NULL,
        FOREIGN KEY (staff_patientid) REFERENCES patients(patient_id)
    )";
    if (mysqli_query($conn, $sql_personnel)) {
        echo "Table 'staff' created successfully<br>";
    } else {
        echo "Error creating table 'staff': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'extensions' table for staff and faculty
    $sql_personnel = "CREATE TABLE IF NOT EXISTS patextensions (
        exten_id INT AUTO_INCREMENT PRIMARY KEY,
        exten_patientid INT NOT NULL,
        exten_idnum INT NOT NULL UNIQUE,
        exten_role VARCHAR(100) NOT NULL,
        FOREIGN KEY (exten_patientid) REFERENCES patients(patient_id)
    )";
    if (mysqli_query($conn, $sql_personnel)) {
        echo "Table 'extension' created successfully<br>";
    } else {
        echo "Error creating table 'extension': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'addresses' table 
    $sql_addresses = "CREATE TABLE IF NOT EXISTS pataddresses (
        address_id INT AUTO_INCREMENT PRIMARY KEY,
        address_patientid INT NOT NULL,
        address_region VARCHAR(100) NOT NULL,
        address_province VARCHAR(100) NOT NULL,
        address_municipality VARCHAR(100) NOT NULL,
        address_barangay VARCHAR(100) NOT NULL,
        address_prkstrtadd VARCHAR(255),
        FOREIGN KEY (address_patientid) REFERENCES patients(patient_id) 
    )";
    if (mysqli_query($conn, $sql_addresses)) {
        echo "Table 'addresses' created successfully<br>";
    } else {
        echo "Error creating table 'addresses': " . mysqli_error($conn) . "<br>";
    }
 
    // Create the 'emergency_contacts' table
    $sql_emergency_contacts = "CREATE TABLE IF NOT EXISTS patemergencycontacts (
        emcon_contactid INT AUTO_INCREMENT PRIMARY KEY,
        emcon_patientid INT NOT NULL,
        emcon_conname VARCHAR(100) NOT NULL,
        emcon_relationship VARCHAR(50) NOT NULL,
        emcon_connum VARCHAR(20) NOT NULL, 
        FOREIGN KEY (emcon_patientid) REFERENCES patients(patient_id)
    )";
    if (mysqli_query($conn, $sql_emergency_contacts)) {
        echo "Table 'emergency_contacts' created successfully<br>";
    } else {
        echo "Error creating table 'emergency_contacts': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'consultations' table
    $sql_consultations = "CREATE TABLE consultations (
        consult_id INT AUTO_INCREMENT PRIMARY KEY,
        consult_patientid INT NOT NULL, 
        consult_diagnosis VARCHAR(255) NOT NULL,
        consult_treatmentnotes VARCHAR(255) NOT NULL,
        consult_remark VARCHAR(255) NOT NULL,
        consult_date DATE,
        consult_timein TIME,
        consult_timeout TIME,
        consult_timespent INT,
        FOREIGN KEY (consult_patientid) REFERENCES patients(patient_id)
    )";

    if (mysqli_query($conn, $sql_consultations)) {
        echo "Table 'consultations' created successfully<br>";
    } else {
        echo "Error creating table 'consultations': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'prescribemed' table
    $sql_consultations = "CREATE TABLE prescribemed (
        pm_id INT AUTO_INCREMENT PRIMARY KEY,
        pm_consultid INT NOT NULL,
        pm_medstockid INT NOT NULL,
        pm_medqty INT NOT NULL,
        FOREIGN KEY (pm_consultid) REFERENCES consultations(consult_id),
        FOREIGN KEY (pm_medstockid) REFERENCES medstock(medstock_id)
    )";

    if (mysqli_query($conn, $sql_consultations)) {
        echo "Table 'prescribemed' created successfully<br>";
    } else {
        echo "Error creating table 'prescribemed': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'medicalrecords' table
    $sql_medicalrecords = "CREATE TABLE IF NOT EXISTS medicalrec (
    medicalrec_id INT AUTO_INCREMENT PRIMARY KEY,
    medicalrec_patientid INT NOT NULL,
    medicalrec_filename VARCHAR(255) NOT NULL,
    medicalrec_file VARCHAR(255),
    medicalrec_comment VARCHAR(255),
    medicalrec_dateadded DATE,
    medicalrec_timeadded TIME,
    FOREIGN KEY (medicalrec_patientid) REFERENCES patients(patient_id))";
    
    if (mysqli_query($conn, $sql_medicalrecords)) {
        echo "Table 'medicalrecords' created successfully<br>";
    } else {
        echo "Error creating table 'medicalrecords': " . mysqli_error($conn) . "<br>";
    }

    // Create the 'medicalrecords' table
    $sql_medissued = "CREATE TABLE IF NOT EXISTS medissued (
        mi_id INT AUTO_INCREMENT PRIMARY KEY,
        mi_medstockid INT NOT NULL,
        mi_medqty INT NOT NULL,
        mi_date DATE,
        FOREIGN KEY (mi_medstockid) REFERENCES medstock(medstock_id)
    )";
    
    if (mysqli_query($conn, $sql_medissued)) {
        echo "Table 'medissued' created successfully<br>";
    } else {
        echo "Error creating table 'medicalrecords': " . mysqli_error($conn) . "<br>";
    }

    //Create the 'transaction' table
    $sql_transaction = "CREATE TABLE IF NOT EXISTS transactions (
        transac_id INT AUTO_INCREMENT PRIMARY KEY,
        transac_patientid INT NOT NULL,
        transac_purpose VARCHAR(50), 
        transac_date DATE,
        transac_in TIME,
        transac_out TIME,
        transac_spent INT,
        transac_status ENUM('Pending', 'Progress', 'Done') NOT NULL,
        FOREIGN KEY (transac_patientid) REFERENCES patients(patient_id)
    )";
 
    if (mysqli_query($conn, $sql_transaction)) {
        echo "Table 'transaction' created successfully<br>";
    } else {
        echo "Error creating table 'transaction': " . mysqli_error($conn) . "<br>";
    }

    //Create the 'notification' table
    $sql_notification = "CREATE TABLE IF NOT EXISTS  adminnotifs (
        notif_id INT AUTO_INCREMENT PRIMARY KEY,
        notif_patid INT NOT NULL,                
        notif_message TEXT NOT NULL,           
        notif_status ENUM('unread', 'read') DEFAULT 'unread',  
        notif_date_added DATETIME DEFAULT CURRENT_TIMESTAMP, 
        FOREIGN KEY (notif_patid) REFERENCES patients(patient_id)
    );";
 
    if (mysqli_query($conn, $sql_notification)) {
        echo "Table 'notification' created successfully<br>";
    } else {
        echo "Error creating table 'transaction': " . mysqli_error($conn) . "<br>";
    }




mysqli_close($conn);
?>
    

