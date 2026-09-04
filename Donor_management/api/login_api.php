<?php
header("Content-Type: application/json");
require_once "../db.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "success" => false,
        "message" => "Invalid request method"
    ]);
    exit;
}

$email    = strtolower(trim($_POST["email"] ?? ""));
$password = trim($_POST["password"] ?? "");
$role     = trim($_POST["role"] ?? "");

if ($email === "" || $password === "" || $role === "") {
    echo json_encode([
        "success" => false,
        "message" => "Please fill in all fields"
    ]);
    exit;
}

$allowedRoles = ["Administrator", "Staff Member", "Donor"];

if (!in_array($role, $allowedRoles)) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid user role"
    ]);
    exit;
}

try {
    // FIX: this endpoint used to be an exact copy of register_api.php.
    // It never checked a password at all - it just created a brand new
    // account for any email that didn't already exist, including
    // role=Administrator. It now correctly looks the user up and
    // verifies their password instead of inserting a new row.
    $stmt = $conn->prepare("SELECT user_id, full_name, email, password_hash, role FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // FIX: declare/verify the submitted password against the hash stored
    // in the database (password_verify), and confirm the account's actual
    // stored role matches the role the caller claims to be logging in as.
    if (!$user || !password_verify($password, $user["password_hash"]) || $user["role"] !== $role) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid email, password, or user type"
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "message" => "Login successful",
        "user" => [
            "user_id"   => $user["user_id"],
            "full_name" => $user["full_name"],
            "email"     => $user["email"],
            "role"      => $user["role"]
        ]
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Login failed"
    ]);
}
?>
