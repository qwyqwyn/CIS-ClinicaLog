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
                        <h1>System Back Up & Restore</h1>
                    </div>
                </div>
            </div>
            <div class="page-inner">
                <div class="row">
                    <div class="col-md-6"> 
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                <h4 class="card-title">Back Up Database</h4>
                                </div>
                            </div>
                            <div class="card-body" id="BackupInfo">
                                <p>Click the button below to back up the current database. The backup file will be saved to the server or available for download.</p>
                                <button id="backupBtn" class="btn btn-primary">Back Up Database</button>
                                <div id="backupProgress" style="display: none; margin-top: 10px;">
                                    <p>Backing up...</p>
                                    <div class="progress">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;" id="backupProgressBar"></div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                            <h4 class="card-title">Restore Database</h4>
                            </div>
                        </div>
                        <div class="card-body" id="RestoreInfo">
                            <p>To restore the database, select a backup file and click the "Restore Database" button. This will overwrite the existing database.</p>
                            <input type="file" id="restoreFile" class="form-control mb-3" accept=".sql, .zip">
                            <button id="restoreBtn" class="btn btn-danger">Restore Database</button>
                            <div id="restoreProgress" style="display: none; margin-top: 10px;">
                                <p>Restoring...</p>
                                <div class="progress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%;" id="restoreProgressBar"></div>
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

    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
<script>
    $(document).ready(function () {
    // Handle Backup Button Click
    $("#backupBtn").click(function () {
    $("#backupProgress").show(); // Show progress bar
    $("#backupProgressBar").css("width", "0%");

    $.ajax({
        url: "backupcontrol.php", // Replace with your PHP script path
        method: "POST",
        dataType: "json", // Expect JSON response from PHP
        beforeSend: function () {
            $("#backupProgressBar").css("width", "50%");
        },
        success: function (response) {
            if (response.success) {
                $("#backupProgressBar").css("width", "100%");
                setTimeout(() => {
                    $("#backupProgress").hide();
                    Swal.fire({
                        icon: "success",
                        title: "Backup Successful", 
                        text: "File saved: " + response.filePath,
                        confirmButtonText: "Download Backup",
                    }).then(() => {
                        // Create a temporary link element
                        const link = document.createElement('a');
                        link.href = response.filePath;
                        link.download = response.filePath.split('/').pop(); // Use the filename for download

                        // Append the link to the body (it needs to be part of the document to trigger the download)
                        document.body.appendChild(link);

                        // Trigger the download by simulating a click
                        link.click();

                        // Remove the link from the document after the download starts
                        document.body.removeChild(link);

                        // After download, redirect to another page
                        window.location.href = "backuprestore.php"; // Replace with the actual URL
                    });
                }, 500);

            } else {
                $("#backupProgress").hide();
                Swal.fire("Error", response.message || "Backup failed.", "error");
            }
        },
        error: function (xhr, status, error) {
            $("#backupProgress").hide();
            Swal.fire("Error", "Backup failed: " + error, "error");
        },
    });
});


$("#restoreBtn").click(function () {
    const fileInput = $("#restoreFile")[0];

    // Check if a file is selected
    if (fileInput.files.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "No File Selected",
            text: "Please select a backup file to restore.",
        });
        return;
    }

    const formData = new FormData();
    formData.append("backup_file", fileInput.files[0]);

    // Show the progress bar
    $("#restoreProgress").show();
    $("#restoreProgressBar").css("width", "0%");

    $.ajax({
        url: "restorecontrol.php", // Backend script for restore
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            // Set initial progress
            $("#restoreProgressBar").css("width", "50%");
        },
        success: function (response) {
            const res = JSON.parse(response); // Parse the JSON response
            
            $("#restoreFile").val("");
            
            // Update progress bar
            $("#restoreProgressBar").css("width", "100%");

            // Hide the progress bar after a short delay
            setTimeout(() => {
                $("#restoreProgress").hide();

                // Display the result using SweetAlert
                if (res.type === "success") {
                    Swal.fire({
                        icon: "success",
                        title: "Restore Successful",
                        text: res.message, // Display success message
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Restore Failed",
                        text: res.message, // Display error message
                    });
                }
            }, 500);
        },
        error: function (xhr, status, error) {
            $("#restoreProgress").hide();
            Swal.fire({
                icon: "error",
                title: "Restore Failed",
                text: "An unexpected error occurred: " + error,
            });
        },
    });
});



});

</script>


</body>
</html>
