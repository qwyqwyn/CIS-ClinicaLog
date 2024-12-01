<?php
session_start();


include('../database/config.php');
include('../php/medicalrecords.php');

$db = new Database();
$conn = $db->getConnection();
$medicalrecords = new MedRecManager($conn);

$patienttype = $_POST['patienttype']; 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addmedicalrecs'])) {
        $patientid = $_POST['patientid'];
        $medrecid = $_POST['medrecid'];
        $comment = 'No Comment';
        $dateadded = date('Y-m-d');
        $timeadded = date('H:i:s');
        $files = $_FILES['uploadfile'];   
        $patienttype = $_POST['patienttype']; 
        $admin_id = $_POST['admin_id'];
        $filenames = []; 
        $hashedFiles = []; 
        $duplicateFilenames = [];

        for ($i = 0; $i < count($files['name']); $i++) {
            $originalName = $files['name'][$i];
            $tmpFilePath = $files['tmp_name'][$i];

            $hashedName = md5(uniqid($originalName, true));

            if ($medicalrecords->getDuplicateFilenames($patientid, $originalName)) {
                $duplicateFilenames[] = $originalName;
            } else {
                $filenames[] = $originalName;
                $hashedFiles[] = $hashedName;
            } 
        }

        if (!empty($duplicateFilenames)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Duplicate files found: ' . implode(', ', $duplicateFilenames);

            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
                    break;
            } 
            header("Location: $redirectUrl");
            exit(); 
        }
    
        $response = $medicalrecords->insertMedicalRecord($admin_id, $patientid, $filenames, $hashedFiles, $comment, $dateadded, $timeadded);
    
        if ($response['status'] === 'success') {
            for ($i = 0; $i < count($hashedFiles); $i++) {
                $originalTmpPath = $files['tmp_name'][$i];
                $hashedName = $hashedFiles[$i];
                $destination = 'uploadmedrecs/' . $hashedName;
    
                if (move_uploaded_file($originalTmpPath, $destination)) {

                } else {
                    
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to upload file: ' . $filenames[$i];
                    // Redirect based on patient type
                    switch ($patienttype) {
                        case 'Student':
                            $redirectUrl = 'patient-studprofile.php';
                            break;
                        case 'Faculty':
                            $redirectUrl = 'patient-facultyprofile.php';
                            break;
                        case 'Staff':
                            $redirectUrl = 'patient-staffprofile.php';
                            break;
                        case 'Extension':
                            $redirectUrl = 'patient-extensionprofile.php';
                            break;
                        default:
                            $redirectUrl = 'patient-record.php';
                            break;
                    }
                    header("Location: $redirectUrl");
                    exit();
                        }
            }
            
            $_SESSION['status'] = 'success';
            $_SESSION['message'] = 'Medical record inserted and files uploaded successfully.';
            // Redirect based on patient type
            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
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
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
                    break;
            }
            header("Location: $redirectUrl");
            exit();
        }
    }
    if (isset($_POST['editmedrecs'])) {
        $patientid = $_POST['patientid'];
        $id = $_POST['editid'];        
        $filename = $_POST['editfilename'];  
        $comment = $_POST['editcomment'];
        $patienttype = $_POST['patienttype']; 
        $admin_id = $_POST['admin_id'];


            $response = $medicalrecords->updateMedicalRecord($admin_id, $id, $patientid, $filename, $comment);

            if ($response['status'] === 'success') {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = $response['message'];
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = $response['message'];
            }

            switch ($patienttype) {
                case 'Student':
                    $redirectUrl = 'patient-studprofile.php';
                    break;
                case 'Faculty':
                    $redirectUrl = 'patient-facultyprofile.php';
                    break;
                case 'Staff':
                    $redirectUrl = 'patient-staffprofile.php';
                    break;
                case 'Extension':
                    $redirectUrl = 'patient-extensionprofile.php';
                    break;
                default:
                    $redirectUrl = 'patient-record.php';
                    break;
            }
            header("Location: $redirectUrl");
            exit();
        
    }
    if (isset($_POST['medrec_id'], $_POST['file_name'], $_POST['admin_id'])) {
        try {
            error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

            $medrecId = $_POST['medrec_id'];
            $fileName = $_POST['file_name'];
            $adminId = $_POST['admin_id'];
    
            // Fetch file path  
            $filePath = $medicalrecords->getFilePathByMedicalRecId($medrecId);
            if ($filePath && $filePath == $fileName) {
                $fullPath = "uploadmedrecs/" . $filePath;
                if (file_exists($fullPath)) {
                    if (!unlink($fullPath)) {
                        echo json_encode(['success' => false, 'message' => 'Failed to delete the file.']);
                        exit();
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'File does not exist.']);
                    exit();
                }
            }
    
            // Delete from database
            $deleteResult = $medicalrecords->deleteMedicalRecord($adminId, $medrecId);
            echo json_encode($deleteResult);
    
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }
    
    
    
    
} 
else {
    $_SESSION['status'] = 'error';
    $_SESSION['message'] = 'Invalid request method.';

    switch ($patienttype) {
        case 'Student':
            $redirectUrl = 'patient-studprofile.php';
            break;
        case 'Faculty':
            $redirectUrl = 'patient-facultyprofile.php';
            break;
        case 'Staff':
            $redirectUrl = 'patient-staffprofile.php';
            break;
        case 'Extension':
            $redirectUrl = 'patient-extensionprofile.php';
            break;
        default:
            $redirectUrl = 'patient-record.php';
            break;
    }
    header("Location: $redirectUrl");
    exit();
}

?>