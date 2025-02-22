<?php
// Database connection
$host = "localhost";
$username = "root";  // Change if using a different DB user
$password = "";      // Change if using a password
$dbname = "user_management";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Simple admin authentication credentials
$admin_username = "admin";
$admin_password = "admin123"; // Change this to a secure password
