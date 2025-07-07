<?php
/*
    Dack's DND Tools - header.php
    =============================
    This version includes a dynamic hero slider that only displays on the homepage.
*/

// Start the session on every page load.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- SECTION 1: DYNAMIC PAGE DATA ---
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'home';

$pageData = [
    'home'       => ['title' => 'Home', 'description' => 'A collection of free, simple, and elegant tools for Dungeons & Dragons 5e.'],
    'sheet'      => ['title' => 'Character Sheet', 'description' => 'A fully editable digital D&D 5e character sheet.'],
    'dice'       => ['title' => 'Dice Roller', 'description' => 'A classic, skeuomorphic dice roller for D&D.'],
    'compendium' => ['title' => 'Compendium', 'description' => 'Search the D&D 5e SRD for spells, monsters, items, and more.'],
    'initiative' => ['title' => 'Initiative Tracker', 'description' => 'A simple tool to track turn order in D&D combat.'],
    'news'       => ['title' => 'News', 'description' => 'The latest news and updates for Dack\'s DND Tools.'],
    'about'      => ['title' => 'About', 'description' => 'Learn about the creation of Dack\'s DND Tools.'],
    'contact'    => ['title' => 'Contact', 'description' => 'Get in touch with the creator of Dack\'s DND Tools.'],
    'register'   => ['title' => 'Register', 'description' => 'Create a new account for Dack\'s DND Tools.'],
    'login'      => ['title' => 'Login', 'description' => 'Log in to your Dack\'s DND Tools account.'],
    'profile'    => ['title' => 'Profile', 'description' => 'View and manage your user profile.']
];

$title = isset($pageData[$currentPage]) ? $pageData[$currentPage]['title'] . ' | Dack\'s DND Tools' : 'Dack\'s DND Tools';
$description = isset($pageData[$currentPage]) ? $pageData[$currentPage]['description'] : 'A collection of free and simple tools for Dungeons & Dragons 5e.';
$base_url = "https://dnd.nerdygamertools.com/";
$ogUrl = $base_url . "?page=" . $currentPage;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!--
        SECTION 2: HTML HEAD
    -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars($title); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($description); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo htmlspecialchars($ogUrl); ?>">
    <meta property="og:image" content="<?php echo $base_url; ?>style/images/og-image.jpg">
    <meta property="og:image:alt" content="Dack's DND Tools Logo">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@400;700;900&family=Merriweather:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js" defer></script>
    <link rel="stylesheet" href="style/style.css">
