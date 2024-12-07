<?php

class NotifNode {
    public $notif_id;
    public $notif_patid;
    public $notif_message;
    public $notif_status;
    public $notif_date_added;
    public $patient_name;  
    public $patient_type; // Add patient_name
    public $next;

    public function __construct($notif_id, $notif_patid, $notif_message, $notif_status, $notif_date_added, $patient_name, $patient_type) {
        $this->notif_id = $notif_id;
        $this->notif_patid = $notif_patid;
        $this->notif_message = $notif_message;
        $this->notif_status = $notif_status;
        $this->notif_date_added = $notif_date_added;
        $this->patient_name = $patient_name; 
        $this->patient_type = $patient_type;  // Initialize patient_name
        $this->next = null;
    }
}


class NotifLinkedList {
    public $head;

    public function __construct() {
        $this->head = null;
    }

    // Add a new notification to the list
    public function addNotification($notif_id, $notif_patid, $notif_message, $notif_status, $notif_date_added, $patient_name, $patient_type) {
        $newNode = new NotifNode($notif_id, $notif_patid, $notif_message, $notif_status, $notif_date_added, $patient_name, $patient_type);

        if ($this->head === null) {
            $this->head = $newNode;
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next;
            }
            $current->next = $newNode;
        }
    }

    // Get all notifications as an array
    public function getAllNotifications() {
        $notifications = [];
        $current = $this->head;

        while ($current !== null) {
            $notifications[] = [
                'notif_id' => $current->notif_id,
                'notif_patid' => $current->notif_patid,
                'notif_message' => $current->notif_message,
                'notif_status' => $current->notif_status,
                'notif_date_added' => $current->notif_date_added,
                'patient_name' => $current->patient_name,
                'patient_type' => $current->patient_type,
            ];
            $current = $current->next;
        }
        return $notifications;
    }

    // Get the four newest notifications
    public function getFourNewestNotifications() {
        $notifications = $this->getAllNotifications();

        // Sort notifications by date in descending order
        usort($notifications, function ($a, $b) {
            return strtotime($b['notif_date_added']) - strtotime($a['notif_date_added']);
        });

        // Return the first four notifications
        return array_slice($notifications, 0, 4);
    }

    // Find a specific notification by ID
    public function findNotificationById($notif_id) {
        $current = $this->head;

        while ($current !== null) {
            if ($current->notif_id == $notif_id) {
                return $current;
            }
            $current = $current->next;
        }
        return null;
    }
}

class AdminNotif {
    private $conn;
    private $notifList;

    public function __construct($db) {
        $this->conn = $db;
        $this->notifList = new NotifLinkedList();
        $this->loadNotifications();
    }

// In your AdminNotif class
public function loadNotifications($status = null) {
    $sql = "SELECT n.notif_id, n.notif_patid, n.notif_message, n.notif_status, n.notif_date_added, 
                   CONCAT(p.patient_fname, ' ', p.patient_lname) AS patient_name, p.patient_patienttype AS patient_type
            FROM adminnotifs n
            INNER JOIN patients p ON n.notif_patid = p.patient_id";
    
    if ($status) {
        $sql .= " WHERE n.notif_status = :status";
    }

    $stmt = $this->conn->prepare($sql);

    if ($status) {
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    }

    $stmt->execute();
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notifications as $notif) {
        // Add the notification along with patient name
        $this->notifList->addNotification(
            $notif['notif_id'],
            $notif['notif_patid'],
            $notif['notif_message'],
            $notif['notif_status'],
            $notif['notif_date_added'],
            $notif['patient_name'],
            $notif['patient_type']
              // Fetch the concatenated patient name
        );
    }
}

public function getUnreadCount() {
    $unreadCount = 0;
    $current = $this->notifList->head;

    while ($current !== null) {
        if ($current->notif_status === 'unread') {
            $unreadCount++;
        }
        $current = $current->next;
    }

    return $unreadCount;
}

    // Get all notifications from the linked list
    public function getAllNotifications() {
        return $this->notifList->getAllNotifications();
    }

    // Get the four newest notifications
    public function getFourNewestNotifications() {
        return $this->notifList->getFourNewestNotifications();
    }

    // Find a specific notification by ID
    public function findNotificationById($notif_id) {
        return $this->notifList->findNotificationById($notif_id);
    }
}

?>
