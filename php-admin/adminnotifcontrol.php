<?php
session_start();

include('../database/config.php');
include('../php/medicine.php');

$db = new Database();
$conn = $db->getConnection();

// Handle Mark as Read action
if (isset($_POST['mark_as_read'])) {
    $notif_id = $_POST['mark_as_read'];

    // Update the notification status to 'read'
    $stmt = $conn->prepare("UPDATE adminnotifs SET notif_status = 'read' WHERE notif_id = :notif_id");
    $stmt->bindParam(':notif_id', $notif_id);
    
    if ($stmt->execute()) {
        // Redirect back to the page after the update
        header("Location: adminnotiftable.php");
        exit();
    } else {
        echo "Failed to mark as read.";
    }
}

// Handle Delete action
if (isset($_POST['delete_notif'])) {
    $notif_id = $_POST['delete_notif'];

    // Delete the notification
    $stmt = $conn->prepare("DELETE FROM adminnotifs WHERE notif_id = :notif_id");
    $stmt->bindParam(':notif_id', $notif_id);
    
    if ($stmt->execute()) {
        // Redirect back to the page after deletion
        header("Location: adminnotiftable.php");
        exit();
    } else {
        echo "Failed to delete notification.";
    }
}

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'read_all') {
        // Mark all notifications as read
        $stmt = $conn->prepare("UPDATE adminnotifs SET notif_status = 'read' WHERE notif_status = 'unread'");
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }

    if ($action == 'clear_history') {
        // Delete all notifications
        $stmt = $conn->prepare("DELETE FROM adminnotifs");
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
}
?>
