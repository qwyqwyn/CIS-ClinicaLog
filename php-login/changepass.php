<?php
session_start();
include '../database/config.php'; 
include '../php/user.php';
include '../php/patient.php';


unset($_SESSION['error_message']);

$database = new Database();
$db = $database->getConnection();
$user = new User($db);
$patient = new PatientManager($db); 

$jsScript = '';
$message = ''; 
$type = '';

if (isset($_POST['changed_password'])) {
    $pass = $_POST['password'];
    $confirmpass = $_POST['confirm_password'];

    if ($pass !== $confirmpass) {
        $_SESSION['message'] = "Passwords do not match!";
        $_SESSION['message_type'] = "error";
    }
 
    elseif (!preg_match('/^(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]{6,}$/', $pass)) {
        $_SESSION['message'] = "Password must be at least 6 characters long and contain at least one special character.";
        $_SESSION['message_type'] = "error";
    }
   
    else {
        $email = $_SESSION['emaill'];
        $encryptpass = password_hash($pass, PASSWORD_DEFAULT);

        if ($user->changePassword($email, $encryptpass)) {
            $type = "success"; 
            $jsScript = "
                document.body.classList.add('active');
                Swal.fire({
                    title: 'Password Updated!',
                    text: 'Click continue to login.',
                    icon: 'success',
                    confirmButtonText: 'Continue',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php'; 
                    }
                });
            ";
        }
        if ($patient->changePassword($email, $encryptpass)) {
            $type = "success"; 
            $jsScript = "
                document.body.classList.add('active');
                Swal.fire({
                    title: 'Password Updated!',
                    text: 'Click continue to login.',
                    icon: 'success', 
                    confirmButtonText: 'Continue',
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'index.php'; 
                    }
                });
            ";
        } else {
            $_SESSION['message'] = "Error updating password. Please try again.";
            $_SESSION['message_type'] = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head> 
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS</title>
    <link rel="stylesheet" type="text/css" href="../css/changepass.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
            <img src="../assets/img/logo.png" alt="logo" id="logo">
            <h1 id="name">USeP Clinic Inventory System</h1>

            <div class="wrapper">
                <div class="login-wrapper">
                    <form action="" method="post">
                        <input type="hidden" name="otp" value="<?php echo htmlspecialchars($otp); ?>">
                        <p id="welcome">Change Password</p>
                        <p id="login2">Create your new Password</p>

                        <?php if (isset($_SESSION['message'])): ?>
                            <p id="error-message" style="color: <?= $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>; text-align: center;">
                                <?= $_SESSION['message']; ?>
                            </p>
                            <?php unset($_SESSION['message']); ?>
                        <?php endif; ?>

                        <div class="form-container">
                            <div class="form-group">
                                <label for="password" class="form-label">Password:</label>
                                <img src="../assets/img/password.png" alt="password icon">
                                <input type="password" name="password" id="password" class="form-input" placeholder="Enter your new Password" required>
                                <input type="checkbox" id="show-password"> 
                            </div>
                    
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirm Password:</label>
                                <img src="../assets/img/password.png" alt="password icon">
                                <input type="password" name="confirm_password" id="confirm_password" class="form-input" placeholder="Confirm Password" required>
                                <input type="checkbox" id="show1"> 
                            </div>
                        </div>
                        <button type="submit" id="loginbtn" name="changed_password">Submit</button>
                        
                        <div class="back-to-login">
                            <img src="../assets/img/back.png" alt="Back icon">
                            <a href="index.php" id="backlogin">Back to Login Page</a>
                        </div>
                    </form>
                </div>
            </div>
            <?php if (isset($jsScript)): ?>
                <script>
                    <?php echo $jsScript; ?>
                </script>
            <?php endif; ?>

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
            <script>
                document.getElementById('show1').addEventListener('change', function() {
                    const passwordField = document.getElementById('confirm_password');
                    if (this.checked) {
                        passwordField.type = 'text';
                    } else {
                        passwordField.type = 'confirm_password';
                    }
                });
            </script>
</body>
</html>
