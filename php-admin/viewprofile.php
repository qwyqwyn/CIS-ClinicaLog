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
    <title>CIS:Clinicalog</title> 
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
            <div class="page-inner">
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                <h3 class="fw-bold mb-3">Your Profile</h3>
                    </div>
                    <div class="col-md-2">
                </div>
                </div>
                <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                    <div class="card">
                    <div class="profile-image">
                        <div class="card-header">
                        <img id="profilePic" src="/php-admin/uploads/<?php echo htmlspecialchars($userData['user_profile']); ?>" alt="" />  
                            <div class="row" >                
                                <span style="
                                    display: inline-block;
                                    padding: 5px 10px;
                                    border-radius: 50px;
                                    background-color: #DA6F65; 
                                    color: white; 
                                    text-align: center;
                                    min-width: 60px;">
                                    <?php echo htmlspecialchars($userData['user_position']); ?>
                            </span>  
                        </div>         
                        </div>
                    </div>
                        <div class="row" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
                            <h5 style="color: #59535A; margin: 0;">#<?php echo htmlspecialchars($userData['user_idnum']); ?></h5>
                            <h5 style="margin: 0;">
                                <span id="lastName"><?php echo htmlspecialchars($userData['user_lname']); ?></span><span>, </span><span id="firstName"><?php echo htmlspecialchars($userData['user_fname']); ?></span> <span id="middleName"><?php echo htmlspecialchars($userData['user_mname']); ?></span>
                            </h5>
                            <h5 style="color: #59535A; margin: 0;">Email: <?php echo htmlspecialchars($userData['user_email']); ?></h5>
                            <h5 style="color: #59535A; margin: 0;">Role: <?php echo htmlspecialchars($userData['user_role']); ?></h5>
                            <p style="color: #888888; margin-top: 5px;">Account Since: <?php echo htmlspecialchars($userData['user_dateadded']); ?></p>
                    </div>
                    </div>
                    </div>
                    <div class="col-md-2">
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
