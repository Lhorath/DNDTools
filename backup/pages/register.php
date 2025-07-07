<!--
    Dack's DND Tools - pages/register.php
    =====================================
    This file provides the user registration form, including a simple math-based
    CAPTCHA to prevent automated sign-ups.
-->

<!-- Main content container with consistent styling -->
<div id="registration-page" class="max-w-xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        PAGE HEADER
        Contains the main title and introductory text.
    -->
    <header class="text-center mb-10">
        <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Create Account</h1>
        <p class="text-xl text-muted mt-4">Join the fellowship and save your character data.</p>
    </header>

    <!--
        REGISTRATION FORM
        This form will be handled by js/auth.js to submit data to the API.
    -->
    <form id="register-form" novalidate>
        <!-- This div uses a 'space-y-6' class to add vertical spacing between all child elements. -->
        <div class="space-y-6">

            <!-- Message area for success or error feedback -->
            <div id="form-message" class="hidden p-4 rounded-md text-center"></div>

            <!-- Name Fields (First & Last) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="firstName">First Name</label>
                    <input type="text" id="firstName" name="firstName" required>
                </div>
                <div>
                    <label for="lastName">Last Name</label>
                    <input type="text" id="lastName" name="lastName" required>
                </div>
            </div>

            <!-- Display Name Field -->
            <div>
                <label for="displayName">Display Name</label>
                <input type="text" id="displayName" name="displayName" required>
            </div>

            <!-- Email Field -->
            <div>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Password Fields (Password & Confirm Password) -->
             <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                     <p class="text-xs text-muted mt-1">Minimum 8 characters.</p>
                </div>
                <div>
                    <label for="confirmPassword">Confirm Password</label>
                    <input type="password" id="confirmPassword" name="confirmPassword" required>
                </div>
            </div>

            <!-- CAPTCHA Section -->
            <div>
                <label for="captcha">Security Question</label>
                <div class="flex items-center gap-4 p-4 rounded-md" style="background-color: var(--bg-main);">
                    <span id="captcha-question" class="text-lg font-bold text-light"></span>
                    <input type="number" id="captcha" name="captcha" class="w-24 text-center" required>
                </div>
            </div>


            <!-- Submit Button -->
            <div class="text-center pt-4">
                <button type="submit" class="action-button font-bold py-3 px-8 rounded-lg text-xl title-font">
                    Register
                </button>
            </div>

        </div>
    </form>

    <p class="text-center mt-6 text-muted">
        Already have an account? <a href="index.php?page=login" class="text-accent-red hover:underline font-bold">Log In</a>
    </p>
</div>
