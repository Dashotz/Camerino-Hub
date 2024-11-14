<?php
session_start();
require_once('../../db/dbConnector.php');

if (!isset($_SESSION['teacher_id'])) {
    header("Location: ../Teacher-Login.php");
    exit();
}

$db = new DbConnector();
$teacher_id = $_SESSION['teacher_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Summary - CamerinoHub</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard-shared.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'navigation.php'; ?>
    
    <div class="dashboard-container">
        <?php include 'sidebar.php'; ?>
        
        <div class="main-content">
            <div class="content-header">
                <h1>Attendance Summary</h1>
                <p>View and export attendance records</p>
            </div>

            <div class="attendance-container">
                <form action="../process_selected.php" method="post">
                    <input type="hidden" name="section_id" value="<?php echo $_GET['section_id'] ?? ''; ?>">
                    <table class="attendance-table">
                        <tr>
                            <th>Select</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>

                        <?php
                        $sql = "SELECT 
                                s.student_id,
                                s.firstname,
                                s.lastname,
                                s.email,
                                COALESCE(a.status, 'absent') as attendance
                            FROM student s
                            LEFT JOIN attendance a ON s.student_id = a.student_id 
                                AND a.date = CURRENT_DATE()
                                AND a.teacher_id = ?
                            WHERE s.status = 'active'
                            ORDER BY s.lastname, s.firstname";

                        $stmt = $db->prepare($sql);
                        $stmt->bind_param("i", $teacher_id);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
                                $isPresent = $row["attendance"] === "present";
                                $rowClass = $isPresent ? "present" : "absent";
                                $checked = $isPresent ? "checked" : "";
                                ?>
                                <tr class="<?php echo $rowClass; ?>">
                                    <td>
                                        <input type="checkbox" name="selected_ids[]" 
                                               value="<?php echo $row['student_id']; ?>" 
                                               <?php echo $checked; ?>>
                                    </td>
                                    <td><?php echo $row["student_id"]; ?></td>
                                    <td><?php echo $row["lastname"] . ", " . $row["firstname"]; ?></td>
                                    <td><?php echo $row["email"]; ?></td>
                                    <td><?php echo ucfirst($row["attendance"]); ?></td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='5'>No records found</td></tr>";
                        }
                        ?>
                    </table>

                    <div class="button-container">
                        <button type="submit" class="btn-export">
                            <i class="fas fa-save"></i> Submit Attendance
                        </button>
                    </div>
                </form>

                <!-- Monthly Summary Table -->
                <?php include 'monthly_summary.php'; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/attendance_chart.js"></script>
</body>
</html>
