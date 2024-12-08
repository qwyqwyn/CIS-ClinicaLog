<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit; 
  }


include('../database/config.php');
include('../php/user.php');
include('../php/transaction.php');

$db = new Database();
$conn = $db->getConnection();

$transac = new TransacManager($conn);

$user_idnum = $_SESSION['user_idnum']; 

$patientId = isset($_GET['id']) ? $_GET['id'] : null;
$patientType = isset($_GET['patient_patienttype']) ? $_GET['patient_patienttype'] : null;

$patientDetails = null;

$medicineId = isset($_GET['id']) ? $_GET['id'] : null; 

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
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.2/xlsx.full.min.js"></script>


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
    <link rel="stylesheet" href="../css/transaction.css" />

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
        <!-- End Header -->

        <!-- Main Content -->
        <div class="container" id="content">
            <div class="page-inner">
                <div class="page-inner">
                <div class="row"> 
                <div class="col-md-11 mb-3">
                    <h1 class="fw-bold mb-3">Manage Transaction</h1>
                </div>
                <div class="col-md-1  mb-3">
                    <button onclick="exportToExcel()" class="btn btn-primary">Export</button>
                </div>
                </div>
                <div
                    class="modal fade"
                    id="addRowModal"
                    tabindex="-1"
                    role="dialog"
                    aria-hidden="true"
                >
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header border-0">
                            <h5 class="modal-title">
                              <span class="fw-mediumbold"> New</span>
                              <span class="fw-mediumbold"> Transaction</span>
                            </h5>
                            <button
                              type="button"
                              class="close"
                              data-bs-dismiss="modal"  
                              aria-label="Close" 
                            >
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div> 
                          <div class="modal-body">
                            <p class="small">
                              
                            </p>
                             <!-- Start Add Modal Form-->
                             <form class="form" action="transacontrol.php" method="POST" enctype="multipart/form-data">
                             <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>

                             <div class="row">
                             <div class="col-md-12 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="pname">Search by Name or ID:</label>
                                        <input type="text" id="pname" name="pname" class="form-control" placeholder="Search" autocomplete="off" required>
                                        <div class="form-control" id="suggestions" style="display: none;"></div>
                                        <!-- Hidden form field to store selected patient ID -->
                                        <input type="hidden" id="selected_patient_id" name="selected_patient_id" style="display:none;">
                                    </div>
                                </div>
                                    <div class="col-md-12 mb-3">
                                    <div class="form-group mb-3">
                                    <label for="transac_purpose">Purpose</label>
                                        <select id="transac_purpose" name="transac_purpose" class="form-control form-select" required>
                                            <option value="" disabled selected>Select Purpose</option>
                                            <option value="Medical Certificate Issuance">Medical Certificate Issuance</option>
                                            <option value="Dental Check Up & Treatment">Dental Check Up & Treatment</option>
                                            <option value="Medical Consultation and Treatment">Medical Consultation and Treatment</option>
                                        </select>
                                    </div>
                                    </div>
                                    </div>
                              <div class="modal-footer border-0">
                              <button type="submit" class="btn btn-primary" name="addtransac">Add</button>
                              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                              </div>
                            </form>
                             <!-- End Add Modal Form-->
                          </div>
                        </div>
                      </div>
                    </div>
                <!-- Consultation Edit Modal -->
                <div class="modal fade" id="editRowModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">
                                    <span class="fw-mediumbold">Edit</span>
                                    <span class="fw-light"> Transaction </span>
                                </h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="edit-exit">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                    <form class="form" action="transacontrol.php" method="POST" enctype="multipart/form-data">
                                    <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>

                                    <div class="row">
                                    <div class="col-md-12 mb-3">
                                            <div class="form-group mb-3">
                                                <input type="hidden" id="transac_id" name="transac_id">
                                                <label for="pname">Search by Name or ID:</label>
                                                <input type="text" id="edit_pname" name="edit_pname" class="form-control" required>
                                                <div class="form-control" id="edit_suggestions" style="display: none;"></div>
                                                <!-- Hidden form field to store selected patient ID -->
                                                <input type="hidden" id="edit_patient_id" name="edit_patient_id" >
                                            </div>
                                        </div>
                                            <div class="col-md-12 mb-3">
                                            <div class="form-group mb-3">
                                            <label for="transac_purpose">Purpose</label>
                                                <select id="edit_purpose" name="edit_purpose" class="form-control form-select" required>
                                                    <option value="" disabled selected>Select Purpose</option>
                                                    <option value="Medical Certificate Issuance">Medical Certificate Issuance</option>
                                                    <option value="Dental Check Up & Treatment">Dental Check Up & Treatment</option>
                                                    <option value="Medical Consultation and Treatment">Medical Consultation and Treatment</option>
                                                </select>
                                            </div>
                                            </div>
                                            </div>
                                    <div class="modal-footer border-0">
                                    <button type="submit" class="btn btn-primary" name="edittransac">Save</button>
                                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
    

                <!-- List of Consultations -->
                <div class="row mt-4"> <!-- Added margin for separation -->
                    <div class="col-md-12">
                        <div class="card card-equal-height">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">All Transaction List</h4>
                                    <button
                                        class="btn btn-primary btn-round ms-auto"
                                        data-bs-toggle="modal"
                                        data-bs-target="#addRowModal"
                                    >
                                        <i class="fa fa-plus"></i>
                                        Add 
                                    </button>
                                    
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-con" class="display table table-striped table-hover">             
                                            <thead>
                                                <tr>
                                                    <th>Patient</th>
                                                    <th>Purpose</th>
                                                    <th>Date</th>
                                                    <th>Time Start</th>
                                                    <th>Time End</th>
                                                    <th>Time Spent</th>
                                                    <th>Status</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>Patient</th>
                                                    <th>Purpose</th>
                                                    <th>Date</th>
                                                    <th>Time Start</th>
                                                    <th>Time End</th>
                                                    <th>Time Spent</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <?php
                                                $transactions = $transac->getAllTransac();
                                                ?>
                                                <tbody>
                                                    <?php if (!empty($transactions)): ?>
                                                        <?php foreach ($transactions as $transaction):
                                                                $statusColor = '';
                                                                if ($transaction->transac_status == 'Pending') {
                                                                    $statusColor = '#ffd54f';  
                                                                } elseif ($transaction->transac_status == 'Progress') {
                                                                    $statusColor = '#64b5f6';  
                                                                } else {
                                                                    $statusColor = '#81c784';  
                                                                }
                                                                
                                                                $transacIn = ($transaction->transac_in == '00:00:00') ? '--' : $transaction->transac_in;
                                                                $transacOut = ($transaction->transac_out == '00:00:00') ? '--' : $transaction->transac_out;

                                                                $transacSpent = '';
                                                                if ($transaction->transac_spent == 0) {
                                                                    $transacSpent = '--';
                                                                } else {
                                                                    $hours = floor($transaction->transac_spent / 3600);
                                                                    $minutes = floor(($transaction->transac_spent % 3600) / 60);
                                                                    $seconds = $transaction->transac_spent % 60;

                                                                    $timeDisplay = '';

                                                                    if ($hours > 0) {
                                                                        $timeDisplay .= $hours . ' hr';
                                                                        if ($hours > 1) {
                                                                            $timeDisplay .= 's'; // Plural for hours
                                                                        }
                                                                        $timeDisplay .= ' ';
                                                                    }

                                                                    if ($minutes > 0) {
                                                                        $timeDisplay .= $minutes . ' min';
                                                                        if ($minutes > 1) {
                                                                            $timeDisplay .= 's'; // Plural for minutes
                                                                        }
                                                                    }

                                                                    if ($hours == 0 && $minutes == 0 && $seconds > 0) {
                                                                        $timeDisplay .= $seconds . ' sec';
                                                                    }

                                                                    $transacSpent = $timeDisplay ? $timeDisplay : '--';
                                                                }                                                                 
                                                            ?>
                                                            <tr data-id="<?php echo $transaction->transac_id; ?>"
                                                                data-patientid="<?php echo $transaction->transac_patientid; ?>"
                                                                data-patientname="<?php echo $transaction->transac_patientname; ?>"
                                                                data-patientprofile="<?php echo $transaction->transac_patientprofile; ?>"
                                                                data-patienttype="<?php echo $transaction->transac_patienttype; ?>"
                                                                data-purpose="<?php echo $transaction->transac_purpose; ?>"
                                                                data-date="<?php echo $transaction->transac_date; ?>"
                                                                data-in="<?php echo $transaction->transac_in; ?>"
                                                                data-out="<?php echo $transaction->transac_out; ?>"
                                                                data-spent="<?php echo $transaction->transac_spent; ?>"
                                                                data-status="<?php echo $transaction->transac_status; ?>"
                                                                data-adminid="<?= htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"
                                                                class="<?php echo $statusColor; ?>">
                                                                <td> 
                                                                    <div style="display: flex; align-items: center;">
                                                                    <img src="../uploads/<?php echo !empty($transaction->transac_patientprofile) ? $transaction->transac_patientprofile : 'default-image.jpg'; ?>" 
                                                                                alt="Profile Image" 
                                                                                style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">

                                                                        <div>
                                                                            <div><?php echo ($transaction->transac_patientname); ?></div>
                                                                            <div style="font-size: 12px; color: gray; margin-top: 5px;">
                                                                                <?php echo ($transaction->transac_patientidnum); ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td><?php echo ($transaction->transac_purpose); ?></td>
                                                                <td><?php echo ($transaction->transac_date); ?></td>
                                                                <td><?php echo ($transacIn); ?></td>
                                                                <td><?php echo ($transacOut); ?></td>
                                                                <td><?php echo ($transacSpent); ?></td>
                                                                <td>
                                                                    <!-- Status display -->
                                                                    <span id="statusDisplay" style="display: inline-block;
                                                                                                    padding: 5px 10px;
                                                                                                    border-radius: 50px;
                                                                                                    background-color: <?php echo $statusColor; ?>;
                                                                                                    color: white;
                                                                                                    text-align: center;
                                                                                                    min-width: 60px; 
                                                                                                    cursor: pointer;">
                                                                        <?php echo $transaction->transac_status; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-arrow-down"></i>
                                                                    </span>

                                                                    <!-- Hidden div with status options -->
                                                                    <div id="statusOptions" style="display: none; position: absolute; 
                                                                                                background-color: white; 
                                                                                                padding: 10px;
                                                                                                border-radius: 8px;
                                                                                                box-shadow: 0 4px 8px rgba(0,0,0,0.2);">
                                                                        <div class="statusOption" data-status="Pending" style="padding: 5px 10px; cursor: pointer; border-radius: 25px; margin: 5px; background-color: #ffd54f; text-align: center;">Pending</div>
                                                                        <div class="statusOption" data-status="Progress" style="padding: 5px 10px; cursor: pointer; border-radius: 25px; margin: 5px; background-color: #64b5f6; text-align: center;">Progress</div>
                                                                        <div class="statusOption" data-status="Done" style="padding: 5px 10px; cursor: pointer; border-radius: 25px; margin: 5px; background-color: #81c784; text-align: center;">Done</div>
                                                                    </div>
                                                                </td>


                                                                <td>
                                                                    <div class="form-button-action">
                                                                        <button type="button" class="btn btn-link btn-primary btn-lg editButton">
                                                                            <i class="fa fa-edit"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-link btn-danger viewButton"
                                                                            data-id="<?php echo $transaction->transac_patientid; ?>"
                                                                            data-type="<?php echo $transaction->transac_patienttype; ?>">
                                                                            <i class="fa fa-eye"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <tr>
                                                            <td colspan="8">No transactions found.</td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>

                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- End of Consultations List -->
                </div>
            </div> <!-- End of .page-inner -->
        </div> <!-- End of #content -->
    </div> <!-- End of .main-panel -->
