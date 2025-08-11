<?php
require_once "includes/db.php";
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Course Management</h2>
    <a href="course_create.php" class="btn btn-success px-4 py-2">+ Add Course</a>
</div>
<form class="row g-2 mb-3" method="GET" action="dashboard_admin.php" style="max-width:600px;">
    <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search by name, code, teacher..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
    </div>
    <div class="col-md-3">
        <select name="teacher" class="form-control">
            <option value="">All Teachers</option>
            <?php
            $teachers = $conn->query("SELECT DISTINCT u.user_id, u.fullname FROM Course c LEFT JOIN User u ON c.teacher_id = u.user_id WHERE u.fullname IS NOT NULL");
            while($t = $teachers->fetch_assoc()): ?>
                <option value="<?= $t['user_id'] ?>" <?= (isset($_GET['teacher']) && $_GET['teacher']==$t['user_id'])?'selected':'' ?>><?= htmlspecialchars($t['fullname']) ?></option>
            <?php endwhile; ?>
        </select>
    </div>
    <div class="col-md-3">
        <input type="date" name="start" class="form-control" value="<?= isset($_GET['start']) ? $_GET['start'] : '' ?>">
    </div>
    <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
</form>
<div class="table-responsive">
<table class="table table-bordered table-hover bg-white align-middle">
    <thead class="table-primary">
        <tr style="vertical-align:middle;">
            <th>ID</th>
            <th>Code</th>
            <th>Name</th>
            <th>Description</th>
            <th>Teacher</th>
            <th>Class</th>
            <th>Start</th>
            <th>End</th>
            <th style="width:120px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $where = [];
    $params = [];
    if (!empty($_GET['search'])) {
        $where[] = "(c.course_name LIKE ? OR c.course_code LIKE ? OR u.fullname LIKE ?)";
        $params[] = "%".$_GET['search']."%";
        $params[] = "%".$_GET['search']."%";
        $params[] = "%".$_GET['search']."%";
    }
    if (!empty($_GET['teacher'])) {
        $where[] = "c.teacher_id = ?";
        $params[] = $_GET['teacher'];
    }
    if (!empty($_GET['start'])) {
        $where[] = "c.start_date >= ?";
        $params[] = $_GET['start'];
    }
    $sql = "SELECT c.*, u.fullname as teacher_name FROM Course c LEFT JOIN User u ON c.teacher_id = u.user_id";
    if ($where) $sql .= " WHERE ".implode(" AND ", $where);
    $sql .= " ORDER BY c.course_id DESC";
    $stmt = $conn->prepare($sql);
    if ($params) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['course_id'] ?></td>
            <td><?= htmlspecialchars($row['course_code']) ?></td>
            <td><?= htmlspecialchars($row['course_name']) ?></td>
            <td><?= htmlspecialchars($row['description']) ?></td>
            <td><?= htmlspecialchars($row['teacher_name']) ?></td>
            <td><?= htmlspecialchars($row['class_name']) ?></td>
            <td><?= htmlspecialchars($row['start_date']) ?></td>
            <td><?= htmlspecialchars($row['end_date']) ?></td>
            <td>
                <a href="course_edit.php?id=<?= $row['course_id'] ?>" class="btn btn-sm btn-warning me-1">Edit</a>
                <a href="course_delete.php?id=<?= $row['course_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this course?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>
