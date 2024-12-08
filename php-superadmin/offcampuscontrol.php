<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

$db = new Database();
$conn = $db->getConnection();
$consultationManager = new ConsultationManager($conn); 
$medicineManager = new MedicineManager($conn); 
$offcampusManager = new OffCampusManager($conn);

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    if (isset($_POST['addoffcampus'])) {
        $adminId = $_POST['admin_id'];
        $date = date('Y-m-d');
        $medstock_id = $_POST['selected_medicine_id'] ?? null;
        $treatment_medqty = isset($_POST['presmedqty']) ? (int)$_POST['presmedqty'] : null;

        if ($medstock_id && $treatment_medqty) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: offcampusadd.php');
                exit();
            }

            $offcampusresult = $offcampusManager->insertOffCampusRecord($adminId, $medstock_id, $treatment_medqty, $date);

            if ($offcampusresult['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = $offcampusresult['message'];
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $offcampusresult['message'];
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID or quantity.";
        }

        header('Location: offcampusadd.php');
        exit();
    }

    if (isset($_POST['updateoffcampus'])) {
        $adminId = $_POST['admin_id'];
        $date = $_POST['editdate'];
        $medstock_id = $_POST['editmedstockid'] ?? null;
        $treatment_medqty = isset($_POST['editmedqty']) ? (int)$_POST['editmedqty'] : null;
        $offcampus_id = $_POST['editid'] ?? null; 

        if ($medstock_id && $treatment_medqty && $offcampus_id) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: offcampusadd.php');
                exit();
            }

            $updateResult = $offcampusManager->updateOffCampusRecord($adminId, $offcampus_id, $medstock_id, $treatment_medqty, $date);
            if ($updateResult['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'Off-campus record updated successfully.';
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $updateResult['message'];
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID, quantity, or record ID.";
        }

        header('Location: offcampusadd.php');
        exit();
    }

    if (isset($_POST['deleteoffcampus'])) {
        header('Content-Type: application/json'); 
        $adminId = $_POST['admin_id'];

        $offcampus_id = $_POST['offcampus_id'] ?? null;
    
        if ($offcampus_id) {
            $deleteResult = $offcampusManager->deleteOffCampusRecord($adminId, $offcampus_id);
            if ($deleteResult['status'] === 'success') {
                echo json_encode(['status' => 'success', 'message' => 'Record deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $deleteResult['message']]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing Record ID']);
        }
        exit();
    }
    
} else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: offcampusadd.php');
    exit();
}
?>
