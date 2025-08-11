<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $student_id = $_POST['student_id'];

    // Kiểm tra dữ liệu đầu vào
    if (!empty($course_id) && !empty($student_id)) {
        $stmt = $conn->prepare("INSERT INTO Enrollment (course_id, student_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $course_id, $student_id);

        if ($stmt->execute()) {
            $message = "Thêm sinh viên vào khoá học thành công!";
        } else {
            $message = "Lỗi: Không thể thêm sinh viên vào khoá học.";
        }

        $stmt->close();
    } else {
        $message = "Vui lòng chọn đầy đủ thông tin.";
    }
}

// Hiển thị danh sách sinh viên đã ghi danh
$enrollments = $conn->query("SELECT e.enrollment_id, c.course_name, u.fullname FROM Enrollment e JOIN Course c ON e.course_id = c.course_id JOIN User u ON e.student_id = u.user_id");
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sinh Viên Vào Khoá Học</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Thêm Sinh Viên Vào Khoá Học</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info"> <?= htmlspecialchars($message) ?> </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="course" class="form-label">Chọn khoá học</label>
            <select name="course_id" id="course" class="form-control" required>
                <option value="">-- Chọn khoá học --</option>
                <?php
                $courses = $conn->query("SELECT course_id, course_name FROM Course");
                while ($course = $courses->fetch_assoc()): ?>
                    <option value="<?= $course['course_id'] ?>"><?= htmlspecialchars($course['course_name']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="student" class="form-label">Chọn sinh viên</label>
            <select name="student_id" id="student" class="form-control" required>
                <option value="">-- Chọn sinh viên --</option>
                <?php
                $students = $conn->query("SELECT u.user_id, u.fullname 
                                          FROM User u
                                          JOIN UserRole ur ON u.user_id = ur.user_id
                                          JOIN Role r ON ur.role_id = r.role_id
                                          WHERE r.role_name = 'student'");
                while ($student = $students->fetch_assoc()): ?>
                    <option value="<?= $student['user_id'] ?>"><?= htmlspecialchars($student['fullname']) ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Thêm</button>
    </form>

    <a href="enroll_management.php" class="btn btn-secondary mt-3">Quay lại</a>

</div>
</body>
</html>