</div> <!-- End of .wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<!-- Core JS -->
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>

<!-- Plugins -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/chart.js/chart.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
<script src="../assets/js/plugin/chart-circle/circles.min.js"></script>
<script src="../assets/js/plugin/datatables/datatables.min.js"></script>
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="../assets/js/plugin/jsvectormap/jsvectormap.min.js"></script>
<script src="../assets/js/plugin/jsvectormap/world.js"></script>

<script>
    $(document).ready(function () {

        $('#add-con').DataTable({
        "ordering": false,  
        
    })


    $("#addRowButton").click(function () {
        var action =
            '<td> <div class="form-button-action"> <button class="editButton" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button class="removeAccess" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';

        $("#add-row")
            .dataTable()
            .fnAddData([
                $("#addid").val(),
                $("#addfname").val(),
                $("#addlname").val(),
                $("#addemail").val(),
                $("#addposition").val(),
                $("#addrole").val(),
                $("#addstatus").val(), 
                action,
            ]);
        $("#addRowModal").modal("hide");
    });

    $(document).on('click', '.editButton', function () {
        var row = $(this).closest('tr'); 
        var id = row.data('id');
        var patientid = row.data('patientid');
        var patientname = row.data('patientname');
        var purpose = row.data('purpose');
        var date = row.data('date');

        console.log("ID:", id, "Patient ID:", patientid, "Patient Name:", patientname, "Purpose:", purpose);

        $("#transac_id").val(id);
        $("#edit_pname").val(patientname);
        $("#edit_patient_id").val(patientid);
        $("#edit_purpose").val(purpose);

        var myModal = new bootstrap.Modal(document.getElementById('editRowModal'));
        myModal.show();
    });

});
</script>


