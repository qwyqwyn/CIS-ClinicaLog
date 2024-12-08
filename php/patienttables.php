<?php
class AllPatTable {
    public $id;
    public $full_name;
    public $email;
    public $profile;
    public $status;
    public $idnum;
    public $sex; 
    public $type;

    public function __construct($patient_id, $full_name, $all_email, 
                                $all_profile, $all_status, $all_idnum,
                                 $all_sex, $patient_type) { 
        $this->id = $patient_id;
        $this->full_name = $full_name;  
        $this->email = $all_email;
        $this->profile = $all_profile;
        $this->status = $all_status;
        $this->idnum = $all_idnum;
        $this->sex = $all_sex; 
        $this->type = $patient_type; 
    }
}

class StudentTable {
    public $patient_id;
    public $full_name;
    public $student_email;
    public $student_profile;
    public $student_status;
    public $student_idnum;
    public $student_program;
    public $student_major;
    public $student_year;
    public $student_section;
    public $student_sex; 
    public $patient_type;

    public function __construct($patient_id, $full_name, $student_email, 
                                $student_profile, $student_status, $student_idnum, 
                                $student_program, $student_major, $student_year, 
                                $student_section, $student_sex, $patient_type) { 
        $this->patient_id = $patient_id;
        $this->full_name = $full_name;  
        $this->student_email = $student_email;
        $this->student_profile = $student_profile;
        $this->student_status = $student_status;
        $this->student_idnum = $student_idnum;
        $this->student_program = $student_program;
        $this->student_major = $student_major;
        $this->student_year = $student_year;
        $this->student_section = $student_section;
        $this->student_sex = $student_sex;
        $this->patient_type = $patient_type; 
    }
}

class FacultyTable {
    public $patient_id;
    public $full_name;
    public $faculty_email;
    public $faculty_profile;
    public $faculty_status;
    public $faculty_idnum;
    public $faculty_college;
    public $faculty_depart;
    public $faculty_role;
    public $faculty_sex; 
    public $patient_type;

    public function __construct($patient_id, $full_name, $faculty_email, 
                                $faculty_profile, $faculty_status, $faculty_idnum, 
                                $faculty_college, $faculty_depart, $faculty_role, 
                                $faculty_sex, $patient_type) { 
        $this->patient_id = $patient_id;
        $this->full_name = $full_name;  
        $this->faculty_email = $faculty_email;
        $this->faculty_profile = $faculty_profile;
        $this->faculty_status = $faculty_status;
        $this->faculty_idnum = $faculty_idnum;
        $this->faculty_college = $faculty_college;
        $this->faculty_depart = $faculty_depart;
        $this->faculty_role = $faculty_role;
        $this->faculty_sex = $faculty_sex; 
        $this->patient_type = $patient_type; 
    }
}

class StaffTable {
    public $patient_id;
    public $full_name;
    public $staff_email;
    public $staff_profile;
    public $staff_status;
    public $staff_idnum;
    public $staff_office;
    public $staff_role;
    public $staff_sex; 
    public $patient_type;

    public function __construct($patient_id, $full_name, $staff_email, 
                                $staff_profile, $staff_status, $staff_idnum, 
                                $staff_office, $staff_role, $staff_sex, $patient_type) { 
        $this->patient_id = $patient_id;
        $this->full_name = $full_name;  
        $this->staff_email = $staff_email;
        $this->staff_profile = $staff_profile;
        $this->staff_status = $staff_status;
        $this->staff_idnum = $staff_idnum;
        $this->staff_office = $staff_office;
        $this->staff_role = $staff_role;
        $this->staff_sex = $staff_sex; 
        $this->patient_type = $patient_type; 
    }
}

class ExtenTable {
    public $patient_id;
    public $full_name;
    public $exten_email;
    public $exten_profile;
    public $exten_status;
    public $exten_idnum;
    public $exten_role;
    public $exten_sex; 
    public $patient_type;

    public function __construct($patient_id, $full_name, $exten_email, 
                                $exten_profile, $exten_status, $exten_idnum, 
                                $exten_role, $exten_sex, $patient_type) { 
        $this->patient_id = $patient_id;
        $this->full_name = $full_name;  
        $this->exten_email = $exten_email;
        $this->exten_profile = $exten_profile;
        $this->exten_status = $exten_status;
        $this->exten_idnum = $exten_idnum;
        $this->exten_role = $exten_role;
        $this->exten_sex = $exten_sex; 
        $this->patient_type = $patient_type; 
    }
}


class PatNode {
    public $item;
    public $next;

    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}

class PatLinkedList {
    public $head;

    public function __construct() {
        $this->head = null;
    }

    public function add($item) {
        $newNode = new PatNode($item);
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
}

class PatientTablesbyType {
    private $db;
    private $allpat;
    private $students;
    private $faculties;
    private $staffs;
    private $extens;




    public function __construct($db) {
        $this->db = $db;
        $this->allpat= new PatLinkedList();
        $this->students = new PatLinkedList();
        $this->faculties = new PatLinkedList();
        $this->staffs = new PatLinkedList();
        $this->extens = new PatLinkedList();
        $this->loadAllTable();
        $this->loadStudentTable();
        $this->loadFacultyTable();
        $this->loadStaffTable();
        $this->loadExtensionsTable();

    }

