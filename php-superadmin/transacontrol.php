<?php
session_start();
header('Content-Type: application/json'); 
include('../database/config.php');
include('../php/transaction.php');

$db = new Database();
$conn = $db->getConnection();
$transac = new TransacManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    if (isset($_POST['addtransac'])) {
        $patientId = $_POST['selected_patient_id'];
        $purpose = $_POST['transac_purpose'];
        $adminId = $_POST['admin_id'];

        $transaction = $transac->addTransaction($adminId, $patientId, $purpose);

        if ($transaction['status'] == 'success') {    
            $_SESSION['message'] = $transaction['message'];
            $_SESSION['status'] = $transaction['status'];
        
        } else {
           
            $_SESSION['message'] = $transaction['message'];
            $_SESSION['status'] = 'error'; 
        }
        header('Location: transactions.php'); 
        exit();
        
    }

    if (isset($_POST['edittransac'])) {
        $transacid = $_POST['transac_id'];
        $patient_id = $_POST['edit_patient_id'];
        $purpose = $_POST['edit_purpose']; 
        $adminId = $_POST['admin_id'];

        $transaction = $transac->updatePatientAndPurpose($adminId, $transacid, $patient_id, $purpose);

        if ($transaction['status'] == 'success') {    
            $_SESSION['message'] = $transaction['message'];
            $_SESSION['status'] = $transaction['status'];
        
        } else {
           
            $_SESSION['message'] = $transaction['message'];
            $_SESSION['status'] = 'error'; 
        }
        header('Location: transactions.php'); 
        exit();
        
    }

    if (isset($_POST['transac_id']) && isset($_POST['status']) && isset($_POST['admin_id'])) {
        $adminId = $_POST['admin_id'];
        $transac_id = $_POST['transac_id'];
        $status = $_POST['status'];
    
        $response = [];
    
        switch ($status) {
            case 'Pending':
                $response = $transac->updateStatusToPending($adminId, $transac_id);
                break;
            case 'Progress':
                $response = $transac->updateStatusToInProgress($adminId, $transac_id);
                break;
            case 'Done':
                $response = $transac->updateStatusToDone($adminId, $transac_id);
                break;
            default:
                echo json_encode(['status' => 'error', 'message' => 'Invalid status']);
                exit;
        }
    
        echo json_encode($response);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
        exit;
    }
    
    

    

}
?>
