<?php
require "db.php";

if (!isset($_SESSION['user_id'])) exit;

$data = json_decode(file_get_contents("php://input"), true);

$correct = $data['correct'];
$wrong = $data['wrong'];
$reaction = $data['reaction'];
$wins = $data['wins'];
$losses = $data['losses'];

$student_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    UPDATE student_stats
    SET 
        games_played = games_played + 1,
        correct_answers = correct_answers + ?,
        wrong_answers = wrong_answers + ?,
        wins = wins + ?,
        losses = losses + ?,
        avg_reaction_time = (avg_reaction_time + ?) / 2
    WHERE user_id = ?
");

$stmt->bind_param("iiiidi", $correct, $wrong, $wins, $losses, $reaction, $student_id);
$stmt->execute();
?>
