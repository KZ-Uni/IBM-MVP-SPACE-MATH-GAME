<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Space Math TCG Battle</title>
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
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
                    <input type="number" id="answerInput">
                    <button onclick="submitAnswer()">Submit</button>
                </div>
            </div>
        </div>

        <div id="wrongAnswerBox">
            Wrong Answer!
        </div>

        <script src="../js/game.js"></script>
    </body>
</html>