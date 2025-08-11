<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$error = $success = "";
$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Lấy danh sách role
$roles = [];
$res = $conn->query("SELECT role_id, role_name FROM Role");
while($row = $res->fetch_assoc()) {
    $roles[] = $row;
}

// Lấy thông tin user
$stmt = $conn->prepare("SELECT u.*, ur.role_id FROM User u JOIN UserRole ur ON u.user_id = ur.user_id WHERE u.user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    die("User not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]); // Thêm xử lý username
    $fullname = trim($_POST["fullname"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $role_id = intval($_POST["role_id"]);
    $is_active = isset($_POST["is_active"]) ? 1 : 0;

    $stmt = $conn->prepare("UPDATE User SET username=?, fullname=?, phone=?, email=?, is_active=? WHERE user_id=?");
    $stmt->bind_param("ssssii", $username, $fullname, $phone, $email, $is_active, $user_id);
    if ($stmt->execute()) {
        $stmt2 = $conn->prepare("UPDATE UserRole SET role_id=? WHERE user_id=?");
        $stmt2->bind_param("ii", $role_id, $user_id);
        $stmt2->execute();
        $success = "User updated successfully.";
        // Refresh user data
        $stmt = $conn->prepare("SELECT u.*, ur.role_id FROM User u JOIN UserRole ur ON u.user_id = ur.user_id WHERE u.user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
    } else {
        $error = "Failed to update user.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .form-box { background: #fff; max-width: 500px; margin: 40px auto; border-radius: 12px; box-shadow: 0 0 12px #ccc; padding: 32px; }
        h2 { color: #007BFF; font-weight: bold; margin-bottom: 24px; }
        .btn { margin-top: 18px; }
        .error { color: #dc3545; }
        .success { color: #28a745; }
    </style>
</head>
<body>
<div class="form-box">
    <h2>Edit User</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>">
        </div>
        <div class="mb-3">
            <label>Full Name:</label>
            <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>">
        </div>
        <div class="mb-3">
            <label>Phone:</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone']) ?>">
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>">
        </div>
        <div class="mb-3">
            <label>Role:</label>
            <select name="role_id" class="form-control" required>
                <?php foreach($roles as $role): ?>
                    <option value="<?= $role['role_id'] ?>" <?= $user['role_id']==$role['role_id']?'selected':'' ?>><?= htmlspecialchars($role['role_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label><input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?>> Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="dashboard_admin.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
