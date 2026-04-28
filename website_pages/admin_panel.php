<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin')
{
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="adminWrapper">

    <h1>Admin Panel</h1>

    <a href="users.php" class="adminBtn" style="
        display:inline-block;
        padding:12px 20px;
        background:linear-gradient(90deg,#00eaff,#0077ff);
        border-radius:8px;
        color:white;
        text-decoration:none;
        font-weight:bold;
        margin-bottom:25px;
        box-shadow:0 0 15px rgba(0,255,255,0.5);
    ">View All Users</a>

    <h2>Assign Student to Educator</h2>
    <form class="adminForm" method="POST" action="assign_educator.php">
        <label>Student ID:</label><br>
        <input type="number" name="student_id" required><br>

        <label>Educator ID:</label><br>
        <input type="number" name="educator_id" required><br>

        <button type="submit">Assign</button>
    </form>

    <h2>Assign Child to Parent</h2>
    <form class="adminForm" method="POST" action="assign_parent.php">
        <label>Child ID:</label><br>
        <input type="number" name="child_id" required><br>

        <label>Parent ID:</label><br>
        <input type="number" name="parent_id" required><br>

        <button type="submit">Assign</button>
    </form>

    <br><br>
    <a id="logoutBtn" href="logout.php">Logout</a>

</div>

</body>
</html>
