<?php
/*
    Dack's DND Tools - functions/config.php
    =======================================
    This file establishes the connection to the MySQL database.
    It contains sensitive credentials required to access the data.

    CRITICAL SECURITY WARNING:
    This file should NOT be stored in a publicly accessible web directory.
    If your server is ever misconfigured, this file could be exposed,
    leaking your database password. It is highly recommended to move this file
    to a directory ABOVE your public web root (e.g., outside of 'public_html')
    and update the 'require_once' path in 'api/search.php' accordingly.
    Example: require_once __DIR__ . '/../../config/db_config.php';
*/

// --- SECTION 1: DATABASE CREDENTIALS ---
// These variables hold the connection details for your database.
// It is best practice to load these from environment variables rather than hardcoding them.
$servername = "srv1846.hstgr.io";
$username = "u971098166_dndtools";
$password = "KqlDiXPX>+q4";
$dbname = "u971098166_websitedb";

// --- SECTION 2: ESTABLISH CONNECTION ---
// A new MySQLi object is created to establish the connection to the database.
$conn = new mysqli($servername, $username, $password, $dbname);

// --- SECTION 3: CONNECTION ERROR HANDLING ---
// This checks if the connection attempt failed.
if ($conn->connect_error) {
    // If the connection fails, the script will terminate immediately (die)
    // and display an error message. This prevents further execution of scripts
    // that rely on the database, avoiding more errors.
    die("Connection failed: " . $conn->connect_error);
}

// Set character set to utf8mb4 to support a wide range of characters
$conn->set_charset("utf8mb4");

?>
