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
  <title>ClinicaLog Dashboard</title>
  <meta
    content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
    name="viewport" />
  <link
    rel="icon"
    href="../assets/img/ClinicaLog.ico" 
    type="image/x-icon" />
 
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


</head>

<body>
  <div class="main-header-logo">
    <!-- Logo Header -->
    <div class="logo-header" data-background-color="dark">
    <a href="<?php echo $redirectPage; ?>" class="logo">
      <img
        src="../assets/img/sidebar-logo.svg"
        alt="navbar brand"
        class="navbar-brand"
        height="60" />
    </a>
      <div class="nav-toggle">
      </div>
      <button class="topbar-toggler more">
        <i class="gg-more-vertical-alt"></i>
      </button>
    </div>
    <!-- End Logo Header -->
  </div>
  <!-- Navbar Header -->
  <nav
    class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom">
    <div class="container-fluid">
      <a href="<?php echo $redirectPage; ?>" class="logo">
        <img
          src="../assets/img/sidebar-logo.svg"
          alt="navbar brand"
          class="navbar-brand"
          height="60" />
      </a>
      <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
        <li>
          <nav
            class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex">
            <div class="input-group">
              <div class="input-group-prepend">
                <button type="submit" class="btn btn-search pe-1">
                  <i class="fa fa-search search-icon" style="color: black !important;"></i>
                </button>
              </div>
              <input
                type="text"
                placeholder="Search ..."
                class="form-control" />
            </div>
          </nav>
        </li>
        <li
          class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none">
          <a
            class="nav-link dropdown-toggle"
            data-bs-toggle="dropdown"
            href="#"
            role="button"
            aria-expanded="false"
            aria-haspopup="true">
            <i class="fa fa-search"></i>
          </a>
          <ul class="dropdown-menu dropdown-search animated fadeIn">
            <form class="navbar-right navbar-form nav-search">
              <div class="input-group">
                <input
                  type="text"
                  placeholder="Search ..."
                  class="form-control" />
              </div>
            </form>
          </ul>
        </li>
        <li class="nav-item topbar-icon dropdown hidden-caret">
          <a
            class="nav-link dropdown-toggle"
            href="#"
            id="notifDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false">
            <i class="fa fa-bell"></i>
            <span class="notification"></span>
          </a>
          <ul
            class="dropdown-menu notif-box animated fadeIn"
            aria-labelledby="notifDropdown">
            <li>
            </li>
            <li>
              <div class="notif-scroll scrollbar-outer">
                <div class="notif-center">
                  <a href="#" id="notif-link">
                    <div class="notif-icon notif-primary">
                    <i class="fa fa-exclamation-triangle"></i>
                    </div>
                    <div class="notif-content">
                      <span class="block">Important Notice! Click here to see more.</span>
                    </div>
                  </a> 
                </div>
              </div>
            </li>
          </ul>
        </li>
        <li class="nav-item topbar-user dropdown hidden-caret">
          <a
            class="dropdown-toggle profile-pic" 
            data-bs-toggle="dropdown"
            href="#"
            aria-expanded="false">
            <div class="avatar-sm">
              <img
              src='/php-admin/uploads/<?php echo !empty($patientData->patient_profile) ? $patientData->patient_profile : 'default-image.jpg'; ?>'
              alt="..."
                class="avatar-img rounded-circle" />
            </div>
            <span class="profile-username">
              <span class="op-7">Hi,</span>
              <span class="fw-bold"><?php echo ($patientData->patient_fname); ?></span>
              </span>
          </a>
          <ul class="dropdown-menu dropdown-user animated fadeIn">
            <div class="dropdown-user-scroll scrollbar-outer">
              <li>
                <div class="user-box">
                  <div class="avatar-lg">
                  <img
                      src='/php-admin/uploads/<?php echo !empty($patientData->patient_profile) ? $patientData->patient_profile : 'default-image.jpg'; ?>'
                      alt="image profile"
                      class="avatar-img rounded"
                    />

                  </div>
                  <div class="u-text">
                    <h4><?php echo ($patientData->patient_fname); ?></h4>
                    <p class="text-muted"><?php echo ($patientData->patient_email); ?></p> 
                  </div>
              </li> 
              <li>
                <a class="dropdown-item" href="clientprofile.php">Account Setting</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="" id="logoutLink">Logout</a>
                </li>
            </div>
          </ul>
        </li>
      </ul>
    </div>
  </nav>
  <!--   Core JS Files   -->
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

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
  document.getElementById('notif-link').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default link action
    
// Trigger SweetAlert
Swal.fire({
  title: 'Welcome to Clinicalog!',
  text: "Please make sure the information you provide is accurate and up-to-date. This helps us serve you better and ensures that everything runs smoothly.\n\nIf anything changes, kindly update your details.\n\nYour cooperation is greatly appreciated.",
  icon: 'info',
  footer: 'From USeP Campus Clinic Tagum-Unit', 
  confirmButtonText: 'Got it, thanks!'
});


  });
</script>
<script>
    document.getElementById("logoutLink").addEventListener("click", function(event) {
        event.preventDefault(); // Prevent the default link action

        Swal.fire({
            title: "Are you sure you want to logout?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, logout",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect to the logout script
                window.location.href = "logout.php";
            }
        });
    });
</script>


</body>

</html>