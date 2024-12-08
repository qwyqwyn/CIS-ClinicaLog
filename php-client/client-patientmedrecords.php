<?php
session_start();

include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');
include('../php/patient.php');
include('../php/medicalrecords.php');

$db = new Database();
$conn = $db->getConnection();

$patient = new PatientManager($db);
$user = new User($conn);
$medicalrecords = new MedRecManager($conn);

$patient_id = $_SESSION['patuser_id'];
$patient_type = $_SESSION['patuser_type'];

?>
<!DOCTYPE html>
<html lang="en">
<!-- Your HTML content remains unchanged -->
<body>  
<div class="row"> 
<h3 class="fw-bold mb-3">Manage Patient's Medical Records</h3>
</div>
<div class="col-md-12">
    <div class="card card-equal-height">
        <div class="card-header">
            <div class="d-flex align-items-center">
                <h4 class="card-title">List of Medical Records</h4>
                <button
                class="btn btn-primary btn-round ms-auto"
                data-bs-toggle="modal"
                data-bs-target="#addMedicalRecModal"
              >
                <i class="fa fa-plus"></i>
                Add Medical Record 
              </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Modal -->
            <div
              class="modal fade"
              id="addMedicalRecModal"
              tabindex="-1"
              role="dialog"
              aria-hidden="true"
            >
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header border-0">
                    <h5 class="modal-title">
                      <span class="fw-mediumbold"> New</span>
                      <span class="fw-light"> Medicine </span>
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
                    <form class="form" action="client-medrecscontrol.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" class="form-control" id="patientid" name="patientid" value="<?php echo htmlspecialchars($patient_id); ?>" />
                        <input type="hidden" class="form-control" id="patienttype" name="patienttype" value="<?php echo htmlspecialchars($patient_type); ?>" />

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group form-group-default">
                                    <label>Upload Medical Record (PDF only)</label>
                                    <input
                                        id="uploadfile"
                                        name="uploadfile[]"
                                        type="file"
                                        class="form-control" 
                                        accept="application/pdf"
                                        multiple
                                        required
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0">
                            <button
                            type="submit"
                            name="addmedicalrecs"
                            class="btn btn-primary"
                            >
                            Add Medical Record
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

                    
                    <!-- Edit Modal Form-->

                    <div
                      class="modal fade"
                      id="editRowModal"
                      tabindex="-1"
                      role="dialog"
                      aria-hidden="true"
                    >
                      <div class="modal-dialog" role="document">
                        <div class="modal-content">
                          <div class="modal-header border-0">
                            <h5 class="modal-title">
                              <span class="fw-mediumbold">Edit File Name and Comment</span>
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
                            <!--Start Edit Form-->
                            <form class="form" action="client-medrecscontrol.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" class="form-control" id="patientid" name="patientid" value="<?php echo $patient_id; ?>" />
                            <input type="hidden" class="form-control" id="patienttype" name="patienttype" value="<?php echo $patient_type; ?>" />

                            <input type="hidden" id="editid" name="editid" class="form-control"/>
                                <div class="row">
                                    <div class="col-md-12 pe-0">
                                        <div class="form-group form-group-default">
                                            <label>File Name</label>
                                            <input id="editfilename" name="editfilename" type="text" class="form-control"/>
                                        </div>
                                    </div>
                                    <!-- Comment/Description -->
                                    <div class="col-md-12">
                                    <div class="form-group form-group-default">
                                        <label>Comment/Description</label>
                                        <textarea
                                        id="editcomment"
                                        name="editcomment"
                                        rows="4"
                                        class="form-control"
                                        placeholder="Write a brief description or comment"
                                        ></textarea>
                                    </div>
                                    </div>
                                </div>
                                

                                    <!-- Modal Footer -->
                                    <div class="modal-footer border-0">
                                        <button type="submit" class="btn btn-primary" name="editmedrecs"> 
                                            Save changes 
                                        </button>
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Close
                                        </button>
                                    </div>
                                </form>
                                <!-- End Edit Form -->

                            <!--End Edit Form-->
                          </div>
                        </div>
                      </div>
                    </div>


                    <?php
                    $records = $medicalrecords->getMedicalRecords($patient_id);
                    ?>

                    <div class="table-responsive">
                        <table id="addmedrecord" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>File Name</th>
                                    <th>Comment</th>
                                    <th>Date & Time Added</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>File Name</th>
                                    <th>Comment</th>
                                    <th>Date & Time Added</th>
                                    <th>Action</th>
                                </tr> 
                            </tfoot>
                            <tbody>
                                <?php if (!empty($records)) : ?>
                                    <?php foreach ($records as $record) : ?>
                                        <tr data-id="<?= $record->medicalrec_id ?>", 
                                            data-filename="<?= htmlspecialchars($record->medicalrec_filename) ?>", 
                                            data-comment="<?= htmlspecialchars($record->medicalrec_comment) ?>" >
                                            <td>
                                                <i class="fas fa-file-pdf" style="color: #d50000; margin-right: 5px;"></i>
                                                <?= htmlspecialchars($record->medicalrec_filename) ?>
                                            </td>

                                            <td><?= htmlspecialchars($record->medicalrec_comment) ?></td>
                                            <td><?= htmlspecialchars($record->medicalrec_dateadded . ' ' . $record->medicalrec_timeadded) ?></td>
                                            <td>
                                                <div class="form-button-action"> 
                                                <button 
                                                    type="button"
                                                    data-bs-toggle="tooltip"
                                                    class="btn btn-link btn-primary btn-lg viewButton"
                                                    data-file="<?= htmlspecialchars($record->medicalrec_file) ?>" 
                                                    data-name="<?= htmlspecialchars($record->medicalrec_filename) ?>" 
                                                    onclick="window.open('viewmedrecpdf.php?file=' + encodeURIComponent(this.getAttribute('data-file')) + '&name=' + encodeURIComponent(this.getAttribute('data-name')), '_blank');"
                                                >
                                                    <i class="fa fa-eye"></i>
                                                </button>
                                                    <button 
                                                        type="button" 
                                                        data-bs-toggle="tooltip"
                                                        class="btn btn-link btn-primary btn-lg editButton"
                                                        data-id="<?= $record->medicalrec_id ?>"
                                                    >
                                                        <i class="fa fa-edit"></i>
                                                    </button>
                                                </div>  
                                            </td>
                                        </tr>  
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="5">No records found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.15.7/dist/sweetalert2.all.min.js"></script>

