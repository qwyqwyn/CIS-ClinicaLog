<?php

// Define a class for a node in the linked list

class ListNode {
    public $user_id;
    public $user_idnum;
    public $user_fname; 
    public $user_lname;
    public $user_mname; 
    public $user_email;
    public $user_position;
    public $user_role;
    public $user_status;
    public $user_dateadded;
    public $user_profile;
    public $passwordhash;
    public $code; 
    public $next;

        // Constructor to initialize the properties of a ListNode

    public function __construct($user_id, $user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, 
    $user_status, $user_dateadded, $user_profile, $passwordhash, $code, $next = null) {
        $this->user_id = $user_id;
        $this->user_idnum = $user_idnum;
        $this->user_fname = $user_fname;
        $this->user_lname = $user_lname;
        $this->user_mname = $user_mname;
        $this->user_email = $user_email;
        $this->user_position = $user_position; 
        $this->user_role = $user_role;
        $this->user_status = $user_status;
        $this->user_dateadded = $user_dateadded;
        $this->user_profile = $user_profile;
        $this->passwordhash = $passwordhash;
        $this->code = $code;
        $this->next = $next;
    } 
}

// LinkedList class to manage ListNode objects
class LinkedList {
    private $head; // Head node of the list

    // Constructor to initialize an empty linked list
    public function __construct() {
        $this->head = null;
    }

    // Get the head node
    public function getHead() {
        return $this->head;
    }

    // Add a new node to the list
    public function addNode($user_id, $user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, $user_status,
     $user_dateadded, $user_profile, $passwordhash, $code) {
        $newNode = new ListNode($user_id, $user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, $user_status, 
        $user_dateadded, $user_profile, $passwordhash, $code, $this->head);
        $this->head = $newNode; // Set new node as head
    }

    // Find a node by email
    public function findNode($email) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->user_email === $email) {
                return $current;
            }
            $current = $current->next;
        }
        return null;
    }

    // Get all nodes in the list
    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current;
            $current = $current->next;
        }
        return $nodes;
    }

    // Remove a node by user ID number
    public function removeNode($user_idnum) {
        $current = $this->head;
        $prev = null;

        while ($current !== null) {
            if ($current->user_idnum === $user_idnum) {
                if ($prev === null) {
                    $this->head = $current->next; // Remove head node
                } else {
                    $prev->next = $current->next; // Remove non-head node
                }
                return true;
            }
            $prev = $current;
            $current = $current->next;
        }
        return false;
    }
}


class User {
    private $conn; // Database connection
    private $linkedList; // Linked list to store user data

    // Constructor that initializes the database connection and loads the user data into the linked list
    public function __construct($db) {
        $this->conn = $db;
        $this->linkedList = new LinkedList();
        $this->loadUsers(); // Load users from database into the linked list
    }

    // Fetches all users except for 'ADMIN001' and returns them as an array
    public function getAllUsers() {
        $allUsers = $this->linkedList->getAllNodes(); // Get all nodes (users) in the linked list
        $filteredUsers = [];
    
        // Filter out the user with ID 'ADMIN001'
        foreach ($allUsers as $user) {
            if ($user->user_idnum !== 'ADMIN001') {
                $filteredUsers[] = $user;
            }
        }
    
        return $filteredUsers;
    }

