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
include('../php/patienttables.php');



$db = new Database();
$conn = $db->getConnection(); 

$patient = new PatientManager($db);

 
$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['id'], $_POST['type'], $_POST['action'])) {
      $id = $_POST['id'];
      $type = $_POST['type'];
      $action = $_POST['action']; 

      $_SESSION['id'] = $id;
      $_SESSION['type'] = $type;
      $_SESSION['action'] = $action;

      if ($action === 'view') {
          switch ($type) {
              case 'Student': 
                  $redirectUrl = 'patient-studprofile.php';
                  break;
              case 'Faculty':
                  $redirectUrl = 'patient-facultyprofile.php';
                  break;
              case 'Staff':
                  $redirectUrl = 'patient-staffprofile.php';
                  break;
              case 'Extension':
                  $redirectUrl = 'patient-extensionprofile.php';
                  break;
              default:
                  $redirectUrl = 'patient-record.php';
                  break;
          }
          echo json_encode(['status' => 'success', 'redirect' => $redirectUrl]);
      } elseif ($action === 'edit') {
          switch ($type) {
            case 'Student':
                $redirectUrl = 'patient-studedit.php';
                break;
            case 'Faculty':
                $redirectUrl = 'patient-facultyedit.php';
                break;
            case 'Staff':
                $redirectUrl = 'patient-staffedit.php';
                break;
            case 'Extension':
                $redirectUrl = 'patient-extenedit.php';
                break;
            default:
                $redirectUrl = 'patient-record.php';
                break;
        }
        echo json_encode(['status' => 'success', 'redirect' => $redirectUrl]);
      } else {
          echo json_encode(['status' => 'error', 'message' => 'patient-record.php']);
      }
  } else {
      echo json_encode(['status' => 'error', 'message' => 'Required parameters are missing']);
  }
  exit;
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Patient Record</title> 
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
                <div>
                  <ul class="nav nav-pills nav-secondary nav-pills-no-bd" id="pills-tab-without-border" role="tablist">
                    <li>
                      <a class="nav-link" href="patient-record.php" role="tab">All</a>
                    </li>
                    <li>
                      <a class="nav-link" href="patient-recordstud.php" role="tab">Student</a>
                    </li>
                    <li>
                      <a class="nav-link" href="patient-recordfac.php" role="tab">Faculty</a>
                    </li>
                    <li>
                      <a class="nav-link" href="patient-recordstaff.php" role="tab">Staff</a>
                    </li>
                    <li>
                      <a class="nav-link active" href="patient-recordexten.php" role="tab">Extension</a>
                    </li>
                  </ul>
                    </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <div class="card">
                    <div class="card-header">
                      <div class="d-flex align-items-center">
                        <h4 class="card-title">Patients</h4>
                        <button
                          class="btn btn-primary btn-round ms-auto"
                          data-bs-toggle="modal"
                          data-bs-target="#addPatientModal"
                          id="addbutton"
                        >
                          <i class="fa fa-plus"></i>
                          Add Patient
                        </button>
                      </div>
                    </div>
                    <div class="card-body">
                      <!-- Modal -->
                      <div
                        class="modal fade"
                        id="addPatientModal"
                        tabindex="-1"
                        role="dialog"
                        aria-hidden="true"
                      >
                        <div class="modal-dialog modal-dialog-centered" role="document" id="AddPatient">
                          <div class="modal-content">
                            <div class="modal-header border-0">
                              <h5 class="modal-title">
                                <span class="fw-mediumbold">Add Patient</span>
                              </h5>
                              <button
                                type="button"
                                class="close"
                                data-bs-dismiss="modal"
                                aria-label="Close"
                                id="edit-exit"
                              >
                                <span aria-hidden="true">&times;</span>
                              </button>
                            </div>
                            <div class="modal-body">
                              <form class="modalButton">
                                <!-- Button for Student Patient -->
                                <a href="add-student.php">
                                  <button type="button" class="btn btn-primary btn-round ms-auto custom-button" id="addbutton">
                                    Student
                                  </button>
                                </a> 
                                <!-- Button for Staff Patient -->
                                <a href="add-faculty.php">
                                  <button type="button" class="btn btn-primary btn-round ms-auto custom-button" id="addbutton">
                                    Faculty
                                  </button>
                                </a>
                                <a href="add-staff.php">
                                  <button type="button" class="btn btn-primary btn-round ms-auto custom-button" id="addbutton">
                                    Staff
                                  </button>
                                </a>
                                <a href="add-extension.php">
                                  <button type="button" class="btn btn-primary btn-round ms-auto custom-button" id="addbutton">
                                    Extension
                                  </button>
                                </a>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="table-responsive">
                                <table id="add-patient" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name & ID</th>
                                            <th>Email</th>
                                            <th>Sex</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th style="width: 10%">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>No.</th>
                                            <th>Name & ID</th>
                                            <th>Email</th>
                                            <th>Sex</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                <?php
                                $patientTables = new PatientTablesbyType($conn);
                                $extens = $patientTables->getAllExtensions();
                                $counter = 1;
                                
                                foreach ($extens as $exten) {
                                    $statusText = isset($exten->exten_status) && $exten->exten_status == 'Inactive' ? 'Disabled' : 'Enabled';
                                    $statusColor = isset($exten->exten_status) && $exten->exten_status == 'Inactive' ? '#ff6961' : '#77dd77';

                                    echo '<tr>';
                                    echo '<td>' . $counter++ . '</td>';
                                    echo '<td>' . $exten->exten_lname . ', ' . $exten->exten_fname . ' ' . $exten->exten_mname . ' (' . $exten->exten_idnum . ')</td>';
                                    echo '<td>' . $exten->exten_email . '</td>';
                                    echo '<td>' . $exten->exten_sex . '</td>';
                                    echo '<td>' . $exten->exten_role . '</td>';
                                    
                                    // Status span with dynamic color and text
                                    echo '<td>
                                            <span style="display: inline-block;
                                                        padding: 5px 10px;
                                                        border-radius: 50px;
                                                        background-color: ' . $statusColor . '; /* Color based on status */
                                                        color: white;
                                                        text-align: center;
                                                        min-width: 60px;">
                                                ' . $statusText . '
                                            </span>
                                        </td>';

                                    echo '<td> 
                                        <div class="form-button-action">
                                            <button type="submit" class="btn btn-link btn-primary btn-lg viewButton" 
                                                    data-id="' . $exten->patient_id . '" data-type="'. $exten->patient_type .'">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            <button type="submit" class="btn btn-link btn-primary btn-lg editButton"
                                                    data-id="' . $exten->patient_id . '" data-type="'. $exten->patient_type .'">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                        </div>
                                    </td>';
                                    echo '</tr>';
                                }
                                ?>
                            </tbody>

                      </table>
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
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>


    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    
    <script>
        $(document).ready(function() {
        $('#add-patient').DataTable({
            responsive: true
        });
    });
    </script>
    <script>
      $(document).ready(function() {
          $('#dynamic-table').DataTable();
          loadDefaultTable(); 
      });
      function loadDefaultTable() {
        $("#tablechange").load("studentstable.php", function(response, status, xhr) {
            if (status === "error") {
                console.log("Error loading studentstable: " + xhr.status + " " + xhr.statusText);
            }
        });
      }

        function changeTableContent(type) {
            let data = [];

            if (type === 'students') {
                $("#tablechange").load("studentstable.php", function(response, status, xhr) {
                    if (status === "error") {
                        console.log("Error loading studentstable: " + xhr.status + " " + xhr.statusText);
                    }
                });
            } else if (type === 'faculties') { 
                $("#tablechange").load("faculty.php", function(response, status, xhr) {
                    if (status === "error") {
                        console.log("Error loading faculty table: " + xhr.status + " " + xhr.statusText);
                    }
                });
            } else if (type === 'staffs') {
                $("#tablechange").load("stafftable.php", function(response, status, xhr) {
                    if (status === "error") {
                        console.log("Error loading staff table: " + xhr.status + " " + xhr.statusText);
                    }
                });
            }

            populateTable(data);
        }
    </script>
      
      <script>
    document.addEventListener('DOMContentLoaded', function () {
        function handleButtonClick(buttonClass) {
            document.querySelectorAll(buttonClass).forEach(function (button) {
                button.addEventListener('click', function () {
                    const id = this.getAttribute('data-id');
                    const type = this.getAttribute('data-type');
                    const action = this.classList.contains('viewButton') ? 'view' : 'edit';

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', '', true); // POST to the same page
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            try {
                                const response = JSON.parse(xhr.responseText); // Parse JSON response
                                if (response.status === 'success') {
                                    window.location.href = response.redirect;
                                } else if (response.status === 'error') {
                                    console.error(response.message); // Handle error messages
                                }
                            } catch (e) {
                                console.error('Invalid JSON response', e);
                            }
                        }
                    };
                    xhr.send(`id=${encodeURIComponent(id)}&type=${encodeURIComponent(type)}&action=${encodeURIComponent(action)}`);
                });
            });
        }

        // Bind click events to both button classes
        handleButtonClick('.viewButton');
        handleButtonClick('.editButton');
    });
</script>

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
</body>
</html>