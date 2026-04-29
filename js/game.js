let round = 1;
let phase = "playerAttack";

let hand = [];
let enemyHand = [];
let selectedCard = null;
let pendingDamage = 0;

let defenseTimer = null;
let attackTimer = null;
let questionPhase = null;
let inputLocked = false;
let totalCorrectAnswers = 0;
let totalWrongAnswers = 0;
let reactionTimes = [];

let totalWins = 0;
let totalLosses = 0;

let nextCardId = 1;

const cardsDiv = document.getElementById("cards");
const enemyCardsDiv = document.getElementById("enemyCards");
const questionBox = document.getElementById("questionBox");
const questionText = document.getElementById("questionText");
const answerInput = document.getElementById("answerInput");
const turnText = document.getElementById("turnText");
const laser = document.getElementById("laser");
const playerShip = document.getElementById("playerShip");
const enemyShip = document.getElementById("enemyShip");
const enemyHPFill = document.getElementById("enemyHPFill");
const playerHPFill = document.getElementById("playerHPFill");
const enemyHPText = document.getElementById("enemyHPText");
const playerHPText = document.getElementById("playerHPText");

let difficulty = window.GAME_DIFFICULTY || "easy";
let playerMaxHP;
let enemyMaxHP;

if (difficulty === "easy")
{
    playerMaxHP = 40;
    enemyMaxHP = 30;
}
else if (difficulty === "medium")
{
    playerMaxHP = 40;
    enemyMaxHP = 45;
}
else
{
    playerMaxHP = 35;
    enemyMaxHP = 55;
}

let playerHP = playerMaxHP;
let enemyHP = enemyMaxHP;

let animationMode = window.animationMode || "fast";

function toggleAnimationSpeed()
{
    animationMode = (animationMode === "fast") ? "slow" : "fast";
    window.animationMode = animationMode;
    localStorage.setItem("animMode", animationMode);

    const el = document.getElementById("animModeText");
    if (el) el.innerText = animationMode.toUpperCase();
}

function lockInput()
{
    inputLocked = true;
}

function unlockInput()
{
    inputLocked = false;
}


function isQuestionActive()
{
    return questionBox.style.display !== "none" && questionBox.style.display !== "";
}

function goIndex()
{
    window.location.href = "index.php";
}

function goDashboard()
{
    window.location.href = "student_dashboard.php";
}

function generateRandomCard()
{
    let cardType = Math.random() < 0.5 ? "attack" : "defense";
    let op;

    if (difficulty === "easy")
    {
        op = cardType === "attack" ? "+" : "-";
    }
    else
    {
        if (cardType === "attack")
            op = Math.random() < 0.5 ? "+" : "*";
        else
            op = Math.random() < 0.5 ? "-" : "/";
    }

    let value;
    let rarity;

    if (op === "*" || op === "/")
    {
        value = 1 + Math.random() * 3;
        value = parseFloat(value.toFixed(2));
        value = parseFloat(value.toFixed(1));

        if (value < 1) value = 1.0;
    }
    else
    {
        value = Math.floor(Math.random() * 5) + 1;
    }

    if (difficulty === "easy")
    {
        rarity = 1;
    }
    else if (difficulty === "medium")
    {
        rarity = Math.random() < 0.7 ? 1 : 2;
    }
    else
    {
        rarity = Math.random() < 0.5 ? 2 : 3;
    }

    return {
        id: nextCardId++,
        type: cardType,
        op: op,
        value: value,
        rarity: rarity
    };
}


function drawCardToHand()
{
    let card = generateRandomCard();

    if (difficulty === "hard")
    {
        if (card.type === "attack" && card.value > 1)
        {
            card.value = typeof card.value === "number" ? card.value - 0.5 : parseFloat(card.value) - 0.5;
        }

        if (card.rarity > 1)
        {
            card.rarity -= 1;
        }
    }

    hand.push(card);
}


function drawCardToEnemy()
{
    let card = generateRandomCard();

    if (difficulty === "hard")
    {
        if (card.type === "attack")
        {
            card.value = typeof card.value === "number" ? card.value + 1 : parseFloat(card.value) + 1;
        }

        if (card.rarity < 2)
        {
            card.rarity = 2;
        }
    }

    enemyHand.push(card);
}

