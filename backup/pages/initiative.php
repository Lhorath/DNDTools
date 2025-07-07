<!--
    Dack's DND Tools - pages/initiative.php
    =======================================
    This file creates the Initiative Tracker tool. It features a two-column layout
    with a form for adding combatants on the left and the turn order list on the right.
-->

<!-- Main container for the initiative tracker tool, targeted by JavaScript -->
<div id="initiative-tracker-tool" class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        PAGE HEADER
        Contains the main title and a descriptive subtitle for the page.
    -->
    <header class="text-center mb-10">
        <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Initiative Tracker</h1>
        <p class="text-xl text-muted mt-4">Roll for initiative!</p>
    </header>

    <!--
        MAIN CONTENT GRID
        A responsive two-column grid that holds the tool's primary interface.
    -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

        <!--
            LEFT COLUMN: ADD COMBATANT FORM
            Contains the form for adding new characters or monsters to the initiative order.
        -->
        <div class="md:col-span-1">
            <div class="section p-6">
                <h2 class="section-title">Add Combatant</h2>
                <hr class="my-4 border-gray-600">
                <form id="add-combatant-form" class="space-y-4">
                    <div>
                        <label for="combatant-name">Name</label>
                        <input type="text" id="combatant-name" placeholder="e.g., Goblin" required class="w-full text-lg p-3 rounded-lg border border-border-color bg-bg-main text-light focus:ring-accent-red focus:border-accent-red">
                    </div>
                    <div>
                        <label for="initiative-roll">Initiative</label>
                        <input type="number" id="initiative-roll" placeholder="e.g., 14" required class="w-full text-lg p-3 rounded-lg border border-border-color bg-bg-main text-light focus:ring-accent-red focus:border-accent-red">
                    </div>
                    <button type="submit" class="action-button w-full font-bold py-3">Add to Combat</button>
                </form>
            </div>
        </div>

        <!--
            RIGHT COLUMN: TURN ORDER DISPLAY
            This area contains the list of combatants, sorted by initiative,
            and the controls for managing the combat flow.
        -->
        <div class="md:col-span-2">
            <div class="section p-6">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                     <h2 class="section-title mb-0">Turn Order</h2>
                     <div class="flex gap-2">
                        <!-- Buttons to control the flow of combat -->
                        <button id="next-turn-btn" class="action-button font-bold py-2 px-4">Next Turn</button>
                        <button id="reset-btn" class="action-button bg-gray-600 hover:bg-gray-700 font-bold py-2 px-4">Reset</button>
                     </div>
                </div>

                <!--
                    This ordered list is populated by JavaScript with the combatant items.
                    It has a minimum height to prevent layout shifts when empty.
                -->
                <ol id="initiative-list" class="space-y-3 min-h-[200px]">
                    <li class="text-muted text-center p-8">Add combatants to begin...</li>
                </ol>
            </div>
        </div>
    </div>
</div>
