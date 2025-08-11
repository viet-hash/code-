<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id > 0) {
    // Xoá user khỏi UserRole trước
    $stmt = $conn->prepare("DELETE FROM UserRole WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    // Xoá user khỏi User
    $stmt2 = $conn->prepare("DELETE FROM User WHERE user_id = ?");
    $stmt2->bind_param("i", $user_id);
    $stmt2->execute();
}
header('Location: dashboard_admin.php');
exit();
