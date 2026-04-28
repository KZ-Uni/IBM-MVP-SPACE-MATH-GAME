<?php
require "db.php";
session_start();

if (!isset($_SESSION['user_id']))
{
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Space Math TCG</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        /* your styles here */
    </style>
</head>
<body>

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

            <a href="game.php">
                <button class="menuBtn start">Start Game</button>
            </a>

            <a href="tutorial.php">
                <button class="menuBtn secondary">Play Tutorial</button>
            </a>

            <a href="logout.php">
                <button class="menuBtn">Logout</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>
