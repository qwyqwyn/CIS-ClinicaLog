<?php
session_start();
include('../database/config.php');
include('../php/user.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header('Location: ../php-login/index.php'); 
  exit; 
}

$db = new Database();
$conn = $db->getConnection();

$user = new User($conn); 
$user_idnum = $_SESSION['user_idnum'];
$userData = $user->getUserData($user_idnum); 
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>Clinic Staff User</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="../assets/img/ClinicaLog.ico" type="image/x-icon"/>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  </style>
  </head>




  <body>
    <div class="wrapper">
      <!-- Sidebar -->
      <div class="sidebar" id="sidebar">
        <div class="sidebar-logo">
          <!-- Logo Header -->
          <div class="logo-header" id="logo-header">
            <a href="index.php" class="logo">
              <img
                src="../assets/img/sidebar-logo.svg"
                alt="navbar brand"
                class="navbar-brand"
                height="60"
              />
            </a>
            <div class="nav-toggle">
              <button class="btn btn-toggle toggle-sidebar">
                <i class="gg-menu-right"></i>
              </button>
              <button class="btn btn-toggle sidenav-toggler">
                <i class="gg-menu-left"></i>
              </button>
            </div>
            <button class="topbar-toggler more">
              <i class="gg-more-vertical-alt"></i>
            </button>
          </div>
          <!-- End Logo Header -->
        </div>
        <div class="sidebar-wrapper scrollbar scrollbar-inner">
          <div class="sidebar-content">
            <ul class="nav nav-secondary">
              <li class="nav-section">
                <span class="sidebar-mini-icon">
                  <i class="fa fa-ellipsis-h"></i>
                </span>
              </li>
              <li class="nav-item">
                <a href="index.php">
                  <i class="fa fas fas fa-home"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datatables.php">
                  <i class="fa fas fa-capsules"></i>
                  <p>Medicine</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datatables.php">
                  <i class="fa fas fas fa-notes-medical"></i>
                  <p>Patient Record</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datatables.php">
                  <i class="fas far fa-calendar-check"></i>
                  <p>Appointments</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datatables.php">
                  <i class="fas fas fa-clipboard-list"></i>
                  <p>Inventory</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datatables.php">
                  <i class="fas fas fa-file-medical"></i>
                  <p>Reports</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="datatables.php">
                  <i class="fas fa-users"></i>
                  <p>Clinic Staff User</p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <!-- End Sidebar -->

      <div class="main-panel">
        <div class="main-header">
          <div class="main-header-logo">
            <!-- Logo Header -->
            <div class="logo-header" data-background-color="dark">
              <a href="../index.php" class="logo">
                <img
                  src="../assets/img/kaiadmin/logo_light.svg"
                  alt="navbar brand"
                  class="navbar-brand"
                  height="20"
                />
              </a>
              <div class="nav-toggle">
                <button class="btn btn-toggle toggle-sidebar">
                  <i class="gg-menu-right"></i>
                </button>
                <button class="btn btn-toggle sidenav-toggler">
                  <i class="gg-menu-left"></i>
                </button>
              </div>
              <button class="topbar-toggler more">
                <i class="gg-more-vertical-alt"></i>
              </button>
            </div>
            <!-- End Logo Header -->
          </div>
          <!-- Navbar Header -->
          <nav
            class="navbar navbar-header navbar-header-transparent navbar-expand-lg border-bottom"
          >
            <div class="container-fluid">
              <nav
                class="navbar navbar-header-left navbar-expand-lg navbar-form nav-search p-0 d-none d-lg-flex"
              >
                <div class="input-group">
                  <div class="input-group-prepend">
                    <button type="submit" class="btn btn-search pe-1">
                      <i class="fa fa-search search-icon"></i>
                    </button>
                  </div>
                  <input
                    type="text"
                    placeholder="Search ..."
                    class="form-control"
                  />
                </div>
              </nav>

              <ul class="navbar-nav topbar-nav ms-md-auto align-items-center">
                <li
                  class="nav-item topbar-icon dropdown hidden-caret d-flex d-lg-none"
                >
                  <a
                    class="nav-link dropdown-toggle"
                    data-bs-toggle="dropdown"
                    href="#" 
                    role="button"
                    aria-expanded="false"
                    aria-haspopup="true"
                  >
                    <i class="fa fa-search"></i>
                  </a>
                  <ul class="dropdown-menu dropdown-search animated fadeIn">
                    <form class="navbar-left navbar-form nav-search">
                      <div class="input-group">
                        <input
                          type="text"
                          placeholder="Search ..."
                          class="form-control"
                        />
                      </div>
                    </form>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="messageDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="fa fa-envelope"></i>
                  </a>
                  <ul
                    class="dropdown-menu messages-notif-box animated fadeIn"
                    aria-labelledby="messageDropdown"
                  >
                    <li>
                      <div
                        class="dropdown-title d-flex justify-content-between align-items-center"
                      >
                        Messages
                        <a href="#" class="small">Mark all as read</a>
                      </div>
                    </li>
                    <li>
                      <div class="message-notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="../assets/img/jm_denis.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Jimmy Denis</span>
                              <span class="block"> How are you ? </span>
                              <span class="time">5 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="../assets/img/chadengle.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Chad</span>
                              <span class="block"> Ok, Thanks ! </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="../assets/img/mlane.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Jhon Doe</span>
                              <span class="block">
                                Ready for the meeting today...
                              </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="../assets/img/talha.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="subject">Talha</span>
                              <span class="block"> Hi, Apa Kabar ? </span>
                              <span class="time">17 minutes ago</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="javascript:void(0);"
                        >See all messages<i class="fa fa-angle-right"></i>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item topbar-icon dropdown hidden-caret">
                  <a
                    class="nav-link dropdown-toggle"
                    href="#"
                    id="notifDropdown"
                    role="button"
                    data-bs-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                  >
                    <i class="fa fa-bell"></i>
                    <span class="notification">4</span>
                  </a>
                  <ul
                    class="dropdown-menu notif-box animated fadeIn"
                    aria-labelledby="notifDropdown"
                  >
                    <li>
                      <div class="dropdown-title">
                        You have 4 new notification
                      </div>
                    </li>
                    <li>
                      <div class="notif-scroll scrollbar-outer">
                        <div class="notif-center">
                          <a href="#">
                            <div class="notif-icon notif-primary">
                              <i class="fa fa-user-plus"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block"> New user registered </span>
                              <span class="time">5 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-icon notif-success">
                              <i class="fa fa-comment"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block">
                                Rahmad commented on Admin
                              </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-img">
                              <img
                                src="../assets/img/profile2.jpg"
                                alt="Img Profile"
                              />
                            </div>
                            <div class="notif-content">
                              <span class="block">
                                Reza send messages to you
                              </span>
                              <span class="time">12 minutes ago</span>
                            </div>
                          </a>
                          <a href="#">
                            <div class="notif-icon notif-danger">
                              <i class="fa fa-heart"></i>
                            </div>
                            <div class="notif-content">
                              <span class="block"> Farrah liked Admin </span>
                              <span class="time">17 minutes ago</span>
                            </div>
                          </a>
                        </div>
                      </div>
                    </li>
                    <li>
                      <a class="see-all" href="javascript:void(0);"
                        >See all notifications<i class="fa fa-angle-right"></i>
                      </a>
                    </li>
                  </ul>
                </li>
                <li class="nav-item topbar-user dropdown hidden-caret">
                <?php if ($userData): ?>
                  <a
                    class="dropdown-toggle profile-pic"
                    data-bs-toggle="dropdown"
                    href="#"
                    aria-expanded="false"
                  >
                    <div class="avatar-sm">
                      <img
                        src='../uploads/<?php echo htmlspecialchars($userData['user_profile']); ?>'
                        alt='Profile Picture'
                        class="avatar-img rounded-circle"
                      />
                    </div>
                    <span class="profile-username">
                      <span class="op-7">Hi,</span>
                      <span class="fw-bold"><?php echo htmlspecialchars($userData['user_fname']); ?></span>
                    </span>
                  </a>
                  <?php else: ?>
                      <p>User data not found.</p>
                  <?php endif; ?>
                  <ul class="dropdown-menu dropdown-user animated fadeIn">
                    <div class="dropdown-user-scroll scrollbar-outer">
                      <li>
                        <div class="user-box">
                          <div class="avatar-lg">
                            <img
                              src="../assets/img/profile.jpg"
                              alt="image profile"
                              class="avatar-img rounded"
                            />
                          </div>
                          <div class="u-text">
                            <h4>Hizrian</h4>
                            <p class="text-muted">hello@example.com</p>
                            <a
                              href="profile.html"
                              class="btn btn-xs btn-secondary btn-sm"
                              >View Profile</a
                            >
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">My Profile</a>
                        <a class="dropdown-item" href="#">My Balance</a>
                        <a class="dropdown-item" href="#">Inbox</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Account Setting</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Logout</a>
                      </li>
                    </div>
                  </ul>
                </li>
              </ul>
            </div>
          </nav>
          <!-- End Navbar -->
        </div>
        <div class="container">
          <div class="page-inner">
            <div class="row">
              <div class="col-md-12">
                <div class="card">
                  <div class="card-header">
                    <div class="d-flex align-items-center">
                      <h4 class="card-title">Add User</h4>
                      <button
                        class="btn btn-primary btn-round ms-auto"
                        data-bs-toggle="modal"
                        data-bs-target="#addRowModal"
                      >
                        <i class="fa fa-plus"></i>
                        Add User
                      </button>
                    </div>
                  </div>
                  <div class="card-body">
                    <!-- Modal -->
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
                              <span class="fw-mediumbold"> Staff User </span>
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
                              Create new user for the system. Make sure to fill all of them.
                            </p>
                             <!-- Start Add Modal Form-->
                             <form class="form" action="usercontrol.php" method="POST" enctype="multipart/form-data">
                              <div class="row">
                                <div class="col-md-12">
                                  <div class="form-group form-group-default">
                                    <label>ID</label>
                                    <input id="addid" name="addid" type="text" class="form-control" placeholder="fill ID" required />
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label>First Name</label>
                                    <input id="addfname" name="addfname" type="text" class="form-control" placeholder="fill first name" required/>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label>Last Name</label>
                                    <input id="addlname" name="addlname" type="text" class="form-control" placeholder="fill last name" required/>
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label>Middle Name</label>
                                    <input id="addmname" name="addmname" type="text" class="form-control" placeholder="fill middle name" required/>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label>Email</label>
                                    <input id="addemail" name="addemail" type="text" class="form-control" placeholder="fill email" required/>
                                  </div>
                                </div>
                                <div class="col-md-6 pe-0">
                                  <div class="form-group form-group-default">
                                    <label>Position</label>
                                    <input id="addposition" name="addposition" type="text" class="form-control" placeholder="fill position"required/>
                                  </div>
                                </div>
                                <div class="col-md-6">
                                  <div class="form-group form-group-default">
                                    <label>Status</label>
                                    <select id="addstatus" name="addstatus" class="form-control">
                                      <option value="Active">Active</option>
                                      <option value="Inactive">Inactive</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="col-md-12">
                                  <div class="form-group form-group-default">
                                    <label>Profile Upload</label>
                                    <input id="addprofile" name="addprofile" type="file" class="form-control" accept=".png, .jpg, .jpeg" />
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer border-0">
                              <button type="submit" class="btn btn-primary" name="addstaff">Add</button>
                              <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                              </div>
                            </form>
                            
                             <!-- End Add Modal Form-->`
                          </div>

                        </div>
                      </div>
                    </div>

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
                              <span class="fw-mediumbold"> Edit Staff User's Profile</span>
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
                  <!-- Start Edit Form -->
                  <form id="editForm" action="usercontrol.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="fw-light">Date Added: <span id="dateadded"></span></p>
                        </div>
                        <div class="col-md-12 text-center mb-4">
                            <div class="profile-display">
                                <!-- Display Current Profile Image (Placeholder if None) -->
                                <img id="currentProfile" src="" alt="Profile Picture" class="img-fluid rounded-circle" style="width: 150px; height: 150px;" />
                                <br>
                                <!-- Edit Profile Button -->
                                <label for="editprofile" class="btn btn-outline-primary mt-3">Edit Profile</label>
                                <input id="editprofile" name="editprofile" type="file" class="form-control d-none" accept=".png, .jpg, .jpeg" />
                            </div>
                        </div>
                        <!-- Other Input Fields -->
                        <div class="col-md-12">
                            <div class="form-group form-group-default">
                                <label>ID</label>
                                <input id="editid" name="editid" type="text" class="form-control" placeholder="fill ID" />
                                <input id="editoldid" name="editoldid" type="text" class="form-control" placeholder="fill ID" hidden/>

                            </div>
                        </div>
                        <div class="col-md-6 pe-0">
                            <div class="form-group form-group-default">
                                <label>First Name</label>
                                <input id="editfname" name="editfname" type="text" class="form-control" placeholder="fill first name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Last Name</label>
                                <input id="editlname" name="editlname" type="text" class="form-control" placeholder="fill last name" />
                            </div>
                        </div>
                        <div class="col-md-6 pe-0">
                            <div class="form-group form-group-default">
                                <label>Middle Name</label>
                                <input id="editmname" name="editmname" type="text" class="form-control" placeholder="fill middle name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Email</label>
                                <input id="editemail" name="editemail" type="text" class="form-control" placeholder="fill email" />
                            </div>
                        </div>
                        <div class="col-md-6 pe-0">
                            <div class="form-group form-group-default">
                                <label>Position</label>
                                <input id="editposition" name="editposition" type="text" class="form-control" placeholder="fill position" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group form-group-default">
                                <label>Status</label>
                                <!-- Dropdown for Status -->
                                <select id="editstatus" name="editstatus" class="form-control">
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select> 
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary" name="updateuser"> 
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
                    <div class="table-responsive">
                    <table id="add-row" class="display table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Profile</th>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Date Added</th>
                                <th>Status</th>
                                <th style="width: 10%">Action</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>Profile</th>
                                <th>ID</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Position</th>
                                <th>Date Added</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </tfoot> 
                        <tbody>
                        <?php
                        $nodes = $user->getAllUsers();
                        foreach ($nodes as $node) {
                            $fullName = "{$node->user_lname}, {$node->user_fname} {$node->user_mname}";
                            echo "<tr data-id='{$node->user_idnum}' data-lname='{$node->user_lname}' data-fname='{$node->user_fname}' data-mname='{$node->user_mname}' data-email='{$node->user_email}' data-position='{$node->user_position}' data-dateadded='{$node->user_dateadded}' data-status='{$node->user_status}'>
                                <td><img src='../uploads/{$node->user_profile}' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'></td>
                                <td>{$node->user_idnum}</td>
                                <td>{$fullName}</td>
                                <td>{$node->user_email}</td>
                                <td>{$node->user_position}</td>
                                <td>{$node->user_dateadded}</td>
                                <td>{$node->user_status}</td>
                                <td>
                                    <div class='form-button-action'>
                                        <button type='button' class='btn btn-link btn-primary btn-lg editButton'>
                                            <i class='fa fa-edit'></i>
                                        </button>
                                        <button type='button' class='btn btn-link btn-danger removeAccess'>
                                            <i class='fa fa-times'></i>
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
    // Initialize DataTable
    $("#add-row").DataTable({
        pageLength: 7,
    });

    // Add row
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
                $("#addstatus").val(),
                action,
            ]);
        $("#addRowModal").modal("hide");
    });

    $(document).on('click', '.editButton', function () {
        var row = $(this).closest('tr'); // Get the closest row
        var id = row.data('id');
        var lname = row.data('lname');
        var fname = row.data('fname');
        var mname = row.data('mname');
        var email = row.data('email');
        var position = row.data('position');
        var dateadded = row.data('dateadded');
        var status = row.data('status');
      
        $("#editid").val(id);
        $("#editoldid").val(id);
        $("#editlname").val(lname);
        $("#editfname").val(fname);
        $("#editmname").val(mname);
        $("#editemail").val(email);
        $("#editposition").val(position);
        $("#dateadded").text(dateadded);
        $("#editstatus").val(status);
       
        var profileImage = row.find('img').attr('src'); 
        $("#currentProfile").attr("src", profileImage);

        // Show the modal
        var myModal = new bootstrap.Modal(document.getElementById('editRowModal'));
        myModal.show();
    });

    $("#editRowButton").click(function () {
        var table = $("#add-row").DataTable();
        var row = table.row('.selected'); 

        // Update row data
        row.data([
            $("#dateadded").val(),
            $("#editprofile").val(),
            $("#editid").val(),
            $("#editfname").val(),
            $("#editlname").val(),
            $("#editmname").val(),
            $("#editemail").val(),
            $("#editposition").val(),
            $("#editstatus").val(),
            '<td> <div class="form-button-action"> <button class="editButton" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-primary btn-lg" data-original-title="Edit Task"> <i class="fa fa-edit"></i> </button> <button class="removeAccess" type="button" data-bs-toggle="tooltip" title="" class="btn btn-link btn-danger" data-original-title="Remove"> <i class="fa fa-times"></i> </button> </div> </td>',
        ]).draw();

        // Hide the modal
        $("#editRowModal").modal("hide");
    });

    $(document).on('click', '.removeAccess', function (e) {
    var row = $(this).closest('tr');
    var userId = row.data('id'); 
     
    swal({
        title: "Do you want to remove user?",
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
                url: 'usercontrol.php', 
                type: 'POST',
                data: { user_idnum: userId },
                success: function(response) {
                   
                    var data = JSON.parse(response); 
                    if (data.success) {
                        $("#add-row").DataTable().row(row).remove().draw(); 

                        swal({
                            title: "Removed!",
                            text: "User access has been removed.",
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
                error: function() {
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
    <script>
      document.getElementById('editprofile').addEventListener('change', function(event) {
    var file = event.target.files[0]; // Get the selected file

    if (file) {
        var reader = new FileReader(); // Create a new FileReader

        reader.onload = function(e) {
            // Update the src attribute of the img element
            document.getElementById('currentProfile').src = e.target.result;
        };

        reader.readAsDataURL(file); // Read the file as a Data URL
    }
});

    </script>

  </body>
</html>
