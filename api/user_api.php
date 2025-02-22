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

try {
    // Read JSON input
    $data = json_decode(file_get_contents("php://input"), true);

    // Handle API Requests
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($data['action'])) {
        switch ($data['action']) {
            case 'add':
                // Validate required fields
                if (!isset($data['name'], $data['email'], $data['password'], $data['role'])) {
                    throw new Exception("Missing fields", 400);
                }

                // Hash password
                $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

                // Prepare SQL Query
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
                if (!$stmt) {
                    throw new Exception("Failed to prepare SQL statement", 500);
                }
                $stmt->bind_param("ssss", $data['name'], $data['email'], $hashedPassword, $data['role']);
                
                // Execute and check for success
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "User added successfully"]);
                } else {
                    throw new Exception("Failed to add user", 500);
                }
                break;

            case 'edit':
                // Validate required fields
                if (!isset($data['id'], $data['name'], $data['email'], $data['role'])) {
                    throw new Exception("Missing fields", 400);
                }

                // Prepare SQL Query
                $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ? AND deleted_at IS NULL");
                if (!$stmt) {
                    throw new Exception("Failed to prepare SQL statement", 500);
                }
                $stmt->bind_param("sssi", $data['name'], $data['email'], $data['role'], $data['id']);
                
                // Execute and check for success
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "User updated successfully"]);
                } else {
                    throw new Exception("Failed to update user", 500);
                }
                break;

            case 'delete':
                // Validate required fields
                if (!isset($data['id'])) {
                    throw new Exception("Missing fields", 400);
                }

                // Prepare SQL Query for soft delete
                $stmt = $conn->prepare("UPDATE users SET deleted_at = NOW() WHERE id = ?");
                if (!$stmt) {
                    throw new Exception("Failed to prepare SQL statement", 500);
                }
                $stmt->bind_param("i", $data['id']);
                
                // Execute and check for success
                if ($stmt->execute()) {
                    echo json_encode(["success" => true, "message" => "User deleted successfully"]);
                } else {
                    throw new Exception("Failed to delete user", 500);
                }
                break;

            default:
                throw new Exception("Invalid action", 400);
        }
    } else {
        throw new Exception("Invalid request", 400);
    }
} catch (Exception $e) {
    // Handle exceptions and return error response
    http_response_code($e->getCode() ?: 500);
    echo json_encode(["error" => $e->getMessage()]);
}