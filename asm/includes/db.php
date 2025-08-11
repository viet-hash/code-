<?php
$servername = "localhost";
$username = "root";
$password = "";

// Sử dụng đúng database theo yêu cầu (tên có dấu và khoảng trắng)
$dbname = "quản lí sinh viên";

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Thiết lập bộ ký tự để xử lý Unicode/tiếng Việt
if (function_exists('mysqli_set_charset')) {
    mysqli_set_charset($conn, 'utf8mb4');
}
?>
