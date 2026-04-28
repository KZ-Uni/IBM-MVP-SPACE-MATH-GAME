<?php
session_start();
$score = $_SESSION["score"];
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>You Win!</title>
</head>
<body style="text-align:center; font-family:Arial; padding:50px;">
    <h1>🎉 Victory!</h1>
    <p>You defeated the alien ship.</p>
    <h2>Your Score: <?= $score ?></h2>

    <a href="index.php">
        <button>Play Again</button>
    </a>
</body>
</html>