<script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
                var status = "<?php echo $_SESSION['status']; ?>";
                var message = "<?php echo $_SESSION['message']; ?>";

                Swal.fire({
                    icon: status === 'success' ? 'success' : 'error',
                    title: status.charAt(0).toUpperCase() + status.slice(1) + '!',
                    text: message
                }).then(() => {
                    <?php 
                    unset($_SESSION['status']);
                    unset($_SESSION['message']);
                    ?>
                });

            <?php endif; ?> 
        });
    </script>

<script>
    $(document).ready(function() {
        <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
            var status = '<?php echo $_SESSION['status']; ?>';
            var message = '<?php echo $_SESSION['message']; ?>';

            if (status === 'success') {
                Swal.fire({
                    title: "Success!",
                    text: message,
                    icon: "success",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#28a745" // Green
                });
            } else if (status === 'error') {
                Swal.fire({
                    title: "Error!",
                    text: message,
                    icon: "error",
                    confirmButtonText: "OK",
                    confirmButtonColor: "#dc3545" // Red
                });
            }

            <?php
                unset($_SESSION['status']);
                unset($_SESSION['message']);
            ?>
        <?php endif; ?>
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

        $("#addmedrow").load("offcampusadd.php", function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
            }
        });
    });
</script>

<script>
document.getElementById('pname').addEventListener('input', function() {
    var pname = this.value;

    if (pname.length >= 3) { 
        fetch('transacsearch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'pname=' + pname
        })
        .then(response => response.text())
        .then(data => {
            var suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = data;
            suggestions.style.display = data ? 'block' : 'none';
        });
    } else {
        document.getElementById('suggestions').style.display = 'none';
    }
});

