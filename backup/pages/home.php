<!--
		Dack's DND Tools - pages/home.php
		=================================
		This file serves as the main landing page. Its layout has been updated
        to a two-column design with a main content area and a sidebar.
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
        primary content for the homepage, which is the "Toolkit" section.
    -->
    <div class="lg:w-3/4">
        <div id="tools" class="max-w-7xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">
            <header class="text-center mb-10">
                <h2 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">The Toolkit</h2>
                <p class="text-xl text-muted mt-4">Your humble workshop for crafting characters and rolling dice.</p>
            </header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Tool Card: Character Sheet -->
                <div class="tool-card">
                    <a href="index.php?page=sheet" class="block p-8 h-full">
                        <h3 class="text-4xl font-bold mb-3">Character Sheet</h3>
                        <p class="text-lg text-muted">A fully editable digital character sheet. Fill it out, save your progress, and download it as a PDF.</p>
                    </a>
                </div>
                <!-- Tool Card: Dice Roller -->
                <div class="tool-card">
                    <a href="index.php?page=dice" class="block p-8 h-full">
                        <h3 class="text-4xl font-bold mb-3">Dice Roller</h3>
                        <p class="text-lg text-muted">A classic dice roller. Build complex rolls with various dice and modifiers, and view your roll history.</p>
                    </a>
                </div>
                <!-- Tool Card: Compendium -->
                <div class="tool-card">
                     <a href="index.php?page=compendium" class="block p-8 h-full">
                        <h3 class="text-4xl font-bold mb-3">Compendium</h3>
                        <p class="text-lg text-muted">Quickly look up spells, monsters, and items from the D&D 5th Edition Systems Reference Document (SRD).</p>
                    </a>
                </div>
                <!-- Tool Card: Initiative Tracker -->
                <div class="tool-card">
                     <a href="index.php?page=initiative" class="block p-8 h-full">
                        <h3 class="text-4xl font-bold mb-3">Initiative Tracker</h3>
                        <p class="text-lg text-muted">A simple tool to track turn order during combat, ensuring fast-paced encounters.</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 
        SECTION 3: SIDEBAR AREA
        This div takes up 1/4 of the width on large screens. It includes the
        aside.php file, which contains the "Recent News" and "Quick Links" widgets.
    -->
    <div class="lg:w-1/4">
        <?php
        // Include the sidebar. Assumes aside.php is in the root directory.
        if (file_exists('./aside.php')) {
            include './aside.php';
        }
        ?>
    </div>
</div>
