<?php
/*
    Dack's DND Tools - api/login.php
    ================================
    This script handles user login. It verifies credentials against the database
    and creates a session if the login is successful.
*/

// Set header to return JSON
header('Content-Type: application/json');

// Start a new session or resume the existing one
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
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Email and password are required.']);
    exit;
}

// --- Verify Credentials ---
$email = $input['email'];
$password = $input['password'];

// Prepare a statement to get the user by email
$stmt = $conn->prepare("SELECT id, display_name, password, user_role FROM dab_account WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Verify the submitted password against the stored hash
    if (password_verify($password, $user['password'])) {
        // Password is correct, so create session variables
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['user_role'] = $user['user_role'];

        // Send a success response
        http_response_code(200);
        echo json_encode([
            'success' => 'Login successful.',
            'user' => [
                'displayName' => $user['display_name']
            ]
        ]);
    } else {
        // Incorrect password
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Invalid email or password.']);
    }
} else {
    // No user found with that email
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Invalid email or password.']);
}

$stmt->close();
$conn->close();

?>
