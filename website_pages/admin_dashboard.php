<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}

$message = '';
$messageType = '';
if (isset($_GET['status']) && isset($_GET['action']))
{
    $action = htmlspecialchars($_GET['action']);
    $status = $_GET['status'] === 'success' ? 'success' : 'error';

    if ($status === 'success')
    {
        $message = ucfirst($action) . " completed successfully!";
    }
    else
    {
        $message = ucfirst($action) . " failed. Please check the IDs.";
    }
    $messageType = $status;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        #adminWrapper
        {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }

        #adminWrapper h1
        {
            margin-bottom: 20px;
        }

        .adminRow
        {
            display: inline-block;
            vertical-align: top;
            width: 280px;
            margin: 10px 20px 30px 0;
        }

        .adminForm input
        {
            width: 100%;
            padding: 10px;
            margin: 6px 0 10px 0;
            border-radius: 6px;
            border: 1px solid #00f2fe;
            background: #1f2937;
            color: white;
            box-sizing: border-box;
        }

        .adminForm label
        {
            display: block;
            margin-top: 6px;
        }

        .adminForm button
        {
            width: 100%;
            margin-top: 10px;
        }

        .adminRow h2
        {
            margin-bottom: 10px;
        }

        #settingsBtn
        {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background: linear-gradient(135deg, #4facfe, #00f2fe);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            letter-spacing: 0.5px;
            box-shadow: 0 0 10px rgba(0, 200, 255, 0.6);
        }

        #settingsBtn:hover
        {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 18px rgba(0, 200, 255, 0.9);
        }

        .feedback
        {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            font-weight: bold;
        }

        .feedback.success
        {
            background-color: #0f766e;
            color: #a7f3d0;
        }

        .feedback.error
        {
            background-color: #991b1b;
            color: #fecaca;
        }
    </style>
</head>
<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="adminWrapper">
    <h1>Admin Panel</h1>

    <?php if ($message !== ''): ?>
        <div class="feedback <?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div style="width:100%; margin-bottom:20px;">
        <a href="users.php" class="adminBtn" style="
            display:inline-block;
            padding:12px 20px;
            background:linear-gradient(90deg,#00eaff,#0077ff);
            border-radius:8px;
            color:white;
            text-decoration:none;
            font-weight:bold;
            box-shadow:0 0 15px rgba(0,255,255,0.5);
        ">View All Users</a>
    </div>

    <div class="adminRow">
        <h2>Assign Student</h2>
        <form class="adminForm" method="POST" action="assign_educator.php">
            <label>Student ID:</label>
            <input type="number" name="student_id" required>

            <label>Educator ID:</label>
            <input type="number" name="educator_id" required>

            <button type="submit">Assign</button>
        </form>
    </div>

    <div class="adminRow">
        <h2>Remove Student</h2>
        <form class="adminForm" method="POST" action="remove_educator.php">
            <label>Student ID:</label>
            <input type="number" name="student_id" required>

            <label>Educator ID:</label>
            <input type="number" name="educator_id" required>

            <button type="submit">Remove</button>
        </form>
    </div>

    <div class="adminRow">
        <h2>Assign Parent</h2>
        <form class="adminForm" method="POST" action="assign_parent.php">
            <label>Child ID:</label>
            <input type="number" name="child_id" required>

            <label>Parent ID:</label>
            <input type="number" name="parent_id" required>

            <button type="submit">Assign</button>
        </form>
    </div>

    <div class="adminRow">
        <h2>Remove Parent Link</h2>
        <form class="adminForm" method="POST" action="remove_parent.php">
            <label>Child ID:</label>
            <input type="number" name="child_id" required>

            <label>Parent ID:</label>
            <input type="number" name="parent_id" required>

            <button type="submit">Remove</button>
        </form>
    </div>

    <div class="adminRow">
        <h2>Delete User</h2>
        <form class="adminForm" method="POST" action="delete_user.php">
            <label>User ID:</label>
            <input type="number" name="user_id" required>

            <button type="submit" style="background:red;">Delete</button>
        </form>
    </div>

    <br><br>
    <a id="settingsBtn" href="settings.php">Settings</a>
    <a id="playBtn" href="index.php">Back to Menu</a>
    <a id="logoutBtn" href="logout.php">Logout</a>
</div>

</body>
</html>
