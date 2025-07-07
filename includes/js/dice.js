/*
    Dack's DND Tools - includes/js/dice.js
    ======================================
    This file contains all JavaScript logic for the Dice Roller page. It handles
    adding dice to a "roll queue," calculating the results, and displaying a
    history of past rolls.
*/

// --- SECTION 1: DOMContentLoaded EVENT ---
// This is the main execution block. It runs after the entire page has loaded
// and initializes all the Dice Roller's functionality.
document.addEventListener('DOMContentLoaded', () => {

    // This function sets up the entire dice roller.
    const initDiceRoller = () => {
        
        // --- SECTION 2: ELEMENT SELECTION & INITIALIZATION ---
        // =====================================================
        // Caching all necessary DOM elements at the start for performance.
        const diceRollerPage = document.getElementById('dice-roller-tool');
        if (!diceRollerPage) return; // Exit if not on the dice roller page.

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
        const diceButtons = diceRollerPage.querySelectorAll('.dice-btn');

        // --- SECTION 3: STATE MANAGEMENT ---
        // ===================================
        // Variables to keep track of the current roll being built.
        let rollQueue = [];
        let modifier = 0;

        // --- SECTION 4: CORE LOGIC & DOM MANIPULATION ---
        // ================================================
        // These functions handle the primary logic of the tool.

        /**
         * Updates the "Current Roll" display with the dice in the queue and the modifier.
         */
        const updateRollDisplay = () => {
            if (rollQueue.length === 0 && modifier === 0) {
                rollDisplay.innerHTML = '<span class="text-gray-500 text-lg">Click dice to add...</span>';
                if (modValueEl) modValueEl.textContent = modifier;
                return;
            }
            // Group dice by type (e.g., 2d6, 1d20) for a clean display.
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
         * Adds the result of a roll to the top of the history log.
         * @param {object} result - The result object to add to history.
         */
        const addToHistory = (result) => {
            const historyItem = document.createElement('div');
            historyItem.className = 'panel p-3 text-sm';
            historyItem.innerHTML = `<div class="flex justify-between items-center"><span class="font-semibold text-light title-font text-lg">${result.formula}</span><span class="text-2xl font-bold title-font" style="color: var(--accent-red);">${result.finalTotal}</span></div><div class="text-xs text-muted mt-1">Breakdown: ${result.breakdown}</div>`;
            // Prepending adds the new roll to the top of the history for easy viewing.
            historyLog.prepend(historyItem);
        };

        /**
         * Displays the result of a roll in the main result panel.
         * @param {object} result - The result object containing the final total and breakdown.
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
         * Executes the roll, calculates the total, and displays the result.
         */
        const executeRoll = () => {
            if (rollQueue.length === 0) return;
            let total = 0;
            let breakdownGroups = [];
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

        // --- SECTION 5: EVENT LISTENERS ---
        // ==================================
        // Attaching all event handlers to make the page interactive.

        diceButtons.forEach(button => button.addEventListener('click', () => {
            rollQueue.push({ sides: parseInt(button.dataset.sides, 10) });
            updateRollDisplay();
        }));

        if (modPlus) modPlus.addEventListener('click', () => { if (modifier < 99) modifier++; updateRollDisplay(); });
        if (modMinus) modMinus.addEventListener('click', () => { if (modifier > -99) modifier--; updateRollDisplay(); });
        if (clearRoll) clearRoll.addEventListener('click', () => {
            rollQueue = [];
            modifier = 0;
            updateRollDisplay();
        });
        if (rollButton) rollButton.addEventListener('click', executeRoll);
        if (clearHistory) clearHistory.addEventListener('click', () => { historyLog.innerHTML = ''; });

        // --- SECTION 6: INITIAL RENDER ---
        // =================================
        // Update the display to show the initial empty state.
        updateRollDisplay();
    };

    // Run the main initialization function for the dice roller.
    initDiceRoller();
});
