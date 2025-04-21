<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Role.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize role object
$role = new Role($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: roles.php");
    exit();
}

// Set role ID
$role->id_rol = $_GET['id'];

// Delete role
if($role->delete()) {
    header("Location: roles.php");
} else {
    header("Location: roles.php");
}
exit();
?>
