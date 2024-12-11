<?php
header('Content-Type: application/json');
include '../php/backup.php';

try {
    // Database connection parameters
    $host = 'localhost';
    $user = 'u753706103_cis';
    $pass = '#Clinicalog@cis4'; // Use your database password
    $dbname = 'u753706103_clinicalog'; // Replace with your database name
    // Generate backup
    $backupFile = backDb($host, $user, $pass, $dbname);

    // Respond with success and file path
    echo json_encode([
        "success" => true,
        "filePath" => $backupFile,
    ]);
} catch (Exception $e) {
    // Respond with error message
    echo json_encode([
        "success" => false,
        "message" => $e->getMessage(),
    ]);
}
 