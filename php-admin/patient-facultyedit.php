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

$db = new Database();
$conn = $db->getConnection();

$patient = new PatientManager($db);
$user = new User($conn);
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum);

if (isset($_SESSION['id']) && isset($_SESSION['type'])) {
    $patientId = $_SESSION['id'];
    $patientType = $_SESSION['type'];

    $patientDetails = $patient->getFacultyData($patientId);
} else {
    echo "No patient data found.";
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
        justify-content: center ;
        align-items: center ;
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

      
      .hidden {
            display: none;
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
        <h4 class="card-title">Edit Patient's Information</h4>
    </div>
    <!-- Form Starts Here -->
    <form id="uppatientForm" action="patientcontrol.php" method="POST" enctype="multipart/form-data">
    <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
    <input type="hidden" class="form-control" id="patientid" name="patientid" value="<?php echo $patientId; ?>" />
    <div class="row">
        <div class="profile-image col-md-3 text-center mx-auto d-flex flex-column align-items-center">
            <img id="profilePic" src="default-image.jpg" alt="Profile Image" class="img-thumbnail mb-2" />
            <label for="addprofile" class="form-label">Upload New Profile</label>
            <input id="addprofile" name="addprofile" type="file" class="form-control" accept=".png, .jpg, .jpeg" style="border: 2px solid #DA6F65;"  />
        </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="d-flex align-items-center ">
                  <h4 class="card-title">Personal Details</h4>
                </div>
              </div>
              <div class="card-body" id="InputInfo">  
                    <!-- Name Fields -->
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="last name"/>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="first name"  />
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middleName" placeholder="middle name" />
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob"  />
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="sex" class="form-label">Sex</label>
                            <select class="form-select form-control" id="sex" name="sex" >
                                <option selected disabled>Select Sex</option>
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                            </select>
                        </div>
                    </div>

                    <!-- ID and Academic Info -->
                    <div class="row">
                        <div class="col-md-2 mb-3">
                            <label for="facultyID" class="form-label">ID Number</label>
                            <input type="text" class="form-control" id="facultyID" name="facultyID" placeholder="ID number"  />
                        </div> 

                        <!-- college Input -->
                        <div class="col-md-4 mb-3">
                            <label for="college" class="form-label">College</label>
                            <select class="form-select form-control" id="college" name="college" >
                                <option value="Click to type...">Click to type...</option>
                                <option value="College of Teacher Education and Technology">College of Teacher Education and Technology</option>
                                <option value="College of Engineering">College of Engineering</option>
                                <option value="School of Medicine">School of Medicine</option>
                            </select>  
                                                
                            <!-- Text input for custom college (hidden initially) -->
                            <div id="collegeInputContainer" class="hidden">
                                <input type="text" class="form-control" id="collegeInput"  name="customCollege" placeholder="Enter your college">
                                </div>
                            </div>

                        <!-- department Input -->
                        <div class="col-md-4 mb-3">
                            <label for="department" class="form-label">Department</label>
                            <select class="form-control form-select" id="department" name="department" >
                                <option value="">Select Department</option>
                            </select>

                            <div id="departmentInputContainer" class="hidden">
                                <input type="text" class="form-control" id="departmentInput" name="customDepartment" placeholder="Enter your department">
                            </div>
                         
                            <!-- Back to dropdown icon button (hidden initially) -->
                            <button type="button" id="backToDropdown" class="hidden">
                                <i class='fa fa-undo'></i>
                            </button>
                        </div>


                        <div class="col-md-2 mb-3">
                            <label for="role" class="form-label">Role</label>
                            <input type="text" class="form-control" id="role" name="role" placeholder="Role"  />
                        </div>
                    </div>
                    

                    <!-- Address Fields -->
                    <h5>Current Address</h5>
                    <div class="row">
                        <!-- Region Input -->
                        <div class="col-md-2 mb-3">
                            <label for="region" class="form-label">Region</label>
                            <select class="form-select form-control" id="region" name="region" placeholder="Enter Region" >
                            <option value="">Select Region</option>
                            <option value="Region XI">Region XI</option>
                            <option value="Region XII">Region XII</option>
                            </select>
                        </div>
                        <!-- Province Input -->
                        <div class="col-md-3 mb-3">
                            <label for="province" class="form-label">Province</label>
                            <select class="form-control" id="province" name="province" placeholder="Enter Province" >
                                <option value="">Select Province</option>
                            </select>
                        </div>

                        <!-- Municipality Input -->
                        <div class="col-md-3 mb-3">
                            <label for="municipality" class="form-label">Municipality</label>
                            <select class="form-select form-control" id="municipality" name="municipality" placeholder="Enter Municipality" >
                                <option value="">Select Municipality</option>
                            </select>
                        </div>

                        <!-- Barangay Input -->
                        <div class="col-md-2 mb-3">
                            <label for="barangay" class="form-label">Barangay</label>
                            <select class="form-select form-control" id="barangay" name="barangay" placeholder="Enter Barangay" >
                                <option value="">Select Barangay</option>
                            </select>
                        </div>


                        <!-- Street Input (Text Field) -->
                        <div class="col-md-2 mb-3">
                            <label for="street" class="form-label">Purok/Block No./Street</label>
                            <input type="text" class="form-control" id="street" name="street" placeholder="Enter street address"  />
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"  />
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="contactNumber" class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" id="contactNumber" name="contactNumber" placeholder="Contact number"  />
                        </div>
                    </div>

                    <!-- Emergency Contact Information -->
                    <h5>Emergency Contact Information</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="emergencyContactName" class="form-label">Emergency Contact Name</label>
                            <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" placeholder="Emergency contact name"  />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="relationship" class="form-label">Relationship</label>
                            <input type="text" class="form-control" id="relationship" name="relationship" placeholder="Relationship" />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="emergencyContactNumber" class="form-label">Emergency Contact Number</label>
                            <input type="tel" class="form-control" id="emergencyContactNumber" name="emergencyContactNumber" placeholder="Emergency contact number"  />
                        </div>
                    </div>

                    <div class="row">
                        <h5>Patient's Account Status</h5>
                        <div class="col-md-2 mb-3">
                            <label for="Status" class="form-label">Status</label>
                            <select class="form-select form-control" id="Status" name="Status" >
                                <option selected disabled>Select Status</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary" id="editfacultypatient" name="editfacultypatient">
                            Save
                        </button>
                        
                        <button type="button" class="btn btn-primary ms-3" id="canceladdpatient">
                            Back
                        </button>
                        </div>
                    </div>
                </form> 
                <!-- End of Form -->
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

<!-- jQuery Sparkline -->
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Bootstrap Notify -->
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Sweet Alert -->
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>

<script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        $("#sidebar").load("sidebar.php", function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading sidebar: " + xhr.status + " " + xhr.statusText);
            }
        });

        $("#header").load("header.php", function(response, status, xhr) {
            if (status == "error") {
                console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
            }
        });

        <?php if (isset($_SESSION['status']) && isset($_SESSION['message'])): ?>
        var status = '<?php echo $_SESSION['status']; ?>';
        var message = '<?php echo htmlspecialchars($_SESSION['message'], ENT_QUOTES); ?>';
        Swal.fire({
            title: status === 'success' ? "Success!" : "Error!",
            text: message,
            icon: status,
            confirmButtonText: "OK",
            confirmButtonColor: status === 'success' ? "#77dd77" : "#ff6961"
        }).then(() => {
            if (status === 'success') {
              sessionStorage.clear();
                window.location.href = "patient-facultyedit.php";
            }
            <?php unset($_SESSION['status'], $_SESSION['message']); ?>
        });
        <?php endif; ?>

        confirmCancelPatient();

        var patientData = <?php echo json_encode($patientDetails); ?>;
        
        function populatePatientForm(data) {
            $('#lastName').val(data.patient.patient_lname || '');
            $('#firstName').val(data.patient.patient_fname || '');
            $('#middleName').val(data.patient.patient_mname || '');
            $('#dob').val(data.patient.patient_dob || '');
            $('#sex').val(data.patient.patient_sex || 'Male');
            $('#facultyID').val(data.faculty.faculty_idnum || '');
            $('#college').val(data.faculty.faculty_college || '').trigger('change');
            $('#role').val(data.faculty.faculty_role || '');
            $('#region').val(data.address.address_region || '').trigger('change'); 
            $('#province').val(data.address.address_province || '').trigger('change'); 
            $('#municipality').val(data.address.address_municipality || '').trigger('change'); 
            $('#barangay').val(data.address.address_barangay || '');
            $('#street').val(data.address.address_prkstrtadd || '');
            $('#email').val(data.patient.patient_email || '');
            $('#contactNumber').val(data.patient.patient_connum || '');
            $('#emergencyContactName').val(data.emergencyContact.emcon_conname || '');
            $('#relationship').val(data.emergencyContact.emcon_relationship || '');
            $('#emergencyContactNumber').val(data.emergencyContact.emcon_connum || '');
            $('#Status').val(data.patient.patient_status || '');
            $('#profilePic').attr('src' , `../uploads/${data.patient.patient_profile}` || 'default-image.jpg');
        }


    
    function confirmCancelPatient() {
        $('#canceladdpatient').click(function(event) {
                event.preventDefault();

                let isFormFilled = false;

                $('#uppatientForm input, facultyForm select, facultyForm textarea').each(function() {
                    if ($(this).val() !== '') {
                        isFormFilled = true; 
                        return false; 
                    }
                });

                if (isFormFilled) {
                    Swal.fire({
                        title: "Are you sure?",
                        text: "Do you really want to cancel updating this patient? Unsaved information will be lost.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, cancel it!"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            sessionStorage.clear();
                            window.location.href = "patient-record.php";
                        }
                    });
                } else {

                    window.location.href = "patient-record.php";
                }
            });
    }

    populatePatientForm(patientData);

    const addressOptions = {
            regions: {
                "Region XI": {
                    provinces: {
                        "Davao del Norte": {
                            municipalities: ["Tagum City", "Sto. Tomas"],
                            barangays: {
                                "Tagum City": ["Apokon", "Pagsabangan"],
                                "Sto. Tomas": ["Kinamayan", "Poblacion"]
                            }
                        },
                        "Davao de Oro": {
                            municipalities: ["Pantukan", "Nabunturan"],
                            barangays: {
                                "Pantukan": ["Kingking", "Magnaga"],
                                "Nabunturan": ["Anislagan", "Poblacion"]
                            }
                        }
                    }
                },
                "Region XII": {
                    provinces: {
                        "Cotabato": {
                            municipalities: ["Alamada", "Carmen"],
                            barangays: {
                                "Alamada": ["Camansi", "Macabasa"],
                                "Carmen": ["Bentangan", "General Luna"]
                            }
                        }
                    }
                }
            }
        };

    let currentField = '';

    // Define departments based on college selection
    const departmentOptions = {
      'Click to type...': [],
      'College of Teacher Education and Technology': ['Click to type...', 'Department of Elementary Education', 
                                                        'Department of Special Needs Education', 
                                                        'Department of Secondary Education',
                                                        'Department of Science in Information Technology',
                                                        'Department of Technical-Vocational Teacher Education',
                                                    ],
      'College of Engineering': ['Click to type...', 'Department of Science in Agricultural and Biosystems Engineering'],
      'School of Medicine': ['Click to type...', 'Doctor of Medicine']
    };
    
    function updateDepartmentDropdown(selectedCollege) {
        const departments = departmentOptions[selectedCollege] || [];
        $('#department').empty(); 

        $.each(departments, function(index, department) {
            $('#department').append(`<option value="${department}">${department}</option>`);
        });

        if (!departments.includes('Other')) {
            $('#department').append('<option value="" hidden></option>');
        }
    }

    function populateProvinces(selectedRegion) {
        const provinceSelect = $('#province');
        provinceSelect.empty().append('<option selected >Select Province</option>');
        if (selectedRegion && addressOptions.regions[selectedRegion]) {
            Object.keys(addressOptions.regions[selectedRegion].provinces).forEach(function(province) {
                provinceSelect.append(`<option value="${province}">${province}</option>`);
            });
        }
        provinceSelect.val(patientData.address.address_province).trigger('change'); 
    }

    function populateMunicipalities(selectedRegion, selectedProvince) {
        const municipalitySelect = $('#municipality');
        municipalitySelect.empty().append('<option selected >Select Municipality</option>');
        if (selectedProvince && addressOptions.regions[selectedRegion].provinces[selectedProvince]) {
            const municipalities = addressOptions.regions[selectedRegion].provinces[selectedProvince].municipalities;
            municipalities.forEach(function(municipality) {
                municipalitySelect.append(`<option value="${municipality}">${municipality}</option>`);
            });
        }
        municipalitySelect.val(patientData.address.address_municipality).trigger('change'); // Set the selected municipality
    }

    function populateBarangays(selectedRegion, selectedProvince, selectedMunicipality) {
        const barangaySelect = $('#barangay');
        barangaySelect.empty().append('<option selected >Select Barangay</option>');
        if (selectedMunicipality && addressOptions.regions[selectedRegion].provinces[selectedProvince]) {
            const barangays = addressOptions.regions[selectedRegion].provinces[selectedProvince].barangays[selectedMunicipality];
            barangays.forEach(function(barangay) {
                barangaySelect.append(`<option value="${barangay}">${barangay}</option>`);
            });
        }
        barangaySelect.val(patientData.address.address_barangay).trigger('change'); // Set the selected barangay
    }

    // Function to check if the value exists in a dropdown
    function checkIfExistsInDropdown(dropdown, value) {
        return $(dropdown).find(`option[value='${value}']`).length > 0;
    }

    $('#region').on('change', function() {
        populateProvinces($(this).val());
    });

    $('#province').on('change', function() {
        populateMunicipalities($('#region').val(), $(this).val());
    });

    $('#municipality').on('change', function() {
        populateBarangays($('#region').val(), $('#province').val(), $(this).val());
    });

    if (patientData.faculty.faculty_college && !checkIfExistsInDropdown('#college', patientData.faculty.faculty_college)) {
        $('#college').hide();
        $('#collegeInputContainer').removeClass('hidden');
        $('#collegeInput').val(patientData.faculty.faculty_college); 
        $('#backToDropdown').removeClass('hidden'); 
        currentField = 'college';
    } else {
        $('#college').val(patientData.faculty.faculty_college); 
        updateDepartmentDropdown(patientData.faculty.faculty_college); 
    }
    
    if (patientData.faculty.faculty_department && !checkIfExistsInDropdown('#department', patientData.faculty.faculty_department)) {
        $('#department').hide();
        $('#departmentInputContainer').removeClass('hidden');
        $('#departmentInput').val(patientData.faculty.faculty_department); 
        $('#backToDropdown').removeClass('hidden');
        currentField = 'department';
    } else {
        $('#department').val(patientData.faculty.faculty_department); 
    }

    if (patientData.faculty.faculty_college) {
        $('#college').val(patientData.faculty.faculty_college).trigger('change');
    }

    if (patientData.address.address_region) {
        $('#region').val(patientData.address.address_region).trigger('change');
    }

    if (patientData.address.address_province) {
        $('#province').val(patientData.address.address_province).trigger('change');
    }

    if (patientData.address.address_municipality) {
        $('#municipality').val(patientData.address.address_municipality).trigger('change');
    }

    if (patientData.address.address_barangay) {
        $('#barangay').val(patientData.address.address_barangay);
    }

    $('#college').on('change', function() {
        const selectedCollege = $(this).val();
        updateDepartmentDropdown(selectedCollege); 
        $('#departmentInputContainer').addClass('hidden');
        $('#department').show();

        if (selectedCollege === 'Click to type...') {
                
            $('#college').hide();
            $('#collegeInputContainer').removeClass('hidden');
            $('#backToDropdown').removeClass('hidden');
            currentField = 'college';

            $('#department').hide();
            $('#departmentInputContainer').removeClass('hidden');
            currentField = 'department';
        } else {
            $('#departmentInputContainer').addClass('hidden'); 
        }
    });

    $('#department').on('change', function() {
        if ($(this).val() === 'Click to type...') {
                
            $('#department').hide();
            $('#departmentInputContainer').removeClass('hidden');
            $('#backToDropdown').removeClass('hidden');
            
            currentField = 'department';
            
        }
    });

    $('#backToDropdown').on('click', function() {

        $('#collegeInputContainer').addClass('hidden');
        $('#college').show();

        $('#departmentInputContainer').addClass('hidden');
        $('#department').show();

        $('#college').val(patientData.faculty.faculty_college); 
        updateDepartmentDropdown(patientData.faculty.faculty_college); 
        $('#department').val(patientData.faculty.faculty_department); 

        $(this).addClass('hidden');
    });

});
</script>

</body>
</html>