<?php
/*
    Dack's DND Tools - pages/profile.php
    ====================================
    This file displays the logged-in user's profile information and allows
    them to edit their details or change their password. The layout has been
    updated to a two-column design with the main content on the left and a
    sidebar on the right.
*/

// SECTION 1: SECURITY CHECK & DATA FETCH
// ======================================
// This block ensures that only logged-in users can access this page. If a guest
// tries to view it, they are redirected to the login page. It then fetches all
// necessary data for the currently logged-in user from the database.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: " . BASE_URL . "login");
    exit;
}

global $conn;
$stmt = $conn->prepare("SELECT a.first_name, a.last_name, a.display_name, a.email, a.created_at, r.name as role_name FROM dab_account a LEFT JOIN dab_roles r ON a.user_role = r.id WHERE a.id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

?>

<!-- 
    SECTION 2: FLEX CONTAINER
    This is the main container that establishes the two-column layout.
    It's a column on mobile and switches to a row on large screens (lg:).
-->
<div class="flex flex-col lg:flex-row gap-8">

    <!-- 
        SECTION 3: MAIN CONTENT AREA
        This div takes up 3/4 of the width on large screens and contains the
        primary content for the "Profile" page, including all the interactive tabs.
    -->
    <div class="lg:w-3/4">
        <div id="profile-page" class="max-w-4xl mx-auto p-4 md:p-10 rounded-xl shadow-2xl" style="background-color: rgba(29, 29, 29, 0.9); backdrop-filter: blur(2px);">
            <!-- Page Header -->
            <header class="text-center mb-10">
                <h1 class="text-6xl md:text-7xl font-bold" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.2);">
                    <?php echo htmlspecialchars($user['display_name']); ?>'s Profile
                </h1>
                <p class="text-xl text-muted mt-4">Your personal corner of the tavern.</p>
            </header>

            <!-- Tabs for View/Edit/Password -->
            <div class="mb-6 border-b-2 border-border-color">
                <nav class="flex flex-wrap -mb-px space-x-4" aria-label="Tabs">
                    <button id="view-tab" class="sheet-tab active-sheet-tab" data-target="view-panel">View Profile</button>
                    <button id="edit-tab" class="sheet-tab" data-target="edit-panel">Edit Profile</button>
                    <button id="password-tab" class="sheet-tab" data-target="password-panel">Change Password</button>
                </nav>
            </div>

            <!-- This div is used by JavaScript to display success or error messages -->
            <div id="form-message" class="hidden p-4 mb-6 rounded-md text-center"></div>

            <!-- View Panel -->
            <div id="view-panel" class="section">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div><label>Display Name</label><p class="profile-display-name text-xl p-3 bg-bg-main rounded-lg border border-border-color"><?php echo htmlspecialchars($user['display_name']); ?></p></div>
                    <div><label>Role</label><p class="text-xl p-3 bg-bg-main rounded-lg border border-border-color"><?php echo htmlspecialchars(ucfirst($user['role_name'])); ?></p></div>
                    <div><label>Full Name</label><p class="profile-full-name text-xl p-3 bg-bg-main rounded-lg border border-border-color"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p></div>
                    <div><label>Email Address</label><p class="text-xl p-3 bg-bg-main rounded-lg border border-border-color"><?php echo htmlspecialchars($user['email']); ?></p></div>
                    <div class="md:col-span-2"><label>Member Since</label><p class="text-xl p-3 bg-bg-main rounded-lg border border-border-color"><?php $joinDate = new DateTime($user['created_at']); echo $joinDate->format('F j, Y'); ?></p></div>
                </div>
            </div>
            
            <!-- Edit Panel (Initially hidden) -->
            <div id="edit-panel" class="hidden section">
                <form id="edit-profile-form" novalidate>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                        <div>
                            <label for="firstName">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                         <div>
                            <label for="lastName">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="displayName">Display Name</label>
                            <input type="text" id="displayName" name="displayName" value="<?php echo htmlspecialchars($user['display_name']); ?>" required>
                        </div>
                        <div class="md:col-span-2">
                            <label for="email">Email Address (Cannot be changed)</label>
                            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled class="text-muted">
                        </div>
                        <div class="md:col-span-2 text-right pt-4">
                            <button type="submit" class="action-button font-bold py-3 px-8 rounded-lg text-xl title-font">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Password Panel (Initially hidden) -->
            <div id="password-panel" class="hidden section">
                <form id="change-password-form" novalidate>
                    <div class="space-y-6">
                        <div>
                            <label for="currentPassword">Current Password</label>
                            <input type="password" id="currentPassword" name="currentPassword" required>
                        </div>
                        <div>
                            <label for="newPassword">New Password</label>
                            <input type="password" id="newPassword" name="newPassword" required>
                            <p class="text-xs text-muted mt-1">Minimum 8 characters.</p>
                        </div>
                        <div>
                            <label for="confirmPassword">Confirm New Password</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" required>
                        </div>
                        <div class="text-right pt-4">
                             <button type="submit" class="action-button font-bold py-3 px-8 rounded-lg text-xl title-font">
                                Update Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 
        SECTION 4: SIDEBAR AREA
        This div takes up 1/4 of the width on large screens. It includes the
        aside.php file to display recent news and quick links.
    -->
    <div class="lg:w-1/4">
        <?php
        // Include the sidebar. The path is relative to the root index.php.
        if (file_exists('core/aside.php')) {
            include 'core/aside.php';
        }
        ?>
    </div>
</div>
