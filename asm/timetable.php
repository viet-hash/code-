<?php
session_start();
require_once "includes/db.php";
// Nếu là sinh viên, lọc theo student_id và lấy trạng thái điểm danh
if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT s.*, c.course_code, c.course_name, c.class_name, att.status AS attendance_status
            FROM Schedule s
            JOIN Course c ON s.course_id = c.course_id
            JOIN Enrollment e ON e.course_id = c.course_id
            LEFT JOIN Attendance att ON att.enrollment_id = e.enrollment_id AND att.schedule_id = s.schedule_id AND att.date = CURDATE()
            WHERE e.student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    // Giáo viên hoặc admin xem toàn bộ
    $sql = "SELECT s.*, c.course_code, c.course_name, c.class_name FROM Schedule s JOIN Course c ON s.course_id = c.course_id";
    $result = $conn->query($sql);
}
$schedule = [];
while ($row = $result->fetch_assoc()) {
    $slot = "{$row['start_time']}-{$row['end_time']}";
    $schedule[$slot][$row['day_of_week']][] = $row;
}
$slots = [
    "07:15:00-09:15:00" => "Slot 1 (7:15-9:15)",
    "09:25:00-11:25:00" => "Slot 2 (9:25-11:25)",
    "12:00:00-14:00:00" => "Slot 3 (12:00-14:00)",
    "14:10:00-16:10:00" => "Slot 4 (14:10-16:10)",
    "16:20:00-18:20:00" => "Slot 5 (16:20-18:20)",
    "18:30:00-20:30:00" => "Slot 6 (18:30-20:30)",
    "20:30:00-22:30:00" => "Slot 7 (20:30-22:30)"
];
$days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Weekly Timetable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Weekly Timetable</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th></th>
                <?php foreach ($days as $day): ?>
                    <th><?php echo $day; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($slots as $slot_time => $slot_label): ?>
                <tr>
                    <td><?php echo $slot_label; ?></td>
                    <?php foreach ($days as $day): ?>
                        <td>
                            <?php
                            if (!empty($schedule[$slot_time][$day])) {
                                foreach ($schedule[$slot_time][$day] as $row) {
                                    echo "<b>{$row['course_code']}</b> ({$row['class_name']})<br>";
                                    echo "{$row['room']}<br>";
                                    // Hiển thị trạng thái điểm danh nếu là sinh viên
                                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'student') {
                                        if (isset($row['attendance_status'])) {
                                            if ($row['attendance_status'] === 'Present') {
                                                echo '<span style=\'color:green;font-weight:bold\'>(attended)</span><br>';
                                            } elseif ($row['attendance_status'] === 'Absent') {
                                                echo '<span style=\'color:red;font-weight:bold\'>(absent)</span><br>';
                                            } else {
                                                echo '<span style=\'color:#888;font-style:italic\'>(not yet marked)</span><br>';
                                            }
                                        } else {
                                            echo '<span style=\'color:#888;font-style:italic\'>(not yet marked)</span><br>';
                                        }
                                    }
                                }
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
