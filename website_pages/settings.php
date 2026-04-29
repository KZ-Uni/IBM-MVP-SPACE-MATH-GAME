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

$difficulty_locked = null;

if ($role === 'student')
{
    $stmt = $conn->prepare("SELECT difficulty_locked FROM student_settings WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($difficulty_locked);
    $stmt->fetch();
    $stmt->close();
}

$backLink = match($role)
{
    'student' => 'student_dashboard.php',
    'parent' => 'parent_dashboard.php',
    'educator' => 'educator_dashboard.php',
    'admin' => 'admin_dashboard.php',
    default => 'index.php'
};
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        #studentWrapper
        {
            width: 450px;
            margin: 80px auto;
            padding: 30px;
            background: #111827 !important;
            border: 3px solid #00f2fe !important;
            box-shadow: 0 0 40px #00f2fe !important;
            border-radius: 15px;
            text-align: center;
            box-sizing: border-box;
        }

        #studentWrapper form
        {
            text-align: left;
        }

        #studentWrapper input, #studentWrapper select
        {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            background: #1f2937 !important;
            border: 1px solid #00f2fe !important;
            color: white !important;
            border-radius: 6px;
            box-sizing: border-box;
        }

        #studentWrapper label
        {
            display: block;
            margin-top: 12px;
            margin-bottom: 4px;
        }

        #studentWrapper button
        {
            margin-top: 20px;
            width: 100%;
            padding: 12px;
            background: #00f2fe !important;
            color: black !important;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-sizing: border-box;
        }

        #backBtn
        {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 15px;
            background: transparent;
            border: 1px solid #00f2fe;
            color: #00f2fe !important;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.2s;
        }

        #backBtn:hover
        {
            background: #00f2fe;
            color: black !important;
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div id="spaceBackground">
        <div id="stars1" class="starLayer"></div>
        <div id="stars2" class="starLayer"></div>
        <div id="stars3" class="starLayer"></div>
    </div>

    <div id="studentWrapper">
        <h1>Account Settings</h1>

        <?php if (isset($_GET['error']) && $_GET['error'] === 'taken') : ?>
            <div style="background:#ff3c3c;color:white;padding:10px;margin-bottom:15px;border-radius:6px;">
                Username or email already taken.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])) : ?>
            <div style="background:#00c853;color:white;padding:10px;margin-bottom:15px;border-radius:6px;">
                Settings updated successfully!
            </div>
        <?php endif; ?>

        <form method="POST" action="update_settings.php">

            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

            <label>New Password</label>
            <input type="password" name="password">

            <?php if ($role === 'student') : ?>

                <h3 style="margin-top:20px;text-align:center;">Student Settings</h3>

                <?php if ($difficulty_locked) : ?>

                    <div style="background:rgba(255,0,0,0.3);padding:10px;border-radius:6px;margin-bottom:10px;text-align:center;font-weight:bold;">
                        Difficulty is locked by your educator.
                    </div>

                <?php else : ?>

                    <label>Difficulty</label>
                    <select name="difficulty">
                        <option value="easy">Easy</option>
                        <option value="normal">Normal</option>
                        <option value="hard">Hard</option>
                    </select>

                <?php endif; ?>

            <?php endif; ?>

            <button type="submit">Save Changes</button>
        </form>

        <a id="backBtn" href="<?php echo $backLink ?>">← Back</a>
    </div>
</body>
</html>
