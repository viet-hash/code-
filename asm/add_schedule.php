<?php
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['teacher', 'admin'])) {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST["course_id"] ?? '';
    $day_of_week = $_POST["day_of_week"] ?? '';
    $slot = $_POST["slot"] ?? '';
    $start_time = '';
    $end_time = '';
    if ($slot) {
        $times = explode('-', $slot);
        if (count($times) == 2) {
            $start_time = $times[0];
            $end_time = $times[1];
        }
    }
    $room = $_POST["room"] ?? '';
    $errors = [];
    // Data validation
    if (!$course_id || !$day_of_week || !$start_time || !$end_time || !$room) {
        $errors[] = "Please fill in all required information.";
    }
    if (empty($errors)) {
        $sql = "INSERT INTO Schedule (course_id, day_of_week, start_time, end_time, room) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $course_id, $day_of_week, $start_time, $end_time, $room);
        if ($stmt->execute()) {
            // Show info of the created session and all schedules
            echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><title>Session Created</title>';
            echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">';
            echo '<style>body{background:#f8f9fa;font-family:Segoe UI,Arial,sans-serif;} .center-page{max-width:600px;margin:40px auto;text-align:center;} h2{color:#ff8800;font-weight:bold;margin-bottom:24px;} table{margin:0 auto;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,0.08);} th,td{padding:12px 18px;text-align:left;} th{background:#ff8800;color:#fff;} </style></head><body>';
            echo '<div class="center-page">';
            echo '<h2>Session created successfully!</h2>';
            echo '<table class="table table-bordered">';
            echo '<tr><th>Course ID</th><td>' . htmlspecialchars($_POST['course_id']) . '</td></tr>';
            echo '<tr><th>Day of Week</th><td>' . htmlspecialchars($_POST['day_of_week']) . '</td></tr>';
            echo '<tr><th>Slot</th><td>' . htmlspecialchars($_POST['slot']) . '</td></tr>';
            echo '<tr><th>Room</th><td>' . htmlspecialchars($_POST['room']) . '</td></tr>';
            echo '</table>';
            echo '<a href="dashboard_admin.php" class="btn btn-primary mt-4">Back to Home</a>';
            echo '<hr style="margin:40px 0;">';
            // Show all schedules
            require_once "includes/db.php";
            $sql = "SELECT s.*, c.course_code, c.course_name, c.class_name FROM Schedule s JOIN Course c ON s.course_id = c.course_id ORDER BY s.day_of_week, s.start_time";
            $result = $conn->query($sql);
            echo '<h2 class="mb-4">All Schedules</h2>';
            echo '<table class="table table-bordered"><thead><tr><th>Course ID</th><th>Course Name</th><th>Class</th><th>Day</th><th>Slot</th><th>Room</th><th>Action</th></tr></thead><tbody>';
            while ($row = $result->fetch_assoc()) {
                $slot = $row['start_time'] . "-" . $row['end_time'];
                echo '<tr>';
                echo '<td>' . htmlspecialchars($row['course_code']) . '</td>';
                echo '<td>' . htmlspecialchars($row['course_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['class_name']) . '</td>';
                echo '<td>' . htmlspecialchars($row['day_of_week']) . '</td>';
                echo '<td>' . htmlspecialchars($slot) . '</td>';
                echo '<td>' . htmlspecialchars($row['room']) . '</td>';
                echo '<td>';
                echo '<a href="edit_schedule.php?schedule_id=' . $row['schedule_id'] . '" class="btn btn-warning btn-sm me-1">Edit</a>';
                echo '<a href="delete_schedule.php?schedule_id=' . $row['schedule_id'] . '" class="btn btn-danger btn-sm">Delete</a>';
                echo '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
            echo '</div></body></html>';
            exit();
        } else {
            $errors[] = "Error creating session: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Session Creation Result</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Session Creation Result</h2>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"> <?php echo $success; ?> </div>
        <a href="create_schedule.php" class="btn btn-primary">Create another session</a>
    <?php else: ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $err) echo $err . '<br>'; ?>
            </div>
        <?php endif; ?>
        <a href="create_schedule.php" class="btn btn-secondary">Back</a>
    <?php endif; ?>
</div>
</body>
</html>

