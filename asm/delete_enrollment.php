<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

if (isset($_GET['id'])) {
    $enrollment_id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM Enrollment WHERE enrollment_id = ?");
    $stmt->bind_param("i", $enrollment_id);

    if ($stmt->execute()) {
        $message = "Xóa thành công!";
    } else {
        $message = "Lỗi: Không thể xóa.";
    }

    $stmt->close();
}

header('Location: enrollment_add.php');
exit();
?>
