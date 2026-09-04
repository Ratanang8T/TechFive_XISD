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

$full_name  = trim($_POST["full_name"] ?? "");
$email      = strtolower(trim($_POST["email"] ?? ""));
$password   = trim($_POST["password"] ?? "");
$role       = trim($_POST["role"] ?? "");
$invite_code = trim($_POST["admin_invite_code"] ?? "");

if ($full_name === "" || $email === "" || $password === "" || $role === "") {
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

// FIX: anyone could previously self-register as "Administrator" with no
// restriction at all, which meant anyone could then log in as an admin.
// Administrator sign-ups now require a valid invite code.
if ($role === "Administrator" && $invite_code !== ADMIN_INVITE_CODE) {
    echo json_encode([
        "success" => false,
        "message" => "A valid admin invite code is required to register as Administrator"
    ]);
    exit;
}

try {
    // FIX: this was "SELECT id FROM users" - the users table's primary key
    // is actually "user_id", so this query always threw an error and the
    // duplicate-email check silently failed (caught below as "Registration
    // failed" for every request).
    $check = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        echo json_encode([
            "success" => false,
            "message" => "Email already registered"
        ]);
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        INSERT INTO users (full_name, email, password_hash, role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $full_name,
        $email,
        $password_hash,
        $role
    ]);

    $users_id = $conn->lastInsertId();

    // Keep donors table in sync, matching register.php's web behaviour.
    if ($role === "Donor") {
        $phone = trim($_POST["phone"] ?? "");
        $address = trim($_POST["address"] ?? "");
        $stmt = $conn->prepare("
            INSERT INTO donors (user_id, name, email, phone, address)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$users_id, $full_name, $email, $phone, $address]);
    }

    echo json_encode([
        "success" => true,
        "message" => "Account created successfully"
    ]);

} catch (Exception $e) {
    echo json_encode([
        "success" => false,
        "message" => "Registration failed"
    ]);
}
?>
