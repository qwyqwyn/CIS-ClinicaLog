<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/dashboard.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit;
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];

$dashboard = new Dashboard($conn); 
$userData = $user->getUserData($user_idnum);  

?>
 
<!DOCTYPE html> 
<html lang="en">  
<head> 
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Account Settings</title> 
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" /> 
    <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon"/>

    <!-- Fonts and icons -->
    <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
    <script>
      WebFont.load({ 
        google: { families: ["Public Sans:300,400,500,600,700"] },
        custom: {
          families: [ 
            "Font Awesome 5 Solid",
            "Font Awesome 5 Regular",
            "Font Awesome 5 Brands",
            "simple-line-icons",
          ],
          urls: ["../css/fonts.min.css"], 
        },
        active: function () {
            
          sessionStorage.fonts = true;
        }, 
      });
    </script>
 
    <!-- CSS Files -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/plugins.min.css" /> 
    <link rel="stylesheet" href="../css/kaiadmin.min.css" /> 

    <!-- ICONS -->
    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">

    <style>
    .sidebar {
      transition: background 0.3s ease;
      /* Initial background */
      background: linear-gradient(to bottom, #DB6079, #DA6F65, #E29AB4);
    }

    .logo-header {
      transition: background 0.3s ease;
    }

    .profile-image {
      display: flex;

      flex-direction: column;
      margin-bottom: 20px;
    }

    .profile-image img {
      border-radius: 50%;
      width: 150px;
      height: 150px;
      margin-bottom: 10px;
    }

    .upload-btn {
      margin-top: 10px;
    }
  </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar"></div>
        <!-- End Sidebar -->
        <div class="main-panel">
            <!-- Header --> 
            <div class="main-header" id="header"></div>
            <!-- Main Content -->
            <div class="container" id="content">
            <div class="page-inner">
          <div class="page-inner">
          <div class="row">
          <div class="col-md-12">
              <!-- Card for Profile Update -->
              <div class="card mb-4">
                  <div class="card-header">
                      <h4 class="card-title">Update Profile Picture & Account Info</h4>
                  </div>
                  <div class="card-body">
                      <form id="profileForm" method="POST" enctype="multipart/form-data">
                          <input type="hidden" name="user_idnum" class="form-control" 
                              value="<?php echo htmlspecialchars($userData['user_idnum']); ?>" />

                          <div class="profile-image mb-3">
                              <img id="profilePic" 
                                  src='/php-admin/uploads/<?php echo !empty($userData['user_profile']) ? $userData['user_profile'] : 'default-image.jpg'; ?>'
                                  alt="Profile Picture" />
                          </div>

                          <div class="row">
                              <div class="col-md-3 mb-3">
                                  <input type="file" class="form-control" id="addprofile" name="addprofile" accept="image/*" />
                              </div>
                              <div class="col-md-3 mb-3">
                                  <button type="submit" class="btn btn-primary">Update Profile</button>
                              </div>
                          </div>

                          <div class="row">
                              <div class="col-md-3 mb-3">
                                  <label for="username" class="form-label">Username</label>
                                  <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($userData['user_fname']); ?> <?php echo htmlspecialchars($userData['user_mname']); ?> <?php echo htmlspecialchars($userData['user_lname']); ?>" disabled />
                              </div>
                              <div class="col-md-3 mb-3">
                                  <label for="email" class="form-label">Email Address</label>
                                  <input type="email" class="form-control" id="emaill" name="emaill" value="<?php echo htmlspecialchars($userData['user_email']); ?>" disabled />
                              </div>
                              <div class="col-md-3 mb-3"> 
                              </div>
                          </div>
                      </form> 
                  </div>
              </div>
                  <div class="col-md-12">
                  <div class="card">
                      <div class="card-header">
                          <h4 class="card-title">Change Password</h4>
                      </div>
                      <div class="card-body">
                      <form method="POST" id="otpForm">
                              <input type="hidden" name="email" id="email" value="<?php echo htmlspecialchars($userData['user_email']); ?>" />
                              <div id="confirmChangePassword">
                                  <p>Do you want to change your password?</p>
                                  <button type="submit" class="btn btn-primary" id="sendOtpBtn">Yes, Change Password</button>
                              </div>
                          </form>
                              <form method="POST" id="verifyForm">
                              <div id="otpSection" style="display: none;">
                                  <label for="otp" class="form-label">Enter OTP</label>
                                  <input type="text" class="form-control" id="otp" name="otp" required />
                                  <button type="button" class="btn btn-secondary mt-2" id="verify">Send OTP</button>
                                </div>
                              </form>
                              <form method="POST" id="changePasswordForm">
                              <input type="hidden" id="userEmail" name="emaill" value="<?php echo htmlspecialchars($userData['user_email']); ?>" />
                                    <div id="newPasswordSection" style="display: none;">
                                        <div class="row">
                                        <div class="col-md-6 mb-3">
                                        <label for="newPassword" class="form-label">New Password</label>
                                        <input type="password" class="form-control" id="newPassword" name="newPassword" required />
                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox" id="showNewPassword" />
                                          <label class="form-check-label" for="showNewPassword">Show Password</label>
                                        </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                        <label for="confirmPassword" class="form-label">Confirm New Password</label>
                                        <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" required />
                                        <div class="form-check">
                                          <input class="form-check-input" type="checkbox" id="showConfirmPassword" />
                                          <label class="form-check-label" for="showConfirmPassword">Show Password</label>
                                        </div>
                                        </div>
                                        </div>
                                        <div id="passwordStrengthFeedback"></div>
                                        <div id="passwordMatchFeedback"></div>
                                        <button type="button" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
                                    </div>
                                </form>
                              </div>
                      </div>
                  </div>
              </div>
          </div>
          </div>
        </div>
            </div>

          </div>
            </div> 
        </div>
    </div>

    
    

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
 
    <!-- Chart JS -->
    <script src="../assets/js/plugin/chart.js/chart.min.js"></script>

    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Chart Circle -->
    <script src="../assets/js/plugin/chart-circle/circles.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- jQuery Vector Maps -->
    <script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
    <script src="../assets/js/plugin/jsvectormap/world.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
    $(document).ready(function() {
       
        $("#sidebar").load("sidebar.php", function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading sidebar: " + xhr.status + " " + xhr.statusText);
            } else {
                
                var currentPage = window.location.pathname.split('/').pop(); 

                $('.nav-item').removeClass('active');

                $('.nav-item').each(function() {
                    var href = $(this).find('a').attr('href');
                    if (href.indexOf(currentPage) !== -1) {
                        $(this).addClass('active');
                    }
                });
            }
        });

        $("#header").load("header.php", function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
            }
        });
    });
