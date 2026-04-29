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

    $stmt = $conn->prepare("DELETE FROM parent_child WHERE child_id = ? AND parent_id = ?");
    $stmt->bind_param("ii", $child_id, $parent_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0)
        header("Location: admin_dashboard.php?action=remove_parent&status=success");
    else
        header("Location: admin_dashboard.php?action=remove_parent&status=error");
    $stmt->close();
    exit;
}
header("Location: admin_dashboard.php?action=remove_parent&status=error");
exit;
?>
