<?php
session_start();
require_once('../db/dbConnector.php');
require_once(__DIR__ . '/../vendor/autoload.php');

use TCPDF as TCPDF;

if (!isset($_SESSION['teacher_id'])) {
    header("Location: Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];

// Add section verification
function verifyTeacherSection($db, $section_subject_id, $teacher_id) {
    $verify_query = "
        SELECT ss.section_id, s.section_name, sub.subject_name 
        FROM section_subjects ss
        JOIN sections s ON ss.section_id = s.section_id
        JOIN subjects sub ON ss.subject_id = sub.id
        WHERE ss.id = ? AND ss.teacher_id = ? AND ss.status = 'active'";

    $stmt = $db->prepare($verify_query);
    $stmt->bind_param("ii", $section_subject_id, $teacher_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function generateAttendancePDF($result, $filename, $type = 'daily', $dateInfo = '', $section_data = null) {
    // Create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('CamerinoHub');
    $pdf->SetAuthor('Gov D.M. Camerino');
    $pdf->SetTitle('Attendance Report');
    
    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Logo
    $pdf->Image('../images/gov.png', 15, 15, 25);
    
    // School header
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 7, 'GOV D.M. CAMERINO INTEGRATED SCHOOL', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 6, 'Imus CITY', 0, 1, 'C');
    $pdf->Cell(0, 6, '135 Medicion II St, Imus, Cavite', 0, 1, 'C');
    $pdf->Cell(0, 6, '(046) 489-5114', 0, 1, 'C');
    
    // Report title and info
    $pdf->Ln(10);
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 6, 'Attendance Report - ' . ucfirst($type), 0, 1, 'C');
    
    if ($section_data) {
        $pdf->SetFont('helvetica', '', 11);
        $pdf->Cell(0, 6, "Section: {$section_data['section_name']}", 0, 1, 'C');
        $pdf->Cell(0, 6, "Subject: {$section_data['subject_name']}", 0, 1, 'C');
    }
    
    $pdf->SetFont('helvetica', '', 11);
    $pdf->Cell(0, 6, $dateInfo, 0, 1, 'C');
    
    // Add space before table
    $pdf->Ln(10);
    
    // Table header
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->SetFillColor(240, 240, 240);
    
    if ($type == 'daily') {
        $header = ['Student Name', 'Status', 'Time In', 'Remarks'];
        $widths = [80, 25, 35, 45];
    } else {
        $header = ['Student Name', 'Present', 'Absent', 'Late', 'Excused'];
        $widths = [80, 25, 25, 25, 35];
    }
    
    // Print header
    foreach ($header as $i => $col) {
        $pdf->Cell($widths[$i], 10, $col, 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Table data
    $pdf->SetFont('helvetica', '', 9);
    $fill = false;
    
    while ($row = $result->fetch_assoc()) {
        if ($type == 'daily') {
            $pdf->Cell($widths[0], 8, $row['name'], 1, 0, 'L', $fill);
            $pdf->Cell($widths[1], 8, ucfirst($row['status']), 1, 0, 'C', $fill);
            $pdf->Cell($widths[2], 8, $row['time_in'], 1, 0, 'C', $fill);
            $pdf->Cell($widths[3], 8, $row['remarks'], 1, 0, 'L', $fill);
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
    
    // Add signature lines
    $pdf->Ln(20);
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(90, 6, 'Prepared by:', 0, 0, 'L');
    $pdf->Cell(90, 6, 'Noted by:', 0, 1, 'L');
    $pdf->Ln(15);
    
    $pdf->SetFont('helvetica', 'B', 10);
    $pdf->Cell(90, 6, '_____________________', 0, 0, 'L');
    $pdf->Cell(90, 6, '_____________________', 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Cell(90, 6, 'Teacher', 0, 0, 'L');
    $pdf->Cell(90, 6, 'Principal', 0, 1, 'L');
    
    // Output PDF
    $pdf->Output($filename, 'D');
    exit();
}

// Export Today's Attendance
if (isset($_POST['export_today'])) {
    $section_subject_id = $_POST['section_subject_id'];
    $section_data = verifyTeacherSection($db, $section_subject_id, $teacher_id);
    
    if (!$section_data) {
        header("Location: attendance.php?error=invalid_section");
        exit();
    }

    $today = date('Y-m-d');
    $sql = "SELECT 
                CONCAT(s.firstname, ' ', s.lastname) as name,
                COALESCE(a.status, 'absent') as status,
                COALESCE(DATE_FORMAT(a.time_in, '%l:%i %p'), '-') as time_in,
                COALESCE(a.remarks, '') as remarks
            FROM student s
            JOIN student_sections ss ON s.student_id = ss.student_id
            LEFT JOIN attendance a ON s.student_id = a.student_id 
                AND DATE(a.date) = ?
                AND a.section_subject_id = ?
            WHERE ss.section_id = ?
            AND s.status = 'active'
            AND ss.status = 'active'
            ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("sii", $today, $section_subject_id, $section_data['section_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        generateAttendancePDF(
            $result, 
            "Attendance_Report_{$section_data['section_name']}_{$section_data['subject_name']}_" . date('Y-m-d') . ".pdf",
            'daily',
            'Date: ' . date('F d, Y'),
            $section_data
        );
    } else {
        header("Location: attendance.php?no_data=true");
        exit();
    }
}

// Export Monthly Attendance
elseif (isset($_POST['export_month'])) {
    $section_subject_id = $_POST['section_subject_id'];
    $section_data = verifyTeacherSection($db, $section_subject_id, $teacher_id);
    
    if (!$section_data) {
        header("Location: attendance.php?error=invalid_section");
        exit();
    }

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
                AND a.section_subject_id = ?
            WHERE ss.section_id = ?
            AND s.status = 'active'
            AND ss.status = 'active'
            GROUP BY s.student_id
            ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("iiii", $month, $year, $section_subject_id, $section_data['section_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $monthName = date('F', mktime(0, 0, 0, $month, 1, $year));
        generateAttendancePDF(
            $result, 
            "Attendance_Report_{$section_data['section_name']}_{$section_data['subject_name']}_{$monthName}_{$year}.pdf",
            'monthly',
            "Month: {$monthName} {$year}",
            $section_data
        );
    } else {
        header("Location: attendance.php?no_data=true&month=$month&year=$year");
        exit();
    }
}

// Export Date Range
elseif (isset($_POST['export_range'])) {
    $section_subject_id = $_POST['section_subject_id'];
    $section_data = verifyTeacherSection($db, $section_subject_id, $teacher_id);
    
    if (!$section_data) {
        header("Location: attendance.php?error=invalid_section");
        exit();
    }

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
                AND a.section_subject_id = ?
            WHERE ss.section_id = ?
            AND s.status = 'active'
            AND ss.status = 'active'
            GROUP BY s.student_id
            ORDER BY s.lastname, s.firstname";

    $stmt = $db->prepare($sql);
    $stmt->bind_param("ssii", $start_date, $end_date, $section_subject_id, $section_data['section_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $start = date('M d, Y', strtotime($start_date));
        $end = date('M d, Y', strtotime($end_date));
        generateAttendancePDF(
            $result, 
            "Attendance_Report_{$section_data['section_name']}_{$section_data['subject_name']}_{$start_date}_to_{$end_date}.pdf",
            'range',
            "Period: {$start} to {$end}",
            $section_data
        );
    } else {
        header("Location: attendance.php?no_data=true&start=$start_date&end=$end_date");
        exit();
    }
}

$db->close();
?> 