<?php
require '../config/database.php';

header('Content-Type: application/json'); // Set response type to JSON

try {
    // Get the raw input data
    $input = file_get_contents("php://input");

    // Decode the JSON data
    $data = json_decode($input, true);

    // Validate the request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['ids'])) {
        throw new Exception("Bad request: Missing or invalid data", 400);
    }

    // Log received IDs
    error_log("Received IDs: " . json_encode($data['ids']));

    // Sanitize the IDs
    $ids = array_map('intval', $data['ids']);
    if (empty($ids)) {
        throw new Exception("No valid IDs provided", 400);
    }

    // Prepare the query
    $idsString = implode(",", $ids);
    $query = "UPDATE users SET deleted_at = NOW() WHERE id IN ($idsString) AND is_admin = '0'";

    // Execute the query
    if ($conn->query($query)) {
        echo json_encode(["status" => "success", "message" => "Users deleted successfully", "ids" => $ids]);
    } else {
        throw new Exception("Database error: " . $conn->error, 500);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    error_log("Error: " . $e->getMessage()); // Log the error message
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
