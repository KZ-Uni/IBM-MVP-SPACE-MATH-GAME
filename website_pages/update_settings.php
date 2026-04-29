<?php
require "db.php";

if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];

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

$stmt = $conn->prepare("
    UPDATE users 
    SET username = ?, email = ?
    WHERE id = ?
");
$stmt->bind_param("ssi", $username, $email, $user_id);
$stmt->execute();

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
}

if ($_SESSION['role'] === 'student' && isset($_POST['difficulty']))
{
    $_SESSION['difficulty'] = $_POST['difficulty'];
}

header("Location: settings.php?success=1");
exit;
?>
