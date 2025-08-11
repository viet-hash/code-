<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";
$teacher_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
        .center-page { max-width: 1200px; margin: 0 auto; text-align: center; }
        h2 { color: #ff8800; font-weight: bold; font-size: 2.2rem; margin-bottom: 28px; }
        .table { min-width: 800px; margin: 0 auto; }
        .table thead th { background: #ff8800; color: #fff; font-weight: 600; }
    </style>
</head>
<body>
<div class="center-page">
    <h2>My Courses</h2>
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-hover bg-white align-middle">
            <thead class="table-primary">
                <tr>
                    <th>Course Code</th>
                    <th>Course Name</th>
                    <th>Description</th>
                    <th>Start Date</th>
                    <th>End Date</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $sql = "SELECT course_code, course_name, description, start_date, end_date
                    FROM Course
                    WHERE teacher_id = $teacher_id";
            $result = $conn->query($sql);
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['course_code']) ?></td>
                    <td><?= htmlspecialchars($row['course_name']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['start_date']) ?></td>
                    <td><?= htmlspecialchars($row['end_date']) ?></td>
                </tr>
                <?php endwhile;
            } else {
                echo '<tr><td colspan="5">No courses have been assigned to you.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
