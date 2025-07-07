<?php
/*
    Dack's DND Tools - Core Includes
    ================================
    This file is the central initializer for the entire application. It's responsible for:
    1. Starting user sessions.
    2. Defining global constants for file paths and URLs.
    3. Handling the URL routing from the .htaccess file.
    4. Connecting to the database.
*/

// SECTION 1: SESSION MANAGEMENT
// =============================
// Start a new session or resume the existing one on every page load. This must
// be done before any HTML is outputted.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


// SECTION 2: GLOBAL CONSTANTS
// ===========================
// Define constants for clean and consistent file paths and URLs throughout the application.

// The absolute server path to the project's root directory.
// Example: /var/www/html/dnd-tools/
define('ROOT_PATH', dirname(__DIR__, 2) . '/');

// The base URL of the site, determined dynamically to work on any server.
// Example: http://localhost/dnd-tools/ or https://www.dackstools.com/
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'];
define('BASE_URL', $protocol . $domainName . '/');


// SECTION 3: URL ROUTING
// ======================
// Process the friendly URL provided by the .htaccess file. This turns a URL
// like /about into a variable that PHP can use to load the correct page.

// Get the requested URL path from the 'url' parameter set by .htaccess.
// Default to 'home' if the parameter is not set or is empty.
$request_uri = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home';
$url_parts = explode('/', $request_uri);

// The main page is the first part of the URL (e.g., 'about', 'profile').
// This is converted to lowercase to ensure case-insensitive matching.
$page = strtolower(array_shift($url_parts));

// Any additional parts of the URL are stored in an array for potential use,
// such as /news/my-article-title, where 'my-article-title' would be a parameter.
$url_params = $url_parts;


// SECTION 4: DATABASE CONNECTION
// ==============================
// Include the database configuration file. This establishes the connection
// and makes the global $conn variable available to any script that includes this file.
require_once ROOT_PATH . 'includes/core/config.php';

?>
