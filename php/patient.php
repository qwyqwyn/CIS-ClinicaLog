<?php

class Patient {
    public $patient_id;
    public $patient_lname;
    public $patient_fname;
    public $patient_mname;
    public $patient_dob;
    public $patient_email;
    public $patient_connum;
    public $patient_sex;
    public $patient_profile;
    public $patient_patienttype;
    public $patient_dateadded;
    public $patient_password;
    public $patient_status;
    public $patient_code; 
    public $next; 

    public function __construct($id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code) {
        $this->patient_id = $id;
        $this->patient_lname = $lname;
        $this->patient_fname = $fname;
        $this->patient_mname = $mname; 
        $this->patient_dob = $dob;
        $this->patient_email = $email;
        $this->patient_connum = $connum;
        $this->patient_sex = $sex;
        $this->patient_profile = $profile;
        $this->patient_patienttype = $type;
        $this->patient_dateadded = $dateadded;
        $this->patient_password = $password;
        $this->patient_status = $status;
        $this->patient_code = $code;
        $this->next = null; // Initialize next pointer to null
    }
}

class Student {
    public $student_id;
    public $student_idnum;
    public $patient_id;
    public $student_program;
    public $student_major;
    public $student_year;
    public $student_section;
    public $next; // Pointer to the next node

    public function __construct($id, $idnum, $patientid, $program, $major, $year, $section) {
        $this->student_id = $id;
        $this->student_idnum = $idnum;
        $this->patient_id = $patientid;
        $this->student_program = $program;
        $this->student_major = $major;
        $this->student_year = $year;
        $this->student_section = $section;
        $this->next = null; // Initialize next pointer to null
    }
}

class Faculty {
    public $faculty_id;
    public $patient_id;
    public $faculty_idnum;
    public $faculty_college;
    public $faculty_depart;
    public $faculty_role;
    public $next; // Pointer to the next node

    public function __construct($id, $patientid, $idnum, $college, $depart, $role) {
        $this->faculty_id = $id;
        $this->patient_id = $patientid;
        $this->faculty_idnum = $idnum;
        $this->faculty_college = $college;
        $this->faculty_depart = $depart;
        $this->faculty_role = $role;
        $this->next = null; // Initialize next pointer to null
    }
}

class Staff {
    public $staff_id;
    public $patient_id;
    public $staff_idnum;
    public $staff_office;
    public $staff_role;
    public $next; // Pointer to the next node

    public function __construct($id, $patientid, $idnum, $office, $role) {
        $this->staff_id = $id;
        $this->patient_id = $patientid;
        $this->staff_idnum = $idnum;
        $this->staff_office = $office;
        $this->staff_role = $role;
        $this->next = null; // Initialize next pointer to null
    }
}

class Extension {
    public $exten_id;
    public $patient_id;
    public $exten_idnum;
    public $exten_role;
    public $next; // Pointer to the next node

    public function __construct($id, $patientid, $idnum, $role) {
        $this->exten_id = $id;
        $this->patient_id = $patientid;
        $this->exten_idnum = $idnum;
        $this->exten_role = $role;
        $this->next = null; // Initialize next pointer to null
    }
}

class Address {
    public $address_id;
    public $patient_id;
    public $address_region;
    public $address_province;
    public $address_municipality;
    public $address_barangay;
    public $address_prkstrtadd;
    public $next; // Pointer to the next node

    public function __construct($id, $patientid, $region, $province, $municipality, $barangay, $prkstrtadd) {
        $this->address_id = $id;
        $this->patient_id = $patientid;
        $this->address_region = $region;
        $this->address_province = $province;
        $this->address_municipality = $municipality;
        $this->address_barangay = $barangay;
        $this->address_prkstrtadd = $prkstrtadd;
        $this->next = null; // Initialize next pointer to null
    }
}

class EmergencyContact {
    public $emcon_contactid;
    public $patient_id;
    public $emcon_conname;
    public $emcon_relationship;
    public $emcon_connum;
    public $next; // Pointer to the next node

    public function __construct($id, $patientid, $conname, $relationship, $connum) {
        $this->emcon_contactid = $id;
        $this->patient_id = $patientid;
        $this->emcon_conname = $conname;
        $this->emcon_relationship = $relationship;
        $this->emcon_connum = $connum;
        $this->next = null; // Initialize next pointer to null
    }
}


