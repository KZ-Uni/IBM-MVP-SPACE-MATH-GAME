<?php
require "db.php";
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}
if (isset($_POST['student_id'], $_POST['educator_id']))
{
    $student_id = intval($_POST['student_id']);
    $educator_id = intval($_POST['educator_id']);

    $stmt = $conn->prepare("INSERT INTO student_educator (student_id, educator_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $student_id, $educator_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0)
        header("Location: admin_dashboard.php?action=assign_student&status=success");
    else
        header("Location: admin_dashboard.php?action=assign_student&status=error");
    $stmt->close();
    exit;
}
header("Location: admin_dashboard.php?action=assign_student&status=error");
exit;
?>
