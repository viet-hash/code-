<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(120deg, #f8f9fa 60%, #e3f0ff 100%); font-family: 'Segoe UI', sans-serif; }
        .sidebar {
            background: linear-gradient(180deg, #fff 70%, #e3f0ff 100%);
            min-height: 100vh;
            box-shadow: 2px 0 8px rgba(0,0,0,0.07);
            padding: 0;
        }
        .sidebar .nav-link {
            color: #333;
            font-weight: 500;
            padding: 18px 24px;
            border-radius: 0 20px 20px 0;
            margin-bottom: 8px;
            transition: background 0.2s, color 0.2s;
        }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background: linear-gradient(90deg, #007BFF 60%, #6dd5ed 100%);
            color: #fff;
        }
        .sidebar .sidebar-title {
            font-size: 1.2rem;
            font-weight: bold;
            padding: 24px 24px 12px 24px;
            color: #007BFF;
            letter-spacing: 1px;
        }
        .content {
            padding: 40px 30px;
            background: rgba(255,255,255,0.95);
            border-radius: 18px;
            margin: 30px 0;
            box-shadow: 0 4px 18px rgba(0,0,0,0.07);
        }
        .topbar {
            background: linear-gradient(90deg, #007BFF 60%, #6dd5ed 100%);
            padding: 16px 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }
        .user-info {
            font-weight: bold;
            color: #fff;
            letter-spacing: 0.5px;
        }
        h2 {
            color: #007BFF;
            font-weight: bold;
            margin-bottom: 18px;
        }
        .nav-link.logout {
            color: #dc3545 !important;
            font-weight: bold;
        }
        .nav-link.logout:hover {
            background: #ffeaea !important;
            color: #b30000 !important;
        }
        .avatar-circle {
            display: inline-block;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6dd5ed 0%, #007BFF 100%);
            color: #fff;
            font-size: 1.3rem;
            font-weight: bold;
            text-align: center;
            line-height: 38px;
            margin-right: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            vertical-align: middle;
        }
        .user-details .detail {
            color: #fff;
            font-size: 1rem;
            margin-left: 10px;
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="topbar">
        <span class="user-info">
            <span class="avatar-circle"><?php echo strtoupper(substr($username,0,1)); ?></span>
            Welcome, <?php echo htmlspecialchars($username); ?>
            <span class="user-details ms-3">
                <?php
                // Lấy thêm thông tin user
                require_once "includes/db.php";
                $stmt = $conn->prepare("SELECT fullname, phone, email FROM User WHERE username = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $stmt->bind_result($fullname, $phone, $email);
                if ($stmt->fetch()) {
                    echo "<span class='detail'>| $fullname</span> <span class='detail'>| $phone</span> <span class='detail'>| $email</span>";
                }
                $stmt->close();
                ?>
            </span>
        </span>
    </div>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-title">Admin Menu</div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link active" href="#" onclick="showContent('user')">User Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('course')">Course Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#" onclick="showContent('enroll')">Enroll</a>
                    </li>
                   <li class="nav-item">
                       <a class="nav-link" href="create_schedule.php">Create New Session</a>
                   </li>
                    <li class="nav-item">
                        <a class="nav-link logout" href="logout.php">Logout</a>
                    </li>
                </ul>
            </nav>
            <main class="col-md-10 ms-sm-auto content" id="main-content">
                <div id="user" class="content-section"></div>
                <div id="course" class="content-section" style="display:none;"></div>
                <div id="enroll" class="content-section" style="display:none;"></div>
            </main>
        </div>
    </div>
    <script>
        function loadTabContent(tab, file) {
            var section = document.getElementById(tab);
            section.innerHTML = '<div class="text-center py-5"><span class="spinner-border text-primary"></span> Loading...</div>';
            fetch(file)
                .then(response => response.text())
                .then(html => {
                    section.innerHTML = html;
                });
        }
        function showContent(section) {
            document.getElementById('user').style.display = (section === 'user') ? '' : 'none';
            document.getElementById('course').style.display = (section === 'course') ? '' : 'none';
            document.getElementById('enroll').style.display = (section === 'enroll') ? '' : 'none';
            var links = document.querySelectorAll('.sidebar .nav-link');
            links.forEach(function(link) { link.classList.remove('active'); });
            if(section === 'user') links[0].classList.add('active');
            if(section === 'course') links[1].classList.add('active');
            if(section === 'enroll') links[2].classList.add('active');
            if(section === 'user') loadTabContent('user', 'user_management.php');
            if(section === 'course') loadTabContent('course', 'course_management.php');
            if(section === 'enroll') loadTabContent('enroll', 'enroll_management.php');
        }
        // Load default tab on page load
        document.addEventListener('DOMContentLoaded', function() {
            showContent('user');
        });
    </script>
</body>
</html>
