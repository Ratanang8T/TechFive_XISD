<?php
$host = 'localhost';
$dbname = 'donor_management_system';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// FIX: previously anyone could self-register as "Administrator" through the
// public register form/API, so anyone could then log in as an admin.
// New Administrator sign-ups must now supply this invite code, known only
// to existing admins/staff who are onboarding a new administrator.
// Change this value before deploying, and share it out of band (not in code).
define('ADMIN_INVITE_CODE', 'STC-ADMIN-2026');
?>
