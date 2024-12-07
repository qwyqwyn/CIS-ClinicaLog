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
    $patientDetails = $patient->getExtensionData($patientId);
} else {
    echo "No patient data found.";
}
?>

<!DOCTYPE html> 
<html lang="en">
<head>
<meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Edit Patient Information</title> 
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
            <h4 class="card-title">Edit Patient's Information</h4>
        </div>
        <!-- Form Starts Here --> 
        <form id="uppatientForm" action="patientcontrol.php" method="POST" enctype="multipart/form-data">   
        <input type="hidden" class="form-control" id="patientid" name="patientid" value="<?php echo $patientId; ?>" />  
        <input id="admin_id" name="admin_id" type="hidden" class="form-control" value="<?php echo htmlspecialchars($user_idnum, ENT_QUOTES, 'UTF-8'); ?>"/>
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
                            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="  last name"  />
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="  first name"  />
                        </div>
                        <div class="col-md-2 mb-3">
                            <label for="middleName" class="form-label">Middle Name</label>
                            <input type="text" class="form-control" id="middleName" name="middleName" placeholder="  middle name" />
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
                            <label for="extenID" class="form-label">ID Number</label>
                            <input type="text" class="form-control" id="extenID" name="extenID" placeholder="ID number"  />
                        </div>

                        <div class="col-md-4 mb-3">
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
                            <option selected disabled>Select Region</option>
                            <option value="Region XI">Region XI</option>
                            <option value="Region XII">Region XII</option>
                            </select>
                        </div>
                        <!-- Province Input -->
                        <div class="col-md-3 mb-3">
                            <label for="province" class="form-label">Province</label>
                            <select class="form-select form-control" id="province" name="province" placeholder="Enter Province" >
                                <option selected disabled>Select Province</option>
                            </select>
                        </div>

                        <!-- Municipality Input -->
                        <div class="col-md-3 mb-3">
                            <label for="municipality" class="form-label">Municipality</label>
                            <select class="form-select form-control" id="municipality" name="municipality" placeholder="Enter Municipality" >
                                <option selected disabled>Select Municipality</option>
                            </select>
                        </div>

                        <!-- Barangay Input -->
                        <div class="col-md-2 mb-3">
                            <label for="barangay" class="form-label">Barangay</label>
                            <select class="form-select form-control" id="barangay" name="barangay" placeholder="Enter Barangay" >
                                <option selected disabled>Select Barangay</option>
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
                        <button type="submit" class="btn btn-primary" id="editextenpatient" name="editextenpatient">
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
                window.location.href = "patient-extenedit.php";
            }
            <?php unset($_SESSION['status'], $_SESSION['message']); ?>
        });
        <?php endif; ?>

        confirmCancelPatient();

        var patientData = <?php echo json_encode($patientDetails); ?>;

        function populatePatientForm(patientData) {
            document.getElementById('lastName').value = patientData.patient.patient_lname || '';
            document.getElementById('firstName').value = patientData.patient.patient_fname || '';
            document.getElementById('middleName').value = patientData.patient.patient_mname || '';
            document.getElementById('dob').value = patientData.patient.patient_dob || '';
            document.getElementById('sex').value = patientData.patient.patient_sex || 'Male';
            document.getElementById('extenID').value = patientData.extension.exten_idnum || '';
            document.getElementById('role').value = patientData.extension.exten_role || '';
            document.getElementById('region').value = patientData.address.address_region || '';
            document.getElementById('province').value = patientData.address.address_province || '';
            document.getElementById('municipality').value = patientData.address.address_municipality || '';
            document.getElementById('barangay').value = patientData.address.address_barangay || '';
            document.getElementById('street').value = patientData.address.address_prkstrtadd || '';
            document.getElementById('email').value = patientData.patient.patient_email || '';
            document.getElementById('contactNumber').value = patientData.patient.patient_connum || '';
            document.getElementById('emergencyContactName').value = patientData.emergencyContact.emcon_conname || '';
            document.getElementById('relationship').value = patientData.emergencyContact.emcon_relationship || '';
            document.getElementById('emergencyContactNumber').value = patientData.emergencyContact.emcon_connum || '';
            document.getElementById('Status').value = patientData.patient.patient_status || '';
            document.getElementById('profilePic').src = `uploads/${patientData.patient.patient_profile}` || 'default-image.jpg';
    }

    function confirmCancelPatient() {
        $('#canceladdpatient').click(function(event) {
                event.preventDefault();

                let isFormFilled = false;

                $('#uppatientForm input, extenForm select, extenForm textarea').each(function() {
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
        let currentField = '';
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

        $('#region').on('change', function() {
            populateProvinces($(this).val());
        });

        $('#province').on('change', function() {
            populateMunicipalities($('#region').val(), $(this).val());
        });

        $('#municipality').on('change', function() {
            populateBarangays($('#region').val(), $('#province').val(), $(this).val());
        });

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

    });
</script>
</body>
</html>