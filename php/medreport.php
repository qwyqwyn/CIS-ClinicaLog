<?php
// Class to represent a Medstock node in the linked list
class MedstockNode {
    public $medstock_id;
    public $item;
    public $unit;
    public $expiry_date;
    public $medicine_balance_quarter;
    public $medstock_added;
    public $total_start_balance;
    public $total_prescribed;
    public $total_issued;
    public $end_balance;
    public $next;

    // Constructor to initialize MedstockNode with values
    public function __construct($medstock_id, $item, $unit, $expiry_date) {
        $this->medstock_id = $medstock_id;
        $this->item = $item;
        $this->unit = $unit;
        $this->expiry_date = $expiry_date;
        $this->next = null; // Initialize next pointer as null
    }
}

// Class to manage the Medstock and its operations
class MedicineManager {
    private $conn; // Database connection
    private $head; // Head of the linked list

    // Constructor to initialize the MedicineManager with the database connection
    public function __construct($conn) {  
        $this->conn = $conn; // Set the database connection
        $this->head = null; // Initialize the linked list with no nodes
    }

    // Method to calculate the total quantity prescribed for a particular Medstock during a quarter
    public function calculateTotalPrescribed($medstock_id, $quarterStart, $quarterEnd) {
        $query = "SELECT COALESCE(SUM(pm.pm_medqty), 0) AS total_prescribed
                  FROM prescribemed pm
                  JOIN consultations c ON pm.pm_consultid = c.consult_id
                  WHERE pm.pm_medstockid = ? 
                  AND c.consult_date BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $quarterStart, $quarterEnd]);
        return $stmt->fetchColumn(); // Return the total prescribed quantity
    }

    // Method to calculate the total quantity issued for a particular Medstock during a quarter
    public function calculateTotalIssued($medstock_id, $quarterStart, $quarterEnd) {
        $query = "SELECT COALESCE(SUM(mi.mi_medqty), 0) AS total_issued
                  FROM medissued mi
                  WHERE mi.mi_medstockid = ? 
                  AND mi.mi_date BETWEEN ? AND ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $quarterStart, $quarterEnd]);
        return $stmt->fetchColumn(); // Return the total issued quantity
    }

    // Method to calculate the medicine balance for the quarter for a particular Medstock
    public function calculateMedicineBalanceQuarter($medstock_id, $quarterStart, $quarterEnd) {
        // SQL query to calculate medicine balance before the selected quarter
        $query = "
            SELECT 
                COALESCE(
                    (SELECT ms_qty.medstock_qty 
                     FROM medstock ms_qty
                     WHERE ms_qty.medstock_id = ? AND ms_qty.medstock_dateadded < ? LIMIT 1), 
                0) - COALESCE(
                    (SELECT SUM(pm.pm_medqty) 
                     FROM prescribemed pm
                     JOIN consultations c ON pm.pm_consultid = c.consult_id
                     WHERE pm.pm_medstockid = ? AND c.consult_date < ?), 
                0) - COALESCE(
                    (SELECT SUM(mi.mi_medqty) 
                     FROM medissued mi
                     WHERE mi.mi_medstockid = ? AND mi.mi_date < ?), 
                0) AS medicine_balance_quarter";
    
        // Prepare and execute the query with the correct parameters
        $stmt = $this->conn->prepare($query);
        $stmt->execute([
            $medstock_id, $quarterStart, // Check stock quantity before the start of the quarter
            $medstock_id, $quarterStart, // Calculate prescribed quantity before the quarter
            $medstock_id, $quarterStart  // Calculate issued quantity before the quarter
        ]);
    
        // Fetch the calculated balance before the quarter
        $balance = $stmt->fetchColumn();
    
        // Return the calculated medicine balance before the selected quarter
        return $balance;
    }
       
    


    // Method to calculate the quantity of Medstock added during the quarter
    public function calculateMedstockAdded($medstock_id, $quarterStart, $quarterEnd) {
        $query = "SELECT COALESCE(
                        (SELECT ms2.medstock_qty 
                         FROM medstock ms2 
                         WHERE ms2.medstock_id = ? 
                         AND ms2.medstock_dateadded BETWEEN ? AND ?), 
                        0) AS medstock_added";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $quarterStart, $quarterEnd]);
        return $stmt->fetchColumn(); // Return the quantity of Medstock added
    }

    // Method to add a new Medstock entry to the linked list
    public function addMedstockToList($medstock_id, $item, $unit, $expiry_date, $quarterStart, $quarterEnd) {
        // Calculate necessary values for the Medstock node
        $medicine_balance_quarter = $this->calculateMedicineBalanceQuarter($medstock_id, $quarterStart, $quarterEnd);
        $medstock_added = $this->calculateMedstockAdded($medstock_id, $quarterStart, $quarterEnd);
        $total_start_balance = $medicine_balance_quarter + $medstock_added;
        $total_prescribed = $this->calculateTotalPrescribed($medstock_id, $quarterStart, $quarterEnd);
        $total_issued = $this->calculateTotalIssued($medstock_id, $quarterStart, $quarterEnd);
        
        // Calculate end_balance as total_start_balance minus the sum of total_prescribed and total_issued
        $end_balance = $total_start_balance - ($total_prescribed + $total_issued);
        
        // Create a new MedstockNode with calculated values
        $newNode = new MedstockNode($medstock_id, $item, $unit, $expiry_date);
        $newNode->medicine_balance_quarter = $medicine_balance_quarter;
        $newNode->medstock_added = $medstock_added;
        $newNode->total_start_balance = $total_start_balance;
        $newNode->total_issued = $total_issued;
        $newNode->total_prescribed = $total_prescribed;
        $newNode->end_balance = $end_balance;
        
        // Insert the new node into the linked list
        if ($this->head === null) {
            $this->head = $newNode; // If the list is empty, set the head to the new node
        } else {
            $current = $this->head;
            // Traverse the list to find the last node
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode; // Insert the new node at the end of the list
        }
    }

    // Method to fetch and store Medstocks from the database
    public function fetchAndStoreMedstocks($quarterStart, $quarterEnd) {
        $query = "SELECT ms.medstock_id, CONCAT(m.medicine_name, ' ', ms.medstock_dosage) AS item, ms.medstock_unit, ms.medstock_expirationdt AS expiry_date
                  FROM medstock ms
                  JOIN medicine m ON ms.medicine_id = m.medicine_id
                  WHERE ms.medstock_dateadded <= ? OR ms.medstock_dateadded BETWEEN ? AND ?"; // Use the correct condition
    
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$quarterEnd, $quarterStart, $quarterEnd]); // Execute with quarterStart and quarterEnd as parameters
    
        // Loop through the results and add each Medstock to the list
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->addMedstockToList(
                $row['medstock_id'], 
                $row['item'], 
                $row['medstock_unit'], 
                $row['expiry_date'],
                $quarterStart,
                $quarterEnd
            );
        }
    }
    

    // Method to retrieve all Medstocks as an array
    public function getAllMedstocksAsArray() {
        $data = []; // Array to hold the data
        $current = $this->head; // Start from the head of the linked list

        // Traverse the linked list and add each Medstock to the data array
        while ($current !== null) {
            $data[] = [
                'medstock_id' => $current->medstock_id,
                'item' => $current->item,
                'unit' => $current->unit,
                'expiry_date' => $current->expiry_date,
                'medicine_balance_quarter' => $current->medicine_balance_quarter,
                'medstock_added' => $current->medstock_added,
                'total_start_balance' => $current->total_start_balance,
                'total_issued' => $current->total_issued,
                'total_prescribed' => $current->total_prescribed,
                'end_balance' => $current->end_balance
            ];
            $current = $current->next; // Move to the next node
        }
        return $data; // Return the data array
    }
}
?>