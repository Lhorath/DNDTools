<!--
    Dack's DND Tools - pages/login.php
    ==================================
    This file provides the user login form. Its layout has been updated
    to a two-column design with the main content on the left and a
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
        primary content for the "Login" page.
    -->
    <div class="lg:w-3/4">
        <!-- Main container for the login page content -->
        <div id="login-page" class="max-w-xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

            <!-- Page Header -->
            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">Login</h1>
                <p class="text-xl text-muted mt-4">Welcome back, adventurer.</p>
            </header>

            <!-- 
                Login Form
                The form's functionality is handled by includes/js/auth.js, which
                sends the data to the login API endpoint.
            -->
            <form id="login-form" novalidate>
                <div class="space-y-6">

                    <!-- This div is used by JavaScript to display success or error messages -->
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
                Don't have an account? <a href="<?php echo BASE_URL; ?>register" class="text-accent-red hover:underline font-bold">Sign Up</a>
            </p>
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
