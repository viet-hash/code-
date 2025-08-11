<?php
require_once "includes/db.php";
?>
<style>
    body { background: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
    .center-page { max-width: 1200px; margin: 0 auto; text-align: center; }
    h2 { color: #ff8800; font-weight: bold; font-size: 2.2rem; margin-bottom: 28px; }
    .table { min-width: 900px; margin: 0 auto; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); font-size: 1.08rem; }
    .table thead th { background: #ff8800; color: #fff; font-weight: 600; text-align: center; padding: 18px 10px; border-bottom: 2px solid #eee; }
    .table tbody td { text-align: center; vertical-align: middle; border-bottom: 1px solid #eee; padding: 14px 8px; }
    .table-hover tbody tr:hover { background: #fff7e6; }
    .btn-primary { background: #ff8800; border: none; font-weight: bold; font-size: 1.1rem; padding: 10px 24px; border-radius: 10px; margin-bottom: 10px; }
    .btn-primary:hover { background: #ff6600; }
    .attendance-radio { margin: 0 10px; accent-color: #ff8800; }
</style>
<div class="center-page">
<h2>Student Attendance by Schedule</h2>
    <?php
    
    $schedule_id = $_GET['schedule_id'] ?? '';
    if ($schedule_id) {
        echo '<form method="post" action="attendance_save.php">';
        echo '<div class="table-responsive mt-4">';
        $sql = "SELECT sch.schedule_id, sch.day_of_week, sch.start_time, sch.end_time, sch.room, c.course_name, c.course_code, u.user_id, u.fullname, e.enrollment_id, c.class_name
                FROM Schedule sch
                JOIN Course c ON sch.course_id = c.course_id
                JOIN Enrollment e ON e.course_id = c.course_id
                JOIN User u ON e.student_id = u.user_id
                WHERE sch.schedule_id = $schedule_id
                ORDER BY u.fullname";
        $result = $conn->query($sql);
        $row_first = $result->fetch_assoc();
        if ($row_first) {
            echo "<h3>Schedule: {$row_first['course_name']} ({$row_first['course_code']}) - {$row_first['day_of_week']} {$row_first['start_time']} - {$row_first['end_time']} at {$row_first['room']}</h3>";
            echo "<table class='table table-bordered table-hover'><thead><tr><th>No.</th><th>Class Code</th><th>Student ID</th><th>Full Name</th><th>Attendance</th><th>Note</th></tr></thead><tbody>";
            $stt = 1;
            // In sinh viên đầu tiên
            echo "<tr>";
            echo "<td>" . $stt++ . "</td>";
            echo "<td>" . htmlspecialchars($row_first['course_code']) . "</td>";
            echo "<td>" . htmlspecialchars($row_first['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row_first['fullname']) . "</td>";
            echo "<td>";
            echo "<label><input type='radio' class='attendance-radio' name='attendance[{$row_first['enrollment_id']}][{$row_first['schedule_id']}]' value='Absent'> Absent</label>";
            echo "<label><input type='radio' class='attendance-radio' name='attendance[{$row_first['enrollment_id']}][{$row_first['schedule_id']}]' value='Present' checked> Present</label>";
            echo "</td>";
            // Note column
            echo "<td><input type='text' class='form-control' name='note[{$row_first['enrollment_id']}][{$row_first['schedule_id']}]' placeholder='Enter note'></td>";
            echo "</tr>";
            // In các sinh viên còn lại
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $stt++ . "</td>";
                echo "<td>" . htmlspecialchars($row['course_code']) . "</td>";
                echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                echo "<td>" . htmlspecialchars($row['fullname']) . "</td>";
                echo "<td>";
                echo "<label><input type='radio' class='attendance-radio' name='attendance[{$row['enrollment_id']}][{$row['schedule_id']}]' value='Absent'> Absent</label>";
                echo "<label><input type='radio' class='attendance-radio' name='attendance[{$row['enrollment_id']}][{$row['schedule_id']}]' value='Present' checked> Present</label>";
                echo "</td>";
                // Note column
                echo "<td><input type='text' class='form-control' name='note[{$row['enrollment_id']}][{$row['schedule_id']}]' placeholder='Enter note'></td>";
                echo "</tr>";
            }
            echo "</tbody></table>";
        } else {
            echo '<div class="alert alert-warning">No students have registered for this session.</div>';
        }
        echo '</div>';
        echo '<button type="submit" class="btn btn-primary">Save Attendance</button>';
        echo '</form>';
    } else {
        echo '<div class="alert alert-info">Please select a session from the list to take attendance.</div>';
    }
    ?>
</div>
