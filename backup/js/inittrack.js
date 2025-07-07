/*
    Dack's DND Tools - js/inittrack.js
    ==================================
    This file contains all the JavaScript logic for the Initiative Tracker page.
    It handles adding and removing combatants, sorting them by initiative,
    and tracking the current turn in a combat encounter.
*/

/**
 * Main initialization function for the initiative tracker.
 */
const initInitiativeTracker = () => {
    // --- SECTION 1: ELEMENT SELECTION & INITIALIZATION ---
    // Find all necessary elements in the DOM.
    const initiativeTrackerTool = document.getElementById('initiative-tracker-tool');
    const addCombatantForm = document.getElementById('add-combatant-form');
    const nameInput = document.getElementById('combatant-name');
    const initiativeInput = document.getElementById('initiative-roll');
    const initiativeList = document.getElementById('initiative-list');
    const nextTurnBtn = document.getElementById('next-turn-btn');
    const resetBtn = document.getElementById('reset-btn');

    // Abort if the main container or other critical elements don't exist.
    if (!initiativeTrackerTool || !addCombatantForm || !nameInput || !initiativeInput || !initiativeList || !nextTurnBtn || !resetBtn) {
        return;
    }

    // Initialize the state for the combat encounter.
    let combatants = [];
    let currentTurnIndex = -1;

    // --- SECTION 2: HELPER UTILITIES ---

    /**
     * Sanitizes a string to prevent XSS attacks by converting HTML characters.
     * @param {string} str The string to escape.
     * @returns {string} The sanitized string.
     */
    const escapeHTML = (str) => {
        const p = document.createElement('p');
        p.textContent = str;
        return p.innerHTML;
    };

    // --- SECTION 3: CORE LOGIC & DOM MANIPULATION ---

    /**
     * Renders the list of combatants in the DOM, sorted by initiative.
     * Highlights the combatant whose turn it currently is.
     */
    const renderList = () => {
        initiativeList.innerHTML = '';
        if (combatants.length === 0) {
            initiativeList.innerHTML = '<li class="text-muted text-center p-8">Add combatants to begin...</li>';
            return;
        }

        // Sort combatants by initiative in descending order.
        combatants.sort((a, b) => b.initiative - a.initiative);

        combatants.forEach((combatant, index) => {
            const li = document.createElement('li');
            li.className = 'combatant-item';
            // Apply a special class if it's the current combatant's turn.
            if (index === currentTurnIndex) {
                li.classList.add('active-turn');
            }

            // Sanitize user-provided name before inserting it into the HTML.
            li.innerHTML = `
                <span class="combatant-name">${escapeHTML(combatant.name)}</span>
                <span class="initiative-value">${combatant.initiative}</span>
                <button class="remove-combatant-btn text-red-500 hover:text-red-300" data-index="${index}">&times;</button>
            `;
            initiativeList.appendChild(li);
        });
    };

    /**
     * Adds a new combatant to the list from the form inputs.
     * @param {Event} e The form submission event.
     */
    const addCombatant = (e) => {
        e.preventDefault();
        const name = nameInput.value.trim();
        const initiative = parseInt(initiativeInput.value, 10);

        if (name && !isNaN(initiative)) {
            combatants.push({ name, initiative });
            nameInput.value = '';
            initiativeInput.value = '';
            nameInput.focus(); // Set focus back to the name input for quick entry.

            // If this is the first combatant, start the turn order.
            if (currentTurnIndex === -1) {
                currentTurnIndex = 0;
            }
            renderList();
        }
    };

    /**
     * Removes a combatant from the list at a given index.
     * @param {number} index The index of the combatant to remove.
     */
    const removeCombatant = (index) => {
        combatants.splice(index, 1);

        // Adjust the current turn index if the removed combatant was before or at the current turn.
        if (currentTurnIndex >= index && currentTurnIndex > 0) {
            currentTurnIndex--;
        }

        // Reset if the last combatant is removed.
        if (combatants.length === 0) {
            currentTurnIndex = -1;
        }
        renderList();
    };

    /**
     * Advances the turn to the next combatant in the order.
     */
    const nextTurn = () => {
        if (combatants.length > 0) {
            currentTurnIndex = (currentTurnIndex + 1) % combatants.length;
            renderList();
        }
    };

    /**
     * Clears all combatants and resets the tracker to its initial state.
     */
    const resetTracker = () => {
        combatants = [];
        currentTurnIndex = -1;
        renderList();
    };

    // --- SECTION 4: EVENT LISTENERS ---

    // Handle the form submission for adding a new combatant.
    addCombatantForm.addEventListener('submit', addCombatant);

    // Handle clicks for the Next Turn and Reset buttons.
    nextTurnBtn.addEventListener('click', nextTurn);
    resetBtn.addEventListener('click', resetTracker);

    // Use event delegation for the remove buttons to improve performance.
    initiativeList.addEventListener('click', (event) => {
        if (event.target.classList.contains('remove-combatant-btn')) {
            const indexToRemove = parseInt(event.target.dataset.index, 10);
            removeCombatant(indexToRemove);
        }
    });

    // --- SECTION 5: INITIAL RENDER ---
    // Render the initial empty state of the list.
    renderList();
};

/**
 * Main execution block.
 * Waits for the DOM to be fully loaded, then initializes the initiative tracker script.
 */
document.addEventListener('DOMContentLoaded', () => {
    initInitiativeTracker();
});
