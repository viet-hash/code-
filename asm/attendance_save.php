<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teacher', 'admin'])) {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['attendance'])) {
    $success = 0;
    $fail = 0;
    foreach ($_POST['attendance'] as $enrollment_id => $schedules) {
        foreach ($schedules as $schedule_id => $status) {
            $date = date('Y-m-d');
            // Lấy ghi chú nếu có
            $note = '';
            if (isset($_POST['note'][$enrollment_id][$schedule_id])) {
                $note = trim($_POST['note'][$enrollment_id][$schedule_id]);
            }
            // Kiểm tra đã có điểm danh chưa
            $check_sql = "SELECT attendance_id FROM Attendance WHERE enrollment_id=? AND schedule_id=? AND date=?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->bind_param('iis', $enrollment_id, $schedule_id, $date);
            $check_stmt->execute();
            $check_stmt->store_result();
            if ($check_stmt->num_rows > 0) {
                // Đã có, cập nhật
                $update_sql = "UPDATE Attendance SET status=?, notes=? WHERE enrollment_id=? AND schedule_id=? AND date=?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param('ssiis', $status, $note, $enrollment_id, $schedule_id, $date);
                if ($update_stmt->execute()) $success++; else $fail++;
            } else {
                // Chưa có, thêm mới
                $insert_sql = "INSERT INTO Attendance (enrollment_id, schedule_id, date, status, notes) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param('iisss', $enrollment_id, $schedule_id, $date, $status, $note);
                if ($insert_stmt->execute()) $success++; else $fail++;
            }
        }
    }
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Attendance Save Result</title>';
    echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
    echo '<style>body{background:#f8f9fa;font-family:Segoe UI,Arial,sans-serif;} .center-page{max-width:600px;margin:40px auto;text-align:center;} h2{color:#ff8800;font-weight:bold;margin-bottom:24px;} </style></head><body>';
    echo '<div class="center-page">';
    echo '<h2>Attendance Save Result</h2>';
    echo '<div class="alert alert-success">Successfully saved attendance for ' . $success . ' students.</div>';
    if ($fail > 0) echo '<div class="alert alert-danger">Failed to save attendance for ' . $fail . ' students.</div>';
    echo '<a href="attendance_list.php" class="btn btn-primary mt-4">Back to Session List</a>';
    echo '</div></body></html>';
    exit();
} else {
    echo '<div class="alert alert-warning">No attendance data submitted.</div>';
}
?>
