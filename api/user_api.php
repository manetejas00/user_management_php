<?php
require __DIR__ . '/config.php'; // Include database connection & credentials
header('Content-Type: application/json');

// Authentication Check
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) || 
    $_SERVER['PHP_AUTH_USER'] !== $admin_username || $_SERVER['PHP_AUTH_PW'] !== $admin_password) {
    echo json_encode(["error" => "Unauthorized"]);
    http_response_code(401);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Handle API Requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action'])) {
    switch ($data['action']) {
        case 'add':
            if (!isset($data['name'], $data['email'], $data['password'], $data['role'])) {
                echo json_encode(["error" => "Missing fields"]);
                http_response_code(400);
                exit;
            }

            // Hash password
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            // Prepare SQL Query
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $data['name'], $data['email'], $hashedPassword, $data['role']);
            
            if ($stmt->execute()) {
                echo json_encode(["success" => true, "message" => "User added successfully"]);
            } else {
                echo json_encode(["error" => "Failed to add user"]);
            }
            break;

        default:
            echo json_encode(["error" => "Invalid action"]);
            http_response_code(400);
    }
} else {
    echo json_encode(["error" => "Invalid request"]);
    http_response_code(400);
}
?>
