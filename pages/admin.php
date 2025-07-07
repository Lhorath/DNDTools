<?php
/*
    Dack's DND Tools - pages/admin.php
    ==================================
    This file contains the logic and HTML for the admin control panel.
    The layout has been updated to use the site's standard two-column
    layout with a main content area and a sidebar.
*/

// SECTION 1: SECURITY & ROUTING
// =============================
// This check is performed first. If the user is not a logged-in Webmaster,
// the script will redirect them before any of this page is rendered.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['user_role']) || $_SESSION['user_role'] != 3) {
    header("Location: " . BASE_URL);
    exit;
}

// Get global variables defined in includes.php to be used in this file.
global $conn, $csrf_token;
$current_view = isset($_GET['view']) ? $_GET['view'] : 'stats';

?>
<!-- 
    SECTION 2: ADMIN PANEL LAYOUT
    This container establishes the two-column layout for the admin panel using Flexbox,
    matching the layout of other pages on the site.
-->
<div class="flex flex-col lg:flex-row gap-8">
    
    <!-- 
        SECTION 3: MAIN CONTENT AREA
        This main element takes up 3/4 of the width on large screens and displays the content
        for the currently selected admin view.
    -->
    <main class="lg:w-3/4">
        
        <!-- This area is reserved for displaying "flash messages" -->
        <?php if(isset($_SESSION['flash_message'])): ?>
        <div class="bg-green-800 border border-green-600 text-white px-4 py-3 rounded-lg relative mb-4" role="alert">
            <span class="block sm:inline"><?php echo $_SESSION['flash_message']; ?></span>
        </div>
        <?php unset($_SESSION['flash_message']); endif; ?>

        <?php
        // This switch statement acts as a router for the admin panel's content area.
        switch($current_view):
            
            // --- Dashboard View ---
            case 'stats':
                // Fetch statistics from the database
                $total_users = $conn->query("SELECT COUNT(*) as count FROM dab_account")->fetch_assoc()['count'];
                $total_articles = $conn->query("SELECT COUNT(*) as count FROM dab_news")->fetch_assoc()['count'];
                $total_roles = $conn->query("SELECT COUNT(*) as count FROM dab_roles")->fetch_assoc()['count'];
                ?>
                <div class="section">
                    <h1 class="text-4xl title-font"><i class="fas fa-chart-line"></i> Dashboard</h1>
                    <p class="text-lg text-muted">Overview of site statistics.</p>
                    <hr class="my-4 border-border-color">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="panel text-center p-4"><h5>Total Users</h5><p class="text-4xl font-bold title-font"><?php echo $total_users; ?></p></div>
                        <div class="panel text-center p-4"><h5>News Articles</h5><p class="text-4xl font-bold title-font"><?php echo $total_articles; ?></p></div>
                        <div class="panel text-center p-4"><h5>User Roles</h5><p class="text-4xl font-bold title-font"><?php echo $total_roles; ?></p></div>
                    </div>
                </div>
                <?php
                break;

            // --- News Management View ---
            case 'news':
                ?>
                <div class="section">
                    <h1 class="text-4xl title-font"><i class="fas fa-newspaper"></i> News Management</h1>
                    <p class="text-lg text-muted">Add, edit, or delete site news articles.</p>
                </div>
                <div class="section mt-6">
                    <h2 class="text-2xl title-font">Add New Article</h2>
                    <p class="text-muted mt-2">Form will go here.</p>
                </div>
                <div class="section mt-6">
                    <h2 class="text-2xl title-font">Manage Articles</h2>
                    <p class="text-muted mt-2">Paginated table will go here.</p>
                </div>
                <?php
                break;

            // --- User Management View ---
            case 'users':
                ?>
                <div class="section">
                    <h1 class="text-4xl title-font"><i class="fas fa-users"></i> User Management</h1>
                    <p class="text-lg text-muted">Manage user roles and assignments.</p>
                </div>
                <div class="section mt-6">
                    <h2 class="text-2xl title-font">Manage Roles</h2>
                    <p class="text-muted mt-2">Table of roles will go here.</p>
                </div>
                <div class="section mt-6">
                    <h2 class="text-2xl title-font">Assign Roles to Users</h2>
                    <p class="text-muted mt-2">Paginated user list with role assignments will go here.</p>
                </div>
                <?php
                break;
            
            // --- Default View (Error) ---
            default:
                echo "<div class='section'><h1 class='text-4xl title-font'>Invalid View</h1><p>Please select a valid section from the sidebar.</p></div>";
                break;
        endswitch;
        ?>
    </main>

    <!-- 
        SECTION 4: SIDEBAR AREA
        This div takes up 1/4 of the width on large screens and includes the
        standard site sidebar.
    -->
    <div class="lg:w-1/4">
        <?php
        if (file_exists('core/aside.php')) {
            include 'core/aside.php';
        }
        ?>
    </div>
</div>