function startGame()
{
    for (let i = 0; i < 5; i++)
    {
        drawCardToHand();
        drawCardToEnemy();
    }
    updateHPBars();
    phase = "playerAttack";
    turnText.innerText = "Your Turn: Choose an Attack Card (30s)";
    renderHand();
    renderEnemyHand();
    startAttackTimer();
}

function renderHand()
{
    cardsDiv.innerHTML = "";

    hand.forEach(card =>
    {
        const div = document.createElement("div");
        div.className = "card";

        if (card.type === "attack")
        {
            div.classList.add("card-attack");
        }
        else
        {
            div.classList.add("card-defense");
        }

        div.classList.add(`rarity-${card.rarity}`);

        let usable = false;
        if (phase === "playerAttack" && card.type === "attack") usable = true;
        if (phase === "playerDefense" && card.type === "defense") usable = true;

        if (!usable || isQuestionActive())
        {
            div.classList.add("disabled");
        }
        else
        {
            div.onclick = () => selectCard(card.id);
        }

        const header = document.createElement("div");
        header.className = "card-header";
        header.innerText = card.type === "attack" ? "Attack Card" : "Defense Card";

        const opDiv = document.createElement("div");
        opDiv.className = "card-op";
        opDiv.innerText = `${card.op}${Number(card.value.toFixed(1))}`;

        const typeDiv = document.createElement("div");
        typeDiv.className = "card-type";
        typeDiv.innerText = card.type === "attack" ? "Damage" : "Damage Reduction";

        const rarityDiv = document.createElement("div");
        rarityDiv.className = "rarity-stars";
        rarityDiv.innerText = "★".repeat(card.rarity);

        const footer = document.createElement("div");
        footer.className = "card-footer";

        if (card.type === "attack")
        {
            footer.innerText = card.op === "+" ? "Adds this value as damage." : "Boosts your attack damage.";
        }
        else
        {
            footer.innerText = card.op === "-" ? "Subtracts this from incoming damage." : "Divides incoming damage.";
        }

        div.appendChild(header);
        div.appendChild(opDiv);
        div.appendChild(typeDiv);
        div.appendChild(rarityDiv);
        div.appendChild(footer);

        cardsDiv.appendChild(div);
    });

    const skipDiv = document.createElement("div");
    skipDiv.className = "card card-skip rarity-1";

    const skipHeader = document.createElement("div");
    skipHeader.className = "card-header";
    skipHeader.innerText = phase === "playerAttack" ? "Skip Attack" : "Skip Defense";

    const skipOp = document.createElement("div");
    skipOp.className = "card-op";
    skipOp.innerText = "⏭";

    const skipType = document.createElement("div");
    skipType.className = "card-type";
    skipType.innerText = "Skip this phase";

    const skipStars = document.createElement("div");
    skipStars.className = "rarity-stars";
    skipStars.innerText = "★";

    const skipFooter = document.createElement("div");
    skipFooter.className = "card-footer";
    skipFooter.innerText = phase === "playerAttack" ? "End your turn without attacking." : "Take full damage without defending.";

    if (isQuestionActive())
    {
        skipDiv.classList.add("disabled");
    }
    else
    {
        skipDiv.onclick = () =>
        {
            if (phase === "playerAttack") skipAttack();
            else skipDefense();
        };
    }

    skipDiv.appendChild(skipHeader);
    skipDiv.appendChild(skipOp);
    skipDiv.appendChild(skipType);
    skipDiv.appendChild(skipStars);
    skipDiv.appendChild(skipFooter);

    cardsDiv.appendChild(skipDiv);
}

