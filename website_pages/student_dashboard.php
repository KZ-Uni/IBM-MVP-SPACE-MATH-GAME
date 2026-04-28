<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student')
{
    header("Location: login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

$query = "
SELECT 
    games_played, 
    correct_answers, 
    wrong_answers, 
    avg_reaction_time,
    wins,
    losses
FROM student_stats
WHERE user_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

$stats = $stats ?? [
    "games_played" => 0,
    "correct_answers" => 0,
    "wrong_answers" => 0,
    "avg_reaction_time" => 0,
    "wins" => 0,
    "losses" => 0
];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="studentWrapper">
    <h1>Student Dashboard</h1>

    <div id="studentStatsBox">

        <div class="statItem">
            <span class="statLabel">Games Played</span>
            <span class="statValue"><?php echo $stats['games_played']; ?></span>
        </div>

        <div class="statItem">
            <span class="statLabel">Correct Answers</span>
            <span class="statValue"><?php echo $stats['correct_answers']; ?></span>
        </div>

        <div class="statItem">
            <span class="statLabel">Wrong Answers</span>
            <span class="statValue"><?php echo $stats['wrong_answers']; ?></span>
        </div>

        <div class="statItem">
            <span class="statLabel">Avg Reaction Time</span>
            <span class="statValue"><?php echo $stats['avg_reaction_time']; ?> ms</span>
        </div>

        <div class="statItem">
            <span class="statLabel">Wins</span>
            <span class="statValue"><?php echo $stats['wins']; ?></span>
        </div>

        <div class="statItem">
            <span class="statLabel">Losses</span>
            <span class="statValue"><?php echo $stats['losses']; ?></span>
        </div>

        <div class="statItem">
            <span class="statLabel">Win Rate</span>
            <span class="statValue">
                <?php
                    $total = $stats['wins'] + $stats['losses'];
                    echo $total > 0 ? round(($stats['wins'] / $total) * 100) . "%" : "0%";
                ?>
            </span>
        </div>

    </div>

    <a id="playBtn" href="game.php">Play Game</a>
    <a id="logoutBtn" href="logout.php">Logout</a>
</div>

</body>
</html>
