<?php
require "db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student')
{
    header("Location: index.php");
    exit;
}

$stmt = $conn->prepare("SELECT difficulty FROM student_settings WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($difficulty);
$stmt->fetch();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Space Math TCG Battle</title>
        <link rel="stylesheet" href="../css/style.css">
        <style>
            #topButtons
            {
                position: absolute;
                top: 20px;
                left: 20px;
                right: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 9999;
            }

            #rightButtons
            {
                display: flex;
                gap: 12px;
            }

            .spaceBtn
            {
                padding: 10px 18px;
                font-size: 18px;
                font-weight: bold;
                border: 2px solid #4af3ff;
                border-radius: 8px;
                background: rgba(0, 20, 40, 0.7);
                color: #4af3ff;
                cursor: pointer;
                text-shadow: 0 0 6px #4af3ff;
                box-shadow: 0 0 10px #4af3ff inset, 0 0 10px #4af3ff;
                transition: 0.2s ease;
                backdrop-filter: blur(4px);
            }

            .spaceBtn:hover
            {
                background: rgba(0, 40, 80, 0.9);
                box-shadow: 0 0 14px #4af3ff inset, 0 0 14px #4af3ff;
                transform: translateY(-2px);
            }
        </style>
        <script>
            window.GAME_DIFFICULTY = "<?php echo $difficulty; ?>";

            window.animationMode = localStorage.getItem("animMode") || "fast";

            function toggleAnimationSpeed()
            {
                animationMode = animationMode === "fast" ? "slow" : "fast";
                localStorage.setItem("animMode", animationMode);

                const el = document.getElementById("animModeText");
                if (el) el.innerText = animationMode.toUpperCase();
            }

            window.addEventListener("DOMContentLoaded", () =>
            {
                const el = document.getElementById("animModeText");
                if (el) el.innerText = animationMode.toUpperCase();
            });


            let recognition = null;

            function startSpeechInput()
            {
                if (!('webkitSpeechRecognition' in window))
                {
                    alert("Speech recognition not supported in this browser.");
                    return;
                }

                recognition = new webkitSpeechRecognition();
                recognition.lang = "en-US";
                recognition.continuous = false;
                recognition.interimResults = false;

                recognition.onresult = (event) =>
                {
                    let text = event.results[0][0].transcript.trim().toLowerCase();

                    text = text.replace(/point|dot/gi, ".").replace(/ /g, "").replace(/[^0-9.]/g, "");

                    if (text.startsWith(".")) text = "0" + text;
                    if (text.endsWith(".")) text = text.slice(0, -1);

                    const input = document.getElementById("answerInput");
                    if (input) input.value = text;
                };

                recognition.onerror = (e) =>
                {
                    console.log("Speech error:", e.error);
                };

                recognition.onend = () =>
                {
                    recognition = null;
                };

                recognition.start();
            }

        </script>
    </head>
    <body>

        <div id="topButtons">
            <button class="spaceBtn" onclick="goIndex()">⟵ Index</button>

            <div id="rightButtons">
                <button class="spaceBtn" onclick="goDashboard()">🛸 Dashboard</button>

                <button class="spaceBtn" onclick="toggleAnimationSpeed()">
                    ⚡ Mode: <span id="animModeText">FAST</span>
                </button>
            </div>
        </div>

        <div id="spaceBackground"></div>

        <div id="uiWrapper">
            <h1>🚀 Space Math TCG Battle</h1>

            <div id="enemyContainer">
                <div id="enemyLabel">Enemy Hand</div>
                <div id="enemyCards"></div>
                <div id="enemyHiddenNote">Hidden until played.</div>
            </div>

            <div id="enemyHPContainer" class="hpContainer">
                <div class="hpPanel">
                    <div class="hpLabel">👾 Enemy</div>
                    <div class="hpBar">
                        <div id="enemyHPFill" class="hpFill"></div>
                    </div>
                    <div id="enemyHPText" class="hpValue">30 / 30</div>
                </div>
            </div>

            <div id="battlefield">
                <img id="enemyShip" class="ship" src="../imgs/enemy.png">
                <img id="playerShip" class="ship" src="../imgs/player.png">
                <div id="laser"></div>
            </div>

            <div id="playerHPContainer" class="hpContainer">
                <div class="hpPanel">
                    <div class="hpLabel">🚀 Player</div>
                    <div class="hpBar">
                        <div id="playerHPFill" class="hpFill"></div>
                    </div>
                    <div id="playerHPText" class="hpValue">30 / 30</div>
                </div>
            </div>

            <h2 id="turnText">Loading...</h2>

            <div id="handContainer">
                <div id="handTitle">Your Hand</div>
                <div id="cards"></div>
            </div>

            <div id="questionBox" style="display:none;">
                <div id="questionInner">
                    <div id="questionText"></div>
                    <input type="text" id="answerInput" inputmode="numeric">

                    <button onclick="submitAnswer()">Submit</button>
                    <button onclick="startSpeechInput()">🎤 Speak</button>
                </div>
            </div>
        </div>

        <div id="wrongAnswerBox">
            Wrong Answer!
        </div>

        <div id="gameEndPopup">
            <div id="gameEndInner">
                <h2 id="gameEndTitle">Game Over</h2>
                <p id="gameEndMessage"></p>

                <button id="playAgainBtn" onclick="playAgain()">Play Again</button>
                <button id="returnDashboardBtn" onclick="returnToDashboard()">Return to Dashboard</button>
            </div>
        </div>

        <script src="../js/game.js"></script>
    </body>
</html>
