<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title>User Profile</title>
  <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
  <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon" />

  <!-- Fonts and icons -->
  <script src="../assets/js/plugin/webfont/webfont.min.js"></script>
  <script>
    WebFont.load({
      google: {
        families: ["Public Sans:300,400,500,600,700"]
      },
      custom: {
        families: ["Font Awesome 5 Solid", "Font Awesome 5 Regular", "Font Awesome 5 Brands", "simple-line-icons"],
        urls: ["../css/fonts.min.css"],
      },
      active: function() {
        sessionStorage.fonts = true;
      },
    });
  </script>

  <!-- CSS Files -->
  <link rel="stylesheet" href="../css/bootstrap.min.css" />
  <link rel="stylesheet" href="../css/plugins.min.css" />
  <link rel="stylesheet" href="../css/kaiadmin.min.css" /> 
  <link rel="stylesheet" href="../css/client.css">

  <!-- ICONS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-pQnI6Z1ypA1QPTDdTnYkkpN0sE+0ZK3SAs+69IXS7SgSR/RG6upgjB8cSBaHh0FYv3cwUqq3Kv1BrV3iwGsnZw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
    .profile-image {
      text-align: center;
    }

    .profile-image img {
      border-radius: 50%;
      width: 150px;
      height: 150px;
      margin-bottom: 10px;
    }

    @media (max-width: 576px) {
      .profile-image img {
        width: 120px;
        height: 120px;
      }
    }

    .card-body {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
      /* Ensure content is spaced evenly */
      max-height: 450px;
      /* Set a max height */
      overflow-y: auto;
      /* Enable vertical scroll */
    }

    .table-responsive {
      max-height: 300px;
      /* Set a max height for the table */
      overflow-y: auto;
      /* Enable vertical scroll */
    }
  </style>
</head>

