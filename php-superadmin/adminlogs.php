<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/dashboard.php');
include('../php/adminlogs.php');


if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php');  
    exit; 
} 

$db = new Database();
$conn = $db->getConnection();  

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];

$logs = new SystemLogs($conn);

$logData = $logs->getAllSystemLogs(); 

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
                <div
                class="d-flex align-items-left align-items-md-center flex-column flex-md-row pt-2 pb-4"
                >
                    <div class="col-md-12">
                        <div class="card card-equal-height">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Admin Logs</h4>
                                    <button
                                        class="btn btn-primary btn-round ms-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addRowModal"
                                        onclick="printTable()"
                                    >
                                        <i class="fa fa-arrow"></i>
                                        Print
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="admin-logs" class="display table table-striped table-hover">             
                                            <thead>
                                                <tr>
                                                <th>No.</th>
                                                <th>ID Number</th>
                                                <th>Name</th>
                                                <th>Date & Time</th>
                                                <th>Action Made</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                <th>No.</th>
                                                <th>ID Number</th>
                                                <th>Name</th>
                                                <th>Date & Time</th>
                                                <th>Action Made</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                            <?php 
                                                $counter = 1;

                                                while ($row = $logData->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<tr>
                                                            <td>" . $counter++ . "</td>
                                                            <td>" . htmlspecialchars($row['idnum']) . "</td>
                                                            <td>" . htmlspecialchars($row['name']) . "</td>
                                                            <td>" . htmlspecialchars($row['date']) . " " . htmlspecialchars($row['time']) . "</td>
                                                            <td>" . htmlspecialchars($row['action']) . "</td>
                                                          </tr>";
                                                }
                                                
                                                ?>
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                </div> 
                            </div>
                        </div>
                    </div> <!-- End of Consultations List -->
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
        $("#admin-logs").DataTable({
        "paging": false // Disable pagination
    });
       
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
function printTable() {
    var printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write('<html><head><title>System Logs</title>');
        
    // Add custom print styles
    printWindow.document.write(`
        <style>
            @media print {
                body {
                    font-family: Arial, sans-serif;
                    margin: 20px;
                }
                h1 {
                    text-align: center; /* Center the title */
                    font-size: 16px; /* Set the font size to 16px */
                    margin-bottom: 20px; /* Add some space below the title */
                }
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                th, td {
                    border: 1px solid #000;
                    padding: 8px;
                    text-align: left;
                }
                th {
                    background-color: #f2f2f2;
                    font-weight: bold;
                }
                tr:nth-child(even) {
                    background-color: #f9f9f9;
                }
                h2, h3, h4, h5, h6 {
                    page-break-after: avoid;
                }
                button {
                    display: none; /* Hide buttons */
                }
                .dataTables_wrapper, .dataTables_paginate, .dataTables_length {
                    display: none !important; /* Hide DataTable controls */
                }
            }
        </style>
    `);
    
    printWindow.document.write('</head><body>');
    
    // Add a title before the table
    printWindow.document.write('<h1>Clinicalog: Campus Clinic Management System Logs</h1>'); // Title centered with specified size
    
    // Append the table HTML
    printWindow.document.write(document.getElementById('admin-logs').outerHTML);
    
    printWindow.document.write('</body></html>');
    
    // Close the document for writing
    printWindow.document.close();
    
    // Wait for the content to load before printing
    printWindow.onload = function() {
        printWindow.print();
        printWindow.close();
    };
}


</script>
</body>
</html>
