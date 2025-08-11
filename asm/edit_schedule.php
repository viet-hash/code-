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

// Lấy thông tin buổi học
$sql = "SELECT s.*, c.course_code, c.course_name, c.class_name FROM Schedule s JOIN Course c ON s.course_id = c.course_id WHERE s.schedule_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $schedule_id);
$stmt->execute();
$result = $stmt->get_result();
$schedule = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day_of_week = $_POST['day_of_week'] ?? $schedule['day_of_week'];
    $start_time = $_POST['start_time'] ?? $schedule['start_time'];
    $end_time = $_POST['end_time'] ?? $schedule['end_time'];
    $room = $_POST['room'] ?? $schedule['room'];
    $sql = "UPDATE Schedule SET day_of_week=?, start_time=?, end_time=?, room=? WHERE schedule_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssi', $day_of_week, $start_time, $end_time, $room, $schedule_id);
    if ($stmt->execute()) {
        echo '<div class="alert alert-success">Cập nhật thành công!</div>';
        // Reload lại thông tin
        $sql = "SELECT s.*, c.course_code, c.course_name, c.class_name FROM Schedule s JOIN Course c ON s.course_id = c.course_id WHERE s.schedule_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $schedule_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $schedule = $result->fetch_assoc();
    } else {
        echo '<div class="alert alert-danger">Lỗi cập nhật: ' . $conn->error . '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sửa buổi học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Sửa buổi học</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Mã khoá học</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($schedule['course_code']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Tên khoá học</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($schedule['course_name']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Lớp</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($schedule['class_name']); ?>" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Thứ trong tuần</label>
            <select name="day_of_week" class="form-select" required>
                <?php
                $days = ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"];
                foreach ($days as $day) {
                    $selected = ($schedule['day_of_week'] == $day) ? 'selected' : '';
                    echo "<option value='$day' $selected>$day</option>";
                }
                ?>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Giờ bắt đầu</label>
            <input type="time" name="start_time" class="form-control" value="<?php echo htmlspecialchars($schedule['start_time']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Giờ kết thúc</label>
            <input type="time" name="end_time" class="form-control" value="<?php echo htmlspecialchars($schedule['end_time']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phòng học</label>
            <input type="text" name="room" class="form-control" value="<?php echo htmlspecialchars($schedule['room']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="add_schedule.php" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
</body>
</html>
