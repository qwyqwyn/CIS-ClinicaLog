<?php
class OffCampus {
    public $offcampus_id;
    public $offcampus_medstockid;
    public $offcampus_medstockname;
    public $offcampus_medqty;
    public $offcampus_date;

    public function __construct($id, $medstockid, $medstockname, $medqty, $date) {
        $this->offcampus_id = $id;
        $this->offcampus_medstockid = $medstockid;
        $this->offcampus_medstockname = $medstockname;
        $this->offcampus_medqty = $medqty;
        $this->offcampus_date = $date;
    }
}

class LinkedListNode {
    public $data;
    public $next;

    public function __construct($data) {
        $this->data = $data;
        $this->next = null;
    }
}
 
class OffCampusLinked {
    private $head;

    public function __construct() {
        $this->head = null;
    }

    public function add($data) {
        $newNode = new LinkedListNode($data);
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

    public function getAll() {
        $dataArray = [];
        $current = $this->head;
        while ($current !== null) {
            $dataArray[] = $current->data;
            $current = $current->next;
        }
        return $dataArray;
    }
}

class OffCampusManager {
    private $db;
    public $offcampusRecords;

    public function __construct($db) {
        $this->db = $db;
        $this->offcampusRecords = new OffCampusLinked();
        $this->loadOffCampusData();
    }

    public function getAllOffCampusData() {
        $allRecords = $this->offcampusRecords->getAll();
    
        $filteredRecords = [];
    
        foreach ($allRecords as $record) { 
            $filteredRecords[] = [
                'id' => $record->offcampus_id,
                'medstockid' => $record->offcampus_medstockid,
                'medstockname' => $record->offcampus_medstockname,
                'medqty' => $record->offcampus_medqty,
                'date' => $record->offcampus_date
            ];
        }
    
        return $filteredRecords;
    }
    

    private function loadOffCampusData() {
        $sql = "SELECT mi.mi_id, mi.mi_medstockid, m.medicine_name, mi.mi_medqty, mi.mi_date
                FROM medissued mi
                JOIN medstock ms ON mi.mi_medstockid = ms.medstock_id
                JOIN medicine m ON ms.medicine_id = m.medicine_id";
        
        $stmt = $this->db->query($sql);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $offCampus = new OffCampus(
                $row['mi_id'],
                $row['mi_medstockid'],
                $row['medicine_name'],
                $row['mi_medqty'], 
                $row['mi_date']
            );

            $this->offcampusRecords->add($offCampus);
        }
    }


    public function insertOffCampusRecord($admin_id, $medstockid, $medqty, $date) {
        $setAdminIdQuery = "SET @admin_id = :admin_id";
        $setStmt = $this->db->prepare($setAdminIdQuery);
        $setStmt->bindValue(':admin_id', $admin_id);
        $setStmt->execute();

        $sql = "INSERT INTO medissued (mi_medstockid, mi_medqty, mi_date) VALUES (?, ?, ?)";
        $stmt = $this->db->prepare($sql);

        try {
            if ($stmt->execute([$medstockid, $medqty, $date])) {
                return [
                    'status' => 'success',
                    'message' => 'Off-campus record inserted successfully.',
                    'id' => $this->db->lastInsertId() 
                ];
            }
        } catch (PDOException $e) {
            error_log("Error inserting record: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while inserting the record.'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to insert record.'
        ];
    }

    public function updateOffCampusRecord($admin_id, $id, $medstockid, $medqty, $date) {
        $setAdminIdQuery = "SET @admin_id = :admin_id";
        $setStmt = $this->db->prepare($setAdminIdQuery);
        $setStmt->bindValue(':admin_id', $admin_id);
        $setStmt->execute();

        $sql = "UPDATE medissued SET mi_medstockid = ?, mi_medqty = ?, mi_date = ? WHERE mi_id = ?";
        $stmt = $this->db->prepare($sql);

        try {
            if ($stmt->execute([$medstockid, $medqty, $date, $id])) {
                return [
                    'status' => 'success',
                    'message' => 'Off-campus record updated successfully.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error updating record: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while updating the record.'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to update record.'
        ];
    }

    public function deleteOffCampusRecord($admin_id, $id) {
        $setAdminIdQuery = "SET @admin_id = :admin_id";
        $setStmt = $this->db->prepare($setAdminIdQuery);
        $setStmt->bindValue(':admin_id', $admin_id);
        $setStmt->execute();
        
        $sql = "DELETE FROM medissued WHERE mi_id = ?";
        $stmt = $this->db->prepare($sql);

        try {
            if ($stmt->execute([$id])) {
                return [
                    'status' => 'success',
                    'message' => 'Off-campus record deleted successfully.'
                ];
            }
        } catch (PDOException $e) {
            error_log("Error deleting record: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred while deleting the record.'
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Failed to delete record.'
        ];
    }


}

?>