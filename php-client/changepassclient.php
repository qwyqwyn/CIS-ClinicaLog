<?php
session_start();
include '../vendor/autoload.php';  
include '../database/config.php';  

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newPassword']) && isset($_POST['email'])) {
 
    $newPassword = trim($_POST['newPassword']);
    $email = trim($_POST['email']); 


    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

    try {
        
        $db = new Database();
        $connection = $db->getConnection();


        $updateSql = "UPDATE patients SET patient_password = :password WHERE patient_email = :email";
        $stmt = $connection->prepare($updateSql);
        $stmt->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        if ($stmt->execute()) {
           
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        } else { 
            echo json_encode(['success' => false, 'message' => 'Failed to change the password. Please try again.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
