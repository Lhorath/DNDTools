<?php
/*
    Dack's DND Tools - pages/article.php
    ====================================
    This file serves as the template for displaying a single news article.
    It retrieves the article's ID from the URL, fetches the corresponding
    data from the database, and renders the content.
*/

// SECTION 1: DATA FETCHING
// ========================
// This block retrieves the article's data from the database. It uses the global
// $url_params array (defined in includes.php) to get the article ID.

global $conn, $url_params;
$article = null;
$article_found = false;

// Check if an article ID was provided in the URL (e.g., /news/1)
if (isset($url_params[0]) && is_numeric($url_params[0])) {
    $article_id = (int)$url_params[0];

    // Prepare a statement to securely fetch the article by its ID.
    $stmt = $conn->prepare("SELECT title, author, body, image_url, publish_date FROM dab_news WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $article_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $article = $result->fetch_assoc();
            $article_found = true;
        }
        $stmt->close();
    }
}

?>

<!-- 
    SECTION 2: LAYOUT CONTAINER
    This container establishes the two-column layout for the page.
-->
<div class="flex flex-col lg:flex-row gap-8">

    <!-- 
        SECTION 3: MAIN CONTENT AREA
        This div contains the primary article content.
    -->
    <div class="lg:w-3/4">
        <?php if ($article_found): ?>
            <!-- This article container is only shown if the article was successfully found. -->
            <article>
                <!-- 
                    HERO SECTION
                    This section displays the article's featured image as a background,
                    with the title, author, and date overlaid on top.
                -->
                <header 
                    class="relative h-96 rounded-xl overflow-hidden flex items-center justify-center text-center p-4 bg-cover bg-center" 
                    style="background-image: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('<?php echo htmlspecialchars($article['image_url'] ?: 'https://placehold.co/1200x400/5e160c/e2e2e2?text=Article'); ?>');">
                    
                    <div class="text-white">
                        <h1 class="text-4xl md:text-6xl font-bold title-font"><?php echo htmlspecialchars($article['title']); ?></h1>
                        <p class="text-lg text-white/90 mt-4">
                            By <?php echo htmlspecialchars($article['author']); ?> on 
                            <?php 
                                $date = new DateTime($article['publish_date']);
                                echo $date->format('F j, Y'); 
                            ?>
                        </p>
                    </div>
                </header>

                <!-- 
                    ARTICLE BODY
                    This section displays the main content of the article.
                -->
                <div class="section mt-8">
                    <div class="prose prose-invert max-w-none text-lg text-light">
                        <?php 
                            // Using nl2br to convert newlines in the database to <br> tags for proper paragraph breaks.
                            echo nl2br(htmlspecialchars($article['body'])); 
                        ?>
                    </div>
                </div>
            </article>

        <?php else: ?>
            <!-- 
                404 ERROR MESSAGE
                This block is displayed if no article with the given ID was found.
            -->
            <?php http_response_code(404); ?>
            <div class="max-w-4xl mx-auto p-10 text-center">
                <h1 class="text-6xl title-font">404</h1>
                <p class="text-xl text-muted mt-4">Article Not Found</p>
                <p class="mt-4">The article you are looking for does not exist or may have been moved.</p>
                <a href="<?php echo BASE_URL; ?>news" class="action-button mt-6 inline-block font-bold py-2 px-6 rounded-lg">Back to News</a>
            </div>

        <?php endif; ?>
    </div>

    <!-- 
        SECTION 4: SIDEBAR AREA
        This div includes the standard site sidebar.
    -->
    <div class="lg:w-1/4">
        <?php
        if (file_exists('core/aside.php')) {
            include 'core/aside.php';
        }
        ?>
    </div>
</div>
