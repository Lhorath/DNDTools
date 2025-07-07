<?php
/*
    Dack's DND Tools - includes/user/register.php
    =============================================
    This script handles new user registration. It receives user data via a POST
    request, validates it, securely hashes the password, and inserts the new
    user into the dab_account table.
*/

// SECTION 1: INITIALIZATION
// =========================
// Set the content type to application/json so the client-side JavaScript
// correctly interprets the response. Start or resume a session.
header('Content-Type: application/json');
session_start();

// Include the database configuration file to get the $conn object.
require_once __DIR__ . '/../core/config.php';

// SECTION 2: REQUEST & INPUT VALIDATION
// =====================================
// Only accept POST requests for security. Get the raw JSON POST data.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Method Not Allowed
    echo json_encode(['error' => 'Only POST method is accepted.']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

// Perform server-side validation on all required fields.
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


// SECTION 3: CHECK FOR EXISTING USER
// ==================================
// Before creating a new account, check if an account with the provided email
// already exists to prevent duplicates.
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


// SECTION 4: CREATE NEW USER
// ==========================
// If the email is unique, proceed with creating the new user account.

// Securely hash the password using PHP's built-in password hashing functions.
// Never store passwords in plain text.
$password_hash = password_hash($input['password'], PASSWORD_DEFAULT);

// Prepare the INSERT statement to add the new user to the database.
// The user_role is defaulted to a standard user role (e.g., ID 2).
$stmt = $conn->prepare("INSERT INTO dab_account (first_name, last_name, display_name, email, password, user_role) VALUES (?, ?, ?, ?, ?, ?)");
$user_role_id = 2; // Assuming 2 is the ID for a standard user role.
$stmt->bind_param("sssssi", $input['firstName'], $input['lastName'], $input['displayName'], $input['email'], $password_hash, $user_role_id);

if ($stmt->execute()) {
    // If the insertion is successful, return a success message.
    http_response_code(201); // Created
    echo json_encode(['success' => 'User registered successfully.']);
} else {
    // If the insertion fails, return a generic server error.
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => 'An error occurred during registration.']);
}

$stmt->close();
$conn->close();

?>
