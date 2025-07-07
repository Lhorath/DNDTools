<!--
    Dack's DND Tools - pages/login.php
    ==================================
    This file provides the user login form.
-->

<!-- Main content container with consistent styling -->
<div id="login-page" class="max-w-xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        PAGE HEADER
        Contains the main title.
    -->
    <header class="text-center mb-10">
        <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Login</h1>
        <p class="text-xl text-muted mt-4">Welcome back, adventurer.</p>
    </header>

    <!--
        LOGIN FORM
        This form will be handled by js/auth.js to submit data to the API.
    -->
    <form id="login-form" novalidate>
        <div class="space-y-6">

            <!-- Message area for success or error feedback -->
            <div id="form-message" class="hidden p-4 rounded-md text-center"></div>

            <!-- Email Field -->
            <div>
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required>
            </div>

            <!-- Password Field -->
            <div>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <!-- Submit Button -->
            <div class="text-center pt-4">
                <button type="submit" class="action-button font-bold py-3 px-8 rounded-lg text-xl title-font">
                    Login
                </button>
            </div>

        </div>
    </form>

    <p class="text-center mt-6 text-muted">
        Don't have an account? <a href="index.php?page=register" class="text-accent-red hover:underline font-bold">Sign Up</a>
    </p>
</div>
