<?php
echo "<h2>🔍 Kiểm tra kết nối Database</h2>";

// Include file kết nối database
include 'includes/db.php';

if ($conn) {
    echo "<p style='color: green; font-weight: bold;'>✅ Kết nối database thành công!</p>";
    
    echo "<h3>📊 Thông tin kết nối:</h3>";
    echo "<ul>";
    echo "<li>Server: $servername</li>";
    echo "<li>Database: $dbname</li>";
    echo "<li>Username: $username</li>";
    echo "<li>Trạng thái: " . ($conn->ping() ? "🟢 Đang kết nối" : "🔴 Mất kết nối") . "</li>";
    echo "</ul>";
    
    // Kiểm tra các bảng
    echo "<h3>📋 Kiểm tra các bảng:</h3>";
    $tables = ['user', 'course', 'courseteacher', 'enrollment', 'schedule', 'attendance', 'role', 'userrole'];
    
    foreach ($tables as $table) {
        $sql = "SHOW TABLES LIKE '$table'";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            echo "<p style='color: green;'>✅ Bảng '$table' - Tồn tại</p>";
        } else {
            echo "<p style='color: red;'>❌ Bảng '$table' - Không tồn tại</p>";
        }
    }
    
    // Test query đơn giản
    echo "<h3>🧪 Test truy vấn:</h3>";
    $test = $conn->query("SELECT 1 as test");
    if ($test) {
        echo "<p style='color: green;'>✅ Truy vấn test thành công</p>";
    } else {
        echo "<p style='color: red;'>❌ Truy vấn test thất bại</p>";
    }
    
    $conn->close();
    echo "<p style='color: blue;'>🔒 Đã đóng kết nối database</p>";
    
} else {
    echo "<p style='color: red; font-weight: bold;'>❌ Không thể kết nối database!</p>";
}
?>

<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 30px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    min-height: 100vh;
}
h2, h3 {
    color: #fff;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
}
ul {
    background: rgba(255,255,255,0.1);
    padding: 20px;
    border-radius: 10px;
    backdrop-filter: blur(10px);
}
p {
    margin: 10px 0;
    padding: 10px;
    border-radius: 5px;
    background: rgba(255,255,255,0.1);
}
</style>

