<?php
class Transaction {
    public $transac_id;
    public $transac_patientid;
    public $transac_patientprofile;
    public $transac_patientname;
    public $transac_patienttype;
    public $transac_purpose;
    public $transac_date;
    public $transac_in;
    public $transac_out;
    public $transac_spent;
    public $transac_status;
    public $transac_patientidnum;

    public function __construct($transac_id, $transac_patientid, $transac_patientprofile, $transac_patientname, $transac_patienttype, $transac_purpose, $transac_date, $transac_in, $transac_out, $transac_spent, $transac_status, $transac_patientidnum) {
        $this->transac_id = $transac_id;
        $this->transac_patientid = $transac_patientid;
        $this->transac_patientprofile = $transac_patientprofile;
        $this->transac_patientname = $transac_patientname;
        $this->transac_patienttype = $transac_patienttype;
        $this->transac_purpose = $transac_purpose;
        $this->transac_date = $transac_date;
        $this->transac_in = $transac_in;
        $this->transac_out = $transac_out;
        $this->transac_spent = $transac_spent;
        $this->transac_status = $transac_status;
        $this->transac_patientidnum = $transac_patientidnum;

    }
}

class LinkedlistNode {
    public $transaction;
    public $next;

    public function __construct($transaction) {
        $this->transaction = $transaction;
        $this->next = null;
    }
}

class TransacLinked {
    private $head;

    public function __construct() {
        $this->head = null;
    }

    public function addTransaction($transaction) {
        $newNode = new LinkedlistNode($transaction);
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

    public function getAllTransaction() {
        $dataArray = [];
        $current = $this->head;
        while ($current !== null) {
            $dataArray[] = $current->transaction;
            $current = $current->next;
        }
        return $dataArray;
    }
    
    public function removeTransactionById($transac_id) {
        if ($this->head === null) {
            return;
        }
        if ($this->head->transaction->transac_id === $transac_id) {
            $this->head = $this->head->next;
            return;
        }
        $current = $this->head;
        while ($current->next !== null && $current->next->transaction->transac_id !== $transac_id) {
            $current = $current->next;
        }
        if ($current->next !== null) {
            $current->next = $current->next->next;
        }
    }
}

class TransacManager{
    private $db;
    private $transacList;

    public function __construct($db) {
        $this->db = $db;
        $this->transacList = new TransacLinked();
        $this->loadTransactions();
    }

    public function loadTransactions() {
        $query = "
SELECT 
    t.transac_id, 
    t.transac_patientid, 
    CONCAT(p.patient_fname, ' ', p.patient_lname) AS transac_patientname,
    p.patient_profile, 
    p.patient_patienttype, 
    t.transac_purpose, 
    t.transac_date, 
    t.transac_in, 
    t.transac_out, 
    t.transac_spent, 
    t.transac_status,
    CASE
        WHEN p.patient_patienttype = 'Student' THEN s.student_idnum
        WHEN p.patient_patienttype = 'Faculty' THEN f.faculty_idnum
        WHEN p.patient_patienttype = 'Staff' THEN st.staff_idnum
        WHEN p.patient_patienttype = 'Extension' THEN e.exten_idnum
        ELSE NULL
    END AS transac_patientidnum
FROM 
    transactions AS t
INNER JOIN 
    patients AS p ON t.transac_patientid = p.patient_id
LEFT JOIN 
    patstudents AS s ON p.patient_id = s.student_patientid
LEFT JOIN 
    patfaculties AS f ON p.patient_id = f.faculty_patientid
LEFT JOIN 
    patstaffs AS st ON p.patient_id = st.staff_patientid
LEFT JOIN 
    patextensions AS e ON p.patient_id = e.exten_patientid
ORDER BY 
    FIELD(t.transac_status, 'Pending', 'Progress', 'Done') ASC,  -- Ensure Pending is first
    t.transac_date ASC;  -- Sort by transaction date within each status



        ";
        
        $stmt = $this->db->prepare($query);
        
        if ($stmt->execute()) {
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            if ($results) {
                foreach ($results as $row) {
                    $transaction = new Transaction(
                        $row['transac_id'],
                        $row['transac_patientid'],
                        $row['patient_profile'],
                        $row['transac_patientname'], 
                        $row['patient_patienttype'], 
                        $row['transac_purpose'],
                        $row['transac_date'],
                        $row['transac_in'],
                        $row['transac_out'],
                        $row['transac_spent'],
                        $row['transac_status'],
                        $row['transac_patientidnum']
                    );
                    $this->transacList->addTransaction($transaction);
                }
            } else {
            }
        } else {
            echo "Failed to load transactions. Error: " . $stmt->errorInfo()[2];
        }
    }
    
    

