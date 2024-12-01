<?php
class MedicalRecords {
    public $medicalrec_id;
    public $medicalrec_patientid;
    public $medicalrec_filename;
    public $medicalrec_file;
    public $medicalrec_comment;
    public $medicalrec_dateadded;
    public $medicalrec_timeadded;

    public function __construct($id, $patientid, $filename, $file, $comment, $dateadded, $timeadded) {
        $this->medicalrec_id = $id;
        $this->medicalrec_patientid = $patientid;
        $this->medicalrec_filename = $filename;
        $this->medicalrec_file = $file;
        $this->medicalrec_comment = $comment;
        $this->medicalrec_dateadded = $dateadded;
        $this->medicalrec_timeadded = $timeadded;
    }
}

class MedRecNode {
    public $item;
    public $next;

    public function __construct($item) {
        $this->item = $item;
        $this->next = null;
    }
} 

class MedRecordsList {
    public $head;

    public function __construct() {
        $this->head = null;
    }

    public function add($item) {
        $newNode = new MedRecNode($item);
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

    public function MedRecExists($patientid, $filename) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicalrec_patientid === $patientid && 
                strcasecmp($current->item->medicalrec_filename, $filename) === 0) {
                return true;
            }
            $current = $current->next;
        }
        return false;
    }

    public function getDuplicateFilenames($patientid, $filenames) {
        $current = $this->head;
        $duplicateFilenames = [];
    
        while ($current !== null) {
            foreach ($filenames as $filename) {
                if ($current->item->medicalrec_patientid === $patientid && 
                    strcasecmp($current->item->medicalrec_filename, $filename) === 0) {
                    $duplicateFilenames[] = $filename;
                }
            }
            $current = $current->next;
        }
            return $duplicateFilenames;
    }

    public function isDuplicateFilename($patientid, $filename) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicalrec_patientid === $patientid && 
                    strcasecmp($current->item->medicalrec_filename, $filename) === 0) { {
                return true;  
            }
            $current = $current->next;
        }
        return false; 
        }
    }   
    
    

    public function findMedicalRecordById($medicalrec_id) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->item->medicalrec_id === $medicalrec_id) {
                return $current->item; 
            }
            $current = $current->next; 
        }
        return null; 
    }
    
    
}

class MedRecManager {
    private $db;
    public $medicalrecs;

    public function __construct($db) {
        $this->db = $db; 
        $this->medicalrecs = new MedRecordsList();
        $this->loadMedicalRecords();
    }

