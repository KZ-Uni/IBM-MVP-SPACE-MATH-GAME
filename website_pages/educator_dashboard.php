<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'educator')
{
    header("Location: login.php");
    exit;
}

$educator_id = $_SESSION['user_id'];

$query = "
SELECT 
    u.username,
    s.games_played,
    s.correct_answers,
    s.wrong_answers,
    s.avg_reaction_time,
    s.wins,
    s.losses
FROM educator_students es
JOIN users u ON es.student_id = u.id
LEFT JOIN student_stats s ON u.id = s.user_id
WHERE es.educator_id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $educator_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Educator Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
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
            transition: all 0.2s ease;
        }

        #settingsBtn:hover
        {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 0 18px rgba(0, 200, 255, 0.9);
        }
    </style>
</head>
<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="educatorWrapper">
    <h1>Educator Dashboard</h1>
    <h2>Your Students</h2>

    <table class="educatorTable">
        <tr>
            <th>Student</th>
            <th>Games Played</th>
            <th>Correct</th>
            <th>Wrong</th>
            <th>Avg Reaction</th>
            <th>Wins</th>
            <th>Losses</th>
            <th>Win Rate</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()):
            $total = $row['wins'] + $row['losses'];
            $winrate = $total > 0 ? round(($row['wins'] / $total) * 100) . "%" : "0%";
        ?>
        <tr>
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= $row['games_played'] ?></td>
            <td><?= $row['correct_answers'] ?></td>
            <td><?= $row['wrong_answers'] ?></td>
            <td><?= $row['avg_reaction_time'] ?> ms</td>
            <td><?= $row['wins'] ?></td>
            <td><?= $row['losses'] ?></td>
            <td><?= $winrate ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <a id="settingsBtn" href="settings.php">Settings</a>

    <a id="playBtn" href="index.php">Back to Menu</a>
    <a id="logoutBtn" href="logout.php">Logout</a>
</div>

</body>
</html>
