<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download PDF with pdfmake</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.69/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.69/vfs_fonts.js"></script>
</head>
<body>
    <div id="content-to-download">
        <h1>Patient Profile</h1>
        <p><strong>Name:</strong> John Doe</p>
        <p><strong>Age:</strong> 29</p>
        <p><strong>Address:</strong> 123 Main Street</p>
        <p><strong>Condition:</strong> Stable</p>
    </div>

    <button onclick="convertToPDF()">Download PDF</button>

    <script>
        function convertToPDF() {
            // Extract content from the div
            const content = document.getElementById('content-to-download').innerHTML;

            // Build the PDF document definition
            const docDefinition = {
                content: [
                    { text: 'Patient Profile', style: 'header' },
                    { text: 'Generated PDF:', margin: [0, 10, 0, 10] },
                    {
                        text: content.replace(/<\/?[^>]+(>|$)/g, ''), // Remove HTML tags
                        style: 'content'
                    }
                ],
                styles: {
                    header: {
                        fontSize: 20,
                        bold: true,
                        margin: [0, 0, 0, 10]
                    },
                    content: {
                        fontSize: 12,
                        margin: [0, 5, 0, 5]
                    }
                }
            };

            // Generate and download the PDF
            pdfMake.createPdf(docDefinition).download('patient-profile.pdf');
        }
    </script>
</body>
</html>
