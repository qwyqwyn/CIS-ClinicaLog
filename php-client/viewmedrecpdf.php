<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <title>View PDF</title>
    <style>
        /* Reset margin and padding for body and html */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;  /* Ensure full height */
            overflow: hidden; /* Prevent scrolling */
        }

        /* Style for the PDF view */
        .pdf-view {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */ 
            height: 100vh; /* Full height of the viewport */
            width: 100vw; /* Full width of the viewport */
        }

        embed {
            border: none; /* No border */
            width: 100%;  /* Full width */
            height: 100%; /* Full height */
        }
    </style>
</head>
<body>
<div class="pdf-view">
    <?php
    // Check if the 'file' parameter is passed via GET request
    if (isset($_GET['file'])) {
        $hashedFile = urldecode($_GET['file']);  // Decode any URL encoding (e.g., %20 for spaces)

        // Define the path where PDF files are stored (using relative URL)
        $pdf_directory = '/uploadpdf/uploadmedrecs/';  // Adjust the folder path as needed

        // Full path to the PDF file 
        $file_path = $pdf_directory . $hashedFile;

        // Check if the file exists in the directory
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . $file_path)) {
            // Optional: Get the user-friendly name if passed in the query
            $userFriendlyName = isset($_GET['name']) ? htmlspecialchars(urldecode($_GET['name'])) : 'Document';

            // Display the PDF file using the embed tag
            ?>
            <embed type="application/pdf" src="<?php echo htmlspecialchars($file_path); ?>" />
            <?php
        } else {
            // File does not exist
            echo "<p>Sorry, the file does not exist.</p>";
        }
    } else {
        echo "<p>No PDF selected.</p>";
    }
    ?>
</div>
</body>
</html>