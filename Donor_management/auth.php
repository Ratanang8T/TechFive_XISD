<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function require_login() {
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: index.php');
        exit();
    }
}

function role_is($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function require_roles($roles) {
    require_login();
    if (!in_array($_SESSION['role'], $roles)) {
        header('Location: dashboard.php');
        exit();
    }
}

function require_admin() {
    require_roles(['Administrator']);
}

function require_staff_or_admin() {
    require_roles(['Administrator', 'Staff Member']);
}

function current_user_name() {
    return $_SESSION['full_name'] ?? 'User';
}

function current_role() {
    return $_SESSION['role'] ?? 'Guest';
}
?>
