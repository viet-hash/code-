<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teacher', 'admin'])) {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$schedule_id = $_GET['schedule_id'] ?? '';
if (!$schedule_id) {
    echo '<div class="alert alert-danger">Không tìm thấy buổi học.</div>';
    exit();
}

$sql = "SELECT a.*, u.fullname, u.user_id, c.course_code, c.course_name, c.class_name
        FROM Attendance a
        JOIN Enrollment e ON a.enrollment_id = e.enrollment_id
        JOIN User u ON e.student_id = u.user_id
        JOIN Schedule s ON a.schedule_id = s.schedule_id
        JOIN Course c ON s.course_id = c.course_id
        WHERE a.schedule_id = ? AND a.date = CURDATE()
        ORDER BY u.fullname";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $schedule_id);
$stmt->execute();
$result = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Thông tin điểm danh sinh viên</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
        .container { max-width: 1100px; margin: 40px auto; }
        h2 { color: #ff8800; font-weight: bold; margin-bottom: 28px; }
        .table { border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); font-size: 1.08rem; }
        .table thead th { background: #ff8800; color: #fff; font-weight: 600; text-align: center; }
        .table tbody td { text-align: center; vertical-align: middle; }
    </style>
</head>
<body>
<div class="container">
    <h2>Thông tin điểm danh sinh viên</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã lớp</th>
                <th>Mã SV</th>
                <th>Họ tên</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stt = 1;
            while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $stt++; ?></td>
                    <td><?php echo htmlspecialchars($row['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($row['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <a href="attendance_list.php" class="btn btn-secondary mt-3">Quay lại danh sách buổi học</a>
</div>
</body>
</html>