    public function addTransaction($transac_patientid, $transac_purpose) {
        $transac_in = '00:00:00';
        $transac_out = '00:00:00';
        $transac_spent = 0;
        $transac_date = date('Y-m-d'); 
        $transac_status = 'Pending';
    
        $query = "
            INSERT INTO transactions (transac_patientid, transac_purpose, transac_date, transac_in, transac_out, transac_spent, transac_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?);
        ";
    
        $stmt = $this->db->prepare($query);
    
        $stmt->bindValue(1, $transac_patientid);
        $stmt->bindValue(2, $transac_purpose);
        $stmt->bindValue(3, $transac_date);
        $stmt->bindValue(4, $transac_in);
        $stmt->bindValue(5, $transac_out);
        $stmt->bindValue(6, $transac_spent);
        $stmt->bindValue(7, $transac_status);
    
        if ($stmt->execute()) {
            return [
                'status' => 'success',
                'message' => 'Transaction successfully added.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to insert record.'
            ];
        }
    }

    public function getAllTransac() {
        $transac = $this->transacList->getAllTransaction();
    
        return $transac;
    }
    
   // Method to update the status to "Pending"
public function updateStatusToPending($transac_id) {
    return $this->updateTransactionStatusToPending($transac_id);
}

// Method to update the status to "In Progress"
public function updateStatusToInProgress($transac_id) {
    return $this->updateTransactionStatusToInProgress($transac_id);
}

// Method to update the status to "Done"
public function updateStatusToDone($transac_id) {
    return $this->updateTransactionStatusToDone($transac_id);
}

// Helper method to update the transaction status to "Pending"
private function updateTransactionStatusToPending($transac_id) {
    $query = "UPDATE transactions SET transac_status = ?, transac_in = ?, transac_out = ?, transac_spent = ? WHERE transac_id = ?";
    $transac_in = '00:00:00';
    $transac_out = '00:00:00';
    $transac_spent = 0;

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(1, 'Pending');
    $stmt->bindValue(2, $transac_in);
    $stmt->bindValue(3, $transac_out);
    $stmt->bindValue(4, $transac_spent);
    $stmt->bindValue(5, $transac_id);

    if ($stmt->execute()) {
        // Update the status in the linked list
        $this->updateStatusInLinkedList($transac_id, 'Pending', $transac_in, $transac_out, $transac_spent);
        return [
            'status' => 'success',
            'message' => "Transaction status updated to 'Pending'."
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to update the status to Pending.'
        ];
    }
}

// Helper method to update the transaction status to "In Progress"
private function updateTransactionStatusToInProgress($transac_id) {
    $query = "UPDATE transactions SET transac_status = ?, transac_in = ? WHERE transac_id = ?";
    date_default_timezone_set('Asia/Manila');
    $transac_in = date('H:i:s');

    $stmt = $this->db->prepare($query);
    $stmt->bindValue(1, 'Progress');
    $stmt->bindValue(2, $transac_in);
    $stmt->bindValue(3, $transac_id);

    if ($stmt->execute()) {
        // Update the status in the linked list
        $this->updateStatusInLinkedList($transac_id, 'Progress', $transac_in, null, null);
        return [
            'status' => 'success',
            'message' => "Transaction status updated to 'In Progress'."
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to update the status to In Progress.'
        ];
    }
}

// Helper method to update the transaction status to "Done"
private function updateTransactionStatusToDone($transac_id) {
    $query = "UPDATE transactions SET transac_status = ?, transac_out = ?, transac_spent = ? WHERE transac_id = ?";
    date_default_timezone_set('Asia/Manila');
    $transac_out = date('H:i:s');

    // Fetch transac_in from the database to calculate transac_spent
    $existingTransac = $this->getTransactionById($transac_id);
    if ($existingTransac && isset($existingTransac['transac_in'])) {
        $transac_spent = $this->calculateDuration($existingTransac['transac_in'], $transac_out);

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(1, 'Done');
        $stmt->bindValue(2, $transac_out);
        $stmt->bindValue(3, $transac_spent);
        $stmt->bindValue(4, $transac_id);

        if ($stmt->execute()) {
            // Update the status in the linked list
            $this->updateStatusInLinkedList($transac_id, 'Done', null, $transac_out, $transac_spent);
            return [
                'status' => 'success',
                'message' => "Transaction status updated to 'Done'."
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Failed to update the status to Done.'
            ];
        }
    } else {
        return [
            'status' => 'error',
            'message' => 'Failed to fetch transaction details for calculating duration.'
        ];
    }
}

// Helper method to fetch a transaction by ID
private function getTransactionById($transac_id) {
    // Assuming a method that retrieves the transaction by its ID
    $query = "SELECT transac_in FROM transactions WHERE transac_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(1, $transac_id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Helper method to calculate the duration (transac_spent) in seconds
private function calculateDuration($transac_in, $transac_out) {
    // Assuming transac_in and transac_out are in 'H:i:s' format, we will calculate the difference
    $inTime = strtotime($transac_in);
    $outTime = strtotime($transac_out);
    
    // Calculate the difference in seconds
    $duration = $outTime - $inTime;
    
    // Return the duration in seconds
    return $duration;
}

private function updateStatusInLinkedList($transac_id, $new_status, $transac_in, $transac_out, $transac_spent) {
    $transactions = $this->transacList->getAllTransaction();
    
    foreach ($transactions as $transaction) {
        if ($transaction->transac_id === $transac_id) {
            $transaction->transac_status = $new_status;
            if ($new_status === 'Progress') {
                $transaction->transac_in = $transac_in;
            } elseif ($new_status === 'Done') {
                $transaction->transac_out = $transac_out;
                $transaction->transac_spent = $transac_spent;
            } elseif ($new_status === 'Pending') {
                $transaction->transac_in = $transac_in;
                $transaction->transac_out = $transac_out;
                $transaction->transac_spent = $transac_spent;
            }
            break;
        }
    }
}

public function updatePatientAndPurpose($transac_id, $new_patientid, $new_purpose) {
    $query = "UPDATE transactions SET transac_patientid = ?, transac_purpose = ? WHERE transac_id = ?";
    $stmt = $this->db->prepare($query);
    $stmt->bindValue(1, $new_patientid);
    $stmt->bindValue(2, $new_purpose);
    $stmt->bindValue(3, $transac_id);

    // Start a database transaction
    $this->db->beginTransaction();
    try {
        if ($stmt->execute()) {
            // Update in linked list if needed
            if ($this->updatePatientAndPurposeInLinkedList($transac_id, $new_patientid, $new_purpose)) {
                $this->db->commit();
                return [
                    'status' => 'success',
                    'message' => 'Patient ID and purpose successfully updated.'
                ];
            } else {
                throw new Exception('Failed to update linked list.');
            }
        } else {
            throw new Exception('Database update failed.');
        }
    } catch (Exception $e) {
        $this->db->rollBack();
        return [
            'status' => 'error',
            'message' => $e->getMessage()
        ];
    }
}

private function updatePatientAndPurposeInLinkedList($transac_id, $new_patientid, $new_purpose) {
    $transactions = $this->transacList->getAllTransaction();
    
    if (is_array($transactions)) {
        foreach ($transactions as $transac) {
            if ($transac->transac_id === $transac_id) {
                $transac->transac_patientid = $new_patientid;
                $transac->transac_purpose = $new_purpose;
                return true;  
            }
        }
    } else {
        $current = $transactions;
        while ($current !== null) {
            if ($current->transac_id === $transac_id) {
                $current->transaction->transac_patientid = $new_patientid;
                $current->transaction->transac_purpose = $new_purpose;
                return true; 
            }
            $current = $current->next;
        }
    }
    
    return false;  
}


    
    
    


}

?>