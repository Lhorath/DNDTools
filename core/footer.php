<?php
/*
    Dack's DND Tools - core/footer.php
    ==================================
    This file contains the closing structure for every page. The footer has been
    redesigned to be more substantial and visually consistent with the site theme.
*/
?>
    </main> <!-- This closes the <main> tag opened in core/body.php -->

    <!--
        SECTION 1: SITE FOOTER
        ======================
        This footer is conditionally hidden on the admin page. It features a
        multi-column layout for better organization of links and information.
    -->
    <?php global $page; if ($page !== 'admin'): ?>
    <footer class="mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <!-- Footer Column 1: About -->
                <div class="space-y-4">
                    <h5 class="text-lg font-bold title-font">Dack's DND Tools</h5>
                    <p class="text-sm text-muted">
                        A free, simple, and elegant suite of tools designed to enhance your Dungeons & Dragons 5e experience.
                    </p>
                </div>
                
                <!-- Footer Column 2: Quick Links -->
                <div class="space-y-4">
                    <h5 class="text-lg font-bold title-font">Quick Links</h5>
                    <nav class="flex flex-col">
                        <a href="<?php echo BASE_URL; ?>sheet">Character Sheet</a>
                        <a href="<?php echo BASE_URL; ?>dice">Dice Roller</a>
                        <a href="<?php echo BASE_URL; ?>compendium">Compendium</a>
                        <a href="<?php echo BASE_URL; ?>initiative">Initiative Tracker</a>
                    </nav>
                </div>

                <!-- Footer Column 3: Legal (Placeholders) -->
                <div class="space-y-4">
                    <h5 class="text-lg font-bold title-font">Legal</h5>
                    <nav class="flex flex-col">
                        <a href="#">Privacy Policy</a>
                        <a href="#">Terms of Service</a>
                    </nav>
                </div>

            </div>
            
            <!-- Footer Bottom Bar -->
            <div class="mt-8 pt-8 border-t border-border-color flex flex-col sm:flex-row justify-between items-center">
                <p class="text-sm text-muted">&copy; <?php echo date('Y'); ?> Dack's DND Tools. All Rights Reserved.</p>
                <div class="flex space-x-6 mt-4 sm:mt-0">
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>
    <?php endif; ?>

    <!--
        SECTION 2: JAVASCRIPT LOADING
        =============================
        This section loads all necessary JavaScript files.
    -->
    <script src="<?php echo BASE_URL; ?>includes/js/main.js"></script>
    <?php
    $page_scripts = [
        'sheet'      => 'sheet.js',
        'dice'       => 'dice.js',
        'compendium' => 'compendium.js',
        'initiative' => 'inittrack.js',
        'register'   => 'auth.js',
        'login'      => 'auth.js',
        'profile'    => 'auth.js'
    ];
    if (isset($page_scripts[$page])) {
        echo '<script src="' . BASE_URL . 'includes/js/' . $page_scripts[$page] . '"></script>';
    }
    ?>
</body>
</html>
