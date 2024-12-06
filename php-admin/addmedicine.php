
<?php
session_start();
include('../database/config.php');
include('../php/user.php');
include('../php/medicine.php');


$db = new Database();
$conn = $db->getConnection(); 

$medicine = new MedicineManager($conn); 

$user_idnum = $_SESSION['user_idnum']; 
?>
<!DOCTYPE html>
<html lang="en">

<body>

<div class="container mt-4">
    <div class="row">
        <!-- Medicine Form Card -->
        <div class="col-md-4">
            <div class="card card-equal-height">
                <div class="card-header"> 
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">Medicine Details</h4> 
                    </div>
                </div> 
                <div class="card-body"> 
                    <form id="medicineForm" action="medicinecontrol.php" method="POST">
                    <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>

                    <input type="hidden" id="medicineId" name="medicineId" value="" />
                        <div class="form-group mb-3">
                            <label for="medicineName">Medicine Name</label>
                            <input type="text" id="medicineName" name="medicineName" class="form-control" placeholder="Enter medicine name" required />
                        </div>
                        <div class="form-group mb-3">
                            <label for="medicineCategory">Category</label>
                            <input type="text" id="medicineCategory" name="medicineCategory" class="form-control" placeholder="Enter category" required />
                        </div>
                        <div class="modal-footer border-0 mt-auto">
                    <button type="submit" class="btn btn-primary" id="addmed" name="addmed">Submit</button>
                    <button type="reset" class="btn btn-secondary ms-2">Clear</button>
                </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Medicines Table Card -->
        <div class="col-md-8">
            <div class="card card-equal-height">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <h4 class="card-title">List of Medicine</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="add-med" class="display table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Stocks</th>
                                    <th style="width: 10%">Action</th>
                                </tr>
                            </thead>
                            <tfoot>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Category</th>
                                    <th>Stocks</th>
                                    <th>Action</th>
                                </tr>
                            </tfoot>
                            <tbody>
                            <?php
                           $items = $medicine->getMedicinesWithStockCount();

                            foreach ($items as $item) {
                                echo "<tr data-id='{$item['medicine_id']}' 
                                        data-name='{$item['medicine_name']}' 
                                        data-category='{$item['medicine_category']}' 
                                        data-stock='{$item['stock_count']}'>
                                        <td>{$item['medicine_id']}</td>
                                        <td>{$item['medicine_name']}</td>
                                        <td>{$item['medicine_category']}</td>
                                        <td>{$item['stock_count']}</td>
                                        <td>
                                          <div class='form-button-action'>
                                              <button type='button' class='btn btn-link btn-primary btn-lg editMedButton'>
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



<script>
$(document).ready(function () {
    // Initialize DataTable
    $("#add-med").DataTable({
        pageLength: 3, // Set the default number of rows per page
        responsive: true, // Make the table responsive
    });

    // Handle edit button click
    $(document).on("click", ".editMedButton", function () {
        // Find the closest row (tr) to the button clicked
        var $row = $(this).closest("tr");

        // Get the data from the row
        var id = $row.find("td:eq(0)").text(); // ID
        var name = $row.find("td:eq(1)").text(); // Medicine Name
        var category = $row.find("td:eq(2)").text(); // Category

        // Populate the form fields with the selected row's data
        $("#medicineId").val(id);
        $("#medicineName").val(name);
        $("#medicineCategory").val(category);

        // Scroll to the form (optional)
        $('html, body').animate({
            scrollTop: $("#medicineForm").offset().top
        }, 500);
    });
});
</script>


</body>
</html>
