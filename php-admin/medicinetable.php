<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: ../php-login/index.php'); 
  exit; 
}


$db = new Database();
$conn = $db->getConnection();  

$medicine = new MedicineManager($conn); 

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum); 
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Medicine</title>
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

    <link rel="stylesheet" href="path/to/font-awesome/css/font-awesome.min.css">


    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link rel="stylesheet" href="../css/demo.css" />

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
      <div class="sidebar" id="sidebar">
        
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header" id="header">
          
          <!-- End Navbar -->
        </div>

        <div class="container" id="content">
          <div class="page-inner">

            <div id="addmedrow"></div>
          
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">Medicine Stock</h4>
                      <button
                        class="btn btn-primary btn-round ms-auto"
                        data-bs-toggle="modal"
                        data-bs-target="#addMedModal"
                      >
                        <i class="fa fa-plus"></i>
                        Add Medicine Stock
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <!-- Modal -->
                    <div
                      class="modal fade"
                      id="addMedModal"
                      tabindex="-1"
                      role="dialog"
                      aria-hidden="true"
                    >
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header border-0">
                            <h5 class="modal-title">
                              <span class="fw-mediumbold"> New</span>
                              <span class="fw-light"> Medicine Stock </span>
                            </h5>
                            <button
                              type="button"
                              class="close"
                              data-dismiss="modal"
                              aria-label="Close"
                            >
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <p class="small">
                            </p>
                            <form class="form" action="medicinecontrol.php" method="POST">
                            <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
                              <div class="row">
                                <div class="col-sm-12">
                                  <div class="form-group form-group-default">
                                    <!-- Medicine Name Dropdown -->
                                    <div> 
                                    <label for="medicineName">Medicine Name</label>
                                    <select id="addname" name="addname" class="form-control">
                                        <option value="" disabled selected hidden>fill medicine name</option>
                                        <?php
                                        // Fetch all medicines
                                        $medicines = $medicine->getAllMedicines();

                                        // Loop through the medicines and create dropdown options
                                        foreach ($medicines as $med) {
                                            echo "<option value='" . $med->medicine_id . "'>" . $med->medicine_name . "</option>";
                                        }
                                        ?>
                                    </select>
                                    </div>
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label>Quantity</label>
                                    <input
                                      id="addquantity"
                                      name="addquantity"
                                      type="number"
                                      class="form-control"
                                      placeholder="fill quantity"
                                    />
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label>Dosage Strength</label>
                                    <input
                                      id="addDS"
                                      name="addDS"
                                      type="text"
                                      class="form-control"
                                      placeholder="fill dosage strength"
                                    />
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label for="addunit">Medicine unit</label>
                                    <select id="addunit" name="addunit" class="form-control" placeholder="Select medicine unit">
                                      <option value="" disabled selected>Select a unit</option>
                                      <option value="Sachet">Sachet</option>
                                      <option value="Capsule">Capsule</option>
                                      <option value="Tablet">Tablet</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label>Expiration Date</label>
                                    <input
                                      id="addED"
                                      name="addED" 
                                      type="date"
                                      class="form-control"
                                      placeholder="fill expiration date"
                                    />
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer border-0">
                                <button
                                  type="submit"
                                  name="addMedicine"
                                  class="btn btn-primary"
                                >
                                  Add
                                </button>
                                <button
                                  type="button"
                                  class="btn btn-danger"
                                  data-bs-dismiss="modal"
                                >
                                  Close
                                </button>
                              </div>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <div
                      class="modal fade"
                      id="editMedModal"
                      tabindex="-1"
                      role="dialog"
                      aria-hidden="true"
                    >
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header border-0">
                            <h5 class="modal-title">
                              <span class="fw-mediumbold"> Edit</span>
                              <span class="fw-light"> Medicine Stock</span>
                            </h5>
                            <button
                              type="button"
                              class="close"
                              data-bs-dismiss="modal"
                              aria-label="Close"
                              id="edit-exit"
                            >
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                          <form id="editForm" action="medicinecontrol.php" method="POST">
                          <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
                          <div class="row">
                          <div class="col-md-6">
                            <p class="fw-light">Date & Time Added: <span id="editdatetimeadded"></span></p>
                        </div>
                            <div class="col-sm-12">
                              <div class="form-group form-group-default">
                                <label>ID</label>
                                <input id="editid" name="editid" type="text" class="form-control" placeholder="fill id" readonly />
                              </div>
                            </div>
                            <div class="col-sm-12">
                            <div class="form-group form-group-default">
                                <label for="editname">Medicine Name</label>
                                <select id="editname" name="editname" class="form-control">
                                    <?php
                                    // Fetch all medicines
                                    $medicines = $medicine->getAllMedicines();

                                    // Loop through the medicines and create dropdown options
                                    foreach ($medicines as $med) {
                                        echo "<option value='" . $med->medicine_id . "'>" . $med->medicine_name . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group form-group-default">
                                <label>Quantity</label>
                                <input id="editquantity" name="editquantity" type="number" class="form-control" placeholder="fill quantity" />
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group form-group-default">
                                <label>Dosage Strength</label>
                                <input id="editDS" name="editDS" type="text" class="form-control" placeholder="fill dosage strength" />
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group form-group-default">
                                <label>Expiration Date</label>
                                <input id="editED" name="editED" type="date" class="form-control" placeholder="fill expiration date" />
                              </div>
                            </div>
                            <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label for="editunit">Medicine unit</label>
                                    <select id="editunit" name="editunit" class="form-control" placeholder="Select medicine unit">
                                      <option value="" disabled selected>Select a unit</option>
                                      <option value="Sachet">Sachet</option>
                                      <option value="Capsule">Capsule</option>
                                      <option value="Tablet">Tablet</option>
                                    </select>
                                  </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group form-group-default">
                                <label>Disable</label>
                                <select id="editDisable" name="editDisable" class="form-control">
                                  <option value="0">No</option>
                                  <option value="1">Yes</option>
                                </select>
                              </div>
                            </div>
                          </div> 
                          <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-primary" data-bs-target="updatemedicine" id="updatemedicine" name="updatemedicine">
                              Edit
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="edit-close">
                              Close
                            </button>
                          </div>
                        </form>

                          </div>                          
                        </div>
                      </div>
                    </div>

                    <div class="table-responsive">
                    <table id="add-med" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Current/Original Quantity</th>
                                <th>Dosage Strength</th>
                                <th>Date & Time Added</th>
                                <th>Expiration Date</th>
                                <th>Status</th> <!-- Column for Disable status -->
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Dosage Strength</th>
                                <th>Date & Time Added</th> 
                                <th>Expiration Date</th>
                                <th>Status</th> <!-- Column for Disable status -->
                                <th>Action</th>
                            </tr>
                        </tfoot>
                        <tbody>
                        <?php
                          $nodes = $medicine->getAllItems();
                          foreach ($nodes as $node) {
                              // Determine status and background color
                              $disableStatus = $node->medstock_disabled == 1 ? 'Disabled' : 'Enabled';
                              $statusColor = $node->medstock_disabled == 1 ? '#ff6961' : '#77dd77';

                              if ($node->medstock_qty == 0) {
                                  $statusqtyMessage = "Out of Stock";
                                  $qtycolor = "#ff6961"; // Pastel red for out of stock
                              } else {
                                  $statusqtyMessage = $node->medstock_qty;
                                  $qtycolor = "#000000"; // No color font 
                              }

                              // Check if the medicine is expired
                              $currentDate = date('Y-m-d'); 
                              $expirationStatus = (strtotime($node->medstock_expirationdt) < strtotime($currentDate)) ? 'Expired' : '';
 
                              echo "<tr data-id='{$node->medstock_id}' 
                                        data-name='{$node->medicine_name}'
                                        data-unit='{$node->medstock_unit}' 
                                        data-qty='{$node->medstock_origqty}' 
                                        data-dosage='{$node->medstock_dosage}' 
                                        data-dateadded='{$node->medstock_dateadded} {$node->medstock_timeadded}' 
                                        data-expirationdt='{$node->medstock_expirationdt}' 
                                        data-disable='{$node->medstock_disabled}' class='$statusColor'>
                                    <td>{$node->medstock_id}</td>
                                    <td>{$node->medicine_name} ({$node->medstock_unit})</td>
                                    <td style='color: $qtycolor;'>{$statusqtyMessage} / {$node->medstock_origqty}</td>
                                    <td>{$node->medstock_dosage}</td>
                                    <td>{$node->medstock_dateadded} {$node->medstock_timeadded}</td>
                                    <td>
                                        <span style='color: #ff6961;'>$expirationStatus</span>
                                        <br>
                                        <span>{$node->medstock_expirationdt}</span>
                                    </td>
                                    <td>
                                        <span style='
                                            display: inline-block;
                                            padding: 5px 10px;
                                            border-radius: 50px;
                                            background-color: {$statusColor};
                                            color: white; 
                                            text-align: center;
                                            min-width: 60px;'>
                                            {$disableStatus}
                                        </span>
                                    </td>
                                    <td>
                                        <div class='form-button-action'>
                                            <button type='button' class='btn btn-link btn-primary btn-lg editButton'>
                                                <i class='fa fa-edit'></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>";
                          }
                          ?>
                        </tbody>
                    </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <footer class="footer">
          
        </footer>
      </div>
    </div>


    <!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- Datatables -->
    <script src="../assets/js/plugin/datatables/datatables.min.js"></script>
    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>
    <script>
      $(document).ready(function () {
        // Add Row
        $("#add-med").DataTable({
          pageLength: 6,
        });

        
        $("#addMedButton").click(function () {
          var action =
          '<td> <div class="form-button-action"> <button class="editButton" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button class="removeAccess" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>';
          $("#add-med")
            .dataTable()
            .fnAddData([
              $("#addcategory").val(),
              $("#addname").val(),
              $("#addquantity").val(),
              $("#addDS").val(),
              $("#addED").val(),
              action,
            ]);
          $("#addMedModal").modal("hide");
        });
      });

    $(document).on('click', '.editButton', function() {
        var row = $(this).closest('tr');
        var id = row.data('id');
        var name = row.data('name');
        var unit = row.data('unit');
        var qty = row.data('qty');
        var dosage = row.data('dosage');
        var dateadded = row.data('dateadded');
        var expirationdt = row.data('expirationdt');
        var disable = row.data('disable'); 

        $("#editid").val(id); 
        $("#editname").val(name); 
        $("#editunit").val(unit);
        $("#editquantity").val(qty);
        $("#editDS").val(dosage);
        $("#editdatetimeadded").text(dateadded);
        $("#editED").val(expirationdt);
        $("#editDisable").val(disable); 

        $("#editname option").filter(function() {
            return $(this).text() == name; 
        }).prop('selected', true);

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('editMedModal'));
        myModal.show();
    });

