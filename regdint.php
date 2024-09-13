<?php
require '/PHPExcel.php';

// Function to save data to Excel file
function saveToExcel($data, $photoPath) {
    $filePath = 'registrations.xlsx';
    $fileExists = file_exists($filePath);

    $objPHPExcel = $fileExists ? PHPExcel_IOFactory::load($filePath) : new PHPExcel();
    $sheet = $objPHPExcel->getActiveSheet();

    if (!$fileExists) {
        $headers = ['Name', 'Academic Year', 'Semester', 'University Registration Number', 'Email', 'Phone/WhatsApp Number', 'BI Number', 'Photo', 'Events', 'Excitement'];
        $sheet->fromArray($headers, null, 'A1');
    }

    $highestRow = $sheet->getHighestRow();
    $updated = false;
    $events = implode(', ', $data['events']);

    // Check for existing entries with the same registration number, BI number, and name
    for ($row = 2; $row <= $highestRow; $row++) {
        $existingRegNum = $sheet->getCell("D$row")->getValue();
        $existingBiNum = $sheet->getCell("G$row")->getValue();
        $existingName = $sheet->getCell("A$row")->getValue();

        if ($existingRegNum == $data['university-reg'] && $existingBiNum == $data['bi-number'] && $existingName == $data['name']) {
            // Merge events
            $existingEvents = $sheet->getCell("I$row")->getValue();
            $mergedEvents = array_unique(array_merge(explode(', ', $existingEvents), $data['events']));
            $sheet->setCellValue("I$row", implode(', ', $mergedEvents));

            // Update other fields with latest information
            $sheet->setCellValue("B$row", $data['academic-year']);
            $sheet->setCellValue("C$row", $data['semester']);
            $sheet->setCellValue("E$row", $data['email']);
            $sheet->setCellValue("F$row", $data['phone']);
            $sheet->setCellValue("H$row", $photoPath);
            $sheet->setCellValue("J$row", $data['excitement']);

            $updated = true;
            break;
        }
    }

    if (!$updated) {
        $nextRow = $highestRow + 1;
        $rowData = [
            $data['name'],
            $data['academic-year'],
            $data['semester'],
            $data['university-reg'],
            $data['email'],
            $data['phone'],
            $data['bi-number'],
            $photoPath,
            $events,
            $data['excitement']
        ];
        $sheet->fromArray($rowData, null, "A$nextRow");
    }

    $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $writer->save($filePath);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $academicYear = $_POST['academic-year'];
    $semester = $_POST['semester'];
    $universityReg = $_POST['university-reg'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $biNumber = $_POST['bi-number'];
    $events = $_POST['events'];
    $excitement = $_POST['excitement'];

    // Handle photo upload
    $photoPath = '';

    if (isset($_FILES['photo-upload']) && $_FILES['photo-upload']['error'] == 0) {
        $photoDir = 'uploads/';
        if (!is_dir($photoDir)) {
            mkdir($photoDir, 0777, true);
        }
        $photoPath = $photoDir . basename($_FILES['photo-upload']['name']);
        move_uploaded_file($_FILES['photo-upload']['tmp_name'], $photoPath);
    }

    $data = [
        'name' => $name,
        'academic-year' => $academicYear,
        'semester' => $semester,
        'university-reg' => $universityReg,
        'email' => $email,
        'phone' => $phone,
        'bi-number' => $biNumber,
        'events' => $events,
        'excitement' => $excitement
    ];

    saveToExcel($data, $photoPath);

    // Redirect to the thank you page
    header('Location: thankyou.html');
    exit;
}
?>
