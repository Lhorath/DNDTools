<!--
    Dack's DND Tools - pages/contact.php
    ====================================
    This file provides a contact form for users. The layout has been
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
        primary content for the "Contact" page.
    -->
    <div class="lg:w-3/4">
        <!-- Main container for the contact page content -->
        <div class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

            <!-- Page Header -->
            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Get In Touch</h1>
                <p class="text-xl text-muted mt-4 max-w-2xl mx-auto">Have a question, a suggestion for a new tool, or a bug to report? Use the form below to send a message.</p>
            </header>

            <!-- 
                Contact Form 
                The form's action attribute should point to a backend script for processing,
                which has not yet been implemented.
            -->
            <form action="#" method="POST">
                <div class="space-y-6">
                    <!-- Name Field -->
                    <div>
                        <label for="name">Your Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>

                    <!-- Email Field -->
                    <div>
                        <label for="email">Your Email</label>
                        <input type="email" name="email" id="email" required>
                    </div>

                    <!-- Subject Field -->
                    <div>
                        <label for="subject">Subject</label>
                        <select id="subject" name="subject" required>
                            <option value="" disabled selected>Please select a subject...</option>
                            <option value="Character Sheet">Character Sheet</option>
                            <option value="Dice Roller">Dice Roller</option>
                            <option value="Compendium">Compendium</option>
                            <option value="Initiative Tracker">Initiative Tracker</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <!-- Message Field -->
                    <div>
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="6" required></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center pt-4">
                        <button type="submit" class="action-button font-bold py-3 px-8 rounded-lg text-xl title-font">
                            Send Message
                        </button>
                    </div>
                </div>
            </form>
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