document.getElementById('suggestions').addEventListener('click', function(e) {
    if (e.target.classList.contains('suggestion')) {
        var patientId = e.target.getAttribute('data-id');
        var patientName = e.target.innerText;
        document.getElementById('pname').value = patientName;
        document.getElementById('selected_patient_id').value = patientId;
        document.getElementById('suggestions').style.display = 'none';
    }
});

document.getElementById('edit_pname').addEventListener('input', function() {
    var pname = this.value;

    if (pname.length >= 3) { 
        fetch('transacsearch.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'edit_pname=' + pname
        })
        .then(response => response.text())
        .then(data => {
            var suggestions = document.getElementById('edit_suggestions');
            suggestions.innerHTML = data;
            suggestions.style.display = data ? 'block' : 'none';
        });
    } else {
        document.getElementById('edit_suggestions').style.display = 'none';
    }
});

document.getElementById('edit_suggestions').addEventListener('click', function(e) {
    if (e.target.classList.contains('edit_suggestion')) {
        var patientId = e.target.getAttribute('data-id');
        var patientName = e.target.innerText;
        document.getElementById('edit_pname').value = patientName;
        document.getElementById('edit_patient_id').value = patientId;
        document.getElementById('edit_suggestions').style.display = 'none';
    }
});


$(document).ready(function() {
    $('#add-con').on('click', '#statusDisplay', function() {
        $('#statusOptions').not($(this).siblings('#statusOptions')).hide();
        
        var statusOptions = $(this).closest('tr').find('#statusOptions');
        statusOptions.toggle();
    });

    $('#add-con').on('click', '.statusOption', function() {
        var selectedStatus = $(this).data('status');
        var row = $(this).closest('tr');

        row.find('#statusDisplay').text(selectedStatus);
        
        var statusColor = '';
        if (selectedStatus == 'Pending') {
            statusColor = '#ffd54f';  
        } else if (selectedStatus == 'Progress') {
            statusColor = '#64b5f6';  
        } else {
            statusColor = '#81c784';  
        }
        
        row.find('#statusDisplay').css('background-color', statusColor);
        
        row.find('#statusOptions').hide();
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest('#statusDisplay, #statusOptions').length) {
            $('#statusOptions').hide();
        }
    });
});


