<?php

session_start();

include '../database/config.php'; 
include '../vendor/autoload.php'; 
include '../php/user.php';
include '../php/patient.php';
include '../php/sentOTP.php'; 


unset($_SESSION['message']);
unset($_SESSION['message_type']);

$database = new Database();
$db = $database->getConnection();
$adminuser = new User($db);
$patient = new PatientManager($db);


$jsScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["emaill"])) {
    $email = trim($_POST["emaill"]);

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $otp = random_int(100000, 999999);  

        if ($adminuser->emailverify($email)) {
            if ($adminuser->updateCode($email, $otp)) {
                $emailSender = new sentOTP();
                $emailResult = $emailSender->sendOtp($email, $otp); 

                if ($emailResult['success']) {
                    $_SESSION['emaill'] = $email; 
                    $type = "success";
                    $jsScript = "
                        document.body.classList.add('active');
                        Swal.fire({
                            title: 'OTP Sent!',
                            text: 'Code has been sent to your email.',
                            icon: 'success', 
                            confirmButtonText: 'Continue',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'verify.php'; 
                            }
                        }); 
                    ";
                } else {
                    $_SESSION['message'] = "An error occurred while sending the OTP.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['message'] = "Error updating OTP.";
                $_SESSION['message_type'] = "error";
            }
        }
        if ($patient->emailverify($email)) {
            if ($patient->updateCode($email, $otp)) {
                $emailSender = new sentOTP();
                $emailResult = $emailSender->sendOtp($email, $otp); 

                if ($emailResult['success']) {
                    $_SESSION['emaill'] = $email; 
                    $type = "success";
                    $jsScript = "
                        document.body.classList.add('active');
                        Swal.fire({
                            title: 'OTP Sent!',
                            text: 'Code has been sent to your email.',
                            icon: 'success', 
                            confirmButtonText: 'Continue',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'verify.php'; 
                            }
                        }); 
                    ";
                } else {
                    $_SESSION['message'] = "An error occurred while sending the OTP.";
                    $_SESSION['message_type'] = "error";
                }
            } else {
                $_SESSION['message'] = "Error updating OTP.";
                $_SESSION['message_type'] = "error";
            }
        }
         else {
            $_SESSION['message'] = "Wrong email input. Please try again.";
            $_SESSION['message_type'] = "error";
        }
    } else {
        $_SESSION['message'] = "Invalid email address provided.";
        $_SESSION['message_type'] = "error";
    }
}
?>
<script>
    // After the form submission is completed
    window.onload = function() {
        // Check if there's a query parameter for the message (success/error)
        const urlParams = new URLSearchParams(window.location.search);
        const success = urlParams.get('success');
        const message = urlParams.get('message');
        
        if (success === 'true') {
            Swal.fire({
                title: 'Success!',
                text: message,
                icon: 'success',
                confirmButtonText: 'OK'
            });
        } else if (success === 'false') {
            Swal.fire({
                title: 'Error!',
                text: message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
</script>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <link rel="stylesheet" type="text/css" href="../css/forgotpass.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <div class="wrapper">
        <div class="login-wrapper">
            <form id="sendemail-form" action="" method="post"> 
                <p id="welcome">Forgot Password?</p>
                <p id="login2">Enter the email address you used for your account,
                    and we will send a verification code to enable you to change 
                    your password.</p>
                
                <?php if (!empty($_SESSION['message']) && empty($jsScript)): ?>
                    <p id="error-message" style="color: <?= $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>; text-align: center;">
                        <?= $_SESSION['message']; ?>
                    </p>
                <?php endif; ?>

                <div class="form-container">
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label>
                        <img src="../assets/img/email.png" alt="email icon">
                        <input type="email" name="emaill" id="email" class="form-input" placeholder="Enter your Email" required>
                    </div>
                </div>

                <div class="buttons">
                    <button id="return" type="button" onclick="window.location.href='index.php';">Back</button>
                    <button id="sendemail" type="submit">Send</button>
                </div>
            </form>
        </div>
    </div>
    <?php if (!empty($jsScript)): ?>
        <script>
            <?= $jsScript; ?>
        </script>
    <?php endif; ?>    
</body>
</html>
