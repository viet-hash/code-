<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Lấy danh sách lịch học và trạng thái điểm danh của sinh viên
$sql = "SELECT sch.schedule_id, sch.day_of_week, sch.start_time, sch.end_time, sch.room, c.course_code, c.course_name, c.class_name, e.enrollment_id,
        att.status AS attendance_status
        FROM Schedule sch
        JOIN Course c ON sch.course_id = c.course_id
        JOIN Enrollment e ON e.course_id = c.course_id
        LEFT JOIN Attendance att ON att.enrollment_id = e.enrollment_id AND att.schedule_id = sch.schedule_id AND att.date = CURDATE()
        WHERE e.student_id = ?
        ORDER BY sch.day_of_week, sch.start_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Tạo mảng thời khoá biểu theo ngày và slot
$timetable = [];
while($row = $result->fetch_assoc()) {
    $slot = $row['start_time'] . ' - ' . $row['end_time'];
    $day = $row['day_of_week'];
    $timetable[$day][$slot] = $row;
}
$days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thời khoá biểu của tôi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .content { max-width: 1200px; margin: 40px auto; padding: 24px; background: #fff; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        h2 { color: #ff8800; font-weight: bold; margin-bottom: 28px; }
        .table thead th { background: #ff8800; color: #fff; font-weight: 600; text-align: center; }
        .table td, .table th { vertical-align: middle; text-align: center; }
        .attended { color: green; font-weight: bold; }
        .absent { color: red; font-weight: bold; }
        .pending { color: #888; font-style: italic; }
        a { color: #007bff; text-decoration: underline; }
    </style>
</head>
<body>
<div class="content">
    <h2>Thời khoá biểu & điểm danh của <?= htmlspecialchars($username) ?></h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Thứ</th>
                <th>Slot</th>
                <th>Mã khoá học</th>
                <th>Tên khoá học</th>
                <th>Lớp</th>
                <th>Phòng</th>
                <th>Điểm danh</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($days as $day): ?>
            <?php if (!empty($timetable[$day])): ?>
                <?php foreach ($timetable[$day] as $slot => $row): ?>
                    <tr>
                        <td><?= $day ?></td>
                        <td><?= $slot ?></td>
                        <td><a href="#" style="color:#007bff;"><?= htmlspecialchars($row['course_code']) ?></a></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td><?= htmlspecialchars($row['class_name']) ?></td>
                        <td><?= htmlspecialchars($row['room']) ?></td>
                        <td>
                            <?php
                            if ($row['attendance_status'] === 'Present') {
                                echo '<span class="attended">(attended)</span>';
                            } elseif ($row['attendance_status'] === 'Absent') {
                                echo '<span class="absent">(absent)</span>';
                            } else {
                                echo '<span class="pending">(chưa điểm danh)</span>';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if (empty($timetable)): ?>
        <div class="alert alert-warning">Bạn chưa có lịch học nào.</div>
    <?php endif; ?>
</div>
</body>
</html>
