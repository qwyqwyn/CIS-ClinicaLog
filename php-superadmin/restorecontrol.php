<?php
$conn = mysqli_connect("localhost", "u753706103_cis", "#Clinicalog@cis4", "u753706103_clinicalog");
$response = array(); // Initialize response array

if (!empty($_FILES)) {
    // Validating SQL file type by extensions
    $fileExtension = strtolower(pathinfo($_FILES["backup_file"]["name"], PATHINFO_EXTENSION));
    if ($fileExtension !== "sql") {
        $response = array(
            "type" => "error",
            "message" => "Invalid File Type. Please upload a .sql file."
        );
    } else { 
        if (is_uploaded_file($_FILES["backup_file"]["tmp_name"])) {
            $uploadFileName = uniqid() . "_" . $_FILES["backup_file"]["name"]; // Avoid name collisions
            if (move_uploaded_file($_FILES["backup_file"]["tmp_name"], $uploadFileName)) {
                $response = restoreMysqlDB($uploadFileName, $conn); // Restore database
            } else {
                $response = array(
                    "type" => "error",
                    "message" => "Failed to move uploaded file."
                );
            }
        } else {  
            $response = array(
                "type" => "error",
                "message" => "File upload error. Please try again."
            );
        }
    }
} else {
    $response = array(
        "type" => "error",
        "message" => "No file uploaded. Please select a file."
    );
}

// Return JSON response
echo json_encode($response);

function restoreMysqlDB($filePath, $conn)
{
    $sql = '';
    $error = '';

    if (file_exists($filePath)) {
        $lines = file($filePath);

        foreach ($lines as $line) {
            // Ignoring comments from the SQL script
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            $sql .= $line;

            // Execute the SQL query when it's complete (ends with ;)
            if (substr(trim($line), -1, 1) == ';') {
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    $error .= mysqli_error($conn) . "\n";
                }
                $sql = ''; // Reset for the next query
            }
        }

        if ($error) {
            return array(
                "type" => "error",
                "message" => "Error restoring database: " . $error
            );
        } else {
            return array(
                "type" => "success",
                "message" => "Database restored successfully."
            );
        }
    } else {
        return array(
            "type" => "error",
            "message" => "File not found."
        );
    }
}
?>
