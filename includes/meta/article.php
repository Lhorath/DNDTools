<?php
/*
    Dack's DND Tools - /includes/meta/article.php
    =============================================
    This file dynamically generates all metadata for a single news article page.
    It fetches the article's details from the database to create unique titles,
    descriptions, and social media card information.
*/

// SECTION 1: INITIALIZE & FETCH ARTICLE DATA
// ==========================================
// Use global variables defined in includes.php to get the database connection
// and the article ID from the URL.
global $conn, $url_params;

// Set default metadata values.
$title = "Article Not Found | Dack's DND Tools";
$description = "The article you are looking for does not exist or may have been moved.";
$keywords = "D&D, Dungeons and Dragons, 5e, news, article";
$author = "Dackary McDab";
$og_url = BASE_URL . 'news';
$og_image = BASE_URL . 'style/images/og-image.jpg';
$og_image_alt = "Dack's DND Tools Logo over a fantasy map background.";

// Check if an article ID is present in the URL (e.g., /article/1).
if (isset($url_params[0]) && is_numeric($url_params[0])) {
    $article_id = (int)$url_params[0];

    // Prepare and execute a query to fetch the specific article.
    $stmt = $conn->prepare("SELECT title, author, body, image_url FROM dab_news WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($article = $result->fetch_assoc()) {
            // If the article is found, update the meta variables with its data.
            $title = htmlspecialchars($article['title']) . " | Dack's DND Tools";
            // Create a short, clean summary for the description meta tag.
            $description = htmlspecialchars(substr(strip_tags($article['body']), 0, 155)) . '...';
            $keywords .= ", " . htmlspecialchars($article['title']);
            $author = htmlspecialchars($article['author']);
            $og_url = BASE_URL . 'article/' . $article_id;
            // Use the article's image if available, otherwise fall back to the default.
            $og_image = !empty($article['image_url']) ? htmlspecialchars($article['image_url']) : $og_image;
            $og_image_alt = "Image for the article: " . htmlspecialchars($article['title']);
        }
        $stmt->close();
    }
}

// --- SECTION 2: OPEN GRAPH & TWITTER METADATA SETUP ---
// ======================================================
// Use the variables defined above to set social media card details.
$og_title = $title;
$og_description = $description;
$og_type = "article";
$og_site_name = "Dack's DND Tools";
$twitter_card = "summary_large_image";
$twitter_title = $og_title;
$twitter_description = $og_description;
$twitter_image = $og_image;
$twitter_image_alt = $og_image_alt;

?>

<!-- Standard SEO & Browser Metadata -->
<title><?php echo $title; ?></title>
<meta name="description" content="<?php echo $description; ?>">
<meta name="keywords" content="<?php echo $keywords; ?>">
<meta name="author" content="<?php echo $author; ?>">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#9f1212">

<!-- Open Graph (Facebook, Discord, etc.) -->
<meta property="og:title" content="<?php echo $og_title; ?>">
<meta property="og:description" content="<?php echo $og_description; ?>">
<meta property="og:type" content="<?php echo $og_type; ?>">
<meta property="og:url" content="<?php echo $og_url; ?>">
<meta property="og:image" content="<?php echo $og_image; ?>">
<meta property="og:image:alt" content="<?php echo $og_image_alt; ?>">
<meta property="og:site_name" content="<?php echo $og_site_name; ?>">

<!-- Twitter Card -->
<meta name="twitter:card" content="<?php echo $twitter_card; ?>">
<meta name="twitter:title" content="<?php echo $twitter_title; ?>">
<meta name="twitter:description" content="<?php echo $twitter_description; ?>">
<meta name="twitter:image" content="<?php echo $twitter_image; ?>">
<meta name="twitter:image:alt" content="<?php echo $twitter_image_alt; ?>">
