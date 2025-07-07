<!--
    Dack's DND Tools - pages/dice.php
    =================================
    This file creates the Dice Roller tool. It features a two-column layout with dice
    selection and roll controls on the left, and the results and history on the right.
    This version has been updated to use placeholder images for the dice.
-->

<!-- Main container for the dice roller tool, targeted by JavaScript -->
<div id="dice-roller-tool" class="max-w-7xl mx-auto p-4 md:p-6 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        PAGE HEADER
        Contains the main title and a descriptive subtitle for the page.
    -->
    <header class="text-center mb-10">
        <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Dice Roller</h1>
        <p class="text-xl text-muted mt-4">Click the dice to build your roll.</p>
    </header>

    <!--
        MAIN CONTENT GRID
        A responsive two-column grid that holds the tool's primary interface.
    -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!--
            LEFT COLUMN: CONTROLS
            Contains the dice selection, roll builder, and modifier controls.
        -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Dice Selection Panel -->
            <div class="panel p-6 center">
                <h2 class="section-title text-center">Add Dice</h2>
                <hr class="my-4 border-gray-600">
                <!--
                    The dice are now represented by clickable images in a responsive grid.
                    The 'dice-btn' class is used by JavaScript to identify them.
                -->
                <div class="grid grid-cols-3 gap-4 text-center content-center">
                    <div class="flex justify-center"><img src="style/images/dice/d4.png" alt="D4" class="dice-btn cursor-pointer transition-transform hover:scale-110 p-2" data-sides="4"></div>
                    <div class="flex justify-center"><img src="style/images/dice/d6.png" alt="D6" class="dice-btn cursor-pointer transition-transform hover:scale-110 p-2" data-sides="6"></div>
                    <div class="flex justify-center"><img src="style/images/dice/d8.png" alt="D8" class="dice-btn cursor-pointer transition-transform hover:scale-110 p-2" data-sides="8"></div>
                    <div class="flex justify-center"><img src="style/images/dice/d10.png" alt="D10" class="dice-btn cursor-pointer transition-transform hover:scale-110 p-2" data-sides="10"></div>
                    <div class="flex justify-center"><img src="style/images/dice/d12.png" alt="D12" class="dice-btn cursor-pointer transition-transform hover:scale-110 p-2" data-sides="12"></div>
                    <div class="flex justify-center"><img src="style/images/dice/d20.png" alt="D20" class="dice-btn cursor-pointer transition-transform hover:scale-110 p-2" data-sides="20"></div>
                </div>
            </div>

            <!-- Roll Builder Panel -->
            <div class="panel p-6 text-center">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="section-title mb-0">Current Roll</h2>
                    <button id="clearRoll" class="text-sm text-accent-red hover:underline">Clear</button>
                </div>
                <hr class="my-4 border-gray-600">
                <div class="text-3xl font-bold bg-black/20 p-4 rounded-lg mb-4 title-font min-h-[76px] flex items-center justify-center flex-wrap gap-x-4" id="rollDisplay">
                    <span class="text-gray-500 text-lg">Click dice to add...</span>
                </div>

                <!-- Modifier Controls -->
                <label class="section-title mb-2 block">Modifier</label>
                 <div class="flex justify-center items-center gap-4">
                    <button class="control-btn rounded-full h-12 w-12 text-2xl font-bold" id="mod-minus">-</button>
                    <span class="text-3xl font-bold w-16 title-font" id="mod-value">0</span>
                    <button class="control-btn rounded-full h-12 w-12 text-2xl font-bold" id="mod-plus">+</button>
                </div>
            </div>

            <!-- Main Roll Button -->
            <button id="rollButton" class="w-full main-roll-btn font-bold text-2xl py-4 rounded-lg transition-all title-font">
                Roll!
            </button>
        </div>

        <!--
            RIGHT COLUMN: RESULTS & HISTORY
            Contains the display for the most recent roll's result and a log of past rolls.
        -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Result Panel -->
            <div id="resultPanel" class="panel p-6 text-center min-h-[160px] flex flex-col justify-center items-center invisible">
                <div id="resultTotal" class="text-7xl font-bold title-font text-accent-red"></div>
                <div id="resultBreakdown" class="text-muted mt-2"></div>
            </div>

            <!-- History Panel -->
            <div class="panel p-6 flex flex-col h-[400px] md:h-auto lg:max-h-[calc(100vh-300px)]">
                <div class="flex justify-between items-center mb-3">
                    <h2 class="section-title mb-0">Roll History</h2>
                    <button id="clearHistory" class="text-sm text-accent-red hover:underline">Clear</button>
                </div>
                <div id="historyLog" class="flex-grow overflow-y-auto space-y-3 pr-2">
                    <!-- History items will be dynamically inserted here by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>