function renderEnemyHand()
{
    enemyCardsDiv.innerHTML = "";

    enemyHand.forEach(card =>
    {
        const div = document.createElement("div");
        div.className = "enemyCard";
        if (card.type === "attack")
        {
            div.classList.add("card-attack");
        }
        else
        {
            div.classList.add("card-defense");
        }

        div.classList.add(`rarity-${card.rarity}`);

        const header = document.createElement("div");
        header.className = "card-header";
        header.innerText = "Enemy Card";

        const opDiv = document.createElement("div");
        opDiv.className = "card-op";
        opDiv.innerText = "?";

        const typeDiv = document.createElement("div");
        typeDiv.className = "card-type";
        typeDiv.innerText = "Unknown";

        const rarityDiv = document.createElement("div");
        rarityDiv.className = "rarity-stars";
        rarityDiv.innerText = "★".repeat(card.rarity);

        const footer = document.createElement("div");
        footer.className = "card-footer";
        footer.innerText = "Hidden until played.";

        div.appendChild(header);
        div.appendChild(opDiv);
        div.appendChild(typeDiv);
        div.appendChild(rarityDiv);
        div.appendChild(footer);

        enemyCardsDiv.appendChild(div);
    });
}

function selectCard(cardId)
{
    if (inputLocked) return;
    if (isQuestionActive()) return;

    const card = hand.find(c => c.id === cardId);
    if (!card) return;

    if (phase === "playerAttack" && card.type !== "attack") return;
    if (phase === "playerDefense" && card.type !== "defense") return;

    selectedCard = card;

    if (phase === "playerAttack")
    {
        clearAttackTimer();
        generateQuestionForCard(card, "attack");
    }
    else if (phase === "playerDefense")
    {
        clearDefenseTimer();
        generateQuestionForCard(card, "defense");
    }
}


function generateQuestionForCard(card, mode)
{
    questionPhase = mode;

    if (mode === "attack")
    {
        const a = round;
        const b = card.value;

        let correct = 0;
        let text = "";

        switch (card.op)
        {
            case "+":
                text = `${a} + ${b}`;
                correct = a + b;
                break;

            case "-":
                text = `${a} - ${b}`;
                correct = a - b;
                break;

            case "*":
                text = `${a} × ${Number(b.toFixed(1))}`;
                correct = parseFloat((a * b).toFixed(1));
                break;

            case "/":
                text = `${a} ÷ ${Number(b.toFixed(1))}`;
                correct = parseFloat((a / b).toFixed(1));
                break;
        }

        card.computedAnswer = correct;
        questionText.innerText = text;
        answerInput.value = "";
        answerInput.dataset.correct = correct;

        questionStartTime = Date.now();
        showQuestionPopup();
        return;
    }

    const enemyDamage = pendingDamage;
    const b = card.value;

    let correct = 0;
    let text = "";

    switch (card.op)
    {
        case "-":
            text = `${enemyDamage} - ${b}`;
            correct = enemyDamage - b;
            break;

        case "/":
            text = `${enemyDamage} ÷ ${Number(b.toFixed(1))}`;
            correct = parseFloat((enemyDamage / b).toFixed(1));
            break;
    }

    card.computedAnswer = correct;
    questionText.innerText = text;
    answerInput.value = "";
    answerInput.dataset.correct = correct;

    questionStartTime = Date.now();
    showQuestionPopup();
}


function showQuestionPopup()
{
    questionBox.style.display = "flex";
    answerInput.focus();
}

function submitAnswer()
{
    if (inputLocked) return;
    lockInput();

    const correct = Number(answerInput.dataset.correct);
    const answer = Number(answerInput.value);

    questionBox.style.display = "none";

    reactionTimes.push(Date.now() - questionStartTime);

    let isCorrect = false;

    if (Number.isNaN(correct) || Number.isNaN(answer))
    {
        isCorrect = false;
    }
    else
    {
        const epsilon = 0.001;
        isCorrect = Math.abs(answer - correct) < epsilon;
    }

    if (isCorrect)
    {
        totalCorrectAnswers++;
    }
    else
    {
        totalWrongAnswers++;
    }

    const card = selectedCard;
    const phaseCopy = questionPhase;

    selectedCard = null;
    questionPhase = null;

    if (!card)
    {
        unlockInput();
        return;
    }

    if (isCorrect)
    {
        if (phaseCopy === "attack") resolveAttackWithCard(card);
        else resolveDefenseWithCard(card);
    }
    else
    {
        if (phaseCopy === "attack") failAttackCard();
        else failDefenseCard();
    }
}

