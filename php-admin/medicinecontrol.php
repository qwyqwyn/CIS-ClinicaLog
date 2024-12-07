<?php
session_start();

include('../database/config.php');
include('../php/medicine.php');



$db = new Database();
$conn = $db->getConnection();
$medicine = new MedicineManager($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    if (isset($_POST['addMedicine'])) {
       
        $medicine_id = $_POST['addname'];
        $medicine_unit = $_POST['addunit'];
        $medicine_qty = $_POST['addquantity'];
        $medicine_dosage = $_POST['addDS'];
        $medicine_dateadded = date('Y-m-d');
        $medicine_timeadded = date('h:i:s');
        date_default_timezone_set('Asia/Manila');
        $medicine_timeadded = date('H:i:s');
        $medicine_expirationdt = $_POST['addED'];
        $medicine_disable = "0";
        $admin_id = $_POST['admin_id'];
       
        if ($medicine->insertMedstock($admin_id, $medicine_id, $medicine_unit, $medicine_qty, $medicine_dosage, $medicine_dateadded, $medicine_timeadded,$medicine_expirationdt, $medicine_disable )) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = "Medicine added successfully";
        } else { 
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Failed to add medicine";
        } 
        header('Location: medicinetable.php'); 
        exit();
    } 

    if (isset($_POST['updatemedicine'])) { 
     
        $medstock_id = $_POST['editid'];
        $medicine_name = $_POST['editname'];
        $medicine_qty = $_POST['editquantity'];
        $medicine_unit = $_POST['editunit'];  
        $medicine_dosage = $_POST['editDS']; 
        $medicine_expirationdt = $_POST['editED']; 
        $medicine_disable = $_POST['editDisable']; 
        $admin_id = $_POST['admin_id'];

    
        $result = $medicine->updateMedstock($admin_id,  $medstock_id, $medicine_name, $medicine_unit, $medicine_qty, $medicine_dosage, $medicine_expirationdt, $medicine_disable);
    
        if ($result['status'] === 'success') {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = $result['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $result['message'];
        }
    
        header('Location: medicinetable.php');
        exit();
    }
    

if (isset($_POST['addmed'])) {
   
    $medicine_id = $_POST['medicineId'];
    $medicine_name = $_POST['medicineName'];
    $medicine_category = $_POST['medicineCategory'];
    $admin_id = $_POST['admin_id'];


    if (empty($medicine_id)) {
       
        if ($medicine->medicines->medicineExists($medicine_name)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Medicine with this name already exists.";
        } else {
            if ($medicine->insertMedicine($admin_id, $medicine_name, $medicine_category)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = "Medicine added successfully";
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = "Failed to add medicine";
            }
        }
    } else {
        
        $existingMedicine = $medicine->medicines->find($medicine_id);
        if ($existingMedicine) {
           
            if ($existingMedicine->medicine_name !== $medicine_name) {
               
                if ($medicine->medicines->medicineExists($medicine_name)) {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = "Medicine with this name already exists.";
                } else {
                    if ($medicine->updateMedicine($admin_id, $medicine_id, $medicine_name, $medicine_category)) {
                        $_SESSION['status'] = 'success';
                        $_SESSION['message'] = "Medicine updated successfully";
                    } else {
                        $_SESSION['status'] = 'error';
                        $_SESSION['message'] = "Failed to update medicine";
                    }
                }
            } else {
                
                if ($medicine->updateMedicine($admin_id, $medicine_id, $medicine_name, $medicine_category)) {
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = "Medicine updated successfully";
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = "Failed to update medicine";
                }
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Medicine not found.";
        }
    }

    header('Location: medicinetable.php');
    exit();
}

    if (isset($_POST['medicine_id'])) {
        $medicine_id = $_POST['medicine_id']; 

        if ($medicine->deleteMedicine($medicine_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete medicine']);
        }
        exit(); 
    }
}
?>


