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
include('../php/offcampus.php');
@include('../php/patient-studprofile.php');
@include('../php/patient-staffprofile.php');
@include('../php/patient-facultyprofile.php');
@include('../php/patient-extensionprofile.php');
include('../php/consultation.php');


$db = new Database();
$conn = $db->getConnection(); 

$user_idnum = $_SESSION['user_idnum']; 

$consultations = new ConsultationManager($conn);
$medicine = new MedicineManager($conn);
$offCampusManager = new OffCampusManager($db);
$allOffCampusRecords = $offCampusManager->getAllOffCampusData();

$patientId = isset($_GET['id']) ? $_GET['id'] : null;
$patientType = isset($_GET['patient_patienttype']) ? $_GET['patient_patienttype'] : null;
 
$patientDetails = null;

$medicineId = isset($_GET['id']) ? $_GET['id'] : null;

if (isset($_POST['prescribemed'])) {
    $searchMed = "%" . $_POST['prescribemed'] . "%"; 
    
    $medQuery = "
        SELECT medstock.medstock_id, medicine.medicine_name 
        FROM medstock 
        JOIN medicine ON medstock.medicine_id = medicine.medicine_id 
        WHERE medicine.medicine_name LIKE ? AND medstock_disable = 0"; 

    $stmt = $conn->prepare($medQuery);
    $stmt->execute([$searchMed]); 
    
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC); 

    if ($result) {
        foreach ($result as $p) {
            echo "<div class='med-suggestion' data-id='{$p['medstock_id']}'>
                    {$p['medicine_name']} ({$p['medstock_id']})
                  </div>";
        }
    } else {
        echo "<div>No results found</div>";
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['medstock_id'], $_POST['requested_qty'])) {
    $medstock_id = $_POST['medstock_id'];
    $requested_qty = (int) $_POST['requested_qty'];

    $stmt = $conn->prepare("SELECT m.medstock_qty - (IFNULL(SUM(pm.pm_medqty), 0) + IFNULL(SUM(mi.mi_medqty), 0)) AS available_stock
                            FROM medstock m 
                            LEFT JOIN prescribemed pm ON pm.pm_medstockid = m.medstock_id 
                            LEFT JOIN medissued mi ON mi.mi_medstockid = m.medstock_id
                            WHERE m.medstock_id = ? 
                            GROUP BY m.medstock_id");
    $stmt->bind_param("s", $medstock_id);
    $stmt->execute();
    $stmt->bind_result($current_qty);
    $stmt->fetch();
    $stmt->close();

    if ($current_qty === null) {
        echo json_encode(["status" => "error", "message" => "Medicine not found"]);
    } elseif ($requested_qty > $current_qty) {
        echo json_encode(["status" => "error", "message" => "Only $current_qty available in stock"]);
    } else {
        echo json_encode(["status" => "success"]);
    }
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Mabini Unit Medicine Issuance</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" /> 
    <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon"/>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    
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
                              <span class="fw-mediumbold"> Issued Medicine</span>
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
                              Issue a Medicine for Mabini Unit Distribution
                            </p>
                             <!-- Start Add Modal Form-->
                             <form class="form" action="offcampuscontrol.php" method="POST" enctype="multipart/form-data">
                             <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
                             <div class="row">
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="prescribemed">Search medicine:</label>
                                        <input type="text" id="prescribemed" name="prescribemed" class="form-control" placeholder="Search" autocomplete="off" required>
                                        <div id="med-suggestion" class="form-control" style="display: none;"></div>
                                        <!-- Hidden form field to store selected medicine ID -->
                                        <input type="text" id="selected_medicine_id" name="selected_medicine_id" style="display:none;" onchange="checkQuantity()">
                                    </div>
                                    </div>
                                    <?php
                                    if (isset($_POST['prescribemed'])) {
                                        $prescribemed = $_POST['prescribemed'];

                                        $prescribemed = $conn->real_escape_string($prescribemed);

                                        $sql = "SELECT medstock_id, medicine_name FROM medstock WHERE medicine_name LIKE '%$prescribemed%' OR medstock_id LIKE '%$prescribemed%' LIMIT 10";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<div class='med-suggestion' data-id='{$row['medstock_id']}'>";
                                                echo $row['medicine_name'] . " (" . $row['medstock_id'] . ")";
                                                echo "</div>";
                                            }
                                        } else {
                                            echo "<div class='med-suggestion'>No results found</div>";
                                        }
                                    }
                                    ?>
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="presmedqty">Quantity:</label>
                                        <input type="number" id="presmedqty" name="presmedqty" class="form-control" placeholder="Enter quantity" min="1"  required oninput="checkQuantity()">
                                        <div id="qty-message" class="text-danger" style="color: red; display: none;"></div>
                                    </div>
                                    </div>
                                    </div>
                              <div class="modal-footer border-0">
                              <button type="submit" class="btn btn-primary" name="addoffcampus">Add</button>
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
                                    <span class="fw-light"> Issuance </span>
                                </h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="edit-exit">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editConForm" action="offcampuscontrol.php" method="POST">
                                <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
                                       <input id="editid" name="editid" type="text" class="form-control" hidden/>
                                        <input id="edit_patient_id" name="edit_patient_id" type="text" class="form-control" hidden  />
                                    <div class="form-group mb-3">
                                        <label for="editmedstockname">Edit Medicine</label>
                                        <input id="editmedstockname" name="editmedstockname" type="text" class="form-control" placeholder="Search medicine" autocomplete="off">
                                        <div id="edit-suggestion" class="form-control" style="display: none;"></div>
                                        <input type="hidden" id="editmedstockid" name="editmedstockid">
                                    </div>
                                        <?php
                                        if (isset($_POST['prescribemed'])) {
                                            $edit_medicine = $_POST['prescribemed'];

                                            $edit_medicine = $conn->real_escape_string($edit_medicine);

                                            $sql = "SELECT medstock_id, medicine_name FROM medstock WHERE medicine_name LIKE '%$edit_medicine%' OR medstock_id LIKE '%$edit_medicine%' LIMIT 10 
                                            AND medstock_disable = 0";
                                            $result = $conn->query($sql);

                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    echo "<div class='med-suggestion' data-id='{$row['medstock_id']}'>";
                                                    echo htmlspecialchars($row['medicine_name']) . " (" . htmlspecialchars($row['medstock_id']) . ")";
                                                    echo "</div>";
                                                }
                                            } else {
                                                echo "<div class='med-suggestion'>No results found</div>";
                                            }
                                        }
                                        ?>
                                        <script>
                                            $(document).ready(function () {
                                                $('#editmedstockname').on('keyup', function () {
                                                    var query = $(this).val();
                                                    if (query.length > 2) {
                                                        $.ajax({
                                                            url: 'offcampusadd.php',
                                                            method: 'POST',
                                                            data: { prescribemed: query },
                                                            success: function (data) {
                                                                $('#edit-suggestion').html(data).show();
                                                            },
                                                            error: function (xhr, status, error) {
                                                                console.error('Error fetching suggestions:', error);
                                                            }
                                                        });
                                                    } else {
                                                        $('#edit-suggestion').html('').hide();
                                                    }
                                                });

                                                $(document).on('click', '.med-suggestion', function () {
                                                    var medName = $(this).text().split(' (')[0];
                                                    var medId = $(this).data('id');
                                                    $('#editmedstockid').val(medId);
                                                    $('#editmedstockname').val(medName);
                                                    $('#edit-suggestion').html('').hide();
                                                });
                                            });
                                        </script>
                                        <div class="form-group mb-3">
                                            <label for="edit_quantity">Quantity</label>
                                            <input id="editmedqty" name="editmedqty" type="number"  min="1" class="form-control" required/>
                                            <div id="qty-message" class="text-danger" style="color: red; display: none;"></div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="edit_date">Date</label>
                                            <input id="editdate" name="editdate" type="date" class="form-control"/>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="submit" class="btn btn-primary" name="updateoffcampus" id="updateoffcampus">Save</button>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                    $(document).on('click', '.editConButton', function () {
                        const row = $(this).closest('tr'); 
                        const consultId = row.attr('data-id');
                        const medicineName = row.attr('data-medicine-name');
                        const medicineId = row.attr('data-medstock'); 
                        const quantity = row.attr('pm_medqty');
                        const date = row.attr('consult_date'); 

                        $("#edit_consult_id").val(consultId);
                        $("#edit_medicine").val(medicineName);
                        $("#edit_medicine_id").val(medicineId);
                        $("#edit_quantity").val(quantity);
                        $("#edit_date").val(date);

                        const editModal = new bootstrap.Modal(document.getElementById('editConModal'));
                        editModal.show();
                    });
                    </script>

                <!-- List of Consultations -->
                <div class="row mt-4"> <!-- Added margin for separation -->
                    <div class="col-md-12">
                        <div class="card card-equal-height">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Mabini Issuance List</h4>
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
                                                    <th>No.</th>
                                                    <th>Medicine Stock No.</th>
                                                    <th>Medicine Name</th>
                                                    <th>Quantity</th>
                                                    <th>Date</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tfoot>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Medicine Stock No.</th>
                                                    <th>Medicine Name</th>
                                                    <th>Quantity</th>
                                                    <th>Date</th>
                                                    <th>Action</th>
                                                </tr>
                                            </tfoot>
                                            <tbody>
                                                <?php foreach ($allOffCampusRecords as $index => $record): ?>
                                                    <tr data-id="<?= $record['id']; ?>" 
                                                    data-medstockid="<?= $record['medstockid']; ?>" 
                                                    data-medstockname="<?= $record['medstockname']; ?>" 
                                                    data-medqty="<?= $record['medqty']; ?>" 
                                                    data-date="<?= $record['date']; ?>"
                                                    data-adminid="<?= htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>">

                                                        <td><?= $index + 1; ?></td>
                                                        <td><?= htmlspecialchars($record['medstockid']) ?></td>
                                                        <td><?= htmlspecialchars($record['medstockname']);?></td>
                                                        <td><?= htmlspecialchars($record['medqty']); ?></td>
                                                        <td><?= htmlspecialchars($record['date']); ?></td>
                                                        <td>
                                                            <div class="form-button-action">
                                                                <button type="button" class="btn btn-link btn-primary btn-lg editButton" data-id="<?= $record['id']; ?>">
                                                                    <i class="fa fa-edit"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-link btn-danger removeButton">
                                                                    <i class="fa fa-times"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
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