    private function loadMedicalRecords() {
        $sql = "SELECT * FROM medicalrec"; 
        $stmt = $this->db->query($sql); 
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $medicalrec = new MedicalRecords(
                $row['medicalrec_id'], $row['medicalrec_patientid'], $row['medicalrec_filename'], 
                $row['medicalrec_file'], $row['medicalrec_comment'], $row['medicalrec_dateadded'], 
                $row['medicalrec_timeadded']
            );        
            $this->medicalrecs->add($medicalrec); 
        }
    }

    public function getDuplicateFilenames($patientid, $filenames) {
        $duplicateFilenames = [];
    
        if (!is_array($filenames)) {
            $filenames = [$filenames]; 
        }
    
        foreach ($filenames as $filename) {
            if ($this->medicalrecs->MedRecExists($patientid, $filename)) {
                $duplicateFilenames[] = $filename;
            }
        }
    
        return $duplicateFilenames;
    }
    

    
    
    public function insertMedicalRecord($admin_id, $patientid, $filenames, $files, $comment, $dateadded, $timeadded) {
        try {    
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
            
            $sql = "INSERT INTO medicalrec (medicalrec_patientid, medicalrec_filename, medicalrec_file, medicalrec_comment, medicalrec_dateadded, medicalrec_timeadded) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);

            foreach ($filenames as $index => $filename) {
                $file = $files[$index]; 
                
                if ($stmt->execute([$patientid, $filename, $file, $comment, $dateadded, $timeadded])) {
                    $medicalrec_id = $this->db->lastInsertId();
                    $newRecord = new MedicalRecords($medicalrec_id, $patientid, $filename, $file, $comment, $dateadded, $timeadded);
                    $this->medicalrecs->add($newRecord);
                } else {
                    return [
                        'status' => 'error',
                        'message' => 'Failed to insert one or more medical records.'
                    ];
                }
            }

            return [
                'status' => 'success',
                'message' => 'All medical records inserted successfully.'
            ];
    
        } catch (PDOException $e) {
            return [ 
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    public function insertMedicalRecordbyPatient($patientid, $filenames, $files, $comment, $dateadded, $timeadded) {
        try {
            $this->db->beginTransaction();
            
            $sql = "INSERT INTO medicalrec (medicalrec_patientid, medicalrec_filename, medicalrec_file, medicalrec_comment, medicalrec_dateadded, medicalrec_timeadded) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
    
            foreach ($filenames as $index => $filename) {
                $file = $files[$index]; 
    
                if ($stmt->execute([$patientid, $filename, $file, $comment, $dateadded, $timeadded])) {
                    $medicalrec_id = $this->db->lastInsertId();
                    $newRecord = new MedicalRecords($medicalrec_id, $patientid, $filename, $file, $comment, $dateadded, $timeadded);
                    $this->medicalrecs->add($newRecord);
                    
                    $notifMessage = "Inserted New Medical Record $filename";
                    $sqlNotif = "INSERT INTO adminnotifs (notif_patid, notif_message, notif_status, notif_date_added) 
                                 VALUES (?, ?, 'unread', NOW())";
                    $stmtNotif = $this->db->prepare($sqlNotif);
                    $stmtNotif->execute([$patientid, $notifMessage]);
                } else {
                    
                    $this->db->rollBack();
                    return [
                        'status' => 'error',
                        'message' => 'Failed to insert one or more medical records.' 
                    ];
                }
            }

            $this->db->commit();
    
            return [
                'status' => 'success',
                'message' => 'All medical records and notifications inserted successfully.'
            ];
    
        } catch (PDOException $e) {

            $this->db->rollBack();
            return [ 
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    

    public function updateMedicalRecordbyPatient($medicalrec_id, $patientid, $filename, $comment) {
        try {
    
            if ($this->medicalrecs->MedRecExists($patientid, $filename)) {
                $existingRecord = $this->medicalrecs->findMedicalRecordById($medicalrec_id);
                if ($existingRecord && 
                    ($existingRecord->medicalrec_patientid !== $patientid || 
                     strcasecmp($existingRecord->medicalrec_filename, $filename) !== 0)) {
                    return [
                        'status' => 'error',
                        'message' => 'A medical record with this patient ID and filename already exists.'
                    ];
                }
            }
    
            $sql = "UPDATE medicalrec 
                    SET medicalrec_filename = ?,  medicalrec_comment = ?
                    WHERE medicalrec_id = ? AND medicalrec_patientid = ?";
            $stmt = $this->db->prepare($sql);
    
            if ($stmt->execute([$filename, $comment, $medicalrec_id, $patientid])) {
    
                $notifMessage = "Updated Medical Record $filename";  

                $sqlNotif = "INSERT INTO adminnotifs (notif_patid, notif_message, notif_status, notif_date_added) 
                             VALUES (?, ?, 'unread', NOW())";
                $stmtNotif = $this->db->prepare($sqlNotif);

                $stmtNotif->execute([$patientid, $notifMessage]);

                return [
                    'status' => 'success',
                    'message' => 'Medical record updated successfully and notification created.',
                    'medicalrec_id' => $medicalrec_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update medical record.'
                ];
            }
    
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    
    

    public function updateMedicalRecord($admin_id, $medicalrec_id, $patientid, $filename, $comment) {
        try {

            
            if ($this->medicalrecs->MedRecExists($patientid, $filename)) {
                $existingRecord = $this->medicalrecs->findMedicalRecordById($medicalrec_id);
                if ($existingRecord && 
                    ($existingRecord->medicalrec_patientid !== $patientid || 
                     strcasecmp($existingRecord->medicalrec_filename, $filename) !== 0)) {
                    return [
                        'status' => 'error',
                        'message' => 'A medical record with this patient ID and filename already exists.'
                    ];
                }
            }
    
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->db->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();

            $sql = "UPDATE medicalrec 
                    SET medicalrec_filename = ?,  medicalrec_comment = ?
                    WHERE medicalrec_id = ? AND medicalrec_patientid = ?";
            $stmt = $this->db->prepare($sql);
    
            if ($stmt->execute([$filename, $comment, $medicalrec_id, $patientid ])) {
                return [
                    'status' => 'success',
                    'message' => 'Medical record updated successfully.',
                    'medicalrec_id' => $medicalrec_id
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Failed to update medical record.'
                ];
            }
    
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    

    public function deleteMedicalRecord($admin_id, $medicalrec_id) {
        try {
            $this->db->beginTransaction(); 
    
            $sql = "DELETE FROM medicalrec WHERE medicalrec_id = ?";
            $stmt = $this->db->prepare($sql);
    
            if ($stmt->execute([$medicalrec_id])) {
                $patientQuery = "SELECT CONCAT(patient_fname, ' ', patient_lname, ' ', patient_mname) AS full_name 
                                 FROM patients 
                                 WHERE patient_id = (
                                     SELECT medicalrec_patientid FROM medicalrec WHERE medicalrec_id = ?
                                 )";
                $patientStmt = $this->db->prepare($patientQuery);
                $patientStmt->execute([$medicalrec_id]);
                $fullName = $patientStmt->fetchColumn();
    
                $logQuery = "INSERT INTO systemlog (syslog_userid, syslog_date, syslog_time, syslog_action) 
                             VALUES (?, CURDATE(), CURTIME(), ?)";
                $logStmt = $this->db->prepare($logQuery);
                $logAction = "Deleted Medical Record for Patient: " . $fullName;
                $logStmt->execute([$admin_id, $logAction]);
    
                $this->db->commit();
                return ['success' => true, 'message' => 'Medical record deleted and log entry created successfully.'];
            } else {
                $this->db->rollBack();
                return ['success' => false, 'message' => 'Failed to delete the medical record.'];
            }
        } catch (PDOException $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    
    
    public function getMedicalRecords($patientid) {
        $records = [];
        $current = $this->medicalrecs->head;

        while ($current !== null) {
            if (strcasecmp($current->item->medicalrec_patientid, $patientid) === 0) {
                $records[] = $current->item; 
            }
            $current = $current->next; 
        }

        return $records; 
    }

    public function getFilePathByMedicalRecId($medicalrecId) {
        $filePath = null; 
        $current = $this->medicalrecs->head; 
    
        while ($current !== null) {
            if (strcasecmp($current->item->medrec_id, $medicalrecId) === 0) {
                $filePath = $current->item->medicalrec_file; 
                break; 
            }
            $current = $current->next; 
        }
    
        return $filePath;
    }
    

    public function isDuplicateFilename($patientid, $filename) {
        return $this->medicalrecs->isDuplicateFilename($patientid, $filename);
    }

}
?>
