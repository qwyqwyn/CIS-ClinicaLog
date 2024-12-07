<?php
include('../database/config.php');
include('../php/patient.php');

$db = new Database();
$conn = $db->getConnection();

if (isset($_GET['pname'])) {
    $query = $_GET['pname'];
    // Prepare SQL with parameterized query
    $sql = "SELECT 
                p.patient_id, 
                CONCAT(p.patient_fname, ' ', p.patient_lname) AS name, 
                CASE 
                    WHEN p.patient_patienttype = 'Student' THEN ps.student_idnum
                    WHEN p.patient_patienttype = 'Faculty' THEN pf.faculty_idnum
                    WHEN p.patient_patienttype = 'Staff' THEN pst.staff_idnum
                    WHEN p.patient_patienttype = 'Extension' THEN pe.exten_idnum
                    ELSE NULL 
                END AS idnum
            FROM 
                patients p
            LEFT JOIN patstudents ps ON p.patient_id = ps.student_patientid
            LEFT JOIN patfaculties pf ON p.patient_id = pf.faculty_patientid
            LEFT JOIN patstaffs pst ON p.patient_id = pst.staff_patientid
            LEFT JOIN patextensions pe ON p.patient_id = pe.exten_patientid
            WHERE 
                p.patient_lname LIKE ? OR 
                p.patient_fname LIKE ? OR 
                ps.student_idnum LIKE ? OR 
                pf.faculty_idnum LIKE ? OR 
                pst.staff_idnum LIKE ? OR 
                pe.exten_idnum LIKE ?
            LIMIT 10";

    // Prepare the statement and bind parameters
    if ($stmt = $conn->prepare($sql)) {
        $searchTerm = "%" . $query . "%";
        $stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);

        // Execute and fetch results
        $stmt->execute();
        $result = $stmt->get_result();

        // Fetch data and return as JSON
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        // Return data as JSON
        echo json_encode($data);
    }
}
?>