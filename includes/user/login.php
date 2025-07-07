<?php
/*
    Dack's DND Tools - includes/user/login.php
    ==========================================
    This script handles user login attempts. It receives credentials via a POST
    request, verifies them against the database, and creates a session if the
    login is successful.
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

// Perform basic validation to ensure fields are not empty.
if (empty($input['email']) || empty($input['password'])) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Email and password are required.']);
    exit;
}


// SECTION 3: VERIFY USER CREDENTIALS
// ==================================
// This is the core logic for authenticating the user.

$email = $input['email'];
$password = $input['password'];

// Prepare a statement to fetch the user by their email address.
$stmt = $conn->prepare("SELECT id, display_name, password, user_role FROM dab_account WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // A user with the provided email was found.
    $user = $result->fetch_assoc();

    // Verify the submitted password against the securely stored hash from the database.
    if (password_verify($password, $user['password'])) {
        // If the password is correct, create session variables to log the user in.
        $_SESSION['loggedin'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['display_name'] = $user['display_name'];
        $_SESSION['user_role'] = $user['user_role'];

        // Send a success response back to the client.
        http_response_code(200);
        echo json_encode([
            'success' => 'Login successful.',
            'user' => [
                'displayName' => $user['display_name']
            ]
        ]);
    } else {
        // The password was incorrect.
        http_response_code(401); // Unauthorized
        echo json_encode(['error' => 'Invalid email or password.']);
    }
} else {
    // No user was found with that email address.
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Invalid email or password.']);
}

$stmt->close();
$conn->close();

?>