<!-- Select2 -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

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

        $("#add-con").DataTable({
        pageLength: 10, 
        responsive: true 
    });

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
        var medstockid = row.data('medstockid');
        var medstockname = row.data('medstockname');
        var medqty = row.data('medqty');
        var date = row.data('date');

        $("#editid").val(id);
        $("#editmedstockid").val(medstockid);
        $("#editmedstockname").val(medstockname);
        $("#editmedqty").val(medqty);
        $("#editdate").val(date);

        var myModal = new bootstrap.Modal(document.getElementById('editRowModal'));
        myModal.show();
    });


    // Keyup event for search box
    $('#pname').on('keyup', function () {
        var query = $(this).val();

        if (query.length > 2) { 
            $.ajax({
                url: 'offcampusadd.php', 
                method: 'POST',
                data: { pname: query }, 
                success: function (data) {
                    $('#suggestions').html(data).show();
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching suggestions:', error);
                }
            });
        } else {
            $('#suggestions').html('').hide(); 
        }
    });

    $(document).on('click', '.suggestion', function () {
        var name = $(this).text().split(' (')[0]; 
        var patientId = $(this).data('id'); 

        $('#selected_patient_id').val(patientId); 
        $('#pname').val(name); 

        $('#suggestions').html('').hide(); 
    });
});
</script>


