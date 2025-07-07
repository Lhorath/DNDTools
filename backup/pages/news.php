<!--
    Dack's DND Tools - pages/news.php
    ==================================
    This file fetches and displays news articles from the dab_news table.
    This version adds an ID to each article to allow for anchor linking.
-->

<!-- Main content container with consistent styling -->
<div class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">

    <!--
        PAGE HEADER
    -->
    <header class="text-center mb-10">
        <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">News & Updates</h1>
        <p class="text-xl text-muted mt-4">The latest tidings from the Dack's DND Tools team.</p>
    </header>

    <!--
        NEWS ARTICLES CONTAINER
        This section will be populated with articles from the database.
    -->
    <div class="space-y-12">
        <?php
        require_once 'functions/config.php';

        $stmt = $conn->prepare("SELECT id, title, author, body, image_url, publish_date FROM dab_news ORDER BY publish_date DESC");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while($article = $result->fetch_assoc()) {
                ?>
                <!-- Each article has an ID matching its database ID for anchor links -->
                <article class="section" id="article-<?php echo htmlspecialchars($article['id']); ?>">
                    <header>
                        <h2 class="text-4xl font-bold title-font"><?php echo htmlspecialchars($article['title']); ?></h2>
                        <p class="text-muted mt-2">
                            By <?php echo htmlspecialchars($article['author']); ?> on 
                            <?php 
                                $date = new DateTime($article['publish_date']);
                                echo $date->format('F j, Y'); 
                            ?>
                        </p>
                    </header>
                    
                    <hr class="my-4 border-gray-600">

                    <?php if (!empty($article['image_url'])): ?>
                        <img src="<?php echo htmlspecialchars($article['image_url']); ?>" alt="<?php echo htmlspecialchars($article['title']); ?> image" class="rounded-lg mb-4 w-full h-auto object-cover max-h-96">
                    <?php endif; ?>

                    <div class="prose prose-invert max-w-none text-lg text-light">
                        <?php 
                            echo nl2br(htmlspecialchars($article['body'])); 
                        ?>
                    </div>
                </article>
                <?php
            }
        } else {
            echo '<p class="text-center text-muted">No news to report at this time. Check back soon!</p>';
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>
