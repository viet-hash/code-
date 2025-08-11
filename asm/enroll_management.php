<?php
require_once "includes/db.php";
?>
<style>
    body { background: #f8f9fa; font-family: 'Segoe UI', Arial, sans-serif; }
    .center-page { max-width: 1300px; margin: 0 auto; text-align: center; }
    h2 { color: #ff8800; font-weight: bold; font-size: 2.4rem; margin-bottom: 32px; text-align: center; }
    .table-responsive { margin: 0 auto; display: flex; justify-content: center; }
    .table { border-radius: 18px; overflow: hidden; box-shadow: 0 6px 24px rgba(0,0,0,0.09); font-size: 1.18rem; min-width: 900px; }
    .table thead th {
        background: #ff8800;
        color: #fff;
        font-weight: 700;   
        text-align: center;
        border: none;
        font-size: 1.22rem;
        padding: 22px 16px;
    }
    .table tbody td {
        text-align: center;
        vertical-align: middle;
        border: none;
        font-size: 1.12rem;
        padding: 18px 12px;
    }
    .btn-primary { background: #ff8800; border: none; font-weight: bold; font-size: 1.15rem; padding: 12px 28px; border-radius: 12px; margin-bottom: 12px; }
    .btn-primary:hover { background: #ff6600; }
    .btn-warning { background: #ffc107; border: none; color: #333; font-size: 1.08rem; border-radius: 10px; padding: 10px 22px; }
    .btn-danger { background: #dc3545; border: none; font-size: 1.08rem; border-radius: 10px; padding: 10px 22px; }
    .table-bordered > :not(caption) > * > * { border-width: 0 0 1px 0; }
    .table thead th, .table tbody td { border-bottom: 1.5px solid #eee !important; }
</style>
<div class="center-page">
    <h2>Student and Course List</h2>
    <a href="enrollment_add.php" class="btn btn-primary">+ Add Student to Course</a>
    <div class="table-responsive mt-4">
        <table class="table table-bordered table-hover bg-white align-middle">
        <thead class="table-primary">
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Class</th>
                <th>Student Name</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT e.enrollment_id, c.course_name, c.course_code, c.class_name, u.fullname
                FROM Enrollment e
                JOIN Course c ON e.course_id = c.course_id
                JOIN User u ON e.student_id = u.user_id
                ORDER BY c.course_name, u.fullname";
        $result = $conn->query($sql);
        while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['course_name']) ?></td>
                <td><?= htmlspecialchars($row['course_code']) ?></td>
                <td><?= htmlspecialchars($row['class_name']) ?></td>
                <td><?= htmlspecialchars($row['fullname']) ?></td>
                <td>
                    <a href="edit_enrollment.php?id=<?= $row['enrollment_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                    <a href="delete_enrollment.php?id=<?= $row['enrollment_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
