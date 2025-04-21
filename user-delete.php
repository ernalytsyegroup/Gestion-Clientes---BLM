<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/User.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize user object
$user = new User($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: users.php");
    exit();
}

// Set user ID
$user->id_usuario = $_GET['id'];

// Delete user
if($user->delete()) {
    header("Location: users.php");
} else {
    header("Location: users.php");
}
exit();
?>
