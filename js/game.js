let playerHP = 30;
let enemyHP = 30;

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


function generateRandomCard()
{
    const rollType = Math.random();
    let cardType;
    let op;

    if (rollType < 0.55)
    {
        cardType = "attack";
        const rollOp = Math.random();
        if (rollOp < 0.75) op = "+";
        else op = "*";
    }
    else
    {
        cardType = "defense";
        const rollOp = Math.random();
        if (rollOp < 0.75) op = "-";
        else op = "/";
    }

    let value;
    let rarity;

    if (op === "+" || op === "-")
    {
        const r = Math.random();
        if (r < 0.4) { value = 1; rarity = 1; }
        else if (r < 0.7) { value = 2; rarity = 1; }
        else if (r < 0.88) { value = 3; rarity = 2; }
        else if (r < 0.97) { value = 4; rarity = 2; }
        else { value = 5; rarity = 3; }
    }
    else
    {
        const r = Math.random();
        if (r < 0.5) { value = 2; rarity = 2; }
        else if (r < 0.8) { value = 3; rarity = 2; }
        else if (r < 0.95) { value = 4; rarity = 3; }
        else { value = 5; rarity = 4; }
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
    hand.push(generateRandomCard());
}

function drawCardToEnemy()
{
    enemyHand.push(generateRandomCard());
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
        opDiv.innerText = `${card.op}${card.value}`;

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
            case "+": text = `${a} + ${b}`; correct = a + b; break;
            case "-": text = `${a} - ${b}`; correct = a - b; break;
            case "*": text = `${a} × ${b}`; correct = a * b; break;
            case "/": text = `${a * b} ÷ ${b}`; correct = a; break;
        }

        card.computedAnswer = correct;
        questionText.innerText = text;
        answerInput.value = "";
        answerInput.dataset.correct = correct;
        showQuestionPopup();
        return;
    }

    const enemyDamage = pendingDamage;
    const b = card.value;

    let correct = 0;
    let text = "";

    if (card.op === "-")
    {
        text = `${enemyDamage} - ${b}`;
        correct = enemyDamage - b;
    }

    if (card.op === "/")
    {
        text = `${enemyDamage} ÷ ${b}`;
        correct = Math.floor(enemyDamage / b);
    }

    if (correct < 0) correct = 0;

    card.computedAnswer = correct;
    questionText.innerText = text;
    answerInput.value = "";
    answerInput.dataset.correct = correct;

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

    if (!selectedCard || !questionPhase)
    {
        questionPhase = null;
        unlockInput();
        return;
    }

    if (answer === correct)
    {
        if (questionPhase === "attack")
            resolveAttackWithCard(selectedCard);
        else
            resolveDefenseWithCard(selectedCard);
    }
    else
    {
        if (questionPhase === "attack")
            failAttackCard();
        else
            failDefenseCard();
    }

    selectedCard = null;
    questionPhase = null;
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

    animateAttack(attackerCardEl, defenderCardEl, true, card.computedAnswer, defenderValue, enemySkipped, () =>
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
            turnText.innerText = "YOU WIN!";
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

    animateAttack(attackerCardEl, defenderCardEl, false, attackerValue, defenderValue, false, () =>
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
        renderHand();
        renderEnemyHand();

        if (playerHP <= 0) {
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

    pendingDamage = enemyAttackCard.op === "+" ? round + enemyAttackCard.value : round * enemyAttackCard.value;

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
        turnText.innerText = "YOU LOSE!";
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
    enemyHPFill.style.width = (enemyHP / 30) * 100 + "%";
    playerHPFill.style.width = (playerHP / 30) * 100 + "%";

    enemyHPText.innerText = `${enemyHP} / 30`;
    playerHPText.innerText = `${playerHP} / 30`;
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
            skipAttack();
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
            skipDefense();
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

function animateAttack(attackerCardEl, defenderCardEl, attackerIsPlayer, attackerValue, defenderValue, enemySkipped, callback)
{
    const attackerClone = createCloneAt(attackerCardEl, attackerIsPlayer, attackerValue);
    let defenderClone = null;

    if (!enemySkipped)
    {
        defenderClone = createCloneAt(defenderCardEl, !attackerIsPlayer, defenderValue);
    }

    const attackerShip = attackerIsPlayer ? playerShip : enemyShip;
    const defenderShip = attackerIsPlayer ? enemyShip : playerShip;

    const attackerFront = getShipFront(attackerShip, !attackerIsPlayer);
    const defenderFront = getShipFront(defenderShip, attackerIsPlayer);

    // PHASE 1 — move to their ships
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

    // PHASE 2 — CHARGE UP
    setTimeout(() =>
    {
        attackerClone.classList.add("chargeUp");
        if (defenderClone) defenderClone.classList.add("chargeUp");
    }, 500);

    // PHASE 3 — SUPER GLOW
    setTimeout(() =>
    {
        attackerClone.classList.add("superGlow");
        if (defenderClone) defenderClone.classList.add("superGlow");
    }, 1200);

    // PHASE 4 — launch at each other
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

    // PHASE 5 — defender gets flung away
    setTimeout(() => {
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
        }
    }, 1600);

    // PHASE 6 — attacker hits the ship
    setTimeout(() =>
    {
        const targetRect = defenderShip.getBoundingClientRect();
        attackerClone.style.left = targetRect.left + targetRect.width/2 - 27 + "px";
        attackerClone.style.top = targetRect.top + targetRect.height/2 - 40 + "px";

        attackerClone.style.opacity = "0"; // disappear instantly

        defenderShip.classList.add("shipFlash");
        setTimeout(() => defenderShip.classList.remove("shipFlash"), 300);
    }, 1900);

    // PHASE 7 — cleanup
    setTimeout(() =>
    {
        attackerClone.remove();
        if (defenderClone) defenderClone.remove();
        callback();
    }, 2600);
}

function animateEnemySkipAttack(attackerValue, callback)
{
    const attackerCardEl = document.querySelector("#enemyCards .enemyCard");
    if (!attackerCardEl)
    {
        callback();
        return;
    }

    const attackerClone = createCloneAt(attackerCardEl, false, attackerValue);
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

function createCloneAt(cardEl, isPlayer, value)
{
    const rect = cardEl.getBoundingClientRect();

    const clone = document.createElement("div");
    clone.className = "cardClone " + (isPlayer ? "playerGlow" : "enemyGlow");

    clone.style.left = rect.left + "px";
    clone.style.top = rect.top + "px";

    clone.innerHTML = `<div class="cardValue">${value}</div>`;

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




// ---------- GAME ----------

startGame();
