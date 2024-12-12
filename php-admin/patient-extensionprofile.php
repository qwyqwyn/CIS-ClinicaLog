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

  $patientDetails = $patient->getExtensionData($patientId);
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.69/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.69/vfs_fonts.js"></script>

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
            <div class="col-md-11 mb-3">
                <h3 class="fw-bold mb-3">Patient's Profle</h3>
            </div>
            <div class="col-md-1  mb-3">
                <button onclick="generatePDF()" class="btn btn-primary">Download</button>
            </div>
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
                          Extension
                        </span>
                      </div>
                    </div>
                  </div>
                  <div class="row" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <h5 style="color: #59535A; margin: 0;">#<span id="extendID"></span></h5>
                    <h5 style="margin: 0;">
                      <span id="lastName"></span><span>, </span><span id="firstName"></span> <span id="middleName"></span>
                    </h5>
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
            window.location.href = "patient-extensionprofile.php";
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

      function getOrdinalSuffix(num) {
        const suffixes = ["th", "st", "nd", "rd"];
        const value = num % 100;
        return suffixes[(value - 20) % 10] || suffixes[value] || suffixes[0];
      }


      // Passing PHP data to JavaScript
      var patientData = <?php echo json_encode($patientDetails); ?>;

      // Function to populate form inputs with patient data
      function populatePatientForm(data) {
        const dobFormatted = data.patient.patient_dob ? formatDateToWords(data.patient.patient_dob) : 'Ey';

        $('#lastName').text(data.patient.patient_lname || '');
        $('#firstName').text(data.patient.patient_fname || '');
        $('#middleName').text(data.patient.patient_mname || ''); 
        $('#dob').text(dobFormatted);
        $('#age').text(data.patient.patient_age);
        $('#sex').text(data.patient.patient_sex || 'Male');
        $('#extendID').text(data.extension.exten_idnum || '');
        $('#role').text(data.extension.exten_role);
        $('#region').text(data.address.address_region || '');
        $('#province').text(data.address.address_province || '');
        $('#municipality').text(data.address.address_municipality || '');
        $('#barangay').text(data.address.address_barangay || '');
        $('#street').text(data.address.address_prkstrtadd || '');
        $('#email').text(data.patient.patient_email || '');
        $('#contactNumber').text(data.patient.patient_connum || '');
        $('#emergencyContactName').text(data.emergencyContact.emcon_conname || 'None');
        $('#relationship').text(data.emergencyContact.emcon_relationship || 'None');
        $('#emergencyContactNumber').text(data.emergencyContact.emcon_connum || 'None');
        $('#Status').text(data.patient.patient_status || '');
        $('#profilePic').attr('src', data.patient.patient_profile ? `../uploads/${data.patient.patient_profile}` : 'default-image.jpg');
      }
      populatePatientForm(patientData);
    });
  </script>

<script>
    function convertImageToBase64(url, callback) {
        const img = new Image();
        img.crossOrigin = 'Anonymous'; // Prevent CORS issues
        img.onload = function () {
            const canvas = document.createElement('canvas');
            canvas.width = img.width;
            canvas.height = img.height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0);
            const dataURL = canvas.toDataURL('image/png'); // Convert to base64
            callback(dataURL);
        };
        img.onerror = function () {
            callback(null); // Handle errors
        };
        img.src = url;
    }

    function convertImageToBase64Element(id) {
        const image = document.getElementById(id);
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = image.width;
        canvas.height = image.height;
        ctx.drawImage(image, 0, 0);
        
        // Convert canvas to base64
        const base64Image = canvas.toDataURL('image/png');
        return base64Image;
    }

    function generatePDF() {
        const profilePicSrc = $('#profilePic').attr('src'); // Get the profile picture source

        // Convert profile picture to base64
        convertImageToBase64(profilePicSrc, function (profilePicBase64) {
            if (!profilePicBase64) {
                console.error('Failed to load profile image.');
                return;
            }

            // Fetch other populated data
            const extendID = $('#extendID').text();
            const lastName = $('#lastName').text();
            const firstName = $('#firstName').text();
            const middleName = $('#middleName').text();
            const dob = $('#dob').text();
            const age = $('#age').text();
            const sex = $('#sex').text();
            const role = $('#role').text();
            const region = $('#region').text();
            const province = $('#province').text();
            const municipality = $('#municipality').text();
            const barangay = $('#barangay').text();
            const street = $('#street').text();
            const email = $('#email').text();
            const contactNumber = $('#contactNumber').text();
            const emergencyContactName = $('#emergencyContactName').text();
            const relationship = $('#relationship').text();
            const emergencyContactNumber = $('#emergencyContactNumber').text();

            const docDefinition = {
                content: [
                    // Header with ClinicLog Title
                    {
                        stack: [
                            {
                                text: 'ClinicaLog',  // ClinicLog text
                                style: 'clinicHeader',
                                alignment: 'left'
                            },
                            {
                                text: 'Patient Profile', // Title of the report
                                style: 'subheader',
                                alignment: 'left'
                            }
                        ],
                        alignment: 'center',
                        margin: [0, 0, 0, 20]
                    },
                    // Patient Profile Section
                    {
                        stack: [
                            {
                                image: profilePicBase64,
                                width: 100,
                                height: 100,
                                alignment: 'center',
                                margin: [0, 0, 0, 10]
                            },
                            {
                                text: `${lastName}, ${firstName} ${middleName}`,
                                style: 'header',
                                alignment: 'center'
                            },
                            {
                                text: `FExtension ID: ${extendID}`,
                                style: 'subheader',
                                alignment: 'center'
                            }
                        ],
                        alignment: 'center',
                        margin: [0, 20, 0, 20]
                    },
                    // Personal Details Section
                    { text: '\nPersonal Details:', style: 'sectionHeader' },
                    { text: `Date of Birth: ${dob}` },
                    { text: `Age: ${age}` },
                    { text: `Sex: ${sex}` },
                    { text: '\nAcademic Information:', style: 'sectionHeader' },
                    { text: `Role: ${role}` },
                    { text: '\nContact Details:', style: 'sectionHeader' },
                    { text: `Email: ${email}` },
                    { text: `Contact Number: ${contactNumber}` },
                    { text: '\nAddress:', style: 'sectionHeader' },
                    { text: `${street}, ${barangay}, ${municipality}, ${province}, ${region}` },
                    { text: '\nEmergency Contact:', style: 'sectionHeader' },
                    { text: `Name: ${emergencyContactName} (${relationship})` },
                    { text: `Contact Number: ${emergencyContactNumber}` }
                ],
                styles: {
                    clinicHeader: { fontSize: 12, bold: true, color: '#DA6F65' }, // ClinicLog text color changed
                    header: { fontSize: 16, bold: true },
                    subheader: { fontSize: 12, bold: true, margin: [0, 5, 0, 5] },
                    sectionHeader: { fontSize: 14, bold: true, margin: [0, 10, 0, 3] }
                },
                pageMargins: [40, 60, 40, 40]
            };

            // Generate the PDF
            pdfMake.createPdf(docDefinition).download(`${lastName}_${extendID}.pdf`);
        });
    }
</script>
</body>

</html>