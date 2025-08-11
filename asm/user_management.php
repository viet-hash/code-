<?php
require_once "includes/db.php";
?>
<h2>User Management</h2>
<p>Manage students and teachers here.</p>
<table class="table table-bordered table-hover bg-white">
    <thead class="table-primary">
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $sql = "SELECT u.user_id, u.username, u.fullname, u.phone, u.email, r.role_name, u.is_active
            FROM User u
            JOIN UserRole ur ON u.user_id = ur.user_id
            JOIN Role r ON ur.role_id = r.role_id
            ORDER BY u.user_id DESC";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['user_id'] ?></td>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['fullname']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['role_name']) ?></td>
            <td><?= $row['is_active'] ? 'Active' : 'Inactive' ?></td>
            <td>
                <a href="user_edit.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                <a href="user_delete.php?id=<?= $row['user_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<a href="user_create.php" class="btn btn-success">Add User</a>
