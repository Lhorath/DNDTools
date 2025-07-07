<div class="flex flex-col lg:flex-row gap-8">

    <div class="lg:w-3/4">
        <div id="tools" class="max-w-7xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">
            <header class="text-center mb-10">
                <h2 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">The Toolkit</h2>
                <p class="text-xl text-muted mt-4">Your humble workshop for crafting characters and rolling dice.</p>
            </header>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <div class="tool-card overflow-hidden flex flex-col">
                    <img class="w-full h-48 object-cover" src="https://dnd.nerdygamertools.com/style/images/sheet.jpg" alt="Character Sheet" />
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-4xl font-bold mb-3">Character Sheet</h3>
                        <p class="text-lg text-muted flex-grow">A powerful digital sheet that automatically loads starting proficiencies, languages, and equipment when you select your character's race and class from the database.</p>
                        <a href="<?php echo BASE_URL; ?>sheet" class="action-button mt-4 text-center font-bold py-2 px-4">Create Character</a>
                    </div>
                </div>

                <div class="tool-card overflow-hidden flex flex-col">
                    <img class="w-full h-48 object-cover" src="https://dnd.nerdygamertools.com/style/images/dice.jpg" alt="Dice Roller" />
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-4xl font-bold mb-3">Dice Roller</h3>
                        <p class="text-lg text-muted flex-grow">A classic, intuitive dice roller. Build complex rolls with multiple dice types and modifiers, and view a complete history of your results.</p>
                        <a href="<?php echo BASE_URL; ?>dice" class="action-button mt-4 text-center font-bold py-2 px-4">Roll Dice</a>
                    </div>
                </div>

                <div class="tool-card overflow-hidden flex flex-col">
                    <img class="w-full h-48 object-cover" src="https://dnd.nerdygamertools.com/style/images/compendium.jpg" alt="Compendium" />
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-4xl font-bold mb-3">Compendium</h3>
                        <p class="text-lg text-muted flex-grow">Your complete reference guide. Instantly search for detailed information on spells, monsters, magic items, classes, races, and more from the D&D 5e SRD.</p>
                        <a href="<?php echo BASE_URL; ?>compendium" class="action-button mt-4 text-center font-bold py-2 px-4">Browse Rules</a>
                    </div>
                </div>

                <div class="tool-card overflow-hidden flex flex-col">
                    <img class="w-full h-48 object-cover" src="https://dnd.nerdygamertools.com/style/images/initiative.jpg" alt="Initiative Tracker" />
                    <div class="p-5 flex flex-col flex-grow">
                        <h3 class="text-4xl font-bold mb-3">Initiative Tracker</h3>
                        <p class="text-lg text-muted flex-grow">A simple and fast tool to manage turn order during combat. Add combatants, sort by initiative, and easily advance through rounds to keep your encounters moving.</p>
                        <a href="<?php echo BASE_URL; ?>initiative" class="action-button mt-4 text-center font-bold py-2 px-4">Track Combat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:w-1/4">
        <?php
        if (file_exists('core/aside.php')) {
            include 'core/aside.php';
        }
        ?>
    </div>
</div>
