<?php
require "db.php";

if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = trim($_POST['password']);

$check = $conn->prepare("
    SELECT id FROM users 
    WHERE (username = ? OR email = ?) 
    AND id != ?
");
$check->bind_param("ssi", $username, $email, $user_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0)
{
    header("Location: settings.php?error=taken");
    exit;
}

$check->close();

$stmt = $conn->prepare("
    UPDATE users 
    SET username = ?, email = ?
    WHERE id = ?
");
$stmt->bind_param("ssi", $username, $email, $user_id);
$stmt->execute();
$stmt->close();

if (!empty($password))
{
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE users 
        SET password = ?
        WHERE id = ?
    ");
    $stmt->bind_param("si", $hashed, $user_id);
    $stmt->execute();
    $stmt->close();
}

if ($role === 'student')
{
    $stmt = $conn->prepare("
        SELECT difficulty_locked 
        FROM student_settings 
        WHERE user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($difficulty_locked);
    $stmt->fetch();
    $stmt->close();

    if (!$difficulty_locked && isset($_POST['difficulty']))
    {
        $difficulty = $_POST['difficulty'];

        $stmt = $conn->prepare("
            UPDATE student_settings 
            SET difficulty = ?
            WHERE user_id = ?
        ");
        $stmt->bind_param("si", $difficulty, $user_id);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: settings.php?success=1");
exit;
?>
