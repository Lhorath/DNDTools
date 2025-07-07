<?php
/*
    Dack's DND Tools - /includes/meta/news.php
    ==========================================
    This file contains all the specific metadata for the "News" page.
    It is included by core/header.php when the news page is loaded.
*/

// --- SECTION 1: SEO & PAGE INFORMATION ---
// =========================================
// This data is used to populate the primary meta tags for search engines.
$title = "News & Updates | Dack's DND Tools";
$description = "The latest news, announcements, and updates for Dack's DND Tools. Stay informed about new features, improvements, and community news.";
$keywords = "D&D tools news, Dungeons and Dragons updates, new features, announcements, TTRPG news";
$author = "Dackary McDab";

// --- SECTION 2: OPEN GRAPH / SOCIAL MEDIA METADATA ---
// =====================================================
// This data is used to create rich link previews on platforms like Facebook, Discord, etc.
$og_title = "News & Updates | Dack's DND Tools";
$og_description = "The latest tidings from the Dack's DND Tools team. Stay up-to-date on all our new features and announcements.";
$og_type = "website";
$og_url = BASE_URL . "news";
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