<script>
$(document).ready(function () {
    
    $("#addmedrecord").DataTable({
        pageLength: 5, 
        responsive: true, 
    });

    $(document).on('click', '.editButton', function () {
        var row = $(this).closest('tr'); 
        var id = row.data('id');
        var filename = row.data('filename');
        var comment = row.data('comment'); 
      
        $("#editid").val(id);
        $("#editid").val(id);
        $("#editfilename").val(filename);
        $("#editcomment").val(comment);

        var myModal = new bootstrap.Modal(document.getElementById('editRowModal'));
        myModal.show();
    });

    $(document).on('click', '.removeAccess', function (e) {
    e.preventDefault(); 
    var row = $(this).closest('tr');
    var medrecId = row.data('id'); 
    var medrecname = row.data('filename'); 
    
    swal({
        title: `Do you want to remove ${medrecname}?`, 
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
                url: 'client-medrecscontrol.php', 
                type: 'POST',
                data: { medrec_id: medrecId },
                success: function(response) {
                    var data = JSON.parse(response);
                    if (data.success) {
                        
                        $("#addmedrecord").DataTable().row(row).remove().draw(); 

                        swal({
                            title: "Removed!",
                            text: "User access has been removed along with the medical record.",
                            icon: "success",
                            buttons: {
                                confirm: {
                                    className: "btn btn-success",
                                },
                            },
                        }).then(() => {
                            location.reload(); 
                        });
                    } else {
                        
                        console.error('Error message from server:', data.message); 
                        swal({
                            title: "Failed!",
                            text: data.message,
                            icon: "error",
                            buttons: {
                                confirm: {
                                    className: "btn btn-danger",
                                },
                            },
                        }).then(() => {
                            location.reload();    
                        });
                    }
                    location.reload();
                }
            });
        } else {
            swal.close();
        }
    });
});



});
</script>

</body>
</html>


