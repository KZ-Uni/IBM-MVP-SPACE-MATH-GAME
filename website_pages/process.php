<?php
session_start();

$user = intval($_POST["answer"]);
$correct = intval($_POST["correct"]);

if ($user === $correct) {
    $_SESSION["enemy_hp"] -= 5;
    $_SESSION["score"] += 10;
    $msg = "Direct hit! Enemy ship damaged.";
} else {
    $msg = "Missed! Wrong answer.";
}

if ($_SESSION["enemy_hp"] <= 0) {
    header("Location: win.php");
    exit;
}

echo "
<script>
alert('$msg');
window.location.href='game.php';
</script>
";
?>
