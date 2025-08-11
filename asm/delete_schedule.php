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

// Xoá buổi học
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {
    $sql = "DELETE FROM Schedule WHERE schedule_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $schedule_id);
    if ($stmt->execute()) {
        header('Location: add_schedule.php');
        exit();
    } else {
        echo '<div class="alert alert-danger">Lỗi xoá: ' . $conn->error . '</div>';
    }
}

// Hiển thị xác nhận xoá
$sql = "SELECT s.*, c.course_code, c.course_name, c.class_name FROM Schedule s JOIN Course c ON s.course_id = c.course_id WHERE s.schedule_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $schedule_id);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Xoá buổi học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Xoá buổi học</h2>
    <div class="alert alert-warning">Bạn có chắc chắn muốn xoá buổi học này?</div>
    <table class="table table-bordered">
        <tr><th>Mã khoá học</th><td><?php echo htmlspecialchars($schedule['course_code']); ?></td></tr>
        <tr><th>Tên khoá học</th><td><?php echo htmlspecialchars($schedule['course_name']); ?></td></tr>
        <tr><th>Lớp</th><td><?php echo htmlspecialchars($schedule['class_name']); ?></td></tr>
        <tr><th>Thứ</th><td><?php echo htmlspecialchars($schedule['day_of_week']); ?></td></tr>
        <tr><th>Giờ bắt đầu</th><td><?php echo htmlspecialchars($schedule['start_time']); ?></td></tr>
        <tr><th>Giờ kết thúc</th><td><?php echo htmlspecialchars($schedule['end_time']); ?></td></tr>
        <tr><th>Phòng học</th><td><?php echo htmlspecialchars($schedule['room']); ?></td></tr>
    </table>
    <a href="delete_schedule.php?schedule_id=<?php echo $schedule_id; ?>&confirm=yes" class="btn btn-danger">Xoá</a>
    <a href="add_schedule.php" class="btn btn-secondary">Quay lại</a>
</div>
</body>
</html>
