<?php
// Medicine class
class Medicine {
    public $medicine_id;
    public $medicine_name;
    public $medicine_category;

    public function __construct($id, $name, $category) {
        $this->medicine_id = $id; 
        $this->medicine_name = $name;
        $this->medicine_category = $category;
    }
}  

// Medstock class
class Medstock { 
    public $medstock_id;
    public $medicine_id;
    public $medstock_origqty;
    public $medstock_unit;
    public $medstock_qty;
    public $medstock_dosage; 
    public $medstock_dateadded;
    public $medstock_timeadded;
    public $medstock_expirationdt;
    public $medstock_disabled;

    
    // Constructor initializes a stock entry with its details
    public function __construct($medstock_id, $medicine_id, $medstock_origqty, $unit, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled) {
        $this->medstock_id = $medstock_id;
        $this->medicine_id = $medicine_id;
        $this->medstock_origqty = $medstock_origqty;
        $this->medstock_unit = $unit;
        $this->medstock_qty = $quantity;
        $this->medstock_dosage = $dosage;
        $this->medstock_dateadded = $date_added;
        $this->medstock_timeadded = $time_added;
        $this->medstock_expirationdt = $expiration_date;
        $this->medstock_disabled = $disabled;
    }
}

// MedListNode class
class MedListNode {
    public $item;
    public $next;

    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
}

// MedicineLinkedList class
class MedicineLinkedList {
    public $head;

        // Constructor initializes a node with a data item
    public function __construct() {
        $this->head = null;
    }

    // MedicineLinkedList class
    public function add($item) {
        $newNode = new MedListNode($item);
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

    
    // Retrieves all nodes as an array
    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current->item;
            $current = $current->next;
        }
        return $nodes;
    }

      // Finds an item in the list by its ID
    public function find($id) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicine_id == $id) { 
                return $current->item;
            }
            $current = $current->next;
        }
        return null;
    }

     // Removes an item from the list by its ID
    public function remove($id) {
        if ($this->head === null) return false; 

        if ($this->head->item->medicine_id == $id) {
            $this->head = $this->head->next; 
            return true;
        }

        $current = $this->head;
        while ($current->next !== null) {
            if ($current->next->item->medicine_id == $id) {
                $current->next = $current->next->next; 
                return true;
            }
            $current = $current->next;
        }
        return false; 
    }

      // Finds an item in the list by its name
    public function findByName($name) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->medicine_name, $name) === 0) { 
                return $current->item; 
            }
            $current = $current->next;
        }
        return null; 
    }

    // Checks if a medicine with the given name exists
    public function medicineExists($name) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->medicine_name, $name) === 0) { 
                return true; 
            }
            $current = $current->next;
        } 
        return false; 
    }
    
}

// MedicineManager class
class MedicineManager {
    private $db;
    public $medicines;
    public $medstocks;

     // Constructor initializes the manager and loads data
    public function __construct($db) {
        $this->db = $db; 
        $this->medicines = new MedicineLinkedList();
        $this->medstocks = new MedicineLinkedList();
        $this->loadMedicines();
        $this->loadMedstocks();
    }