// When Save/Edit Row button is clicked
$(document).on('click', '#updatemedicine', function() {
  var table = $("#add-med").DataTable();
  var row = table.row('.selected');

  // Update row data with the new input values
  row.data([
    $("#editcategory").val(),
    $("#editname").val(),
    $("#editquantity").val(),
    $("#editDS").val(),
    $("#editED").val(),
    $("#editDisable option:selected").text(), // Set the 'Disable' field value as text in the table
    '<td> <div class="form-button-action"> <button class="editButton" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button class="removeAccess" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>',
  ]).draw();

  // Hide the modal
  $("#editMedModal").modal("hide");
});


//-----------------------------------------
$(document).on('click', '.removeAccess', function (e) {
    var row = $(this).closest('tr');
    var medicine_id = row.data('id'); // Correct variable name for medicine ID

    swal({
        title: "Do you want to remove medicine?",
        text: "You won't be able to revert this!",
        icon: "warning",
        buttons: {
            confirm: {
                text: "Yes",
                className: "btn btn-success",
            },
            cancel: {
                visible: true,
                className: "btn btn-danger",
            },
        },
    }).then((Delete) => {
        if (Delete) {
            $.ajax({
                url: 'medicinecontrol.php',
                type: 'POST',
                data: { medicine_id: medicine_id }, // Sending correct medicine_id
                success: function (response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        $("#add-med").DataTable().row(row).remove().draw(); // Remove the row

                        swal({
                            title: "Removed!",
                            text: "Medicine has been removed.",
                            icon: "success",
                            buttons: {
                                confirm: {
                                    className: "btn btn-success",
                                },
                            },
                        });
                    } else {
                        swal({
                            title: "Failed!",
                            text: data.message,
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        });
                    }
                },
                error: function () {
                    swal({
                        title: "Error!",
                        text: "An error occurred while processing your request.",
                        icon: "error",
                        buttons: {
                            confirm: {
                                className: "btn btn-danger",
                            },
                        },
                    });
                }
            });
        } else {
            swal.close();
        } 
    });
});

$("#edit-exit, #edit-close").click(function (e) {
    swal("", {
        buttons: false,
        timer: 100,
    }).then(() => {
        $("#editRowModal").modal('hide');
    });
});

  
  
      
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if there is a session message available
            <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
                var status = "<?php echo $_SESSION['status']; ?>";
                var message = "<?php echo $_SESSION['message']; ?>";

                Swal.fire({
                    icon: status === 'success' ? 'success' : 'error',
                    title: status.charAt(0).toUpperCase() + status.slice(1) + '!',
                    text: message
                }).then(() => {
                    // Clear the session messages after displaying
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
        // Check for session status and message
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

            // Clear session messages after showing alert
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

        $("#addmedrow").load("addmedicine.php", function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
            }
        });
    });
</script>
  
  </body>
</html>
