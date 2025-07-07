<!--
    Dack's DND Tools - pages/initiative.php
    =======================================
    This file creates the user interface for the Initiative Tracker tool. The layout
    has been updated to a two-column design with the main tool on the left and a
    sidebar on the right.
-->

<!-- 
    SECTION 1: FLEX CONTAINER
    This is the main container that establishes the two-column layout.
    It's a column on mobile and switches to a row on large screens (lg:).
-->
<div class="flex flex-col lg:flex-row gap-8">

    <!-- 
        SECTION 2: MAIN CONTENT AREA
        This div takes up 3/4 of the width on large screens and contains the
        primary content for the initiative tracker tool.
    -->
    <div class="lg:w-3/4">
        <!-- Main container for the initiative tracker tool, targeted by JavaScript -->
        <div id="initiative-tracker-tool" class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

            <!-- Page Header -->
            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Initiative Tracker</h1>
                <p class="text-xl text-muted mt-4">Roll for initiative!</p>
            </header>

            <!-- Main Content Grid for the Tool -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                <!-- Left Column: Add Combatant Form -->
                <div class="md:col-span-1">
                    <div class="section p-6">
                        <h2 class="section-title text-center">Add Combatant</h2>
                        <hr class="my-4 border-gray-600">
                        <form id="add-combatant-form" class="space-y-4">
                            <div>
                                <label for="combatant-name">Name</label>
                                <input type="text" id="combatant-name" placeholder="e.g., Goblin" required>
                            </div>
                            <div>
                                <label for="initiative-roll">Initiative</label>
                                <input type="number" id="initiative-roll" placeholder="e.g., 14" required>
                            </div>
                            <button type="submit" class="action-button w-full font-bold py-3">Add to Combat</button>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Turn Order Display -->
                <div class="md:col-span-2">
                    <div class="section p-6">
                        <div class="flex flex-col sm:flex-row justify-between items-center mb-4 gap-4">
                             <h2 class="section-title mb-0">Turn Order</h2>
                             <div class="flex gap-2">
                                <button id="next-turn-btn" class="action-button font-bold py-2 px-4">Next Turn</button>
                                <button id="reset-btn" class="action-button bg-gray-600 hover:bg-gray-700 font-bold py-2 px-4">Reset</button>
                             </div>
                        </div>

                        <!-- This ordered list is populated by JavaScript with combatants -->
                        <ol id="initiative-list" class="space-y-3 min-h-[200px]">
                            <li class="text-muted text-center p-8">Add combatants to begin...</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 
        SECTION 3: SIDEBAR AREA
        This div takes up 1/4 of the width on large screens. It includes the
        aside.php file to display recent news and quick links.
    -->
    <div class="lg:w-1/4">
        <?php
        // Include the sidebar. The path is relative to the root index.php.
        if (file_exists('core/aside.php')) {
            include 'core/aside.php';
        }
        ?>
    </div>
</div>
