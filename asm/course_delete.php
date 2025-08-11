<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}
require_once "includes/db.php";

$course_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($course_id > 0) {
    $stmt = $conn->prepare("DELETE FROM Course WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
}
header('Location: dashboard_admin.php');
exit();
