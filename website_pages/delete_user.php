<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}

$user_id = $_POST['user_id'];

$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: admin.php");
exit;