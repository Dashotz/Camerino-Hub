<h2>Monthly Attendance Summary</h2>
<table class="attendance-table">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <?php
        foreach ($months as $name) {
            echo "<th>$name</th>";
        }
        ?>
    </tr>

    <?php
    $monthly_sql = "SELECT 
        CONCAT(s.firstname, ' ', s.lastname) as name,
        s.email,
        " . implode(",\n        ", array_map(function($m) {
            return "SUM(MONTH(a.date) = $m AND a.status = 'present') AS month_$m";
        }, array_keys($months))) . "
    FROM student s
    LEFT JOIN attendance a ON s.student_id = a.student_id 
        AND YEAR(a.date) = YEAR(CURRENT_DATE)
    WHERE s.status = 'active'
    GROUP BY s.student_id, s.firstname, s.lastname, s.email
    ORDER BY name";

    $result = $db->query($monthly_sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["name"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            foreach (array_keys($months) as $m) {
                echo "<td>" . ($row["month_$m"] ?? 0) . "</td>";
            }
            echo "</tr>";
        }
    }
    ?>
</table>