class PatientNode{
    public $item;
    public $next;

    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}

class PatientLinkedList {
    public $head;

    public function __construct() {
        $this->head = null;
    }

    public function add($item) {
        $newNode = new PatientNode($item);
        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
    }

    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current->item;
            $current = $current->next;
        }
        return $nodes;
    }

    public function PatientExists($email) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->patient_email  == $email) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    public function PatientEmailExists($email) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->patient_email === $email) { // Access the Patient object through the `item` property
                return $current->item; // Return the Patient object
            }
            $current = $current->next;
        }
        return null; // Return null instead of false
    }
    
    
    

    public function StudentExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->student_idnum, $id) === 0) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    public function FacultyExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->faculty_idnum, $id) === 0) { 
                return true; 
            } 
            $current = $current->next;
        }
        return false; 
    }

    public function StaffExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->staff_idnum, $id) === 0) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

    public function ExtensionExists($id) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->exten_idnum, $id) === 0) { 
                return true; 
            }
            $current = $current->next;
        }
        return false; 
    }

}

class PatientManager{
    private $db;
    private $patients;
    private $students;
    private $faculties;
    private $staffs;
    private $extensions;
    private $addresses;
    private $emergencycon;


    public function __construct($db) {
        $this->db = $db; 
        $this->patients = new PatientLinkedList();
        $this->students = new PatientLinkedList();
        $this->faculties = new PatientLinkedList();
        $this->staffs = new PatientLinkedList();
        $this->extensions = new PatientLinkedList();
        $this->addresses = new PatientLinkedList();
        $this->emergencycon = new PatientLinkedList();
        $this->loadPatients();
        $this->loadFaculties();
        $this->loadStaff();
        $this->loadStudents();
        $this->loadExtensions();
        $this->loadEmergencyContacts();
        $this->loadAddresses();
    }
 
