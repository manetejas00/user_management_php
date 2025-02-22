<?php
require '../config/database.php';

header('Content-Type: application/json');

try {
    $input = file_get_contents("php://input");
    $data = json_decode($input, true);

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['users'])) {
        throw new Exception("Bad request: No valid users provided", 400);
    }

    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO users (name, email, role, status,password,is_admin) VALUES (?, ?, ?, 'Active','','0')");

    foreach ($data['users'] as $user) {
        // Validate inputs
        if (empty($user['name']) || empty($user['email']) || empty($user['role'])) {
            continue; // Skip invalid entries
        }

        $stmt->bind_param("sss", $user['name'], $user['email'], $user['role']);
        $stmt->execute();
    }

    echo json_encode(["status" => "success", "message" => "Users added successfully"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
