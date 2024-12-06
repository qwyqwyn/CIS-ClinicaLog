<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/medreport.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit; 
}

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn); 
$medicineManager = new MedicineManager($conn);
$user_idnum = $_SESSION['user_idnum'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $monthYear = $_POST['monthYear'];
    list($year, $month) = explode('-', $monthYear);
    
    $firstDayOfMonth = date("Y-m-d", strtotime("$year-$month-01"));
    $quarterYear = $_POST['quarteryear']; 
    $quarter = $_POST['quarter'];

    switch ($quarter) { 
        case '1':
            $startOfQuarter = date("Y-m-d", strtotime("$quarterYear-01-01")); 
            $endOfQuarter = date("Y-m-d", strtotime("$quarterYear-03-31")); 
            break;
        case '2':
            $startOfQuarter = date("Y-m-d", strtotime("$quarterYear-04-01")); 
            $endOfQuarter = date("Y-m-d", strtotime("$quarterYear-06-30"));
            break;
        case '3': 
            $startOfQuarter = date("Y-m-d", strtotime("$quarterYear-07-01"));
            $endOfQuarter = date("Y-m-d", strtotime("$quarterYear-09-30")); 
            break;
        case '4':
            $startOfQuarter = date("Y-m-d", strtotime("$quarterYear-10-01")); 
            $endOfQuarter = date("Y-m-d", strtotime("$quarterYear-12-31"));
            break;
        default:
            $startOfQuarter = null;
            $endOfQuarter = null;
    } 

    $medicineManager->fetchAndStoreMedstocks($firstDayOfMonth, $startOfQuarter, $endOfQuarter);

    $response = [
        'status' => 'success',
        'message' => 'Data submitted successfully.',
        'firstDayOfMonth' => $firstDayOfMonth,
        'quarterStart' => $startOfQuarter,
        'quarterEnd' => $endOfQuarter, 
        'medstocks' => $medicineManager->getAllMedstocksAsArray()
    ];
    echo json_encode($response);

    exit;
}

?>

<!DOCTYPE html> 
<html lang="en"> 
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>CIS:Clinicalog</title> 
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" /> 
    <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

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
                        <h1>Inventory Report of Medicines</h1>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <div class="d-flex align-items-center">
                                        <h4 class="card-title">Personal Details</h4>
                                    </div>
                                </div>
                                <form id="balanceForm">
                                    <div class="card-body" id="InputInfo">
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <label for="monthYear" class="form-label">Select Month & Year as Start Balance</label>
                                                <input type="month" id="monthYear" class="form-control" required>
                                            </div>
                                            <div class="col-md-3 mb-3"> 
                                                <label for="quarter" class="form-label">Select a Quarter:</label>
                                                <select id="quarter" class="form-control" required>
                                                    <option value="" disabled selected>Select Quarter</option>
                                                    <option value="1">1st Quarter</option>
                                                    <option value="2">2nd Quarter</option>
                                                    <option value="3">3rd Quarter</option>
                                                    <option value="4">4th Quarter</option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label for="quarteryear" class="form-label">Select a Year for Selected Quarter</label>
                                                <select id="quarteryear" class="form-control" required>
                                                    <option value="" disabled selected>Select Year</option>
                                                    <script>
                                                        const yearSelect = document.getElementById('quarteryear');
                                                        const startYear = 2020; // Adjust as needed
                                                        const currentYear = new Date().getFullYear();
                                                        for (let year = startYear; year <= currentYear + 5; year++) {
                                                            const option = document.createElement('option');
                                                            option.value = year;
                                                            option.textContent = year;
                                                            yearSelect.appendChild(option);
                                                        }
                                                    </script> 
                                                </select>
                                            </div>
                                            <div class="col-md-3 mb-3"> 
                                                <label class="form-label">&nbsp;</label>
                                                <button type="submit" class="btn btn-primary form-control">Filter</button>
                                            </div>
                                        </div>
                                    </div>
                                
                            </div>
                        </div>
                    </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">List of Medicine Issued</h4>
                                    <button
                                        type="button"
                                        id="exportButton"
                                        name="dlexcel"
                                        class="btn btn-primary btn-round ms-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addMedicalRecModal"
                                    >
                                        <i class="fa fa-xls"></i>
                                        Export
                                    </button>                    
                                </div>
                            </div>
                            </form>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-row" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Balance Month</th>
                                                <th>Received for the Period</th>
                                                <th>Total Start Balance</th>
                                                <th>Unit</th>
                                                <th>Item</th>
                                                <th>Issuance (Mabini)</th>
                                                <th>Issuance (Apokon)</th>
                                                <th>End Balance</th>
                                                <th>Expiry Date</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Balance Month</th>
                                                <th>Received for the Period</th>
                                                <th>Total Start Balance</th> 
                                                <th>Unit</th>
                                                <th>Item</th>
                                                <th>Issuance (Mabini)</th>
                                                <th>Issuance (Apokon)</th>
                                                <th>End Balance</th>
                                                <th>Expiry Date</th>
                                            </tr>
                                        </tfoot> 
                                        <tbody>
                                            <tr>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
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

    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>

    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // DataTable initialization

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


            $("#balanceForm").on("submit", function(event) {
                event.preventDefault();

                var formData = {
                    monthYear: $("#monthYear").val(),
                    quarter: $("#quarter").val(),
                    quarteryear: $("#quarteryear").val(),
                };

                $.ajax({
                    url: 'inventory.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            console.log("Data received successfully:", response);

                            let table = $("#add-row").DataTable();
                            table.clear();  
                            response.medstocks.forEach(function(medstock) {
                                table.row.add([ 
                                    medstock.medicine_balance_month,
                                    medstock.medstock_added,
                                    medstock.total_start_balance,
                                    medstock.unit,
                                    medstock.item,
                                    medstock.total_issued,
                                    medstock.total_prescribed,
                                    medstock.end_balance,
                                    medstock.expiry_date
                                ]);
                            });
                            table.draw();  
                        } else {
                            console.error("Error: Unexpected response status");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error: ", xhr.responseText);
                    }
                });
            });

            $("#exportButton").on("click", function() {
                let table = $("#add-row").DataTable();
                let data = [];
                
                const headers = [
                    "Month Balance", "Added Stock", "Start Balance", "Unit",
                    "Item", "Prescribed", "Issued", "End Balance", "Expiry Date"
                ];
                data.push(headers);
                
                table.rows().every(function() {
                    const rowData = this.data();
                    const row = [
                        rowData[0], 
                        rowData[1], 
                        rowData[2], 
                        rowData[3], 
                        rowData[4], 
                        rowData[5], 
                        rowData[6], 
                        rowData[7], 
                        rowData[8]  
                    ];
                    data.push(row);
                });
                
                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.aoa_to_sheet(data);
                XLSX.utils.book_append_sheet(wb, ws, "Medstock Data");
                
                const monthYear = $("#monthYear").val();
                const quarter = $("#quarter").val();
                const quarteryear = $("#quarteryear").val();
                
                const fileName = `MedicineReport_${monthYear}_Q${quarter}_of_${quarteryear}.xlsx`;
                
                XLSX.writeFile(wb, fileName);
            });






        });
    </script>
</body>
</html>