function resolveAttackWithCard(card)
{
    const attackerCardEl = [...document.querySelectorAll("#cards .card")].find(el => el.innerText.includes(card.op + card.value));

    const enemyDefenseCard = enemyChooseDefenseCard();

    let defenderCardEl = null;
    let defenderValue = 0;
    let enemySkipped = false;

    if (enemyDefenseCard)
    {
        defenderValue = enemyDefenseCard.value;
        defenderCardEl = document.querySelector("#enemyCards .enemyCard");
        enemySkipped = false;
    }
    else
    {
        defenderCardEl = null;
        defenderValue = 0;
        enemySkipped = true;
    }

    let dmg = card.computedAnswer;

    if (enemyDefenseCard)
    {
        if (enemyDefenseCard.op === "-") dmg -= enemyDefenseCard.value;
        if (enemyDefenseCard.op === "/") dmg = Math.floor(dmg / enemyDefenseCard.value);
        if (dmg < 0) dmg = 0;
    }

    animateAttack(attackerCardEl, defenderCardEl, true, card.computedAnswer, defenderValue, enemySkipped, card.op, enemyDefenseCard ? enemyDefenseCard.op : null, () =>
    {
        enemyHP -= dmg;
        if (enemyHP < 0) enemyHP = 0;

        hand = hand.filter(c => c.id !== card.id);
        drawCardToHand();

        updateHPBars();
        renderHand();
        renderEnemyHand();

        if (enemyHP <= 0)
        {
            endGame(true);
            clearAttackTimer();
            clearDefenseTimer();
            return;
        }
        enemyAttack();
        unlockInput();
    });
}

function resolveDefenseWithCard(card)
{
    const defenderCardEl = [...document.querySelectorAll("#cards .card")].find(el => el.innerText.includes(card.op + card.value));
    const attackerCardEl = document.querySelector("#enemyCards .enemyCard");
    const attackerValue = pendingDamage;
    const defenderValue = card.value;

    animateAttack(attackerCardEl, defenderCardEl, false, attackerValue, defenderValue, false, null, card.op, () =>
    {
        let dmg = pendingDamage;

        if (card.op === "-") dmg -= card.value;
        if (card.op === "/") dmg = Math.floor(dmg / card.value);
        if (dmg < 0) dmg = 0;

        applyDamageToPlayer(dmg);

        hand = hand.filter(c => c.id !== card.id);
        drawCardToHand();

        renderHand();
        renderEnemyHand();

        if (playerHP <= 0) return;

        phase = "playerAttack";
        renderHand();
        turnText.innerText = "Your Turn: Choose an Attack Card (30s)";
        startAttackTimer();
        unlockInput();
    });
}

function failAttackCard()
{
    showWrongAnswer();

    hand = hand.filter(c => c.id !== selectedCard.id);
    drawCardToHand();
    renderHand();
    renderEnemyHand();
    unlockInput();
    enemyAttack();
}

function failDefenseCard()
{
    lockInput();
    showWrongAnswer();

    setTimeout(() =>
    {
        applyDamageToPlayer(pendingDamage);

        if (playerHP <= 0)
        {
            unlockInput();
            return;
        }

        phase = "playerAttack";
        renderHand();
        turnText.innerText = "Your Turn: Choose an Attack Card (30s)";

        unlockInput();
        startAttackTimer();
    }, 1200);
}


function enemyAttack()
{
    phase = "playerDefense";
    renderHand();
    drawCardToEnemy();
    renderEnemyHand();

    let enemyAttackCard = enemyHand.find(c => c.type === "attack");

    if (!enemyAttackCard)
    {
        pendingDamage = 0;
        phase = "playerAttack";
        renderHand();
        renderEnemyHand();
        turnText.innerText = "Your Turn: Choose an Attack Card (30s)";
        startAttackTimer();
        return;
    }

    enemyHand = enemyHand.filter(c => c.id !== enemyAttackCard.id);
    renderEnemyHand();

    if (enemyAttackCard.op === "+")
    {
        pendingDamage = round + enemyAttackCard.value;
    }
    else
    {
        pendingDamage = round * enemyAttackCard.value;
    }

    turnText.innerText = "Enemy is attacking! Defend (30s)";
    renderEnemyHand();
    startDefenseTimer();
    unlockInput();
}

function skipAttack()
{
    if (inputLocked) return;
    lockInput();
    clearAttackTimer();
    enemyAttack();
}

