<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "math_game";
$port = 3307;

$conn = new mysqli($servername, $username, $password, "", $port);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");
$conn->select_db($dbname);

$conn->query("
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('student','educator','parent','admin') DEFAULT 'student',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS student_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    games_played INT DEFAULT 0,
    correct_answers INT DEFAULT 0,
    wrong_answers INT DEFAULT 0,
    avg_reaction_time FLOAT DEFAULT 0,
    wins INT DEFAULT 0,
    losses INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS educator_students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    educator_id INT NOT NULL,
    student_id INT NOT NULL,
    FOREIGN KEY (educator_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS parent_children (
    id INT AUTO_INCREMENT PRIMARY KEY,
    parent_id INT NOT NULL,
    child_id INT NOT NULL,
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (child_id) REFERENCES users(id) ON DELETE CASCADE
)
");

$conn->query("
CREATE TABLE IF NOT EXISTS student_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    difficulty ENUM('easy','medium','hard') DEFAULT 'easy',
    difficulty_locked TINYINT(1) DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)
");

function createDefaultUser($conn, $username, $password, $role)
{
    $email = $username . "@system.local";
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0)
    {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $username, $email, $hashed, $role);
        $insert->execute();
    }
}

createDefaultUser($conn, "admin", "admin", "admin");
createDefaultUser($conn, "educator", "educator", "educator");
createDefaultUser($conn, "parent", "parent", "parent");
createDefaultUser($conn, "student", "student", "student");

function getUserIdByRole($conn, $role)
{
    $stmt = $conn->prepare("SELECT id FROM users WHERE role = ? LIMIT 1");
    $stmt->bind_param("s", $role);
    $stmt->execute();
    $stmt->bind_result($id);
    $stmt->fetch();
    return $id;
}

$studentId  = getUserIdByRole($conn, "student");
$educatorId = getUserIdByRole($conn, "educator");
$parentId   = getUserIdByRole($conn, "parent");

if ($studentId && $educatorId)
{
    $check = $conn->prepare("SELECT id FROM educator_students WHERE student_id = ? AND educator_id = ?");
    $check->bind_param("ii", $studentId, $educatorId);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0)
    {
        $assign = $conn->prepare("INSERT INTO educator_students (educator_id, student_id) VALUES (?, ?)");
        $assign->bind_param("ii", $educatorId, $studentId);
        $assign->execute();
    }
}

if ($studentId && $parentId)
{
    $check = $conn->prepare("SELECT id FROM parent_children WHERE child_id = ? AND parent_id = ?");
    $check->bind_param("ii", $studentId, $parentId);
    $check->execute();
    $check->store_result();
    if ($check->num_rows === 0)
    {
        $assign = $conn->prepare("INSERT INTO parent_children (parent_id, child_id) VALUES (?, ?)");
        $assign->bind_param("ii", $parentId, $studentId);
        $assign->execute();
    }
}

if ($studentId)
{
    $checkStats = $conn->prepare("SELECT user_id FROM student_stats WHERE user_id = ?");
    $checkStats->bind_param("i", $studentId);
    $checkStats->execute();
    $checkStats->store_result();
    if ($checkStats->num_rows === 0)
    {
        $insertStats = $conn->prepare("INSERT INTO student_stats (user_id) VALUES (?)");
        $insertStats->bind_param("i", $studentId);
        $insertStats->execute();
    }

    $checkSettings = $conn->prepare("SELECT id FROM student_settings WHERE user_id = ?");
    $checkSettings->bind_param("i", $studentId);
    $checkSettings->execute();
    $checkSettings->store_result();
    if ($checkSettings->num_rows === 0)
    {
        $insertSettings = $conn->prepare("INSERT INTO student_settings (user_id) VALUES (?)");
        $insertSettings->bind_param("i", $studentId);
        $insertSettings->execute();
    }
}

session_start();
?>
