<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Blackjack – Space Math TCG</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        body
        {
            background: radial-gradient(circle at center, #020617 0%, #00010a 70%, #000 100%);
            overflow-x: hidden;
        }

        .stars, .stars2, .stars3
        {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 200%;
            height: 200%;
            transform: translate(-50%, -50%);
            background-repeat: repeat;
            background-size: contain;
            pointer-events: none;
            z-index: -5;
        }

        .stars
        {
            background-image: radial-gradient(2px 2px at 20px 30px, #ffffff55, transparent),
                            radial-gradient(2px 2px at 200px 150px, #ffffff33, transparent),
                            radial-gradient(2px 2px at 400px 80px, #ffffff44, transparent),
                            radial-gradient(2px 2px at 600px 300px, #ffffff22, transparent);
            animation: drift1 120s linear infinite;
        }

        .stars2
        {
            background-image: radial-gradient(2px 2px at 50px 200px, #00eaff55, transparent),
                            radial-gradient(2px 2px at 300px 400px, #00eaff33, transparent),
                            radial-gradient(2px 2px at 500px 100px, #00eaff44, transparent);
            animation: drift2 180s linear infinite;
        }

        .stars3
        {
            background-image: radial-gradient(3px 3px at 150px 100px, #00ff8844, transparent),
                            radial-gradient(3px 3px at 450px 250px, #00ff8844, transparent);
            animation: drift3 240s linear infinite;
        }

        #nebulaFog
        {
            position: fixed;
            inset: 0;
            background: radial-gradient(circle at 30% 70%, rgba(0, 255, 200, 0.08), transparent 60%),
                        radial-gradient(circle at 70% 30%, rgba(0, 150, 255, 0.06), transparent 60%);
            filter: blur(40px);
            animation: fogMove 40s ease-in-out infinite alternate;
            z-index: -4;
        }

        @keyframes drift1
        {
            from { transform: translate(-50%, -50%) translate(0, 0); }
            to   { transform: translate(-50%, -50%) translate(-200px, -200px); }
        }

        @keyframes drift2
        {
            from { transform: translate(-50%, -50%) translate(0, 0); }
            to   { transform: translate(-50%, -50%) translate(200px, -150px); }
        }

        @keyframes drift3
        {
            from { transform: translate(-50%, -50%) translate(0, 0); }
            to   { transform: translate(-50%, -50%) translate(-150px, 200px); }
        }

        @keyframes fogMove
        {
            from { transform: translate(0, 0) scale(1); }
            to   { transform: translate(-40px, 30px) scale(1.1); }
        }

        .stars, .stars2, .stars3
        {
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
        }


        #blackjackTable
        {
            margin: 40px auto;
            width: fit-content;
            text-align: center;
        }

        .card
        {
            width: 90px;
            height: 130px;
            background: rgba(5, 10, 30, 0.9);
            border: 2px solid #00eaff;
            border-radius: 10px;
            display: inline-block;
            margin: 10px;
            padding: 10px;
            box-shadow: 0 0 12px rgba(0, 234, 255, 0.4);
        }

        .card-value
        {
            font-size: 28px;
            font-weight: bold;
        }

        .card-suit
        {
            font-size: 22px;
            margin-top: 5px;
        }

        #controls button
        {
            margin: 10px;
            padding: 12px 20px;
            border-radius: 10px;
            border: 2px solid #00eaff;
            background: rgba(5, 10, 30, 0.9);
            color: #00eaff;
            cursor: pointer;
            font-size: 16px;
            transition: 0.2s ease;
        }

        #controls button:hover
        {
            transform: scale(1.05);
            box-shadow: 0 0 15px #00eaff;
        }

        #result
        {
            margin-top: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        #contentWrapper
        {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            text-align: center;
        }
        #contentWrapper h1, #contentWrapper h2, #contentWrapper p, #contentWrapper div, #contentWrapper span
        {
            text-shadow: 0 0 6px #00eaffaa, 0 0 12px #00eaff55;
        }

        #playerTotal, #dealerTotal
        {
            text-shadow: 0 0 8px #00ffccaa, 0 0 14px #00ffcc55;
        }

        #result
        {
            text-shadow: 0 0 10px #ff00ffaa, 0 0 20px #ff00ff55;
        }

        #controls button
        {
            box-shadow: 0 0 10px #00eaff55;
        }
    </style>
</head>

<body>
    <div class="stars"></div>
    <div class="stars2"></div>
    <div class="stars3"></div>
    <div id="nebulaFog"></div>

    <div id="contentWrapper">
        <h1>🃏 Blackjack – Space Math Edition</h1>
        <p>Just for fun. No scores, no database.</p>

        <div id="blackjackTable">

            <h2 id="turnText"></h2>

            <h2>Dealer</h2>
            <div id="dealerCards"></div>
            <div id="dealerTotal"></div>

            <h2>Your Hand</h2>
            <div id="playerCards"></div>
            <div id="playerTotal"></div>

        </div>

        <div id="controls">
            <button onclick="hit()">Hit</button>
            <button onclick="stand()">Stand</button>
            <button onclick="restart()">Restart</button>
        </div>

        <div id="result"></div>
    </div>
    <script>
        const suits = ["♥", "♦", "♣", "♠"];
        const values = ["A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "Q", "K"];

        let deck = [];
        let player = [];
        let dealer = [];

        let playerTurn = true;
        let playerSkipped = false;
        let dealerSkipped = false;

        function createDeck()
        {
            deck = [];
            for (let s of suits)
            {
                for (let v of values)
                {
                    deck.push({ suit: s, value: v });
                }
            }
            deck.sort(() => Math.random() - 0.5);
        }

        function cardValue(card)
        {
            if (card.value === "A") return 11;
            if (["J", "Q", "K"].includes(card.value)) return 10;
            return Number(card.value);
        }

        function handTotal(hand)
        {
            let total = hand.reduce((sum, c) => sum + cardValue(c), 0);
            let aces = hand.filter(c => c.value === "A").length;

            while (total > 21 && aces > 0)
            {
                total -= 10;
                aces--;
            }
            return total;
        }

        function drawCard(hand)
        {
            hand.push(deck.pop());
        }

        function render()
        {
            renderHand("playerCards", player);
            renderHand("dealerCards", dealer);

            document.getElementById("playerTotal").innerText = "Total: " + handTotal(player);
            document.getElementById("dealerTotal").innerText = "Total: " + handTotal(dealer);

            document.getElementById("turnText").innerText =
                playerTurn ? "Your Turn" : "Dealer's Turn";
        }


        function renderHand(id, hand)
        {
            const div = document.getElementById(id);
            div.innerHTML = "";

            hand.forEach(card => {
                const c = document.createElement("div");
                c.className = "card";

                c.innerHTML = `
                    <div class="card-value">${card.value}</div>
                    <div class="card-suit">${card.suit}</div>
                `;

                div.appendChild(c);
            });
        }

        function hit()
        {
            playerDraw();
        }

        function stand()
        {
            playerSkip();
        }

        function playerDraw()
        {
            if (!playerTurn) return;

            drawCard(player);
            playerSkipped = false;
            render();

            if (handTotal(player) > 21)
            {
                endGame("You busted!");
                return;
            }

            playerTurn = false;
            dealerTurn(false);
        }

        function playerSkip()
        {
            if (!playerTurn) return;

            playerSkipped = true;
            playerTurn = false;

            dealerTurn(true);
        }

        function dealerTurn(sameRound)
        {
            setTimeout(() =>
            {
                const total = handTotal(dealer);

                if (total < 15)
                    {
                    drawCard(dealer);
                    dealerSkipped = false;
                }
                else
                {
                    dealerSkipped = true;
                }

                render();

                if (handTotal(dealer) > 21)
                {
                    endGame("Dealer busted! You win!");
                    return;
                }

                if (sameRound && playerSkipped && dealerSkipped)
                {
                    compareHands();
                    return;
                }

                playerTurn = true;
                render();
            }, 700);
        }

        function compareHands()
        {
            const p = handTotal(player);
            const d = handTotal(dealer);

            if (p > d) endGame("You win!");
            else if (p < d) endGame("Dealer wins!");
            else endGame("It's a tie!");
        }

        function endGame(msg)
        {
            document.getElementById("result").innerText = msg;
            playerTurn = false;
        }

        function restart()
        {
            createDeck();
            player = [];
            dealer = [];
            playerTurn = true;
            playerSkipped = false;
            dealerSkipped = false;

            drawCard(player);
            drawCard(player);
            drawCard(dealer);
            drawCard(dealer);

            document.getElementById("result").innerText = "";
            render();
        }

        restart();
    </script>
</body>
</html>