function skipDefense()
{
        if (inputLocked) return;
    lockInput();
    clearDefenseTimer();

    animateEnemySkipAttack(pendingDamage, () =>
    {
        applyDamageToPlayer(pendingDamage);
        renderHand();
        renderEnemyHand();

        if (playerHP <= 0) return;

        phase = "playerAttack";
        renderHand();
        turnText.innerText = "Your Turn: Choose an Attack Card (30s)";
        startAttackTimer();
        unlockInput();
    });
}

function applyDamageToPlayer(dmg)
{
    playerHP -= dmg;

    if (playerHP < 0) playerHP = 0;
    updateHPBars();

    if (playerHP <= 0)
    {
        endGame(false);
        clearAttackTimer();
        clearDefenseTimer();
    }
    else
    {
        round++;
    }
}

function updateHPBars()
{
    enemyHPFill.style.width = (enemyHP / enemyMaxHP) * 100 + "%";
    playerHPFill.style.width = (playerHP / playerMaxHP) * 100 + "%";

    enemyHPText.innerText = enemyHP + " / " + enemyMaxHP;
    playerHPText.innerText = playerHP + " / " + playerMaxHP;
}

function startAttackTimer()
{
    clearAttackTimer();
    let t = 30;
    turnText.innerText = `Your Turn: Choose an Attack Card (${t}s)`;

    attackTimer = setInterval(() =>
    {
        t--;

        if (t >= 0)
        {
            turnText.innerText = `Your Turn: Choose an Attack Card (${t}s)`;
        }
        if (t < 0)
        {
            clearAttackTimer();
            if (!isQuestionActive()) skipAttack();
        }
    }, 1000);
}

function startDefenseTimer()
{
    clearDefenseTimer();
    let t = 30;
    turnText.innerText = `Enemy Attack: Defend (${t}s)`;

    defenseTimer = setInterval(() =>
    {
        t--;
        if (t >= 0)
        {
            turnText.innerText = `Enemy Attack: Defend (${t}s)`;
        }
        if (t < 0)
        {
            clearDefenseTimer();
            if (!isQuestionActive()) skipDefense();
        }
    }, 1000);
}

function clearAttackTimer()
{
    if (attackTimer)
    {
        clearInterval(attackTimer);
        attackTimer = null;
    }
}

function clearDefenseTimer()
{
    if (defenseTimer)
    {
        clearInterval(defenseTimer);
        defenseTimer = null;
    }
}

