<?php
session_start();
header('Content-Type: application/json');
include '../database/config.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $enteredOtp = trim($_POST['otp']);
    $email = $_SESSION['email']; 

    try {
        $db = new Database();
        $connection = $db->getConnection();

        $query = "SELECT patient_code FROM patients WHERE patient_email = :email";
        $stmt = $connection->prepare($query);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $storedOtp = $stmt->fetchColumn();

            if ($enteredOtp == $storedOtp) {
 
                $updateOtpQuery = "UPDATE patients SET patient_code = 0 WHERE patient_email = :email";
                $updateStmt = $connection->prepare($updateOtpQuery);
                $updateStmt->bindValue(':email', $email, PDO::PARAM_STR);
                $updateStmt->execute();

                unset($_SESSION['email']); 

                echo json_encode(['success' => true, 'message' => 'OTP verified successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid OTP.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Email not found in the database.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
