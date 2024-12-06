
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

// Fetch and prepare data once
$extensionData = $patient->getextensionData($patient_id);

$profilePic = $extensionData['patient']['patient_profile'];
$extensionID = $extensionData['extension']['exten_idnum'];
$lastName = $extensionData['patient']['patient_lname'];
$firstName = $extensionData['patient']['patient_fname'];
$middleName = $extensionData['patient']['patient_mname'];
$role = $extensionData['extension']['exten_role'];
$dob = $extensionData['patient']['patient_dob'];
$sex = $extensionData['patient']['patient_sex'];
$address = "{$extensionData['address']['address_prkstrtadd']}, 
    {$extensionData['address']['address_barangay']}, 
    {$extensionData['address']['address_municipality']}, 
    {$extensionData['address']['address_province']}, 
    {$extensionData['address']['address_region']}";
$email = $extensionData['patient']['patient_email'];
$contactNumber = $extensionData['patient']['patient_connum'];
$emergencyContactName = !empty($studentData['emergencyContact']['emcon_conname']) 
    ? $studentData['emergencyContact']['emcon_conname'] 
    : 'None';

$emergencyContactNumber = !empty($studentData['emergencyContact']['emcon_connum']) 
    ? $studentData['emergencyContact']['emcon_connum'] 
    : 'None';

$status = $extensionData['patient']['patient_status']; 

// Calculate age
$dobDateTime = new DateTime($dob);
$age = $dobDateTime->diff(new DateTime())->y;

?>

<!DOCTYPE html> 
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>Client Panel</title>
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
        families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
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
  <link rel="stylesheet" href="../css/client.css">

  <!-- ICONS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pQnI6Z1ypA1QPTDdTnYkkpN0sE+0ZK3SAs+69IXS7SgSR/RG6upgjB8cSBaHh0FYv3cwUqq3Kv1BrV3iwGsnZw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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
    <div class="main-panel" id="clientpanel">
      <!-- Header -->
      <div class="main-header" id="client_header"></div>
      <!-- Main Content -->
      <div class="container" id="content">
        <div class="page-inner">
        <div class="page-inner">
          <div class=row>
          </div>
          <div class="page-inner">
            <div class="row">
              <h3 class="fw-bold mb-3">Patient's Profile</h3>
            </div>
            <div class="row">
        <div class="col-md-4">
        <div class="card">
        <div class="profile-image">
            <div class="card-header">
            <img id="profilePic" src="/php-admin/uploads/<?php echo $profilePic; ?>" alt="" />  
                <div class="row" >                
                    <span style="
                        display: inline-block;
                        padding: 5px 10px;
                        border-radius: 50px;
                        background-color: #DA6F65; 
                        color: white; 
                        text-align: center;
                        min-width: 60px;">
                        Extension
                </span>  
            </div>         
            </div>
        </div>
            <div class="row" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                <h5 style="color: #59535A; margin: 0;">#<?php echo $extensionID; ?></h5>
                <h5 style="margin: 0;">
                    <span id="lastName"><?php echo $lastName; ?></span><span>, </span><span id="firstName"><?php echo $firstName; ?></span> <span id="middleName"><?php echo $middleName; ?></span>
                </h5>
                <h5 style="color: #59535A; margin: 0;">Role: <?php echo $role; ?></h5>
                <p style="color: #888888; margin-top: 5px;">Status: <?php echo $status; ?></p>
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
                        <h5 style="margin: 0;" id="age"><?php echo $age; ?> years old</h5>
                        <label for="dob" class="form-label">Age</label>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5 style="margin: 0;" id="sex"><?php echo $sex; ?></h5>
                        <label for="dob" class="form-label">Sex</label>
                    </div>
                    <div class="col-md-4 mb-3">
                        <h5 style="margin: 0;" id="dob"><?php echo $dob; ?></h5>
                        <label for="dob" class="form-label">Date of Birth</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <h5 style="margin: 0;">
                            <?php echo $address; ?>
                        </h5>
                        <label for="dob" class="form-label">Current Address (Strt./Prk., Brgy., Municipality, Province, Region)</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h5 style="margin: 0;" id="email"><?php echo $email; ?></h5>
                        <label for="dob" class="form-label">Email Address</label>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 style="margin: 0;" id="contactNumber"><?php echo $contactNumber; ?></h5>
                        <label for="dob" class="form-label">Contact Number</label>
                    </div>
                </div>
                <div class="row">
                    <h5 style="margin-top: 9px">Emergency Contact Information</h5>
                    <div class="col-md-6 mb-3">
                        <h5 style="margin: 0;" id="emergencyContactName"><?php echo $emergencyContactName; ?></h5>
                        <label for="dob" class="form-label">Emergency Contact Name</label>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h5 style="margin: 0;" id="emergencyContactNumber"><?php echo $emergencyContactNumber; ?></h5>
                        <label for="dob" class="form-label">Emergency Contact Number</label>
                    </div>
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

  <!-- Kaiadmin JS -->
  <script src="../assets/js/kaiadmin.min.js"></script>

  <!-- Appointment and Calendar Functionality Script -->
  <script>
    $(document).ready(function() {
      // Load Header
      $("#client_header").load("clientheader.php", function(response, status, xhr) {
        if (status == "error") {
          console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
        }
      });

      $("#medicalrecord").load("client-patientmedrecords.php", function(response, status, xhr) {
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
            timer: 3000,  
            showConfirmButton: false,
            willClose: () => {
                if (status === 'success') {
                    sessionStorage.clear();
                    window.location.href = "patextension.php";
                }
            }
        });

        // Remove session data after alert closes
        <?php unset($_SESSION['status'], $_SESSION['message']); ?>
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

          $(document).ready(function () {
              var $patientDetails = <?php echo json_encode($patientData); ?>;

          });  
    });
  </script>
</body>

</html>