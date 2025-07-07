<?php
/*
    Dack's DND Tools - pages/news.php
    ==================================
    This file displays a grid of news article summaries. Each summary card
    links to the full article page (article.php). The layout has been updated
    to include the site sidebar.
*/
?>

<!-- 
    SECTION 1: FLEX CONTAINER
    This is the main container that establishes the two-column layout.
-->
<div class="flex flex-col lg:flex-row gap-8">

    <!-- 
        SECTION 2: MAIN CONTENT AREA
        This div contains the primary content for the news page.
    -->
    <div class="lg:w-3/4">
        <div class="max-w-7xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

            <!-- Page Header -->
            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">News & Updates</h1>
                <p class="text-xl text-muted mt-4">The latest tidings from the Dack's DND Tools team.</p>
            </header>

            <!-- 
                Article Grid
                This section dynamically fetches articles and displays them in a responsive grid.
            -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php
                global $conn;
                $stmt = $conn->prepare("SELECT id, title, author, body, image_url, publish_date FROM dab_news ORDER BY publish_date DESC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while($article = $result->fetch_assoc()) {
                        ?>
                        <!-- Article Card -->
                        <div class="tool-card overflow-hidden flex flex-col">
                            <a href="<?php echo BASE_URL . 'article/' . htmlspecialchars($article['id']); ?>">
                                <img class="w-full h-48 object-cover" src="<?php echo htmlspecialchars($article['image_url'] ?: 'https://placehold.co/600x400/5e160c/e2e2e2?text=News'); ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                            </a>
                            <div class="p-5 flex flex-col flex-grow">
                                <h3 class="text-2xl font-bold mb-2 title-font"><?php echo htmlspecialchars($article['title']); ?></h3>
                                <p class="text-sm text-muted mb-4">
                                    By <?php echo htmlspecialchars($article['author']); ?> on 
                                    <?php 
                                        $date = new DateTime($article['publish_date']);
                                        echo $date->format('F j, Y'); 
                                    ?>
                                </p>
                                <p class="text-light flex-grow">
                                    <?php
                                        // Create a short summary from the article body.
                                        $summary = substr(strip_tags($article['body']), 0, 150);
                                        echo htmlspecialchars($summary) . (strlen($article['body']) > 150 ? '...' : '');
                                    ?>
                                </p>
                                <a href="<?php echo BASE_URL . 'article/' . htmlspecialchars($article['id']); ?>" class="action-button mt-4 text-center font-bold py-2 px-4 self-start">
                                    Read More
                                </a>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<p class="md:col-span-2 text-center text-muted">No news to report at this time. Check back soon!</p>';
                }
                $stmt->close();
                ?>
            </div>
        </div>
    </div>

    <!-- 
        SECTION 3: SIDEBAR AREA
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
