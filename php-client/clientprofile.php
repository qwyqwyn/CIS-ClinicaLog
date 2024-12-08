<?php
session_start();
include('../database/config.php');
include '../php/patient.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: ../php-login/index.php'); 
  exit; 
}

$db = new Database();
$conn = $db->getConnection();

$patient_id = $_SESSION['patuser_id'];
$patient_type = $_SESSION['patuser_type'];

$patient = new PatientManager($conn);
$patientData = $patient->getPatientData($patient_id); 

if ($patient_type === 'Student') {
  $redirectPage = 'patstudents.php';
} elseif ($patient_type === 'Staff') {
  $redirectPage = 'patstaff.php';
} elseif ($patient_type === 'Faculty') {
  $redirectPage = 'patfaculty.php';
} elseif ($patient_type === 'Extension') {
  $redirectPage = 'patextension.php';
} else {
  $redirectPage = 'patstudents.php'; 
}

?> 

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>User Profile</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.4/dist/sweetalert2.min.css" rel="stylesheet">


  <!-- Fonts and icons -->
  <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
      custom: {
        families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
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
  <link rel="stylesheet" href="../css/client.css">

  <!-- ICONS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pQnI6Z1ypA1QPTDdTnYkkpN0sE+0ZK3SAs+69IXS7SgSR/RG6upgjB8cSBaHh0FYv3cwUqq3Kv1BrV3iwGsnZw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    .profile-image {
      
    }

    .profile-image img {
      border-radius: 50%;
      width: 150px;
      height: 150px;
      margin-bottom: 10px;  
    }

    @media (max-width: 576px) {
      .profile-image img {
        width: 120px;
        height: 120px;
      }
    }

    .invalid {
      border-color: red !important;
    }


    .table-responsive {
      max-height: 300px; 
      overflow-y: auto;
    }

    small.text-success { color: green; }
    small.text-warning { color: orange; }
    small.text-danger { color: red; }

  </style>
</head>

<body>
  <div class="wrapper">
    <div class="main-panel" id="clientpanel">
      <!-- Header -->
      <div class="main-header" id="client_header"></div>
      <!-- Main Content -->
      <div class="container" id="content">
        <div class="page-inner">
        <div class="page-inner">
          <!-- Modal Structure -->
          <div class="row">
            <div class="col-12">
              <a href="<?php echo $redirectPage; ?>" class="back-nav">
                <i class="fas fa-arrow-left"></i> Back to Home
              </a>
            </div>
          </div>
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
                          <input type="hidden" name="patient_id" class="form-control" 
                              value="<?php echo htmlspecialchars($patient_id, ENT_QUOTES, 'UTF-8'); ?>" />

                          <div class="profile-image mb-3">
                              <img id="profilePic" 
                                  src='../uploads/<?php echo !empty($patientData->patient_profile) ? $patientData->patient_profile : 'default-image.jpg'; ?>'
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
                                  <input type="text" class="form-control" id="username" name="username" value="<?php echo ($patientData->patient_fname); ?> <?php echo ($patientData->patient_mname); ?> <?php echo ($patientData->patient_lname); ?>" disabled />
                              </div>
                              <div class="col-md-3 mb-3">
                                  <label for="email" class="form-label">Email Address</label>
                                  <input type="email" class="form-control" id="emaill" name="emaill" value="<?php echo ($patientData->patient_email); ?>" disabled />
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
                              <input type="hidden" name="email" id="email" value="<?php echo ($patientData->patient_email); ?>" />
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
                              <input type="hidden" id="userEmail" name="email" value="<?php echo ($patientData->patient_email); ?>">
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
<!-- Core JS Files -->
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap.min.js"></script>

  <!-- Core JS Files -->
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
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.4/dist/sweetalert2.all.min.js"></script>

  <!-- Kaiadmin JS -->
  <script src="../assets/js/kaiadmin.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

      <script>
        $(document).ready(function () {
      
          $("#client_header").load("clientheader.php", function (response, status, xhr) {
            if (status == "error") {
              console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
            }
          });

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
            url: 'clientupdate.php',
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

      fetch('sent-otp.php', { 
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

      fetch('verifyclient.php', { 
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

          fetch('changepassclient.php', {
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