$(document).ready(function() {
    $(document).on('click', '.statusDisplay', function() {
        var $row = $(this).closest('tr');
        $row.find('.statusOptions').toggle();
    });

    $(document).on('click', '.statusOption', function() {
        var selectedStatus = $(this).data('status');
        var $row = $(this).closest('tr');
        var transac_id = $row.data('id');
        var adminid = $row.data('adminid');
        
        $.ajax({
            url: 'transacontrol.php',
            method: 'POST',
            data: {
                transac_id: transac_id,
                status: selectedStatus,
                admin_id: adminid
            },
            dataType: 'json',  
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status updated!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload the page after the success message
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.message
                    });
                }
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred: ' + xhr.responseText
                });
            }
        });



    });

    function getStatusColor(status) {
        switch (status) {
            case 'Pending': return '#ffd54f'; // Yellow
            case 'Progress': return '#64b5f6'; // Blue
            case 'Done': return '#81c784';    // Green
            default: return '#ffffff';        // Default (white)
        }
    }
});

$(".viewButton").click(function() {
    var patientId = $(this).closest("tr").data("patientid");
    var patientType = $(this).closest("tr").data("patienttype");

    $.ajax({
        url: "transacpatientview.php",
        method: "POST",
        data: {
            patient_id: patientId,
            patient_type: patientType
        },
        dataType: "json",
        success: function(response) {
            if (response.status === 'success') {
                // Redirect based on patient type 
                if (patientType === 'Faculty') {
                    window.location.href = "patient-facultyprofile.php";
                } else if (patientType === 'Student') {
                    window.location.href = "patient-studprofile.php";
                } else if (patientType === 'Staff') {
                    window.location.href = "patient-staffprofile.php";
                } else if (patientType === 'Extension') {
                    window.location.href = "patient-extensionprofile.php";
                } else { 
                    alert("Invalid patient type");
                }
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', 'Something went wrong: ' + xhr.responseText, 'error');
        }
    });
});

</script>


<script>
    function exportToExcel() {
        var wb = XLSX.utils.table_to_book(document.getElementById('add-con'), {sheet: "Sheet 1"});
        XLSX.writeFile(wb, "table_data.xlsx");
    }
</script>

</body>
</html>