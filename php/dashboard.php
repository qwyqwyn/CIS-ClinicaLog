<?php
class AllPatients {
    public $id;
    public $faculty;
    public $student;
    public $staff;
    public $extension;

    public function __construct($patient_id, $patienttype_faculty, $patienttype_student, $patienttype_staff, $patienttype_extension) {
        $this->id = $patient_id;
        $this->faculty = $patienttype_faculty;
        $this->student = $patienttype_student;
        $this->staff = $patienttype_staff;
        $this->extension = $patienttype_extension;
    }
}

class StaffUser {
    public $user_idnum;
    public $status;

    public function __construct($staffuser_idnum, $user_status) {
        $this->user_idnum = $staffuser_idnum;
        $this->status = $user_status;
    }
}

class Medicines {
    public $medstock_id;
    public $name;
    public $expiration_date;
    public $status;

    public function __construct($medstock_id, $med_name, $expiration_date, $expiration_status) {
        $this->medstock_id = $medstock_id;
        $this->name = $med_name;
        $this->expiration_date = $expiration_date;
        $this->status = $expiration_status;
    }
}

class Transactions {
    public $transaction_id;

    public function __construct($transaction_id) {
        $this->transaction_id = $transaction_id;
    }
}

class DashboardNode {
    public $item;
    public $next;

    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}

class DashboardLinkedList {
    public $head;

    public function __construct() {
        $this->head = null;
    }

    public function add($item) {
        $newNode = new DashboardNode($item);
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

class Dashboard {
    private $db;
    private $allpat;
    private $students;
    private $faculties;
    private $staffs;
    private $extens;

    public function __construct($db) {
        $this->db = $db;
        $this->allpat = new DashboardLinkedList();
        $this->students = new DashboardLinkedList();
        $this->faculties = new DashboardLinkedList();
        $this->staffs = new DashboardLinkedList();
        $this->extens = new DashboardLinkedList();
        $this->countActivePatients();
        $this->countActiveadminusers();
        $this->countAvailableMedstocks();
        $this->getAlmostExpiredMedstocks();
        $this->countTransactions();
    }

    //working 
    public function countActivePatients() {
        try {
            // Prepare the SQL query to count active patients
            $query = "SELECT * FROM active_patient_count";
    
            // Execute the query
            $stmt = $this->db->prepare($query);
            $stmt->execute();
    
            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $row['active_count'];
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }
    
    public function countStudentPerMonth($start_date = null, $end_date = null) {
        return $this->countPatientsByTypePerMonth('student', $start_date, $end_date);
    }
    
    public function countFacultyPerMonth($start_date = null, $end_date = null) {
        return $this->countPatientsByTypePerMonth('faculty', $start_date, $end_date);
    }
    
    public function countStaffPerMonth($start_date = null, $end_date = null) {
        return $this->countPatientsByTypePerMonth('staff', $start_date, $end_date);
    }
    
    public function countExtensionPerMonth($start_date = null, $end_date = null) {
        return $this->countPatientsByTypePerMonth('extension', $start_date, $end_date);
    }
 
    private function countPatientsByTypePerMonth($type, $start_date = null, $end_date = null) {
        try {
            // SQL query for consultations filtered by patient type
            $query = "
               SELECT 
                    YEAR(p.patient_dateadded) AS year,
                    MONTH(p.patient_dateadded) AS month,
                    COUNT(*) AS count
                FROM patients p
                WHERE p.patient_patienttype = :type
                AND p.patient_status = 'Active'
                " . ($start_date && $end_date ? "AND p.patient_dateadded BETWEEN :start_date AND :end_date" : "") . "
                GROUP BY YEAR(p.patient_dateadded), MONTH(p.patient_dateadded), p.patient_patienttype
                ORDER BY YEAR(p.patient_dateadded), MONTH(p.patient_dateadded);
            ";
    
            $stmt = $this->db->prepare($query);
    
            // Bind parameters
            $stmt->bindParam(':type', $type);
            if ($start_date && $end_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }
    
            $stmt->execute();
    
            // Process results
            $monthlyCounts = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $yearMonth = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
                $monthlyCounts[$yearMonth] = $row['count'];
            }
    
            // Fill missing months
            $allMonths = [];
            $start = new DateTime($start_date ?: 'first day of January this year');
            $end = new DateTime($end_date ?: 'last day of December this year');
            $interval = new DateInterval('P1M'); // 1 month interval
    
            while ($start <= $end) {
                $yearMonth = $start->format('Y-m');
                if (!isset($monthlyCounts[$yearMonth])) {
                    $monthlyCounts[$yearMonth] = 0;
                }
                $start->add($interval);
            }
    
            return $monthlyCounts;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }
    
    

    //working
    public function countActiveadminusers() {
        try {
            // Prepare the SQL query to count active staff users with role 'Admin'
            $query = "SELECT * FROM active_admin_count";
    
            // Execute the query
            $stmt = $this->db->prepare($query);
            $stmt->execute();
    
            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $row['active_staff_count'];
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }

    //working
    public function countAvailableMedstocks() {
        try {
            
            $query = "SELECT * FROM available_medicine_stock";
    
            // Execute the query
            $stmt = $this->db->prepare($query);
            $stmt->execute();
    
            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
     
            return $row['overall_available_stock'];
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return 0; 
        }
    }   


    public function getAlmostExpiredMedstocks($daysThreshold = 30) {
        try {
            // Define the stored procedure call
            $query = "CALL get_expiring_med_soon(:daysThreshold)";
    
            // Prepare the statement
            $stmt = $this->db->prepare($query);
    
            // Bind the parameter (daysThreshold)
            $stmt->bindParam(':daysThreshold', $daysThreshold, PDO::PARAM_INT);
    
            // Execute the statement
            $stmt->execute();
    
            // Fetch all the results
            $almostExpiredMedstocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            // Return the results
            return $almostExpiredMedstocks;
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return array();
        }
    }
      

    public function countTransactions() {
        try {
            
            $query = "SELECT * FROM total_done_transactions ";
    
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $row['total_transactions'];
        } catch (PDOException $e) {
           
            echo "Error: " . $e->getMessage();
            return 0;
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