function animateAttack(attackerCardEl, defenderCardEl, attackerIsPlayer, attackerValue, defenderValue, enemySkipped, attackerOp, defenderOp, callback)
{
    const attackerClone = createCloneAt(attackerCardEl, attackerIsPlayer, attackerValue, attackerOp);
    let defenderClone = null;

    if (!enemySkipped && defenderCardEl)
    {
        defenderClone = createCloneAt(defenderCardEl, !attackerIsPlayer, defenderValue, defenderOp);
    }

    const attackerShip = attackerIsPlayer ? playerShip : enemyShip;
    const defenderShip = attackerIsPlayer ? enemyShip : playerShip;

    const attackerFront = getShipFront(attackerShip, !attackerIsPlayer);
    const defenderFront = getShipFront(defenderShip, attackerIsPlayer);

    if (animationMode === "fast")
    {
        setTimeout(() =>
        {
            attackerClone.style.left = attackerFront.x + "px";
            attackerClone.style.top = attackerFront.y + "px";

            if (defenderClone)
            {
                defenderClone.style.left = defenderFront.x + "px";
                defenderClone.style.top = defenderFront.y + "px";
            }
        }, 50);

        setTimeout(() =>
        {
            attackerClone.classList.add("chargeUp");
            if (defenderClone) defenderClone.classList.add("chargeUp");
        }, 500);

        setTimeout(() =>
        {
            attackerClone.classList.add("superGlow");
            if (defenderClone) defenderClone.classList.add("superGlow");
        }, 1200);

        setTimeout(() =>
        {
            attackerClone.classList.remove("chargeUp", "superGlow");
            if (defenderClone) defenderClone.classList.remove("chargeUp", "superGlow");

            attackerClone.classList.add("fly");
            if (defenderClone) defenderClone.classList.add("fly");

            attackerClone.style.left = defenderFront.x + "px";
            attackerClone.style.top = defenderFront.y + "px";

            if (defenderClone)
            {
                defenderClone.style.left = attackerFront.x + "px";
                defenderClone.style.top = attackerFront.y + "px";
            }
        }, 1300);

        setTimeout(() =>
        {
            if (attackerValue < defenderValue)
            {
                attackerClone.classList.add("defenderHit");

                if (attackerIsPlayer)
                {
                    attackerClone.style.left = attackerFront.x - 200 + "px";
                    attackerClone.style.top = attackerFront.y + 150 + "px";
                }
                else
                {
                    attackerClone.style.left = attackerFront.x + 200 + "px";
                    attackerClone.style.top = attackerFront.y - 150 + "px";
                }

                setTimeout(() => attackerClone.style.opacity = "0", 200);

                setTimeout(() =>
                {
                    if (defenderClone)
                        defenderClone.style.opacity = "0";
                }, 300);

                setTimeout(() =>
                {
                    attackerClone.remove();
                    if (defenderClone) defenderClone.remove();
                    callback();
                }, 1500);

                return;
            }

            if (attackerValue === defenderValue)
            {
                attackerClone.classList.add("defenderHit");
                attackerClone.style.opacity = "0";

                if (defenderClone)
                {
                    defenderClone.classList.add("defenderHit");
                    defenderClone.style.opacity = "0";
                }

                setTimeout(() =>
                {
                    attackerClone.remove();
                    if (defenderClone) defenderClone.remove();
                    callback();
                }, 800);

                return;
            }

            if (defenderClone)
            {
                defenderClone.classList.add("defenderHit");

                if (attackerIsPlayer)
                {
                    defenderClone.style.left = defenderFront.x + 200 + "px";
                    defenderClone.style.top = defenderFront.y - 150 + "px";
                }
                else
                {
                    defenderClone.style.left = defenderFront.x - 200 + "px";
                    defenderClone.style.top = defenderFront.y + 150 + "px";
                }

                defenderClone.style.opacity = "0";
            }

        }, 1400);

        setTimeout(() =>
        {
            if (attackerValue <= defenderValue) return;

            const targetRect = defenderShip.getBoundingClientRect();

            attackerClone.style.left = targetRect.left + targetRect.width/2 - 27 + "px";
            attackerClone.style.top = targetRect.top + targetRect.height/2 - 40 + "px";
            attackerClone.style.opacity = "0";

            defenderShip.classList.add("shipFlash");
            setTimeout(() => defenderShip.classList.remove("shipFlash"), 300);
        }, 1900);

        setTimeout(() =>
        {
            attackerClone.remove();
            if (defenderClone) defenderClone.remove();
            callback();
        }, 2600);

        return;
    }


    if (animationMode === "slow")
    {
        const centerX = (attackerFront.x + defenderFront.x) / 2;
        const centerY = (attackerFront.y + defenderFront.y) / 2;

        const attackerStart = { x: attackerFront.x, y: attackerFront.y };
        const defenderStart = { x: defenderFront.x, y: defenderFront.y };

        const clashOffset = 35;

        const attackerOffsetY = attackerIsPlayer ? clashOffset : -clashOffset;
        const defenderOffsetY = -attackerOffsetY;

        setTimeout(() =>
        {
            attackerClone.style.left = attackerStart.x + "px";
            attackerClone.style.top = attackerStart.y + "px";

            if (defenderClone)
            {
                defenderClone.style.left = defenderStart.x + "px";
                defenderClone.style.top = defenderStart.y + "px";
            }
        }, 100);

        setTimeout(() =>
        {
            attackerClone.style.left = centerX + "px";
            attackerClone.style.top = (centerY + attackerOffsetY) + "px";

            if (defenderClone)
            {
                defenderClone.style.left = centerX + "px";
                defenderClone.style.top = (centerY + defenderOffsetY) + "px";
            }
        }, 900);

        setTimeout(() =>
        {
            let atk = attackerValue;
            let def = defenderValue;

            const atkText = attackerClone.querySelector(".cardValue");
            const defText = defenderClone ? defenderClone.querySelector(".cardValue") : null;

            const atkOp = attackerClone.dataset.op;
            const defOp = defenderClone ? defenderClone.dataset.op : null;

            const drain = setInterval(() =>
            {
                atk = parseFloat((atk - 0.05).toFixed(2));
                if (defenderClone) def = parseFloat((def - 0.05).toFixed(2));


                if (atkText)
                {
                    atkText.innerText =
                        atkOp === "-" || atkOp === "/" ? `${atkOp}${atk.toFixed(1)}` : atk.toFixed(1);
                }

                if (defText)
                {
                    defText.innerText =
                        defOp === "-" || defOp === "/" ? `${defOp}${def.toFixed(1)}` : def.toFixed(1);
                }

                const shake = (Math.random() - 0.5) * 4;
                attackerClone.style.transform = `translateY(${shake}px)`;
                if (defenderClone)
                {
                    defenderClone.style.transform = `translateY(${-shake}px)`;
                }

                if (atk <= 0 && def > 0)
                {
                    clearInterval(drain);
                    attackerClone.classList.add("defenderHit");
                    attackerClone.style.opacity = "0";

                    setTimeout(() =>
                    {
                        if (defenderClone)
                        {
                            defenderClone.classList.add("defenderHit");
                            defenderClone.style.opacity = "0";
                        }

                        setTimeout(() =>
                        {
                            attackerClone.remove();
                            if (defenderClone) defenderClone.remove();
                            callback();
                        }, 800);

                    }, 1200);

                    return;
                }

                if (atk <= 0 && def <= 0)
                {
                    clearInterval(drain);

                    attackerClone.classList.add("defenderHit");
                    attackerClone.style.opacity = "0";

                    if (defenderClone)
                    {
                        defenderClone.classList.add("defenderHit");
                        defenderClone.style.opacity = "0";
                    }

                    setTimeout(() =>
                    {
                        attackerClone.remove();
                        if (defenderClone) defenderClone.remove();
                        callback();
                    }, 800);

                    return;
                }

                if (!defenderClone || def <= 0)
                {
                    clearInterval(drain);

                    attackerClone.style.transform = "none";

                    if (defenderClone)
                    {
                        defenderClone.classList.add("defenderHit");

                        if (attackerIsPlayer)
                        {
                            defenderClone.style.left = defenderFront.x + 200 + "px";
                            defenderClone.style.top = defenderFront.y - 150 + "px";
                        }
                        else
                        {
                            defenderClone.style.left = defenderFront.x - 200 + "px";
                            defenderClone.style.top = defenderFront.y + 150 + "px";
                        }

                        setTimeout(() =>
                        {
                            defenderClone.style.opacity = "0";
                        }, 200);
                    }

                    setTimeout(() =>
                    {
                        attackerClone.style.transition = "left 0.15s linear, top 0.15s linear";
                        attackerClone.style.left = defenderFront.x + "px";
                        attackerClone.style.top = defenderFront.y + "px";

                        setTimeout(() =>
                        {
                            attackerClone.style.transition = "";
                        }, 200);

                    }, 300);

                    setTimeout(() =>
                    {
                        const targetRect = defenderShip.getBoundingClientRect();

                        attackerClone.style.left =
                            targetRect.left + targetRect.width / 2 - 27 + "px";

                        attackerClone.style.top =
                            targetRect.top + targetRect.height / 2 - 40 + "px";

                        attackerClone.style.opacity = "0";

                        defenderShip.classList.add("shipFlash");
                        setTimeout(() => defenderShip.classList.remove("shipFlash"), 300);
                    }, 500);

                    setTimeout(() =>
                    {
                        attackerClone.remove();
                        if (defenderClone) defenderClone.remove();
                        callback();
                    }, 1400);

                    return;
                }
            }, 40);
        }, 1200);
        return;
    }
}