</script>

<script>
        $(document).ready(function () {

          $("#changePasswordForm").on("submit", function (event) {
            event.preventDefault(); 

            const currentPassword = $("#currentPassword").val();
            const newPassword = $("#newPassword").val();
            const confirmPassword = $("#confirmPassword").val();

            const correctPassword = "your_actual_password"; 

            if (currentPassword !== correctPassword) {
              $("#currentPassword").addClass("invalid");
              return; 
            } else {
              $("#currentPassword").removeClass("invalid");
            }

            if (newPassword !== confirmPassword) {
              alert("New password and confirmation do not match.");
              return; 
            }

            this.submit(); 
          });

          $("#showCurrentPassword").on("change", function () {
            const type = $(this).is(":checked") ? "text" : "password";
            $("#currentPassword").attr("type", type);
          });

          $("#showNewPassword").on("change", function () {
            const type = $(this).is(":checked") ? "text" : "password";
            $("#newPassword").attr("type", type);
          });

          $("#showConfirmPassword").on("change", function () {
            const type = $(this).is(":checked") ? "text" : "password";
            $("#confirmPassword").attr("type", type);
          });
        });
        $(document).ready(function() {

    $('#profileForm').on('submit', function(e) {
        e.preventDefault(); 

        var formData = new FormData(this);

        $.ajax({
            url: 'settingsupprofile.php',
            type: 'POST',
            data: formData,
            contentType: false, 
            processData: false, 
            success: function(response) {
                console.log(response); 

                var jsonResponse = JSON.parse(response);

                if (jsonResponse.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: jsonResponse.message, 
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(() => {
                       
                        location.reload(); 
                    });
                } else {
                   
                    Swal.fire({
                        title: 'Error!',
                        text: jsonResponse.message, 
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error(error); 
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error updating the profile. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    });
});
      </script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
 
    const form = document.getElementById('otpForm');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const otpSection = document.getElementById('otpSection');
    const confirmChangePassword = document.getElementById('confirmChangePassword');

    form.addEventListener('submit', function(event) {
      event.preventDefault(); 

      Swal.fire({
        title: 'Sending OTP...',
        text: 'Please wait.',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const formData = new FormData(form);

      fetch('settingsentotp.php', { 
        method: 'POST',
        body: formData
      })
      .then(response => response.json()) 
      .then(data => {
   
        if (data.success) {
         
          confirmChangePassword.style.display = 'none';

          otpSection.style.display = 'block';

          Swal.fire({
            title: 'Success!',
            text: data.message,
            icon: 'success',
            confirmButtonText: 'OK'
          });
        } else {
          Swal.fire({
            title: 'Error!',
            text: data.message,
            icon: 'error',
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
      
        Swal.fire({
          title: 'Error!',
          text: 'An unexpected error occurred.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
      });
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const otpSection = document.getElementById('otpSection');
    const verifyBtn = document.getElementById('verify');
    const otpInput = document.getElementById('otp');
    const form = document.getElementById('verifyForm');
    const newPasswordSection = document.getElementById('newPasswordSection');

    // Function to handle OTP verification
    verifyBtn.addEventListener('click', function () {
      const otp = otpInput.value.trim();

      if (!otp) {
        Swal.fire({
          title: 'Error!',
          text: 'Please enter the OTP.',
          icon: 'error',
          confirmButtonText: 'OK'
        });
        return;
      }

      Swal.fire({
        title: 'Verifying OTP...',
        text: 'Please wait.',
        icon: 'info',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });

      const formData = new FormData(form);
      formData.append('otp', otp); 

      fetch('settingverify.php', { 
        method: 'POST',
        body: formData
      })
        .then(response => response.json()) 
        .then(data => {
         
          if (data.success) {
            Swal.fire({
              title: 'Success!',
              text: data.message,
              icon: 'success',
              confirmButtonText: 'OK'
            }).then(() => {
                otpSection.style.display = 'none';
  
                newPasswordSection.style.display = 'block';

            });
          } else {
            Swal.fire({
              title: 'Error!',
              text: data.message,
              icon: 'error',
              confirmButtonText: 'OK'
            });
          }
        })
        .catch(error => {
     
          Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred. Please try again.',
            icon: 'error',
            confirmButtonText: 'OK'
          });
        });
    });
  });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
      const showNewPassword = document.getElementById("showNewPassword");
      const showConfirmPassword = document.getElementById("showConfirmPassword");
      const newPasswordInput = document.getElementById("newPassword");
      const confirmPasswordInput = document.getElementById("confirmPassword");
      const passwordStrengthFeedback = document.getElementById("passwordStrengthFeedback");
      const passwordMatchFeedback = document.getElementById("passwordMatchFeedback");
      const changePasswordBtn = document.getElementById("changePasswordBtn");

      showNewPassword.addEventListener("change", function () {
        newPasswordInput.type = showNewPassword.checked ? "text" : "password";
      });

      showConfirmPassword.addEventListener("change", function () {
        confirmPasswordInput.type = showConfirmPassword.checked ? "text" : "password";
      });

      function checkPasswordStrength(password) {
        const strength = {
          weak: /^(?=.*[a-z]).{6,}$/,
          medium: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9]).{6,}$/,
          strong: /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[\W_]).{8,}$/
        };

        if (strength.strong.test(password)) {
          passwordStrengthFeedback.innerHTML = "<small class='text-success'>Strong password</small>";
          return "strong";
        } else if (strength.medium.test(password)) {
          passwordStrengthFeedback.innerHTML = "<small class='text-warning'>Medium strength password</small>";
          return "medium";
        } else if (strength.weak.test(password)) {
          passwordStrengthFeedback.innerHTML = "<small class='text-danger'>Weak password</small>";
          return "weak";
        } else {
          passwordStrengthFeedback.innerHTML = "<small class='text-danger'>Password too weak</small>";
          return "weak";
        }
      }

      newPasswordInput.addEventListener("input", function () {
        checkPasswordStrength(newPasswordInput.value);
        checkPasswordMatch();
      });

      confirmPasswordInput.addEventListener("input", checkPasswordMatch);

      function checkPasswordMatch() {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        if (confirmPassword === "") {
          passwordMatchFeedback.innerHTML = ""; 
          return false;
        }

        if (newPassword !== confirmPassword) {
          passwordMatchFeedback.innerHTML = '<span style="color: red;">Passwords do not match!</span>';
          return false;
        } else {
          passwordMatchFeedback.innerHTML = '<span style="color: green;">Passwords match!</span>';
          return true;
        }
      }

      changePasswordBtn.addEventListener("click", function () {
        const newPassword = newPasswordInput.value;
        const confirmPassword = confirmPasswordInput.value;
        const email = document.getElementById("userEmail").value;

        const strength = checkPasswordStrength(newPassword);
        const isMatch = checkPasswordMatch();

        if (strength === "strong" && isMatch) {
          const formData = new FormData();
          formData.append("newPassword", newPassword);
          formData.append("email", email);

          fetch('settingchangepass.php', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            Swal.fire({
              icon: data.success ? 'success' : 'error',
              title: data.success ? 'Success' : 'Error',
              text: data.message,
              confirmButtonText: 'OK'
            }).then(() => {
              if (data.success) location.reload();
            });
          })
          .catch(error => {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An error occurred while changing the password.',
              confirmButtonText: 'OK'
            });
          });
        } else {
          Swal.fire({
            icon: 'warning',
            title: 'Invalid Input',
            text: 'Please correct the errors before submitting.',
            confirmButtonText: 'OK'
          });
        }
      });
    });
  </script>

</body>
</html>
