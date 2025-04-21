<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Plan.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize plan object
$plan = new Plan($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: planes.php");
    exit();
}

// Set plan ID
$plan->id_plan = $_GET['id'];

// Delete plan
if($plan->delete()) {
    header("Location: planes.php");
} else {
    header("Location: planes.php");
}
exit();
?>
