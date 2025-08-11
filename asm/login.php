<?php
session_start();
require_once "includes/db.php";

$error = "";

// Tạo role và admin mặc định nếu chưa có
$default_admin_username = 'Khanhdz';
$default_admin_password = password_hash('khanh123', PASSWORD_DEFAULT);

// Tạo role nếu chưa có
$roles = ['admin', 'teacher', 'student'];
foreach ($roles as $role_name) {
    $check_role = $conn->prepare("SELECT * FROM Role WHERE role_name = ?");
    $check_role->bind_param("s", $role_name);
    $check_role->execute();
    $role_result = $check_role->get_result();
    if ($role_result->num_rows === 0) {
        $insert_role = $conn->prepare("INSERT INTO Role (role_name) VALUES (?)");
        $insert_role->bind_param("s", $role_name);
        $insert_role->execute();
    }
}
// Tạo admin nếu chưa có
$check_admin = $conn->prepare("SELECT * FROM User WHERE username = ?");
$check_admin->bind_param("s", $default_admin_username);
$check_admin->execute();
$check_admin_result = $check_admin->get_result();
if ($check_admin_result->num_rows === 0) {
    $insert_admin = $conn->prepare("INSERT INTO User (username, password, is_active) VALUES (?, ?, 1)");
    $insert_admin->bind_param("ss", $default_admin_username, $default_admin_password);
    $insert_admin->execute();
    $admin_id = $conn->insert_id;
    // Gán role admin cho user
    $get_role = $conn->prepare("SELECT role_id FROM Role WHERE role_name = 'admin'");
    $get_role->execute();
    $role_row = $get_role->get_result()->fetch_assoc();
    $role_id = $role_row['role_id'];
    $insert_userrole = $conn->prepare("INSERT INTO UserRole (user_id, role_id) VALUES (?, ?)");
    $insert_userrole->bind_param("ii", $admin_id, $role_id);
    $insert_userrole->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT u.*, r.role_name FROM User u
        JOIN UserRole ur ON u.user_id = ur.user_id
        JOIN Role r ON ur.role_id = r.role_id
        WHERE u.username = ? AND u.is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role_name'];
            $_SESSION['username'] = $user['username'];
            if (headers_sent($file, $line)) {
                die("Headers already sent in $file on line $line");
            }
            switch ($_SESSION['role']) {
                case 'admin':
                    header("Location: dashboard_admin.php");
                    exit;
                case 'teacher':
                    header("Location: dashboard_teacher.php");
                    exit;
                case 'student':
                    header("Location: dashboard_student.php");
                    exit;
                default:
                    $error = "Invalid role.";
            }
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "User not found or inactive.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="login-container">
    <h2>Login</h2>
    <form method="post">
      <div class="form-group">
        <label>Username:</label>
        <input type="text" name="username" required>
      </div>

      <div class="form-group">
        <label>Password:</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit">Login</button>
    </form>
</div>

<?php if (!empty($error)): ?>
    <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>
</body>
</html>
