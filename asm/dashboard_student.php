<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$username = $_SESSION['username'];
$profile_image = $_SESSION['profile_image'] ?? 'default-profile.png';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .sidebar { background: #ff8800; color: #fff; height: 100vh; padding: 20px; position: fixed; width: 250px; }
        .sidebar h3 { font-weight: bold; margin-bottom: 20px; }
        .sidebar a { color: #fff; text-decoration: none; display: block; margin-bottom: 10px; font-weight: 600; }
        .sidebar a:hover { background: #ff6600; padding-left: 10px; }
        .content { margin-left: 270px; padding: 20px; }
        .content h2 { color: #ff8800; font-weight: bold; }
        .table thead th { background: #ff8800; color: #fff; font-weight: 600; }
        .profile { position: absolute; top: 20px; right: 20px; display: flex; align-items: center; }
        .profile img { width: 50px; height: 50px; border-radius: 50%; margin-right: 10px; }
        .profile span { font-weight: bold; color: #333; }
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body>
<div class="sidebar">
    <div style="text-align:center; margin-bottom:20px;">
        <img src="img/btec.jpg" alt="BTEC Logo" style="width:120px; height:auto; border-radius:12px; box-shadow:0 2px 8px rgba(0,0,0,0.12);">
    </div>
    <a href="#">Home</a>
    <a href="student_courses.php">My Courses</a>
    <a href="timetable.php">Timetable & Attendance</a>
    <a href="#">Notifications</a>
</div>
<div class="profile">
    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; margin-right: 10px;">
        <i class="bi bi-person" style="font-size: 1.5rem; color: #fff;"></i>
    </div>
    <span><?= htmlspecialchars($username) ?></span>
</div>
<div class="content">
    <h2>Welcome back, <?= htmlspecialchars($username) ?></h2>
    <ul class="list-group">
        <li class="list-group-item">01/08/25 16:42 - THÔNG BÁO THAY ĐỔI BIỂU PHÍ MÔN HỌC LẠI</li>
        <li class="list-group-item">28/07/25 11:30 - THÔNG BÁO: ĐĂNG KÝ CHUYỂN NGÀNH/CHUYỂN NGÀNH, CHUYỂN CƠ SỞ, BẢO LƯU, MIỄN GIẢM HỌC PHẦN - KỲ FALL 2025</li>
        <li class="list-group-item">21/07/25 13:48 - THÔNG BÁO VỀ VIỆC HỌC ONLINE</li>
        <li class="list-group-item">14/07/25 14:15 - [KHẢO THÍ BTEC]: DANH SÁCH SINH VIÊN THI PROGRESS TEST 2 CÁC MÔN TIẾNG ANH KỲ SUMMER 2025 - PART 1 NGÀY 18-21/07/2025</li>
    </ul>
    <a href="#" class="btn btn-primary mt-3">More</a>
</div>
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</body>
<!-- Contact Section -->
<div class="container-fluid mt-5" style="background: #fff7e6; border-top: 2px solid #ff8800; margin-left:270px;">
  <div class="row py-4 justify-content-end">
    <div class="col-md-12">
      <h5 class="fw-bold mb-3">For feedback or any queries, please contact:</h5>
      <p class="mb-1"><i class="bi bi-geo-alt-fill text-warning"></i> <b>Address:</b></p>
      <ul class="mb-2">
        <li>BTEC: BTEC FPT Building, Trinh Van Bo Street, Nam Tu Liem District, Hanoi.</li>
        <li>Melbourne: Melbourne Polytechnic Building D, Trinh Van Bo Street, Nam Tu Liem District, Hanoi</li>
      </ul>
      <p class="mb-1"><i class="bi bi-envelope-fill text-warning"></i> <b>Email:</b></p>
      <ul>
        <li>Academic Department: <a href="mailto:Academic.btec.hn@fe.edu.vn" class="text-decoration-none text-warning">Academic.btec.hn@fe.edu.vn</a></li>
        <li>SRO Department: <a href="mailto:sro.btec.hn@fe.edu.vn" class="text-decoration-none text-warning">sro.btec.hn@fe.edu.vn</a></li>
        <li>Student Affairs: <a href="mailto:se.btec.hn@fe.edu.vn" class="text-decoration-none text-warning">se.btec.hn@fe.edu.vn</a></li>
      </ul>
    </div>
  </div>
</div>
</html>
