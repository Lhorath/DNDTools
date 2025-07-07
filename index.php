<?php
/*
    Dack's DND Tools - Main Entry Point
    ===================================
    This file acts as the single entry point and router for the entire application.
    It constructs the page by including the necessary core layout components.
*/

// SECTION 1: INITIALIZE THE APPLICATION
// This file handles all core setup tasks, such as starting sessions, connecting to
// the database, processing URLs, and defining global constants. By including it
// first, we ensure all necessary resources are available to the entire site.
require_once 'includes/core/includes.php';


// SECTION 2: BUILD THE HTML DOCUMENT
// The page is now built in a logical, structured sequence by including the
// primary layout components from the /core/ directory.

// 2.1: Include the site header (Doctype, <head>, top navigation, and hero slider).
require_once 'core/header.php';

// 2.2: Include the main body, which contains the primary content-loading logic.
require_once 'core/body.php';

// 2.3: Include the site footer and closing script tags.
require_once 'core/footer.php';

?>
