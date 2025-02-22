<?php
require '../config/database.php';

header('Content-Type: application/json'); // Set response type to JSON

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Bad request", 400);
    }

    $id = intval($_POST['id']);
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role = trim($_POST['role']);

    if (empty($id) || empty($name) || empty($email) || empty($role)) {
        throw new Exception("All fields are required", 400);
    }

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ?, is_admin = '0' WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success", "message" => "User updated successfully"]);
    } else {
        throw new Exception("Database error: " . $conn->error, 500);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}