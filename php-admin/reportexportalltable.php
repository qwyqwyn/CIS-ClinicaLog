<?php
include('../database/config.php');
$db = new Database();
$conn = $db->getConnection();

try {
    $year = isset($_GET['year']) ? $_GET['year'] : date('Y');

    $query = "
        SELECT 
            MONTH(t.transac_date) AS month,
            SUM(CASE WHEN p.patient_patienttype = 'student' AND t.transac_purpose = 'Dental Check Up & Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS dental_student,
            SUM(CASE WHEN p.patient_patienttype = 'faculty' AND t.transac_purpose = 'Dental Check Up & Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS dental_faculty,
            SUM(CASE WHEN p.patient_patienttype = 'staff' AND t.transac_purpose = 'Dental Check Up & Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS dental_staff,
            SUM(CASE WHEN p.patient_patienttype = 'extension' AND t.transac_purpose = 'Dental Check Up & Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS dental_extension,
            SUM(CASE WHEN p.patient_patienttype = 'student' AND t.transac_purpose = 'Medical Consultation and Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS consult_student,
            SUM(CASE WHEN p.patient_patienttype = 'faculty' AND t.transac_purpose = 'Medical Consultation and Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS consult_faculty,
            SUM(CASE WHEN p.patient_patienttype = 'staff' AND t.transac_purpose = 'Medical Consultation and Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS consult_staff,
            SUM(CASE WHEN p.patient_patienttype = 'extension' AND t.transac_purpose = 'Medical Consultation and Treatment' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS consult_extension,
            SUM(CASE WHEN p.patient_patienttype = 'student' AND t.transac_purpose = 'Medical Certificate Issuance' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS medical_cert_student,
            SUM(CASE WHEN p.patient_patienttype = 'faculty' AND t.transac_purpose = 'Medical Certificate Issuance' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS medical_cert_faculty,
            SUM(CASE WHEN p.patient_patienttype = 'staff' AND t.transac_purpose = 'Medical Certificate Issuance' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS medical_cert_staff,
            SUM(CASE WHEN p.patient_patienttype = 'extension' AND t.transac_purpose = 'Medical Certificate Issuance' AND t.transac_status = 'Done' THEN 1 ELSE 0 END) AS medical_cert_extension
        FROM 
            transactions t
        INNER JOIN 
            patients p ON t.transac_patientid = p.patient_id
        WHERE 
            YEAR(t.transac_date) = :year
        GROUP BY 
            MONTH(t.transac_date)
        ORDER BY 
            month
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute([':year' => $year]);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $finalData = [];
    $totalRow = [
        'month' => 'Total',
        'dental_student' => 0, 'dental_faculty' => 0, 'dental_staff' => 0, 'dental_extension' => 0, 'dental_sum' => 0,
        'consult_student' => 0, 'consult_faculty' => 0, 'consult_staff' => 0, 'consult_extension' => 0, 'consult_sum' => 0,
        'medical_cert_student' => 0, 'medical_cert_faculty' => 0, 'medical_cert_staff' => 0, 'medical_cert_extension' => 0, 'medical_cert_sum' => 0,
        'total_sum' => 0
    ];

    foreach ($data as $row) {
        $month = $row['month'];
        $row['dental_sum'] = $row['dental_student'] + $row['dental_faculty'] + $row['dental_staff'] + $row['dental_extension'];
        $row['consult_sum'] = $row['consult_student'] + $row['consult_faculty'] + $row['consult_staff'] + $row['consult_extension'];
        $row['medical_cert_sum'] = $row['medical_cert_student'] + $row['medical_cert_faculty'] + $row['medical_cert_staff'] + $row['medical_cert_extension'];
        $row['total_sum'] = $row['dental_sum'] + $row['consult_sum'] + $row['medical_cert_sum'];

        foreach ($row as $key => $value) {
            if ($key !== 'month') {
                $totalRow[$key] += $value;
            }
        }

        $finalData[] = $row;
    }

    $finalData[] = $totalRow;

    header('Content-Type: application/json');
    echo json_encode($finalData, JSON_PRETTY_PRINT);

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'Database Error: ' . $e->getMessage()]);
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => true, 'message' => 'An unexpected error occurred: ' . $e->getMessage()]);
}
?>