    // Loads users from the 'admin_user_info' table into the linked list
    private function loadUsers() {
        try {
            $query = "SELECT * FROM admin_user_info";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();

            // Fetch each row and add it to the linked list
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $this->linkedList->addNode(
                    $row['admin_id'] ?? null,
                    $row['admin_idnum'] ?? null,
                    $row['admin_fname'] ?? null,
                    $row['admin_lname'] ?? null,
                    $row['admin_mname'] ?? null,
                    $row['admin_email'] ?? null,
                    $row['admin_position'] ?? null,
                    $row['admin_role'] ?? null,
                    $row['admin_status'] ?? null,
                    $row['admin_dateadded'] ?? null,
                    $row['admin_profile'] ?? null,
                    $row['admin_password'] ?? null,
                    $row['admin_code'] ?? null
                );
            }
        } catch (PDOException $e) {
            // Log or handle database errors
            error_log("Database error: " . $e->getMessage());
        }
    }

    // Checks if a user exists by email and password
    public function userExists($email, $password) {
        $node = $this->linkedList->findNode($email); // Find the user node by email
        if (!$node) {
            $this->log("User not found for email: $email");
            return false;
        }
    
        if (!password_verify($password, $node->passwordhash)) { // Verify the password
            $this->log("Incorrect password attempt for email: $email");
            return false;
        }
    
        return $node; // Return the user node if credentials are correct
    }

    // Logs messages for debugging
    private function log($message) {
        error_log($message); // Log the message to the server's error log
        echo "<script>console.log(" . json_encode($message) . ");</script>"; // Also log to the browser console
    }

    // Verifies if a user with the given email exists
    public function emailVerify($email) {
        return $this->linkedList->findNode($email) !== null;
    }

    // Finds a user by ID
    public function findByID($id) {
        $node = $this->linkedList->findNode($id);
        return $node !== null; // Return true if the user is found
    }

    // Verifies the OTP (One Time Password) for the user
    public function verifyOtp($email, $otp) {
        $node = $this->linkedList->findNode($email); // Find user by email
        return $node && $node->code == $otp; // Return true if OTP matches
    }

    // Gets the profile image URL for a user by ID number
    public function getProfileImageURL($user_idnum) {
        $node = $this->linkedList->findNode($user_idnum); // Find the user node
        if ($node) {
            return $node->user_profile; // Return the profile image URL
        } else {
            return null; // Return null if the user is not found
        }
    }

    // Registers a new user in the system
    public function register($user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, $user_status,
    $user_dateadded, $user_profile, $password, $code, $admin_id) {
    
        // Check if the email already exists
        if ($this->emailVerify($user_email)) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Email already exists.';
            return false;
        }

        try {
            // Set the admin ID for auditing purposes
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->conn->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();

            // Insert the new admin user into the database using a stored procedure
            $query = "CALL add_new_admin_user(:user_idnum, :user_fname, :user_lname, :user_mname, :user_email, 
                :user_position, :user_role, :user_status, :user_dateadded, :user_profile, :user_password, :user_code)";

            // Prepare and bind the parameters for the query
            $stmt = $this->conn->prepare($query);
            $stmt->bindValue(':user_idnum', $user_idnum);
            $stmt->bindValue(':user_fname', $user_fname);
            $stmt->bindValue(':user_lname', $user_lname);
            $stmt->bindValue(':user_mname', $user_mname);
            $stmt->bindValue(':user_email', $user_email);
            $stmt->bindValue(':user_position', $user_position);
            $stmt->bindValue(':user_role', $user_role);
            $stmt->bindValue(':user_status', $user_status);
            $stmt->bindValue(':user_dateadded', $user_dateadded);
            $stmt->bindValue(':user_profile', $user_profile);
            $stmt->bindValue(':user_password', $password);
            $stmt->bindValue(':user_code', $code);

            // Execute the query and handle success/failure
            if ($stmt->execute()) {
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'User registered successfully!';
                header('Location: staffuser.php');
                exit();
            } else {
                $errorInfo = $stmt->errorInfo();
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Error executing query: ' . $errorInfo[2];
                error_log("Error executing query: " . $errorInfo[2]);
                return false;
            }
        } catch (PDOException $e) {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Error executing stored procedure: ' . $e->getMessage();
            error_log("Error executing stored procedure: " . $e->getMessage());
            return false;
        }
    }

    // Updates the user code (OTP) for password recovery
    public function updateCode($email, $otp) {
        $sql_update_statement = "UPDATE adminusers SET user_code = ? WHERE user_email = ?";
        $stmt = $this->conn->prepare($sql_update_statement);

        if ($stmt) {
            $stmt->bindParam(1, $otp);
            $stmt->bindParam(2, $email);
            return $stmt->execute(); // Execute the update query
        } else {
            die("Error preparing statement: " . $this->conn->errorInfo()[2]);
        }
    }

    // Changes the user's password and resets the OTP
    public function changePassword($email, $newPassword) {
        $code = 0; // Reset the OTP after changing the password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT); // Hash the new password
        $node = $this->linkedList->findNode($email); // Find the user node

        if ($node) {
            $sql_update_statement = "UPDATE adminusers SET user_password = ?, user_code = ? WHERE user_email = ?";
            $stmt = $this->conn->prepare($sql_update_statement);

            if ($stmt) {
                $stmt->bindParam(1, $hashedPassword);
                $stmt->bindParam(2, $code);
                $stmt->bindParam(3, $email);
                return $stmt->execute(); // Execute the update query
            } else {
                die("Error preparing statement: " . $this->conn->errorInfo()[2]);
            }
        } else {
            return false; // Return false if the user is not found
        }
    }

    // Deletes a user by ID number from the database and the linked list
    public function deleteUser($user_idnum) {
        $sql_delete = "DELETE FROM adminusers WHERE user_idnum = ?";
        $stmt = $this->conn->prepare($sql_delete);

        if ($stmt) {
            $stmt->bindValue(1, $user_idnum);

            if ($stmt->execute()) {
                $this->linkedList->removeNode($user_idnum); // Remove from linked list
                return true; // Return true on success
            } else {
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Error executing delete query: ' . $stmt->errorInfo()[2];
                error_log("Error executing delete query: " . $stmt->errorInfo()[2]);
                return false;
            }
        } else {
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Error preparing delete statement: ' . $this->conn->errorInfo()[2];
            error_log("Error preparing delete statement: " . $this->conn->errorInfo()[2]);
            return false;
        }
    }

    
    public function updateUser($admin_id, $old_user_idnum, $new_user_idnum, $new_fname, $new_lname, $new_mname, 
    $new_email, $new_position, $new_role, $new_status) {
        try {
            // Start a transaction to ensure atomicity
            $this->conn->beginTransaction();
            
            // Optional: Set the admin ID if needed for auditing purposes
            $setAdminIdQuery = "SET @admin_id = :admin_id";
            $setStmt = $this->conn->prepare($setAdminIdQuery);
            $setStmt->bindValue(':admin_id', $admin_id);
            $setStmt->execute();
    
            // Update the user details
            $sql_update_statement = "UPDATE adminusers SET 
                user_idnum = ?, 
                user_fname = ?, 
                user_lname = ?, 
                user_mname = ?, 
                user_email = ?, 
                user_position = ?, 
                user_role = ?,  
                user_status = ? 
                WHERE user_idnum = ?";
    
            $stmt = $this->conn->prepare($sql_update_statement);
    
            $stmt->bindValue(1, $new_user_idnum);
            $stmt->bindValue(2, $new_fname);
            $stmt->bindValue(3, $new_lname); 
            $stmt->bindValue(4, $new_mname);
            $stmt->bindValue(5, $new_email);
            $stmt->bindValue(6, $new_position);
            $stmt->bindValue(7, $new_role);
            $stmt->bindValue(8, $new_status);
            $stmt->bindValue(9, $old_user_idnum);
    
            if ($stmt->execute()) {
                // Commit the transaction
                $this->conn->commit();
                
                $_SESSION['status'] = 'success';
                $_SESSION['message'] = 'User updated successfully!';
                return true;
            } else {
                // If execution fails, roll back the transaction
                $this->conn->rollBack();
                throw new Exception("Error executing update query: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (Exception $e) {
            // Roll back the transaction in case of error
            $this->conn->rollBack();
            
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = $e->getMessage();
            error_log($e->getMessage());
            return false;
        }
    }
    
    
    // Updates the profile picture for a user by their user_idnum
    public function updateProfilePicture($user_idnum, $profile) {
        // SQL query to update the profile picture in the database
        $sql_update_statement = "UPDATE adminusers SET user_profile = ? WHERE user_idnum = ?";
        
        // Prepare the SQL statement
        $stmt = $this->conn->prepare($sql_update_statement);

        // Check if the statement is prepared correctly
        if ($stmt) {
            // Bind the parameters for the query: profile picture and user_idnum
            $stmt->bindParam(1, $profile);
            $stmt->bindParam(2, $user_idnum);

            // Execute the query and check if it was successful
            if ($stmt->execute()) {
                return true; // Return true if the update was successful
            } else {
                // If there was an error executing the query, log the error and return false
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Error updating profile picture in the database: ' . $stmt->errorInfo()[2];
                error_log("Error updating profile picture in the database: " . $stmt->errorInfo()[2]);
                return false;
            }
        } else {
            // If the statement preparation failed, log the error and return false
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Error preparing update statement: ' . $this->conn->errorInfo()[2];
            error_log("Error preparing update statement: " . $this->conn->errorInfo()[2]);
            return false;
        }
    }

    // Fetches the user data by their user_idnum
    public function getUserData($user_idnum) {
        // SQL query to select all fields from the 'adminusers' table where the user_idnum matches
        $query = "SELECT * FROM adminusers WHERE user_idnum = :user_idnum";
        
        // Prepare the SQL statement
        $stmt = $this->conn->prepare($query);
        
        // Bind the user_idnum parameter to the query
        $stmt->bindParam(':user_idnum', $user_idnum);
        
        // Execute the query
        $stmt->execute();

        // Fetch the results as an associative array and return it
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    
}
?>
