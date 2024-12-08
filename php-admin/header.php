<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/adminnotif.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: ../php-login/index.php'); 
  exit; 
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum);
$adminNotif = new AdminNotif($db); 



// Function to format time ago
function timeAgo($timestamp) {
  $time_ago = strtotime($timestamp);
  $current_time = time();
  $time_difference = $current_time - $time_ago;
  $seconds = $time_difference;
  $minutes      = round($seconds / 60);           // value 60 is seconds
  $hours        = round($seconds / 3600);         // value 3600 is 60 minutes * 60 sec
  $days         = round($seconds / 86400);        // value 86400 is 24 hours * 60 minutes * 60 sec
  $weeks        = round($seconds / 604800);       // value 604800 is 7 days * 24 hours * 60 minutes * 60 sec
  $months       = round($seconds / 2629440);      // value 2629440 is ((365+365)/2/12) days * 24 hours * 60 minutes * 60 sec
  $years        = round($seconds / 31553280);     // value 31553280 is ((365+365)/2) days * 24 hours * 60 minutes * 60 sec

  if ($seconds <= 60) {
      return "Just Now";
  } else if ($minutes <= 60) {
      if ($minutes == 1) {
          return "one minute ago";
      } else {
          return "$minutes minutes ago";
      }
  } else if ($hours <= 24) {
      if ($hours == 1) {
          return "an hour ago";
      } else {
          return "$hours hours ago";
      }
  } else if ($days <= 7) {
      if ($days == 1) {
          return "yesterday";
      } else {
          return "$days days ago";
      }
  } else if ($weeks <= 4.3) { // 4.3 == 30/7
      if ($weeks == 1) {
          return "a week ago";
      } else {
          return "$weeks weeks ago";
      }
  } else if ($months <= 12) {
      if ($months == 1) {
          return "one month ago";
      } else {
          return "$months months ago";
      }
  } else {
      if ($years == 1) {
          return "one year ago";
      } else {
          return "$years years ago";
      }
  }
}

// Function to get the icon for the notification
function getNotifIcon($status) {
  switch ($status) {
      case 'primary':
          return 'fa-user-plus';
      case 'success':
          return 'fa-comment';
      case 'danger':
          return 'fa-heart';
      default:
          return 'fa-bell';
  }
}

// Function to get the class for the notification
function getNotifClass($status) {
  switch ($status) {
      case 'primary':
          return 'success';
      case 'success':
          return 'success';
      case 'danger':
          return 'danger';
      default:
          return 'info';
  }
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>ClinicaLog Dashboard</title> 
    <meta
      content="width=device-width, initial-scale=1.0, shrink-to-fit=no"
      name="viewport"
    />
    <link 
      rel="icon"
      href="../assets/img/ClinicaLog.ico"
      type="image/x-icon" 
    />

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
    <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark"> 
              <a href="index.php" class="logo">
                <img
                  src="../assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>  
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div>
                  <input
                    type="text"
                    placeholder="Search ..."
                    class="form-control"
                  />
                </div>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#"
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>

                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a class="nav-link dropdown-toggle" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <i class="fa fa-bell"></i>
                     <span class="notification"><?php echo $adminNotif->getUnreadCount(); ?></span>

                  </a>
                  <?php
                  $notifications = $adminNotif->getFourNewestNotifications();
                  ?>
                  <ul class="dropdown-menu notif-box animated fadeIn" aria-labelledby="notifDropdown">
                      <li>
                          <div class="dropdown-title">
                              You have <?php echo count($notifications); ?> new notifications
                          </div>
                      </li>
                      <li>
                          <div class="notif-scroll scrollbar-outer">
                              <div class="notif-center">
                                  <?php
                                  foreach ($notifications as $notif) {
                                      $timeAgo = timeAgo($notif['notif_date_added']);
                                      $patientName = $notif['patient_name']; 
                                      echo '
                                      <a href="adminnotiftable.php">
                                          <div class="notif-icon notif-' . getNotifClass($notif['notif_status']) . '">
                                              <i class="fa ' . getNotifIcon($notif['notif_status']) . '"></i>
                                          </div>
                                          <div class="notif-content">
                                              <span class="block">' . $patientName . ' - ' . $notif['notif_message'] . '</span>
                                              <span class="time">' . $timeAgo . '</span>
                                          </div>
                                      </a>';
                                  }
                                  ?>
                              </div>
                          </div>
                      </li>
                      <li>
                          <a class="see-all" href="adminnotiftable.php">
                              See all notifications<i class="fa fa-angle-right"></i>
                          </a>
                      </li>
                  </ul>
              </li>


                <li class="nav-item topbar-user dropdown hidden-caret">
                <?php if ($userData): ?>
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false" 
                  >
                    <div class="avatar-sm">
                      <img
                      src='/php-admin/uploads/<?php echo !empty($userData['user_profile']) ? htmlspecialchars($userData['user_profile']) : 'default-image.jpg'; ?>'
                      alt='Profile Picture'
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?php echo htmlspecialchars($userData['user_fname']); ?></span>
                    </span>
                  </a>
                  <?php else: ?>
                      <p>User data not found.</p>
                  <?php endif; ?>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                          <img
                              src='/php-admin/uploads/<?php echo !empty($userData['user_profile']) ? htmlspecialchars($userData['user_profile']) : 'default-image.jpg'; ?>'
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4><?php echo htmlspecialchars($userData['user_fname']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($userData['user_email']); ?></p>
                            <a
                              href="viewprofile.php"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            >
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="setting.php">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" id="logoutLink">Logout</a>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="../assets/js/setting-demo.js"></script>
    <script src="../assets/js/demo.js"></script>
    <script>
      $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#177dff",
        fillColor: "rgba(23, 125, 255, 0.14)",
      });

      $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#f3545d",
        fillColor: "rgba(243, 84, 93, .14)",
      });

      $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
        type: "line",
        height: "70",
        width: "100%",
        lineWidth: "2",
        lineColor: "#ffa534",
        fillColor: "rgba(255, 165, 52, .14)",
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