    private function loadPatients() {
        $sql = "SELECT * FROM patients"; // Adjust the SQL as needed
        $stmt = $this->db->query($sql); // Prepare the SQL query
        
       while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $patient = new Patient(
                $row['patient_id'], $row['patient_lname'], $row['patient_fname'], 
                $row['patient_mname'], $row['patient_dob'], $row['patient_email'], 
                $row['patient_connum'], $row['patient_sex'], $row['patient_profile'], 
                $row['patient_patienttype'], $row['patient_dateadded'], $row['patient_password'], 
                $row['patient_status'], $row['patient_code']
            );        
            $this->patients->add($patient); 
        }
    }
    

    private function loadStudents() {
        $sql = "SELECT * FROM patstudents";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $student = new Student(
                $row['student_id'], $row['student_idnum'], $row['student_patientid'], 
                $row['student_program'], $row['student_major'], $row['student_year'], 
                $row['student_section']
            );
            $this->students->add($student);
        }
    }

    private function loadFaculties() {
        $sql = "SELECT * FROM patfaculties";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $faculty = new Faculty(
                $row['faculty_id'], $row['faculty_patientid'], $row['faculty_idnum'], 
                $row['faculty_college'], $row['faculty_depart'], $row['faculty_role']
            );
            $this->faculties->add($faculty);
        }
    }

    private function loadStaff() {
        $sql = "SELECT * FROM patstaffs";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $staff = new Staff(
                $row['staff_id'], $row['staff_patientid'], $row['staff_idnum'], 
                $row['staff_office'], $row['staff_role']
            );
            $this->staffs->add($staff);
        }
    }

    private function loadExtensions() {
        $sql = "SELECT * FROM patextensions";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $extension = new Extension(
                $row['exten_id'], $row['exten_patientid'], $row['exten_idnum'], 
                $row['exten_role']
            );
            $this->extensions->add($extension);
        }
    }
    
    private function loadAddresses() {
        $sql = "SELECT * FROM pataddresses";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $address = new Address(
                $row['address_id'], $row['address_patientid'], $row['address_region'], 
                $row['address_province'], $row['address_municipality'], $row['address_barangay'], 
                $row['address_prkstrtadd']
            );
            $this->addresses->add($address);
        }
    }
    
    private function loadEmergencyContacts() {
        $sql = "SELECT * FROM patemergencycontacts";
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $emergencyContact = new EmergencyContact(
                $row['emcon_contactid'], $row['emcon_patientid'], $row['emcon_conname'], 
                $row['emcon_relationship'], $row['emcon_connum']
            );
            $this->emergencycon->add($emergencyContact);
        }
    }

    public function userpatientExists($email, $password) {
        $sql = "SELECT * FROM patients WHERE patient_email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row && password_verify($password, $row['patient_password'])) {
            return new Patient(
                $row['patient_id'], $row['patient_lname'], $row['patient_fname'], 
                $row['patient_mname'], $row['patient_dob'], $row['patient_email'], 
                $row['patient_connum'], $row['patient_sex'], $row['patient_profile'], 
                $row['patient_patienttype'], $row['patient_dateadded'], $row['patient_password'], 
                $row['patient_status'], $row['patient_code']
            );
        }
    
        return false;
    }

    public function getPatientData($patient_id) {
        $sql = "SELECT * FROM patients WHERE patient_id = :patientid";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':patientid', $patient_id);
        $stmt->execute();
    
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($row) {
            return new Patient(
                $row['patient_id'], $row['patient_lname'], $row['patient_fname'], 
                $row['patient_mname'], $row['patient_dob'], $row['patient_email'], 
                $row['patient_connum'], $row['patient_sex'], $row['patient_profile'], 
                $row['patient_patienttype'], $row['patient_dateadded'], $row['patient_password'], 
                $row['patient_status'], $row['patient_code']
            );
        }
    
        return false;
    }
    

    

    public function insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code) {
        try {
            // Set admin ID for logging
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
    
            // Check if the patient already exists
            if ($this->patients->patientExists($email)) {
                return ['status' => 'error', 'message' => 'Patient already exists.'];
            }
    
            // Insert the patient
            $sql = "INSERT INTO patients 
                    (patient_lname, patient_fname, patient_mname, patient_dob, patient_email, patient_connum, patient_sex, patient_profile, patient_patienttype, patient_dateadded, patient_password, patient_status, patient_code)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
    
            $params = [
                $lname, 
                $fname, 
                $mname === '' ? null : $mname,  
                $dob, 
                $email, 
                $connum, 
                $sex, 
                $profile, 
                $type, 
                $dateadded, 
                $password, 
                $status, 
                $code
            ];
    
            $stmt->execute($params);
    
            // Get the inserted patient ID
            $patient_id = $this->db->lastInsertId();
    
            // Add the patient to the linked list
            $patient = new Patient($patient_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
            $this->patients->add($patient);
    
            return ['status' => 'success', 'message' => 'Patient inserted successfully.', 'patient_id' => $patient_id];
    
        } catch (PDOException $e) {
            error_log("Error inserting patient: " . $e->getMessage());
    
            return [
                'status' => 'error',
                'message' => 'Error inserting patient: ' . $e->getMessage(),  
                'details' => [
                    'sqlState' => $e->getCode(),  
                    'params' => json_encode($params)  
                ]
            ];
        }
    }
    
    
    
    public function insertStudent($idnum, $patientid, $program, $major, $year, $section) {
        if ($this->students->studentExists($idnum)) {
            return ['status' => 'error', 'message' => 'Student already exists.'];
        }
    
        $sql = "INSERT INTO patstudents (student_idnum, student_patientid, student_program, student_major, student_year, student_section)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$idnum, $patientid, $program === '' ? null : $program, $major === '' ? null : $major, $year === '' ? null : $year, $section === '' ? null : $section]);
            $student_id = $this->db->lastInsertId();
            return ['status' => 'success', 'message' => 'Student inserted successfully.', 'student_id' => $student_id];
    
        } catch (PDOException $e) {
            error_log("Error inserting student: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting student. Please try again later.'];
        }
    }
    
    
    public function insertFaculty($patientid, $idnum, $college, $depart, $role) {    
        if ($this->faculties->facultyExists($idnum)) {
            return ['status' => 'error', 'message' => 'Faculty already exists.'];
        }
    
        $sql = "INSERT INTO patfaculties (faculty_patientid, faculty_idnum, faculty_college, faculty_depart, faculty_role)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$patientid, $idnum, $college, $depart, $role]);
            $faculty_id = $this->db->lastInsertId();
        
            return [
                'status' => 'success', 
                'message' => 'Faculty inserted successfully. Faculty ID: ' . $faculty_id 
            ];
        } catch (PDOException $e) {
            $errorMessage = "Error inserting faculty: " . $e->getMessage();
            echo $errorMessage . "<br>";
            return [
                'status' => 'error', 
                'message' => 'Error inserting faculty. Please try again later. SQL Error: ' . $errorMessage 
            ];
        }
    }
    
    
    public function insertStaff($patientid, $idnum, $office, $role) {
        if ($this->staffs->staffExists($idnum)) {
            return ['status' => 'error', 'message' => 'Staff already exists.'];
        }
    
        $sql = "INSERT INTO patstaffs (staff_patientid, staff_idnum, staff_office, staff_role)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        
        try {
            $stmt->execute([$patientid, $idnum, $office, $role]);
            $staff_id = $this->db->lastInsertId();
            $staff = new Staff($staff_id, $patientid, $idnum, $office, $role);
            $this->staffs->add($staff);
            return ['status' => 'success', 'message' => 'Staff inserted successfully.', 'staff_id' => $staff_id];

        } catch (PDOException $e) {
            error_log("Error inserting staff: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting staff. Please try again later.'];

        }
    }
    
    public function insertExtension($idnum, $patientid, $role) {
        if ($this->extensions->ExtensionExists($idnum)) {
            return ['status' => 'error', 'message' => 'Extension already exists.'];
        }
    
        $insertSql = "INSERT INTO patextensions (exten_patientid, exten_idnum, exten_role)
                      VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($insertSql);
        
        try {
            $stmt->execute([$patientid, $idnum,  $role]);
            $extension_id = $this->db->lastInsertId();
            $extension = new Extension($extension_id, $patientid, $idnum, $role);
            $this->extensions->add($extension); 
            return ['status' => 'success', 'message' => 'Extension inserted successfully.', 'staff_id' => $extension_id];
        } catch (PDOException $e) {
            error_log("Error inserting staff: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting extension. Please try again later.'];
        }
    }
    
    public function insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd) {
        $sql = "INSERT INTO pataddresses (address_patientid, address_region, address_province, address_municipality, address_barangay, address_prkstrtadd)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$patientid, $region, $province, $municipality, $barangay, $prkstrtadd]);
            $address_id = $this->db->lastInsertId();
            return ['status' => 'success', 'message' => 'Address inserted successfully.', 'address_id' => $address_id];
    
        } catch (PDOException $e) {
            error_log("Error inserting address: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting address. Please try again later.'];
        }
    }
    
    
    public function insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum) {
        $sql = "INSERT INTO patemergencycontacts (emcon_patientid, emcon_conname, emcon_relationship, emcon_connum)
                VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$patientid, $conname, $relationship, $emergency_connum]);
            $contact_id = $this->db->lastInsertId();
            return ['status' => 'success', 'message' => 'Emergency contact inserted successfully.', 'contact_id' => $contact_id];
    
        } catch (PDOException $e) {
            error_log("Error inserting emergency contact: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error inserting emergency contact. Please try again later.'];
        }
    }
    
    public function addStudentPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code, $idnum, $program, $major, $year, $section, $region, $province, $municipality, $barangay, $prkstrtadd, $conname, $relationship, $emergency_connum) {
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        
        $patientid = $insertPatientResponse['patient_id']; 
    
        $insertStudentResponse = $this->insertStudent($idnum, $patientid, $program, $major, $year, $section);
        if ($insertStudentResponse['status'] !== 'success') {
            return $insertStudentResponse; 
        }
    
        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }
    
        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse; 
        }
    
       
        return [
            'status' => 'success',
            'message' => 'Student patient added successfully.',
            'patient_id' => $patientid,
            'student_id' => $insertStudentResponse['student_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    }
    
    public function addFacultyPatient(
        $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $college, $depart, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
       
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        
        $patientid = $insertPatientResponse['patient_id']; 

      
        $insertFacultyResponse = $this->insertFaculty($patientid, $idnum, $college, $depart, $role);
        if ($insertFacultyResponse['status'] !== 'success') {
            return $insertFacultyResponse; 
        }

        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }

        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse; 
        }

        return [
            'status' => 'success',
            'message' => 'Faculty patient added successfully.',
            'patient_id' => $patientid,
            'faculty_id' => $insertFacultyResponse['faculty_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    }

    public function addStaffPatient(
        $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $office, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        
        $patientid = $insertPatientResponse['patient_id']; 

        $insertStaffResponse = $this->insertStaff($patientid, $idnum, $office, $role);
        if ($insertStaffResponse['status'] !== 'success') {
            return $insertStaffResponse; 
        }

        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }

        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse;
        }

        return [
            'status' => 'success',
            'message' => 'Staff patient added successfully.',
            'patient_id' => $patientid,
            'staff_id' => $insertStaffResponse['staff_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    }

    public function addExtenPatient(
        $admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, 
        $password, $status, $code, $idnum, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $insertPatientResponse = $this->insertPatient($admin_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $profile, $type, $dateadded, $password, $status, $code);
        
        if ($insertPatientResponse['status'] !== 'success') {
            return $insertPatientResponse; 
        }
        
        $patientid = $insertPatientResponse['patient_id']; 

        $insertExtenResponse = $this->insertExtension($idnum, $patientid, $role);
        if ($insertExtenResponse['status'] !== 'success') {
            return $insertExtenResponse; 
        }

        $insertAddressResponse = $this->insertAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($insertAddressResponse['status'] !== 'success') {
            return $insertAddressResponse; 
        }

        $insertEmergencyContactResponse = $this->insertEmergencyContact($patientid, $conname, $relationship, $emergency_connum);
        if ($insertEmergencyContactResponse['status'] !== 'success') {
            return $insertEmergencyContactResponse; 
        }
        return [
            'status' => 'success',
            'message' => 'Extension patient added successfully.',
            'patient_id' => $patientid,
            'extension_id' => $insertExtenResponse['exten_id'],
            'address_id' => $insertAddressResponse['address_id'],
            'contact_id' => $insertEmergencyContactResponse['contact_id']
        ];
    
    }

    public function emailVerify($email) {
        return $this->patients->PatientEmailExists($email) !== null;
    } 

    public function verifyOtp($email, $otp) {
        $node = $this->patients->PatientEmailExists($email); // Retrieves the Patient object
        return $node !== false && $node->patient_code == $otp;
    }
    

    public function updateCode($email, $otp) {
        $sql_update_statement = "UPDATE patients SET patient_code = ? WHERE patient_email = ?";
        $stmt = $this->db->prepare($sql_update_statement);

        if ($stmt) {
            $stmt->bindParam(1, $otp);
            $stmt->bindParam(2, $email);

            return $stmt->execute();
        } else {
            die("Error preparing statement: " . $this->db->errorInfo()[2]);
        }
    }

    public function changePassword($email, $newPassword) {
        $code = 0;
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $node = $this->patients->PatientEmailExists($email);

        if ($node) {
            $sql_update_statement = "UPDATE patients SET patient_password = ?, patient_code = ? WHERE patient_email = ?";
            $stmt = $this->db->prepare($sql_update_statement);

            if ($stmt) {
                $stmt->bindParam(1, $hashedPassword);
                $stmt->bindParam(2, $code);
                $stmt->bindParam(3, $email);

                return $stmt->execute();
            } else {
                die("Error preparing statement: " . $this->db->errorInfo()[2]);
            }
        } else {
            return false;
        }
    }


    public function updatePatient($admin_id, $patient_id, $lname, $fname, $mname, $dob, $email, $connum, $sex, $newPassword, $status) {
        try {
            // Set admin ID for logging
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
    
            // Update patient details
            $sql = "UPDATE patients 
                    SET patient_lname = ?, patient_fname = ?, patient_mname = ?, patient_dob = ?, 
                        patient_email = ?, patient_connum = ?, patient_sex = ?, 
                        patient_password = ?, patient_status = ?   
                    WHERE patient_id = ?";
            $stmt = $this->db->prepare($sql);
    
            $params = [
                $lname,
                $fname,
                $mname === '' ? null : $mname,
                $dob,
                $email,
                $connum,
                $sex,
                $newPassword,
                $status,
                $patient_id
            ];
    
            $stmt->execute($params);
    
            return ['status' => 'success', 'message' => 'Patient updated successfully.'];
    
        } catch (PDOException $e) {
            error_log("Error updating patient: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error updating patient. Please try again later.'
            ];
        }
    }
    
    
    public function updateStudent($patientid, $idnum, $program, $major, $year, $section) {
        $sql = "UPDATE patstudents 
                SET student_idnum = ?, student_program = ?, student_major = ?, student_year = ?, student_section = ?
                WHERE student_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([
                            $idnum,
                            $program, 
                            $major,  
                            $year, 
                            $section, 
                            $patientid]);
    
            return ['status' => 'success', 'message' => 'Student updated successfully.'];
    
        } catch (PDOException $e) {
            error_log("Error updating student: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating student. Please try again later.'];
        }
    }

    public function updateFaculty($patientid, $idnum, $college, $depart, $role) {    
        $sql = "UPDATE patfaculties 
                SET faculty_idnum = ?, faculty_college = ?, faculty_depart = ?, faculty_role = ?
                WHERE faculty_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$idnum, $college, $depart, $role, $patientid]);
    
            return [
                'status' => 'success', 
                'message' => 'Faculty updated successfully.'
            ];
        } catch (PDOException $e) {
            error_log("Error updating faculty: " . $e->getMessage());
            return [
                'status' => 'error', 
                'message' => 'Error updating faculty. Please try again later.'
            ];
        }
    }

    public function updateStaff($patientid, $idnum, $office, $role) {
        $sql = "UPDATE patstaffs 
                SET staff_idnum = ?, staff_office = ?, staff_role = ?
                WHERE staff_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$idnum, $office, $role, $patientid]);
            return ['status' => 'success', 'message' => 'Staff updated successfully.'];
    
        } catch (PDOException $e) {
            error_log("Error updating staff: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating staff. Please try again later.'];
        }
    }

    public function updateExtension($patientid, $idnum, $role) {
        $sql = "UPDATE patextensions 
                SET exten_idnum = ?, exten_role = ?
                WHERE exten_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$idnum, $role, $patientid]);
            return ['status' => 'success', 'message' => 'Extension updated successfully.'];
        } catch (PDOException $e) {
            error_log("Error updating extension: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating extension. Please try again later.'];
        }
    }

    public function updateAddress($patientid, $region, $province, $municipality, $barangay, $prkstrtadd) {
        $sql = "UPDATE pataddresses 
                SET address_region = ?, address_province = ?, address_municipality = ?, address_barangay = ?, address_prkstrtadd = ?
                WHERE address_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$region, $province, $municipality, $barangay, $prkstrtadd, $patientid]);
            return ['status' => 'success', 'message' => 'Address updated successfully.'];
    
        } catch (PDOException $e) {
            error_log("Error updating address: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating address. Please try again later.'];
        }
    }

    public function updateEmergencyContact($patientid, $conname, $relationship, $emergency_connum) {
        $sql = "UPDATE patemergencycontacts 
                SET emcon_conname = ?, emcon_relationship = ?, emcon_connum = ?
                WHERE emcon_patientid = ?";
        $stmt = $this->db->prepare($sql);
    
        try {
            $stmt->execute([$conname, $relationship, $emergency_connum, $patientid]);
            return ['status' => 'success', 'message' => 'Emergency contact updated successfully.'];
    
        } catch (PDOException $e) {
            error_log("Error updating emergency contact: " . $e->getMessage());
            return ['status' => 'error', 'message' => 'Error updating emergency contact. Please try again later.'];
        }
    }

    public function updatePatientProfileImage($patient_id, $profile) {
        try {
            $sql = "UPDATE patients 
                    SET patient_profile = ?
                    WHERE patient_id = ?";
            
            $stmt = $this->db->prepare($sql);
            
            // Prepare the values
            $params = [
                $profile,
                $patient_id
            ];
            
            $stmt->execute($params);
            
            return ['status' => 'success', 'message' => 'Profile image updated successfully.'];
            
        } catch (PDOException $e) {
            error_log("Error updating profile image: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'Error updating profile image. Please try again later.'
            ];
        }
    }
    

    public function updateStudentPatient(
        $admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex,   
        $password, $status, $idnum, $program, $major, $year, $section, 
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateStudentResponse = $this->updateStudent($patientId, $idnum, $program, $major, $year, $section);
        if ($updateStudentResponse['status'] !== 'success') {
            return $updateStudentResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Student patient updated successfully.',
            'patient_id' => $patientId,
            'student_id' => $updateStudentResponse['student_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    
    public function updateFacultyPatient(
        $admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, 
        $password, $status, $idnum, $college, $depart, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateFacultyResponse = $this->updateFaculty($patientId, $idnum, $college, $depart, $role);
        if ($updateFacultyResponse['status'] !== 'success') {
            return $updateFacultyResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Faculty patient updated successfully.',
            'patient_id' => $patientId,
            'faculty_id' => $updateFacultyResponse['faculty_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    
    public function updateStaffPatient(
        $admin_id,  $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex,
        $password, $status, $idnum, $office, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id, $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateStaffResponse = $this->updateStaff($patientId, $idnum, $office, $role);
        if ($updateStaffResponse['status'] !== 'success') {
            return $updateStaffResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Staff patient updated successfully.',
            'patient_id' => $patientId,
            'staff_id' => $updateStaffResponse['staff_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    
    public function updateExtenPatient(
        $admin_id,  $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex,
        $password, $status, $idnum, $role,
        $region, $province, $municipality, $barangay, $prkstrtadd, $conname, 
        $relationship, $emergency_connum
    ) {
        $updatePatientResponse = $this->updatePatient($admin_id,  $patientId, $lname, $fname, $mname, $dob, $email, $connum, $sex, $password, $status);
        
        if ($updatePatientResponse['status'] !== 'success') {
            return $updatePatientResponse; 
        }
    
        $updateExtenResponse = $this->updateExtension($patientId, $idnum, $role);
        if ($updateExtenResponse['status'] !== 'success') {
            return $updateExtenResponse; 
        }
    
        $updateAddressResponse = $this->updateAddress($patientId, $region, $province, $municipality, $barangay, $prkstrtadd);
        if ($updateAddressResponse['status'] !== 'success') {
            return $updateAddressResponse; 
        }
    
        $updateEmergencyContactResponse = $this->updateEmergencyContact($patientId, $conname, $relationship, $emergency_connum);
        if ($updateEmergencyContactResponse['status'] !== 'success') {
            return $updateEmergencyContactResponse; 
        }
    
        return [
            'status' => 'success',
            'message' => 'Extension patient updated successfully.',
            'patient_id' => $patientId,
            'extension_id' => $updateExtenResponse['exten_id'],
            'address_id' => $updateAddressResponse['address_id'],
            'contact_id' => $updateEmergencyContactResponse['contact_id']
        ];
    }
    

    
    public function getAllPatients() {
        return $this->patients->getAllNodes();
    }

    public function getAllStudents() {
        return $this->students->getAllNodes();
    }
    
    public function getAllFaculties() {
        return $this->faculties->getAllNodes();
    }

    public function getAllStaffs() {
        return $this->staffs->getAllNodes();
    }

    public function getAllExtensions() {
        return $this->extensions->getAllNodes();
    }

    public function getAllAddresses() {
        return $this->addresses->getAllNodes();
    }

    public function getAllEmergencyCon() {
        return $this->emergencycon->getAllNodes();
    }

    public function getAllPatientsTable() {
        $patients = $this->patients->getAllNodes();
        $students = $this->students->getAllNodes();
        $faculties = $this->faculties->getAllNodes();
        $staffs = $this->staffs->getAllNodes();
        $extensions = $this->extensions->getAllNodes();
    
        $combinedData = [];
    
        $combineRows = function($personType, $dataArray) use (&$combinedData) {
            foreach ($dataArray as $entry) {
                if (isset($entry->patient_id, $entry->patient_lname, $entry->patient_fname, $entry->patient_email, $entry->patient_sex, $entry->patient_status)) {
                    
                    // Construct dynamic idnum field name based on the personType
                    $idnumField = strtolower($personType) . '_idnum';
                    $idnum = property_exists($entry, $idnumField) ? $entry->$idnumField : null;
    
                    $combinedEntry = (object) [
                        'id' => $entry->patient_id,
                        'name' => $entry->patient_lname . ' ' . $entry->patient_fname,
                        'email' => $entry->patient_email,
                        'sex' => $entry->patient_sex,
                        'type' => $personType,
                        'status' => $entry->patient_status,
                        'idnum' => $idnum // Dynamically added idnum
                    ];
    
                    $combinedData[] = $combinedEntry;
                } else {
                    error_log("Missing required fields in entry: " . json_encode($entry));
                }
            }
        };
    
        // Combine each type of data
        $combineRows('Patient', $patients);
        $combineRows('Student', $students);
        $combineRows('Faculty', $faculties);
        $combineRows('Staff', $staffs);
        $combineRows('Extension Worker', $extensions);
    
        return $combinedData;
    }
    
    
    
  
public function getStudentData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            s.student_idnum, 
            s.student_program, 
            s.student_major, 
            s.student_year, 
            s.student_section, 
            a.address_region, 
            a.address_province, 
            a.address_municipality, 
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patstudents s ON p.patient_id = s.student_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Student'
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, 
        'student' => [
            'student_idnum' => $data['student_idnum'],
            'student_program' => $data['student_program'],
            'student_major' => $data['student_major'],
            'student_year' => $data['student_year'],
            'student_section' => $data['student_section']
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

public function getFacultyData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            f.faculty_idnum, 
            f.faculty_college, 
            f.faculty_depart, 
            f.faculty_role, 
            a.address_region, 
            a.address_province, 
            a.address_municipality, 
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patfaculties f ON p.patient_id = f.faculty_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Faculty'
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, // Patient data
        'faculty' => [ 
            'faculty_idnum' => $data['faculty_idnum'],
            'faculty_college' => $data['faculty_college'],
            'faculty_department' => $data['faculty_depart'],
            'faculty_role' => $data['faculty_role']
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

public function getStaffData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            s.staff_idnum, 
            s.staff_office, 
            s.staff_role,  
            a.address_region, 
            a.address_province, 
            a.address_municipality,  
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patstaffs s ON p.patient_id = s.staff_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Staff'
    ";
    
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, 
        'staff' => [
            'staff_idnum' => $data['staff_idnum'],
            'staff_office' => $data['staff_office'],
            'staff_role' => $data['staff_role']
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

public function getExtensionData($patient_id) {
    $query = "
        SELECT 
            p.*, 
            e.exten_idnum, 
            e.exten_role, 
            a.address_region, 
            a.address_province, 
            a.address_municipality, 
            a.address_barangay, 
            a.address_prkstrtadd, 
            ec.emcon_conname, 
            ec.emcon_relationship, 
            ec.emcon_connum
        FROM 
            patients p
        LEFT JOIN 
            patextensions e ON p.patient_id = e.exten_patientid
        LEFT JOIN 
            pataddresses a ON p.patient_id = a.address_patientid
        LEFT JOIN 
            patemergencycontacts ec ON p.patient_id = ec.emcon_patientid
        WHERE 
            p.patient_id = :patient_id AND 
            p.patient_patienttype = 'Extension'
    ";

    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->execute();

    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return [
        'patient' => $data, 
        'extension' => [ 
            'exten_idnum' => $data['exten_idnum'], 
            'exten_role' => $data['exten_role'],   
        ],
        'address' => [
            'address_region' => $data['address_region'],
            'address_province' => $data['address_province'],
            'address_municipality' => $data['address_municipality'],
            'address_barangay' => $data['address_barangay'],
            'address_prkstrtadd' => $data['address_prkstrtadd']
        ],
        'emergencyContact' => [
            'emcon_conname' => $data['emcon_conname'],
            'emcon_relationship' => $data['emcon_relationship'],
            'emcon_connum' => $data['emcon_connum']
        ]
    ];
}

}


?>