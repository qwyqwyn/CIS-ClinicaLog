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

    .hidden {
      display: none;
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
          <div class="row">
            <h4 class="card-title">Edit Patient's Information</h4>
          </div>
          <!-- Form Starts Here -->
          <form id="uppatientForm" action="patientcontrol.php" method="POST" enctype="multipart/form-data">
            <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>" />
            <input type="hidden" class="form-control" id="patientid" name="patientid" value="<?php echo $patientId; ?>" />
            <div class="row">
              <div class="profile-image col-md-3 text-center mx-auto d-flex flex-column align-items-center">
                <img id="profilePic" src="default-image.jpg" alt="Profile Image" class="img-thumbnail mb-2" />
                <label for="addprofile" class="form-label">Upload New Profile</label>
                <input id="addprofile" name="addprofile" type="file" class="form-control" accept=".png, .jpg, .jpeg" style="border: 2px solid #DA6F65;" />
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center ">
                      <h4 class="card-title">Personal Details</h4>
                    </div>
                  </div>
                  <div class="card-body" id="InputInfo">
                    <!-- Name Fields -->
                    <div class="row">
                      <div class="col-md-3 mb-3">
                        <label for="lastName" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" placeholder="  last name" />
                      </div>
                      <div class="col-md-3 mb-3">
                        <label for="firstName" class="form-label">First Name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" placeholder="  first name" />
                      </div>
                      <div class="col-md-2 mb-3">
                        <label for="middleName" class="form-label">Middle Name</label>
                        <input type="text" class="form-control" id="middleName" name="middleName" placeholder="  middle name" />
                      </div>
                      <div class="col-md-2 mb-3">
                        <label for="dob" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="dob" name="dob" />
                      </div>
                      <div class="col-md-2 mb-3">
                        <label for="sex" class="form-label">Sex</label>
                        <select class="form-select form-control" id="sex" name="sex">
                          <option selected disabled>Select Sex</option>
                          <option value="Female">Female</option>
                          <option value="Male">Male</option>
                        </select>
                      </div>
                    </div>

                    <!-- ID and Academic Info -->
                    <div class="row">
                      <div class="col-md-3 mb-3">
                        <label for="staffID" class="form-label">ID Number</label>
                        <input type="text" class="form-control" id="staffID" name="staffID" placeholder="ID number" />
                      </div>

                      <!--  Office Input -->
                      <div class="col-md-4 mb-3">
                        <label for="Office" class="form-label"> Office</label>
                        <select class="form-select  form-control" id="office" name="office">
                          <option value="" hidden>Select an Office...</option>
                          <option value="Click to type...">Click to type...</option>
                          <option value="Office of the Chancellor">Office of the Chancellor</option>
                          <option value="Administrative Office">Administrative Office</option>
                          <option value="Campus Registrar Office">Campus Registrar Office</option>
                          <option value="Office of Student Affairs and Services">Office of Student Affairs and Services</option>
                          <option value="Campus Clinic">Campus Clinic</option>
                          <option value="System and Data Management Division Office (SDMD)">System and Data Management Division Office (SDMD)</option>
                          <option value="CTET Dean's Office">CTET Dean's Office</option>
                          <option value="CARS Dean's Office">CARS Dean's Office</option>
                          <option value="CoE Office">CoE Office</option>
                          <option value="SOM Dean's Office">SOM Dean's Office</option>
                          <option value="University Learning Resource Center Office (ULRC)">University Learning Resource Center Office (ULRC)</option>
                          <option value="Corporate Enterprise Development Unit Office (CEDU)">Corporate Enterprise Development Unit Office (CEDU)</option>
                          <option value="CTET Graduate School">CTET Graduate School</option>
                          <option value="CARS Graduate School">CARS Graduate School</option>
                        </select>

                        <!-- Text input for custom office (hidden initially) -->
                        <div id="officeInputContainer" class="hidden">
                          <input type="text" class="form-control" id="officeInput" name="customOffice" placeholder="Enter your office">
                        </div>

                        <!-- Back to dropdown icon button (hidden initially) -->
                        <button type="button" id="backToDropdown" class="hidden ">
                          <i class='fa fa-undo'></i>
                        </button>
                      </div>

                      <div class="col-md-2 mb-3">
                        <label for="role" class="form-label">Role</label>
                        <input type="text" class="form-control" id="role" name="role" placeholder="Role" />
                      </div>
                    </div>

                    <!-- Address Fields -->
                    <h5>Current Address</h5>
                    <div class="row">
                      <!-- Region Input -->
                      <div class="col-md-2 mb-3">
                        <label for="region" class="form-label">Region</label>
                        <select class="form-select form-control" id="region" name="region" placeholder="Enter Region">
                          <option selected disabled>Select Region</option>
                          <option value="Region XI">Region XI</option>
                          <option value="Region XII">Region XII</option>
                        </select>
                      </div>
                      <!-- Province Input -->
                      <div class="col-md-3 mb-3">
                        <label for="province" class="form-label">Province</label>
                        <select class="form-select form-control" id="province" name="province" placeholder="Enter Province">
                          <option selected disabled>Select Province</option>
                        </select>
                      </div>

                      <!-- Municipality Input -->
                      <div class="col-md-3 mb-3">
                        <label for="municipality" class="form-label">Municipality</label>
                        <select class="form-select form-control" id="municipality" name="municipality" placeholder="Enter Municipality">
                          <option selected disabled>Select Municipality</option>
                        </select>
                      </div>

                      <!-- Barangay Input -->
                      <div class="col-md-2 mb-3">
                        <label for="barangay" class="form-label">Barangay</label>
                        <select class="form-select form-control" id="barangay" name="barangay" placeholder="Enter Barangay">
                          <option selected disabled>Select Barangay</option>
                        </select>
                      </div>


                      <!-- Street Input (Text Field) -->
                      <div class="col-md-2 mb-3">
                        <label for="street" class="form-label">Purok/Block No./Street</label>
                        <input type="text" class="form-control" id="street" name="street" placeholder="Enter street address" />
                      </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" />
                      </div>
                      <div class="col-md-6 mb-3">
                        <label for="contactNumber" class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" id="contactNumber" name="contactNumber" placeholder="Contact number" />
                      </div>
                    </div>

                    <!-- Emergency Contact Information -->
                    <h5>Emergency Contact Information</h5>
                    <div class="row">
                      <div class="col-md-6 mb-3">
                        <label for="emergencyContactName" class="form-label">Emergency Contact Name</label>
                        <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" placeholder="Emergency contact name" />
                      </div>
                      <div class="col-md-3 mb-3">
                        <label for="relationship" class="form-label">Relationship</label>
                        <input type="text" class="form-control" id="relationship" name="relationship" placeholder="Relationship" />
                      </div>
                      <div class="col-md-3 mb-3">
                        <label for="emergencyContactNumber" class="form-label">Emergency Contact Number</label>
                        <input type="tel" class="form-control" id="emergencyContactNumber" name="emergencyContactNumber" placeholder="Emergency contact number" />
                      </div>
                    </div>

                    <div class="row">
                      <h5>Patient's Account Status</h5>
                      <div class="col-md-2 mb-3">
                        <label for="Status" class="form-label">Status</label>
                        <select class="form-select form-control" id="Status" name="Status">
                          <option selected disabled>Select Status</option>
                          <option value="Active">Active</option>
                          <option value="Inactive">Inactive</option>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary" id="editstaffpatient" name="editstaffpatient">
                          Save
                        </button>

                        <button type="button" class="btn btn-primary ms-3" id="canceladdpatient">
                          Back
                        </button>
                      </div>
                    </div>
          </form>
          <!-- End of Form -->
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

  <!-- jQuery Sparkline -->
  <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

  <!-- Bootstrap Notify -->
  <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

  <!-- Sweet Alert -->
  <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

  <!-- Kaiadmin JS -->
  <script src="../assets/js/kaiadmin.min.js"></script>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    $(document).ready(function() {

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
            window.location.href = "patient-staffedit.php";
          }
          <?php unset($_SESSION['status'], $_SESSION['message']); ?>
        });
      <?php endif; ?>

      confirmCancelPatient();

      var patientData = <?php echo json_encode($patientDetails); ?>;

      function populatePatientForm(data) {
        $('#lastName').val(data.patient.patient_lname || '');
        $('#firstName').val(data.patient.patient_fname || '');
        $('#middleName').val(data.patient.patient_mname || '');
        $('#dob').val(data.patient.patient_dob || '');
        $('#sex').val(data.patient.patient_sex || 'Male');
        $('#staffID').val(data.staff.staff_idnum || '');

        $('#Office').val(data.staff.staff_office || '');
        $('#role').val(data.staff.staff_role || '');
        $('#region').val(data.address.address_region || '').trigger('change');
        $('#province').val(data.address.address_province || '').trigger('change');
        $('#municipality').val(data.address.address_municipality || '').trigger('change');
        $('#barangay').val(data.address.address_barangay || '');
        $('#street').val(data.address.address_prkstrtadd || '');
        $('#email').val(data.patient.patient_email || '');
        $('#contactNumber').val(data.patient.patient_connum || '');
        $('#emergencyContactName').val(data.emergencyContact.emcon_conname || '');
        $('#relationship').val(data.emergencyContact.emcon_relationship || '');
        $('#emergencyContactNumber').val(data.emergencyContact.emcon_connum || '');
        $('#Status').val(data.patient.patient_status || '');
        $('#profilePic').attr('src', `uploads/${patientData.patient.patient_profile}` || 'default-image.jpg');

      }

      function confirmCancelPatient() {
        $('#canceladdpatient').click(function(event) {
          event.preventDefault();

          let isFormFilled = false;

          $('#uppatientForm input, staffForm select, staffForm textarea').each(function() {
            if ($(this).val() !== '') {
              isFormFilled = true;
              return false;
            }
          });

          if (isFormFilled) {
            Swal.fire({
              title: "Are you sure?",
              text: "Do you really want to cancel updating this patient? Unsaved information will be lost.",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes, cancel it!"
            }).then((result) => {
              if (result.isConfirmed) {
                sessionStorage.clear();
                window.location.href = "patient-record.php";
              }
            });
          } else {

            window.location.href = "patient-record.php";
          }
        });
      }
      let currentField = '';
      populatePatientForm(patientData);

      const addressOptions = {
        regions: {
          "Region XI": {
            provinces: {
              "Davao del Norte": {
                municipalities: ["Asuncion", "Carmen", "Kapalong", "New Corella", "City of Panabo", "Island Garden City of Samal", "Santo Tomas", "City of Tagum", "Talaingod", "Braulio E. Dujali", "San Isidro"],
                barangays: {
                  "Asuncion": ["Binancian", "Buan", "Buclad", "Cabaywa", "Camansa", "Cambanogoy (Pob.)", "Camoning", "Canatan", "Concepcion", "Do\u00f1a Andrea", "Magatos", "Napungas", "New Bantayan", "New Loon", "New Santiago", "Pamacaun", "Sagayen", "San Vicente", "Santa Filomena", "Sonlon"],
                  "Carmen": ["Alejal", "Anibongan", "Asuncion", "Cebulano", "Guadalupe", "Ising (Pob.)", "La Paz", "Mabaus", "Mabuhay", "Magsaysay", "Mangalcal", "Minda", "New Camiling", "Salvacion", "San Isidro", "Santo Ni\u00f1o", "Taba", "Tibulao", "Tubod", "Tuganay"],
                  "Kapalong": ["Capungagan", "Florida", "Gabuyan", "Gupitan", "Katipunan", "Luna", "Mabantao", "Mamacao", "Maniki", "Pag-asa", "Sampao", "Semong", "Sua-on", "Tiburcia"],
                  "New Corella": ["Cabidianan", "Carcor", "Del Monte", "Del Pilar", "El Salvador", "Limba-an", "Macgum", "Mambing", "Mesaoy", "New Bohol", "New Cortez", "New Sambog", "Patrocenio", "Poblacion", "San Jose", "San Roque", "Santa Cruz", "Santa Fe", "Santo Ni\u00f1o", "Suawon"],
                  "City of Panabo": ["A. O. Floirendo", "Buenavista", "Cacao", "Cagangohan", "Consolacion", "Dapco", "Datu Abdul Dadia", "Gredu (Pob.)", "J.P. Laurel", "Kasilak", "Katipunan", "Katualan", "Kauswagan", "Kiotoy", "Little Panay", "Lower Panaga", "Mabunao", "Maduao", "Malativas", "Manay", "Nanyo", "New Malaga", "New Malitbog", "New Pandan (Pob.)", "New Visayas", "Quezon", "Salvacion", "San Francisco (Pob.)", "San Nicolas", "San Pedro", "San Roque", "San Vicente", "Santa Cruz", "Santo Ni\u00f1o (Pob.)", "Sindaton", "Southern Davao", "Tagpore", "Tibungol", "Upper Licanan", "Waterfall"],
                  "Island Garden City of Samal": ["Adecor", "Anonang", "Aumbay", "Aundanao", "Balet", "Bandera", "Caliclic", "Camudmud", "Catagman", "Cawag", "Cogon", "Cogon (Talicod)", "Dadatan", "Del Monte", "Guilon", "Kanaan", "Kinawitnon", "Libertad", "Libuak", "Licup", "Limao", "Linosutan", "Mambago-A", "Mambago-B", "Miranda (Pob.)", "Moncado (Pob.)", "Pangubatan", "Pe\u00f1aplata (Pob.)", "Poblacion", "San Agustin", "San Antonio", "San Isidro (Babak)", "San Isidro (Kaputian)", "San Jose", "San Miguel", "San Remigio", "Santa Cruz", "Santo Ni\u00f1o", "Sion", "Tagbaobo", "Tagbay", "Tagbitan-ag", "Tagdaliao", "Tagpopongan", "Tambo", "Toril"],
                  "Santo Tomas": ["Balagunan", "Bobongon", "Casig-Ang", "Esperanza", "Kimamon", "Kinamayan", "La Libertad", "Lungaog", "Magwawa", "New Katipunan", "New Visayas", "Pantaron", "Salvacion", "San Jose", "San Miguel", "San Vicente", "Talomo", "Tibal-og (Pob.)", "Tulalian"],
                  "City of Tagum": ["Apokon", "Busaon", "Canocotan", "Cuambogan", "La Filipina", "Liboganon", "Madaum", "Magdum", "Magugpo East", "Magugpo North", "Magugpo Poblacion", "Magugpo South", "Magugpo West", "Mankilam", "New Balamban", "Nueva Fuerza", "Pagsabangan", "Pandapan", "San Agustin", "San Isidro", "San Miguel", "Visayan Village"],
                  "Talaingod": ["Dagohoy", "Palma Gil", "Santo Ni\u00f1o"],
                  "Braulio E. Dujali": ["Cabay-Angan", "Dujali", "Magupising", "New Casay", "Tanglaw"],
                  "San Isidro": ["Dacudao", "Datu Balong", "Igangon", "Kipalili", "Libuton", "Linao", "Mamangan", "Monte Dujali", "Pinamuno", "Sabangan", "San Miguel", "Santo Ni\u00f1o", "Sawata"]
                }
              },
              "Davao del Sur": {
                municipalities: ["Bansalan", "City of Davao", "City of Digos", "Hagonoy", "Kiblawan", "Magsaysay", "Malalag", "Matanao", "Padada", "Santa Cruz", "Sulop"],
                barangays: {
                  "Bansalan": ["Alegre", "Alta Vista", "Anonang", "Bitaug", "Bonifacio", "Buenavista", "Darapuay", "Dolo", "Eman", "Kinuskusan", "Libertad", "Linawan", "Mabuhay", "Mabunga", "Managa", "Marber", "New Clarin", "Poblacion", "Poblacion Dos", "Rizal", "Santo Ni\u00f1o", "Sibayan", "Tinongtongan", "Tubod", "Union"],
                  "City of Davao": ["Acacia", "Agdao", "Alambre", "Alejandra Navarro", "Alfonso Angliongto Sr.", "Angalan", "Atan-Awe", "Baganihan", "Bago Aplaya", "Bago Gallera", "Bago Oshiro", "Baguio (Pob.)", "Balengaeng", "Baliok", "Bangkas Heights", "Bantol", "Baracatan", "Barangay 1-A (Pob.)", "Barangay 10-A (Pob.)", "Barangay 11-B (Pob.)", "Barangay 12-B (Pob.)", "Barangay 13-B (Pob.)", "Barangay 14-B (Pob.)", "Barangay 15-B (Pob.)", "Barangay 16-B (Pob.)", "Barangay 17-B (Pob.)", "Barangay 18-B (Pob.)", "Barangay 19-B (Pob.)", "Barangay 2-A (Pob.)", "Barangay 20-B (Pob.)", "Barangay 21-C (Pob.)", "Barangay 22-C (Pob.)", "Barangay 23-C (Pob.)", "Barangay 24-C (Pob.)", "Barangay 25-C (Pob.)", "Barangay 26-C (Pob.)", "Barangay 27-C (Pob.)", "Barangay 28-C (Pob.)", "Barangay 29-C (Pob.)", "Barangay 3-A (Pob.)", "Barangay 30-C (Pob.)", "Barangay 31-D (Pob.)", "Barangay 32-D (Pob.)", "Barangay 33-D (Pob.)", "Barangay 34-D (Pob.)", "Barangay 35-D (Pob.)", "Barangay 36-D (Pob.)", "Barangay 37-D (Pob.)", "Barangay 38-D (Pob.)", "Barangay 39-D (Pob.)", "Barangay 4-A (Pob.)", "Barangay 40-D (Pob.)", "Barangay 5-A (Pob.)", "Barangay 6-A (Pob.)", "Barangay 7-A (Pob.)", "Barangay 8-A (Pob.)", "Barangay 9-A (Pob.)", "Bato", "Bayabas", "Biao Escuela", "Biao Guianga", "Biao Joaquin", "Binugao", "Bucana", "Buda", "Buhangin (Pob.)", "Bunawan (Pob.)", "Cabantian", "Cadalian", "Calinan (Pob.)", "Callawa", "Camansi", "Carmen", "Catalunan Grande", "Catalunan Peque\u00f1o", "Catigan", "Cawayan", "Centro", "Colosas", "Communal", "Crossing Bayabas", "Dacudao", "Dalag", "Dalagdag", "Daliao", "Daliaon Plantation", "Datu Salumay", "Dominga", "Dumoy", "Eden", "Fatima", "Gatungan", "Gov. Paciano Bangoy", "Gov. Vicente Duterte", "Gumalang", "Gumitan", "Ilang", "Inayangan", "Indangan", "Kap. Tomas Monteverde, Sr.", "Kilate", "Lacson", "Lamanan", "Lampianao", "Langub", "Lapu-lapu", "Leon Garcia, Sr.", "Lizada", "Los Amigos", "Lubogan", "Lumiad", "Ma-a", "Mabuhay", "Magsaysay", "Magtuod", "Mahayag", "Malabog", "Malagos", "Malamba", "Manambulan", "Mandug", "Manuel Guianga", "Mapula", "Marapangi", "Marilog", "Matina Aplaya", "Matina Biao", "Matina Crossing", "Matina Pangi", "Megkawayan", "Mintal", "Mudiang", "Mulig", "New Carmen", "New Valencia", "Pampanga", "Panacan", "Panalum", "Pandaitan", "Pangyan", "Paquibato (Pob.)", "Paradise Embak", "Rafael Castillo", "Riverside", "Salapawan", "Salaysay", "Saloy", "San Antonio", "San Isidro", "Santo Ni\u00f1o", "Sasa", "Sibulan", "Sirawan", "Sirib", "Suawan", "Subasta", "Sumimao", "Tacunan", "Tagakpan", "Tagluno", "Tagurano", "Talandang", "Talomo (Pob.)", "Talomo River", "Tamayong", "Tambobong", "Tamugan", "Tapak", "Tawan-tawan", "Tibuloy", "Tibungco", "Tigatto", "Toril (Pob.)", "Tugbok (Pob.)", "Tungakalan", "Ubalde", "Ula", "Vicente Hizon Sr.", "Waan", "Wangan", "Wilfredo Aquino", "Wines"],
                  "City of Digos": ["Aplaya", "Balabag", "Binaton", "Cogon", "Colorado", "Dawis", "Dulangan", "Goma", "Igpit", "Kapatagan", "Kiagot", "Lungag", "Mahayahay", "Matti", "Ruparan", "San Agustin", "San Jose", "San Miguel", "San Roque", "Sinawilan", "Soong", "Tiguman", "Tres De Mayo", "Zone 1 (Pob.)", "Zone 2 (Pob.)", "Zone 3 (Pob.)"],
                  "Hagonoy": ["Balutakay", "Clib", "Guihing", "Guihing Aplaya", "Hagonoy Crossing", "Kibuaya", "La Union", "Lanuro", "Lapulabao", "Leling", "Mahayahay", "Malabang Damsite", "Maliit Digos", "New Quezon", "Paligue", "Poblacion", "Sacub", "San Guillermo", "San Isidro", "Sinayawan", "Tologan"],
                  "Kiblawan": ["Abnate", "Bagong Negros", "Bagong Silang", "Bagumbayan", "Balasiao", "Bonifacio", "Bulol-Salo", "Bunot", "Cogon-Bacaca", "Dapok", "Ihan", "Kibongbong", "Kimlawis", "Kisulan", "Lati-an", "Manual", "Maraga-a", "Molopolo", "New Sibonga", "Panaglib", "Pasig", "Poblacion", "Pocaleel", "San Isidro", "San Jose", "San Pedro", "Santo Ni\u00f1o", "Tacub", "Tacul", "Waterfall"],
                  "Magsaysay": ["Bacungan", "Balnate", "Barayong", "Blocon", "Dalawinon", "Dalumay", "Glamang", "Kanapulo", "Kasuga", "Lower Bala", "Mabini", "Maibo", "Malawanit", "Malongon", "New Ilocos", "New Opon", "Poblacion", "San Isidro", "San Miguel", "Tacul", "Tagaytay", "Upper Bala"],
                  "Malalag": ["Bagumbayan", "Baybay", "Bolton", "Bulacan", "Caputian", "Ibo", "Kiblagon", "Lapu-Lapu", "Mabini", "New Baclayon", "Pitu", "Poblacion", "Rizal", "San Isidro", "Tagansule"],
                  "Matanao": ["Asbang", "Asinan", "Bagumbayan", "Bangkal", "Buas", "Buri", "Cabligan", "Camanchiles", "Ceboza", "Colonsabak", "Dongan-Pekong", "Kabasagan", "Kapok", "Kauswagan", "Kibao", "La Suerte", "Langa-an", "Lower Marber", "Manga", "New Katipunan", "New Murcia", "New Visayas", "Poblacion", "Saboy", "San Jose", "San Miguel", "San Vicente", "Saub", "Sinaragan", "Sinawilan", "Tamlangon", "Tibongbong", "Towak"],
                  "Padada": ["Almendras (Pob.)", "Don Sergio Osme\u00f1a, Sr.", "Harada Butai", "Lower Katipunan", "Lower Limonzo", "Lower Malinao", "N C Ordaneza District (Pob.)", "Northern Paligue", "Palili", "Piape", "Punta Piape", "Quirino District (Pob.)", "San Isidro", "Southern Paligue", "Tulogan", "Upper Limonzo", "Upper Malinao"],
                  "Santa Cruz": ["Astorga", "Bato", "Coronon", "Darong", "Inawayan", "Jose Rizal", "Matutungan", "Melilia", "Saliducon", "Sibulan", "Sinoron", "Tagabuli", "Tibolo", "Tuban", "Zone I (Pob.)", "Zone II (Pob.)", "Zone III (Pob.)", "Zone IV (Pob.)"],
                  "Sulop": ["Balasinon", "Buguis", "Carre", "Clib", "Harada Butai", "Katipunan", "Kiblagon", "Labon", "Laperas", "Lapla", "Litos", "Luparan", "Mckinley", "New Cebu", "Osme\u00f1a", "Palili", "Parame", "Poblacion", "Roxas", "Solongvale", "Tagolilong", "Tala-o", "Talas", "Tanwalang", "Waterfall"]
                }
              },
              "Davao Oriental": {
                municipalities: ["Baganga", "Banaybanay", "Boston", "Caraga", "Cateel", "Governor Generoso", "Lupon", "Manay", "City of Mati", "San Isidro", "Tarragona"],
                barangays: {
                  "Baganga": ["Baculin", "Banao", "Batawan", "Batiano", "Binondo", "Bobonao", "Campawan", "Central (Pob.)", "Dapnan", "Kinablangan", "Lambajon", "Lucod", "Mahanub", "Mikit", "Salingcomot", "San Isidro", "San Victor", "Saoquegue"],
                  "Banaybanay": ["Cabangcalan", "Caganganan", "Calubihan", "Causwagan", "Mahayag", "Maputi", "Mogbongcogon", "Panikian", "Pintatagan", "Piso Proper", "Poblacion", "Punta Linao", "Rang-ay", "San Vicente"],
                  "Boston": ["Caatihan", "Cabasagan", "Carmen", "Cawayanan", "Poblacion", "San Jose", "Sibajay", "Simulao"],
                  "Caraga": ["Alvar", "Caningag", "Don Leon Balante", "Lamiawan", "Manorigao", "Mercedes", "Palma Gil", "Pichon", "Poblacion", "San Antonio", "San Jose", "San Luis", "San Miguel", "San Pedro", "Santa Fe", "Santiago", "Sobrecarey"],
                  "Cateel": ["Abijod", "Alegria", "Aliwagwag", "Aragon", "Baybay", "Maglahus", "Mainit", "Malibago", "Poblacion", "San Alfonso", "San Antonio", "San Miguel", "San Rafael", "San Vicente", "Santa Filomena", "Taytayan"],
                  "Governor Generoso": ["Anitap", "Crispin Dela Cruz", "Don Aurelio Chicote", "Lavigan", "Luzon", "Magdug", "Manuel Roxas", "Monserrat", "Nangan", "Oregon", "Poblacion", "Pundaguitan", "Sergio Osme\u00f1a", "Surop", "Tagabebe", "Tamban", "Tandang Sora", "Tibanban", "Tiblawan", "Upper Tibanban"],
                  "Lupon": ["Bagumbayan", "Cabadiangan", "Calapagan", "Cocornon", "Corporacion", "Don Mariano Marcos", "Ilangay", "Langka", "Lantawan", "Limbahan", "Macangao", "Magsaysay", "Mahayahay", "Maragatas", "Marayag", "New Visayas", "Poblacion", "San Isidro", "San Jose", "Tagboa", "Tagugpo"],
                  "Manay": ["Capasnan", "Cayawan", "Central (Pob.)", "Concepcion", "Del Pilar", "Guza", "Holy Cross", "Lambog", "Mabini", "Manreza", "New Taokanga", "Old Macopa", "Rizal", "San Fermin", "San Ignacio", "San Isidro", "Zaragosa"],
                  "City of Mati": ["Badas", "Bobon", "Buso", "Cabuaya", "Central (Pob.)", "Culian", "Dahican", "Danao", "Dawan", "Don Enrique Lopez", "Don Martin Marundan", "Don Salvador Lopez, Sr.", "Langka", "Lawigan", "Libudon", "Luban", "Macambol", "Mamali", "Matiao", "Mayo", "Sainz", "Sanghay", "Tagabakid", "Tagbinonga", "Taguibo", "Tamisan"],
                  "San Isidro": ["Baon", "Batobato (Pob.)", "Bitaogan", "Cambaleon", "Dugmanon", "Iba", "La Union", "Lapu-lapu", "Maag", "Manikling", "Maputi", "San Miguel", "San Roque", "Santo Rosario", "Sudlon", "Talisay"],
                  "Tarragona": ["Cabagayan", "Central (Pob.)", "Dadong", "Jovellar", "Limot", "Lucatan", "Maganda", "Ompao", "Tomoaong", "Tubaon"],
                }
              },
              "Davao de Oro": {
                municipalities: ["Compostela", "Laak", "Mabini", "Maco", "Maragusan", "Mawab", "Monkayo", "Montevista", "Nabunturan", "New Bataan", "Pantukan"],
                barangays: {
                  "Compostela": ["Aurora", "Bagongon", "Gabi", "Lagab", "Mangayon", "Mapaca", "Maparat", "New Alegria", "Ngan", "Osme\u00f1a", "Panansalan", "Poblacion", "San Jose", "San Miguel", "Siocon", "Tamia"],
                  "Laak": ["Aguinaldo", "Amor Cruz", "Ampawid", "Andap", "Anitap", "Bagong Silang", "Banbanon", "Belmonte", "Binasbas", "Bullucan", "Cebulida", "Concepcion", "Datu Ampunan", "Datu Davao", "Do\u00f1a Josefa", "El Katipunan", "Il Papa", "Imelda", "Inacayan", "Kaligutan", "Kapatagan", "Kidawa", "Kilagding", "Kiokmay", "Laac (Pob.)", "Langtud", "Longanapan", "Mabuhay", "Macopa", "Malinao", "Mangloy", "Melale", "Naga", "New Bethlehem", "Panamoren", "Sabud", "San Antonio", "Santa Emilia", "Santo Ni\u00f1o", "Sisimon"],
                  "Mabini": ["Anitapan", "Cabuyuan", "Cadunan", "Cuambog (Pob.)", "Del Pilar", "Golden Valley", "Libodon", "Pangibiran", "Pindasan", "San Antonio", "Tagnanan"],
                  "Maco": ["Anibongan", "Anislagan", "Binuangan", "Bucana", "Calabcab", "Concepcion", "Dumlan", "Elizalde", "Gubatan", "Hijo", "Kinuban", "Langgam", "Lapu-lapu", "Libay-libay", "Limbo", "Lumatab", "Magangit", "Mainit", "Malamodao", "Manipongol", "Mapaang", "Masara", "New Asturias", "New Barili", "New Leyte", "New Visayas", "Panangan", "Pangi", "Panibasan", "Panoraon", "Poblacion", "San Juan", "San Roque", "Sangab", "Tagbaros", "Taglawig", "Teresa"],
                  "Maragusan": ["Bagong Silang", "Bahi", "Cambagang", "Coronobe", "Katipunan", "Lahi", "Langgawisan", "Mabugnao", "Magcagong", "Mahayahay", "Mapawa", "Maragusan (Pob.)", "Mauswagon", "New Albay", "New Katipunan", "New Man-ay", "New Panay", "Paloc", "Pamintaran", "Parasanon", "Talian", "Tandik", "Tigbao", "Tupaz"],
                  "Mawab": ["Andili", "Bawani", "Concepcion", "Malinawon", "Nueva Visayas", "Nuevo Iloco", "Poblacion", "Salvacion", "Saosao", "Sawangan", "Tuboran"],
                  "Monkayo": ["Awao", "Babag", "Banlag", "Baylo", "Casoon", "Haguimitan", "Inambatan", "Macopa", "Mamunga", "Mount Diwata", "Naboc", "Olaycon", "Pasian", "Poblacion", "Rizal", "Salvacion", "San Isidro", "San Jose", "Tubo-tubo", "Union", "Upper Ulip"],
                  "Montevista": ["Banagbanag", "Banglasan", "Bankerohan Norte", "Bankerohan Sur", "Camansi", "Camantangan", "Canidkid", "Concepcion", "Dauman", "Lebanon", "Linoan", "Mayaon", "New Calape", "New Cebulan", "New Dalaguete", "New Visayas", "Prosperidad", "San Jose (Pob.)", "San Vicente", "Tapia"],
                  "Nabunturan": ["Anislagan", "Antiquera", "Basak", "Bayabas", "Bukal", "Cabacungan", "Cabidianan", "Katipunan", "Libasan", "Linda", "Magading", "Magsaysay", "Mainit", "Manat", "Matilo", "Mipangi", "New Dauis", "New Sibonga", "Ogao", "Pangutosan", "Poblacion", "San Isidro", "San Roque", "San Vicente", "Santa Maria", "Santo Ni\u00f1o", "Sasa", "Tagnocon"],
                  "New Bataan": ["Andap", "Bantacan", "Batinao", "Cabinuangan (Pob.)", "Camanlangan", "Cogonon", "Fatima", "Kahayag", "Katipunan", "Magangit", "Magsaysay", "Manurigao", "Pagsabangan", "Panag", "San Roque", "Tandawan"],
                  "Pantukan": ["Araibo", "Bongabong", "Bongbong", "Kingking (Pob.)", "Las Arenas", "Magnaga", "Matiao", "Napnapan", "P. Fuentes", "Tag-Ugpo", "Tagdangua", "Tambongon", "Tibagon"]
                }
              },
              "Davao Occidental": {
                municipalities: ["Don Marcelino", "Jose Abad Santos", "Malita", "Santa Maria", "Sarangani"],
                barangays: {
                  "Don Marcelino": ["Baluntaya", "Calian", "Dalupan", "Kinanga", "Kiobog", "Lanao", "Lapuan", "Lawa (Pob.)", "Linadasan", "Mabuhay", "North Lamidan", "Nueva Villa", "South Lamidan", "Talagutong (Pob.)", "West Lamidan"],
                  "Jose Abad Santos": ["Balangonan", "Buguis", "Bukid", "Butuan", "Butulan", "Caburan Big", "Caburan Small (Pob.)", "Camalian", "Carahayan", "Cayaponga", "Culaman", "Kalbay", "Kitayo", "Magulibas", "Malalan", "Mangile", "Marabutuan", "Meybio", "Molmol", "Nuing", "Patulang", "Quiapo", "San Isidro", "Sugal", "Tabayon", "Tanuman"],
                  "Malita": ["Bito", "Bolila", "Buhangin", "Culaman", "Datu Danwata", "Demoloc", "Felis", "Fishing Village", "Kibalatong", "Kidalapong", "Kilalag", "Kinangan", "Lacaron", "Lagumit", "Lais", "Little Baguio", "Macol", "Mana", "Manuel Peralta", "New Argao", "Pangaleon", "Pangian", "Pinalpalan", "Poblacion", "Sangay", "Talogoy", "Tical", "Ticulon", "Tingolo", "Tubalan"],
                  "Santa Maria": ["Basiawan", "Buca", "Cadaatan", "Datu Daligasao", "Datu Intan", "Kidadan", "Kinilidan", "Kisulad", "Malalag Tubig", "Mamacao", "Ogpao", "Poblacion", "Pongpong", "San Agustin", "San Antonio", "San Isidro", "San Juan", "San Pedro", "San Roque", "Santo Ni\u00f1o", "Santo Rosario", "Tanglad"],
                  "Sarangani": ["Batuganding", "Camahual", "Camalig", "Gomtago", "Konel", "Laker", "Lipol", "Mabila (Pob.)", "Patuco", "Tagen", "Tinina", "Tucal"]
                }
              },
            }
          },
          "Region XII": {
            provinces: {
              "Cotabato": {
                municipalities: ["Alamada", "Carmen", "Kabacan", "City of Kidapawan", "Libungan", "Magpet", "Makilala", "Matalam", "Midsayap", "M'Lang", "Pigkawayan", "Pikit", "President Roxas", "Tulunan", "Antipas", "Banisilan", "Aleosan", "Arakan"],
                barangays: {
                  "Alamada": ["Bao", "Barangiran", "Camansi", "Dado", "Guiling", "Kitacubong (Pob.)", "Lower Dado", "Macabasa", "Malitubog", "Mapurok", "Mirasol", "Pacao", "Paruayan", "Pigcawaran", "Polayagan", "Rangayen", "Raradangan"],
                  "Carmen": ["Aroman", "Bentangan", "Cadiis", "General Luna", "Katanayanan", "Kib-Ayao", "Kibenes", "Kibugtongan", "Kilala", "Kimadzil", "Kitulaan", "Langogan", "Lanoon", "Liliongan", "Macabenban", "Malapag", "Manarapan", "Manili", "Nasapian", "Palanggalan", "Pebpoloan", "Poblacion", "Ranzo", "Tacupan", "Tambad", "Tonganon", "Tupig", "Ugalingan"],
                  "Kabacan": ["Aringay", "Bangilan", "Bannawag", "Buluan", "Cuyapon", "Dagupan", "Katidtuan", "Kayaga", "Kilagasan", "Magatos", "Malamote", "Malanduague", "Nanga-an", "Osias", "Paatan Lower", "Paatan Upper", "Pedtad", "Pisan", "Poblacion", "Salapungan", "Sanggadong", "Simbuhay", "Simone", "Tamped"],
                  "City of Kidapawan": ["Amas", "Amazion", "Balabag", "Balindog", "Benoligan", "Berada", "Gayola", "Ginatilan", "Ilomavis", "Indangan", "Junction", "Kalaisan", "Kalasuyan", "Katipunan", "Lanao", "Linangcob", "Luvimin", "Macabolig", "Magsaysay", "Malinan", "Manongol", "Marbel", "Mateo", "Meochao", "Mua-an", "New Bohol", "Nuangan", "Onica", "Paco", "Patadon", "Perez", "Poblacion", "San Isidro", "San Roque", "Santo Ni\u00f1o", "Sibawan", "Sikitan", "Singao", "Sudapin", "Sumbao"],
                  "Libungan": ["Abaga", "Baguer", "Barongis", "Batiocan", "Cabaruyan", "Cabpangi", "Demapaco", "Grebona", "Gumaga", "Kapayawi", "Kiloyao", "Kitubod", "Malengen", "Montay", "Nica-an", "Palao", "Poblacion", "Sinapangan", "Sinawingan", "Ulamian"],
                  "Magpet": ["Alibayon", "Amabel", "Bagumbayan", "Balete", "Bangkal", "Bantac", "Basak", "Binay", "Bongolanon", "Datu Celo", "Del Pilar", "Doles", "Don Panaca", "Gubatan", "Ilian", "Imamaling", "Inac", "Kamada", "Kauswagan", "Kinarum", "Kisandal", "Magcaalam", "Mahongcog", "Manobisa", "Manobo", "Noa", "Owas", "Pangao-an", "Poblacion", "Sallab", "Tagbac", "Temporan"],
                  "Makilala": ["Batasan", "Bato", "Biangan", "Buena Vida", "Buhay", "Bulakanon", "Cabilao", "Concepcion", "Dagupan", "Garsika", "Guangan", "Indangan", "Jose Rizal", "Katipunan II", "Kawayanon", "Kisante", "Leboce", "Libertad", "Luayon", "Luna Norte", "Luna Sur", "Malabuan", "Malasila", "Malungon", "New Baguio", "New Bulatukan", "New Cebu", "New Israel", "Old Bulatukan", "Poblacion", "Rodero", "Saguing", "San Vicente", "Santa Felomina", "Santo Ni\u00f1o", "Sinkatulan", "Taluntalunan", "Villaflores"],
                  "Matalam": ["Arakan", "Bangbang", "Bato", "Central Malamote", "Dalapitan", "Estado", "Ilian", "Kabulacan", "Kibia", "Kibudoc", "Kidama", "Kilada", "Lampayan", "Latagan", "Linao", "Lower Malamote", "Manubuan", "Manupal", "Marbel", "Minamaing", "Natutungan", "New Abra", "New Alimodian", "New Bugasong", "New Pandan", "Patadon West", "Pinamaton", "Poblacion", "Salvacion", "Santa Maria", "Sarayan", "Taculen", "Taguranao", "Tamped"],
                  "Midsayap": ["Agriculture", "Anonang", "Arizona", "Bagumba", "Baliki", "Barangay Poblacion 1", "Barangay Poblacion 2", "Barangay Poblacion 3", "Barangay Poblacion 4", "Barangay Poblacion 5", "Barangay Poblacion 6", "Barangay Poblacion 7", "Barangay Poblacion 8", "Bitoka", "Bual Norte", "Bual Sur", "Bulanan Upper", "Central Bulanan", "Central Glad", "Central Katingawan", "Central Labas", "Damatulan", "Ilbocean", "Kadigasan", "Kadingilan", "Kapinpilan", "Kimagango", "Kiwanan", "Kudarangan", "Lagumbingan", "Lomopog", "Lower Glad", "Lower Katingawan", "Macasendeg", "Malamote", "Malingao", "Milaya", "Mudseng", "Nabalawag", "Nalin", "Nes", "Olandang", "Palongoguen", "Patindeguen", "Rangaban", "Sadaan", "Salunayan", "Sambulawan", "San Isidro", "San Pedro", "Santa Cruz", "Tugal", "Tumbras", "Upper Glad I", "Upper Glad II", "Upper Labas", "Villarica"],
                  "M'Lang": ["Bagontapay", "Bialong", "Buayan", "Calunasan", "Dagong", "Dalipe", "Dungo-an", "Gaunan", "Inas", "Katipunan", "La Fortuna", "La Suerte", "Langkong", "Lepaga", "Liboo", "Lika", "Luz Village", "Magallon", "Malayan", "New Antique", "New Barbaza", "New Consolacion", "New Esperanza", "New Janiuay", "New Kalibo", "New Lawa-an", "New Rizal", "Nueva Vida", "Pag-asa", "Palma-Perez", "Poblacion", "Poblacion B", "Pulang-lupa", "Sangat", "Tawantawan", "Tibao", "Ugpay"],
                  "Pigkawayan": ["Anick", "Balacayon", "Balogo", "Banucagon", "Buluan", "Bulucaon", "Buricain", "Cabpangi", "Capayuran", "Central Panatan", "Datu Binasing", "Datu Mantil", "Kadingilan", "Kimarayang", "Libungan Torreta", "Lower Baguer", "Lower Pangangkalan", "Malagakit", "Maluao", "Matilac", "Midpapan I", "Midpapan II", "Mulok", "New Culasi", "New Igbaras", "New Panay", "North Manuangan", "Patot", "Payong-payong", "Poblacion I", "Poblacion II", "Poblacion III", "Presbitero", "Renibon", "Simsiman", "South Manuangan", "Tigbawan", "Tubon", "Upper Baguer", "Upper Pangangkalan"],
                  "Pikit": ["Bagoaingud", "Balabak", "Balatican", "Balong", "Balungis", "Barungis", "Batulawan", "Bualan", "Buliok", "Bulod", "Bulol", "Calawag", "Dalingaoen", "Damalasak", "Fort Pikit", "Ginatilan", "Gligli", "Gokoton", "Inug-ug", "Kabasalan", "Kalacacan", "Katilacan", "Kolambog", "Ladtingan", "Lagunde", "Langayen", "Macabual", "Macasendeg", "Manaulanan", "Nabundas", "Nalapaan", "Nunguan", "Paidu Pulangi", "Pamalian", "Panicupan", "Poblacion", "Punol", "Rajah Muda", "Silik", "Takipan", "Talitay", "Tinutulan"],
                  "President Roxas": ["Alegria", "Bato-bato", "Cabangbangan", "Camasi", "Datu Indang", "Datu Sandongan", "Del Carmen", "F. Cajelo", "Greenhill", "Idaoman", "Ilustre", "Kamarahan", "Kimaruhing", "Kisupaan", "La Esperanza", "Labu-o", "Lamalama", "Lomonay", "Mabuhay", "New Cebu", "Poblacion", "Sagcungan", "Salat", "Sarayan", "Tuael"],
                  "Tulunan": ["Bagumbayan", "Banayal", "Batang", "Bituan", "Bual", "Bunawan", "Daig", "Damawato", "Dungos", "F. Cajelo", "Galidan", "Genoveva Baynosa", "Kanibong", "La Esperanza", "Lampagang", "Magbok", "Maybula", "Minapan", "Nabundasan", "New Caridad", "New Culasi", "New Panay", "Paraiso", "Poblacion", "Popoyon", "Sibsib", "Tambac", "Tuburan"],
                  "Antipas": ["B. Cadungon", "Camutan", "Canaan", "Datu Agod", "Dolores", "Kiyaab", "Luhong", "Magsaysay", "Malangag", "Malatad", "Malire", "New Pontevedra", "Poblacion"],
                  "Banisilan": ["Banisilan Poblacion", "Busaon", "Capayangan", "Carugmanan", "Gastay", "Kalawaig", "Kiaring", "Malagap", "Malinao", "Miguel Macasarte", "Pantar", "Paradise", "Pinamulaan", "Poblacion II", "Puting-bato", "Solama", "Thailand", "Tinimbacan", "Tumbao-Camalig", "Wadya"],
                  "Aleosan": ["Bagolibas", "Cawilihan", "Dualing", "Dunguan", "Katalicanan", "Lawili", "Lower Mingading", "Luanan", "Malapang", "New Leon", "New Panay", "Pagangan", "Palacat", "Pentil", "San Mateo", "Santa Cruz", "Tapodoc", "Tomado", "Upper Mingading"],
                  "Arakan": ["Allab", "Anapolon", "Badiangon", "Binoongan", "Dallag", "Datu Ladayon", "Datu Matangkil", "Doroluman", "Gambodes", "Ganatan", "Greenfield", "Kabalantian", "Katipunan", "Kinawayan", "Kulaman Valley", "Lanao Kuran", "Libertad", "Makalangot", "Malibatuan", "Maria Caridad", "Meocan", "Naje", "Napalico", "Salasang", "San Miguel", "Santo Ni\u00f1o", "Sumalili", "Tumanding"]
                }
              },
              "South Cotabato": {
                municipalities: ["Banga", "City of General Santos", "City of Koronadal", "Norala", "Polomolok", "Surallah", "Tampakan", "Tantangan", "T'Boli", "Tupi", "Santo Ni\u00f1o", "Lake Sebu"],
                barangays: {
                  "Banga": ["Benitez (Pob.)", "Cabudian", "Cabuling", "Cinco", "Derilon", "El Nonok", "Improgo Village (Pob.)", "Kusan", "Lam-Apos", "Lamba", "Lambingi", "Lampari", "Liwanay", "Malaya", "Punong Grande", "Rang-ay", "Reyes (Pob.)", "Rizal", "Rizal Poblacion", "San Jose", "San Vicente", "Yangco Poblacion"],
                  "City of General Santos": ["Apopong", "Baluan", "Batomelong", "Buayan", "Bula", "Calumpang", "City Heights", "Conel", "Dadiangas East (Pob.)", "Dadiangas North", "Dadiangas South", "Dadiangas West", "Fatima", "Katangawan", "Labangal", "Lagao", "Ligaya", "Mabuhay", "Olympog", "San Isidro", "San Jose", "Siguel", "Sinawal", "Tambler", "Tinagacan", "Upper Labay"],
                  "City of Koronadal": ["Assumption", "Avance\u00f1a", "Cacub", "Caloocan", "Carpenter Hill", "Concepcion", "Esperanza", "General Paulino Santos", "Mabini", "Magsaysay", "Mambucal", "Morales", "Namnama", "New Pangasinan", "Paraiso", "Rotonda", "San Isidro", "San Jose", "San Roque", "Santa Cruz", "Santo Ni\u00f1o", "Sarabia", "Zone I (Pob.)", "Zone II (Pob.)", "Zone III (Pob.)", "Zone IV (Pob.)", "Zulueta"],
                  "Norala": ["Benigno Aquino, Jr.", "Dumaguil", "Esperanza", "Kibid", "Lapuz", "Liberty", "Lopez Jaena", "Matapol", "Poblacion", "Puti", "San Jose", "San Miguel", "Simsiman", "Tinago"],
                  "Polomolok": ["Bentung", "Cannery Site", "Crossing Palkan", "Glamang", "Kinilis", "Klinan 6", "Koronadal Proper", "Lam-Caliaf", "Landan", "Lapu", "Lumakil", "Magsaysay", "Maligo", "Pagalungan", "Palkan", "Poblacion", "Polo", "Rubber", "Silway 7", "Silway 8", "Sulit", "Sumbakil", "Upper Klinan"],
                  "Surallah": ["Buenavista", "Canahay", "Centrala", "Colongulo", "Dajay", "Duengas", "Lambontong", "Lamian", "Lamsugod", "Libertad (Pob.)", "Little Baguio", "Moloy", "Naci", "Talahik", "Tubiala", "Upper Sepaka", "Veterans"],
                  "Tampakan": ["Albagan", "Buto", "Danlag", "Kipalbig", "Lambayong", "Lampitak", "Liberty", "Maltana", "Palo", "Poblacion", "Pula-bato", "San Isidro", "Santa Cruz", "Tablu"],
                  "Tantangan": ["Bukay Pait", "Cabuling", "Dumadalig", "Libas", "Magon", "Maibo", "Mangilala", "New Cuyapo", "New Iloilo", "New Lambunao", "Poblacion", "San Felipe", "Tinongcop"],
                  "T'Boli": ["Aflek", "Afus", "Basag", "Datal Bob", "Desawo", "Dlanag", "Edwards (Pob.)", "Kematu", "Laconon", "Lambangan", "Lambuling", "Lamhako", "Lamsalome", "Lemsnolon", "Maan", "Malugong", "Mongocayo", "New Dumangas", "Poblacion", "Salacafe", "Sinolon", "T'bolok", "Talcon", "Talufo", "Tudok"],
                  "Tupi": ["Acmonan", "Bololmala", "Bunao", "Cebuano", "Crossing Rubber", "Kablon", "Kalkam", "Linan", "Lunen", "Miasong", "Palian", "Poblacion", "Polonuling", "Simbo", "Tubeng"],
                  "Santo Ni\u00f1o": ["Ambalgan", "Guinsang-an", "Katipunan", "Manuel Roxas", "Panay", "Poblacion", "Sajaneba", "San Isidro", "San Vicente", "Teresita"],
                  "Lake Sebu": ["Bacdulong", "Denlag", "Halilan", "Hanoon", "Klubi", "Lake Lahit", "Lamcade", "Lamdalag", "Lamfugon", "Lamlahak", "Lower Maculan", "Luhib", "Ned", "Poblacion", "Siluton", "Takunel", "Talisay", "Tasiman", "Upper Maculan"]
                }
              },
              "Sultan Kudarat": {
                municipalities: ["Bagumbayan", "Columbio", "Esperanza", "Isulan", "Kalamansig", "Lebak", "Lutayan", "Lambayong", "Palimbang", "President Quirino", "City of Tacurong", "Sen. Ninoy Aquino"],
                barangays: {
                  "Bagumbayan": ["Bai Sarifinang", "Biwang", "Busok", "Chua", "Daguma", "Daluga", "Kabulanan", "Kanulay", "Kapaya", "Kinayao", "Masiag", "Monteverde", "Poblacion", "Santo Ni\u00f1o", "Sison", "South Sepaka", "Sumilil", "Titulok", "Tuka"],
                  "Columbio": ["Bantangan", "Datablao", "Eday", "Elbebe", "Lasak", "Libertad", "Lomoyon", "Makat", "Maligaya", "Mayo", "Natividad", "Poblacion", "Polomolok", "Sinapulan", "Sucob", "Telafas"],
                  "Esperanza": ["Ala", "Daladap", "Dukay", "Guiamalia", "Ilian", "Kangkong", "Laguinding", "Magsaysay", "Margues", "New Panay", "Numo", "Paitan", "Pamantingan", "Poblacion", "Sagasa", "Salabaca", "Saliao", "Salumping", "Villamor"],
                  "Isulan": ["Bambad", "Bual", "D'Lotilla", "Dansuli", "Impao", "Kalawag I (Pob.)", "Kalawag II (Pob.)", "Kalawag III (Pob.)", "Kenram", "Kolambog", "Kudanding", "Lagandang", "Laguilayan", "Mapantig", "New Pangasinan", "Sampao", "Tayugo"],
                  "Kalamansig": ["Bantogon", "Cadiz", "Datu Ito Andong", "Datu Wasay", "Dumangas Nuevo", "Hinalaan", "Limulan", "Nalilidan", "Obial", "Pag-asa", "Paril", "Poblacion", "Sabanal", "Sangay", "Santa Maria"],
                  "Lebak": ["Aurelio F. Freires", "Barurao", "Barurao II", "Basak", "Bolebok", "Bululawan", "Capilan", "Christiannuevo", "Datu Karon", "Kalamongog", "Keytodac", "Kinodalan", "New Calinog", "Nuling", "Pansud", "Pasandalan", "Poblacion", "Poblacion III", "Poloy-poloy", "Purikay", "Ragandang", "Salaman", "Salangsang", "Taguisa", "Tibpuan", "Tran", "Villamonte"],
                  "Lutayan": ["Antong", "Bayasong", "Blingkong", "Lutayan Proper", "Maindang", "Mamali", "Manili", "Palavilla", "Sampao", "Sisiman", "Tamnag (Pob.)"],
                  "Lambayong": ["Caridad", "Didtaras", "Gansing", "Kabulakan", "Kapingkong", "Katitisan", "Lagao", "Lilit", "Madanding", "Maligaya", "Mamali", "Matiompong", "Midtapok", "New Cebu", "Palumbi", "Pidtiguian", "Pimbalayan", "Pinguiaman", "Poblacion", "Sadsalan", "Seneben", "Sigayan", "Tambak", "Tinumigues", "Tumiao", "Udtong"],
                  "Palimbang": ["Akol", "Badiangon", "Baliango", "Balwan", "Bambanen", "Baranayan", "Barongis", "Batang-baglas", "Butril", "Colobe", "Datu Maguiales", "Domolol", "Kabuling", "Kalibuhan", "Kanipaan", "Kidayan", "Kiponget", "Kisek", "Kraan", "Kulong-kulong", "Langali", "Libua", "Ligao", "Lopoken", "Lumitan", "Maganao", "Maguid", "Malatuneng", "Malisbong", "Medol", "Milbuk", "Mina", "Molon", "Namat Masla", "Napnapon", "Poblacion", "San Roque", "Tibuhol", "Wal", "Wasag"],
                  "President Quirino": ["Bagumbayan", "Bannawag", "Bayawa", "C. Mangilala", "Estrella", "Kalanawe I", "Kalanawe II", "Katico", "Malingon", "Mangalen", "Pedtubo", "Poblacion", "Romualdez", "San Jose", "San Pedro", "Sinakulay", "Suben", "Tinaungan", "Tual"],
                  "City of Tacurong": ["Baras", "Buenaflor", "Calean", "Carmen", "D'Ledesma", "Enrique JC Montilla", "Kalandagan", "Lancheta", "New Isabela", "New Lagao", "New Passi", "Poblacion", "Rajah Nuda", "San Antonio", "San Emmanuel", "San Pablo", "San Rafael", "Tina", "Upper Katungal", "Virginia Gri\u00f1o"],
                  "Sen. Ninoy Aquino": ["Banali", "Basag", "Buenaflores", "Bugso", "Buklod", "Gapok", "Kadi", "Kapatagan", "Kiadsam", "Kuden", "Kulaman", "Lagubang", "Langgal", "Limuhay", "Malegdeg", "Midtungok", "Nati", "Sewod", "Tacupis", "Tinalon"]
                }
              },
              "Sarangani": {
                municipalities: ["Alabel", "Glan", "Kiamba", "Maasim", "Maitum", "Malapatan", "Malungon", "Cotabato City"],
                barangays: {
                  "Alabel": ["Alegria", "Bagacay", "Baluntay", "Datal Anggas", "Domolok", "Kawas", "Ladol", "Maribulan", "Pag-Asa", "Paraiso", "Poblacion", "Spring", "Tokawal"],
                  "Glan": ["Baliton", "Batotuling", "Batulaki", "Big Margus", "Burias", "Cablalan", "Calabanit", "Calpidong", "Congan", "Cross", "Datalbukay", "E. Alegado", "Glan Padidu", "Gumasa", "Ilaya", "Kaltuad", "Kapatan", "Lago", "Laguimit", "Mudan", "New Aklan", "Pangyan", "Poblacion", "Rio Del Pilar", "San Jose", "San Vicente", "Small Margus", "Sufatubo", "Taluya", "Tango", "Tapon"],
                  "Kiamba": ["Badtasan", "Datu Dani", "Gasi", "Kapate", "Katubao", "Kayupo", "Kling", "Lagundi", "Lebe", "Lomuyon", "Luma", "Maligang", "Nalus", "Poblacion", "Salakit", "Suli", "Tablao", "Tamadang", "Tambilil"],
                  "Maasim": ["Amsipit", "Bales", "Colon", "Daliao", "Kabatiol", "Kablacan", "Kamanga", "Kanalo", "Lumasal", "Lumatil", "Malbang", "Nomoh", "Pananag", "Poblacion", "Seven Hills", "Tinoto"],
                  "Maitum": ["Bati-an", "Kalaneg", "Kalaong", "Kiambing", "Kiayap", "Mabay", "Maguling", "Malalag (Pob.)", "Mindupok", "New La Union", "Old Poblacion", "Pangi", "Pinol", "Sison", "Ticulab", "Tuanadatu", "Upo", "Wali", "Zion"],
                  "Malapatan": ["Daan Suyan", "Kihan", "Kinam", "Libi", "Lun Masla", "Lun Padidu", "Patag", "Poblacion", "Sapu Masla", "Sapu Padidu", "Tuyan", "Upper Suyan"],
                  "Malungon": ["Alkikan", "Ampon", "Atlae", "B'Laan", "Banahaw", "Banate", "Datal Batong", "Datal Bila", "Datal Tampal", "J.P. Laurel", "Kawayan", "Kibala", "Kiblat", "Kinabalan", "Lower Mainit", "Lutay", "Malabod", "Malalag Cogon", "Malandag", "Malungon Gamay", "Nagpan", "Panamin", "Poblacion", "San Juan", "San Miguel", "San Roque", "Talus", "Tamban", "Upper Biangan", "Upper Lumabat", "Upper Mainit"],
                  "Cotabato City": ["Bagua", "Bagua I", "Bagua II", "Bagua III", "Kalanganan", "Kalanganan I", "Kalanganan II", "Poblacion", "Poblacion I", "Poblacion II", "Poblacion III", "Poblacion IV", "Poblacion IX", "Poblacion V", "Poblacion VI", "Poblacion VII", "Poblacion VIII", "Rosary Heights", "Rosary Heights I", "Rosary Heights II", "Rosary Heights III", "Rosary Heights IV", "Rosary Heights IX", "Rosary Heights V", "Rosary Heights VI", "Rosary Heights VII", "Rosary Heights VIII", "Rosary Heights X", "Rosary Heights XI", "Rosary Heights XII", "Rosary Heights XIII", "Tamontaka", "Tamontaka I", "Tamontaka II", "Tamontaka III", "Tamontaka IV", "Tamontaka V"]
                }
              }
            }
          },
          "Region XIII": {
            provinces: {
              "Agusan del Norte": {
                municipalities: ["Buenavista", "City of Butuan", "City of Cabadbaran", "Carmen", "Jabonga", "Kitcharao", "Las Nieves", "Magallanes", "Santiago", "Tubay", "Remedios T. Romualdez"],
                barangays: {
                  "Buenavista": ["Abilan", "Agong-ong", "Alubijid", "Guinabsan", "Lower Olave", "Macalang", "Malapong", "Malpoc", "Manapa", "Matabao", "Poblacion 1", "Poblacion 10", "Poblacion 2", "Poblacion 3", "Poblacion 4", "Poblacion 5", "Poblacion 6", "Poblacion 7", "Poblacion 8", "Poblacion 9", "Rizal", "Sacol", "Sangay", "Simbalan", "Talo-ao"],
                  "City of Butuan": ["Agao Pob.", "Agusan Peque\u00f1o", "Ambago", "Amparo", "Ampayon", "Anticala", "Antongalon", "Aupagan", "Baan KM 3", "Baan Riverside Pob.", "Babag", "Bading Pob.", "Bancasi", "Banza", "Baobaoan", "Basag", "Bayanihan Pob.", "Bilay", "Bit-os", "Bitan-agan", "Bobon", "Bonbon", "Bugabus", "Bugsukan", "Buhangin Pob.", "Cabcabon", "Camayahan", "Dagohoy Pob.", "Dankias", "De Oro", "Diego Silang Pob.", "Don Francisco", "Doongan", "Dulag", "Dumalagan", "Florida", "Golden Ribbon Pob.", "Holy Redeemer Pob.", "Humabon Pob.", "Imadejas Pob.", "Jose Rizal Pob.", "Kinamlutan", "Lapu-lapu Pob.", "Lemon", "Leon Kilat Pob.", "Libertad", "Limaha Pob.", "Los Angeles", "Lumbocan", "Maguinda", "Mahay", "Mahogany Pob.", "Maibu", "Mandamo", "Manila de Bugabus", "Maon Pob.", "Masao", "Maug", "New Society Village Pob.", "Nong-nong", "Obrero Pob.", "Ong Yiu Pob.", "Pagatpatan", "Pangabugan", "Pianing", "Pigdaulan", "Pinamanculan", "Port Poyohon Pob.", "Rajah Soliman Pob.", "Salvacion", "San Ignacio Pob.", "San Mateo", "San Vicente", "Santo Ni\u00f1o", "Sikatuna Pob.", "Silongan Pob.", "Sumile", "Sumilihon", "Tagabaca", "Taguibo", "Taligaman", "Tandang Sora Pob.", "Tiniwisan", "Tungao", "Urduja Pob.", "Villa Kananga"],
                  "City of Cabadbaran": ["Antonio Luna", "Bay-ang", "Bayabas", "Caasinan", "Cabinet", "Calamba", "Calibunan", "Comagascas", "Concepcion", "Del Pilar", "Katugasan", "Kauswagan", "La Union", "Mabini", "Mahaba", "Poblacion 1", "Poblacion 10", "Poblacion 11", "Poblacion 12", "Poblacion 2", "Poblacion 3", "Poblacion 4", "Poblacion 5", "Poblacion 6", "Poblacion 7", "Poblacion 8", "Poblacion 9", "Puting Bato", "Sanghan", "Soriano", "Tolosa"],
                  "Carmen": ["Cahayagan", "Gosoon", "Manoligao", "Poblacion", "Rojales", "San Agustin", "Tagcatong", "Vinapor"],
                  "Jabonga": ["A. Beltran", "Baleguian", "Bangonay", "Bunga", "Colorado", "Cuyago", "Libas", "Magdagooc", "Magsaysay", "Maraiging", "Poblacion", "San Jose", "San Pablo", "San Vicente", "Santo Ni\u00f1o"],
                  "Kitcharao": ["Bangayan", "Canaway", "Crossing", "Hinimbangan", "Jaliobong", "Mahayahay", "Poblacion", "San Isidro", "San Roque", "Sangay", "Songkoy"],
                  "Las Nieves": ["Ambacon", "Balungagan", "Bonifacio", "Casiklan", "Consorcia", "Durian", "Eduardo G. Montilla", "Ibuan", "Katipunan", "Lingayao", "Malicato", "Maningalao", "Marcos Calo", "Mat-i", "Pinana-an", "Poblacion", "Rosario", "San Isidro", "San Roque", "Tinucoran"],
                  "Magallanes": ["Buhang", "Caloc-an", "Guiasan", "Marcos", "Poblacion", "Santo Ni\u00f1o", "Santo Rosario", "Taod-oy"],
                  "Nasipit": ["Aclan", "Amontay", "Ata-atahon", "Barangay 1 (Pob.)", "Barangay 2 (Pob.)", "Barangay 3 (Pob.)", "Barangay 4 (Pob.)", "Barangay 5 (Pob.)", "Barangay 6 (Pob.)", "Barangay 7 (Pob.)", "Camagong", "Cubi-cubi", "Culit", "Jaguimitan", "Kinabjangan", "Punta", "Santa Ana", "Talisay", "Triangulo"],
                  "Santiago": ["Curva", "Estanislao Morgado", "Jagupit", "La Paz", "Pangaylan-IP", "Poblacion I", "Poblacion II", "San Isidro", "Tagbuyacan"],
                  "Tubay": ["Binuangan", "Cabayawa", "Do\u00f1a Rosario", "Do\u00f1a Telesfora", "La Fraternidad", "Lawigan", "Poblacion 1", "Poblacion 2", "Santa Ana", "Tagmamarkay", "Tagpangahoy", "Tinigbasan", "Victory"],
                  "Remedios T. Romualdez": ["Balangbalang", "Basilisa", "Humilog", "Panaytayon", "Poblacion I", "Poblacion II", "San Antonio", "Tagbongabong"]
                }
              },
              "Agusan del Sur": {
                municipalities: ["City of Bayugan", "Bunawan", "Esperanza", "La Paz", "Loreto", "Prosperidad", "Rosario", "San Francisco", "San Luis", "Santa Josefa", "Veruela", "Sibagat"],
                barangays: {
                  "City of Bayugan": ["Berseba", "Bucac", "Cagbas", "Calaitan", "Canayugan", "Charito", "Claro Cortez", "Fili", "Gamao", "Getsemane", "Grace Estate", "Hamogaway", "Katipunan", "Mabuhay", "Magkiangkang", "Mahayag", "Marcelina", "Maygatasan", "Montivesta", "Mt. Ararat", "Mt. Carmel", "Mt. Olive", "New Salem", "Noli", "Osme\u00f1a", "Panaytay", "Pinagalaan", "Poblacion", "Sagmone", "Saguma", "Salvacion", "San Agustin", "San Isidro", "San Juan", "Santa Irene", "Santa Teresita", "Santo Ni\u00f1o", "Taglatawan", "Taglibas", "Tagubay", "Verdu", "Villa Undayon", "Wawa"],
                  "Bunawan": ["Bunawan Brook", "Consuelo", "Imelda", "Libertad", "Mambalili", "Nueva Era", "Poblacion", "San Andres", "San Marcos", "San Teodoro"],
                  "Esperanza": ["Agsabu", "Aguinaldo", "Anolingan", "Bakingking", "Balubo", "Bentahon", "Bunaguit", "Catmonon", "Cebulan", "Concordia", "Crossing Luna", "Cubo", "Dakutan", "Duangan", "Guadalupe", "Guibonon", "Hawilian", "Kalabuan", "Kinamaybay", "Labao", "Langag", "Maasin", "Mac-Arthur", "Mahagcot", "Maliwanag", "Milagros", "Nato", "New Gingoog", "Odiong", "Oro", "Piglawigan", "Poblacion", "Remedios", "Salug", "San Isidro", "San Jose", "San Toribio", "San Vicente", "Santa Fe", "Segunda", "Sinakungan", "Tagabase", "Taganahaw", "Tagbalili", "Tahina", "Tandang Sora", "Valentina"],
                  "La Paz": ["Angeles", "Bataan", "Comota", "Halapitan", "Kasapa II", "Langasian", "Lydia", "Osme\u00f1a, Sr.", "Panagangan", "Poblacion", "Sabang Adgawan", "Sagunto", "San Patricio", "Valentina", "Villa Paz"],
                  "Loreto": ["Binucayan", "Johnson", "Kasapa", "Katipunan", "Kauswagan", "Magaud", "Nueva Gracia", "Poblacion", "Sabud", "San Isidro", "San Mariano", "San Vicente", "Santa Teresa", "Santo Ni\u00f1o", "Santo Tomas", "Violanta", "Waloe"],
                  "Prosperidad": ["Aurora", "Awa", "Azpetia", "La Caridad", "La Perian", "La Purisima", "La Suerte", "La Union", "Las Navas", "Libertad", "Los Arcos", "Lucena", "Mabuhay", "Magsaysay", "Mapaga", "Napo", "New Maug", "Patin-ay", "Poblacion", "Salimbogaon", "Salvacion", "San Joaquin", "San Jose", "San Lorenzo", "San Martin", "San Pedro", "San Rafael", "San Roque", "San Salvador", "San Vicente", "Santa Irene", "Santa Maria"],
                  "Rosario": ["Bayugan 3", "Cabantao", "Cabawan", "Libuac", "Maligaya", "Marfil", "Novele", "Poblacion", "Santa Cruz", "Tagbayagan", "Wasi-an"],
                  "San Francisco": ["Alegria", "Barangay 1 (Pob.)", "Barangay 2 (Pob.)", "Barangay 3 (Pob.)", "Barangay 4 (Pob.)", "Barangay 5 (Pob.)", "Bayugan 2", "Bitan-agan", "Borbon", "Buenasuerte", "Caimpugan", "Das-agan", "Ebro", "Hubang", "Karaus", "Ladgadan", "Lapinigan", "Lucac", "Mate", "New Visayas", "Ormaca", "Pasta", "Pisa-an", "Rizal", "San Isidro", "Santa Ana", "Tagapua"],
                  "San Luis": ["Anislagan", "Balit", "Baylo", "Binicalan", "Cecilia", "Coalicion", "Culi", "Dimasalang", "Don Alejandro", "Don Pedro", "Do\u00f1a Flavia", "Do\u00f1a Maxima", "Mahagsay", "Mahapag", "Mahayahay", "Muritula", "Nuevo Trabajo", "Poblacion", "Policarpo", "San Isidro", "San Pedro", "Santa Ines", "Santa Rita", "Santiago", "Wegguam"],
                  "Santa Josefa": ["Angas", "Aurora", "Awao", "Concepcion", "Pag-asa", "Patrocinio", "Poblacion", "San Jose", "Santa Isabel", "Sayon", "Tapaz"],
                  "Talacogon": ["Batucan", "BuenaGracia", "Causwagan", "Culi", "Del Monte", "Desamparados", "La Flora", "Labnig", "Maharlika", "Marbon", "Sabang Gibung", "San Agustin (Pob.)", "San Isidro (Pob.)", "San Nicolas (Pob.)", "Zamora", "Zillovia"],
                  "Trento": ["Basa", "Cebolin", "Cuevas", "Kapatungan", "Langkila-an", "Manat", "New Visayas", "Pangyan", "Poblacion", "Pulang-lupa", "Salvacion", "San Ignacio", "San Isidro", "San Roque", "Santa Maria", "Tudela"],
                  "Veruela": ["Anitap", "Bacay II", "Binongan", "Caigangan", "Candiis", "Del Monte", "Don Mateo", "Katipunan", "La Fortuna", "Limot", "Magsaysay", "Masayan", "Poblacion", "Sampaguita", "San Gabriel", "Santa Cruz", "Santa Emelia", "Sawagan", "Sinobong", "Sisimon"],
                  "Sibagat": ["Afga", "Anahawan", "Banagbanag", "Del Rosario", "El Rio", "Ilihan", "Kauswagan", "Kioya", "Kolambugan", "Magkalape", "Magsaysay", "Mahayahay", "New Tubigon", "Padiay", "Perez", "Poblacion", "San Isidro", "San Vicente", "Santa Cruz", "Santa Maria", "Sinai", "Tabon-tabon", "Tag-uyango", "Villangit"]
                }
              },
              "Surigao del Norte": {
                municipalities: ["Alegria", "Bacuag", "Burgos", "Claver", "Dapa", "Del Carmen", "General Luna", "Gigaquit", "Mainit", "Malimono", "Pilar", "Placer", "San Benito", "San Francisco", "San Isidro", "Santa Monica", "Sison", "Socorro", "City of Surigao", "Tubod"],
                barangays: {
                  "Alegria": ["Alipao", "Anahaw", "Budlingin", "Camp Eduard", "Ferlda", "Gamuton", "Julio Ouano (Pob.)", "Ombong", "Poblacion", "Pongtud", "San Juan", "San Pedro"],
                  "Bacuag": ["Cabugao", "Cambuayon", "Campo", "Dugsangon", "Pautao", "Payapag", "Poblacion", "Pungtod", "Santo Rosario"],
                  "Burgos": ["Baybay", "Bitaug", "Matin-ao", "Poblacion 1", "Poblacion 2", "San Mateo"],
                  "Claver": ["Bagakay", "Cabugo", "Cagdianao", "Daywan", "Hayanggabon", "Ladgaron (Pob.)", "Lapinigan", "Magallanes", "Panatao", "Sapa", "Taganito", "Tayaga", "Urbiztondo", "Wangke"],
                  "Dapa": ["Bagakay", "Barangay 1 (Pob.)", "Barangay 10 (Pob.)", "Barangay 11 (Pob.)", "Barangay 12 (Pob.)", "Barangay 13 (Pob.)", "Barangay 2 (Pob.)", "Barangay 3 (Pob.)", "Barangay 4 (Pob.)", "Barangay 5 (Pob.)", "Barangay 6 (Pob.)", "Barangay 7 (Pob.)", "Barangay 8 (Pob.)", "Barangay 9 (Pob.)", "Buenavista", "Cabawa", "Cambas-ac", "Consolacion", "Corregidor", "Dagohoy", "Don Paulino", "Jubang", "Montserrat", "Osme\u00f1a", "San Carlos", "San Miguel", "Santa Fe", "Santa Felomina", "Union"],
                  "Del Carmen": ["Antipolo", "Bagakay", "Bitoon", "Cabugao", "Cancohoy", "Caub", "Del Carmen (Pob.)", "Domoyog", "Esperanza", "Halian", "Jamoyaon", "Katipunan", "Lobogon", "Mabuhay", "Mahayahay", "Quezon", "San Fernando", "San Jose (Pob.)", "Sayak", "Tuboran"],
                  "General Luna": ["Anajawan", "Cabitoonan", "Catangnan", "Consuelo", "Corazon", "Daku", "La Januza", "Libertad", "Magsaysay", "Malinao", "Poblacion I", "Poblacion II", "Poblacion III", "Poblacion IV", "Poblacion V", "Santa Cruz", "Santa Fe", "Suyangan", "Tawin-tawin"],
                  "Gigaquit": ["Alambique (Pob.)", "Anibongan", "Cam-boayon", "Camam-onan", "Ipil (Pob.)", "Lahi", "Mahanub", "Poniente", "San Antonio", "San Isidro", "Sico-sico", "Villaflor", "Villafranca"],
                  "Mainit": ["Binga", "Bobona-on", "Cantugas", "Dayano", "Mabini", "Magpayang", "Magsaysay (Pob.)", "Mansayao", "Marayag", "Matin-ao", "Paco", "Quezon (Pob.)", "Roxas", "San Francisco", "San Isidro", "San Jose", "Siana", "Silop", "Tagbuyawan", "Tapi-an", "Tolingon"],
                  "Malimono": ["Bunyasan", "Cagtinae", "Can-aga", "Cansayong", "Cantapoy", "Cayawan", "Doro", "Hanagdong", "Karihatag", "Masgad", "Pili", "San Isidro (Pob.)", "Tinago", "Villariza"],
                  "Pilar": ["Asinan (Pob.)", "Caridad", "Centro (Pob.)", "Consolacion", "Datu", "Dayaohay", "Jaboy", "Katipunan", "Maasin", "Mabini", "Mabuhay", "Pilaring (Pob.)", "Punta (Pob.)", "Salvacion", "San Roque"],
                  "Placer": ["Amoslog", "Anislagan", "Bad-as", "Boyongan", "Bugas-bugas", "Central (Pob.)", "Ellaperal", "Ipil (Pob.)", "Lakandula", "Mabini", "Macalaya", "Magsaysay (Pob.)", "Magupange", "Pananay-an", "Panhutongan", "San Isidro", "Sani-sani", "Santa Cruz", "Suyoc", "Tagbongabong"],
                  "San Benito": ["Bongdo", "Maribojoc", "Nuevo Campo", "San Juan", "Santa Cruz (Pob.)", "Talisay (Pob.)"],
                  "San Francisco": ["Amontay", "Balite", "Banbanon", "Diaz", "Honrado", "Jubgan", "Linongganan", "Macopa", "Magtangale", "Oslao", "Poblacion"],
                  "San Isidro": ["Buhing Calipay", "Del Carmen (Pob.)", "Del Pilar", "Macapagal", "Pacifico", "Pelaez", "Roxas", "San Miguel", "Santa Paz", "Santo Ni\u00f1o", "Tambacan", "Tigasao"],
                  "Santa Monica": ["Abad Santos", "Alegria", "Bailan", "Garcia", "Libertad", "Mabini", "Mabuhay (Pob.)", "Magsaysay", "Rizal", "T. Arlan (Pob.)", "Tangbo"],
                  "Sison": ["Biyabid", "Gacepan", "Ima", "Lower Patag", "Mabuhay", "Mayag", "Poblacion", "San Isidro", "San Pablo", "Tagbayani", "Tinogpahan", "Upper Patag"],
                  "Socorro": ["Albino Taruc", "Del Pilar", "Helene", "Honrado", "Navarro (Pob.)", "Nueva Estrella", "Pamosaingan", "Rizal (Pob.)", "Salog", "San Roque", "Santa Cruz", "Sering", "Songkoy", "Sudlon"],
                  "City of Surigao": ["Alang-alang", "Alegria", "Anomar", "Aurora", "Balibayon", "Baybay", "Bilabid", "Bitaugan", "Bonifacio", "Buenavista", "Cabongbongan", "Cagniog", "Cagutsan", "Canlanipa", "Cantiasay", "Capalayan", "Catadman", "Danao", "Danawan", "Day-asan", "Ipil", "Libuac", "Lipata", "Lisondra", "Luna", "Mabini", "Mabua", "Manyagao", "Mapawa", "Mat-i", "Nabago", "Nonoc", "Orok", "Poctoy", "Punta Bilar", "Quezon", "Rizal", "Sabang", "San Isidro", "San Jose", "San Juan", "San Pedro", "San Roque", "Serna", "Sidlakan", "Silop", "Sugbay", "Sukailang", "Taft (Pob.)", "Talisay", "Togbongon", "Trinidad", "Washington (Pob.)", "Zaragoza"],
                  "Tagana-An": ["Aurora (Pob.)", "Azucena (Pob.)", "Banban", "Cawilan", "Fabio", "Himamaug", "Laurel", "Lower Libas", "Opong", "Patino", "Sampaguita (Pob.)", "Talavera", "Union", "Upper Libas"],
                  "Tubod": ["Capayahan", "Cawilan", "Del Rosario", "Marga", "Motorpool", "Poblacion", "San Isidro", "San Pablo", "Timamana"]
                }
              },
              "Surigao del Sur": {
                municipalities: ["Barobo", "Bayabas", "City of Bislig", "Cagwait", "Cantilan", "Carmen", "Carrascal", "Cortes", "Hinatuan", "Lanuza", "Lianga", "Lingig", "Madrid", "Marihatag", "San Agustin", "San Miguel", "Tagbina", "Tago", "City of Tandag"],
                barangays: {
                  "Barobo": ["Amaga", "Bahi", "Cabacungan", "Cambagang", "Causwagan", "Dapdap", "Dughan", "Gamut", "Javier", "Kinayan", "Mamis", "Poblacion", "Rizal", "San Jose", "San Roque", "San Vicente", "Sua", "Sudlon", "Tambis", "Unidad", "Wakat"],
                  "Bayabas": ["Amag", "Balete (Pob.)", "Cabugo", "Cagbaoto", "La Paz", "Magobawok", "Panaosawon"],
                  "City of Bislig": ["Bucto", "Burboanan", "Caguyao", "Coleto", "Comawas", "Kahayag", "Labisma", "Lawigan", "Maharlika", "Mangagoy", "Mone", "Pamanlinan", "Pamaypayan", "Poblacion", "San Antonio", "San Fernando", "San Isidro", "San Jose", "San Roque", "San Vicente", "Santa Cruz", "Sibaroy", "Tabon", "Tumanan"],
                  "Cagwait": ["Aras-Asan", "Bacolod", "Bitaugan East", "Bitaugan West", "La Purisima", "Lactudan", "Mat-e", "Poblacion", "Tawagan", "Tubo-tubo", "Unidad"],
                  "Cantilan": ["Bugsukan", "Buntalid", "Cabangahan", "Cabas-an", "Calagdaan", "Consuelo", "General Island", "Lininti-an (Pob.)", "Lobo", "Magasang", "Magosilom (Pob.)", "Pag-Antayan", "Palasao", "Parang", "San Pedro", "Tapi", "Tigabong"],
                  "Carmen": ["Antao", "Cancavan", "Carmen (Pob.)", "Esperanza", "Hinapoyan", "Puyat", "San Vicente", "Santa Cruz"],
                  "Carrascal": ["Adlay", "Babuyan", "Bacolod", "Baybay (Pob.)", "Bon-ot", "Caglayag", "Dahican", "Doyos (Pob.)", "Embarcadero (Pob.)", "Gamuton", "Panikian", "Pantukan", "Saca (Pob.)", "Tag-Anito"],
                  "Cortes": ["Balibadon", "Burgos", "Capandan", "Mabahin", "Madrelino", "Manlico", "Matho", "Poblacion", "Tag-Anongan", "Tigao", "Tuboran", "Uba"],
                  "Hinatuan": ["Baculin", "Benigno Aquino", "Bigaan", "Cambatong", "Campa", "Dugmanon", "Harip", "La Casa (Pob.)", "Loyola", "Maligaya", "Pagtigni-an", "Pocto", "Port Lamon", "Roxas", "San Juan", "Sasa", "Tagasaka", "Tagbobonga", "Talisay", "Tarusan", "Tidman", "Tiwi", "Zone II (Pob.)", "Zone III Maharlika (Pob.)"],
                  "Lanuza": ["Agsam", "Bocawe", "Bunga", "Gamuton", "Habag", "Mampi", "Nurcia", "Pakwan", "Sibahay", "Zone I (Pob.)", "Zone II (Pob.)", "Zone III (Pob.)", "Zone IV (Pob.)"],
                  "Lianga": ["Anibongan", "Ban-as", "Banahao", "Baucawe", "Diatagon", "Ganayon", "Liatimco", "Manyayay", "Payasan", "Poblacion", "Saint Christine", "San Isidro", "San Pedro"],
                  "Lingig": ["Anibongan", "Barcelona", "Bogak", "Bongan", "Handamayan", "Mahayahay", "Mandus", "Mansa-ilao", "Pagtila-an", "Palo Alto", "Poblacion", "Rajah Cabungso-an", "Sabang", "Salvacion", "San Roque", "Tagpoporan", "Union", "Valencia"],
                  "Madrid": ["Bagsac", "Bayogo", "Linibonan", "Magsaysay", "Manga", "Panayogon", "Patong Patong", "Quirino (Pob.)", "San Antonio", "San Juan", "San Roque", "San Vicente", "Songkit", "Union"],
                  "Marihatag": ["Alegria", "Amontay", "Antipolo", "Arorogan", "Bayan", "Mahaba", "Mararag", "Poblacion", "San Antonio", "San Isidro", "San Pedro", "Santa Cruz"],
                  "San Agustin": ["Bretania", "Buatong", "Buhisan", "Gata", "Hornasan", "Janipaan", "Kauswagan", "Oteiza", "Poblacion", "Pong-on", "Pongtod", "Salvacion", "Santo Ni\u00f1o"],
                  "San Miguel": ["Bagyang", "Baras", "Bitaugan", "Bolhoon", "Calatngan", "Carromata", "Castillo", "Libas Gua", "Libas Sud", "Magroyong", "Mahayag", "Patong", "Poblacion", "Sagbayan", "San Roque", "Siagao", "Tina", "Umalag"],
                  "Tagbina": ["Batunan", "Carpenito", "Do\u00f1a Carmen", "Hinagdanan", "Kahayagan", "Lago", "Maglambing", "Maglatab", "Magsaysay", "Malixi", "Manambia", "Osme\u00f1a", "Poblacion", "Quezon", "San Vicente", "Santa Cruz", "Santa Fe", "Santa Juana", "Santa Maria", "Sayon", "Soriano", "Tagongon", "Trinidad", "Ugoban", "Villaverde"],
                  "Tago": ["Alba", "Anahao Bag-o", "Anahao Daan", "Badong", "Bajao", "Bangsud", "Cabangahan", "Cagdapao", "Camagong", "Caras-an", "Cayale", "Dayo-an", "Gamut", "Jubang", "Kinabigtasan", "Layog", "Lindoy", "Mercedes", "Purisima (Pob.)", "Sumo-sumo", "Umbay", "Unaban", "Unidos", "Victoria"],
                  "City of Tandag": ["Awasian", "Bagong Lungsod (Pob.)", "Bioto", "Bongtod Pob.", "Buenavista", "Dagocdoc (Pob.)", "Mabua", "Mabuhay", "Maitum", "Maticdum", "Pandanon", "Pangi", "Quezon", "Rosario", "Salvacion", "San Agustin Norte", "San Agustin Sur", "San Antonio", "San Isidro", "San Jose", "Telaje"]
                }
              },
              "Dinagat Islands": {
                municipalities: ["Basilisa", "Cagdianao", "Dinagat", "Libjo", "Loreto", "San Jose", "Tubajon"],
                barangays: {
                  "Basilisa": ["Benglen", "Catadman", "Columbus", "Coring", "Cortes", "Diegas", "Do\u00f1a Helene", "Edera", "Ferdinand", "Geotina", "Imee", "Melgar", "Montag", "Navarro", "New Nazareth", "Poblacion", "Puerto Princesa", "Rita Glenda", "Roma", "Roxas", "Santa Monica", "Santo Ni\u00f1o", "Sering", "Sombrado", "Tag-abaca", "Villa Ecleo", "Villa Pantinople"],
                  "Cagdianao": ["Boa", "Cabunga-an", "Del Pilar", "Laguna", "Legaspi", "Ma-atas", "Mabini", "Nueva Estrella", "Poblacion", "R. Ecleo, Sr.", "San Jose", "Santa Rita", "Tigbao", "Valencia"],
                  "Dinagat": ["Bagumbayan", "Cab-ilan", "Cabayawan", "Cayetano", "Escolta (Pob.)", "Gomez", "Justiniana Edera", "Magsaysay", "Mauswagon (Pob.)", "New Mabuhay", "Wadas", "White Beach (Pob.)"],
                  "Libjo": ["Albor (Pob.)", "Arellano", "Bayanihan", "Do\u00f1a Helen", "Garcia", "General Aguinaldo", "Kanihaan", "Llamera", "Magsaysay", "Osme\u00f1a", "Plaridel", "Quezon", "Rosita", "San Antonio (Pob.)", "San Jose", "Santo Ni\u00f1o"],
                  "Loreto": ["Carmen (Pob.)", "Esperanza", "Ferdinand", "Helene", "Liberty", "Magsaysay", "Panamaon", "San Juan (Pob.)", "Santa Cruz (Pob.)", "Santiago (Pob.)"],
                  "San Jose": ["Aurelio", "Cuarinta", "Don Ruben Ecleo", "Jacquez", "Justiniana Edera", "Luna", "Mahayahay", "Matingbe", "San Jose (Pob.)", "San Juan", "Santa Cruz", "Wilson"],
                  "Tubajon": ["Diaz", "Imelda", "Mabini", "Malinao", "Navarro", "Roxas", "San Roque (Pob.)", "San Vicente (Pob.)", "Santa Cruz (Pob.)"]
                }
              }
            }
          }
        }
      };

      function populateProvinces(selectedRegion) {
        const provinceSelect = $('#province');
        provinceSelect.empty().append('<option selected >Select Province</option>');
        if (selectedRegion && addressOptions.regions[selectedRegion]) {
          Object.keys(addressOptions.regions[selectedRegion].provinces).forEach(function(province) {
            provinceSelect.append(`<option value="${province}">${province}</option>`);
          });
        }
        provinceSelect.val(patientData.address.address_province).trigger('change');
      }

      function populateMunicipalities(selectedRegion, selectedProvince) {
        const municipalitySelect = $('#municipality');
        municipalitySelect.empty().append('<option selected >Select Municipality</option>');
        if (selectedProvince && addressOptions.regions[selectedRegion].provinces[selectedProvince]) {
          const municipalities = addressOptions.regions[selectedRegion].provinces[selectedProvince].municipalities;
          municipalities.forEach(function(municipality) {
            municipalitySelect.append(`<option value="${municipality}">${municipality}</option>`);
          });
        }
        municipalitySelect.val(patientData.address.address_municipality).trigger('change'); // Set the selected municipality
      }

      function populateBarangays(selectedRegion, selectedProvince, selectedMunicipality) {
        const barangaySelect = $('#barangay');
        barangaySelect.empty().append('<option selected >Select Barangay</option>');
        if (selectedMunicipality && addressOptions.regions[selectedRegion].provinces[selectedProvince]) {
          const barangays = addressOptions.regions[selectedRegion].provinces[selectedProvince].barangays[selectedMunicipality];
          barangays.forEach(function(barangay) {
            barangaySelect.append(`<option value="${barangay}">${barangay}</option>`);
          });
        }
        barangaySelect.val(patientData.address.address_barangay).trigger('change'); // Set the selected barangay
      }

      // Function to check if the value exists in a dropdown
      function checkIfExistsInDropdown(dropdown, value) {
        return $(dropdown).find(`option[value='${value}']`).length > 0;
      }

      $('#region').on('change', function() {
        populateProvinces($(this).val());
      });

      $('#province').on('change', function() {
        populateMunicipalities($('#region').val(), $(this).val());
      });

      $('#municipality').on('change', function() {
        populateBarangays($('#region').val(), $('#province').val(), $(this).val());
      });

      if (patientData.staff.staff_office && !checkIfExistsInDropdown('#office', patientData.staff.staff_office)) {
        $('#office').hide();
        $('#officeInputContainer').removeClass('hidden');
        $('#officeInput').val(patientData.staff.staff_office);
        $('#backToDropdown').removeClass('hidden');
        currentField = 'office';
      } else {
        $('#office').val(patientData.staff.staff_office);
      }


      if (patientData.staff.staff_office) {
        $('#office').val(patientData.staff.staff_office).trigger('change');
      }

      if (patientData.address.address_region) {
        $('#region').val(patientData.address.address_region).trigger('change');
      }

      if (patientData.address.address_province) {
        $('#province').val(patientData.address.address_province).trigger('change');
      }

      if (patientData.address.address_municipality) {
        $('#municipality').val(patientData.address.address_municipality).trigger('change');
      }

      if (patientData.address.address_barangay) {
        $('#barangay').val(patientData.address.address_barangay);
      }

      $('#office').on('change', function() {
        const selectedoffice = $(this).val();

        if (selectedoffice === 'Click to type...') {

          $('#office').hide();
          $('#officeInputContainer').removeClass('hidden');
          $('#backToDropdown').removeClass('hidden');
          currentField = 'office';

        }
      });


      $('#backToDropdown').on('click', function() {
        $('#officeInputContainer').addClass('hidden');
        $('#office').show();

        $('#office').val(patientData.staff.staff_office);

        $(this).addClass('hidden');
      });

    });
  </script>
</body>

</html>