<?php
/*
    Dack's DND Tools - includes/user/update_profile.php
    ===================================================
    This script securely handles updates to a logged-in user's profile
    information, such as their name and display name.
*/

// SECTION 1: INITIALIZATION
// =========================
// Set the content type to application/json so the client-side JavaScript
// correctly interprets the response. Start or resume a session.
header('Content-Type: application/json');
session_start();


// SECTION 2: SECURITY & ACCESS CONTROL
// ====================================
// This check ensures that only authenticated users can access this script.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'You must be logged in to update your profile.']);
    exit;
}

// Include the database configuration file to get the $conn object.
require_once __DIR__ . '/../core/config.php';


// SECTION 3: REQUEST & INPUT VALIDATION
// =====================================
// Only accept POST requests for security. Get the raw JSON POST data.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is accepted.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Perform server-side validation to ensure required fields are not empty.
if (empty($input['firstName']) || empty($input['lastName']) || empty($input['displayName'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'First name, last name, and display name cannot be empty.']);
    exit;
}


// SECTION 4: PREPARE AND EXECUTE UPDATE
// =====================================
// This is the core logic that updates the user's data in the database.

// Get the user's ID from the session to ensure they can only update their own profile.
$user_id = $_SESSION['user_id'];
$first_name = $input['firstName'];
$last_name = $input['lastName'];
$display_name = $input['displayName'];

// Prepare the UPDATE statement to modify the user's data.
$stmt = $conn->prepare("UPDATE dab_account SET first_name = ?, last_name = ?, display_name = ? WHERE id = ?");
$stmt->bind_param("sssi", $first_name, $last_name, $display_name, $user_id);

if ($stmt->execute()) {
    // If the update is successful, also update the display name in the session
    // so it appears correctly in the header immediately without needing a re-login.
    $_SESSION['display_name'] = $display_name;
    
    http_response_code(200);
    echo json_encode(['success' => 'Profile updated successfully.']);
} else {
    // If the update fails, return a generic server error.
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while updating the profile.']);
}

$stmt->close();
$conn->close();

?>
