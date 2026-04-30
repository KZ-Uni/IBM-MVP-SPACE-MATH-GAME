<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'educator')
{
    header("Location: login.php");
    exit;
}

$educator_id = $_SESSION['user_id'];

if (isset($_POST['add_student_id']))
{
    $student_id = intval($_POST['add_student_id']);
    $stmt = $conn->prepare("INSERT IGNORE INTO educator_students (educator_id, student_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $educator_id, $student_id);
    $stmt->execute();

    $_SESSION['status'] = $stmt->affected_rows > 0 
        ? "Student added successfully!" 
        : "Student already assigned or invalid ID.";

    header("Location: educator_dashboard.php");
    exit;
}

if (isset($_POST['remove_student_id']))
{
    $student_id = intval($_POST['remove_student_id']);
    $stmt = $conn->prepare("DELETE FROM educator_students WHERE educator_id = ? AND student_id = ?");
    $stmt->bind_param("ii", $educator_id, $student_id);
    $stmt->execute();

    $_SESSION['status'] = $stmt->affected_rows > 0 
        ? "Student removed successfully!" 
        : "Student not found under your account.";

    header("Location: educator_dashboard.php");
    exit;
}


if (isset($_POST['set_student_id'], $_POST['difficulty_level']))
{
    $student_id = intval($_POST['set_student_id']);
    $difficulty = $_POST['difficulty_level'];

    $stmt = $conn->prepare("
        UPDATE student_settings 
        SET difficulty = ? 
        WHERE user_id = ? 
        AND EXISTS (
            SELECT 1 FROM educator_students 
            WHERE educator_id = ? AND student_id = ?
        )
    ");
    $stmt->bind_param("siii", $difficulty, $student_id, $educator_id, $student_id);
    $stmt->execute();

    $_SESSION['status'] = $stmt->affected_rows > 0 
        ? "Difficulty updated!" 
        : "Failed to update difficulty.";

    header("Location: educator_dashboard.php");
    exit;
}


if (isset($_POST['toggle_lock_student_id']))
{
    $student_id = intval($_POST['toggle_lock_student_id']);

    $stmt = $conn->prepare("
        SELECT difficulty_locked 
        FROM student_settings 
        WHERE user_id = ? 
        AND EXISTS (
            SELECT 1 FROM educator_students 
            WHERE educator_id = ? AND student_id = ?
        )
    ");
    $stmt->bind_param("iii", $student_id, $educator_id, $student_id);
    $stmt->execute();
    $stmt->bind_result($locked);
    $stmt->fetch();
    $stmt->close();

    $newLock = $locked ? 0 : 1;

    $stmt = $conn->prepare("UPDATE student_settings SET difficulty_locked = ? WHERE user_id = ?");
    $stmt->bind_param("ii", $newLock, $student_id);
    $stmt->execute();

    $_SESSION['status'] = $newLock ? "Difficulty locked!" : "Difficulty unlocked!";

    header("Location: educator_dashboard.php");
    exit;
}

$query = "
SELECT 
    u.id,
    u.username,
    s.games_played,
    s.correct_answers,
    s.wrong_answers,
    s.avg_reaction_time,
    s.wins,
    s.losses,
    ss.difficulty,
    ss.difficulty_locked
FROM educator_students es
JOIN users u ON es.student_id = u.id
LEFT JOIN student_stats s ON u.id = s.user_id
LEFT JOIN student_settings ss ON u.id = ss.user_id
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
        .tableWrapper
        {
            width: 100%;
            overflow-x: auto;
            margin: 0 auto;
        }
        .tableWrapper table
        {
            min-width: 900px;
        }
        
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

    <?php 
    if (isset($_SESSION['status'])) {
        echo "<div class='status'>{$_SESSION['status']}</div>";
        unset($_SESSION['status']);
    }
    ?>

    <h2>Your Students</h2>

    <div class="tableWrapper">
        <table class="educatorTable">
            <tr>
                <th>ID</th>
                <th>Student</th>
                <th>Games</th>
                <th>Correct</th>
                <th>Wrong</th>
                <th>Avg Reaction</th>
                <th>Wins</th>
                <th>Losses</th>
                <th>Win Rate</th>
                <th>Difficulty</th>
                <th>Lock</th>
            </tr>

            <?php while($row = $result->fetch_assoc()):
                $total = $row['wins'] + $row['losses'];
                $winrate = $total > 0 ? round(($row['wins'] / $total) * 100) . "%" : "0%";
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= $row['games_played'] ?></td>
                <td><?= $row['correct_answers'] ?></td>
                <td><?= $row['wrong_answers'] ?></td>
                <td><?= $row['avg_reaction_time'] ?> ms</td>
                <td><?= $row['wins'] ?></td>
                <td><?= $row['losses'] ?></td>
                <td><?= $winrate ?></td>
                <td><?= $row['difficulty'] ?></td>
                <td>
                    <form method="POST">
                        <input type="hidden" name="toggle_lock_student_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn-lock">
                            <?= $row['difficulty_locked'] ? "Unlock" : "Lock" ?>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <h2>Manage Students</h2>

    <form method="POST" class="form-inline">
        <input type="number" name="add_student_id" placeholder="Student ID to Add" required>
        <button type="submit" class="btn-action">Add Student</button>
    </form>

    <form method="POST" class="form-inline">
        <input type="number" name="remove_student_id" placeholder="Student ID to Remove" required>
        <button type="submit" class="btn-action">Remove Student</button>
    </form>

    <h2>Set Student Difficulty</h2>

    <form method="POST" class="form-inline">
        <input type="number" name="set_student_id" placeholder="Student ID" required>
        <select name="difficulty_level" required>
            <option value="easy">Easy</option>
            <option value="medium">Medium</option>
            <option value="hard">Hard</option>
        </select>
        <button type="submit" class="btn-action">Set Difficulty</button>
    </form>

    <a id="settingsBtn" href="settings.php">Settings</a>

    <a id="playBtn" href="index.php">Back to Menu</a>
    <a id="logoutBtn" href="logout.php">Logout</a>

</div>

</body>
</html>