<script>
        $(document).ready(function () {
        $('#prescribemed').on('keyup', function () {
            var query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: 'offcampusadd.php', 
                    method: 'POST',
                    data: { prescribemed: query },
                    success: function (data) {
                       
                        $('#med-suggestion').html(data).show(); 
                        
                    },
                    error: function (xhr, status, error) {
                    console.error('Error fetching suggestions:', error);
                }
                });
            } else {
                $('#med-suggestion').html('').hide(); 
            }
        });


        $(document).on('click', '.med-suggestion', function () {
        var medName = $(this).text().split(' (')[0];
        var medId = $(this).data('id');
        $('#selected_medicine_id').val(medId);
        $('#prescribemed').val(medName);
        $('#med-suggestion').html('').hide();
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

    <!-- Include SweetAlert library -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    function checkQuantity() {
        const medstockId = document.getElementById('selected_medicine_id').value;
        const requestedQty = document.getElementById('presmedqty').value;
        const messageElement = document.getElementById('qty-message');

        
        console.log(`Selected medstock_id: ${medstockId}, Requested quantity: ${requestedQty}`);

        if (medstockId && requestedQty > 0) {
            const formData = new FormData();
            formData.append('medstock_id', medstockId); 
            formData.append('requested_qty', requestedQty); 

            fetch('consultationcontrol.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'error') {
                    messageElement.textContent = data.message;
                    messageElement.style.display = 'block';
                } else {
                    messageElement.style.display = 'none'; 
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                messageElement.textContent = 'An error occurred: ' + error.message;
                messageElement.style.display = 'block';
            });
        } else {
            messageElement.style.display = 'none'; 
        }
    }
</script>

<script>
$(document).ready(function () {
    $('.removeButton').on('click', function () {
        var offcampusId = $(this).data('id'); 
        var adminid= $(this).data('adminid'); 
        
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this record!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                
                $.ajax({
                    url: 'offcampuscontrol.php',
                    method: 'POST',
                    data: { deleteoffcampus: true, offcampus_id: offcampusId, admin_id: adminid},
                    success: function (response) {
                        console.log(response); 

                        var result = typeof response === 'object' ? response : JSON.parse(response);
 
                        swal(result.message, {
                            icon: result.status === 'success' ? 'success' : 'error',
                        }).then(() => {
                            if (result.status === 'success') {
                                window.location.href = 'offcampusadd.php';
                            }
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error('Error deleting record:', error);
                        swal('An error occurred while deleting the record.', {
                            icon: 'error',
                        });
                    }
                });
            } else {
                
                swal("Your record is safe!", {
                    icon: 'info',
                });
            }
        });
    });
});
</script>






</body>
</html>