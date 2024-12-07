<?php
header('Content-Type: application/json');

include('../database/config.php');
include('../php/medicalrecords.php');

$db = new Database();
$conn = $db->getConnection();

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the year from the query string, default to current year if not provided
    $year = isset($_GET['year']) ? intval($_GET['year']) : date("Y");

    // Step 1: Fetch all distinct student programs
    $programStmt = $conn->prepare("SELECT DISTINCT student_program FROM patstudents");
    $programStmt->execute();
    $programs = $programStmt->fetchAll(PDO::FETCH_ASSOC);

    // Step 2: Dynamically build the SELECT COUNT clauses for each program
    $countClauses = [];
    foreach ($programs as $program) {
        $programName = $program['student_program'];
        $countClauses[] = "COUNT(CASE WHEN s.student_program = '$programName' THEN t.transac_id END) AS '{$programName}_count'";
    }

    // Step 3: Construct the final SQL query with the dynamic COUNT clauses
    $countQuery = implode(", ", $countClauses);
    
    // Full SQL query with dynamic program count columns (WITHOUT ROLLUP)
    $stmt = $conn->prepare(" 
        SELECT 
            MONTHNAME(t.transac_date) AS month,
            $countQuery,
            COUNT(CASE WHEN f.faculty_patientid IS NOT NULL THEN t.transac_id END) AS faculty_count,
            COUNT(CASE WHEN st.staff_patientid IS NOT NULL THEN t.transac_id END) AS staff_count,
            COUNT(CASE WHEN e.exten_patientid IS NOT NULL THEN t.transac_id END) AS extension_count,
            COUNT(t.transac_id) AS monthly_total
        FROM 
            transactions t
        LEFT JOIN 
            patstudents s ON s.student_patientid = t.transac_patientid
        LEFT JOIN 
            patfaculties f ON f.faculty_patientid = t.transac_patientid
        LEFT JOIN 
            patstaffs st ON st.staff_patientid = t.transac_patientid
        LEFT JOIN 
            patextensions e ON e.exten_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        GROUP BY 
            MONTH(t.transac_date)
    ");
    
    // Bind the year parameter
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if data was fetched and output the result
    if ($result) {
        echo json_encode($result);
    } else {
        echo json_encode(["error" => "No data found"]);
    }

} catch (PDOException $e) {
    // Output error message if exception is thrown
    echo json_encode(["error" => $e->getMessage()]);
}
?>
