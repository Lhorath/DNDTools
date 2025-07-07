<!--
    Dack's DND Tools - pages/sheet.php
    ==================================
    This file creates the interactive D&D 5e Character Sheet. The layout has been
    restructured into a classic three-column format for better alignment, readability,
    and a more intuitive user experience. All original form fields and functionality
    have been preserved.
-->

<!-- Main container for the character sheet, targeted by JavaScript -->
<div id="character-sheet" class="max-w-7xl mx-auto p-4 md:p-6 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        SECTION 1: PAGE HEADER
        Contains the main title and action buttons for saving data and downloading a PDF.
    -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-6">
        <h1 class="text-4xl font-bold text-light title-font">Character Sheet</h1>
        <div class="flex space-x-4 mt-4 md:mt-0">
            <button id="save-button" class="action-button font-bold py-2 px-4">Save Data</button>
            <button id="download-pdf" class="action-button font-bold py-2 px-4">Download PDF</button>
        </div>
    </div>

    <!--
        SECTION 2: CHARACTER INFORMATION
        A top section for the character's core identity details like name, class, race, etc.
    -->
    <div class="section mb-6">
        <h2 class="section-title text-center">Character Information</h2>
        <hr class="my-4 border-gray-600">
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
            <div><label for="char-name">Character Name</label><input type="text" id="char-name"></div>
            <div><label for="player-name">Player Name</label><input type="text" id="player-name"></div>
            <div><label for="class">Class</label><select id="class" name="class"></select></div>
            <div><label for="level">Level</label><input type="number" id="level" value="1"></div>
            <div><label for="race">Race</label><select id="race" name="race"></select></div>
            <div><label for="background">Background</label><input type="text" id="background"></div>
            <div><label for="alignment">Alignment</label><input type="text" id="alignment"></div>
            <div><label for="exp">Experience Points</label><input type="number" id="exp"></div>
        </div>
    </div>

    <!--
        SECTION 3: MAIN CONTENT GRID
        A responsive three-column grid that holds the sheet's primary stats.
    -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!--
            COLUMN 1: ABILITIES & SKILLS
            Contains Ability Scores, Saving Throws, and Skills.
        -->
        <div class="lg:col-span-1 flex flex-col gap-6">

            <!-- Ability Scores -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6">
                <div class="section">
                    <h2 class="section-title text-center">Ability Scores</h2>
                    <hr class="my-4 border-gray-600">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-2 gap-4">
                        <div class="text-center"><label for="strength">Strength</label><input type="number" id="strength" class="ability-score" data-mod="strength-mod" value="10"><input type="text" id="strength-mod" class="ability-mod" readonly></div>
                        <div class="text-center"><label for="dexterity">Dexterity</label><input type="number" id="dexterity" class="ability-score" data-mod="dexterity-mod" value="10"><input type="text" id="dexterity-mod" class="ability-mod" readonly></div>
                        <div class="text-center"><label for="constitution">Constitution</label><input type="number" id="constitution" class="ability-score" data-mod="constitution-mod" value="10"><input type="text" id="constitution-mod" class="ability-mod" readonly></div>
                        <div class="text-center"><label for="intelligence">Intelligence</label><input type="number" id="intelligence" class="ability-score" data-mod="intelligence-mod" value="10"><input type="text" id="intelligence-mod" class="ability-mod" readonly></div>
                        <div class="text-center"><label for="wisdom">Wisdom</label><input type="number" id="wisdom" class="ability-score" data-mod="wisdom-mod" value="10"><input type="text" id="wisdom-mod" class="ability-mod" readonly></div>
                        <div class="text-center"><label for="charisma">Charisma</label><input type="number" id="charisma" class="ability-score" data-mod="charisma-mod" value="10"><input type="text" id="charisma-mod" class="ability-mod" readonly></div>
                    </div>
                </div>

                <!-- Saving Throws & Core Stats -->
                <div class="section">
                     <div class="space-y-2 mb-6">
                        <div><label for="inspiration">Inspiration</label><input type="text" id="inspiration" class="text-2xl title-font h-12 text-center"></div>
                        <div><label for="proficiency-bonus">Proficiency Bonus</label><input type="text" id="proficiency-bonus" class="text-2xl title-font h-12 text-center" value="+2" readonly></div>
                        <div><label for="passive-perception">Passive Perception</label><input type="text" id="passive-perception" class="text-2xl title-font h-12 text-center" readonly></div>
                    </div>
                    <hr class="my-4 border-gray-600">
                    <h2 class="section-title text-center">Saving Throws</h2>
                    <hr class="my-4 border-gray-600">
                    <div class="space-y-2">
                        <div class="flex items-center"><input type="checkbox" id="strength-save-prof" data-ability="strength" class="prof-check mr-2 h-5 w-5"><label for="strength-save-prof" class="flex-grow text-lg">Strength</label><input type="text" id="strength-save" class="stat-view" readonly></div>
                        <div class="flex items-center"><input type="checkbox" id="dexterity-save-prof" data-ability="dexterity" class="prof-check mr-2 h-5 w-5"><label for="dexterity-save-prof" class="flex-grow text-lg">Dexterity</label><input type="text" id="dexterity-save" class="stat-view" readonly></div>
                        <div class="flex items-center"><input type="checkbox" id="constitution-save-prof" data-ability="constitution" class="prof-check mr-2 h-5 w-5"><label for="constitution-save-prof" class="flex-grow text-lg">Constitution</label><input type="text" id="constitution-save" class="stat-view" readonly></div>
                        <div class="flex items-center"><input type="checkbox" id="intelligence-save-prof" data-ability="intelligence" class="prof-check mr-2 h-5 w-5"><label for="intelligence-save-prof" class="flex-grow text-lg">Intelligence</label><input type="text" id="intelligence-save" class="stat-view" readonly></div>
                        <div class="flex items-center"><input type="checkbox" id="wisdom-save-prof" data-ability="wisdom" class="prof-check mr-2 h-5 w-5"><label for="wisdom-save-prof" class="flex-grow text-lg">Wisdom</label><input type="text" id="wisdom-save" class="stat-view" readonly></div>
                        <div class="flex items-center"><input type="checkbox" id="charisma-save-prof" data-ability="charisma" class="prof-check mr-2 h-5 w-5"><label for="charisma-save-prof" class="flex-grow text-lg">Charisma</label><input type="text" id="charisma-save" class="stat-view" readonly></div>
                    </div>
                </div>
            </div>

            <!-- Skills Section -->
            <div class="section">
                <h2 class="section-title text-center">Skills</h2>
                <hr class="my-4 border-gray-600">
                <div class="space-y-2">
                    <div class="flex items-center"><input type="checkbox" id="acrobatics-prof" data-ability="dexterity" class="prof-check mr-2 h-4 w-4"><label for="acrobatics-prof" class="flex-grow">Acrobatics <span class="text-xs text-muted">(Dex)</span></label><input type="text" id="acrobatics" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="animal-handling-prof" data-ability="wisdom" class="prof-check mr-2 h-4 w-4"><label for="animal-handling-prof" class="flex-grow">Animal Handling <span class="text-xs text-muted">(Wis)</span></label><input type="text" id="animal-handling" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="arcana-prof" data-ability="intelligence" class="prof-check mr-2 h-4 w-4"><label for="arcana-prof" class="flex-grow">Arcana <span class="text-xs text-muted">(Int)</span></label><input type="text" id="arcana" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="athletics-prof" data-ability="strength" class="prof-check mr-2 h-4 w-4"><label for="athletics-prof" class="flex-grow">Athletics <span class="text-xs text-muted">(Str)</span></label><input type="text" id="athletics" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="deception-prof" data-ability="charisma" class="prof-check mr-2 h-4 w-4"><label for="deception-prof" class="flex-grow">Deception <span class="text-xs text-muted">(Cha)</span></label><input type="text" id="deception" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="history-prof" data-ability="intelligence" class="prof-check mr-2 h-4 w-4"><label for="history-prof" class="flex-grow">History <span class="text-xs text-muted">(Int)</span></label><input type="text" id="history" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="insight-prof" data-ability="wisdom" class="prof-check mr-2 h-4 w-4"><label for="insight-prof" class="flex-grow">Insight <span class="text-xs text-muted">(Wis)</span></label><input type="text" id="insight" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="intimidation-prof" data-ability="charisma" class="prof-check mr-2 h-4 w-4"><label for="intimidation-prof" class="flex-grow">Intimidation <span class="text-xs text-muted">(Cha)</span></label><input type="text" id="intimidation" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="investigation-prof" data-ability="intelligence" class="prof-check mr-2 h-4 w-4"><label for="investigation-prof" class="flex-grow">Investigation <span class="text-xs text-muted">(Int)</span></label><input type="text" id="investigation" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="medicine-prof" data-ability="wisdom" class="prof-check mr-2 h-4 w-4"><label for="medicine-prof" class="flex-grow">Medicine <span class="text-xs text-muted">(Wis)</span></label><input type="text" id="medicine" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="nature-prof" data-ability="intelligence" class="prof-check mr-2 h-4 w-4"><label for="nature-prof" class="flex-grow">Nature <span class="text-xs text-muted">(Int)</span></label><input type="text" id="nature" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="perception-prof" data-ability="wisdom" class="prof-check mr-2 h-4 w-4"><label for="perception-prof" class="flex-grow">Perception <span class="text-xs text-muted">(Wis)</span></label><input type="text" id="perception" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="performance-prof" data-ability="charisma" class="prof-check mr-2 h-4 w-4"><label for="performance-prof" class="flex-grow">Performance <span class="text-xs text-muted">(Cha)</span></label><input type="text" id="performance" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="persuasion-prof" data-ability="charisma" class="prof-check mr-2 h-4 w-4"><label for="persuasion-prof" class="flex-grow">Persuasion <span class="text-xs text-muted">(Cha)</span></label><input type="text" id="persuasion" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="religion-prof" data-ability="intelligence" class="prof-check mr-2 h-4 w-4"><label for="religion-prof" class="flex-grow">Religion <span class="text-xs text-muted">(Int)</span></label><input type="text" id="religion" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="sleight-of-hand-prof" data-ability="dexterity" class="prof-check mr-2 h-4 w-4"><label for="sleight-of-hand-prof" class="flex-grow">Sleight of Hand <span class="text-xs text-muted">(Dex)</span></label><input type="text" id="sleight-of-hand" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="stealth-prof" data-ability="dexterity" class="prof-check mr-2 h-4 w-4"><label for="stealth-prof" class="flex-grow">Stealth <span class="text-xs text-muted">(Dex)</span></label><input type="text" id="stealth" class="stat-view" readonly></div>
                    <div class="flex items-center"><input type="checkbox" id="survival-prof" data-ability="wisdom" class="prof-check mr-2 h-4 w-4"><label for="survival-prof" class="flex-grow">Survival <span class="text-xs text-muted">(Wis)</span></label><input type="text" id="survival" class="stat-view" readonly></div>
                </div>
            </div>
        </div>

        <!--
            COLUMN 2: COMBAT & PERSONALITY
            Contains Combat Info, Hit Points, and Personality Traits.
        -->
        <div class="lg:col-span-1 flex flex-col gap-6">
            <div class="section">
                <h2 class="section-title text-center">Combat Stats</h2>
                <hr class="my-4 border-gray-600">
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div><label for="ac">Armor Class</label><input type="number" id="ac" class="h-24 text-4xl font-bold title-font text-center"></div>
                    <div><label for="initiative">Initiative</label><input type="text" id="initiative" class="h-24 text-4xl font-bold title-font text-center" readonly></div>
                    <div><label for="speed">Speed</label><input type="text" id="speed" class="h-24 text-4xl font-bold title-font text-center"></div>
                </div>
                <hr class="my-4 border-gray-600">
                <div class="space-y-4">
                    <div><label for="hp-max" class="text-center">Hit Point Maximum</label><input type="number" id="hp-max"></div>
                    <div><label for="hp-current" class="text-center">Current Hit Points</label><input type="number" id="hp-current"></div>
                    <div><label for="hp-temp" class="text-center">Temporary Hit Points</label><input type="number" id="hp-temp"></div>
                </div>
                <hr class="my-4 border-gray-600">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="text-center"><label for="hit-dice">Hit Dice</label><input type="text" id="hit-dice"></div>
                    <div class="text-center">
                        <label>Death Saves</label>
                        <div class="flex items-center justify-center gap-4">
                            <span>Successes</span>
                            <div class="flex gap-2">
                                <input type="checkbox" id="ds-s1" class="h-6 w-6 appearance-none bg-bg-main rounded-full border border-border-color checked:bg-green-600">
                                <input type="checkbox" id="ds-s2" class="h-6 w-6 appearance-none bg-bg-main rounded-full border border-border-color checked:bg-green-600">
                                <input type="checkbox" id="ds-s3" class="h-6 w-6 appearance-none bg-bg-main rounded-full border border-border-color checked:bg-green-600">
                            </div>
                        </div>
                        <div class="flex items-center justify-center gap-4 mt-2">
                            <span>Failures</span>
                             <div class="flex gap-2">
                                <input type="checkbox" id="ds-f1" class="h-6 w-6 appearance-none bg-bg-main rounded-full border border-border-color checked:bg-red-600">
                                <input type="checkbox" id="ds-f2" class="h-6 w-6 appearance-none bg-bg-main rounded-full border border-border-color checked:bg-red-600">
                                <input type="checkbox" id="ds-f3" class="h-6 w-6 appearance-none bg-bg-main rounded-full border border-border-color checked:bg-red-600">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title text-center">Personality</h2>
                <hr class="my-4 border-gray-600">
                <label for="personality-traits">Personality Traits</label><textarea id="personality-traits" rows="2"></textarea>
                <label for="ideals" class="mt-4">Ideals</label><textarea id="ideals" rows="2"></textarea>
                <label for="bonds" class="mt-4">Bonds</label><textarea id="bonds" rows="2"></textarea>
                <label for="flaws" class="mt-4">Flaws</label><textarea id="flaws" rows="2"></textarea>
            </div>
             <div class="section">
                <h2 class="section-title text-center">Features & Traits</h2>
                <hr class="my-4 border-gray-600">
                <textarea id="features-traits" rows="8"></textarea>
            </div>
        </div>

        <!--
            COLUMN 3: EQUIPMENT & DESCRIPTION
            Contains Equipment, Character Appearance, and other text areas.
        -->
        <div class="lg:col-span-1 flex flex-col gap-6">
             <div class="section">
                <h2 class="section-title text-center">Equipment</h2>
                <hr class="my-4 border-gray-600">
                <div class="grid grid-cols-5 gap-2 text-center mb-4">
                    <div><label>CP</label><input type="number" id="cp"></div>
                    <div><label>SP</label><input type="number" id="sp"></div>
                    <div><label>EP</label><input type="number" id="ep"></div>
                    <div><label>GP</label><input type="number" id="gp"></div>
                    <div><label>PP</label><input type="number" id="pp"></div>
                </div>
                <div class="mb-4">
                    <label for="equipment-search" class="font-semibold">Add Equipment:</label>
                    <div class="flex gap-2 mt-1"><input type="text" id="equipment-search" placeholder="Search..."><button id="equipment-search-btn" class="action-button px-4 py-2"><i class="fas fa-search"></i></button></div>
                    <div id="equipment-search-results" class="relative"></div>
                </div>
                <h3 class="section-title border-t-2 border-border-color pt-4 text-center">Carried Items</h3>
                <hr class="my-4 border-gray-600">
                <div id="equipment-list" class="space-y-2 min-h-[100px] p-2 bg-black/20 rounded-md"></div>
            </div>
            <div class="section">
                <h2 class="section-title text-center">Other Proficiencies & Languages</h2>
                <hr class="my-4 border-gray-600">
                <textarea id="other-proficiencies" rows="5"></textarea>
            </div>
            <div class="section">
                <h2 class="section-title text-center">Character Appearance</h2>
                <hr class="my-4 border-gray-600">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div><label for="char-age">Age</label><input type="text" id="char-age"></div>
                    <div><label for="height">Height</label><input type="text" id="height"></div>
                    <div><label for="weight">Weight</label><input type="text" id="weight"></div>
                    <div><label for="eyes">Eyes</label><input type="text" id="eyes"></div>
                    <div><label for="skin">Skin</label><input type="text" id="skin"></div>
                    <div><label for="hair">Hair</label><input type="text" id="hair"></div>
                </div>
            </div>
        </div>
    </div>

    <!--
        SECTION 4: DYNAMIC CARD SECTIONS
        These containers are where detailed cards for spells, items, etc., will be displayed by JavaScript.
    -->
    <div class="section mt-6">
        <h2 class="section-title text-center">Equipment Details</h2>
        <hr class="my-4 border-gray-600">
        <div id="equipment-cards-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[50px]"></div>
    </div>

    <!--
        SECTION 5: SPELLCASTING
        A dedicated section for spellcasters.
    -->
    <div class="section mt-6">
        <h2 class="section-title text-center">Spellcasting</h2>
        <hr class="my-4 border-gray-600">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div><label for="spell-class">Spellcasting Class</label><input type="text" id="spell-class" readonly></div>
            <div><label for="spell-ability">Spellcasting Ability</label><input type="text" id="spell-ability" readonly></div>
            <div><label for="spell-save-dc">Spell Save DC</label><input type="text" id="spell-save-dc" readonly></div>
            <div><label for="spell-atk-bonus">Spell Attack Bonus</label><input type="text" id="spell-atk-bonus" readonly></div>
        </div>
        <div class="mb-4">
            <label for="spell-search" class="font-semibold">Add a Spell:</label>
            <div class="flex gap-2 mt-1"><input type="text" id="spell-search" placeholder="Search..."><button id="spell-search-btn" class="action-button px-4 py-2"><i class="fas fa-search"></i></button></div>
            <div id="spell-search-results" class="relative"></div>
        </div>
        <h3 class="section-title border-t-2 border-border-color pt-4 text-center">Known Spells</h3>
        <hr class="my-4 border-gray-600">
        <div id="spell-list" class="space-y-2 min-h-[100px] p-2 bg-black/20 rounded-md"></div>
    </div>

    <div class="section mt-6">
        <h2 class="section-title text-center">Spell Details</h2>
        <hr class="my-4 border-gray-600">
        <div id="spell-cards-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[50px]"></div>
    </div>

    <!--
        SECTION 6: TREASURE & CUSTOM CONTENT
    -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div class="section">
            <h2 class="section-title text-center">Treasure</h2>
            <hr class="my-4 border-gray-600">
            <div class="mb-4">
                <label for="treasure-search" class="font-semibold">Add Treasure:</label>
                <div class="flex gap-2 mt-1"><input type="text" id="treasure-search" placeholder="Search for a magic item..."><button id="treasure-search-btn" class="action-button px-4 py-2"><i class="fas fa-search"></i></button></div>
                <div id="treasure-search-results" class="relative"></div>
            </div>
            <div id="treasure-list" class="space-y-2 min-h-[100px] p-2 bg-black/20 rounded-md"></div>
        </div>
        <div class="section">
            <h2 class="section-title text-center">Custom Content</h2>
            <hr class="my-4 border-gray-600">
            <div class="flex flex-wrap justify-center gap-4">
                <button id="add-custom-spell" class="action-button font-bold py-2 px-4">Add Custom Spell</button>
                <button id="add-custom-equipment" class="action-button font-bold py-2 px-4">Add Custom Equipment</button>
                <button id="add-custom-treasure" class="action-button font-bold py-2 px-4">Add Custom Treasure</button>
            </div>
        </div>
    </div>

    <div class="section mt-6">
        <h2 class="section-title text-center">Treasure Details</h2>
        <hr class="my-4 border-gray-600">
        <div id="treasure-cards-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 min-h-[50px]"></div>
    </div>

</div>
