<?php
/*
    Dack's DND Tools - core/aside.php
    =================================
    This file serves as a sidebar. It displays a dynamic user profile card,
    an admin menu for authorized users, recent news articles, and quick
    navigation links to the main tools.
*/
?>

<aside class="space-y-8">
    <!-- 
        SECTION 1: MEMBER PROFILE
        =========================
        This section dynamically displays either the logged-in user's profile
        information (with a Gravatar image) or login/register buttons for guests.
    -->
    <div class="section">
        <h3 class="section-title text-center">Profile</h3>
        <hr class="my-4 border-gray-600">
        <div class="px-4 pt-4">

            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): 
            
                // --- Gravatar & User Data Logic ---
                $email = '';
                $created_at = '';
                $role_name = '';
                global $conn;
                if ($conn && !$conn->connect_error) {
                    $user_stmt = $conn->prepare("
                        SELECT a.email, a.created_at, r.name as role_name 
                        FROM dab_account a
                        LEFT JOIN dab_roles r ON a.user_role = r.id
                        WHERE a.id = ?
                    ");
                    if ($user_stmt) {
                        $user_stmt->bind_param("i", $_SESSION['user_id']);
                        $user_stmt->execute();
                        $user_result = $user_stmt->get_result();
                        if ($user_data = $user_result->fetch_assoc()) {
                            $email = $user_data['email'];
                            $created_at = $user_data['created_at'];
                            $role_name = $user_data['role_name'];
                        }
                        $user_stmt->close();
                    }
                }
                
                $gravatar_hash = md5(strtolower(trim($email)));
                $default_initial = htmlspecialchars(strtoupper(substr($_SESSION['display_name'], 0, 1)));
                $default_image_url = urlencode("https://placehold.co/96x96/5e160c/e2e2e2?text={$default_initial}");
                $gravatar_url = "https://www.gravatar.com/avatar/{$gravatar_hash}?s=96&d={$default_image_url}";
            ?>

                <!-- Logged-In User View -->
                <div class="flex flex-col items-center pb-10 text-center">
                    <img class="w-24 h-24 mb-3 rounded-full shadow-lg" src="<?php echo $gravatar_url; ?>" alt="User Avatar"/>
                    <h5 class="mb-1 text-xl font-medium text-white"><?php echo htmlspecialchars($_SESSION['display_name']); ?></h5>
                    
                    <?php if ($role_name): ?>
                        <span class="text-sm text-muted capitalize"><?php echo htmlspecialchars($role_name); ?></span>
                    <?php endif; ?>
                    <?php if ($created_at): ?>
                        <span class="text-xs text-muted mt-1">
                            Joined <?php $joinDate = new DateTime($created_at); echo $joinDate->format('M Y'); ?>
                        </span>
                    <?php endif; ?>

                    <div class="flex mt-4 md:mt-6">
                        <a href="<?php echo BASE_URL; ?>profile" class="action-button inline-flex items-center px-4 py-2 text-sm font-medium text-center">
                            <i class="fas fa-user-circle fa-fw me-2"></i>Profile
                        </a>
                        <a href="<?php echo BASE_URL; ?>includes/user/logout.php" class="action-button-secondary inline-flex items-center ms-2 px-4 py-2 text-sm font-medium text-center">
                            <i class="fas fa-sign-out-alt fa-fw me-2"></i>Logout
                        </a>
                    </div>
                </div>

            <?php else: ?>

                <!-- Guest View -->
                <div class="text-center pb-4">
                    <p class="text-muted mb-4">Log in to save your characters and access more features!</p>
                    <div class="flex justify-center mt-4 md:mt-6">
                         <a href="<?php echo BASE_URL; ?>login" class="action-button inline-flex items-center px-4 py-2 text-sm font-medium text-center">
                            <i class="fas fa-sign-in-alt fa-fw me-2"></i>Login
                         </a>
                         <a href="<?php echo BASE_URL; ?>register" class="action-button-secondary inline-flex items-center ms-2 px-4 py-2 text-sm font-medium text-center">
                            <i class="fas fa-user-plus fa-fw me-2"></i>Sign Up
                         </a>
                    </div>
                </div>

            <?php endif; ?>

        </div>
    </div>
    
    <!-- 
        SECTION 2: ADMIN MENU (CONDITIONAL)
        ===================================
        This menu only appears if the logged-in user has the Webmaster role (ID 3).
    -->
    <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 3): ?>
    <div class="section">
        <h3 class="section-title text-center">Admin Menu</h3>
        <hr class="my-4 border-gray-600">
        <nav class="flex flex-col space-y-1">
            <?php 
            global $page;
            $current_admin_view = '';
            if ($page === 'admin') {
                $current_admin_view = isset($_GET['view']) ? $_GET['view'] : 'stats';
            }
            ?>
            <a href="<?php echo BASE_URL; ?>admin?view=stats" class="admin-nav-link <?php if($current_admin_view === 'stats') echo 'active'; ?>">
                <i class="fa-fw fas fa-chart-line"></i> Dashboard
            </a>
            <a href="<?php echo BASE_URL; ?>admin?view=news" class="admin-nav-link <?php if($current_admin_view === 'news') echo 'active'; ?>">
                <i class="fa-fw fas fa-newspaper"></i> News Management
            </a>
            <a href="<?php echo BASE_URL; ?>admin?view=users" class="admin-nav-link <?php if($current_admin_view === 'users') echo 'active'; ?>">
                <i class="fa-fw fas fa-users"></i> User Management
            </a>
        </nav>
    </div>
    <?php endif; ?>
    
    <!-- 
        SECTION 3: RECENT NEWS
        ========================
        This section dynamically fetches the three most recent articles.
    -->
    <div class="section">
        <h3 class="section-title text-center">Recent News</h3>
        <hr class="my-4 border-gray-600">
        <div class="space-y-4">
            <?php
            if (!isset($conn) || !$conn || $conn->connect_error) {
                require ROOT_PATH . 'includes/core/config.php';
            }
            
            if (isset($conn) && !$conn->connect_error) {
                $news_stmt = $conn->prepare("SELECT id, title, image_url, publish_date FROM dab_news ORDER BY publish_date DESC LIMIT 3");
                if ($news_stmt) {
                    $news_stmt->execute();
                    $news_result = $news_stmt->get_result();
                    if ($news_result->num_rows > 0) {
                        echo '<ul class="space-y-2">';
                        while($row = $news_result->fetch_assoc()) {
                            $date = new DateTime($row['publish_date']);
                            $image_url = !empty($row['image_url']) ? htmlspecialchars($row['image_url']) : 'https://placehold.co/32x32/5e160c/e2e2e2?text=N';
                            echo '<li>';
                            echo '<a href="' . BASE_URL . 'article/' . htmlspecialchars($row['id']) . '" class="flex items-center p-2 rounded-md hover:bg-accent-gold transition-colors">';
                            echo '<img src="' . $image_url . '" alt="Article thumbnail" class="w-8 h-8 rounded object-cover mr-3 flex-shrink-0">';
                            echo '<div>';
                            echo '<span class="font-bold text-light leading-tight">' . htmlspecialchars($row['title']) . '</span>';
                            echo '<span class="block text-xs text-muted">' . $date->format('F j, Y') . '</span>';
                            echo '</div>';
                            echo '</a>';
                            echo '</li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<p class="text-muted text-center">No recent news.</p>';
                    }
                    $news_stmt->close();
                }
            } else {
                echo '<p class="text-muted text-center">Could not connect to fetch news.</p>';
            }
            ?>
        </div>
    </div>

    <!-- 
        SECTION 4: QUICK LINKS
        ======================
        This section provides navigation to the primary tools on the site.
    -->
    <div class="section">
        <h3 class="section-title text-center">Quick Links</h3>
        <hr class="my-4 border-gray-600">
        <nav class="flex flex-col space-y-1">
            <a href="<?php echo BASE_URL; ?>sheet" class="admin-nav-link">
                <i class="fa-fw fas fa-scroll"></i> Character Sheet
            </a>
            <a href="<?php echo BASE_URL; ?>dice" class="admin-nav-link">
                <i class="fa-fw fas fa-dice-d20"></i> Dice Roller
            </a>
            <a href="<?php echo BASE_URL; ?>compendium" class="admin-nav-link">
                <i class="fa-fw fas fa-book-open"></i> Compendium
            </a>
            <a href="<?php echo BASE_URL; ?>initiative" class="admin-nav-link">
                <i class="fa-fw fas fa-list-ol"></i> Initiative Tracker
            </a>
        </nav>
    </div>
</aside>
