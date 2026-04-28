<?php
require "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST")
{
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $role);


    if ($stmt->execute()) {
        $message = "Account created! You can now log in.";
    } else {
        $message = "Error: Username or email already exists.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
</head>
<body>

<h2>Create Account</h2>
<p style="color:red;"><?php echo $message; ?></p>

<form method="POST">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>

    <select name="role" required>
        <option value="student">Student</option>
        <option value="teacher">Teacher</option>
        <option value="parent">Parent</option>
    </select><br><br>

    <button type="submit">Register</button>
</form>

</body>
</html>
