<?php

// Class to represent a single notification node
class NotifNode {
    public $notif_id;        
    public $notif_patid;     
    public $notif_message;    
    public $notif_status;     
    public $notif_date_added; 
    public $patient_name;     
    public $patient_type;     
    public $next;             

    // Constructor to initialize a notification node
    public function __construct($notif_id, $notif_patid, $notif_message, $notif_status, $notif_date_added, $patient_name, $patient_type) {
        $this->notif_id = $notif_id;
        $this->notif_patid = $notif_patid;
        $this->notif_message = $notif_message;
        $this->notif_status = $notif_status;
        $this->notif_date_added = $notif_date_added;
        $this->patient_name = $patient_name;
        $this->patient_type = $patient_type;
        $this->next = null;
    }
}

// Class to manage a linked list of notifications
class NotifLinkedList {
    public $head; // The head of the linked list

    // Constructor to initialize the linked list
    public function __construct() {
        $this->head = null;
    }

    // Method to add a new notification to the linked list
    public function addNotification($notif_id, $notif_patid, $notif_message, $notif_status, $notif_date_added, $patient_name, $patient_type) {
        $newNode = new NotifNode($notif_id, $notif_patid, $notif_message, $notif_status, $notif_date_added, $patient_name, $patient_type);

        if ($this->head === null) {
            $this->head = $newNode; // If the list is empty, the new node becomes the head
        } else {
            $current = $this->head;
            while ($current->next !== null) {
                $current = $current->next; // Traverse to the end of the list
            }
            $current->next = $newNode; // Append the new node to the end
        }
    }

    // Method to get all notifications in an array
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
            $current = $current->next; // Move to the next node
        }
        return $notifications;
    }

    // Method to get the four newest notifications, sorted by date
    public function getFourNewestNotifications() {
        $notifications = $this->getAllNotifications();

        // Sort notifications by date in descending order (newest first)
        usort($notifications, function ($a, $b) {
            return strtotime($b['notif_date_added']) - strtotime($a['notif_date_added']);
        });

        // Return the first four notifications (newest)
        return array_slice($notifications, 0, 4);
    }

    // Method to find a specific notification by its ID
    public function findNotificationById($notif_id) {
        $current = $this->head;

        while ($current !== null) {
            if ($current->notif_id == $notif_id) {
                return $current; // Return the notification node if found
            }
            $current = $current->next; // Move to the next node
        }
        return null; // Return null if the notification is not found
    }
}

// Class for managing admin notifications
class AdminNotif {
    private $conn;         // Database connection
    private $notifList;    // Linked list to store notifications

    // Constructor to initialize the AdminNotif class with a database connection
    public function __construct($db) {
        $this->conn = $db;
        $this->notifList = new NotifLinkedList();
        $this->loadNotifications(); // Load notifications from the database
    }

    // Method to load notifications from the database into the linked list
    public function loadNotifications($status = null) {
        $sql = "SELECT n.notif_id, n.notif_patid, n.notif_message, n.notif_status, n.notif_date_added, 
                   CONCAT(p.patient_fname, ' ', p.patient_lname) AS patient_name, p.patient_patienttype AS patient_type
            FROM adminnotifs n
            INNER JOIN patients p ON n.notif_patid = p.patient_id";
    
        // If a specific status is provided, filter notifications by status
        if ($status) {
            $sql .= " WHERE n.notif_status = :status";
        }

        $stmt = $this->conn->prepare($sql);

        if ($status) {
            $stmt->bindParam(':status', $status, PDO::PARAM_STR); // Bind the status parameter
        }

        $stmt->execute(); // Execute the query
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all notifications as an associative array

        foreach ($notifications as $notif) {
            // Add each notification to the linked list, including patient name and type
            $this->notifList->addNotification(
                $notif['notif_id'],
                $notif['notif_patid'],
                $notif['notif_message'],
                $notif['notif_status'],
                $notif['notif_date_added'],
                $notif['patient_name'],
                $notif['patient_type']
            );
        }
    }

    // Method to get the count of unread notifications
    public function getUnreadCount() {
        $unreadCount = 0;
        $current = $this->notifList->head;

        // Traverse the linked list and count notifications with 'unread' status
        while ($current !== null) {
            if ($current->notif_status === 'unread') {
                $unreadCount++;
            }
            $current = $current->next; // Move to the next node
        }
 
        return $unreadCount; // Return the unread count
    }

    // Method to get all notifications from the linked list
    public function getAllNotifications() {
        return $this->notifList->getAllNotifications();
    }

    // Method to get the four newest notifications
    public function getFourNewestNotifications() {
        return $this->notifList->getFourNewestNotifications();
    }

    // Method to find a specific notification by ID
    public function findNotificationById($notif_id) {
        return $this->notifList->findNotificationById($notif_id);
    }
}


?>
