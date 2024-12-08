<?php
session_start(); // Start a new session or resume the existing session.
header('Content-Type: application/json'); // Set the response content type to JSON.
error_reporting(E_ALL); // Report all PHP errors for debugging purposes.
ini_set('display_errors', 1); // Display all PHP errors.

// Include the necessary PHP files for database connection and various classes.
include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');
include('../php/patient.php');
include('../php/offcampus.php'); 
@include('../php/patient-studprofile.php');
@include('../php/patient-staffprofile.php');
@include('../php/patient-facultyprofile.php'); 
@include('../php/patient-extensionprofile.php');
include('../php/consultation.php');

// Initialize database connection and class instances.
$db = new Database();
$conn = $db->getConnection();
$consultationManager = new ConsultationManager($conn); 
$medicineManager = new MedicineManager($conn); 
$offcampusManager = new OffCampusManager($conn);

// Decode the JSON payload from the client.
$data = json_decode(file_get_contents('php://input'), true);

// Check if the request method is POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 

    // Handle the addition of an off-campus record.
    if (isset($_POST['addoffcampus'])) {
        $adminId = $_POST['admin_id']; // Admin ID performing the operation.
        $date = date('Y-m-d'); // Current date.
        $medstock_id = $_POST['selected_medicine_id'] ?? null; // Selected medicine ID.
        $treatment_medqty = isset($_POST['presmedqty']) ? (int)$_POST['presmedqty'] : null; // Prescribed quantity.

        // Validate that both medicine ID and quantity are provided.
        if ($medstock_id && $treatment_medqty) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id); // Check available stock.
            
            // Validate if there is enough stock.
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: offcampusadd.php');
                exit();
            }

            // Insert the off-campus record.
            $offcampusresult = $offcampusManager->insertOffCampusRecord($adminId, $medstock_id, $treatment_medqty, $date);

            // Set session messages based on operation result.
            if ($offcampusresult['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = $offcampusresult['message'];
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $offcampusresult['message'];
            }
        } else {
            // Error for missing input.
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID or quantity.";
        }

        header('Location: offcampusadd.php');
        exit();
    }

    // Handle updating an off-campus record.
    if (isset($_POST['updateoffcampus'])) {
        $adminId = $_POST['admin_id']; // Admin ID performing the update.
        $date = $_POST['editdate']; // Edited date.
        $medstock_id = $_POST['editmedstockid'] ?? null; // Medicine ID.
        $treatment_medqty = isset($_POST['editmedqty']) ? (int)$_POST['editmedqty'] : null; // Edited quantity.
        $offcampus_id = $_POST['editid'] ?? null; // Record ID being updated.

        // Validate inputs.
        if ($medstock_id && $treatment_medqty && $offcampus_id) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id); // Check available stock.

            // Check for stock sufficiency.
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: offcampusadd.php');
                exit();
            }

            // Perform the update.
            $updateResult = $offcampusManager->updateOffCampusRecord($adminId, $offcampus_id, $medstock_id, $treatment_medqty, $date);
            if ($updateResult['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Off-campus record updated successfully.';
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $updateResult['message'];
            }
        } else {
            // Error for missing required inputs.
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID, quantity, or record ID.";
        }

        header('Location: offcampusadd.php');
        exit();
    }

    // Handle deleting an off-campus record.
    if (isset($_POST['deleteoffcampus'])) {
        header('Content-Type: application/json'); // Set the response format to JSON.
        $adminId = $_POST['admin_id']; // Admin ID performing the deletion.
        $offcampus_id = $_POST['offcampus_id'] ?? null; // Record ID to delete.
    
        // Validate record ID.
        if ($offcampus_id) {
            $deleteResult = $offcampusManager->deleteOffCampusRecord($adminId, $offcampus_id); // Perform deletion.
            if ($deleteResult['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $deleteResult['message']]);
            }
        } else {
            // Error for missing record ID.
            echo json_encode(['status' => 'error', 'message' => 'Missing Record ID']);
        }
        exit();
    }
    
} else {
    // Handle invalid request method.
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: offcampusadd.php');
    exit();
}
?>
