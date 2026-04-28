<?php
require "db.php";

if ($_SESSION['role'] !== 'admin') exit;

$child = $_POST['child_id'];
$parent = $_POST['parent_id'];

$stmt = $conn->prepare("INSERT INTO parent_children (parent_id, child_id) VALUES (?, ?)");
$stmt->bind_param("ii", $parent, $child);
$stmt->execute();

header("Location: admin_panel.php");
exit;
?>
