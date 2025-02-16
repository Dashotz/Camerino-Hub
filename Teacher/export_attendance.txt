<?php
session_start();
require_once('../db/dbConnector.php');
require_once(__DIR__ . '/../vendor/autoload.php');  // Updated path

// Add the correct namespace
use TCPDF as TCPDF;

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

function generateAttendancePDF($result, $filename, $type = 'daily', $dateInfo = '') {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('CamerinoHub');
    $pdf->SetAuthor('Gov D.M. Camerino');
    $pdf->SetTitle('Attendance Report');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    $pdf->AddPage();
    
    // Logo and header text
    $logo = '../images/gov.png';
    $headerText = "GOV D.M. CAMERINO INTEGRATED SCHOOL\nImus CITY\n\n" . 
                 "135 Medicion II St, Imus, Cavite\n(046) 489-5114\n\n" .
                 "Attendance Report - " . ucfirst($type) . "\n" . $dateInfo;
    
    $pdf->Image($logo, 15, 15, 25);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->SetXY(45, 15);
    $pdf->MultiCell(0, 5, $headerText, 0, 'C');
    $pdf->Ln(10);
    
    // Table header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(240, 240, 240);
    
    if ($type == 'daily') {
        $header = ['Student Name', 'Status', 'Time In', 'Remarks'];
        $widths = [80, 25, 35, 50];
    } else {
        $header = ['Student Name', 'Present', 'Absent', 'Late', 'Excused'];
        $widths = [80, 25, 25, 25, 35];
    }
    
    // Print header
    $pdf->SetX(15);
    foreach ($header as $i => $col) {
        $pdf->Cell($widths[$i], 10, $col, 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Table data
    $pdf->SetFont('helvetica', '', 9);
    $fill = false;
    
    while ($row = $result->fetch_assoc()) {
        $pdf->SetX(15);
        
        if ($type == 'daily') {
            $pdf->Cell($widths[0], 8, $row['name'], 1, 0, 'L', $fill);
            $pdf->Cell($widths[1], 8, ucfirst($row['status'] ?? 'absent'), 1, 0, 'C', $fill);
            $pdf->Cell($widths[2], 8, $row['time_in'] ?? '-', 1, 0, 'C', $fill);
            $pdf->Cell($widths[3], 8, $row['remarks'] ?? '', 1, 0, 'L', $fill);
        } else {
            $pdf->Cell($widths[0], 8, $row['name'], 1, 0, 'L', $fill);
            $pdf->Cell($widths[1], 8, $row['present_count'], 1, 0, 'C', $fill);
            $pdf->Cell($widths[2], 8, $row['absent_count'], 1, 0, 'C', $fill);
            $pdf->Cell($widths[3], 8, $row['late_count'], 1, 0, 'C', $fill);
            $pdf->Cell($widths[4], 8, $row['excused_count'], 1, 0, 'C', $fill);
        }
        
        $pdf->Ln();
        $fill = !$fill;
    }
    
    // Output PDF
    $pdf->Output($filename, 'D');
}

// Export Today's Attendance
if (isset($_POST['export_today'])) {
    $today = date('Y-m-d');
    $sql = "SELECT 
                CONCAT(s.firstname, ' ', s.lastname) as name,
                COALESCE(a.status, 'absent') as status,  -- Set default status to 'absent'
                COALESCE(a.time_in, '-') as time_in,     -- Set default time_in to '-'
                COALESCE(a.remarks, '') as remarks       -- Set default remarks to empty string
            FROM student s
            JOIN student_sections ss ON s.student_id = ss.student_id
            LEFT JOIN attendance a ON s.student_id = a.student_id 
                AND DATE(a.date) = ?
            WHERE s.status = 'active'
            AND ss.status = 'active'
            ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        generateAttendancePDF($result, "Attendance_Report_" . date('Y-m-d') . ".pdf", 'daily', 'Date: ' . date('F d, Y'));
    } else {
        header("Location: attendance.php?no_data=true");
        exit();
    }
}

// Export Monthly Attendance
elseif (isset($_POST['export_month'])) {
    $month = $_POST['month'];
    $year = $_POST['year'];
    
    $sql = "SELECT 
                CONCAT(s.firstname, ' ', s.lastname) as name,
                COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present_count,
                COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_count,
                COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late_count,
                COUNT(CASE WHEN a.status = 'excused' THEN 1 END) as excused_count
            FROM student s
            JOIN student_sections ss ON s.student_id = ss.student_id
            LEFT JOIN attendance a ON s.student_id = a.student_id 
                AND MONTH(a.date) = ? 
                AND YEAR(a.date) = ?
            WHERE s.status = 'active'
            AND ss.status = 'active'
            GROUP BY s.student_id
            ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ii", $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
        generateAttendancePDF($result, 
            "Attendance_Report_{$monthName}_{$year}.pdf", 
            'monthly', 
            "Month: {$monthName} {$year}"
        );
    } else {
        header("Location: attendance.php?no_data=true&month=$month&year=$year");
        exit();
    }
}

// Export Date Range
elseif (isset($_POST['export_range'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    
    $sql = "SELECT 
                CONCAT(s.firstname, ' ', s.lastname) as name,
                COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present_count,
                COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_count,
                COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late_count,
                COUNT(CASE WHEN a.status = 'excused' THEN 1 END) as excused_count
            FROM student s
            JOIN student_sections ss ON s.student_id = ss.student_id
            LEFT JOIN attendance a ON s.student_id = a.student_id 
                AND a.date BETWEEN ? AND ?
            WHERE s.status = 'active'
            AND ss.status = 'active'
            GROUP BY s.student_id
            ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $start = date('M d, Y', strtotime($start_date));
        $end = date('M d, Y', strtotime($end_date));
        generateAttendancePDF($result, 
            "Attendance_Report_{$start_date}_to_{$end_date}.pdf", 
            'range', 
            "Period: {$start} to {$end}"
        );
    } else {
        header("Location: attendance.php?no_data=true&start=$start_date&end=$end_date");
        exit();
    }
}

$db->close();
?>
