<?php
/*
    Dack's DND Tools - index.php
    ============================
    This file acts as the main router or controller for the entire website.
    It's responsible for including the header and footer on every page and for
    dynamically loading the correct page content based on the URL.
*/

// SECTION 1: INCLUDE HEADER
// The 'header.php' file contains the opening HTML, <head> section, and navigation bar.
// It is included at the top of every page to ensure a consistent layout.
include 'header.php';

?>

<?php

// SECTION 2: DYNAMIC PAGE ROUTING
// This block of PHP code determines which page content to display.

// It checks the 'page' parameter from the URL (e.g., index.php?page=about).
// If the parameter is not set, it safely defaults to 'home'.
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// It constructs the full path to the requested page file within the 'pages' directory.
$page_path = 'pages/' . $page . '.php';

// It checks if the requested page file actually exists. This is a security measure
// to prevent errors and potential vulnerabilities from including non-existent files.
if (file_exists($page_path)) {
    // If the file exists, it is included, and its content is displayed.
    include $page_path;
} else {
    // If the file does not exist, a simple "404 - Page Not Found" message is displayed.
    // This provides basic error handling for invalid URLs.
    echo '<div class="max-w-4xl mx-auto p-10 text-center"><h1 class="text-6xl title-font">404</h1><p class="text-xl text-muted mt-4">Page Not Found</p></div>';
}

?>

<?php

// SECTION 3: INCLUDE FOOTER
// The 'footer.php' file contains the closing HTML, the site's footer, and script tags.
// It is included at the bottom of every page.
include 'footer.php';

?>
