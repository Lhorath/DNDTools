<?php
/*
    Dack's DND Tools - api/change_password.php
    ==========================================
    This script securely handles password changes for logged-in users.
*/

// Set header to return JSON and start the session.
header('Content-Type: application/json');
session_start();

// 1. SECURITY CHECK: Ensure the user is logged in.
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'You must be logged in to change your password.']);
    exit;
}

// 2. CONFIG & METHOD CHECK
require_once '../functions/config.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is accepted.']);
    exit;
}

// 3. GET INPUT & VALIDATE
$input = json_decode(file_get_contents('php://input'), true);

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

// 4. FETCH CURRENT PASSWORD HASH
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT password FROM dab_account WHERE id = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Database prepare statement failed.']);
    exit;
}
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
$currentPasswordHash = $user['password'];

// 5. VERIFY CURRENT PASSWORD
if (!password_verify($currentPassword, $currentPasswordHash)) {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Incorrect current password.']);
    $conn->close();
    exit;
}

// 6. HASH NEW PASSWORD & UPDATE DATABASE
$newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

$stmt = $conn->prepare("UPDATE dab_account SET password = ? WHERE id = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => 'Database update statement failed.']);
    $conn->close();
    exit;
}
$stmt->bind_param("si", $newPasswordHash, $user_id);

if ($stmt->execute()) {
    // Success
    http_response_code(200);
    echo json_encode(['success' => 'Password updated successfully.']);
} else {
    // Failure
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while updating your password.']);
}

$stmt->close();
$conn->close();

?>
// End of script