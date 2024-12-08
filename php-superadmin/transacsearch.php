<?php
include('../database/config.php');  
$db = new Database(); 
$conn = $db->getConnection(); 

if (isset($_POST['pname'])) {
    $pname = $_POST['pname'];

    $sql = "SELECT 
                p.patient_id,
                CONCAT(p.patient_fname, ' ', p.patient_lname) AS patient_name, 
                COALESCE(s.student_idnum, f.faculty_idnum, st.staff_idnum) AS idnum 
            FROM patients p
            LEFT JOIN patstudents s ON p.patient_id = s.student_patientid
            LEFT JOIN patfaculties f ON p.patient_id = f.faculty_patientid
            LEFT JOIN patstaffs st ON p.patient_id = st.staff_patientid
            WHERE 
                CONCAT(p.patient_fname, ' ', p.patient_lname) LIKE :pname 
                OR s.student_idnum LIKE :pname
                OR f.faculty_idnum LIKE :pname
                OR st.staff_idnum LIKE :pname
            LIMIT 10";

    $stmt = $conn->prepare($sql);
    
    // Prepare the search term
    $searchTerm = '%' . $pname . '%'; 
    
    $stmt->bindParam(':pname', $searchTerm, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Ensure idnum is set properly
            $idnum = $row['idnum'] ?? '';  // If no idnum found, return empty string

            echo "<div class='suggestion' data-id='{$row['patient_id']}' style='cursor: pointer;'>";
            echo htmlspecialchars($row['patient_name']) . " (" . htmlspecialchars($idnum) . ")";
            echo "</div>";
        }
    } else {
        echo "<div class='suggestion'>No results found</div>";
    }
}


if (isset($_POST['edit_pname'])) {
    $pname = $_POST['edit_pname'];

    // Query to search patients and join with students, faculties, and staffs to get idnum
    $sql = "SELECT 
                p.patient_id,
                CONCAT(p.patient_fname, ' ', p.patient_lname) AS patient_name, 
                COALESCE(s.student_idnum, f.faculty_idnum, st.staff_idnum) AS idnum
            FROM patients p
            LEFT JOIN patstudents s ON p.patient_id = s.student_patientid
            LEFT JOIN patfaculties f ON p.patient_id = f.faculty_patientid
            LEFT JOIN patstaffs st ON p.patient_id = st.staff_patientid
            WHERE 
                CONCAT(p.patient_fname, ' ', p.patient_lname) LIKE :pname 
                OR s.student_idnum LIKE :pname
                OR f.faculty_idnum LIKE :pname
                OR st.staff_idnum LIKE :pname
            LIMIT 10";

    $stmt = $conn->prepare($sql);

    $searchTerm = '%' . $pname . '%'; 

    $stmt->bindParam(':pname', $searchTerm, PDO::PARAM_STR);

    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $idnum = $row['idnum'] ?? '';  // Ensure idnum exists or default to empty string
            echo "<div class='edit_suggestion' data-id='{$row['patient_id']}' style='cursor: pointer;'>";
            echo htmlspecialchars($row['patient_name']) . " (" . htmlspecialchars($idnum) . ")";
            echo "</div>";
        }
    } else {
        echo "<div class='suggestion'>No results found</div>";
    }
}


?>
