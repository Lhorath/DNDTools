<!--
    Dack's DND Tools - pages/contact.php
    ====================================
    This file provides a contact form for users to send messages.
    It has been updated with a more consistent design, better layout, and a new "Subject" field.
-->

<!-- Main content container with consistent styling -->
<div class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        PAGE HEADER
        Contains the main title and a brief introductory paragraph.
    -->
    <header class="text-center mb-10">
        <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Get In Touch</h1>
        <p class="text-xl text-muted mt-4 max-w-2xl mx-auto">Have a question, a suggestion for a new tool, or a bug to report? Use the form below to send a message.</p>
    </header>

    <!--
        CONTACT FORM
        A standard form for users to input their name, email, subject, and message.
        The 'action' attribute should be updated to point to a PHP script for processing.
    -->
    <form action="#" method="POST">
        <!-- This div uses a 'space-y-6' class to add vertical spacing between all child elements (the form fields). -->
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

            <!-- Subject Field (NEW) -->
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
