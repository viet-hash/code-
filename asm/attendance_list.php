<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";
// Lấy danh sách buổi học của giáo viên
$username = $_SESSION['username'];
$sql = "SELECT s.schedule_id, s.start_time, s.end_time, c.course_name, c.class_name, s.room FROM Schedule s JOIN Course c ON s.course_id = c.course_id JOIN User u ON c.teacher_id = u.user_id WHERE u.username = ? ORDER BY s.start_time";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Session List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
        .table thead th {
            background: #ff8800;
            color: #fff;
            font-weight: 600;
            text-align: center;
            border: none;
            font-size: 1rem;
        }
        .table tbody td {
            text-align: center;
            vertical-align: middle;
            border: none;
            font-size: 0.98rem;
        }
        .table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        }
        .btn-attendance {
            background: #ff8800;
            color: #fff;
            font-weight: bold;
            border-radius: 18px;
            padding: 6px 18px;
            border: none;
            transition: background 0.2s;
        }
        .btn-attendance:hover {
            background: #ff6600;
        }
        .btn-attendance.taken {
            background: #28a745;
            color: #fff;
        }
        .action-btn {
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            margin-right: 6px;
            border: none;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
        }
        .action-view {
            background: #007bff;
            color: #fff;
        }
        .action-edit {
            background: #ff8800;
            color: #fff;
        }
        .action-view:hover {
            background: #0056b3;
        }
        .action-edit:hover {
            background: #ff6600;
        }
        .table-bordered > :not(caption) > * > * {
            border-width: 0 0 1px 0;
        }
        .table thead th, .table tbody td {
            border-bottom: 1px solid #eee !important;
        }
        .container h2 {
            color: #ff8800;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Attendance Session List</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Slot</th>
                <th>Start</th>
                <th>End</th>
                <th>Subject</th>
                <th>Class</th>
                <th>Room</th>
                <th>Take Attendance</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php $slot_num = 1; foreach ($schedules as $row): ?>
            <tr>
                <td><?php echo $slot_num++; ?></td>
                <td><?php echo substr($row['start_time'],0,5); ?></td>
                <td><?php echo substr($row['end_time'],0,5); ?></td>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo htmlspecialchars($row['class_name']); ?></td>
                <td><?php echo htmlspecialchars($row['room']); ?></td>
                <td>
                    <a href="attendance_students.php?schedule_id=<?php echo $row['schedule_id']; ?>" class="btn btn-attendance">Take Attendance</a>
                </td>
                <td>
                    <a href="view_attendance.php?schedule_id=<?php echo $row['schedule_id']; ?>" class="action-btn action-view" title="View Attendance"><i class="bi bi-eye"></i></a>
                    <a href="edit_schedule.php?schedule_id=<?php echo $row['schedule_id']; ?>" class="action-btn action-edit" title="Edit Session"><i class="bi bi-pencil"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
</html>
