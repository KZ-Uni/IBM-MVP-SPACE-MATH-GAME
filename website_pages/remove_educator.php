<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}

$student_id = $_POST['student_id'];
$educator_id = $_POST['educator_id'];

$stmt = $conn->prepare("
    DELETE FROM educator_students 
    WHERE student_id = ? AND educator_id = ?
");
$stmt->bind_param("ii", $student_id, $educator_id);
$stmt->execute();

header("Location: admin.php");
exit;