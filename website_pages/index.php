<?php require "db.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Space Math TCG</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        #menuContainer {
            margin-top: 120px;
            text-align: center;
        }

        #menuBox {
            display: inline-block;
            padding: 40px;
            border-radius: 15px;
            background: rgba(0, 0, 0, 0.6);
            box-shadow: 0 0 25px rgba(0,255,255,0.4);
        }

        #menuBox h1 {
            font-size: 42px;
            margin-bottom: 10px;
        }

        #menuBox p {
            opacity: 0.8;
            margin-bottom: 25px;
        }

        .menuBtn {
            display: block;
            width: 220px;
            margin: 12px auto;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            color: white;
            background: linear-gradient(90deg, #00eaff, #0077ff);
            box-shadow: 0 0 12px rgba(0,255,255,0.6);
            transition: 0.2s;
        }

        .menuBtn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(0,255,255,1);
        }

        .menuBtn.secondary {
            background: linear-gradient(90deg, #ff9f1c, #ff4d00);
            box-shadow: 0 0 12px rgba(255,150,0,0.6);
        }

        .menuBtn.secondary:hover {
            box-shadow: 0 0 20px rgba(255,150,0,1);
        }
    </style>
</head>
<body>

<!-- SPACE BACKGROUND -->
<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="uiWrapper">
    <div id="menuContainer">
        <div id="menuBox">
            <h1>🚀 Space Math TCG</h1>
            <p>Battle enemies using math-powered cards</p>

            <a href="login.php">
                <button class="menuBtn">Login</button>
            </a>

            <a href="register.php">
                <button class="menuBtn">Create Account</button>
            </a>

            <a href="tutorial.php">
                <button class="menuBtn secondary">Play Tutorial</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>