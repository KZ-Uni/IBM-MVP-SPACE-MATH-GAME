<?php
require "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}
if (isset($_POST['user_id']))
{
    $userId = intval($_POST['user_id']);

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0)
        header("Location: admin_dashboard.php?action=delete_user&status=success");
    else
        header("Location: admin_dashboard.php?action=delete_user&status=error");
    $stmt->close();
    exit;
}
header("Location: admin_dashboard.php?action=delete_user&status=error");
exit;
?>
