<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teacher', 'admin'])) {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";
// Lấy danh sách khoá học của giáo viên

if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT course_id, course_name FROM Course";
    $result = $conn->query($sql);
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
} else {
    $teacher_id = $_SESSION['user_id'];
    $sql = "SELECT course_id, course_name FROM Course WHERE teacher_id = $teacher_id";
    $result = $conn->query($sql);
    $courses = [];
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create New Session</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Create New Session</h2>
    <form method="post" action="add_schedule.php" class="border p-4 rounded bg-light">
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select name="course_id" id="course_id" class="form-select" required>
                <option value="">-- Select course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="day_of_week" class="form-label">Day of Week</label>
            <select name="day_of_week" id="day_of_week" class="form-select" required>
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="slot" class="form-label">Select Slot</label>
            <select name="slot" id="slot" class="form-select" required>
                <option value="07:15:00-09:15:00">Slot 1 (7:15-9:15)</option>
                <option value="09:25:00-11:25:00">Slot 2 (9:25-11:25)</option>
                <option value="12:00:00-14:00:00">Slot 3 (12:00-14:00)</option>
                <option value="14:10:00-16:10:00">Slot 4 (14:10-16:10)</option>
                <option value="16:20:00-18:20:00">Slot 5 (16:20-18:20)</option>
                <option value="18:30:00-20:30:00">Slot 6 (18:30-20:30)</option>
                <option value="20:30:00-22:30:00">Slot 7 (20:30-22:30)</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="room" class="form-label">Room</label>
            <input type="text" name="room" id="room" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Session</button>
    </form>
</div>
</body>
</html>
