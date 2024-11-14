  // Fetch data for Chart.js
    const attendanceData = <?php
        $data = [];
        $conn = new mysqli($servername, $username, $password, $dbname);
        $sql = "SELECT attendance_date, SUM(attendance_status = 'present') AS present_count, SUM(attendance_status = 'absent') AS absent_count FROM attendance_records GROUP BY attendance_date ORDER BY attendance_date";
        $result = $conn->query($sql);
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'date' => $row['attendance_date'],
                'present' => $row['present_count'],
                'absent' => $row['absent_count']
            ];
        }
        echo json_encode($data);
        $conn->close();
    ?>;

    const dates = attendanceData.map(item => item.date);
    const presentCounts = attendanceData.map(item => item.present);
    const absentCounts = attendanceData.map(item => item.absent);

    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [
                {
                    label: 'Present',
                    data: presentCounts,
                    borderColor: 'green',
                    fill: false
                },
                {
                    label: 'Absent',
                    data: absentCounts,
                    borderColor: 'red',
                    fill: false
                }
            ]
        },
        options: {
            scales: {
                x: { title: { display: true, text: 'Date' } },
                y: { title: { display: true, text: 'Number of Students' } }
            }
        }
    });

