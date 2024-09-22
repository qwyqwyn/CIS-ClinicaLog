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
    public $medstock_qty;
    public $medstock_dosage;
    public $medstock_dateadded;
    public $medstock_timeadded;
    public $medstock_expirationdt;
    public $medstock_disabled;

    public function __construct($medstock_id, $medicine_id, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled) {
        $this->medstock_id = $medstock_id;
        $this->medicine_id = $medicine_id;
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

    public function __construct() {
        $this->head = null;
    }

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

    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current->item;
            $current = $current->next;
        }
        return $nodes;
    }

    public function find($id) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicine_id == $id) { // Use medicine_id for searching
                return $current->item;
            }
            $current = $current->next;
        }
        return null;
    }

    public function remove($id) {
        if ($this->head === null) return false; // List is empty

        if ($this->head->item->medicine_id == $id) {
            $this->head = $this->head->next; // Remove head
            return true;
        }

        $current = $this->head;
        while ($current->next !== null) {
            if ($current->next->item->medicine_id == $id) {
                $current->next = $current->next->next; // Remove node
                return true;
            }
            $current = $current->next;
        }
        return false; // Not found
    }

    public function findByName($name) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->medicine_name, $name) === 0) { // Case-insensitive comparison
                return $current->item; // Return the medicine object
            }
            $current = $current->next;
        }
        return null; // Medicine not found
    }

    public function medicineExists($name) {
        $current = $this->head;
        while ($current !== null) {
            if (strcasecmp($current->item->medicine_name, $name) === 0) { // Case-insensitive comparison
                return true; // Medicine exists
            }
            $current = $current->next;
        }
        return false; // Medicine does not exist
    }
    
}

// MedicineManager class
class MedicineManager {
    private $db;
    public $medicines;
    public $medstocks;

    public function __construct($db) {
        $this->db = $db; // Assuming $db is a PDO instance
        $this->medicines = new MedicineLinkedList();
        $this->medstocks = new MedicineLinkedList();
        $this->loadMedicines();
        $this->loadMedstocks();
    }

    private function loadMedicines() {
        $sql = "SELECT * FROM medicine";
        $stmt = $this->db->query($sql); // Use PDO query method
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medicine = new Medicine($row['medicine_id'], $row['medicine_name'], $row['medicine_category']);
            $this->medicines->add($medicine);
        }
    }

    private function loadMedstocks() {
        $sql = "SELECT * FROM medstock";
        $stmt = $this->db->query($sql); // Use PDO query method
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medstock = new Medstock(
                $row['medstock_id'],
                $row['medicine_id'],
                $row['medstock_qty'],
                $row['medstock_dosage'],
                $row['medstock_dateadded'],
                $row['medstock_timeadded'],
                $row['medstock_expirationdt'],
                $row['medstock_disable']
            );
            $this->medstocks->add($medstock);
        }
    }

    public function insertMedicine($name, $category) {
        if ($this->medicines->medicineExists($name)) {
            echo "Medicine already exists.<br>";
            return false;
        }

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

    public function insertMedstock($medicine_id, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled) {
        $sql = "INSERT INTO medstock (medicine_id, medstock_qty, medstock_dosage, medstock_dateadded, medstock_timeadded, medstock_expirationdt, medstock_disable) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        if ($stmt) {
            $stmt->execute([$medicine_id, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled]);
            $medstock_id = $this->db->lastInsertId(); // Get last inserted ID
            $medstock = new Medstock($medstock_id, $medicine_id, $quantity, $dosage, $date_added, $time_added, $expiration_date, $disabled);
            $this->medstocks->add($medstock);
            echo "Medstock inserted successfully.<br>";
            return true;
        } else {
            echo "Error inserting medstock.<br>";
            return false;
        }
    }

    public function getAllMedicines() {
        return $this->medicines->getAllNodes();
    }

    public function getAllMedstocks() {
        return $this->medstocks->getAllNodes();
    }

    public function getAllItems() {
        $medstocks = $this->medstocks->getAllNodes();
        $medicines = $this->medicines->getAllNodes();
        
        $medicineMap = [];
        
        // Create a map of medicine IDs to medicine names
        foreach ($medicines as $medicine) {
            $medicineMap[$medicine->medicine_id] = $medicine->medicine_name;
        }
    
        // Combine medstocks with corresponding medicine names
        $combinedItems = [];
        foreach ($medstocks as $medstock) {
            $medstock->medicine_name = $medicineMap[$medstock->medicine_id] ?? 'Unknown'; // Set medicine name
            $combinedItems[] = $medstock; // Add the medstock object with the medicine name
        }
    
        return $combinedItems; // Return the combined array
    }
    public function getMedicinesWithStockCount() {
        $medstocks = $this->medstocks->getAllNodes();
        $medicines = $this->medicines->getAllNodes();
        
        // Create a map to count occurrences (stocks) per medicine
        $stockCountMap = [];
    
        // Count occurrences of each medicine_id in medstocks
        foreach ($medstocks as $medstock) {
            if (!isset($stockCountMap[$medstock->medicine_id])) {
                $stockCountMap[$medstock->medicine_id] = 0;
            }
            // Increment occurrence for each medstock entry
            $stockCountMap[$medstock->medicine_id]++;
        }
    
        // Combine medicines with their stock counts
        $combinedItems = [];
        foreach ($medicines as $medicine) {
            $combinedItems[] = [
                'medicine_id' => $medicine->medicine_id,
                'medicine_name' => $medicine->medicine_name,
                'medicine_category' => $medicine->medicine_category,
                'stock_count' => $stockCountMap[$medicine->medicine_id] ?? 0 // Default to 0 if no stock
            ];
        }
    
        return $combinedItems; // Return the combined array
    }
    

    public function updateMedicine($medicine_id, $name, $category) {
        try {
            // Prepare the SQL update statement
            $sql = "UPDATE medicine SET medicine_name = ?, medicine_category = ? WHERE medicine_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare the SQL statement.");
            }
    
            // Execute the statement with bound parameters
            if ($stmt->execute([$name, $category, $medicine_id])) {
                // Update the linked list after the database operation succeeds
                $medicine = $this->medicines->find($medicine_id);
                if ($medicine) {
                    $medicine->medicine_name = $name;
                    $medicine->medicine_category = $category;
                }
                echo "Medicine updated successfully.<br>";
                return true;
            } else {
                // Handle execution failure
                throw new Exception("Failed to execute the update statement.");
            }
        } catch (Exception $e) {
            // Display detailed error message
            echo "Error updating medicine: " . $e->getMessage() . "<br>";
            return false;
        }
    }
    

    
    public function updateMedstock($medstock_id, $medicine_id, $medicine_qty, $medicine_dosage, $medicine_expirationdt, $medicine_disable) {
        try {
            
            // Prepare the SQL statement to update medstock
            $sql = "UPDATE medstock SET medicine_id = ?, medstock_qty = ?, medstock_dosage = ?, medstock_expirationdt = ?, medstock_disable = ? WHERE medstock_id = ?";
            $stmt = $this->db->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Failed to prepare SQL statement.");
            }
    
            // Execute the statement with bound parameters
            if ($stmt->execute([$medicine_id, $medicine_qty, $medicine_dosage, $medicine_expirationdt, $medicine_disable, $medstock_id])) {
                // Update the linked list
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
                throw new Exception("Failed to execute update statement.");
            }
        } catch (Exception $e) {
            // Return error message
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
    
    
    

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
