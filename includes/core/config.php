<?php
/*
    Dack's DND Tools - includes/core/config.php
    ==========================================
    This file establishes the connection to the MySQL database. It contains
    sensitive credentials and should be handled with care.
*/

// SECTION 1: DATABASE CREDENTIALS
// These variables hold the connection details for your database.
// It is a security best practice to load these from environment variables
// rather than hardcoding them directly in the file.
$servername = "srv1846.hstgr.io";
$username = "u971098166_dndtools";
$password = "KqlDiXPX>+q4";
$dbname = "u971098166_websitedb";


// SECTION 2: ESTABLISH CONNECTION
// A new mysqli object is created, which attempts to establish a connection
// to the database server using the credentials provided above.
$conn = new mysqli($servername, $username, $password, $dbname);


// SECTION 3: CONNECTION ERROR HANDLING
// This check is crucial for debugging. If the connection attempt fails for any
// reason (e.g., wrong password, server down), the script will terminate
// immediately and display a descriptive error message.
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// SECTION 4: SET CHARACTER SET
// This line ensures that all data sent to and retrieved from the database
// uses the 'utf8mb4' character set, which supports a wide range of characters,
// including emojis and international symbols.
$conn->set_charset("utf8mb4");

?>
