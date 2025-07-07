/*
    Dack's DND Tools - js/dice.js
    =============================
    This file contains all the JavaScript logic for the Dice Roller page.
    It handles adding dice to a "roll queue", calculating the results,
    and displaying a history of past rolls.
*/

/**
 * Main initialization function for the dice roller.
 */
const initDiceRoller = () => {
    // --- SECTION 1: ELEMENT SELECTION & INITIALIZATION ---
    // Find all necessary elements in the DOM.
    const diceRollerPage = document.getElementById('dice-roller-tool');
    const rollDisplay = document.getElementById('rollDisplay');
    const resultPanel = document.getElementById('resultPanel');
    const resultTotal = document.getElementById('resultTotal');
    const resultBreakdown = document.getElementById('resultBreakdown');
    const historyLog = document.getElementById('historyLog');
    const rollButton = document.getElementById('rollButton');
    const clearRoll = document.getElementById('clearRoll');
    const clearHistory = document.getElementById('clearHistory');
    const modPlus = document.getElementById('mod-plus');
    const modMinus = document.getElementById('mod-minus');
    const modValueEl = document.getElementById('mod-value');

    // Abort if the main container or other critical elements don't exist.
    if (!diceRollerPage || !rollDisplay || !resultPanel || !resultTotal || !resultBreakdown || !historyLog) {
        return;
    }

    // Initialize the state for the dice roller.
    let rollQueue = [];
    let modifier = 0;

    // --- SECTION 2: CORE LOGIC & DOM MANIPULATION ---

    /**
     * Updates the "Current Roll" display with the dice in the queue and the modifier.
     */
    const updateRollDisplay = () => {
        if (rollQueue.length === 0 && modifier === 0) {
            rollDisplay.innerHTML = '<span class="text-gray-500 text-lg">Click dice to add...</span>';
            if (modValueEl) modValueEl.textContent = modifier;
            return;
        }

        // Group dice by type (e.g., 2d6, 1d20).
        const diceGroups = rollQueue.reduce((acc, die) => {
            acc[die.sides] = (acc[die.sides] || 0) + 1;
            return acc;
        }, {});

        const groupStrings = Object.entries(diceGroups).map(([sides, count]) => `<span class="roll-queue-item">${count}d${sides}</span>`);
        let modString = '';
        if (modifier > 0) modString = ` + ${modifier}`;
        else if (modifier < 0) modString = ` - ${Math.abs(modifier)}`;

        rollDisplay.innerHTML = groupStrings.join(' + ') + modString;
        if (modValueEl) modValueEl.textContent = modifier;
    };

    /**
     * Executes the roll, calculates the total, and displays the result.
     */
    const executeRoll = () => {
        if (rollQueue.length === 0) return;

        let total = 0;
        let breakdownGroups = [];

        // Group dice to roll them together (e.g., roll all d6's at once).
        const diceGroups = rollQueue.reduce((acc, die) => {
            acc[die.sides] = (acc[die.sides] || 0) + 1;
            return acc;
        }, {});

        Object.entries(diceGroups).forEach(([sides, count]) => {
            let groupRolls = [];
            for (let i = 0; i < count; i++) {
                const roll = Math.floor(Math.random() * sides) + 1;
                groupRolls.push(roll);
                total += roll;
            }
            breakdownGroups.push(`${count}d${sides} [${groupRolls.join(', ')}]`);
        });

        const finalTotal = total + modifier;
        displayResult({
            formula: rollDisplay.textContent,
            breakdown: breakdownGroups.join(' + '),
            finalTotal
        });
    };

    /**
     * Displays the result of a roll in the result panel.
     * @param {object} result The result object containing the final total and breakdown.
     */
    const displayResult = (result) => {
        resultPanel.classList.remove('invisible');
        resultTotal.textContent = result.finalTotal;

        let breakdownText = result.breakdown;
        if (modifier !== 0) {
            const modSign = modifier > 0 ? '+' : '-';
            breakdownText += ` ${modSign} ${Math.abs(modifier)}`;
        }
        resultBreakdown.textContent = breakdownText;
        addToHistory(result);
    };

    /**
     * Adds the result of a roll to the top of the history log.
     * @param {object} result The result object to add to history.
     */
    const addToHistory = (result) => {
        const historyItem = document.createElement('div');
        historyItem.className = 'panel p-3 text-sm';
        historyItem.innerHTML = `<div class="flex justify-between items-center"><span class="font-semibold text-light title-font text-lg">${result.formula}</span><span class="text-2xl font-bold title-font" style="color: var(--accent-red);">${result.finalTotal}</span></div><div class="text-xs text-muted mt-1">Breakdown: ${result.breakdown}</div>`;
        // Prepend adds the new roll to the top of the history.
        historyLog.prepend(historyItem);
    };

    // --- SECTION 3: EVENT LISTENERS ---

    // Add event listeners to all the dice buttons/images.
    const diceButtons = diceRollerPage.querySelectorAll('.dice-btn');
    diceButtons.forEach(button => button.addEventListener('click', () => {
        rollQueue.push({ sides: parseInt(button.dataset.sides, 10) });
        updateRollDisplay();
    }));

    // Add listeners for all the control buttons if they exist.
    if (modPlus) modPlus.addEventListener('click', () => { if (modifier < 99) modifier++; updateRollDisplay(); });
    if (modMinus) modMinus.addEventListener('click', () => { if (modifier > -99) modifier--; updateRollDisplay(); });
    if (clearRoll) clearRoll.addEventListener('click', () => { rollQueue = []; modifier = 0; updateRollDisplay(); });
    if (rollButton) rollButton.addEventListener('click', executeRoll);
    if (clearHistory) clearHistory.addEventListener('click', () => { historyLog.innerHTML = ''; });

    // --- SECTION 4: INITIAL RENDER ---
    // Update the display to show the initial empty state.
    updateRollDisplay();
};

/**
 * Main execution block.
 * Waits for the DOM to be fully loaded, then initializes the dice roller script.
 */
document.addEventListener('DOMContentLoaded', () => {
    initDiceRoller();
});
