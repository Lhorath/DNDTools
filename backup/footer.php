<?php
/*
    Dack's DND Tools - footer.php
    =============================
    This file contains the closing structure for every page. It includes the
    closing </main> tag, the site footer with social media links, and the logic
    for loading the correct page-specific JavaScript file.
*/
?>
    </main> <!-- This closes the <main> tag opened in header.php -->

    <!--
        SITE FOOTER
        A consistent footer that appears at the bottom of every page.
        It contains social media links and a copyright notice.
    -->
    <footer class="mt-12">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-center text-muted border-t-2 border-border-color" style="background-color: rgba(17, 17, 17, 0.8); backdrop-filter: blur(10px);">
            <div class="flex justify-center space-x-6 mb-4 text-2xl">
                <!-- Social media links (currently placeholders) -->
                <a href="#" class="social-icon">
                    <i class="fa-brands fa-twitter"></i>
                </a>
                <a href="#" class="social-icon">
                    <i class="fa-brands fa-instagram"></i>
                </a>
                 <a href="#" class="social-icon">
                    <i class="fa-brands fa-github"></i>
                </a>
            </div>
            <p>&copy; <?php echo date('Y'); ?> Dack's DND Tools. All Rights Reserved.</p>
        </div>
    </footer>

    <!--
        JAVASCRIPT LOADING
        This section loads all necessary JavaScript files.
    -->

    <!-- Main JavaScript file with scripts that run on every page (e.g., for the navigation menu) -->
    <script src="js/main.js"></script>

    <?php
    // This PHP block dynamically includes the correct JavaScript file for the current page.
    // This is an efficient way to ensure that pages only load the scripts they need.
    $page_scripts = [
        'sheet'      => 'js/sheet.js',
        'dice'       => 'js/dice.js',
        'compendium' => 'js/compendium.js',
        'initiative' => 'js/inittrack.js',
        'register'   => 'js/auth.js',
        'login'      => 'js/auth.js',
        'profile'    => 'js/auth.js'
    ];

    // It checks if an entry for the current page exists in the $page_scripts array.
    if (isset($page_scripts[$currentPage])) {
        // If it exists, it generates a <script> tag to load that specific file.
        echo '<script src="' . $page_scripts[$currentPage] . '"></script>';
    }
    ?>
</body>
</html>