    // Loads medicines from the database into the linked list
    private function loadMedicines() {
        $sql = "SELECT * FROM medicine";
        $stmt = $this->db->query($sql); // Use PDO query method
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medicine = new Medicine($row['medicine_id'], $row['medicine_name'], $row['medicine_category']);
            $this->medicines->add($medicine);
        }
    }

     // Loads stocks from the database into the linked list
    private function loadMedstocks() {
        $sql = "
            SELECT 
                ms.medstock_id,
                ms.medicine_id,
                ms.medstock_qty,
                ms.medstock_unit,
                ms.medstock_qty - 
                    (COALESCE(SUM(p.pm_medqty), 0) + COALESCE(SUM(mi.mi_medqty), 0)) AS current_qty,
                ms.medstock_dosage,
                ms.medstock_dateadded,
                ms.medstock_timeadded,
                ms.medstock_expirationdt,
                ms.medstock_disable
            FROM  
                medstock ms
            LEFT JOIN 
                (SELECT pm_medstockid, SUM(pm_medqty) AS pm_medqty
                FROM prescribemed
                GROUP BY pm_medstockid) p 
                ON ms.medstock_id = p.pm_medstockid
            LEFT JOIN 
                (SELECT mi_medstockid, SUM(mi_medqty) AS mi_medqty
                FROM medissued
                GROUP BY mi_medstockid) mi 
                ON ms.medstock_id = mi.mi_medstockid
            GROUP BY 
                ms.medstock_id, ms.medicine_id, ms.medstock_unit, ms.medstock_qty, ms.medstock_dosage, 
                ms.medstock_dateadded, ms.medstock_timeadded, ms.medstock_expirationdt, ms.medstock_disable;
        ";
    
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medstock = new Medstock(
                $row['medstock_id'],
                $row['medicine_id'],
                $row['medstock_qty'],
                $row['medstock_unit'], 
                $row['current_qty'],     
                $row['medstock_dosage'],
                $row['medstock_dateadded'],
                $row['medstock_timeadded'],
                $row['medstock_expirationdt'],
                $row['medstock_disable']
            );
            $this->medstocks->add($medstock);
        }
    }
    
    // Inserts a new medicine into the database and linked list
    public function insertMedicine($admin_id, $name, $category) {
        if ($this->medicines->medicineExists($name)) {
            echo "Medicine already exists.<br>";
            return false;
        }

        $setAdminIdQuery = "SET @admin_id = :admin_id";
        $setStmt = $this->db->prepare($setAdminIdQuery);
        $setStmt->bindValue(':admin_id', $admin_id);
        $setStmt->execute();

        $sql = "INSERT INTO medicine (medicine_name, medicine_category) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->execute([$name, $category]);
            $medicine_id = $this->db->lastInsertId(); // Get last inserted ID
            $medicine = new Medicine($medicine_id, $name, $category);
            $this->medicines->add($medicine);
            echo "Medicine inserted successfully.<br>";
            return true;
        } else {
            echo "Error inserting medicine.<br>";
            return false;
        } 
    }
    
    // Inserts a new stock entry into the database and linked list
    public function insertMedstock($admin_id, $medicine_id, $unit, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled) {
        $setAdminIdQuery = "SET @admin_id = :admin_id";
        $setStmt = $this->db->prepare($setAdminIdQuery);
        $setStmt->bindValue(':admin_id', $admin_id);
        $setStmt->execute();

        $sql = "INSERT INTO medstock (medicine_id, medstock_unit, medstock_qty, medstock_dosage, medstock_dateadded, medstock_timeadded, medstock_expirationdt, medstock_disable) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->execute([$medicine_id, $unit, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled]);
            echo "Medstock inserted successfully.<br>";
            return true;
        } else {
            echo "Error inserting medstock.<br>";
            return false;
        }
    }

    // Retrieves all medicines as an array
    public function getAllMedicines() {
        return $this->medicines->getAllNodes();
    }

    // Retrieves all stock entries as an array
    public function getAllMedstocks() {
        return $this->medstocks->getAllNodes();
    }

    // Retrieves all stock entries with their associated medicine names
    public function getAllItems() {
        $medstocks = $this->medstocks->getAllNodes();
        $medicines = $this->medicines->getAllNodes();
        
        $medicineMap = [];
        
        foreach ($medicines as $medicine) {
            $medicineMap[$medicine->medicine_id] = $medicine->medicine_name;
        }
    
        $combinedItems = [];
        foreach ($medstocks as $medstock) {
            $medstock->medicine_name = $medicineMap[$medstock->medicine_id] ?? 'Unknown'; // Set medicine name
            $combinedItems[] = $medstock; // Add the medstock object with the medicine name
        }
    
        return $combinedItems; 
    }

    // Retrieves medicines with their associated stock count
    public function getMedicinesWithStockCount() {
        $medstocks = $this->medstocks->getAllNodes();
        $medicines = $this->medicines->getAllNodes();
        
        $stockCountMap = [];
    
        // Count occurrences of each medicine_id in medstocks
        foreach ($medstocks as $medstock) {
            if (!isset($stockCountMap[$medstock->medicine_id])) {
                $stockCountMap[$medstock->medicine_id] = 0;
            }
            // Increment occurrence for each medstock entry
            $stockCountMap[$medstock->medicine_id]++;
        }

        $combinedItems = [];
        foreach ($medicines as $medicine) {
            $combinedItems[] = [
                'medicine_id' => $medicine->medicine_id,
                'medicine_name' => $medicine->medicine_name,
                'medicine_category' => $medicine->medicine_category,
                'stock_count' => $stockCountMap[$medicine->medicine_id] ?? 0 
            ];
        }
    
        return $combinedItems; 
    }
    
    // Updates a medicine's details in the database and linked list
    public function updateMedicine($admin_id, $medicine_id, $name, $category) {
        try {
            // Set admin ID for the trigger (if required in session)
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
    
            // Update the medicine record directly
            $sql = "UPDATE medicine SET medicine_name = ?,  medicine_category = ? WHERE medicine_id = ?";
            $stmt = $this->db->prepare($sql);
    
            if ($stmt->execute([$name, $category, $medicine_id])) {
                echo "Medicine updated successfully.<br>";
                return true;
            } else {
                throw new Exception("Failed to execute the update statement.");
            }
        } catch (Exception $e) {
            echo "Error updating medicine: " . $e->getMessage() . "<br>";
            return false;
        }
    }
    
    

        // Updates a stock entry's details in the database and linked list
    public function updateMedstock($admin_id, $medstock_id, $medicine_id, $medstock_unit, $medicine_qty, $medicine_dosage, $medicine_expirationdt, $medicine_disable) {
        try {
            // Set the admin ID as a session variable for the stored procedure if needed
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
            
            // Prepare the call to the stored procedure
            $query = "CALL update_medicine_stock(:medstock_id, :medicine_id, :medstock_unit, :medicine_qty, :medicine_dosage, :medicine_expirationdt, :medicine_disable)";
            $stmt = $this->db->prepare($query);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare SQL statement.");
            }
    
            // Bind the parameters to the stored procedure
            $stmt->bindValue(':medstock_id', $medstock_id, PDO::PARAM_INT);
            $stmt->bindValue(':medicine_id', $medicine_id, PDO::PARAM_INT);
            $stmt->bindValue(':medstock_unit', $medstock_unit, PDO::PARAM_INT);
            $stmt->bindValue(':medicine_qty', $medicine_qty, PDO::PARAM_INT);
            $stmt->bindValue(':medicine_dosage', $medicine_dosage, PDO::PARAM_STR);
            $stmt->bindValue(':medicine_expirationdt', $medicine_expirationdt, PDO::PARAM_STR);
            $stmt->bindValue(':medicine_disable', $medicine_disable, PDO::PARAM_STR);
    
            // Execute the stored procedure
            if ($stmt->execute()) {
                // Optionally update the linked list in memory (if you have it)
                $medstock = $this->medstocks->find($medstock_id);
                if ($medstock) {
                    $medstock->medicine_id = $medicine_id; // Update to new medicine ID
                    $medstock->medstock_qty = $medicine_qty; 
                    $medstock->medstock_dosage = $medicine_dosage;
                    $medstock->medstock_expirationdt = $medicine_expirationdt;
                    $medstock->medstock_disabled = $medicine_disable;
                }
    
                return ['status' => 'success', 'message' => 'Medstock updated successfully.'];
            } else {
                throw new Exception("Failed to execute update procedure.");
            }
        } catch (Exception $e) {
            // Return error message
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    } 
    
    // Medicine total available stock using stored function 
    public function getMedicineStock($medicine_id) {
        // SQL to call the stored function
        $query = "SELECT get_medicine_stock(:medicine_id) AS remaining_stock";
        
        // Prepare and execute the query
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':medicine_id', $medicine_id, PDO::PARAM_INT);
        $stmt->execute();
        
        // Fetch and return the result
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        return $data['remaining_stock'];
    }
    
    
    
    // Deletes a medicine from the database and linked list
    public function deleteMedicine($medicine_id) {
        // Remove from the database
        $sql = "DELETE FROM medicine WHERE medicine_id = ?";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->execute([$medicine_id]);
            // Also remove from the linked list
            if ($this->medicines->remove($medicine_id)) {
                echo "Medicine deleted successfully.<br>";
                return true;
            } else {
                echo "Error deleting medicine from linked list.<br>";
            }
        } else {
            echo "Error deleting medicine from database.<br>";
        }
        return false;
    }



    
}
?>
