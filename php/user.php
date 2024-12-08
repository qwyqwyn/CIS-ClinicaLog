<?php

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

class LinkedList {
    private $head;

    public function __construct() {
        $this->head = null;
    }

    public function getHead() {
        return $this->head;
    }

    public function addNode($user_id, $user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, $user_status,
     $user_dateadded, $user_profile, $passwordhash, $code) {
        $newNode = new ListNode($user_id, $user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, $user_status, 
        $user_dateadded, $user_profile, $passwordhash, $code, $this->head);
        $this->head = $newNode;
    }

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

    public function findNodeByEmail($email) {
        $current = $this->head;
        while ($current !== null) {
            if ($current->user_email === $email) {
                return $current;
            }
            $current = $current->next;
        }
        return null;
    }
    

    public function getAllNodes() {
        $nodes = [];
        $current = $this->head;
        while ($current !== null) {
            $nodes[] = $current;
            $current = $current->next;
        }
        return $nodes;
    }

    public function removeNode($user_idnum) {
        $current = $this->head;
        $prev = null;

        while ($current !== null) {
            if ($current->user_idnum === $user_idnum) {
                if ($prev === null) {
                    $this->head = $current->next;
                } else { 
                    $prev->next = $current->next;
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
    private $conn;
    private $linkedList;

    public function __construct($db) {
        $this->conn = $db;
        $this->linkedList = new LinkedList();
        $this->loadUsers();
    }

    public function getAllUsers() {
        $allUsers = $this->linkedList->getAllNodes();
        $filteredUsers = [];
    
        foreach ($allUsers as $user) {
            if ($user->user_idnum !== 'ADMIN001') {
                $filteredUsers[] = $user;
            }
        }
    
        return $filteredUsers;
    }
    

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


    public function userExists($email, $password) {
        $node = $this->linkedList->findNode($email);
        if (!$node) {
            $this->log("User not found for email: $email");
            return false;
        }
    
        if (!password_verify($password, $node->passwordhash)) {
            $this->log("Incorrect password attempt for email: $email");
            return false;
        }
    
        return $node;
    }
    
    private function log($message) {
        
        error_log($message); 
        echo "<script>console.log(" . json_encode($message) . ");</script>";
    }
    

    public function emailVerify($email) {
        return $this->linkedList->findNode($email) !== null;
    }

    public function findByID($id) {
        $node = $this->linkedList->findNode($id);
        return $node !== null;
    }

    public function verifyOtp($email, $otp) {
        $node = $this->linkedList->findNode($email);
        return $node && $node->code == $otp;
    }

    public function getProfileImageURL($user_idnum) {
        
        $node = $this->linkedList->findNode($user_idnum);
        if ($node) {
            return $node->user_profile;
        } else {
            return null;
        }
    }
    

    public function register($user_idnum, $user_fname, $user_lname, $user_mname, $user_email, $user_position, $user_role, $user_status,
    $user_dateadded, $user_profile, $password, $code, $admin_id) {
    
    // Check if the email already exists
    if ($this->emailVerify($user_email)) {
        $_SESSION['status'] = 'error';
        $_SESSION['message'] = 'Email already exists.';
        return false;
    }

    try {
        // First, execute the SET statement to store the admin_id value in a session variable
        $setAdminIdQuery = "SET @admin_id = :admin_id";
        $setStmt = $this->conn->prepare($setAdminIdQuery);
        $setStmt->bindValue(':admin_id', $admin_id);
        $setStmt->execute();

        // Now, prepare and execute the stored procedure to insert the new admin user
        $query = "CALL add_new_admin_user(:user_idnum, :user_fname, :user_lname, :user_mname, :user_email, 
            :user_position, :user_role, :user_status, :user_dateadded, :user_profile, :user_password, :user_code)";

        // Prepare the statement
        $stmt = $this->conn->prepare($query);

        // Bind parameters
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

        // Execute the stored procedure
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


    

    public function updateCode($email, $otp) {
        $sql_update_statement = "UPDATE adminusers SET user_code = ? WHERE user_email = ?";
        $stmt = $this->conn->prepare($sql_update_statement);

        if ($stmt) {
            $stmt->bindParam(1, $otp);
            $stmt->bindParam(2, $email);

            return $stmt->execute();
        } else {
            die("Error preparing statement: " . $this->conn->errorInfo()[2]);
        }
    }
 
    public function changePassword($email, $newPassword) {
        $code = 0;
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $node = $this->linkedList->findNode($email);

        if ($node) {
            $sql_update_statement = "UPDATE adminusers SET user_password = ?, user_code = ? WHERE user_email = ?";
            $stmt = $this->conn->prepare($sql_update_statement);

            if ($stmt) {
                $stmt->bindParam(1, $hashedPassword);
                $stmt->bindParam(2, $code);
                $stmt->bindParam(3, $email);

                return $stmt->execute();
            } else {
                die("Error preparing statement: " . $this->conn->errorInfo()[2]);
            }
        } else {
            return false;
        }
    }

    public function deleteUser($user_idnum) {
        $sql_delete = "DELETE FROM adminusers WHERE user_idnum = ?";
        $stmt = $this->conn->prepare($sql_delete);

        if ($stmt) {
            $stmt->bindValue(1, $user_idnum);

            if ($stmt->execute()) {
                $this->linkedList->removeNode($user_idnum);
                return true;
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
    
    
    public function updateProfilePicture($user_idnum, $profile) {
        
        $sql_update_statement = "UPDATE adminusers SET user_profile = ? WHERE user_idnum = ?";
        
        $stmt = $this->conn->prepare($sql_update_statement);
    
        if ($stmt) {
           
            $stmt->bindParam(1, $profile);
            $stmt->bindParam(2, $user_idnum);
    
            if ($stmt->execute()) {
                return true;
            } else {  
                
                $_SESSION['status'] = 'error';
                $_SESSION['message'] = 'Error updating profile picture in the database: ' . $stmt->errorInfo()[2];
                error_log("Error updating profile picture in the database: " . $stmt->errorInfo()[2]);
                return false;
            }
        } else {
            
            $_SESSION['status'] = 'error';
            $_SESSION['message'] = 'Error preparing update statement: ' . $this->conn->errorInfo()[2];
            error_log("Error preparing update statement: " . $this->conn->errorInfo()[2]);
            return false;
        }
    }

    public function getUserData($user_idnum) {
        $query = "SELECT * FROM adminusers WHERE user_idnum = :user_idnum";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_idnum', $user_idnum);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);  
    }
    
    
}
?>
