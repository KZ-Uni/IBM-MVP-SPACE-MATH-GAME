<?php
require "db.php";

if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'];

$stmt = $conn->prepare("SELECT username, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div id="studentWrapper">
        <h1>Account Settings</h1>

        <form method="POST" action="update_settings.php">

            <label>Username</label>
            <input type="text" name="username"
                value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>Email</label>
            <input type="email" name="email"
                value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>New Password (leave empty to keep current)</label>
            <input type="password" name="password">

            <?php if ($role === 'student'): ?>
                <h3>Student Settings</h3>
                <select name="difficulty">
                    <option value="easy">Easy</option>
                    <option value="normal">Normal</option>
                    <option value="hard">Hard</option>
                </select>
            <?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>

        <br>
        <a href="javascript:history.back()">← Back</a>
    </div>
</body>
</html>