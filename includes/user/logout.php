<?php
/*
    Dack's DND Tools - includes/user/logout.php
    ===========================================
    This script securely ends a user's session by destroying all session data
    and then redirects the user back to the homepage.
*/

// SECTION 1: SESSION START
// ========================
// A session must be started or resumed before it can be destroyed.
session_start();


// SECTION 2: UNSET ALL SESSION VARIABLES
// ======================================
// This immediately removes all data stored within the session, such as
// 'loggedin', 'user_id', and 'display_name'.
$_SESSION = array();


// SECTION 3: DESTROY THE SESSION COOKIE
// =====================================
// This is a best practice to ensure the session is fully terminated on the
// client-side by invalidating the session cookie in the user's browser.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}


// SECTION 4: DESTROY THE SESSION
// ==============================
// This completely destroys the session on the server-side.
session_destroy();


// SECTION 5: REDIRECT TO HOMEPAGE
// ===============================
// After the user is logged out, they are redirected back to the homepage.
// We require the core includes file to get the BASE_URL for a clean redirect.
require_once __DIR__ . '/../core/includes.php';
header("Location: " . BASE_URL . "home");
exit;

?>
