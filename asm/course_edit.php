<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy thông tin khóa học
$stmt = $conn->prepare("SELECT * FROM Course WHERE course_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$course = $stmt->get_result()->fetch_assoc();
if (!$course) {
    die("Course not found.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_code = trim($_POST['course_code']);
    $course_name = trim($_POST['course_name']);
    $description = trim($_POST['description']);
    $teacher_id = intval($_POST['teacher_id']);
    $class_name = trim($_POST['class_name']);
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("UPDATE Course SET course_code = ?, course_name = ?, description = ?, teacher_id = ?, class_name = ?, start_date = ?, end_date = ? WHERE course_id = ?");
    $stmt->bind_param("sssisssi", $course_code, $course_name, $description, $teacher_id, $class_name, $start_date, $end_date, $course_id);

    if ($stmt->execute()) {
        header("Location: course_management.php");
        exit();
    } else {
        $error = "Failed to update course.";
    }
}

// Lấy danh sách giáo viên
$teachers = $conn->query("SELECT u.user_id, u.fullname FROM User u JOIN UserRole ur ON u.user_id = ur.user_id JOIN Role r ON ur.role_id = r.role_id WHERE r.role_name = 'teacher'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Course</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"> <?= htmlspecialchars($error) ?> </div>
    <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label for="course_code" class="form-label">Course Code</label>
            <input type="text" name="course_code" id="course_code" class="form-control" value="<?= htmlspecialchars($course['course_code']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="course_name" class="form-label">Course Name</label>
            <input type="text" name="course_name" id="course_name" class="form-control" value="<?= htmlspecialchars($course['course_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="3" required><?= htmlspecialchars($course['description']) ?></textarea>
        </div>
        <div class="mb-3">
            <label for="teacher_id" class="form-label">Teacher</label>
            <select name="teacher_id" id="teacher_id" class="form-control" required>
                <option value="">-- Select Teacher --</option>
                <?php while ($teacher = $teachers->fetch_assoc()): ?>
                    <option value="<?= $teacher['user_id'] ?>" <?= $teacher['user_id'] == $course['teacher_id'] ? 'selected' : '' ?>><?= htmlspecialchars($teacher['fullname']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="class_name" class="form-label">Class</label>
            <input type="text" name="class_name" id="class_name" class="form-control" value="<?= htmlspecialchars($course['class_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="start_date" class="form-label">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= htmlspecialchars($course['start_date']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= htmlspecialchars($course['end_date']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard_admin.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
