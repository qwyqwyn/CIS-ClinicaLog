<?php
session_start();
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../database/config.php');
include('../php/medicalrecords.php');

$db = new Database();
$conn = $db->getConnection();
$medicalrecords = new MedRecManager($conn);

$patienttype = $_POST['patienttype']; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addmedicalrecs'])) {
        $patientid = $_POST['patientid'];
        $comment = 'No Comment';
        $dateadded = date('Y-m-d');
        $timeadded = date('H:i:s');
        $files = $_FILES['uploadfile'];   
        $patienttype = $_POST['patienttype']; 
    
        $filenames = [];
        $hashedFiles = []; 
        $duplicateFilenames = [];
    
        // Handle file uploads 
        for ($i = 0; $i < count($files['name']); $i++) {
            $originalName = $files['name'][$i];
            $tmpFilePath = $files['tmp_name'][$i];
    
            $hashedName = md5(uniqid($originalName, true));
    
            // Check for duplicate filenames
            if ($medicalrecords->getDuplicateFilenames($patientid, $originalName)) {
                $duplicateFilenames[] = $originalName;
            } else {
                $filenames[] = $originalName;
                $hashedFiles[] = $hashedName;
            } 
        }
    
        // If there are duplicates, show an error and redirect
        if (!empty($duplicateFilenames)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Duplicate files found: ' . implode(', ', $duplicateFilenames);
    
            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patstudents.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patfaculty.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patstaff.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patextension.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php'; // Default URL
                    break;
            }
    
            header("Location: $redirectUrl");
            exit();
        }
    
        // Insert medical record
        $response = $medicalrecords->insertMedicalRecordbyPatient($patientid, $filenames, $hashedFiles, $comment, $dateadded, $timeadded);
    
        if ($response['status'] === 'success') {
            // Upload files
            for ($i = 0; $i < count($hashedFiles); $i++) {
                $originalTmpPath = $files['tmp_name'][$i];
                $hashedName = $hashedFiles[$i];
    
                // Use the correct absolute path for file upload
                $destination = $_SERVER['DOCUMENT_ROOT'] . '/php-admin/uploadmedrecs/' . $hashedName;
    
                // Move the uploaded file to the destination
                if (move_uploaded_file($originalTmpPath, $destination)) {
                    // File uploaded successfully
                } else {
                    // File upload failed
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to upload file: ' . $filenames[$i];
    
                    // Redirect based on patient type
                    switch ($patienttype) {
                        case 'Student':
                            $redirectUrl = 'patstudents.php';
                            break;
                        case 'Faculty': 
                            $redirectUrl = 'patfaculty.php';
                            break;
                        case 'Staff':
                            $redirectUrl = 'patstaff.php';
                            break;
                        case 'Extension':
                            $redirectUrl = 'patextension.php';
                            break;
                        default:
                            $redirectUrl = 'patient-record.php'; // Default URL
                            break;
                    }
    
                    header("Location: $redirectUrl");
                    exit();
                }
            }
    
            // Success message
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Medical record inserted and files uploaded successfully.';
    
            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patstudents.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patfaculty.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patstaff.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patextension.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php'; // Default URL
                    break;
            }
    
            header("Location: $redirectUrl");
            exit();
        } else {
            // Error during insert
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $response['message'];
    
            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patstudents.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patfaculty.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patstaff.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patextension.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php'; // Default URL
                    break;
            }
    
            header("Location: $redirectUrl");
            exit();
        }
    }
    
    
    // Helper function to determine redirect URL based on patient type
    function getRedirectUrl($patienttype) {
        switch ($patienttype) {
            case 'Student':
                return 'patstudents.php';
            case 'Faculty':
                return 'patfaculty.php';
            case 'Staff':
                return 'patstaff.php';
            case 'Extension':
                return 'patextension.php';
            default:
                return 'patient-record.php';
        }
    }
    
    if (isset($_POST['editmedrecs'])) {
        $patientid = $_POST['patientid'];
        $id = $_POST['editid'];        
        $filename = $_POST['editfilename'];  
        $comment = $_POST['editcomment'];
        $patienttype = $_POST['patienttype']; 

            $response = $medicalrecords->updateMedicalRecordbyPatient($id, $patientid, $filename, $comment);

            if ($response['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = $response['message'];
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $response['message'];
            }

            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patstudents.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patfaculty.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patstaff.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patextension.php';
                    break;
                default:
                    //$redirectUrl = 'patient-record.php';
                    break;
            }
            header("Location: $redirectUrl");
            exit();
        
    }


    
}
else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';

    switch ($patienttype) {
        case 'Student':
            $redirectUrl = 'patstudents.php';
            break;
        case 'Faculty':
            $redirectUrl = 'patfaculty.php';
            break;
        case 'Staff':
            $redirectUrl = 'patstaff.php';
            break;
        case 'Extension':
            $redirectUrl = 'patextension.php';
            break;
        default:
            //$redirectUrl = 'patient-record.php';
            break;
    }
    header("Location: $redirectUrl");
    exit();
}

?>