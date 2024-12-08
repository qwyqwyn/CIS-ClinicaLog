<?php
// Represents a patient record with associated patient type data
class AllPatients {
    public $id;
    public $faculty;
    public $student;
    public $staff;
    public $extension;

    // Constructor initializes the patient properties
    public function __construct($patient_id, $patienttype_faculty, $patienttype_student, $patienttype_staff, $patienttype_extension) {
        $this->id = $patient_id;
        $this->faculty = $patienttype_faculty;
        $this->student = $patienttype_student; 
        $this->staff = $patienttype_staff;
        $this->extension = $patienttype_extension;
    }
}

// Represents a staff user record with their ID and status
class StaffUser {
    public $user_idnum;
    public $status;

    // Constructor initializes the staff user properties
    public function __construct($staffuser_idnum, $user_status) {
        $this->user_idnum = $staffuser_idnum;
        $this->status = $user_status;
    }
}

// Represents a medicine record in stock
class Medicines {
    public $medstock_id;
    public $name;
    public $expiration_date;
    public $status;

    // Constructor initializes the medicine properties
    public function __construct($medstock_id, $med_name, $expiration_date, $expiration_status) {
        $this->medstock_id = $medstock_id;
        $this->name = $med_name;
        $this->expiration_date = $expiration_date;
        $this->status = $expiration_status;
    }
}

// Represents a single transaction record
class Transactions {
    public $transaction_id;

    // Constructor initializes the transaction ID
    public function __construct($transaction_id) {
        $this->transaction_id = $transaction_id;
    }
}

// Represents a node in a linked list used for dashboard data
class DashboardNode {
    public $item;
    public $next;

    // Constructor initializes the node with an item
    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}

// Implements a linked list to store dashboard data
class DashboardLinkedList {
    public $head;

    // Constructor initializes the linked list as empty
    public function __construct() {
        $this->head = null;
    }

    // Adds a new item to the end of the linked list
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

    // Retrieves all items stored in the linked list
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

// Manages various operations related to the dashboard
class Dashboard {
    private $db;
    private $allpat;
    private $students;
    private $faculties;
    private $staffs;
    private $extens;

    // Constructor initializes the dashboard and populates data
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

    // Counts active patients in the database using View
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
    
   // Method to count the number of students added per month within a given date range
    public function countStudentPerMonth($start_date = null, $end_date = null) {
        // Calls the generic method with 'student' as the patient type
        return $this->countPatientsByTypePerMonth('student', $start_date, $end_date);
    }

    // Method to count the number of faculty members added per month within a given date range
    public function countFacultyPerMonth($start_date = null, $end_date = null) {
        // Calls the generic method with 'faculty' as the patient type
        return $this->countPatientsByTypePerMonth('faculty', $start_date, $end_date);
    }

    // Method to count the number of staff members added per month within a given date range
    public function countStaffPerMonth($start_date = null, $end_date = null) {
        // Calls the generic method with 'staff' as the patient type
        return $this->countPatientsByTypePerMonth('staff', $start_date, $end_date);
    }

    // Method to count the number of extension program participants added per month within a given date range
    public function countExtensionPerMonth($start_date = null, $end_date = null) {
        // Calls the generic method with 'extension' as the patient type
        return $this->countPatientsByTypePerMonth('extension', $start_date, $end_date);
    }

    // Private method to count patients of a specific type added per month
    private function countPatientsByTypePerMonth($type, $start_date = null, $end_date = null) {
        try {
            // SQL query to get the count of patients grouped by year and month
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

            // Prepare the SQL statement
            $stmt = $this->db->prepare($query);

            // Bind the patient type parameter
            $stmt->bindParam(':type', $type);

            // Bind the start and end dates if provided
            if ($start_date && $end_date) {
                $stmt->bindParam(':start_date', $start_date);
                $stmt->bindParam(':end_date', $end_date);
            }

            // Execute the statement
            $stmt->execute();

            // Initialize an array to hold the monthly counts
            $monthlyCounts = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Create a formatted key for the year-month
                $yearMonth = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
                // Store the count for that month
                $monthlyCounts[$yearMonth] = $row['count'];
            }

            // Fill in missing months with 0 counts
            $allMonths = [];
            $start = new DateTime($start_date ?: 'first day of January this year'); // Default start date is January 1 of the current year
            $end = new DateTime($end_date ?: 'last day of December this year'); // Default end date is December 31 of the current year
            $interval = new DateInterval('P1M'); // 1-month interval

            // Loop through all months between start and end dates
            while ($start <= $end) {
                $yearMonth = $start->format('Y-m'); // Format as "YYYY-MM"
                if (!isset($monthlyCounts[$yearMonth])) {
                    // Set missing months to 0
                    $monthlyCounts[$yearMonth] = 0;
                }
                $start->add($interval); // Move to the next month
            }

            return $monthlyCounts; // Return the monthly counts
        } catch (PDOException $e) {
            // Catch and display any database-related errors
            echo "Error: " . $e->getMessage();
            return []; // Return an empty array in case of error
        }
    }


    // Counts active admin in the database using View
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

    // Counts available medicine stock in the database using View
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

    // Using stored procedure
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
      
    // Counts the total number of transactions
    public function countTransactions() {
        try {
            //View from database
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
    

    // Retrieves all patient records
    public function getAllTable() {
        return $this->allpat->getAllNodes();
    }

    // Retrieves student records
    public function getAllStudents() {
        return $this->students->getAllNodes();
    }

    // Retrieves faculty records
    public function getAllFaculties() {
        return $this->faculties->getAllNodes();
    }

    // Retrieves staff records
    public function getAllStaffs() {
        return $this->staffs->getAllNodes();
    }

    // Retrieves extension records
    public function getAllExtensions() {
        return $this->extens->getAllNodes();
    }
}
?>
