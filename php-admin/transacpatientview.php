<?php
session_start(); 

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json'); 

include('../database/config.php');
include('../php/medicine.php');

$db = new Database();
$conn = $db->getConnection();



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['patient_id']) && isset($_POST['patient_type'])) {
        $_SESSION['id'] = $_POST['patient_id']; 
        $_SESSION['type'] = $_POST['patient_type'];

        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Patient data not found']);
    }

    if (isset($_POST['notif_id'], $_POST['patient_id'], $_POST['patient_type'])) {
        // Store patient data in the session
        $_SESSION['id'] = $_POST['patient_id'];
        $_SESSION['type'] = $_POST['patient_type'];
        $notif_id = $_POST['notif_id'];
    
        // Log incoming POST data for debugging
        error_log("Received POST data: " . print_r($_POST, true));
    
        try {
            $stmt = $conn->prepare("UPDATE adminnotifs SET notif_status = 'read' WHERE notif_id = :notif_id");
            $stmt->bindParam(':notif_id', $notif_id);
    
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update notification status']);
            }
        } catch (PDOException $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        // Return error if required parameters are missing
        echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    }
}
?>
 