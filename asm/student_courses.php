<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Lấy danh sách khoá học đã đăng ký của sinh viên
$sql = "SELECT c.course_code, c.course_name, c.class_name, c.start_date, c.end_date
        FROM Enrollment e
        JOIN Course c ON e.course_id = c.course_id
        WHERE e.student_id = ?
        ORDER BY c.start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Registered Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .content { max-width: 1100px; margin: 40px auto; padding: 24px; background: #fff; border-radius: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        h2 { color: #ff8800; font-weight: bold; margin-bottom: 28px; }
        .table thead th { background: #ff8800; color: #fff; font-weight: 600; }
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body>
<div class="content">
    <h2>Registered Courses of <?= htmlspecialchars($username) ?></h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Class</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
            <?php $stt = 1; while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $stt++ ?></td>
                    <td><?= htmlspecialchars($row['course_code']) ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['class_name']) ?></td>
                    <td><?= htmlspecialchars($row['start_date']) ?></td>
                    <td><?= htmlspecialchars($row['end_date']) ?></td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-warning">You have not registered for any courses.</div>
    <?php endif; ?>
</div>
</body>
</html>
