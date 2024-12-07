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

?> 

<!DOCTYPE html> 
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Transaction Report</title> 
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

        <!-- Main Panel -->
        <div class="main-panel">
            <!-- Header -->
            <div class="main-header" id="header"></div>

            <!-- Main Content -->
            <div class="container" id="content">
                <div class="page-inner">
                <div class="page-inner">
                <div class="page-inner">
                                <!-- Year Selection Dropdown -->


                                <div class="row">
                                            <div class="col-md-9 mb-3">
                                            <label for="yearSelect" class="form-label"></label>
                                                    <h1>Yearly Statistic Report of Services</h1>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="yearSelect" class="form-label">Select a Year</label>
                                                <select id="yearSelect"  class="form-control" onchange="loadDataForYear(this.value)">
                                                    
                                                </select>
                                            </div>
                                        </div>

                                <!-- Start All Patient Records Report -->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-round">
                                        <div class="card-header">
                                            <div class="card-head-row">
                                            <div class="card-title">Summary of All Services</div>
                                            <div class="card-tools">
                                                <a href="#" class="btn btn-label-success btn-round btn-sm me-2" onclick="exportToExcel()">
                                                <span class="btn-label"><i class="fa fa-pencil"></i></span>
                                                Export
                                                </a>
                                            </div> 
                                            </div>
                                        </div>
                                        <div class="card-container" style="display: flex; flex-direction: column; gap: 20px;">
                                            <div class="card-body" style="flex: 1;">
                                            <div class="chart-container" style="min-height: 255px;"> 
                                                <canvas id="lineChart"></canvas>
                                            </div>
                                            </div>
                                        </div>
                                        </div>
                                    </div>                    
                                    </div>
                                
                                <h1></h1>
                                <h1></h1>
                                <h1></h1>
                                <h1>Number of Clients Served Each Transactions</h1>
                                 <!-- Start All Transactions Report -->
                                 <div class="row">
                                    <div class="col-md-12">
                                        <div class="card card-round">
                                        <div class="card-header">
                                            <div class="card-head-row">
                                            <div class="card-title">Total for All Transactions</div>
                                            <div class="card-tools">
                                                    <a
                                                        href="#"
                                                        class="btn btn-label-success btn-round btn-sm me-2"
                                                        onclick="reportToExportAllTable()">
                                                    >
                                                        <span class="btn-label">
                                                            <i class="fa fa-pencil"></i>
                                                        </span>
                                                        Export
                                                    </a>
                                                </div>
                                            </div>
                                            <div id="loadingMessage" style="display: none;">Loading, please wait...</div>
                                        </div>
                                        <div class="card-body">
                                            <div class="chart-container" style="min-height: 375px">
                                            <canvas id="statisticsChart"></canvas>
                                            </div>
                                            <div id="myChart"></div>
                                        </div>
                                        </div>
                                    </div>                   
                                </div>
                
                                <!-- Start All Medical Certificate Issuance Report -->
                                <div class="row">
                                <div class="col-md-12">
                                                <div class="card card-equal-height">
                                                <div class="card-header">
                                                    <div class="d-flex align-items-center">
                                                        <h4 class="card-title">Medical Certificate Issuance</h4>
                                                        <div class="card-tools ms-auto"> <!-- Add ms-auto to align the button to the right -->
                                                            
                                                        </div>
                                                    </div>
                                                </div>
                                                    <div class="card-body">
                                                    </div>
                                                        <div class="table-responsive">
                                                        <table id="medcert" class="display table table-striped table-hover">
                                                        <thead>
                                                                <tr>
                                                                    <th></th> <!-- Empty header for the row labels column -->
                                                                    <th>Faculty</th>
                                                                    <th>Staff</th>
                                                                    <th>Student</th>
                                                                    <th>Extension</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </thead>
                                                            <tfoot>
                                                                <tr>
                                                                    <th></th> <!-- Empty footer for the row labels column -->
                                                                    <th>Faculty</th>
                                                                    <th>Staff</th>
                                                                    <th>Student</th>
                                                                    <th>Extension</th>
                                                                    <th>Total</th>
                                                                </tr>
                                                            </tfoot>
                                                            <tbody>
                                                                <tr>
                                                                    <td>January</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>February</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td> 
                                                                </tr>
                                                                <tr>
                                                                    <td>March</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>April</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>May</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>June</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>July</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>August</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>September</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>October</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>November</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>December</td>
                                                                    <td></td> <td></td> <td></td> <td></td> <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
    
                                
                                <!-- Start All Check-Up Report -->
                                <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-equal-height">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <h4 class="card-title">Medical Consultation and Treatment</h4>
                                            <div class="card-tools ms-auto"> <!-- Add ms-auto to align the button to the right -->
                                                
                                            </div>
                                        </div>
                                    </div>
                                        <div class="card-body">
                        
                                        </div>

                                            <div class="table-responsive">
                                            <table id="consult" class="display table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th></th> <!-- Empty header for the row labels column -->
                                                        <th>Faculty</th>
                                                        <th>Staff</th>
                                                        <th>Student</th>
                                                        <th>Extension</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th> <!-- Empty footer for the row labels column -->
                                                        <th>Faculty</th>
                                                        <th>Staff</th>
                                                        <th>Student</th>
                                                        <th>Extension</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <tr>
                                                        <td>January</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>February</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>March</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>April</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>May</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>June</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>July</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>August</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>September</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>October</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>November</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>December</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                <div class="col-md-12">
                                    <div class="card card-equal-height">
                                    <div class="card-header">
                                        <div class="d-flex align-items-center">
                                            <h4 class="card-title">Dental Check Up & Treatment</h4>
                                            <div class="card-tools ms-auto"> <!-- Add ms-auto to align the button to the right -->
                                               
                                            </div>
                                        </div>
                                    </div>
                                        <div class="card-body">
                        
                                        </div>

                                            <div class="table-responsive">
                                            <table id="checkup" class="display table table-striped table-hover">
                                            <thead>
                                                    <tr>
                                                        <th></th> <!-- Empty header for the row labels column -->
                                                        <th>Faculty</th>
                                                        <th>Staff</th>
                                                        <th>Student</th> 
                                                        <th>Extension</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tfoot>
                                                    <tr>
                                                        <th></th> <!-- Empty footer for the row labels column -->
                                                        <th>Faculty</th>
                                                        <th>Staff</th>
                                                        <th>Student</th>
                                                        <th>Extension</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </tfoot>
                                                <tbody>
                                                    <tr>
                                                        <td>January</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>February</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>March</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>April</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>May</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>June</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>July</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>August</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>September</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>October</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>November</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
                                                    <tr>
                                                        <td>December</td>
                                                        <td></td> <td></td> <td></td> <td></td> <td></td>
                                                    </tr>
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
            <!-- End Main Content -->
        </div>
        <!-- End Main Panel -->
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>
    <script src="../assets/js/reportjs.js"></script>
    


    
    <script>
        $(document).ready(function() {
            // Dynamically load the sidebar
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
            $("#allPatientRecords").load("patientrecordReport.php", function(response, status, xhr) {
                if (status == "error") {
                    console.log("Error loading patient records: " + xhr.status + " " + xhr.statusText);
                }
            });
        });
    </script>

</body> 
</html>
