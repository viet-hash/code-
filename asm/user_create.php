<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$error = $success = "";

// Lấy danh sách role
$roles = [];
$res = $conn->query("SELECT role_id, role_name FROM Role");
while($row = $res->fetch_assoc()) {
    $roles[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];
    $fullname = trim($_POST["fullname"]);
    $phone = trim($_POST["phone"]);
    $email = trim($_POST["email"]);
    $role_id = intval($_POST["role_id"]);
    $is_active = isset($_POST["is_active"]) ? 1 : 0;

    // Check username exists
    $stmt = $conn->prepare("SELECT * FROM User WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $error = "Username already exists.";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO User (username, password, fullname, phone, email, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $username, $hash, $fullname, $phone, $email, $is_active);
        if ($stmt->execute()) {
            $user_id = $conn->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO UserRole (user_id, role_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $user_id, $role_id);
            $stmt2->execute();
            header('Location: dashboard_admin.php');
            exit();
        } else {
            $error = "Failed to create user.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
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
    <h2>Add User</h2>
    <?php if ($error) echo "<div class='error'>$error</div>"; ?>
    <?php if ($success) echo "<div class='success'>$success</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Username:</label>
            <input type="text" name="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password:</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Full Name:</label>
            <input type="text" name="fullname" class="form-control">
        </div>
        <div class="mb-3">
            <label>Phone:</label>
            <input type="text" name="phone" class="form-control">
        </div>
        <div class="mb-3">
            <label>Email:</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label>Role:</label>
            <select name="role_id" class="form-control" required>
                <option value="">-- Select Role --</option>
                <?php foreach($roles as $role): ?>
                    <option value="<?= $role['role_id'] ?>"><?= htmlspecialchars($role['role_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label><input type="checkbox" name="is_active" checked> Active</label>
        </div>
        <button type="submit" class="btn btn-primary">Add User</button>
        <a href="dashboard_admin.php" class="btn btn-secondary">Back</a>
    </form>
</div>
</body>
</html>