function animateEnemySkipAttack(attackerValue, callback)
{
    const attackerCardEl = document.querySelector("#enemyCards .enemyCard");
    if (!attackerCardEl)
    {
        callback();
        return;
    }

    const op = attackerCardEl.dataset.op || "";
    const attackerClone = createCloneAt(attackerCardEl, false, attackerValue, op);

    const attackerFront = getShipFront(enemyShip, false);
    const defenderFront = getShipFront(playerShip, true);

    setTimeout(() =>
    {
        attackerClone.style.left = attackerFront.x + "px";
        attackerClone.style.top = attackerFront.y + "px";
    }, 50);

    setTimeout(() =>
    {
        attackerClone.classList.add("chargeUp");
    }, 500);

    setTimeout(() =>
    {
        attackerClone.classList.add("superGlow");
    }, 1200);

    setTimeout(() =>
    {
        attackerClone.classList.remove("chargeUp", "superGlow");
        attackerClone.classList.add("fly");
        attackerClone.style.left = defenderFront.x + "px";
        attackerClone.style.top = defenderFront.y + "px";
    }, 1300);

    setTimeout(() =>
    {
        const targetRect = playerShip.getBoundingClientRect();
        attackerClone.style.left = targetRect.left + targetRect.width / 2 - 27 + "px";
        attackerClone.style.top = targetRect.top + targetRect.height / 2 - 40 + "px";
        attackerClone.style.opacity = "0";

        playerShip.classList.add("shipFlash");
        setTimeout(() => playerShip.classList.remove("shipFlash"), 300);
    }, 1700);

    setTimeout(() =>
    {
        attackerClone.remove();
        callback();
    }, 2300);
}

