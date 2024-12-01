<?php
session_start();
include '../vendor/autoload.php'; 
include '../php/sentOTP.php'; 
include '../database/config.php'; 

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            
            $db = new Database();
            $connection = $db->getConnection();

      
            $checkEmailSql = "SELECT patient_email FROM patients WHERE patient_email = :email";
            $stmt = $connection->prepare($checkEmailSql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
               
                $otp = random_int(100000, 999999);

               
                $updateSql = "UPDATE patients SET patient_code = :otp WHERE patient_email = :email";
                $updateStmt = $connection->prepare($updateSql);
                $updateStmt->bindValue(':otp', $otp, PDO::PARAM_INT);
                $updateStmt->bindValue(':email', $email, PDO::PARAM_STR);
                $updateStmt->execute();

                if ($updateStmt->rowCount() > 0) {
                   
                    $emailSender = new sentOTP();
                    $emailResult = $emailSender->sendOtp($email, $otp);

                    if ($emailResult['success']) {
                        $_SESSION['email'] = $email;
                        echo json_encode(['success' => true, 'message' => 'OTP sent successfully.']);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Failed to send OTP. Please try again.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update OTP in the database.']);
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Email not found in the database.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
