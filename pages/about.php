<!--
    Dack's DND Tools - pages/about.php
    ==================================
    This file provides information about the website. The layout has been
    updated to a two-column design with the main content on the left and a
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
        primary content for the "About" page.
    -->
    <div class="lg:w-3/4">
        <!-- Main container for the about page content -->
        <div class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

            <!-- Page Header -->
            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">About The Tools</h1>
            </header>

            <!-- Main Content Body -->
            <div class="text-lg text-light space-y-6 text-left">
                <!-- Introduction -->
                <p>Welcome, adventurer, to this humble corner of the web! Dack's DND Tools was forged from a passion for tabletop role-playing games, with the goal of providing a free, simple, and elegant suite of tools for players and Dungeon Masters alike.</p>

                <!-- Our Philosophy Section -->
                <div>
                    <h2 class="text-3xl font-bold title-font pt-4 pb-2">Our Philosophy</h2>
                    <p>We believe that technology should enhance the TTRPG experience, not complicate it. Our tools are designed to be intuitive and fast, getting out of your way so you can focus on what truly matters: weaving epic stories with your friends. We aim for a clean, classic aesthetic that evokes the feeling of a well-loved player's handbook while providing modern convenience.</p>
                </div>

                <!-- The Toolkit Section -->
                <div>
                    <h2 class="text-3xl font-bold title-font pt-4 pb-2">The Toolkit</h2>
                    <p>This website brings together four core tools to streamline your game:</p>
                    <ul class="list-disc list-inside space-y-3 mt-4 pl-4">
                        <li>
                            <strong>The Character Sheet:</strong> A fully-featured digital version of the classic 5th Edition sheet. It saves your data directly in your browser, automatically calculates modifiers, and allows you to download a PDF copy for printing.
                        </li>
                        <li>
                            <strong>The Dice Roller:</strong> A versatile dice roller that handles simple d20 checks and complex multi-dice damage rolls with modifiers. It keeps a running history of your rolls for easy reference.
                        </li>
                        <li>
                            <strong>The Compendium:</strong> Your quick-reference guide to the D&D 5th Edition Systems Reference Document (SRD). Effortlessly search for spells, monsters, magic items, and more.
                        </li>
                         <li>
                            <strong>The Initiative Tracker:</strong> A straightforward tool to manage turn order during combat. Add combatants, sort them by their initiative rolls, and easily advance to the next turn to keep your encounters running smoothly.
                        </li>
                    </ul>
                </div>

                <!-- The Creator Section -->
                <div>
                    <h2 class="text-3xl font-bold title-font pt-4 pb-2">The Creator</h2>
                    <p>Dack is a long-time TTRPG enthusiast and web developer who wanted to combine his two passions. This site is a personal project, built with the hope that it might prove useful to fellow adventurers in the vast and wonderful world of Dungeons & Dragons.</p>
                </div>

                <!-- Closing Statement -->
                <p class="pt-6 text-center text-muted">Thank you for visiting. May your rolls be ever in your favor!</p>
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
