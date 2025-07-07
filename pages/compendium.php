<div class="flex flex-col lg:flex-row gap-8">

    <div class="lg:w-3/4">
        <div id="lookup-tool" class="max-w-7xl mx-auto p-4 md:p-6 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Compendium</h1>
                <p class="text-xl text-muted mt-4">Browse the D&D 5th Edition Systems Reference Document.</p>
            </header>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">

                <div class="md:col-span-1">
                    <div class="section p-4">
                        <h2 class="section-title text-center">Categories</h2>
                        <hr class="my-4 border-gray-600">
                        <div class="max-h-[60vh] overflow-y-auto pr-2">
                            <nav id="compendium-categories" class="flex flex-col space-y-1">
                                <button data-tab="spells" class="sheet-tab active-sheet-tab">Spells</button>
                                <button data-tab="monsters" class="sheet-tab">Monsters</button>
                                <button data-tab="equipment" class="sheet-tab">Equipment</button>
                                <button data-tab="magic_items" class="sheet-tab">Magic Items</button>
                                <button data-tab="classes" class="sheet-tab">Classes</button>
                                <button data-tab="races" class="sheet-tab">Races</button>
                                <button data-tab="subclasses" class="sheet-tab">Subclasses</button>
                                <button data-tab="subraces" class="sheet-tab">Subraces</button>
                                <button data-tab="skills" class="sheet-tab">Skills</button>
                                <button data-tab="traits" class="sheet-tab">Traits</button>
                                <button data-tab="proficiencies" class="sheet-tab">Proficiencies</button>
                                <button data-tab="languages" class="sheet-tab">Languages</button>
                                <button data-tab="features" class="sheet-tab">Features</button>
                                <button data-tab="backgrounds" class="sheet-tab">Backgrounds</button>
                                <button data-tab="feats" class="sheet-tab">Feats</button>
                                <button data-tab="alignments" class="sheet-tab">Alignments</button>
                                <button data-tab="equipment_categories" class="sheet-tab">Eq. Categories</button>
                                <button data-tab="damage_types" class="sheet-tab">Damage Types</button>
                                <button data-tab="conditions" class="sheet-tab">Conditions</button>
                                <button data-tab="ability_scores" class="sheet-tab">Ability Scores</button>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="md:col-span-3">
                     <div class="flex items-center gap-4 mb-6">
                        <input type="text" id="lookup-search-input" placeholder="Search for spells..." class="w-full text-lg p-3 rounded-lg border border-border-color bg-bg-main text-light focus:ring-accent-red focus:border-accent-red">
                        <button id="lookup-search-button" class="action-button px-6 py-3 rounded-lg text-xl title-font">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>

                    <div id="lookup-results" class="panel p-6 min-h-[500px]">
                        <p class="text-muted text-center py-8">Select a category and use the search bar to begin.</p>
                    </div>

                    <div id="pagination-controls" class="flex justify-center items-center mt-6 space-x-2">
                        </div>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:w-1/4">
        <?php
        // Include the sidebar. The path is relative to the root index.php.
        if (file_exists('core/aside.php')) {
            include 'core/aside.php';
        }
        ?>
    </div>
</div>
