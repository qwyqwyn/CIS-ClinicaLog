<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/dashboard.php');
include('../php/adminnotif.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit;
}

$db = new Database();
$conn = $db->getConnection(); 
 
$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];

$notificationHandler = new AdminNotif($conn);
$notifications = $notificationHandler->getAllNotifications();

function getNotifIcon($status) {  
    switch ($status) {
        case 'read':
            return 'fa-check-circle'; // Example icon
        case 'unread': 
            return 'fa-circle'; // Example icon
        default:
            return 'fa-bell'; // Default icon 
    }
}
  
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
            <div class="page-inner">
            <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                <h3 class="fw-bold mb-3">Notifications</h3>
                    </div>
                    <div class="col-md-2">
                </div>
                </div>
                <div class="row">
                <div class="col-md-2">
                </div>
                <div class="col-md-8">
                <div class="row mt-4"> <!-- Added margin for separation -->
                    <div class="col-md-12">
                        <div class="card card-equal-height">
                        <div class="card-header">
                                <div class="d-flex align-items-center">
                                    
                                <div class="dropdown">
                                    <button
                                        class="btn btn-primary btn-round ms-auto dropdown-toggle"
                                        type="button"
                                        id="dropdownMenuButton"
                                        data-bs-toggle="dropdown"
                                        aria-expanded="false"
                                    >
                                        
                                        Settings
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <li>
                                            <a class="dropdown-item" href="#" id="readAll">Read All</a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="#" id="clearHistory">Clear History</a>
                                        </li>
                                    </ul>
                                </div>

                                </div>
                            </div>  

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-row" class="display table table-striped table-hover">             
                                    <thead>
                                            <tr>
                                                <th>Message</th>
                                               
                                            </tr>
                                        </thead>
                                        <tbody>
                                                <?php if (empty($notifications)) { ?>
                                                    <tr><td colspan="2">No notifications found.</td></tr>
                                                <?php } else { ?>
                                                    <?php foreach ($notifications as $notif) { ?>
                                                        <tr
                                                        class="clickable-row"
                                                            data-id="<?php echo $notif['notif_id']; ?>"
                                                            data-patid="<?php echo $notif['notif_patid']; ?>"
                                                            data-type="<?php echo htmlspecialchars($notif['patient_type']); ?>"
                                                            style="cursor: pointer;" >
                                                        <td 
                                                            colspan="2" 
                                                            
                                                            >
                                                            
                                                                <?php echo htmlspecialchars($notif['patient_name']); ?><br>
                                                                <?php echo htmlspecialchars($notif['notif_message']); ?><br>
                                                                <?php echo date('Y-m-d H:i:s', strtotime($notif['notif_date_added'])); ?><br>

                                                                <?php if ($notif['notif_status'] === 'unread') { ?>
                                                                    <!-- Display Three Circles icon for unread notifications -->
                                                                    <div class="dropdown">
                                                                        <span
                                                                            class="fa fa-ellipsis-h"
                                                                            id="dropdownMenuButton-<?php echo $notif['notif_id']; ?>"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false"
                                                                            style="cursor: pointer;">
                                                                        </span>
                                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-<?php echo $notif['notif_id']; ?>">
                                                                            <li>
                                                                                <a class="dropdown-item mark-as-read" href="#" data-notif-id="<?php echo $notif['notif_id']; ?>">
                                                                                    Mark as Read
                                                                                </a>
                                                                            </li>
                                                                            <li>
                                                                                <a class="dropdown-item delete-notif" href="#" data-notif-id="<?php echo $notif['notif_id']; ?>">
                                                                                    Delete
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                <?php } else { ?>
                                                                    <!-- If already read, show 'Read' status -->
                                                                    <div class="dropdown">
                                                                        <span
                                                                            class="fa fa-check-circle"
                                                                            id="dropdownMenuButton-read-<?php echo $notif['notif_id']; ?>"
                                                                            data-bs-toggle="dropdown"
                                                                            aria-expanded="false"
                                                                            style="cursor: pointer;">
                                                                            Read
                                                                        </span>
                                                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-read-<?php echo $notif['notif_id']; ?>">
                                                                            <li>
                                                                                <a class="dropdown-item delete-notif" href="#" data-notif-id="<?php echo $notif['notif_id']; ?>">
                                                                                    Delete
                                                                                </a>
                                                                            </li>
                                                                        </ul>
                                                                    </div>
                                                                <?php } ?>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tbody>
                                        <td></td>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- End of Consultations List -->
                            </div>
                        </div>
                    </div> <!-- End of Consultations List -->
                    </div>
                    <div class="col-md-2">
                </div>
                </div>
            </div>   
          </div>

    
     

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

 

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    
    <script>
    $(document).ready(function () {
        // Initialize DataTable
        $('#add-row').DataTable({
            "ordering": false,
            "searching": true
        });

        // Load Sidebar
        $("#sidebar").load("sidebar.php", function (response, status, xhr) {
            if (status === "error") {
                console.log("Error loading sidebar: " + xhr.status + " " + xhr.statusText);
                alert("Sidebar failed to load. Please check the console for errors.");
            } else {
                let currentPage = window.location.pathname.split('/').pop();
                $('.nav-item').removeClass('active');
                    $('.nav-item').each(function() {
                        var href = $(this).find('a').attr('href');
                        if (href.indexOf(currentPage) !== -1) {
                            $(this).addClass('active');
                        }
                    });
            }
        });

        // Load Header
        $("#header").load("header.php", function (response, status, xhr) {
            if (status === "error") {
                console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
                alert("Header failed to load. Please check the console for errors.");
            } else {
                console.log("Header loaded successfully.");
            }
        });

        // Notification Actions
        document.getElementById('readAll').addEventListener('click', function (e) {
            e.preventDefault();
            fetch('adminnotifcontrol.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=read_all'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('All notifications marked as read!');
                    location.reload();
                } else {
                    alert('Failed to mark notifications as read.');
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.getElementById('clearHistory').addEventListener('click', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to clear all notifications?')) {
                fetch('adminnotifcontrol.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=clear_history'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('All notifications cleared!');
                        location.reload();
                    } else {
                        alert('Failed to clear notifications.');
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });

        document.querySelectorAll('.mark-as-read').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const notifId = this.getAttribute('data-notif-id');
                let form = new FormData();
                form.append('mark_as_read', notifId);
                fetch('adminnotifcontrol.php', {
                    method: 'POST',
                    body: form
                }).then(() => location.reload());
            });
        });

        document.querySelectorAll('.delete-notif').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const notifId = this.getAttribute('data-notif-id');
                let form = new FormData();
                form.append('delete_notif', notifId);
                fetch('adminnotifcontrol.php', {
                    method: 'POST',
                    body: form
                }).then(() => location.reload());
            });
        });

        // Table Row Click Handling
        const tableBody = document.querySelector("tbody");
        tableBody.addEventListener("click", function (event) {
            if (event.target.closest(".dropdown")) return;

            const row = event.target.closest(".clickable-row");
            if (row) {
                const notifId = row.getAttribute("data-id");
                const patientId = row.getAttribute("data-patid");
                const patientType = row.getAttribute("data-type");

                if (!notifId || !patientId || !patientType) {
                    Swal.fire('Error', 'Invalid or missing notification or patient data.', 'error');
                    return;
                }

                $.ajax({
                    url: "transacpatientview.php",
                    method: "POST",
                    data: { notif_id: notifId, patient_id: patientId, patient_type: patientType },
                    dataType: "json",
                    success: function (response) {
                        if (response.status === 'success') {
                            let redirectUrl;
                            switch (patientType) {
                                case 'Faculty': redirectUrl = "patient-facultyprofile.php"; break;
                                case 'Student': redirectUrl = "patient-studprofile.php"; break;
                                case 'Staff': redirectUrl = "patient-staffprofile.php"; break;
                                case 'Extension': redirectUrl = "patient-extensionprofile.php"; break;
                                default: Swal.fire('Error', 'Unknown patient type.', 'error'); return;
                            }
                            window.location.href = redirectUrl;
                        } else {
                            Swal.fire('Error', response.message || 'Unexpected error occurred.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Failed to handle request.', 'error');
                    }
                });
            }
        });
    });
</script>

</body>
</html>
