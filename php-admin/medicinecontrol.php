<?php
// Start a session to manage user status messages
session_start();

// Include database configuration and MedicineManager class
include('../database/config.php');
include('../php/medicine.php');

// Initialize the database connection and MedicineManager instance
$db = new Database();
$conn = $db->getConnection();
$medicine = new MedicineManager($conn);

// Handle incoming POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Handle the addition of new medicine stock
    if (isset($_POST['addMedicine'])) {
        // Extract form data for adding medicine stock
        $medicine_id = $_POST['addname'];
        $medicine_unit = $_POST['addunit'];
        $medicine_qty = $_POST['addquantity'];
        $medicine_dosage = $_POST['addDS'];
        $medicine_dateadded = date('Y-m-d');
        $medicine_timeadded = date('h:i:s');
        date_default_timezone_set('Asia/Manila');
        $medicine_timeadded = date('H:i:s'); // Set to Asia/Manila timezone
        $medicine_expirationdt = $_POST['addED'];
        $medicine_disable = "0"; // Disable flag set to "0" by default
        $admin_id = $_POST['admin_id'];

        // Insert medicine stock into the database
        if ($medicine->insertMedstock($admin_id, $medicine_id, $medicine_unit, $medicine_qty, $medicine_dosage, $medicine_dateadded, $medicine_timeadded, $medicine_expirationdt, $medicine_disable)) {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = "Medicine added successfully";
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = "Failed to add medicine";
        }
        header('Location: medicinetable.php'); // Redirect to the medicine table
        exit();
    }

    // Handle updates to existing medicine stock
    if (isset($_POST['updatemedicine'])) {
        // Extract form data for updating medicine stock
        $medstock_id = $_POST['editid'];
        $medicine_name = $_POST['editname'];
        $medicine_qty = $_POST['editquantity'];
        $medicine_unit = $_POST['editunit'];
        $medicine_dosage = $_POST['editDS'];
        $medicine_expirationdt = $_POST['editED'];
        $medicine_disable = $_POST['editDisable'];
        $admin_id = $_POST['admin_id'];

        // Update medicine stock in the database
        $result = $medicine->updateMedstock($admin_id, $medstock_id, $medicine_name, $medicine_unit, $medicine_qty, $medicine_dosage, $medicine_expirationdt, $medicine_disable);

        // Set session status and message based on the result
        if ($result['status'] === 'success') {
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = $result['message'];
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $result['message'];
        }
        header('Location: medicinetable.php'); // Redirect to the medicine table
        exit();
    }

    // Handle adding or updating medicines in the main medicine table
    if (isset($_POST['addmed'])) {
        // Extract form data for adding or updating a medicine
        $medicine_id = $_POST['medicineId'];
        $medicine_name = $_POST['medicineName'];
        $medicine_category = $_POST['medicineCategory'];
        $admin_id = $_POST['admin_id'];

        // Check if it's a new medicine addition or an update
        if (empty($medicine_id)) {
            // Add a new medicine if it doesn't exist
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
            // Update an existing medicine
            $existingMedicine = $medicine->medicines->find($medicine_id);
            if ($existingMedicine) {
                // Check if the medicine name is being updated
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
                    // Update without changing the medicine name
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
        header('Location: medicinetable.php'); // Redirect to the medicine table
        exit();
    }

    // Handle deletion of a medicine
    if (isset($_POST['medicine_id'])) {
        $medicine_id = $_POST['medicine_id'];

        // Delete the medicine from the database
        if ($medicine->deleteMedicine($medicine_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete medicine']);
        }
        exit();
    }
}
?>
