<?php
/*
    Dack's DND Tools - api/update_profile.php
    =========================================
    This script handles updates to a user's profile information.
*/

header('Content-Type: application/json');
session_start();

// Security Check: Ensure the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'You must be logged in to update your profile.']);
    exit;
}

require_once '../functions/config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is accepted.']);
    exit;
}

// Get the raw POST data
$input = json_decode(file_get_contents('php://input'), true);

// --- Data Validation ---
if (empty($input['firstName']) || empty($input['lastName']) || empty($input['displayName'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'First name, last name, and display name cannot be empty.']);
    exit;
}

// Get user ID from the session
$user_id = $_SESSION['user_id'];
$first_name = $input['firstName'];
$last_name = $input['lastName'];
$display_name = $input['displayName'];

// --- Prepare and Execute Update ---
$stmt = $conn->prepare("UPDATE dab_account SET first_name = ?, last_name = ?, display_name = ? WHERE id = ?");
$stmt->bind_param("sssi", $first_name, $last_name, $display_name, $user_id);

if ($stmt->execute()) {
    // Update was successful
    // Update the display name in the session as well
    $_SESSION['display_name'] = $display_name;

    http_response_code(200);
    echo json_encode(['success' => 'Profile updated successfully.']);
} else {
    // Update failed
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while updating the profile.']);
}

$stmt->close();
$conn->close();

?>
