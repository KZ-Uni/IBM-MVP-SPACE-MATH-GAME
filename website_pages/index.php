<?php
require "db.php";
$logged_in = isset($_SESSION['user_id']);

$dashboard_link = "index.php";

if ($logged_in)
{
    switch ($_SESSION['role'])
    {
        case 'student':
            $dashboard_link = "student_dashboard.php";
            break;
        case 'admin':
            $dashboard_link = "admin_panel.php";
            break;
        case 'educator':
            $dashboard_link = "educator_dashboard.php";
            break;
        case 'parent':
            $dashboard_link = "parent_dashboard.php";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Space Math TCG</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        html, body
        {
            margin: 0;
            padding: 0;
            min-height: 100%;
            overflow-x: hidden;
        }

        #noScrollWrapper
        {
            width: 100%;
            min-height: 100vh;
            position: relative;
        }

        #spaceBackground
        {
            position: fixed;
            inset: 0;
            z-index: -5;
        }

        #menuBox
        {
            background: rgba(3, 6, 20, 0.85);
            padding: 40px 50px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(0, 234, 255, 0.25);
            box-shadow: 0 0 30px rgba(0,0,0,0.9), 0 0 25px rgba(0,234,255,0.25);
        }

        .menuBtn
        {
            display: block;
            width: 100%;
            max-width: 260px;
            margin: 14px auto;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            background: rgba(5, 10, 30, 0.9);
            color: #00eaff;
            border: 2px solid #00eaff;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 0 12px rgba(0, 234, 255, 0.4);
            transition: all 0.2s ease;
        }

        .menuBtn:hover
        {
            transform: translateY(-3px) scale(1.05);
            background: rgba(0, 234, 255, 0.1);
            box-shadow: 0 0 15px #00eaff, 0 0 30px rgba(0,234,255,0.6);
        }

        .menuBtn:active
        {
            transform: scale(0.95);
        }

        .menuBtn.start
        {
            border-color: #00ff88;
            color: #00ff88;
            box-shadow: 0 0 14px rgba(0,255,136,0.5);
        }

        .menuBtn.start:hover
        {
            background: rgba(0,255,136,0.15);
            box-shadow: 0 0 18px #00ff88, 0 0 35px rgba(0,255,136,0.7);
        }

        .menuBtn.secondary
        {
            border-color: #0077ff;
            color: #66b3ff;
        }

        .menuBtn.secondary:hover
        {
            background: rgba(0,119,255,0.15);
        }

        .menuBtn.dashboard
        {
            border-color: #00eaff;
        }

        .menuBtn.logout
        {
            border-color: #ff4d4d;
            color: #ff4d4d;
            box-shadow: 0 0 12px rgba(255,0,0,0.5);
        }

        .menuBtn.logout:hover
        {
            background: rgba(255,0,0,0.15);
            box-shadow: 0 0 18px #ff4d4d, 0 0 30px rgba(255,0,0,0.7);
        }

        #menuLayout
        {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        #loginLinkLayout
        {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #loginLinkBox
        {
            background: rgba(3, 6, 20, 0.85);
            padding: 30px 40px;
            border-radius: 15px;
            text-align: center;
            border: 1px solid rgba(0, 234, 255, 0.25);
            box-shadow: 0 0 30px rgba(0,0,0,0.9), 0 0 25px rgba(0,234,255,0.25);
            color: #00eaff;
        }

        #loginLinkBox a
        {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            border-radius: 10px;
            border: 2px solid #00eaff;
            color: #00eaff;
            text-decoration: none;
            font-weight: bold;
            background: rgba(5, 10, 30, 0.9);
            box-shadow: 0 0 12px rgba(0, 234, 255, 0.4);
            transition: all 0.2s ease;
        }

        #loginLinkBox a:hover
        {
            transform: translateY(-3px) scale(1.05);
            background: rgba(0, 234, 255, 0.1);
            box-shadow: 0 0 15px #00eaff, 0 0 30px rgba(0,234,255,0.6);
        }
    </style>
</head>
<body>
    <div id="noScrollWrapper">
        <div id="spaceBackground">
            <div id="stars1" class="starLayer"></div>
            <div id="stars2" class="starLayer"></div>
            <div id="stars3" class="starLayer"></div>
        </div>

        <div id="uiWrapper">
            <?php if (!$logged_in): ?>
                <div id="loginLinkLayout">
                    <div id="loginLinkBox">
                        <h1>🚀 Space Math TCG</h1>
                        <p>Welcome. Please log in to continue.</p>
                        <a href="login.php">Go to Login</a>
                    </div>
                </div>

            <?php else: ?>

                <div id="menuLayout">
                    <div id="menuBox">
                        <h1>🚀 Space Math TCG</h1>
                        <p>Battle enemies using math-powered cards</p>

                        <a href="game.php"><button class="menuBtn start">Start Game</button></a>
                        <a href="tutorial.php"><button class="menuBtn secondary">Play Tutorial</button></a>
                        <a href="<?php echo $dashboard_link; ?>"><button class="menuBtn dashboard">Go to Dashboard</button></a>
                        <a href="logout.php"><button class="menuBtn logout">Logout</button></a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
