<?php
header('Content-Type: application/json');

// Database connection
$host = 'localhost';
$dbname = 'clinicalog';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the year from the URL parameter or set a default
    $year = isset($_GET['year']) ? (int)$_GET['year'] : 2024;  // Example: default to 2024 if no year is provided

    // Query 1: Monthly transaction counts by patient type for all purposes
    $stmt1 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            COUNT(CASE WHEN p.patient_patienttype = 'Student' THEN 1 END) AS students,
            COUNT(CASE WHEN p.patient_patienttype = 'Faculty' THEN 1 END) AS faculty,
            COUNT(CASE WHEN p.patient_patienttype = 'Staff' THEN 1 END) AS staff,
            COUNT(CASE WHEN p.patient_patienttype = 'Extension' THEN 1 END) AS extension
        FROM 
            transactions t
        INNER JOIN  
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date)
        ORDER BY 
            month
    ");
    $stmt1->execute(['year' => $year]);
    $results1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // Query 2: Monthly transaction counts for 'Medical Certificate Issuance' purpose by patient type
    $stmt2 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            p.patient_patienttype,
            COUNT(*) AS total_count
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            t.transac_purpose = 'Medical Certificate Issuance' 
            AND YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date), p.patient_patienttype
        ORDER BY 
            month
    ");
    $stmt2->execute(['year' => $year]);
    $results2 = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // Query 3: Monthly transaction counts for 'Medical Consultation and Treatment' by patient type
    $stmt3 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            p.patient_patienttype,
            COUNT(*) AS total_count
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            t.transac_purpose = 'Medical Consultation and Treatment' 
            AND YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date), p.patient_patienttype
        ORDER BY 
            month
    ");
    $stmt3->execute(['year' => $year]);
    $results3 = $stmt3->fetchAll(PDO::FETCH_ASSOC);

    // Query 4: Monthly transaction counts for 'Dental Check Up & Treatment' by patient type
    $stmt4 = $pdo->prepare("SELECT 
            MONTH(t.transac_date) AS month,
            p.patient_patienttype,
            COUNT(*) AS total_count
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            t.transac_purpose = 'Dental Check Up & Treatment' 
            AND YEAR(t.transac_date) = :year AND t.transac_status = 'Done'
        GROUP BY 
            MONTH(t.transac_date), p.patient_patienttype
        ORDER BY 
            month
    ");
    $stmt4->execute(['year' => $year]);
    $results4 = $stmt4->fetchAll(PDO::FETCH_ASSOC);

    // New Query: Transaction counts by category for all patient types
    $stmt5 = $pdo->prepare("SELECT 
            SUBSTRING_INDEX(SUBSTRING_INDEX(s.student_program, '(', -1), ')', 1) AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patstudents s
        JOIN 
            transactions t ON s.student_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        GROUP BY 
            category
        UNION ALL
        SELECT 
            'Faculty' AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patfaculties f
        JOIN 
            transactions t ON f.faculty_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        UNION ALL
        SELECT 
            'Staff' AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patstaffs st
        JOIN 
            transactions t ON st.staff_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year
        UNION ALL
        SELECT 
            'Extension' AS category, 
            COUNT(t.transac_id) AS total_count
        FROM 
            patextensions e
        JOIN 
            transactions t ON e.exten_patientid = t.transac_patientid
        WHERE 
            t.transac_status = 'Done' 
            AND YEAR(t.transac_date) = :year");
    $stmt5->execute(['year' => $year]);
    $results5 = $stmt5->fetchAll(PDO::FETCH_ASSOC);

    // Initialize arrays for the categories (students by program, faculty, staff, extension)
    $categoryData = [
        'students' => [],
        'faculty' => 0,
        'staff' => 0,
        'extension' => 0,
    ];

    // Loop through results from the new query (transaction counts by category)
    foreach ($results5 as $row) {
        if ($row['category'] == 'Faculty') {
            $categoryData['faculty'] = (int)$row['total_count'];
        } elseif ($row['category'] == 'Staff') {
            $categoryData['staff'] = (int)$row['total_count'];
        } elseif ($row['category'] == 'Extension') {
            $categoryData['extension'] = (int)$row['total_count'];
        } else {
            // This is a student program
            $categoryData['students'][$row['category']] = (int)$row['total_count'];
        }
    }

    // Initialize arrays for each patient type and month (for all queries)
    $months = range(1, 12);
    $data = [
        'months' => array_map(function($month) { return date('F', mktime(0, 0, 0, $month, 10)); }, $months),
        'students' => array_fill(0, 12, 0),
        'faculty' => array_fill(0, 12, 0),
        'staff' => array_fill(0, 12, 0),
        'extension' => array_fill(0, 12, 0),
    ];

    // Populate data arrays with results from the first query
    foreach ($results1 as $row) {
        $monthIndex = $row['month'] - 1;
        $data['students'][$monthIndex] = (int)$row['students'];
        $data['faculty'][$monthIndex] = (int)$row['faculty'];
        $data['staff'][$monthIndex] = (int)$row['staff'];
        $data['extension'][$monthIndex] = (int)$row['extension'];
    }

    // Initialize arrays for medical certificate issuance data
    $medicalData = [
        'medical_students' => array_fill(0, 12, 0),
        'medical_faculty' => array_fill(0, 12, 0),
        'medical_staff' => array_fill(0, 12, 0),
        'medical_extension' => array_fill(0, 12, 0),
    ];

    // Populate medical data arrays with results from the second query (Medical Certificate Issuance)
    foreach ($results2 as $row) {
        $monthIndex = $row['month'] - 1;
        if ($row['patient_patienttype'] === 'Student') {
            $medicalData['medical_students'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Faculty') {
            $medicalData['medical_faculty'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Staff') {
            $medicalData['medical_staff'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Extension') {
            $medicalData['medical_extension'][$monthIndex] = (int)$row['total_count'];
        }
    }

    // Initialize arrays for medical consultation and dental checkup data
    $consultationData = [
        'consultation_students' => array_fill(0, 12, 0),
        'consultation_faculty' => array_fill(0, 12, 0),
        'consultation_staff' => array_fill(0, 12, 0),
        'consultation_extension' => array_fill(0, 12, 0),
    ];

    // Populate consultation data arrays with results from the third query
    foreach ($results3 as $row) {
        $monthIndex = $row['month'] - 1;
        if ($row['patient_patienttype'] === 'Student') {
            $consultationData['consultation_students'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Faculty') {
            $consultationData['consultation_faculty'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Staff') {
            $consultationData['consultation_staff'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Extension') {
            $consultationData['consultation_extension'][$monthIndex] = (int)$row['total_count'];
        }
    }

    // Initialize arrays for dental checkup data
    $dentalData = [
        'dental_students' => array_fill(0, 12, 0),
        'dental_faculty' => array_fill(0, 12, 0),
        'dental_staff' => array_fill(0, 12, 0),
        'dental_extension' => array_fill(0, 12, 0),
    ];

    // Populate dental checkup data arrays with results from the fourth query
    foreach ($results4 as $row) {
        $monthIndex = $row['month'] - 1;
        if ($row['patient_patienttype'] === 'Student') {
            $dentalData['dental_students'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Faculty') {
            $dentalData['dental_faculty'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Staff') {
            $dentalData['dental_staff'][$monthIndex] = (int)$row['total_count'];
        } elseif ($row['patient_patienttype'] === 'Extension') {
            $dentalData['dental_extension'][$monthIndex] = (int)$row['total_count'];
        }
    }

    // Return all the data as a JSON response
    echo json_encode([
        'categoryData' => $categoryData,
        'general_transactions' => $data,
        'medical_certificates' => $medicalData,
        'consultation_treatments' => $consultationData,
        'dental_checkups' => $dentalData
    ]);


} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
