<?php
session_start();
include('../database/config.php');

$db = new Database();
$conn = $db->getConnection();

$response = ['status' => 'error', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_POST['user_idnum']) && isset($_FILES['addprofile']) && $_FILES['addprofile']['error'] === UPLOAD_ERR_OK) {
        $user_idnum = $_POST['user_idnum'];
        $profile = $_FILES['addprofile'];
        $profile_original_name = basename($profile['name']);
        $profile_tmp = $profile['tmp_name'];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $profile_tmp);
        $allowed_mimes = ['image/jpeg', 'image/png'];
 
        if (in_array($mime, $allowed_mimes)) {
  
            $profile_hash = md5(uniqid($profile_original_name, true));
            $profile_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $profile_hash) . '.' . strtolower(pathinfo($profile_original_name, PATHINFO_EXTENSION));

            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . './uploads/';

            if (!is_writable($uploadDir)) {
                $response['message'] = 'The upload directory "' . $uploadDir . '" is not writable. Please check directory permissions.';
                echo json_encode($response);
                exit();
            }
  
            $profile_destination = $uploadDir . $profile_name;

            if (move_uploaded_file($profile_tmp, $profile_destination)) {

                $sql_update = "UPDATE adminusers SET user_profile = :user_profile WHERE user_idnum = :user_idnum";
                $stmt = $conn->prepare($sql_update);

                $stmt->bindValue(':user_profile', $profile_name, PDO::PARAM_STR);
                $stmt->bindValue(':user_idnum', $user_idnum, PDO::PARAM_STR); // Ensure the binding type matches the database column
 
                if ($stmt->execute()) {
                    $response['status'] = 'success';
                    $response['message'] = 'Profile updated successfully!';
                } else {
                    $response['message'] = 'Failed to update profile in the database. Please try again.';
                }
            } else {
                $response['message'] = 'Failed to upload profile picture. Please check the server permissions.';
            }
        } else {
            $response['message'] = 'Invalid file type. Only JPEG and PNG files are allowed.';
        }
        finfo_close($finfo);
    } else {
        $response['message'] = 'No file uploaded or upload error occurred. Please try again.';
    }
} else {
    $response['message'] = 'Invalid request method. Please use POST to upload the file.';
}

echo json_encode($response);
?>
