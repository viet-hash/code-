<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Course List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .content { margin: 40px auto; max-width: 900px; padding: 30px; background: #fff; border-radius: 18px; box-shadow: 0 4px 18px rgba(0,0,0,0.07); }
        h2 { color: #ff8800; font-weight: bold; margin-bottom: 24px; }
        .table thead th { background: #ff8800; color: #fff; font-weight: 600; }
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body>
<div class="content">
    <h2>Danh sách khoá học</h2>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>Mã khoá học</th>
                <th>Tên khoá học</th>
                <th>Mô tả</th>
                <th>Giảng viên</th>
                <th>Lớp</th>
                <th>Ngày bắt đầu</th>
                <th>Ngày kết thúc</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT c.course_code, c.course_name, c.description, u.fullname as teacher, c.class_name, c.start_date, c.end_date FROM Course c JOIN User u ON c.teacher_id = u.user_id WHERE u.username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['course_code']) . "</td>";
                echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                echo "<td>" . htmlspecialchars($row['teacher']) . "</td>";
                echo "<td>" . htmlspecialchars($row['class_name']) . "</td>";
                echo "<td>" . htmlspecialchars($row['start_date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['end_date']) . "</td>";
                echo "</tr>";
            }
        } else {
            echo '<tr><td colspan="7" class="text-center">Không có khoá học nào.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>
