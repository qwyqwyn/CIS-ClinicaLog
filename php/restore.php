<?php
// Function to restore the MySQL database from the SQL file
function restoreMysqlDB($filePath, $conn)
{
    $sql = '';
    $error = '';

    // Check if the file exists
    if (file_exists($filePath)) {
        // Read the SQL file line by line
        $lines = file($filePath);

        // Loop through each line in the file
        foreach ($lines as $line) {
            // Ignore comments from the SQL script
            if (substr($line, 0, 2) == '--' || $line == '') {
                continue;
            }

            // Accumulate the SQL query
            $sql .= $line;

            // Execute SQL statement when a complete query (ending with ;) is found
            if (substr(trim($line), -1, 1) == ';') {
                $result = mysqli_query($conn, $sql);
                if (!$result) {
                    $error .= mysqli_error($conn) . "\n"; // Store any errors
                }
                $sql = ''; // Reset the SQL query for the next statement
            }
        }

        // Return response based on success or error
        if ($error) {
            return array(
                "type" => "error",
                "message" => $error
            );
        } else {
            return array(
                "type" => "success",
                "message" => "Database Restore Completed Successfully."
            );
        }
    } else {
        return array(
            "type" => "error",
            "message" => "The file does not exist."
        );
    }
}
?>
