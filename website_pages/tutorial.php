<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Space Math Tutorial</title>
    <link rel="stylesheet" href="../css/style.css">

    <style>
        #tutorialOverlay
        {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.85);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        #tutorialBox
        {
            background: #111827;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            max-width: 420px;
            box-shadow: 0 0 20px #00eaff;
        }

        .highlight
        {
            outline: 3px solid yellow;
            transform: scale(1.05);
        }

        #tutorialBox .tutorialBtn {
            margin-top: 15px;
            padding: 10px 25px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;

            color: white;
            background: linear-gradient(90deg, #00eaff, #0077ff);

            box-shadow: 0 0 12px rgba(0,255,255,0.6);
            transition: all 0.2s ease;
            animation: glowPulse 2s infinite;
        }

        #tutorialBox .tutorialBtn:hover {
            transform: scale(1.08);
            box-shadow: 0 0 20px rgba(0,255,255,1);
        }

        #tutorialBox .tutorialBtn:active {
            transform: scale(0.95);
            box-shadow: 0 0 8px rgba(0,255,255,0.8);
        }

        @keyframes glowPulse {
            0% { box-shadow: 0 0 10px rgba(0,255,255,0.5); }
            50% { box-shadow: 0 0 20px rgba(0,255,255,1); }
            100% { box-shadow: 0 0 10px rgba(0,255,255,0.5); }
        }
    </style>
</head>

<body>

<div id="spaceBackground">
    <div id="stars1" class="starLayer"></div>
    <div id="stars2" class="starLayer"></div>
    <div id="stars3" class="starLayer"></div>
</div>

<div id="uiWrapper">

    <h1>Tutorial Battle!</h1>
    <h2 id="turnText"></h2>

    <div id="cards"></div>


</div>

<div id="tutorialOverlay">
    <div id="tutorialBox">
        <div id="tutorialMessage"></div>
        <button class="tutorialBtn" onclick="nextStep()">Continue</button>
    </div>
</div>

<script>
let step = 0;

let allowedType = null;

let hand = [
    {id:1, type:"attack", op:"+", value:3, rarity:1},
    {id:2, type:"defense", op:"-", value:2, rarity:1}
];

const cardsDiv = document.getElementById("cards");
const overlay = document.getElementById("tutorialOverlay");
const message = document.getElementById("tutorialMessage");
const questionBox = document.getElementById("questionBox");


function show(msg)
{
    message.innerHTML = msg;
    overlay.style.display = "flex";
}

function nextStep()
{
    overlay.style.display = "none";
    step++;
    render();
}

function render()
{
    renderCards();

    switch(step) {

        case 0:
            show("Welcome!");
            break;

        case 1:
            show("This is your hand.");
            break;

        case 2:
            allowedType = "attack";
            show("Click the ATTACK card (+3).");
            break;

        case 3:
            askQuestion("attack"); // Forced attack
            break;

        case 4:
            show("You attacked the enemy!");
            break;

        case 5:
            allowedType = "defense"; // Force defense
            show("The enemy attacks!");
            break;

        case 6:
            show("Click the DEFENSE card (-2).");
            break;

        case 7:
            askQuestion("defense");
            break;

        case 8:
            show("Enemy attack defended!");
            break;

        case 9:
            show("Tutorial complete!");
            break;

        case 10:
            window.location.href = "game.php";
            break;
    }
}


function renderCards()
{
    cardsDiv.innerHTML = "";

    hand.forEach(card =>
    {
        const div = document.createElement("div");
        div.className = "card";

        if (card.type === "attack") div.classList.add("card-attack");
        else div.classList.add("card-defense");

        div.innerHTML = `
            <div class="card-header">${card.type.toUpperCase()}</div>
            <div class="card-op">${card.op}${card.value}</div>
            <div class="card-type">${card.type === "attack" ? "Damage" : "Defense"}</div>
            <div class="rarity-stars">${"★".repeat(card.rarity)}</div>
        `;

        div.onclick = () => handleClick(card);

        cardsDiv.appendChild(div);
    });
}


function handleClick(card)
{
    if (overlay.style.display === "flex") return;

    if (card.type !== allowedType) return;

    step++;
    render();
}


function askQuestion(type)
{
    const a = 2, b = 3;

    document.getElementById("questionText").innerText =
        (type === "attack" ? "Attack: " : "Defense: ") + `${a} + ${b}`;

    document.getElementById("answerInput").dataset.correct = 5;

    questionBox.style.display = "block";
}

function submitAnswer()
{
    const val = Number(document.getElementById("answerInput").value);
    const correct = Number(document.getElementById("answerInput").dataset.correct);

    if (val !== correct) {
        alert("Wrong!");
        return;
    }

    questionBox.style.display = "none";
    step++;
    render();
}

render();
</script>

</body>
</html>