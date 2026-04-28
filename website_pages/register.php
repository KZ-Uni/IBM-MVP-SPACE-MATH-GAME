<?php
require "db.php";

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $role = $_POST["role"];

    $checkUsername = $conn->prepare("SELECT id FROM users WHERE username = ? LIMIT 1");
    $checkUsername->bind_param("s", $username);
    $checkUsername->execute();
    $checkUsername->store_result();

    $checkEmail = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $checkEmail->store_result();

    if ($checkUsername->num_rows > 0)
    {
        $message = "Error: Username already exists.";
        $messageType = "error";
    }
    elseif ($checkEmail->num_rows > 0)
        {
        $message = "Error: Email already exists.";
        $messageType = "error";
    }
    else
    {
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $email, $password, $role);

        if ($stmt->execute())
        {
            $message = "Congratulations! Your account is ready. Please log in to begin.";
            $messageType = "success";
        }
        else
        {
            $message = "Oops! Something went wrong. Please try again.";
            $messageType = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        html, body
        {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        body
        {
            background: black;
        }

        #spaceBackground
        {
            position: fixed;
            inset: 0;
            z-index: -5;
        }

        #loginLayout
        {
            position: absolute;
            inset: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #loginBox
        {
            width: 320px;
            padding: 30px;
            background: rgba(3, 6, 20, 0.9);
            border: 1px solid rgba(0, 234, 255, 0.25);
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.9), 0 0 25px rgba(0,234,255,0.25);
            text-align: center;
        }

        #loginBox input,
        #loginBox select
        {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            background: rgba(5, 10, 30, 0.9);
            border: 2px solid #00eaff;
            border-radius: 10px;
            color: #00eaff;
            font-size: 14px;
            font-weight: bold;
            outline: none;
            box-sizing: border-box;
        }

        #loginBox select option
        {
            background: #050a1e;
            color: #00eaff;
        }

        #loginBox button
        {
            width: 100%;
            margin-top: 12px;
            padding: 12px;
            background: #00eaff;
            border: none;
            border-radius: 10px;
            font-weight: bold;
            cursor: pointer;
        }

        #loginError
        {
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 8px;
            font-weight: bold;
        }

        .success
        {
            color: #00ff88 !important;
            background-color: rgba(0, 255, 136, 0.12);
            border: 2px solid #00ff88;
        }

        .error
        {
            color: red;
            background-color: rgba(255, 0, 0, 0.1);
            border: 2px solid red;
        }

        a
        {
            color: cyan;
            display: block;
            margin-top: 10px;
            text-decoration: none;
        }
    </style>
</head>

<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="loginLayout">
    <div id="loginBox">

        <h2>Create Account</h2>

        <?php if (!empty($message)): ?>
            <div id="loginError" class="<?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>

            <select name="role" required>
                <option value="student">Student</option>
                <option value="educator">Educator</option>
                <option value="parent">Parent</option>
                <option value="admin">Admin</option>
            </select>

            <button type="submit">Register</button>
        </form>

        <a href="login.php">Already have an account? Login</a>

    </div>
</div>

</body>
</html>
