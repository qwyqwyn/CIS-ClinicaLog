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
    <title>Clinic Staff User</title>
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
                    <!-- Add Consultation Form -->
                    <div class="col-md-12">
                        <div class="card card-equal-height">
                            <div class="card-header">
                                <div class="d-flex align-items-center">
                                    <h4 class="card-title">Add Consultation</h4>
                                </div>
                            </div>
                            <div class="card-body">
                                <form id="addConsultationForm" action="consultationcontrol.php" method="POST">
                                <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
                                <div class="row">
                                <div class="col-md-4 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="pname">Search by Name or ID:</label>
                                        <input type="text" id="pname" name="pname" class="form-control" placeholder="Search" autocomplete="off" required>
                                        <div class="form-control" id="suggestions" style="display: none;"></div>
                                        <!-- Hidden form field to store selected patient ID -->
                                        <input type="text" id="selected_patient_id" name="selected_patient_id" style="display:none;">
                                    </div>  
                                </div>                                 
                                    <?php
                                    if (isset($_POST['pname'])) {
                                        $pname = $_POST['pname'];

                                        $pname = $conn->real_escape_string($pname);

                                        $sql = "SELECT patient_id, patient_name FROM patients WHERE patient_name LIKE '%$pname%' OR patient_id LIKE '%$pname%' LIMIT 10";
                                        $result = $conn->query($sql);

                                        if ($result->num_rows > 0) {
                                            // Generate suggestion list items
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<div class='suggestion' data-id='{$row['patient_id']}'>";
                                                echo $row['patient_name'] . " (" . $row['patient_id'] . ")";
                                                echo "</div>";
                                            }
                                        } else {
                                            echo "<div class='suggestion'>No results found</div>";
                                        }
                                    }
                                    ?>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="Diagnosis">Diagnosis:</label>
                                        <textarea id="Diagnosis" name="Diagnosis" class="form-control" placeholder="Type the diagnosis (e.g., Hypertension)"  rows="3" required></textarea>
                                    </div>
                                    </div>
                                    <div class="col-md-3 mb-3">
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
                                    <div class="col-md-3 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="presmedqty">Quantity:</label>
                                        <input type="number" id="presmedqty" name="presmedqty" class="form-control" placeholder="Enter quantity" min="1"  required oninput="checkQuantity()">
                                        <div id="qty-message" class="text-danger" style="color: red; display: none;"></div>
                                    </div>
                                    </div>
                                    </div>
                                    <div class="row">
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="presmednotes">Notes:</label>
                                        <textarea id="presmednotes" name="presmednotes" class="form-control" placeholder="Enter any notes regarding the treatment (optional)"  rows="3"></textarea>
                                    </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                    <div class="form-group mb-3">
                                        <label for="Remarks">Remarks:</label>
                                        <textarea id="Remarks" name="Remarks" class="form-control" placeholder="Enter any important remarks (e.g., follow-up needed)"  rows="3" required></textarea>
                                    </div>
                                    </div>
                                    </div>
                                    <div class="modal-footer border-0 mt-auto">
                                        <button type="submit" class="btn btn-primary" name="addcon" id="addcon">Submit</button>
                                        <button type="reset" class="btn btn-secondary ms-2">Clear</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div> <!-- End of .row -->

                <!-- Consultation Edit Modal -->
                <div class="modal fade" id="editConModal" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header border-0">
                                <h5 class="modal-title">
                                    <span class="fw-mediumbold"> Edit</span>
                                    <span class="fw-light"> Consultation </span>
                                </h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close" id="edit-exit">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="editConForm" action="consultationcontrol.php" method="POST">
                                    <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
                                       <input id="edit_consult_id" name="edit_consult_id" type="text" class="form-control" hidden/>
                                        <input id="edit_patient_id" name="edit_patient_id" type="text" class="form-control" hidden  />
                                    <div class="form-group mb-3">
                                        <label>Patient Name</label>
                                        <input id="edit_patient_name" name="edit_patient_name" type="text" class="form-control" readonly />
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_diagnosis">Diagnosis</label>
                                        <input id="edit_diagnosis" name="edit_diagnosis" type="text" class="form-control" required />
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="edit_medicine">Edit Medicine</label>
                                        <input id="edit_medicine" name="edit_medicine" type="text" class="form-control" placeholder="Search medicine" autocomplete="off">
                                        <div id="edit-suggestion" class="form-control" style="display: none;"></div>
                                        <input type="hidden" id="edit_medicine_id" name="edit_medicine_id">
                                    </div>
                                        <?php
                                        if (isset($_POST['prescribemed'])) {
                                            $edit_medicine = $_POST['prescribemed'];

                                            $edit_medicine = $conn->real_escape_string($edit_medicine);

                                            $sql = "SELECT medstock_id, medicine_name FROM medstock WHERE medicine_name LIKE '%$edit_medicine%' OR medstock_id LIKE '%$edit_medicine%' LIMIT 10 AND medstock_disable = 0";
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
                                                $('#edit_medicine').on('keyup', function () {
                                                    var query = $(this).val();
                                                    if (query.length > 2) {
                                                        $.ajax({
                                                            url: 'addconsultation.php',
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

                                                // Make sure the suggestions are clickable
                                                $(document).on('click', '.med-suggestion', function () {
                                                    var medName = $(this).text().split(' (')[0];
                                                    var medId = $(this).data('id');
                                                    $('#edit_medicine_id').val(medId);
                                                    $('#edit_medicine').val(medName);
                                                    $('#edit-suggestion').html('').hide();
                                                });
                                            });
                                        </script>
                                        <div class="form-group mb-3">
                                            <label for="edit_quantity">Quantity</label>
                                            <input id="edit_quantity" name="edit_quantity" type="number"  min="1" class="form-control" required/>
                                            <div id="qty-message" class="text-danger" style="color: red; display: none;"></div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="edit_notes">Notes</label>
                                            <input id="edit_notes" name="edit_notes" type="text" class="form-control" />
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="edit_remarks">Remarks</label>
                                            <input id="edit_remarks" name="edit_remarks" type="text" class="form-control" required />
                                        </div>
                                        <div class="form-group mb-3">
                                            <label for="edit_date">Date</label>
                                            <input id="edit_date" name="edit_date" type="date" class="form-control" readonly />
                                        </div>

                                        <div class="modal-footer border-0">
                                            <button type="submit" class="btn btn-primary" name="editcon" id="editcon">Save</button>
                                            <button type="submit" class="btn btn-danger" name="delete" id="delete">Delete</button>
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
                        const patientId = row.attr('data-stock');
                        const patientName = row.attr('name'); 
                        const diagnosis = row.attr('consult_diagnosis'); 
                        const medicineName = row.attr('data-medicine-name');
                        const medicineId = row.attr('data-medstock'); 
                        const quantity = row.attr('pm_medqty');
                        const notes = row.attr('notes'); 
                        const remarks = row.attr('remarks'); 
                        const date = row.attr('consult_date'); 

                        $("#edit_consult_id").val(consultId);
                        $("#edit_patient_id").val(patientId);
                        $("#edit_patient_name").val(patientName);
                        $("#edit_diagnosis").val(diagnosis);
                        $("#edit_medicine").val(medicineName);
                        $("#edit_medicine_id").val(medicineId);
                        $("#edit_quantity").val(quantity);
                        $("#edit_notes").val(notes); 
                        $("#edit_remarks").val(remarks);
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
                                    <h4 class="card-title">Consultations List</h4>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="add-con" class="display table table-striped table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name:</th>
                                                <th>Diagnosis</th>
                                                <th>Prescribed Medicine (Quantity)</th>
                                                <th>Clinician</th>
                                                <th>Notes:</th>
                                                <th>Remark</th>
                                                <th>Date</th>
                                                <th style="width: 10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tfoot>
                                            <tr>
                                                <th>Name:</th>
                                                <th>Diagnosis</th> 
                                                <th>Prescribed Medicine (Quantity)</th>
                                                <th>Clinician</th>
                                                <th>Notes:</th>
                                                <th>Remark</th>
                                                <th>Date</th>
                                                <th>Action</th>
                                            </tr>
                                        </tfoot>
                                        <tbody>
                                        <?php
                                            $items = $consultations->getAllItems();
 
                                            if (is_array($items) && !empty($items)) {
                                                foreach ($items as $item) {
                                                    if (is_array($item)) {
                                                        $consultId = $item['consult_id'] ?? null;
                                                        $medstockId = 'N/A';
                                                        $medicationName = 'N/A';
                                                        $medQty = 'N/A';

                                                        if ($consultId) {
                                                            $stmt = $conn->prepare("
                                                                SELECT pm.pm_medstockid, pm.pm_medqty, m.medicine_name 
                                                                FROM prescribemed AS pm 
                                                                JOIN medstock AS ms ON pm.pm_medstockid = ms.medstock_id 
                                                                JOIN medicine AS m ON ms.medicine_id = m.medicine_id 
                                                                WHERE pm.pm_consultid = :consult_id
                                                            ");
                                                            $stmt->execute(['consult_id' => $consultId]);
                                                            $prescription = $stmt->fetch(PDO::FETCH_ASSOC);

                                                            if ($prescription) {
                                                                $medstockId = htmlspecialchars($prescription['pm_medstockid'] ?? 'N/A');
                                                                $medicationName = htmlspecialchars($prescription['medicine_name'] ?? 'N/A');
                                                                $medQty = htmlspecialchars($prescription['pm_medqty'] ?? 'N/A');
                                                            }
                                                        }

                                                        echo "<tr data-id='" . htmlspecialchars($item['consult_id']) . "'
                                                            data-stock='" . htmlspecialchars($item['consult_patientid']) . "' 
                                                            name='" . htmlspecialchars($item['name'] ?? 'N/A') . "'
                                                            data-medstock='" . $medstockId . "' 
                                                            data-medicine-name='" . $medicationName . "' 
                                                            pm_medqty='" . htmlspecialchars($medQty) . "'
                                                            notes='" . htmlspecialchars($item['consult_treatmentnotes'] ?? 'N/A') . "'
                                                            remarks='" . htmlspecialchars($item['consult_remark'] ?? 'N/A') . "'
                                                            consult_diagnosis='" . htmlspecialchars($item['consult_diagnosis'] ?? 'N/A') . "' 
                                                            consult_date='" . htmlspecialchars($item['consult_date'] ?? 'N/A') . "' >
                                                            <td>" . htmlspecialchars($item['name'] ?? 'N/A') . "</td>
                                                            <td>" . htmlspecialchars($item['consult_diagnosis'] ?? 'N/A') . "</td>
                                                            <td>" . $medicationName . " (". $medQty . ")" ."</td>
                                                            <td>" . htmlspecialchars($item['consult_clinician'] ?? 'N/A') ."</td>
                                                            <td>" . htmlspecialchars($item['consult_treatmentnotes'] ?? 'N/A') . "</td>
                                                            <td>" . htmlspecialchars($item['consult_remark'] ?? 'N/A') . "</td>
                                                            <td>" . htmlspecialchars($item['consult_date'] ?? 'N/A') . "</td>
                                                            <td>
                                                                <div class='form-button-action'>
                                                                    <button type='button' class='btn btn-link btn-primary btn-lg editConButton'>
                                                                        <i class='fa fa-edit'></i>
                                                                    </button>  
                                                                </div>
                                                            </td>
                                                        </tr>";
                                                    } else {
                                                        echo "<tr><td colspan='11'>Invalid data format</td></tr>";
                                                    }
                                                }
                                            } else {
                                                echo "<tr><td colspan='11'>No consultations found.</td></tr>";
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
</script>


<script>
        $(document).ready(function () {
        $('#prescribemed').on('keyup', function () {
            var query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: 'addconsultation.php', 
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

        $("#addmedrow").load("addconsultation.php", function(response, status, xhr) {
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






</body>
</html>