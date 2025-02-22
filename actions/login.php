<?php
session_start();
require '../config/database.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Bad request", 400);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ? AND status = 'Active' AND is_admin = '1'");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $name, $hashed_password);

    if ($stmt->num_rows > 0) {
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user'] = ['id' => $id, 'name' => $name];
            header("Location: ../public/dashboard.php"); // Redirect to dashboard
            exit; // Ensure no further code is executed after the redirect
        } else {
            throw new Exception("Invalid credentials", 401);
        }
    } else {
        throw new Exception("Invalid credentials", 401);
    }
} catch (Exception $e) {
    http_response_code($e->getCode());
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
