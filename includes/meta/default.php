<?php
/*
    Dack's DND Tools - /includes/meta/default.php
    =============================================
    Fallback metadata used when a page-specific meta file does not exist.
*/

$title = "Dack's DND Tools";
$description = "A free suite of D&D 5e tools including character sheet, dice roller, compendium, and initiative tracker.";
$keywords = "D&D, Dungeons and Dragons, 5e, character sheet, dice roller, compendium, initiative tracker";
$author = "Dackary McDab";
$og_url = BASE_URL . 'home';
$og_image = BASE_URL . 'style/images/og-image.jpg';
$og_image_alt = "Dack's DND Tools Logo over a fantasy map background.";
$og_type = "website";
$og_site_name = "Dack's DND Tools";
$twitter_card = "summary_large_image";
?>

<title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="keywords" content="<?php echo htmlspecialchars($keywords, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="author" content="<?php echo htmlspecialchars($author, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#9f1212">

<meta property="og:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:type" content="<?php echo htmlspecialchars($og_type, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($og_url, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image:alt" content="<?php echo htmlspecialchars($og_image_alt, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:site_name" content="<?php echo htmlspecialchars($og_site_name, ENT_QUOTES, 'UTF-8'); ?>">

<meta name="twitter:card" content="<?php echo htmlspecialchars($twitter_card, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:title" content="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($og_image, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image:alt" content="<?php echo htmlspecialchars($og_image_alt, ENT_QUOTES, 'UTF-8'); ?>">
