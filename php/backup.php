<?php
function backDb($host, $user, $pass, $dbname, $tables = '*') {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Get tables
    if ($tables == '*') {
        $tables = [];
        $query = $conn->query("SHOW FULL TABLES WHERE Table_Type = 'BASE TABLE'"); // Only include BASE TABLES, not VIEWS
        while ($row = $query->fetch_row()) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }

    $outsql = '';
    foreach ($tables as $table) {
        // Fetch CREATE TABLE statement and add IF NOT EXISTS
        $createTable = $conn->query("SHOW CREATE TABLE `$table`")->fetch_row()[1];
        $createTable = preg_replace('/^CREATE TABLE/', 'CREATE TABLE IF NOT EXISTS', $createTable);
        $outsql .= "\n\n" . $createTable . ";\n\n";

        // Fetch rows from table
        $rows = $conn->query("SELECT * FROM `$table`");
        while ($row = $rows->fetch_assoc()) {
            $columns = implode("`, `", array_keys($row));
            $values = array_map(fn($val) => $conn->real_escape_string($val), array_values($row));
            $values = implode("','", $values);

            $outsql .= "INSERT IGNORE INTO `$table` (`$columns`) VALUES ('$values');\n";
        }
        $outsql .= "\n";
    }

    // Specify the folder where you want to save the backup file
    $backupFolder = '../backups/'; // Ensure this folder exists and is writable

    // Create the folder if it doesn't exist
    if (!is_dir($backupFolder)) {
        mkdir($backupFolder, 0777, true); // Create folder with permissions
    }

    // Save to a file inside the folder
    $backupFile = $backupFolder . 'backup_' . $dbname . '_' . time() . '.sql';
    file_put_contents($backupFile, $outsql);


    return $backupFile; // Return file path
}
?>