<body>
  <div class="wrapper">
    <div class="main-panel" id="clientpanel">
      <!-- Header -->
      <div class="main-header" id="client_header"></div>
      <!-- Main Content -->
      <div class="container" id="content">
        <div class="page-inner">
          <!-- Modal Structure -->
          <div class="row">
            <div class="col-12">
              <a href="<?php echo $redirectPage; ?>" class="back-nav">
                <i class="fas fa-arrow-left"></i> Back to Appointment
              </a>
            </div>
          </div>
          <div class="row">
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header">
                  <div class="d-flex align-items-center">
                    <h4 class="card-title">Personal Details</h4>
                  </div>
                </div>
                <div class="card-body" id="InputInfo">
                  <div class="profile-image">
                    <img id="profilePic" src="../assets/img/profile.jpg" alt="Profile Image" />
                  </div>
                  <!-- Name Fields -->
                  <div class="row">
                    <div class="col-md-3 mb-3">
                      <label for="lastName" class="form-label">Last Name</label>
                      <input type="text" class="form-control" id="lastName" value="Furog" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="firstName" class="form-label">First Name</label>
                      <input type="text" class="form-control" id="firstName" value="Jackilyn" readonly />
                    </div>
                    <div class="col-md-2 mb-3">
                      <label for="middleName" class="form-label">Middle Name</label>
                      <input type="text" class="form-control" id="middleName" value="Mancao" readonly />
                    </div>
                    <div class="col-md-2 mb-3">
                      <label for="dob" class="form-label">Date of Birth</label>
                      <input type="date" class="form-control" id="dob" value="06/28/2003" readonly />
                    </div>
                    <div class="col-md-2 mb-3">
                      <label for="sex" class="form-label">Sex</label>
                      <input type="text" class="form-control" name="sex" id="sex" value="F" readonly>
                    </div>
                  </div>

                  <!-- ID and Academic Info -->
                  <div class="row">
                    <div class="col-md-2 mb-3">
                      <label for="studentID" class="form-label">ID Number</label>
                      <input type="text" class="form-control" id="studentID" value="2022-00473" readonly />
                    </div>

                    <!-- Program Input -->
                    <div class="col-md-3 mb-3">
                      <label for="program" class="form-label">Program</label>
                      <input type="text" class="form-control" name="program" id="program" value="Bachelor of Science in Information Technology" readonly>
                    </div>

                    <!-- Major Input -->
                    <div class="col-md-2 mb-3">
                      <label for="major" class="form-label">Major</label>
                      <input type="text" class="form-control" name="major" id="major" value="Information Security" readonly>
                    </div>

                    <!-- Year Dropdown -->
                    <div class="col-md-2 mb-3">
                      <label for="year" class="form-label">Year</label>
                      <input type="text" class="form-control" name="year" id="year" value="3rd Year" readonly>
                    </div>

                    <div class="col-md-2 mb-3">
                      <label for="section" class="form-label">Section</label>
                      <input type="text" class="form-control" id="section" name="section" value="3IT" readonly />
                    </div>
                  </div>

                  <!-- Address Fields -->
                  <h5>Current Address</h5>
                  <div class="row">
                    <!-- Region Input -->
                    <div class="col-md-2 mb-3">
                      <label for="region" class="form-label">Region</label>
                      <input type="text" class="form-control" id="region" name="region" value="Region XI" readonly />
                    </div>
                    <!-- Province Input -->
                    <div class="col-md-3 mb-3">
                      <label for="province" class="form-label">Province</label>
                      <input type="text" class="form-control" id="province" name="province" value="Davao de Oro" readonly />
                    </div>

                    <!-- Municipality Input -->
                    <div class="col-md-3 mb-3">
                      <label for="municipality" class="form-label">Municipality</label>
                      <input type="text" class="form-control" id="municipality" name="municipality" value="Pantukan" readonly />
                    </div>

                    <!-- Barangay Input -->
                    <div class="col-md-2 mb-3">
                      <label for="barangay" class="form-label">Barangay</label>
                      <input type="text" class="form-control" id="barangay" name="barangay" value="Kingking" readonly />
                    </div>


                    <!-- Street Input (Text Field) -->
                    <div class="col-md-2 mb-3">
                      <label for="street" class="form-label">Purok/Block No./Street</label>
                      <input type="text" class="form-control" id="street" name="street" value="E.Quirino" readonly />
                    </div>
                  </div>

                  <!-- Contact Information -->
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="email" class="form-label">Email Address</label>
                      <input type="email" class="form-control" id="email" name="email" value="jmfurog@usep.edu.ph" readonly />
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="contactNumber" class="form-label">Contact Number</label>
                      <input type="tel" class="form-control" id="contactNumber" name="contactNumber" value="09756066512" readonly />
                    </div>
                  </div>

                  <!-- Emergency Contact Information -->
                  <h5>Emergency Contact Information</h5>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="emergencyContactName" class="form-label">Emergency Contact Name</label>
                      <input type="text" class="form-control" id="emergencyContactName" name="emergencyContactName" value="Jocelyn M. Furog" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="relationship" class="form-label">Relationship</label>
                      <input type="text" class="form-control" id="relationship" name="relationship" value="Mother" readonly />
                    </div>
                    <div class="col-md-3 mb-3">
                      <label for="emergencyContactNumber" class="form-label">Emergency Contact Number</label>
                      <input type="tel" class="form-control" id="emergencyContactNumber" name="emergencyContactNumber" value="09752646165" readonly />
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Core JS Files -->
      <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
      <script src="../assets/js/core/popper.min.js"></script>
      <script src="../assets/js/core/bootstrap.min.js"></script>

      <!-- Core JS Files -->
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

      <script>
        $(document).ready(function() {
          // Load Header
          $("#client_header").load("clientheader.php", function(response, status, xhr) {
            if (status == "error") {
              console.log("Error loading header: " + xhr.status + " " + xhr.statusText);
            }
          });
        });
      </script>
</body>

</html>