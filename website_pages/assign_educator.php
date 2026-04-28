<?php
require "db.php";

if ($_SESSION['role'] !== 'admin') exit;

$student = $_POST['student_id'];
$educator = $_POST['educator_id'];

$stmt = $conn->prepare("INSERT INTO educator_students (educator_id, student_id) VALUES (?, ?)");
$stmt->bind_param("ii", $educator, $student);
$stmt->execute();

header("Location: admin_panel.php");
exit;
?>
