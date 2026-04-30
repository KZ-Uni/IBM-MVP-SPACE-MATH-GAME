<?php
require "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}
if (isset($_POST['child_id'], $_POST['parent_id']))
{
    $child_id = intval($_POST['child_id']);
    $parent_id = intval($_POST['parent_id']);

    $stmt = $conn->prepare("INSERT INTO parent_children (child_id, parent_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $child_id, $parent_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0)
        header("Location: admin_dashboard.php?action=assign_parent&status=success");
    else
        header("Location: admin_dashboard.php?action=assign_parent&status=error");
    $stmt->close();
    exit;
}
header("Location: admin_dashboard.php?action=assign_parent&status=error");
exit;
?>
