<?php
/*
    Dack's DND Tools - core/header.php
    ==================================
    This file contains the opening HTML structure, site-wide metadata, and the
    main navigation. It includes logic to conditionally hide the main navigation
    on the admin page and to show an "Admin" link only to authorized users.
*/

// SECTION 1: INITIALIZATION
// =========================
// Start the session to access user login state and get the global $page
// variable to determine the current page.
if (session_status() === PHP_SESSION_NONE) { session_start(); }
global $page;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    $meta_file = ROOT_PATH . 'includes/meta/' . $page . '.php';
    if (file_exists($meta_file)) {
        include $meta_file;
    } else {
        include ROOT_PATH . 'includes/meta/default.php';
    }
    ?>

    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL; ?>favicon.ico">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" defer></script>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>style/style.css">

</head>
<body class="bg-bg-main text-text-light">

    <?php if ($page !== 'admin'): ?>
    <nav class="sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="<?php echo BASE_URL; ?>home">
                        <img class="h-16" src="<?php echo BASE_URL; ?>style/images/logo.png" alt="Dack's DND Tools Logo">
                    </a>
                </div>

                <div class="hidden md:flex items-center space-x-1">
                    <a href="<?php echo BASE_URL; ?>home" class="nav-link <?php if($page == 'home') echo 'active-nav'; ?>">Home</a>
                    <div class="relative dropdown">
                        <button class="nav-link tools-dropdown-btn flex items-center">
                            <span>Tools</span>
                            <i class="fas fa-chevron-down text-xs ml-2"></i>
                        </button>
                        <div id="tools-dropdown-content" class="dropdown-content absolute hidden bg-bg-panel mt-2 py-2 w-56 rounded-md shadow-xl z-50">
                            <a href="<?php echo BASE_URL; ?>sheet" class="dropdown-item"><i class="fas fa-scroll fa-fw me-2"></i>Character Sheet</a>
                            <a href="<?php echo BASE_URL; ?>dice" class="dropdown-item"><i class="fas fa-dice-d20 fa-fw me-2"></i>Dice Roller</a>
                            <a href="<?php echo BASE_URL; ?>compendium" class="dropdown-item"><i class="fas fa-book-open fa-fw me-2"></i>Compendium</a>
                            <a href="<?php echo BASE_URL; ?>initiative" class="dropdown-item"><i class="fas fa-list-ol fa-fw me-2"></i>Initiative Tracker</a>
                        </div>
                    </div>
                    <a href="<?php echo BASE_URL; ?>news" class="nav-link <?php if($page == 'news') echo 'active-nav'; ?>">News</a>
                    <a href="<?php echo BASE_URL; ?>about" class="nav-link <?php if($page == 'about') echo 'active-nav'; ?>">About</a>
                    <a href="<?php echo BASE_URL; ?>contact" class="nav-link <?php if($page == 'contact') echo 'active-nav'; ?>">Contact</a>
                    
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <a href="<?php echo BASE_URL; ?>profile" class="nav-link <?php if($page == 'profile') echo 'active-nav'; ?>">Profile</a>
                        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 3): ?>
                            <a href="<?php echo BASE_URL; ?>admin" class="nav-link">Admin</a>
                        <?php endif; ?>
                        <a href="<?php echo BASE_URL; ?>includes/user/logout.php" class="nav-link">Logout</a>
                    <?php else: ?>
                        <a href="<?php echo BASE_URL; ?>login" class="nav-link <?php if($page == 'login') echo 'active-nav'; ?>">Login</a>
                        <a href="<?php echo BASE_URL; ?>register" class="nav-link <?php if($page == 'register') echo 'active-nav'; ?>">Register</a>
                    <?php endif; ?>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-white hover:bg-gray-700">
                        <i id="menu-open-icon" class="fas fa-bars text-2xl"></i>
                        <i id="menu-close-icon" class="fas fa-times text-2xl hidden"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="mobile-menu" class="md:hidden hidden">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="<?php echo BASE_URL; ?>home" class="mobile-nav-link <?php if($page == 'home') echo 'active-nav'; ?>">Home</a>
                <hr class="border-gray-700 my-2">
                <span class="block px-3 py-2 text-gray-400 text-sm font-bold uppercase">Tools</span>
                <a href="<?php echo BASE_URL; ?>sheet" class="mobile-nav-link ml-4">Character Sheet</a>
                <a href="<?php echo BASE_URL; ?>dice" class="mobile-nav-link ml-4">Dice Roller</a>
                <a href="<?php echo BASE_URL; ?>compendium" class="mobile-nav-link ml-4">Compendium</a>
                <a href="<?php echo BASE_URL; ?>initiative" class="mobile-nav-link ml-4">Initiative Tracker</a>
                <hr class="border-gray-700 my-2">
                <a href="<?php echo BASE_URL; ?>news" class="mobile-nav-link <?php if($page == 'news') echo 'active-nav'; ?>">News</a>
                <a href="<?php echo BASE_URL; ?>about" class="mobile-nav-link <?php if($page == 'about') echo 'active-nav'; ?>">About</a>
                <a href="<?php echo BASE_URL; ?>contact" class="mobile-nav-link <?php if($page == 'contact') echo 'active-nav'; ?>">Contact</a>
                <hr class="border-gray-700 my-2">
                <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                    <a href="<?php echo BASE_URL; ?>profile" class="mobile-nav-link <?php if($page == 'profile') echo 'active-nav'; ?>">Profile</a>
                    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 3): ?>
                        <a href="<?php echo BASE_URL; ?>admin" class="mobile-nav-link">Admin</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>includes/user/logout.php" class="mobile-nav-link">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login" class="mobile-nav-link <?php if($page == 'login') echo 'active-nav'; ?>">Login</a>
                    <a href="<?php echo BASE_URL; ?>register" class="mobile-nav-link <?php if($page == 'register') echo 'active-nav'; ?>">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <?php
    // Conditionally include the hero slider component only on the homepage.
    if ($page === 'home') {
        require_once 'slider.php';
    }
    ?>
    
<?php endif; // End of check for admin page ?>
