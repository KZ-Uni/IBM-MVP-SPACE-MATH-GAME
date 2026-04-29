<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}

$child_id = $_POST['child_id'];
$parent_id = $_POST['parent_id'];

$stmt = $conn->prepare("
    DELETE FROM parent_children 
    WHERE child_id = ? AND parent_id = ?
");
$stmt->bind_param("ii", $child_id, $parent_id);
$stmt->execute();

header("Location: admin.php");
exit;