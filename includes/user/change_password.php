<?php
/*
    Dack's DND Tools - includes/user/change_password.php
    ====================================================
    This script securely handles password changes for logged-in users.
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
    echo json_encode(['error' => 'You must be logged in to change your password.']);
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

// Perform server-side validation on all required fields.
$currentPassword = $input['currentPassword'] ?? '';
$newPassword = $input['newPassword'] ?? '';
$confirmPassword = $input['confirmPassword'] ?? '';

if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'All password fields are required.']);
    exit;
}

if (strlen($newPassword) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'New password must be at least 8 characters long.']);
    exit;
}

if ($newPassword !== $confirmPassword) {
    http_response_code(400);
    echo json_encode(['error' => 'New passwords do not match.']);
    exit;
}

// SECTION 4: FETCH & VERIFY CURRENT PASSWORD
// ==========================================
// This section retrieves the user's current password from the database
// and verifies that the one they submitted is correct.

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT password FROM dab_account WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    http_response_code(404);
    echo json_encode(['error' => 'User not found.']);
    $conn->close();
    exit;
}

// Verify the submitted password against the securely stored hash.
if (!password_verify($currentPassword, $user['password'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Incorrect current password.']);
    $conn->close();
    exit;
}

// SECTION 5: HASH & UPDATE NEW PASSWORD
// =====================================
// If the current password was correct, hash the new password and update it
// in the database for the current user.

$newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE dab_account SET password = ? WHERE id = ?");
$stmt->bind_param("si", $newPasswordHash, $user_id);

if ($stmt->execute()) {
    // If the update is successful, return a success message.
    http_response_code(200);
    echo json_encode(['success' => 'Password updated successfully.']);
} else {
    // If the update fails, return a generic server error.
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while updating your password.']);
}

$stmt->close();
$conn->close();

?>
