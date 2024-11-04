<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');
include('../php/patient.php');
@include('../php/patient-studprofile.php');
@include('../php/patient-staffprofile.php');
@include('../php/patient-facultyprofile.php'); 
@include('../php/patient-extensionprofile.php');
include('../php/consultation.php');

$db = new Database();
$conn = $db->getConnection();
$consultationManager = new ConsultationManager($conn); 
$medicineManager = new MedicineManager($conn); 
$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addcon'])) {
        $patient_idnum = $_POST['selected_patient_id'] ?? null;
        $diagnosis = htmlspecialchars($_POST['Diagnosis'], ENT_QUOTES);
        $treatment_notes = htmlspecialchars($_POST['presmednotes'], ENT_QUOTES);
        $remark = htmlspecialchars($_POST['Remarks'], ENT_QUOTES);
        $consult_date = date('Y-m-d');
    
        if (!$patient_idnum || !$diagnosis) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing required fields.";
            header('Location: addconsultation.php');
            exit();
        }
    
        $medstock_id = $_POST['selected_medicine_id'] ?? null;
        $treatment_medqty = isset($_POST['presmedqty']) ? (int)$_POST['presmedqty'] : null;
    
        if ($medstock_id && $treatment_medqty) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
    
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: addconsultation.php');
                exit();
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID or quantity.";
            header('Location: addconsultation.php');
            exit();
        }
    
        $consultationResult = $consultationManager->insertConsultation($patient_idnum, $diagnosis, $treatment_notes, $remark, $consult_date);
    
        if ($consultationResult['status'] === 'success') {
            $consult_id = $consultationResult['consult_id'];
    
            if ($consultationManager->insertPrescribemed($consult_id, $medstock_id, $treatment_medqty)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = "Consultation and prescribed medicine added successfully.";
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Consultation added, but failed to add prescribed medicine.";
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $consultationResult['message'];
        }
    
        header('Location: addconsultation.php');
        exit();
    }
    
    if (isset($_POST['editcon'])) {
        $consult_id = $_POST['edit_consult_id'] ?? null;
        $medstock_id = $_POST['edit_medicine_id'] ?? null;
        $edited_medqty = isset($_POST['edit_quantity']) ? (int)$_POST['edit_quantity'] : null;
        $patient_idnum = $_POST['edit_patient_id'] ?? null;
        $diagnosis = htmlspecialchars($_POST['edit_diagnosis'], ENT_QUOTES);
        $treatment_notes = htmlspecialchars($_POST['edit_notes'], ENT_QUOTES);
        $remark = htmlspecialchars($_POST['edit_remarks'], ENT_QUOTES);
        $consult_date = $_POST['edit_date'] ?? date('Y-m-d');
    
        error_log("Consult ID: $consult_id, Medicine ID: $medstock_id, Edited Quantity: $edited_medqty");
    
        if (!$consult_id || !$medstock_id || !$edited_medqty || !$patient_idnum || !$diagnosis) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing required fields.";
            header('Location: addconsultation.php');
            exit();
        }

        $medstock_id = $_POST['edit_medicine_id'] ?? null;
        $treatment_medqty = isset($_POST['edit_quantity']) ? (int)$_POST['edit_quantity'] : null;
    
        if ($medstock_id && $treatment_medqty) {
            $availableQty = $consultationManager->getAvailableQuantity($medstock_id);
    
            if ($treatment_medqty > $availableQty) {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Insufficient stock: only $availableQty available.";
                header('Location: addconsultation.php');
                exit();
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Missing medicine ID or quantity.";
            header('Location: addconsultation.php');
            exit();
        }

        $consultationManager = new ConsultationManager($db);
    
        // Validate the patient ID
        $stmt = $db->prepare("SELECT COUNT(*) FROM patients WHERE patient_id = :patient_id");
        $stmt->execute([':patient_id' => $patient_idnum]);
        if ($stmt->fetchColumn() == 0) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid Patient ID. Please select a valid patient.';
            header('Location: addconsultation.php');
            exit();
        }
    
        $pm_id = $consultationManager->getPmIdByConsultId($consult_id);
        if (!$pm_id) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Error fetching prescribed medicine ID.";
            header('Location: addconsultation.php');
            exit();
        }
    
        $originalMedData = $consultationManager->getMedDataByPmId($pm_id);
        if ($originalMedData === false) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Error fetching original medicine data.";
            header('Location: addconsultation.php');
            exit();
        }
        $originalMedStockId = $originalMedData['pm_medstockid'];
        $originalMedQty = $originalMedData['pm_medqty'];
    
        try {
            // Update consultation details
            $stmt = $db->prepare("UPDATE consultations SET 
                consult_patientid = :consult_patientid,
                consult_diagnosis = :consult_diagnosis,
                consult_treatmentnotes = :consult_treatmentnotes,
                consult_remark = :consult_remark,
                consult_date = :consult_date
                WHERE consult_id = :consult_id");
        
            $stmt->execute([
                ':consult_patientid' => $patient_idnum,
                ':consult_diagnosis' => $diagnosis,
                ':consult_treatmentnotes' => $treatment_notes,
                ':consult_remark' => $remark,
                ':consult_date' => $consult_date,
                ':consult_id' => $consult_id
            ]);
        
            // Check if either the medicine ID or quantity has changed
            if ($medstock_id != $originalMedStockId || $edited_medqty != $originalMedQty) {
                // Update the prescribed medicine if there are changes in medstock_id or quantity
                $updateResult = $consultationManager->updatePrescribemd($pm_id, $consult_id, $medstock_id, $edited_medqty);
                
                // Check the result of the updatePrescribemd function
                if ($updateResult['status'] === 'success') {
                        $_SESSION['status'] = 'success';
                        $_SESSION['message'] = "Consultation and prescribed medicine updated successfully.";
                } else {
                    // Log and handle the medicine update failure
                    error_log("Failed to update prescribed medicine: " . $updateResult['message']);
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Error updating prescribed medicine: ' . $updateResult['message'];
                }
            } else {
                // No change in medicine ID or quantity, so just update the consultation data
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = "Consultation updated successfully. No changes made to prescribed medicine.";
            }
        } catch (PDOException $e) {
            error_log("PDO Error updating consultation: " . $e->getMessage() . 
                      "\nConsult ID: $consult_id, Medicine ID: $medstock_id, Edited Quantity: $edited_medqty, 
                      Diagnosis: $diagnosis, Treatment Notes: $treatment_notes, Remark: $remark");
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'An error occurred while updating the consultation: ' . $e->getMessage();
        }
        
        header('Location: addconsultation.php');
        exit();
    }
    
    if (isset($_POST['delete'])) {
        
        // Handle deletion of a consultation
        $consult_id = $_POST['edit_consult_id'] ?? null;

        if ($consult_id) {
            $deleteResult = $consultationManager->deleteConsultation($consult_id);

            // Send JSON response
            echo json_encode([
                'status' => $deleteResult['status'],
                'message' => $deleteResult['message']
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Consultation ID is missing.'
            ]);
        }
        header('Location: addconsultation.php');
        exit();
    }
    if (isset($_POST['medstock_id'], $_POST['requested_qty'])) {
        $medstock_id = $_POST['medstock_id']; // Ensure this matches JavaScript
        $requested_qty = (int) $_POST['requested_qty'];
    
        // Prepare SQL query
        $stmt = $conn->prepare("SELECT m.medstock_qty - (IFNULL(SUM(pm.pm_medqty), 0) + IFNULL(SUM(mi.mi_medqty), 0)) AS available_stock
                                FROM medstock m 
                                LEFT JOIN prescribemed pm ON pm.pm_medstockid = m.medstock_id 
                                LEFT JOIN medissued mi ON mi.mi_medstockid = m.medstock_id
                                WHERE m.medstock_id = ? 
                                GROUP BY m.medstock_id");
        $stmt->execute([$medstock_id]);
        $current_qty = $stmt->fetchColumn(); // Fetch single value directly
    
        // Check the quantity
        if ($current_qty === false) {
            echo json_encode(["status" => "error", "message" => "Medicine not found"]);
        } elseif ($requested_qty > $current_qty) {
            echo json_encode(["status" => "error", "message" => "Only $current_qty available in stock"]);
        } else {
            echo json_encode(["status" => "success"]);
        }
        exit;
    }
    
    

} else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';
    header('Location: addconsultation.php');
    exit();
}







?>