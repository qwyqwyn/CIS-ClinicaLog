<?php
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

session_start();

include '../database/config.php'; 
include '../php/user.php';
include '../php/patient.php'; 
$database = new Database();       
$db = $database->getConnection();
$user = new User($db);
$patient = new PatientManager($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email']) && isset($_POST['password'])) {

    $email = $_POST['email'];  
    $password = $_POST['password'];  
    $defaultadmin = "Administrator";
      
    $userData = $user->userExists($email, $password);
 
if ($userData) {  
    session_regenerate_id(true);  
    $_SESSION['logged_in'] = true;  
    $_SESSION['user_idnum'] = $userData->user_idnum;  
    $_SESSION['user_status'] = $userData->user_status;
    $_SESSION['user_position'] = $userData->user_position;
    $_SESSION['user_role'] = $userData->user_role;

    if ($userData->user_status === 'Active') {  
        if ($_SESSION['user_position'] === $defaultadmin) {
            header('Location: ../php-admin/index.php');  
            exit;
        } elseif ($_SESSION['user_role'] === 'Super Admin') {
            header('Location: ../php-admin/index.php'); 
            exit;
        } elseif ($_SESSION['user_role'] === 'Admin') { 
            header('Location: ../php-admin/adminindex.php');   
            exit;
        }
    } else {   
        $_SESSION['error_message'] = "Account can't be used."; 
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
} else {
    $patientData = $patient->userpatientExists($email, $password);

    if ($patientData) {
        if ($patientData->patient_status === 'Active') {
            session_regenerate_id(true); 
            $_SESSION['logged_in'] = true; 
            $_SESSION['patuser_id'] = $patientData->patient_id;  
            $_SESSION['patuser_status'] = $patientData->patient_status;
            $type = $_SESSION['patuser_type'] = $patientData->patient_patienttype;
            
            switch ($type) {
                case 'Student':  
                    header('Location: ../php-client/patstudents.php'); 
                    break;
                case 'Faculty':
                    header('Location: ../php-client/patfaculty.php'); 
                    break;
                case 'Staff':
                    header('Location: ../php-client/patstaff.php');  
                    break;
                case 'Extension':
                    header('Location: ../php-client/patextension.php'); 
                    break;
            } 
            echo json_encode(['status' => 'success']);
            exit;
        } else {
            $_SESSION['error_message'] = "Account can't be used.";  
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
    } else {
        $_SESSION['error_message'] = "Invalid email or password.";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
} 

}
 

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon"/>
    <link rel="stylesheet" type="text/css" href="../css/style.css">
</head>
<body>
    <a href="../php-admin/index.php"></a>
    <img src="../assets/img/logo.png" alt="logo" id="logo">
    <h1 id="name">USeP Clinic Inventory System</h1>

    <div class="wrapper">
        <div class="login-wrapper">
            <form id="login-form" action="" method="post" autocomplete="off">
                <p id="welcome">Welcome!</p>
                <p id="login2">Login to Continue</p>
                
                <?php if ($error_message): ?> 
                    <p id="error-message" style="color: red; text-align: center;">
                        <?= $error_message; ?>
                    </p>
                <?php endif; ?>
                
                <div class="form-container">
                    <div class="form-group">
                        <label for="email" class="form-label">Email:</label> 
                        <img src="../assets/img/email.png" alt="email icon">
                        <input type="text" name="email" id="email" class="form-input" placeholder="Enter your Email" required>
                    </div>
     
                    <div class="form-group">
                        <label for="password" class="form-label">Password:</label>
                        <img src="../assets/img/password.png" alt="password icon">
                        <input type="password" name="password" id="password" class="form-input" placeholder="Enter your Password" required>
                      <input type="checkbox" id="show-password"> 
                    </div> 

                    <div class="forgotpassword"> 
                        <span id="forgot">Forgot Password?</span>
                        <span id="click"><a href="forgotpassword.php">Click Here.</a></span>
                    </div>
                </div>
 
                <button id="loginbtn" type="submit">Login</button>
            </form>
        </div>
    </div>
    <script>
        document.getElementById('show-password').addEventListener('change', function() {
            const passwordField = document.getElementById('password');
            if (this.checked) {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password'; 
            }
            });
    </script>
</body>
</html>
