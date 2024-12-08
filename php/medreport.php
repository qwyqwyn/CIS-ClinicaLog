<?php
class Medstock {
    // Class properties to hold medstock details.
    public $medstock_id;
    public $item;
    public $unit;

    // Property to store expiration date.
    public $expiry_date;
    public $medicine_balance_month;
    public $medstock_added;

    // Properties for tracking stock balances and usage.
    public $total_start_balance;
    public $total_prescribed;
    public $total_issued;

    // Property to track end balance of the stock.
    public $end_balance;

    // Constructor to initialize a new Medstock object.
    public function __construct($medstock_id, $item, $unit, $expiry_date) {
        $this->medstock_id = $medstock_id;
        $this->item = $item;
        $this->unit = $unit;

        // Assign expiration date during object creation.
        $this->expiry_date = $expiry_date;
    }
}

class MedicineManager {
    private $conn; // Database connection.
    private $medstocks = []; // Array to store Medstock objects.

    // Initialize MedicineManager with a database connection.
    public function __construct($conn) {
        $this->conn = $conn;
    }

    // Calculate total prescribed quantity within a date range.
    public function calculateTotalPrescribed($medstock_id, $quarterStart, $quarterEnd) {
        $query = "SELECT COALESCE(SUM(pm.pm_medqty), 0) AS total_prescribed
                  FROM prescribemed pm
                  JOIN consultations c ON pm.pm_consultid = c.consult_id
                  WHERE pm.pm_medstockid = ?
                  AND c.consult_date BETWEEN ? AND ?";

        // Prepare the query to prevent SQL injection.
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $quarterStart, $quarterEnd]);

        // Fetch and return the total prescribed quantity.
        return $stmt->fetchColumn();
    }

    // Calculate total issued quantity within a date range.
    public function calculateTotalIssued($medstock_id, $quarterStart, $quarterEnd) {
        $query = "SELECT COALESCE(SUM(mi.mi_medqty), 0) AS total_issued
                  FROM medissued mi
                  WHERE mi.mi_medstockid = ? 
                  AND mi.mi_date BETWEEN ? AND ?";

        // Prepare and execute the query with parameters.
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $quarterStart, $quarterEnd]);

        // Return the total issued quantity.
        return $stmt->fetchColumn();
    }

    // Calculate medicine balance before a given date.
    public function calculateMedicineBalanceMonth($medstock_id, $cutoffDate) {
        $query = "SELECT COALESCE(
                        (SELECT ms_qty.medstock_qty 
                         FROM medstock ms_qty
                         WHERE ms_qty.medstock_id = ? AND ms_qty.medstock_dateadded < ?) 
                        - COALESCE((SELECT SUM(pm.pm_medqty) 
                                     FROM prescribemed pm
                                     JOIN consultations c ON pm.pm_consultid = c.consult_id
                                     WHERE pm.pm_medstockid = ? AND c.consult_date < ?), 0)
                        - COALESCE((SELECT SUM(mi.mi_medqty) 
                                     FROM medissued mi
                                     WHERE mi.mi_medstockid = ? AND mi.mi_date < ?), 0), 
                        0) AS medicine_balance_month";

        // Prepare the statement for safe execution.
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $cutoffDate, $medstock_id, $cutoffDate, $medstock_id, $cutoffDate]);

        // Return the calculated balance.
        return $stmt->fetchColumn();
    }

    // Calculate stock added within a specified date range.
    public function calculateMedstockAdded($medstock_id, $startDate, $endDate) {
        $query = "SELECT COALESCE(
                        (SELECT ms2.medstock_qty 
                         FROM medstock ms2 
                         WHERE ms2.medstock_id = ? 
                         AND ms2.medstock_dateadded BETWEEN ? AND ?), 
                        0) AS medstock_added";

        // Prepare the query to avoid SQL injection issues.
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$medstock_id, $startDate, $endDate]);

        // Return the amount of stock added.
        return $stmt->fetchColumn();
    }

    // Add a medstock entry with calculated stock details.
    public function addMedstock($medstock_id, $item, $unit, $expiry_date, $selectedDate, $quarterStart, $quarterEnd) {
        // Calculate stock balances and usage.
        $medicine_balance_month = $this->calculateMedicineBalanceMonth($medstock_id, $selectedDate);
        $medstock_added = $this->calculateMedstockAdded($medstock_id, $quarterStart, $quarterEnd);
        $total_start_balance = $medicine_balance_month + $medstock_added;

        // Calculate prescriptions and issued amounts.
        $total_prescribed = $this->calculateTotalPrescribed($medstock_id, $quarterStart, $quarterEnd);
        $total_issued = $this->calculateTotalIssued($medstock_id, $quarterStart, $quarterEnd);
        $end_balance = $total_start_balance - ($total_prescribed + $total_issued);

        // Create a Medstock object and set its attributes.
        $medstock = new Medstock($medstock_id, $item, $unit, $expiry_date);
        $medstock->medicine_balance_month = $medicine_balance_month;
        $medstock->medstock_added = $medstock_added;
        $medstock->total_start_balance = $total_start_balance;

        // Set remaining calculated properties.
        $medstock->total_prescribed = $total_prescribed;
        $medstock->total_issued = $total_issued;
        $medstock->end_balance = $end_balance;

        // Store the Medstock object for later retrieval.
        $this->medstocks[] = $medstock;
    }

    // Fetch medstock data from the database and populate the list. 
    public function fetchAndStoreMedstocks($selectedDate, $quarterStart, $quarterEnd) {
        $query = "SELECT ms.medstock_id, CONCAT(m.medicine_name, ' ', ms.medstock_dosage) AS item, ms.medstock_unit, ms.medstock_expirationdt AS expiry_date
                  FROM medstock ms
                  JOIN medicine m ON ms.medicine_id = m.medicine_id
                  WHERE ms.medstock_dateadded < ? 
                  OR (ms.medstock_dateadded BETWEEN ? AND ?)";

        // Prepare the query and execute with date parameters.
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$selectedDate, $quarterStart, $quarterEnd]);

        // Loop through the results and add each medstock.
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->addMedstock(
                $row['medstock_id'],
                $row['item'],
                $row['medstock_unit'],
                $row['expiry_date'],
                $selectedDate,
                $quarterStart,
                $quarterEnd
            );
        }
    }

    // Retrieve medstocks as an array for external use.
    public function getAllMedstocksAsArray() {
        $data = []; // Initialize an empty array to store medstock data.

        // Loop through each stored medstock and format the output.
        foreach ($this->medstocks as $medstock) {
            $data[] = [
                'medstock_id' => $medstock->medstock_id,
                'item' => $medstock->item,
                'unit' => $medstock->unit,
                'expiry_date' => $medstock->expiry_date,
                'medicine_balance_month' => $medstock->medicine_balance_month,
                'medstock_added' => $medstock->medstock_added,
                'total_start_balance' => $medstock->total_start_balance,
                'total_prescribed' => $medstock->total_prescribed,
                'total_issued' => $medstock->total_issued,
                'end_balance' => $medstock->end_balance
            ];
        }

        return $data; // Return the collected data array.
    }
}
?>