function createCloneAt(cardEl, isPlayer, value, op)
{
    const rect = cardEl.getBoundingClientRect();

    const clone = document.createElement("div");
    clone.className = "cardClone " + (isPlayer ? "playerGlow" : "enemyGlow");

    clone.style.left = rect.left + "px";
    clone.style.top = rect.top + "px";

    op = op || "";

    clone.dataset.op = op;

    clone.innerHTML = `<div class="cardValue">${op}${value}</div>`;

    document.body.appendChild(clone);
    return clone;
}


function getBattleCenter()
{
    const field = document.getElementById("battlefield").getBoundingClientRect();
    return {
        x: field.left + field.width / 2 - 27,
        y: field.top + field.height / 2 - 40
    };
}


function getShipFront(shipEl, isEnemy)
{
    const rect = shipEl.getBoundingClientRect();

    return {
        x: rect.left + rect.width / 2 - 27,
        y: isEnemy ? rect.bottom - 30 : rect.top - 40
    };
}

function getCardValueFromElement(el)
{
    if (!el) return 0;
    const text = el.innerText.trim().split("\n")[0];
    return parseInt(text.replace(/[^0-9-]/g, ""));
}

function enemyChooseDefenseCard()
{
    let defenseCard = enemyHand.find(c => c.type === "defense");

    if (!defenseCard) return null;

    enemyHand = enemyHand.filter(c => c.id !== defenseCard.id);

    return defenseCard;
}

function showWrongAnswer()
{
    const box = document.getElementById("wrongAnswerBox");
    box.style.display = "block";
    box.style.animation = "none";
    void box.offsetWidth;
    box.style.animation = "wrongFlash 0.2s ease-out forwards";

    setTimeout(() =>
    {
        box.style.display = "none";
    }, 1200);
}

function endGame(win)
{
    if (win) totalWins++;
    else totalLosses++;

    sendStatsToServer(win);
    showEndPopup(win);
}


function showEndPopup(win)
{
    document.getElementById("gameEndTitle").innerText = win ? "YOU WIN!" : "YOU LOSE!";
    document.getElementById("gameEndMessage").innerText = win ? "Great job! Want to play again?" : "Don't worry, try again!";

    document.getElementById("gameEndPopup").style.display = "flex";
}

function playAgain()
{
    window.location.reload();
}

function returnToDashboard()
{
    window.location.href = "student_dashboard.php";
}

function getAverageReaction()
{
    if (reactionTimes.length === 0) return 0;
    return Math.round(reactionTimes.reduce((a,b)=>a+b) / reactionTimes.length);
}

function sendStatsToServer(win)
{
    fetch("update_stats.php",
    {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            won: win,
            correct: totalCorrectAnswers,
            wrong: totalWrongAnswers,
            reaction: getAverageReaction(),
            wins: totalWins,
            losses: totalLosses
        })
    });
}

window.addEventListener("load", () =>
{
    setInterval(() =>
    {
        if (!isQuestionActive() && inputLocked)
        {
            unlockInput();
        }
    }, 2000);

    startGame();
});
