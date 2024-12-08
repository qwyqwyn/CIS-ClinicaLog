<?php
session_start();
include('../database/config.php');
include('../php/user.php');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php'); 
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
</head>
<body>
    <p><?php echo $user_idnum; ?></p>
    <?php if ($userData): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Profile</th>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Date Added</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><img src='../uploads/<?php echo htmlspecialchars($userData['user_profile']); ?>' alt='Profile Picture' style='width: 50px; height: 50px; border-radius: 50%;'></td>
                <td><?php echo htmlspecialchars($userData['user_idnum']); ?></td>
                <td><?php echo htmlspecialchars($userData['user_lname']) . ', ' . htmlspecialchars($userData['user_fname']) . ' ' . htmlspecialchars($userData['user_mname']); ?></td>
                <td><?php echo htmlspecialchars($userData['user_email']); ?></td>
                <td><?php echo htmlspecialchars($userData['user_position']); ?></td>
                <td><?php echo htmlspecialchars($userData['user_dateadded']); ?></td>
                <td><?php echo htmlspecialchars($userData['user_status']); ?></td>
            </tr>
        </tbody>
    </table>
    <?php else: ?>
        <p>User data not found.</p>
    <?php endif; ?>
</body>
</html>