    public function loadAllTable() {
        $stmt = $this->db->prepare("SELECT 
                            p.patient_id AS patient_id,
                            get_patient_full_name(p.patient_id) AS full_name, 
                            p.patient_email AS all_email,
                            p.patient_profile AS all_profile,
                            p.patient_status AS all_status,
                            p.patient_sex AS all_sex,
                            p.patient_patienttype AS patient_type,
                            COALESCE(patstudents.student_idnum, patfaculties.faculty_idnum, patstaffs.staff_idnum, patextensions.exten_idnum) AS all_idnum
                        FROM patients p
                        LEFT JOIN patstudents ON p.patient_id = patstudents.student_patientid
                        LEFT JOIN patfaculties ON p.patient_id = patfaculties.faculty_patientid
                        LEFT JOIN patstaffs ON p.patient_id = patstaffs.staff_patientid
                        LEFT JOIN patextensions ON p.patient_id = patextensions.exten_patientid;
                        ");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $all = new AllPatTable(
                $row['patient_id'],
                $row['full_name'], 
                $row['all_email'],
                $row['all_profile'],
                $row['all_status'],
                $row['all_idnum'],
                $row['all_sex'],
                $row['patient_type']
            );
            $this->allpat->add($all);
        }
    }

    public function loadStudentTable() {
        $stmt = $this->db->prepare("SELECT p.patient_id, 
                                           get_patient_full_name(p.patient_id) AS full_name, 
                                           p.patient_email, p.patient_sex, p.patient_profile, p.patient_status, 
                                           ps.student_idnum, ps.student_program, ps.student_major, ps.student_year, 
                                           ps.student_section, p.patient_patienttype
                                    FROM patients p
                                    JOIN patstudents ps ON p.patient_id = ps.student_patientid"); 
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($result as $row) {
            
            $student = new StudentTable(
                $row['patient_id'],
                $row['full_name'],  
                $row['patient_email'],
                $row['patient_profile'],
                $row['patient_status'],
                $row['student_idnum'],
                $row['student_program'],
                $row['student_major'],
                $row['student_year'],
                $row['student_section'],
                $row['patient_sex'], 
                $row['patient_patienttype'] 
            );
            $this->students->add($student);
        }
    }
    

    public function loadFacultyTable() {
        $stmt = $this->db->prepare("SELECT p.patient_id, get_patient_full_name(p.patient_id) AS full_name,
                                    p.patient_email, p.patient_sex, p.patient_profile, p.patient_status, pf.faculty_idnum, 
                                    pf.faculty_college, pf.faculty_depart, pf.faculty_role, p.patient_patienttype
                                    FROM patients p
                                    JOIN patfaculties pf ON p.patient_id = pf.faculty_patientid");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $faculty = new FacultyTable(
                $row['patient_id'],
                $row['full_name'],
                $row['patient_email'],
                $row['patient_profile'],
                $row['patient_status'],
                $row['faculty_idnum'],
                $row['faculty_college'],
                $row['faculty_depart'],
                $row['faculty_role'],
                $row['patient_sex'], 
                $row['patient_patienttype'] 
            );
            $this->faculties->add($faculty);
        }
    }

    
    public function loadStaffTable() {
        $stmt = $this->db->prepare("SELECT p.patient_id, get_patient_full_name(p.patient_id) AS full_name,
                                    p.patient_email, p.patient_sex, p.patient_profile, p.patient_status, ps.staff_idnum, 
                                    ps.staff_office, ps.staff_role, p.patient_patienttype
                                    FROM patients p
                                    JOIN patstaffs ps ON p.patient_id = ps.staff_patientid");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $staff = new StaffTable(
                $row['patient_id'],
                $row['full_name'],
                $row['patient_email'],
                $row['patient_profile'],
                $row['patient_status'],
                $row['staff_idnum'],
                $row['staff_office'],
                $row['staff_role'],
                $row['patient_sex'], 
                $row['patient_patienttype'] 
            );
            $this->staffs->add($staff);
        }
    }

    public function loadExtensionsTable() {
        $stmt = $this->db->prepare("SELECT p.patient_id, get_patient_full_name(p.patient_id) AS full_name,
                                    p.patient_email, p.patient_sex, p.patient_profile, p.patient_status, pe.exten_idnum, 
                                    pe.exten_role, p.patient_patienttype
                                    FROM patients p
                                    JOIN patextensions pe ON p.patient_id = pe.exten_patientid");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $row) {
            $exten = new ExtenTable(
                $row['patient_id'],
                $row['full_name'],
                $row['patient_email'],
                $row['patient_profile'],
                $row['patient_status'],
                $row['exten_idnum'],
                $row['exten_role'],
                $row['patient_sex'], 
                $row['patient_patienttype'] 
            );
            $this->extens->add($exten);
        }
    }

    public function getAllTable() {
        return $this->allpat->getAllNodes();
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
        return $this->extens->getAllNodes();
    }
}
?>
