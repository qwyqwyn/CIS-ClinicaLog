<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

include '../database/config.php';
include '../vendor/autoload.php';
include '../php/user.php';
include '../php/patient.php';
include '../php/sentOTP.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$patient = new PatientManager($db); 


$jsScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['otp'])) {
        $otp = trim($_POST['otp']);
        $email = $_SESSION['emaill'] ?? '';

        if (!empty($email) && !empty($otp)) {
            if ($user->verifyOtp($email, $otp)) {
                $_SESSION['emaill'] = $email;
                $jsScript = "
                    document.body.classList.add('active');
                    Swal.fire({
                        title: 'Verified Successfully!',
                        text: 'Please press continue to change password',
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'changepass.php'; 
                        }
                    });
                "; 
            }if ($patient->verifyOtp($email, $otp)) {
                $_SESSION['emaill'] = $email;
                $jsScript = "
                    document.body.classList.add('active');
                    Swal.fire({
                        title: 'Verified Successfully!',
                        text: 'Please press continue to change password',
                        icon: 'success',
                        confirmButtonText: 'Continue',
                        allowOutsideClick: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'changepass.php'; 
                        }
                    });
                "; 
            } else {
                $_SESSION['message'] = "Invalid OTP. Please try again.";
                $_SESSION['message_type'] = "error";
            }
            
        } else {
            $_SESSION['message'] = "Please enter the OTP.";
            $_SESSION['message_type'] = "error";
        }
    } elseif (isset($_POST['resend']) && $_POST['resend'] === 'check') {
        $email = $_SESSION['emaill'] ?? '';

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $otp = random_int(100000, 999999);

            if ($user->emailverify($email)) {
                if ($user->updateCode($email, $otp)) {
                    $emailSender = new sentOTP();
                    $emailResult = $emailSender->sendOtp($email, $otp);

                    if ($emailResult['success']) {
                        $_SESSION['emaill'] = $email;
                        $_SESSION['message'] = "New code sent!";
                        $_SESSION['message_type'] = "success";
                    } else {
                        $_SESSION['message'] = "An error occurred while sending the OTP.";
                        $_SESSION['message_type'] = "error";
                    }
                } else {
                    $_SESSION['message'] = "Error updating OTP.";
                    $_SESSION['message_type'] = "error"; 
                }
            } else {
                $_SESSION['message'] = "Email not found. Please try again.";
                $_SESSION['message_type'] = "error";
            }
        } else {
            $_SESSION['message'] = "Invalid email address provided.";
            $_SESSION['message_type'] = "error";
        }

        // Return the message directly as JSON
        echo json_encode([
            'message' => $_SESSION['message'] ?? '',
            'message_type' => $_SESSION['message_type'] ?? ''
        ]);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <link rel="stylesheet" type="text/css" href="../css/verify.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <div class="wrapper">
        <div class="login-wrapper">
            <form action="verify.php" method="POST" autocomplete="off">
                <p id="welcome">Verification</p>
                <p id="login2">Enter the code sent through your email. </p>

                <?php if (isset($_SESSION['message'])): ?>
                    <p id="error-message" style="color: <?= $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>; text-align: center;">
                        <?= $_SESSION['message']; ?>
                    </p>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <div class="form-container">
                    <div class="form-group">
                        <label for="otp" class="form-label">Code:</label>
                        <img src="../assets/img/email.png" alt="email icon">
                        <input type="text" name="otp" id="otp" class="form-input" placeholder="Enter Code">
                        <span class="right-placeholder" id="countdown"></span>
                    </div>

                    <div class="resend">
                        <span id="countdown"></span>
                        <span id="click"><a href="#" id="resend-link" onclick="resendCode(); return false;">Resend Code</a></span>
                    </div>
                </div>

                <button type="submit" id="sendemail" name="sendotp">Continue</button>
            </form>
            <div class="back-to-login">
                <img src="../assets/img/back.png" alt="Back icon">
                <a href="index.php" id="backlogin">Back to Login Page</a>
            </div>
        </div>
    </div>

    <?php if (!empty($jsScript)): ?>
        <script>
            <?= $jsScript; ?>
        </script>
    <?php endif; ?>

    <script>
        function resendCode() {
            // Send a request to the server-side PHP script
            fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'resend=check'
                })
                .then(response => response.json())
                .then(data => {
                    // Handle the response from PHP
                    const messageElement = document.getElementById('error-message');
                    messageElement.innerText = data.message;
                    messageElement.style.color = data.message_type === 'success' ? 'green' : 'red';
                });
            startCountdown();
        }

        function startCountdown() {
            let countdown = 20; // 20 seconds
            const resendLink = document.getElementById('resend-link');
            resendLink.style.pointerEvents = 'none'; // Disable link

            const countdownDisplay = document.getElementById('countdown');
            countdownDisplay.innerText = `${countdown} seconds`;

            const countdownTimer = setInterval(function() {
                countdown--;
                countdownDisplay.innerText = `${countdown} seconds`;
                if (countdown <= 0) {
                    clearInterval(countdownTimer);
                    resendLink.style.pointerEvents = 'auto'; // Enable link
                    countdownDisplay.innerText = ''; // Clear display when done
                }
            }, 1000); // Update every second
        }
    </script>

</body>

</html>