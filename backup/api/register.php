<?php
/*
    Dack's DND Tools - api/register.php
    ===================================
    This script handles new user registration. It receives user data via a POST request,
    validates it, securely hashes the password, and inserts the new user into the
    dab_account table.
*/

// Set header to return JSON
header('Content-Type: application/json');

// Use sessions to potentially log the user in immediately after registration
session_start();

// Include the database configuration
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
if (empty($input['firstName']) || empty($input['lastName']) || empty($input['displayName']) || empty($input['email']) || empty($input['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'All fields are required.']);
    exit;
}

if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email format.']);
    exit;
}

if (strlen($input['password']) < 8) {
    http_response_code(400);
    echo json_encode(['error' => 'Password must be at least 8 characters long.']);
    exit;
}


// --- Check for Existing User ---
$email = $input['email'];
$stmt = $conn->prepare("SELECT id FROM dab_account WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(['error' => 'An account with this email already exists.']);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();


// --- Create New User ---

// CRITICAL: Securely hash the password. Never store plain-text passwords.
$password_hash = password_hash($input['password'], PASSWORD_DEFAULT);

// Prepare the INSERT statement
$stmt = $conn->prepare("INSERT INTO dab_account (first_name, last_name, display_name, email, password, user_role) VALUES (?, ?, ?, ?, ?, ?)");

// Default user_role to 2 (assuming 1 is admin, 2 is standard user, etc. - adjust if needed)
$user_role_id = 2;

$stmt->bind_param("sssssi", $input['firstName'], $input['lastName'], $input['displayName'], $input['email'], $password_hash, $user_role_id);

if ($stmt->execute()) {
    // Registration successful
    http_response_code(201); // Created
    echo json_encode(['success' => 'User registered successfully.']);
} else {
    // Registration failed
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred during registration.']);
}

$stmt->close();
$conn->close();

?>
