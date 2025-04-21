<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;
}

// Redirect if not logged in
function requireLogin() {
    if(!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

// Redirect if not admin
function requireAdmin() {
    if(!isAdmin()) {
        header("Location: index.php");
        exit();
    }
}

// Get current user ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// Set user session
function setUserSession($user_id, $user_name, $is_admin) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['user_name'] = $user_name;
    $_SESSION['is_admin'] = $is_admin;
}

// Clear user session
function clearUserSession() {
    session_unset();
    session_destroy();
}
?>
