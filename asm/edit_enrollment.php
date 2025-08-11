<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $enrollment_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT course_id, student_id FROM Enrollment WHERE enrollment_id = ?");
    $stmt->bind_param("i", $enrollment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $enrollment = $result->fetch_assoc();
    $stmt->close();

    if (!$enrollment) {
        header('Location: enrollment_add.php');
        exit();
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enrollment_id = $_POST['enrollment_id'];
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];

    if (!empty($course_id) && !empty($student_id)) {
        $stmt = $conn->prepare("UPDATE Enrollment SET course_id = ?, student_id = ? WHERE enrollment_id = ?");
        $stmt->bind_param("iii", $course_id, $student_id, $enrollment_id);

        if ($stmt->execute()) {
            $message = "Cập nhật thành công!";
        } else {
            $message = "Lỗi: Không thể cập nhật.";
        }

        $stmt->close();
    } else {
        $message = "Vui lòng chọn đầy đủ thông tin.";
    }
}

$courses = $conn->query("SELECT course_id, course_name FROM Course");
$students = $conn->query("SELECT u.user_id, u.fullname FROM User u JOIN UserRole ur ON u.user_id = ur.user_id JOIN Role r ON ur.role_id = r.role_id WHERE r.role_name = 'student'");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa Ghi Danh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Sửa Ghi Danh</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="hidden" name="enrollment_id" value="<?= $enrollment_id ?>">
        <div class="mb-3">
            <label for="course" class="form-label">Chọn khoá học</label>
            <select name="course_id" id="course" class="form-control" required>
                <option value="">-- Chọn khoá học --</option>
                <?php while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?= $course['course_id'] ?>" <?= $course['course_id'] == $enrollment['course_id'] ? 'selected' : '' ?>><?= htmlspecialchars($course['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="student" class="form-label">Chọn sinh viên</label>
            <select name="student_id" id="student" class="form-control" required>
                <option value="">-- Chọn sinh viên --</option>
                <?php while ($student = $students->fetch_assoc()): ?>
                    <option value="<?= $student['user_id'] ?>" <?= $student['user_id'] == $enrollment['student_id'] ? 'selected' : '' ?>><?= htmlspecialchars($student['fullname']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>

    <a href="enrollment_add.php" class="btn btn-secondary mt-3">Quay lại</a>
</div>
</body>
</html>
