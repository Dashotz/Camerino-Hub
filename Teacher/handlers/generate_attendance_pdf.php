<?php
session_start();
require_once('../../db/dbConnector.php');
require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');

if (!isset($_SESSION['teacher_id'])) {
    exit('Unauthorized');
}

try {
    $db = new DbConnector();
    $section_subject_id = $_GET['section_subject_id'] ?? '';
    $type = $_GET['type'] ?? 'daily';
    $date = $_GET['date'] ?? date('Y-m-d');

    if (empty($section_subject_id)) {
        exit('Missing section');
    }

    // Get section and subject details
    $details_query = "SELECT 
        s.section_name,
        sub.subject_name
    FROM section_subjects ss
    JOIN sections s ON ss.section_id = s.section_id
    JOIN subjects sub ON ss.subject_id = sub.id
    WHERE ss.id = ?";

    $stmt = $db->prepare($details_query);
    $stmt->bind_param("i", $section_subject_id);
    $stmt->execute();
    $details = $stmt->get_result()->fetch_assoc();

    // Create new PDF document
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);

    // Set document information
    $pdf->SetCreator('CamerinoHub');
    $pdf->SetAuthor('Teacher');
    $pdf->SetTitle('Attendance Report');

    // Remove default header/footer
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);

    // Add a page
    $pdf->AddPage();

    // School Header
    $pdf->SetFont('helvetica', 'B', 14);
    $pdf->Cell(0, 10, 'GOV D.M. CAMERINO INTEGRATED SCHOOL', 0, 1, 'C');
    $pdf->SetFont('helvetica', '', 12);
    $pdf->Cell(0, 7, 'Imus CITY', 0, 1, 'C');
    $pdf->Cell(0, 7, '135 Medicion II St, Imus, Cavite', 0, 1, 'C');
    $pdf->Cell(0, 7, '(046) 489-5114', 0, 1, 'C');
    $pdf->Ln(5);

    // Report Title
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, 'Attendance Report - ' . ($type === 'daily' ? 'Daily' : 'Monthly'), 0, 1, 'C');
    
    // Date and Class Info
    $pdf->SetFont('helvetica', '', 11);
    if ($type === 'daily') {
        $pdf->Cell(0, 7, 'Date: ' . date('F d, Y', strtotime($date)), 0, 1, 'C');
    } else {
        $pdf->Cell(0, 7, 'Month: ' . date('F Y', strtotime($date)), 0, 1, 'C');
    }
    $pdf->Cell(0, 7, $details['section_name'] . ' - ' . $details['subject_name'], 0, 1, 'C');
    $pdf->Ln(5);

    if ($type === 'daily') {
        // Center the table
        $pageWidth = $pdf->getPageWidth();
        $tableWidth = 190; // Total width of our table
        $margin = ($pageWidth - $tableWidth) / 2;
        $pdf->SetX($margin);

        // Table header
        $pdf->SetFont('helvetica', 'B', 11);
        $pdf->Cell(80, 7, 'Student Name', 1, 0, 'C');
        $pdf->Cell(60, 7, 'Status', 1, 0, 'C');
        $pdf->Cell(50, 7, 'Time In', 1, 1, 'C');

        // Get daily attendance records
        $query = "SELECT 
            s.firstname,
            s.lastname,
            a.status,
            TIME_FORMAT(a.time_in, '%h:%i %p') as time_in
        FROM student_sections ss
        JOIN student s ON ss.student_id = s.student_id
        JOIN section_subjects ssub ON ss.section_id = ssub.section_id
        LEFT JOIN attendance a ON s.student_id = a.student_id 
            AND a.section_subject_id = ? 
            AND a.date = ?
        WHERE ssub.id = ?
        AND ss.status = 'active'
        ORDER BY s.lastname, s.firstname";

        $stmt = $db->prepare($query);
        $stmt->bind_param("isi", $section_subject_id, $date, $section_subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        $pdf->SetFont('helvetica', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->SetX($margin);
            $status = $row['status'] ?? 'Absent';
            $time = $row['time_in'] ?? 'N/A';
            
            $pdf->Cell(80, 7, $row['lastname'] . ', ' . $row['firstname'], 1, 0, 'L');
            $pdf->Cell(60, 7, ucfirst($status), 1, 0, 'C');
            $pdf->Cell(50, 7, $time, 1, 1, 'C');
        }
    } else {
        $month = $_GET['month'] ?? date('m');
        $year = $_GET['year'] ?? date('Y');
        $days_in_month = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        // Get monthly attendance records
        $query = "SELECT 
            s.firstname,
            s.lastname,
            GROUP_CONCAT(a.status ORDER BY a.date) as daily_status,
            COUNT(CASE WHEN a.status = 'present' THEN 1 END) as present_count,
            COUNT(CASE WHEN a.status = 'absent' THEN 1 END) as absent_count,
            COUNT(CASE WHEN a.status = 'late' THEN 1 END) as late_count
        FROM student_sections ss
        JOIN student s ON ss.student_id = s.student_id
        JOIN section_subjects ssub ON ss.section_id = ssub.section_id
        LEFT JOIN attendance a ON s.student_id = a.student_id 
            AND a.section_subject_id = ? 
            AND MONTH(a.date) = ?
            AND YEAR(a.date) = ?
        WHERE ssub.id = ?
        AND ss.status = 'active'
        GROUP BY s.student_id
        ORDER BY s.lastname, s.firstname";

        $stmt = $db->prepare($query);
        $stmt->bind_param("iiii", $section_subject_id, $month, $year, $section_subject_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // Calculate column widths
        $nameWidth = 50;
        $dayWidth = 6;
        $summaryWidth = 12;

        // Print header
        $pdf->SetFont('helvetica', 'B', 8);
        $pdf->Cell($nameWidth, 7, 'Student Name', 1, 0, 'C');
        for ($i = 1; $i <= $days_in_month; $i++) {
            $pdf->Cell($dayWidth, 7, $i, 1, 0, 'C');
        }
        $pdf->Cell($summaryWidth, 7, 'P', 1, 0, 'C');
        $pdf->Cell($summaryWidth, 7, 'L', 1, 0, 'C');
        $pdf->Cell($summaryWidth, 7, 'A', 1, 1, 'C');

        // Print student records
        $pdf->SetFont('helvetica', '', 8);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell($nameWidth, 7, $row['lastname'] . ', ' . $row['firstname'], 1, 0, 'L');
            
            $daily_status = explode(',', $row['daily_status']);
            for ($i = 1; $i <= $days_in_month; $i++) {
                $status = $daily_status[$i-1] ?? 'A';
                $pdf->Cell($dayWidth, 7, substr($status, 0, 1), 1, 0, 'C');
            }
            
            $pdf->Cell($summaryWidth, 7, $row['present_count'], 1, 0, 'C');
            $pdf->Cell($summaryWidth, 7, $row['late_count'], 1, 0, 'C');
            $pdf->Cell($summaryWidth, 7, $row['absent_count'], 1, 1, 'C');
        }
    }

    // Output PDF
    $filename = $type === 'daily' ? 'daily_attendance.pdf' : 'monthly_attendance.pdf';
    $pdf->Output($filename, 'D');

} catch (Exception $e) {
    error_log('PDF Generation Error: ' . $e->getMessage());
    echo 'Error generating PDF. Please try again or contact support.';
}
?> 