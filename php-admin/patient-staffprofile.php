<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: ../php-login/index.php'); 
  exit; 
}

include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');
include('../php/patient.php');

$db = new Database();
$conn = $db->getConnection();

$patient = new PatientManager($db);
$user = new User($conn);
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum);

if (isset($_SESSION['id']) && isset($_SESSION['type'])) {
  $patientId = $_SESSION['id'];
  $patientType = $_SESSION['type'];
  $patientDetails = $patient->getStaffData($patientId);
} else {
  echo "No patient data found.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>CIS:Clinicalog</title> 
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

  <!-- Fonts and icons -->
  <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
      custom: {
        families: [
          "Font Awesome 5 Solid",
          "Font Awesome 5 Regular",
          "Font Awesome 5 Brands",
          "simple-line-icons",
        ],
        urls: ["../css/fonts.min.css"],
      },
      active: function() {
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
      justify-content: center;
      align-items: center;
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
          <div class=row>
            <div class="mb-3">
              <a href="patient-record.php" class="back-nav">
                <i class="fas fa-arrow-left "></i> Back to Patients' Table
              </a>
            </div> 
          </div>
          <div class="page-inner">
            <div class="row">
              <h3 class="fw-bold mb-3">Patient's Profle</h3>
            </div>
            <div class="row">
              <div class="col-md-4">
                <div class="card">
                  <div class="profile-image">
                    <div class="card-header">
                      <img id="profilePic" src="default-image.jpg" alt="Profile Image" />
                      <div class="row">
                        <span style="
                        display: inline-block;
                        padding: 5px 10px;
                        border-radius: 50px;
                        background-color: #DA6F65; 
                        color: white; 
                        text-align: center;
                        min-width: 60px;">
                          Staff
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="row" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <h5 style="color: #59535A; margin: 0;">#<span id="staffID"></span></h5>
                    <h5 style="margin: 0;">
                      <span id="lastName"></span><span>, </span><span id="firstName"></span> <span id="middleName"></span>
                    </h5>
                    <h5 style="color: #59535A; margin: 0;"><span id="Office"></span></h5>
                    <h5 style="color: #59535A; margin: 0;"><span id="role"></span></h5>
                    <p style="color: #888888; margin-top: 5px;">Status: <span id="Status"></span></p>
                  </div>
                </div>
              </div>
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">Personal Details</h4>
                    </div>
                  </div>
                  <div class="card-body" id="InputInfo">
                    <div class="row">
                      <div class="col-md-4 mb-3">
                        <h5 style=" margin: 0;"><span id="age"></span></h5>
                        <label for="dob" class="form-label">Age</label>

                      </div>
                      <div class="col-md-4 mb-3">
                        <h5 style=" margin: 0;"><span id="sex"></span></h5>
                        <label for="dob" class="form-label">Sex</label>

                      </div>
                      <div class="col-md-4 mb-3">
                        <h5 style=" margin: 0;"><span id="dob"></span></h5>
                        <label for="dob" class="form-label">Date of Birth</label>

                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 mb-3">
                        <h5 style=" margin: 0;">
                          <span id="street"></span>,
                          <span id="barangay"></span>,
                          <span id="municipality"></span>,
                          <span id="province"></span>,
                          <span id="region"></span>
                        </h5>
                        <label for="dob" class="form-label">Current Address (Strt./Prk., Brgy., Municipality, Province, Region)</label>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <h5 style=" margin: 0;"><span id="email"></span></h5>
                        <label for="dob" class="form-label">Email Address</label>
                      </div>
                      <div class="col-md-6 mb-3">
                        <h5 style=" margin: 0;"><span id="contactNumber"></span></h5>
                        <label for="dob" class="form-label">Contact Number</label>

                      </div>
                    </div>
                    <div class="row">
                      <h5 style="margin-top: 9px">Emergency Contact Information</h5>
                      <div class="col-md-6 mb-3">
                        <h5 style=" margin: 0;"><span id="emergencyContactName"></span> <label for="dob" class="form-label" id="relationship">//</label></h5>
                        <label for="dob" class="form-label">Emergency Contact Name</label>
                      </div>
                      <div class="col-md-6 mb-3">
                        <h5 style=" margin: 0;"><span id="emergencyContactNumber"></span></h5>
                        <label for="dob" class="form-label">Emergency Contact Number</label>

                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Start Medical Record -->
              <div id="medicalrecord"> </div>
              <!-- End Medical Record -->
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

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {
      // Dynamically load the sidebar
      $("#sidebar").load("sidebar.php", function(response, status, xhr) {
        if (status == "error") {
          console.log("Error loading sidebar: " + xhr.status + " " + xhr.statusText);
        }
      });

      $("#header").load("header.php", function(response, status, xhr) {
        if (status == "error") {
          console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
        }
      });

      $("#medicalrecord").load("patientmedrecords.php", function(response, status, xhr) {
        if (status == "error") {
          console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
        }
      });

      <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
        var status = '<?php echo $_SESSION['status']; ?>';
        var message = '<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES); ?>';
        Swal.fire({
          title: status === 'success' ? "Success!" : "Error!",
          text: message,
          icon: status,
          confirmButtonText: "OK",
          confirmButtonColor: status === 'success' ? "#77dd77" : "#ff6961"
        }).then(() => {
          if (status === 'success') {
            sessionStorage.clear();
            window.location.href = "patient-staffprofile.php";
          }
          <?php unset($_SESSION['status'], $_SESSION['message']); ?>
        });
      <?php endif; ?>

      function formatDateToWords(dateString) {
        if (!dateString || (!dateString.includes('/') && !dateString.includes('-'))) {
          return '';
        }

        const monthNames = [
          "January", "February", "March", "April", "May", "June",
          "July", "August", "September", "October", "November", "December"
        ];

        dateString = dateString.replace(/-/g, '/');

        const [year, month, day] = dateString.split('/');

        if (!year || !month || !day) return '';

        const monthName = monthNames[parseInt(month, 10) - 1];
        const dayNumber = parseInt(day, 10);

        if (!monthName || isNaN(dayNumber)) return '';

        return `${monthName} ${dayNumber}, ${year}`;
      }

      function calculateAge(dobString) {
        if (!dobString) return '';

        const dob = new Date(dobString);
        const today = new Date();

        let age = today.getFullYear() - dob.getFullYear();
        const monthDifference = today.getMonth() - dob.getMonth();

        if (monthDifference < 0 || (monthDifference === 0 && today.getDate() < dob.getDate())) {
          age--;
        }

        return age;
      }

      function getOrdinalSuffix(num) {
        const suffixes = ["th", "st", "nd", "rd"];
        const value = num % 100;
        return suffixes[(value - 20) % 10] || suffixes[value] || suffixes[0];
      }

      // Passing PHP data to JavaScript
      var patientData = <?php echo json_encode($patientDetails); ?>;

      function populatePatientForm(data) {
        const dobFormatted = data.patient.patient_dob ? formatDateToWords(data.patient.patient_dob) : 'Ey';
        const age = calculateAge(data.patient.patient_dob);

        $('#lastName').text(data.patient.patient_lname || '');
        $('#firstName').text(data.patient.patient_fname || '');
        $('#middleName').text(data.patient.patient_mname || '');
        $('#dob').text(dobFormatted);
        $('#age').text(age);
        $('#sex').text(data.patient.patient_sex || 'Male');
        $('#staffID').text(data.staff.staff_idnum || '');
        $('#Office').text(data.staff.staff_office || '');
        $('#role').text(data.staff.staff_role);
        $('#region').text(data.address.address_region || '');
        $('#province').text(data.address.address_province || '');
        $('#municipality').text(data.address.address_municipality || '');
        $('#barangay').text(data.address.address_barangay || '');
        $('#street').text(data.address.address_prkstrtadd || '');
        $('#email').text(data.patient.patient_email || '');
        $('#contactNumber').text(data.patient.patient_connum || '');
        $('#emergencyContactName').text(data.emergencyContact.emcon_conname || '');
        $('#relationship').text(data.emergencyContact.emcon_relationship || '');
        $('#emergencyContactNumber').text(data.emergencyContact.emcon_connum || '');
        $('#Status').text(data.patient.patient_status || '');
        $('#profilePic').attr('src', `uploads/${data.patient.patient_profile}` || 'default-image.jpg');

      }

      populatePatientForm(patientData);
    });
  </script>
</body>

</html>