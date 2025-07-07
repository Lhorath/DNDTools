<?php
/*
    Dack's DND Tools - /includes/meta/dice.php
    ==========================================
    This file contains all the specific metadata for the "Dice Roller" page.
    It is included by core/header.php when the dice page is loaded.
*/

// --- SECTION 1: SEO & PAGE INFORMATION ---
// =========================================
// This data is used to populate the primary meta tags for search engines.
$title = "Dice Roller | Dack's DND Tools";
$description = "A classic, skeuomorphic dice roller for D&D. Build complex rolls with various dice and modifiers, and view your roll history.";
$keywords = "D&D dice roller, roll d20, TTRPG dice, virtual dice, online dice, Dungeons and Dragons";
$author = "Dackary McDab";

// --- SECTION 2: OPEN GRAPH / SOCIAL MEDIA METADATA ---
// =====================================================
// This data is used to create rich link previews on platforms like Facebook, Discord, etc.
$og_title = "D&D 5e Dice Roller | Dack's DND Tools";
$og_description = "A versatile dice roller for every situation. Build complex rolls with multiple dice types and modifiers, and view a complete history of your results.";
$og_type = "website";
$og_url = BASE_URL . "dice";
$og_image = BASE_URL . "style/images/og-image.jpg";
$og_image_alt = "The Dack's DND Tools logo over a background of a dragon scale texture.";
$og_site_name = "Dack's DND Tools";

// --- SECTION 3: TWITTER CARD METADATA ---
// ========================================
// These tags are specific to creating rich link previews on X (formerly Twitter).
$twitter_card = "summary_large_image";
$twitter_title = $og_title;
$twitter_description = $og_description;
$twitter_image = $og_image;
$twitter_image_alt = $og_image_alt;

?>

<title><?php echo htmlspecialchars($title); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($description); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($keywords); ?>">
<meta name="author" content="<?php echo htmlspecialchars($author); ?>">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#9f1212">

<meta property="og:title" content="<?php echo htmlspecialchars($og_title); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($og_description); ?>">
<meta property="og:type" content="<?php echo htmlspecialchars($og_type); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($og_url); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($og_image); ?>">
<meta property="og:image:alt" content="<?php echo htmlspecialchars($og_image_alt); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($og_site_name); ?>">

<meta name="twitter:card" content="<?php echo htmlspecialchars($twitter_card); ?>">
<meta name="twitter:title" content="<?php echo htmlspecialchars($twitter_title); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($twitter_description); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($twitter_image); ?>">
<meta name="twitter:image:alt" content="<?php echo htmlspecialchars($twitter_image_alt); ?>">
