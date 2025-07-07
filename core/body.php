<?php
/*
    Dack's DND Tools - core/body.php
    ================================
    This file acts as the main content loader for the application.
    It checks for the existence of a page file and includes it. If the page
    doesn't exist, it displays a 404 error.
*/

// SECTION 1: GLOBAL VARIABLE ACCESS
// ===================================
// The global $page variable, defined in 'includes/core/includes.php',
// is used to determine which content file to load from the 'pages' directory.
global $page;

// Construct the full path to the requested page file.
$page_path = ROOT_PATH . 'pages/' . $page . '.php';

?>

<!-- 
    SECTION 2: MAIN CONTENT WRAPPER
    ===============================
    This <main> tag wraps all page-specific content, providing consistent
    padding and a semantic container for the primary content of the document.
-->
<main class="p-4 sm:p-6 lg:p-8">

<?php
// SECTION 3: DYNAMIC PAGE INCLUSION
// =================================
// This is the core logic that makes the site's routing work.
// It checks if the constructed file path points to a real file.
if (file_exists($page_path)) {
    // If the file exists, it is included here, rendering its content
    // directly into the <main> tag of the page.
    include $page_path;
} else {
    // If the file does not exist, a 404 "Not Found" HTTP status code is sent,
    // and a user-friendly error message is displayed, complete with a link
    // to return to the homepage.
    http_response_code(404);
    echo '<div class="max-w-4xl mx-auto p-10 text-center"><h1 class="text-6xl title-font">404</h1><p class="text-xl text-muted mt-4">Page Not Found</p><p class="mt-4"><a href="' . BASE_URL . 'home" class="action-button font-bold py-2 px-6 rounded-lg">Go Home</a></p></div>';
}
?>

</main>