</head>
<body class="bg-bg-main text-text-light">
    <!--
        SECTION 3: NAVIGATION BAR
    -->
    <nav class="sticky top-0 z-50 shadow-md">
        <!-- Navigation content remains the same -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex-shrink-0">
                    <a href="index.php?page=home"><img class="h-16" src="style/images/logo.png" alt="Dack's DND Tools Logo"></a>
                </div>
                <div class="hidden md:flex items-center space-x-1">
                    <a href="index.php?page=home" class="nav-link <?php if($currentPage == 'home') echo 'active-nav'; ?>">Home</a>
                    <div class="relative dropdown">
                        <button class="nav-link tools-dropdown-btn flex items-center">
                            <span>Tools</span>
                            <i class="fas fa-chevron-down text-xs ml-2"></i>
                        </button>
                        <div id="tools-dropdown-content" class="dropdown-content absolute hidden bg-bg-panel mt-2 py-2 w-56 rounded-md shadow-xl z-50">
                            <a href="index.php?page=sheet" class="dropdown-item">Character Sheet</a>
                            <a href="index.php?page=dice" class="dropdown-item">Dice Roller</a>
                            <a href="index.php?page=compendium" class="dropdown-item">Compendium</a>
                            <a href="index.php?page=initiative" class="dropdown-item">Initiative Tracker</a>
                        </div>
                    </div>
                    <a href="index.php?page=news" class="nav-link <?php if($currentPage == 'news') echo 'active-nav'; ?>">News</a>
                    <a href="index.php?page=about" class="nav-link <?php if($currentPage == 'about') echo 'active-nav'; ?>">About</a>
                    <a href="index.php?page=contact" class="nav-link <?php if($currentPage == 'contact') echo 'active-nav'; ?>">Contact</a>
                    <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                        <a href="index.php?page=profile" class="nav-link <?php if($currentPage == 'profile') echo 'active-nav'; ?>">Profile</a>
                        <a href="api/logout.php" class="nav-link">Logout</a>
                    <?php else: ?>
                        <a href="index.php?page=login" class="nav-link <?php if($currentPage == 'login') echo 'active-nav'; ?>">Login</a>
                        <a href="index.php?page=register" class="nav-link <?php if($currentPage == 'register') echo 'active-nav'; ?>">Register</a>
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
            <!-- Mobile nav links remain the same -->
        </div>
    </nav>
    
    <?php
    // --- SECTION 4: HERO SLIDER (HOMEPAGE ONLY) ---
    // This entire block will only be rendered if the current page is 'home'.
    if ($currentPage === 'home'):
    ?>
    <div id="hero-slider" class="relative w-full h-96 md:h-[500px] overflow-hidden shadow-lg">
        <!-- Slides Container -->
        <div class="relative h-full">
            <!-- Slide 1 -->
            <div class="hero-slide absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="https://placehold.co/1920x1080/5e160c/e2e2e2?text=Forge+Your+Legend" class="w-full h-full object-cover" alt="Fantasy Map">
                <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                    <a href="index.php?page=sheet" class="text-center p-4">
                        <h2 class="text-4xl md:text-6xl text-white font-bold title-font text-shadow">Forge Your Legend</h2>
                        <p class="text-lg md:text-xl text-white/90 mt-2">Create and manage your characters with a powerful digital sheet.</p>
                    </a>
                </div>
            </div>
            <!-- Slide 2 -->
            <div class="hero-slide absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="https://placehold.co/1920x1080/5e160c/e2e2e2?text=Let+Fate+Decide" class="w-full h-full object-cover" alt="Dice on a table">
                 <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                    <a href="index.php?page=dice" class="text-center p-4">
                        <h2 class="text-4xl md:text-6xl text-white font-bold title-font text-shadow">Let Fate Decide</h2>
                        <p class="text-lg md:text-xl text-white/90 mt-2">A versatile dice roller for every situation.</p>
                    </a>
                </div>
            </div>
            <!-- Slide 3 -->
            <div class="hero-slide absolute w-full h-full transition-opacity duration-1000 ease-in-out opacity-0">
                <img src="https://placehold.co/1920x1080/5e160c/e2e2e2?text=Unlock+Ancient+Lore" class="w-full h-full object-cover" alt="An old book">
                 <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                    <a href="index.php?page=compendium" class="text-center p-4">
                        <h2 class="text-4xl md:text-6xl text-white font-bold title-font text-shadow">Unlock Ancient Lore</h2>
                        <p class="text-lg md:text-xl text-white/90 mt-2">Instantly search the entire 5e SRD Compendium.</p>
                    </a>
                </div>
            </div>
        </div>

        <!-- Slider Navigation -->
        <button id="slider-prev" class="absolute top-1/2 left-4 -translate-y-1/2 bg-black/50 text-white p-3 rounded-full hover:bg-black/75 transition-colors">
            <i class="fas fa-chevron-left"></i>
        </button>
        <button id="slider-next" class="absolute top-1/2 right-4 -translate-y-1/2 bg-black/50 text-white p-3 rounded-full hover:bg-black/75 transition-colors">
            <i class="fas fa-chevron-right"></i>
        </button>

        <!-- Dot Indicators -->
        <div id="slider-dots" class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2">
            <!-- Dots will be generated by JavaScript -->
        </div>
    </div>
    <?php endif; ?>

    <!--
        SECTION 5: MAIN CONTENT WRAPPER
    -->
    <main class="p-4 sm:p-6 lg:p-8">
