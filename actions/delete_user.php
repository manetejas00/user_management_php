<?php
require '../config/database.php';

header('Content-Type: application/json'); // Set response type to JSON

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
        throw new Exception("Bad request", 400);
    }

    $id = intval($_POST['id']);
    $query = "UPDATE users SET deleted_at = NOW() WHERE id = $id AND is_admin = '0'";

    if ($conn->query($query)) {
        echo json_encode(["status" => "success", "message" => "User deleted successfully"]);
    } else {
        throw new Exception("Database error: " . $conn->error, 500);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}