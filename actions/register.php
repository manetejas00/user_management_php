<?php
require '../config/database.php';

// Start session to use session variables
session_start();

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Bad request", 400);
    }

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash('admin123', PASSWORD_DEFAULT); // Use admin123 as the default password
    $role = $_POST['role'];

    // Check if the email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        throw new Exception("Email already exists", 400);
    }

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, is_admin) VALUES (?, ?, ?, ?, '1')");
    $stmt->bind_param("ssss", $name, $email, $password, $role);

    if ($stmt->execute()) {
        // Return success message as JSON without redirecting
        echo json_encode(["status" => "success", "message" => "Registration successful"]);
    } else {
        throw new Exception("Error registering user", 500);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
