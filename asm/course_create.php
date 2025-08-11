<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$error = $success = "";

// Lấy danh sách giáo viên
$teacherList = [];
$res = $conn->query("SELECT u.user_id, u.fullname FROM UserRole ur JOIN User u ON ur.user_id = u.user_id JOIN Role r ON ur.role_id = r.role_id WHERE r.role_name = 'teacher'");
while($row = $res->fetch_assoc()) {
    $teacherList[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_code = trim($_POST["course_code"]);
    $course_name = trim($_POST["course_name"]);
    $description = trim($_POST["description"]);
    $teacher_id = intval($_POST["teacher_id"]);
    $class_name = trim($_POST["class_name"]);
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];

    // Validate
    if (!$course_code || !$course_name || !$teacher_id || !$class_name || !$start_date || !$end_date) {
        $error = "Please fill in all required fields.";
    } else {
        // Check trùng mã khoá học
        $stmt = $conn->prepare("SELECT * FROM Course WHERE course_code = ?");
        $stmt->bind_param("s", $course_code);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error = "Course code already exists.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Course (course_code, course_name, description, teacher_id, class_name, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssisss", $course_code, $course_name, $description, $teacher_id, $class_name, $start_date, $end_date);
            if ($stmt->execute()) {
                $success = "Course created successfully.";
                echo "<script>setTimeout(function(){ window.location='dashboard_admin.php'; }, 1200);</script>";
            } else {
                $error = "Failed to create course.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #e3f0ff 60%, #f8f9fa 100%); font-family: 'Segoe UI', sans-serif; }
        .form-box {
            background: #fff;
            max-width: 540px;
            margin: 40px auto;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,123,255,0.10), 0 1.5px 8px #b3d8ff;
            padding: 38px 36px 30px 36px;
        }
        h2 {
            color: #007BFF;
            font-weight: bold;
            margin-bottom: 28px;
            letter-spacing: 1px;
        }
        label {
            font-size: 1.08rem;
            font-weight: 600;
            color: #007BFF;
            margin-bottom: 6px;
        }
        .form-control {
            border-radius: 10px;
            border: 1.5px solid #b3d8ff;
            box-shadow: none;
            font-size: 1.04rem;
            padding: 10px 14px;
            margin-bottom: 6px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            border-color: #007BFF;
            box-shadow: 0 0 0 2px #b3d8ff44;
        }
        .mb-3 {
            margin-bottom: 20px !important;
        }
        .btn {
            margin-top: 18px;
            border-radius: 8px;
            font-weight: 600;
            padding: 8px 22px;
            font-size: 1.05rem;
        }
        .btn-primary {
            background: linear-gradient(90deg, #007BFF 60%, #6dd5ed 100%);
            border: none;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3 60%, #4fc3f7 100%);
        }
        .btn-secondary {
            background: #e3f0ff;
            color: #007BFF;
            border: none;
        }
        .btn-secondary:hover {
            background: #b3d8ff;
            color: #0056b3;
        }
        .error {
            color: #dc3545;
            font-weight: 600;
            margin-bottom: 12px;
        }
        .success {
            color: #28a745;
            font-weight: 600;
            margin-bottom: 12px;
        }
        @media (max-width: 600px) {
            .form-box { padding: 18px 6vw; }
            h2 { font-size: 1.3rem; }
        }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Add Course</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <form method="POST" autocomplete="off">
        <div class="mb-3">
            <label>Course Code <span style="color:red">*</span>:</label>
            <input type="text" name="course_code" class="form-control" value="<?= isset($_POST['course_code']) ? htmlspecialchars($_POST['course_code']) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label>Course Name <span style="color:red">*</span>:</label>
            <input type="text" name="course_name" class="form-control" value="<?= isset($_POST['course_name']) ? htmlspecialchars($_POST['course_name']) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label>Description:</label>
            <textarea name="description" class="form-control"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
        </div>
        <div class="mb-3">
            <label>Teacher <span style="color:red">*</span>:</label>
            <select name="teacher_id" class="form-control" required>
                <option value="">-- Select Teacher --</option>
                <?php foreach($teacherList as $t): ?>
                    <option value="<?= $t['user_id'] ?>" <?= (isset($_POST['teacher_id']) && $_POST['teacher_id']==$t['user_id'])?'selected':'' ?>><?= htmlspecialchars($t['fullname']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Class <span style="color:red">*</span>:</label>
            <input type="text" name="class_name" class="form-control" value="<?= isset($_POST['class_name']) ? htmlspecialchars($_POST['class_name']) : '' ?>" required>
        </div>
        <div class="mb-3">
            <label>Start Date <span style="color:red">*</span>:</label>
            <input type="date" name="start_date" class="form-control" value="<?= isset($_POST['start_date']) ? $_POST['start_date'] : '' ?>" required>
        </div>
        <div class="mb-3">
            <label>End Date <span style="color:red">*</span>:</label>
            <input type="date" name="end_date" class="form-control" value="<?= isset($_POST['end_date']) ? $_POST['end_date'] : '' ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
        <a href="dashboard_admin.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
