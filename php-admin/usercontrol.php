<?php
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../php-login/index.php'); 
    exit; 
  }
  

include('../database/config.php');
include('../php/user.php');

$db = new Database();
$conn = $db->getConnection();
$user = new User($conn);
$password = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['addstaff'])) {
        
        $id = trim($_POST['addid']);
        $first_name = $_POST['addfname'];
        $last_name = $_POST['addlname'];
        $middle_name = $_POST['addmname'];
        $email = filter_var($_POST['addemail'], FILTER_SANITIZE_EMAIL);
        $position = $_POST['addposition'];
        $role = $_POST['addrole'];
        $status = $_POST['addstatus'];
        $dateadded = date('Y-m-d H:i:s');
        $password = password_hash($id, PASSWORD_BCRYPT);  
        $code = 0; 

        $user_profile = '';
        if (isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
            $profile = $_FILES['addprofile'];
            $profile_original_name = basename($profile['name']);
            $profile_tmp = $profile['tmp_name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $profile_tmp);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            if (in_array($mime, $allowed_mimes)) {
                $profile_hash = md5(uniqid($profile_original_name, true));
                $profile_name = $profile_hash . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));
                $uploadDir = 'uploads/';
                $profile_destination = $uploadDir . $profile_name;

                if (move_uploaded_file($profile_tmp, $profile_destination)) {
                    $user_profile = $profile_name;
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Failed to upload profile picture.';
                }
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Invalid file type.';
            }
            finfo_close($finfo);
        }

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
  
            if ($user->register($id, $first_name, $last_name, $middle_name, $email, $position, $role, $status, $dateadded, $user_profile, $password, $code)) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'User registered successfully!';
                header('Location: staffuser.php');
                exit();
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Registration failed. Please try again.';
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Invalid email address.';
        }
        header('Location: staffuser.php');
        exit();
    }

    if (isset($_POST['updateuser'])) {

        $user_oldid = $_POST['editoldid'];
        $user_id = $_POST['editid'];
        $new_fname = $_POST['editfname'];
        $new_lname = $_POST['editlname'];
        $new_mname = $_POST['editmname'];
        $new_email = filter_var($_POST['editemail'], FILTER_SANITIZE_EMAIL);
        $new_position = $_POST['editposition'];
        $new_role = $_POST['editrole'];
        $new_status = $_POST['editstatus'];

        $new_profile = null;

        if (isset($_FILES['editprofile']) && $_FILES['editprofile']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['editprofile']['tmp_name'];

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $fileTmpPath);
            $allowed_mimes = ['image/jpeg', 'image/png'];

            if (in_array($mime, $allowed_mimes)) {
                $fileHash = md5(uniqid($_FILES['editprofile']['name'], true));
                $new_profile = $fileHash . '.' . strtolower(pathinfo($_FILES['editprofile']['name'], PATHINFO_EXTENSION));
                $uploadFileDir = 'uploads/';
                $dest_path = $uploadFileDir . $new_profile;

                if (!move_uploaded_file($fileTmpPath, $dest_path)) {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'Error moving the uploaded file.';
                }
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Invalid file type.';
            }
            finfo_close($finfo);
        }

        if ($user->updateUser($user_oldid, $user_id, $new_fname, $new_lname, $new_mname, $new_email, $new_position, $new_role, $new_status)) {

            if ($new_profile) {
                if ($user->updateProfilePicture($user_id, $new_profile)) {
                    $_SESSION['status'] = 'success';
                    $_SESSION['message'] = 'User updated successfully!';
                } else {
                    $_SESSION['status'] = 'error';
                    $_SESSION['message'] = 'User updated, but failed to update profile picture.';
                }
            } else {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'User updated successfully!';
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Failed to update user.';
        }

        header('Location: staffuser.php');
        exit();
    }

    if (isset($_POST['user_id'])) {
        $user_id = $_POST['user_id'];
        
        if ($user->deleteUser($user_id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => $_SESSION['message']]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No user ID provided']);
    }
}
?>