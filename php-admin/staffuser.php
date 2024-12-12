<?php
session_start();
include('../database/config.php');
include('../php/user.php');
 
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit; 
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn);  
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum);  
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
    <title>Clinic Staff User</title>
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
      
    <style>
      .sidebar {
          transition: background 0.3s ease;
          /* Initial background */
          background: linear-gradient(to bottom, #DB6079, #DA6F65, #E29AB4);
      }
      .logo-header {
          transition: background 0.3s ease;
      }
      .nav-item.active {
            background-color: rgba(0, 0, 0, 0.1); 
            color: #fff; 
        }

        .nav-item.active i {
            color: #fff;
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
              <div class="col-md-12"> 
                <div class="card">
                  <div class="card-header"> 
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">Add User</h4>
                    </div>
                  </div>
                  <div class="card-body">
                    <!-- Modal -->

                    <div class="table-responsive">
                    <table id="add-row" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>System Role</th>
                                <th>Date Added</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Profile</th>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>System Role</th>
                                <th>Date Added</th>
                                <th>Status</th>
                            </tr>
                        </tfoot> 
                        <tbody>
                            <?php
                               
                                $nodes = $user->getAllUsers();
                                foreach ($nodes as $node) {
                                    // Skip the user if their ID matches the excluded ID
                                    if ($node->user_idnum === $user_idnum) {
                                        continue;  // Skip this iteration and move to the next node
                                    }

                                    $fullName = "{$node->user_lname}, {$node->user_fname} {$node->user_mname}";
                                    $statusColor = ($node->user_status === 'Active') ?  '#77dd77' : '#ff6961';
                                    $statusText = ucfirst($node->user_status); 
                                    $profilePic = !empty($node->user_profile) ? "../uploads/{$node->user_profile}" : '../uploads/default-image.jpg';

                                    echo "<tr data-id='{$node->user_idnum}' data-lname='{$node->user_lname}' data-fname='{$node->user_fname}' data-mname='{$node->user_mname}' data-email='{$node->user_email}' data-position='{$node->user_position}' data-role='{$node->user_role}' data-dateadded='{$node->user_dateadded}' data-status='{$node->user_status}'> 
                                            <td>
                                                <img src='" . htmlspecialchars($profilePic) . "' 
                                                    alt='Profile Picture' 
                                                    style='width: 50px; height: 50px; border-radius: 50%;'>
                                            </td>
                                            <td>{$node->user_idnum}</td>
                                            <td>{$fullName}</td>
                                            <td>{$node->user_email}</td>
                                            <td>{$node->user_position}</td>
                                            <td>{$node->user_role}</td>
                                            <td>{$node->user_dateadded}</td>
                                            <td>
                                                <span style='
                                                    display: inline-block; 
                                                    padding: 5px 10px;
                                                    border-radius: 50px;
                                                    background-color: {$statusColor};
                                                    color: white; 
                                                    text-align: center;
                                                    min-width: 60px;'>
                                                    {$statusText}
                                                </span>
                                            </td>
                                          </tr>";
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

    <!--   Core JS Files   -->
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
     $(document).ready(function () {
    // Initialize DataTable
    $("#add-row").DataTable({
        pageLength: 7,
    });
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
