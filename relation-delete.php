<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Relation.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize relation object
$relation = new Relation($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['user_id']) || empty($_GET['user_id'])) {
    header("Location: users.php");
    exit();
}

// Set relation ID
$relation->id_relacion = $_GET['id'];
$user_id = $_GET['user_id'];

// Delete relation
if($relation->delete()) {
    header("Location: user-view.php?id=" . $user_id);
} else {
    header("Location: user-view.php?id=" . $user_id);
}
exit();
?>
