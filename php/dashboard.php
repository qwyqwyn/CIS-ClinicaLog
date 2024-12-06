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
            $query = "SELECT COUNT(*) AS active_count FROM patients WHERE patient_status = 'Active'";
    
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
    
    public function countStudentConsultationsPerMonth($start_date = null, $end_date = null) {
        return $this->countConsultationsByTypePerMonth('student', $start_date, $end_date);
    }
    
    public function countFacultyConsultationsPerMonth($start_date = null, $end_date = null) {
        return $this->countConsultationsByTypePerMonth('faculty', $start_date, $end_date);
    }
    
    public function countStaffConsultationsPerMonth($start_date = null, $end_date = null) {
        return $this->countConsultationsByTypePerMonth('staff', $start_date, $end_date);
    }
    
    public function countExtensionConsultationsPerMonth($start_date = null, $end_date = null) {
        return $this->countConsultationsByTypePerMonth('extension', $start_date, $end_date);
    }
 
    private function countConsultationsByTypePerMonth($type, $start_date = null, $end_date = null) {
        try {
            // SQL query for consultations filtered by patient type
            $query = "
                SELECT  
                    YEAR(c.consult_date) AS year,
                    MONTH(c.consult_date) AS month,
                    COUNT(*) AS count
                FROM consultations c
                INNER JOIN patients p ON c.consult_patientid = p.patient_id
                WHERE p.patient_patienttype = :type
                " . ($start_date && $end_date ? "AND c.consult_date BETWEEN :start_date AND :end_date" : "") . "
                GROUP BY YEAR(c.consult_date), MONTH(c.consult_date)
                ORDER BY YEAR(c.consult_date), MONTH(c.consult_date);
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
            $query = "SELECT COUNT(*) AS active_staff_count FROM adminusers WHERE user_status = 'Active' AND user_role = 'Admin'";
    
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
            // Prepare the SQL query to count medstocks that are available (not disabled, not expired, and in stock)
            $query = "SELECT SUM(medstock_qty) AS total_available_qty
                  FROM medstock
                  WHERE medstock_disable = 0
                  AND medstock_expirationdt >= CURDATE()
                  AND medstock_qty > 0";
    
            // Execute the query
            $stmt = $this->db->prepare($query);
            $stmt->execute();
    
            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $row['total_available_qty'];
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return 0;
        }
    }   

    //working
    public function getAlmostExpiredMedstocks($daysThreshold = 30) {
        try {
            // Prepare the SQL query to get medstock details that are almost expired
            $query = "
                SELECT m.medicine_name, ms.medstock_id, ms.medstock_expirationdt
                FROM medstock ms
                JOIN medicine m ON ms.medicine_id = m.medicine_id
                WHERE ms.medstock_disable = 0
                  AND ms.medstock_expirationdt >= CURDATE()
                  AND ms.medstock_expirationdt <= CURDATE() + INTERVAL :daysThreshold DAY
                ORDER BY ms.medstock_expirationdt ASC
                LIMIT 5
            ";
    
            // Prepare and execute the statement with the threshold parameter
            $stmt = $this->db->prepare($query); 
            $stmt->bindParam(':daysThreshold', $daysThreshold, PDO::PARAM_INT);
            $stmt->execute();
    
            // Fetch the results and store them in an array
            $almostExpiredMedstocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            return $almostExpiredMedstocks;
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return array();
        }
    }    

    //working
    public function countTransactions() {
        try {
            // Prepare the SQL query to count the total number of transactions
            $query = "SELECT COUNT(*) AS total_transactions FROM transactions where transac_status = 'Done' ";
    
            // Execute the query 
            $stmt = $this->db->prepare($query);
            $stmt->execute();
    
            // Fetch the result
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return $row['total_transactions'];
        } catch (PDOException $e) {
            // Handle any errors
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
