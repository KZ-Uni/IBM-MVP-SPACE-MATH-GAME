<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "math_game";
$port = 3307;

$conn = new mysqli($servername, $username, $password, "", $port);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ---------------------------
   DATABASE
----------------------------*/
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

/* ---------------------------
   PLAYERS
----------------------------*/
$conn->query("
CREATE TABLE IF NOT EXISTS players (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
");

/* ---------------------------
   GAME SESSIONS (NEW)
   - tracks each full match
----------------------------*/
$conn->query("
CREATE TABLE IF NOT EXISTS game_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    player_id INT NOT NULL,
    score INT DEFAULT 0,
    rounds_survived INT DEFAULT 0,
    result ENUM('win','lose') DEFAULT 'lose',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE
) ENGINE=InnoDB;
");

/* ---------------------------
   OPTIONAL: CARD LOG (NEW)
   - useful for debugging / analytics
----------------------------*/
$conn->query("
CREATE TABLE IF NOT EXISTS game_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL,
    turn_type VARCHAR(20),
    card_type VARCHAR(20),
    operator CHAR(1),
    value INT,
    damage INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES game_sessions(id) ON DELETE CASCADE
) ENGINE=InnoDB;
");
?>