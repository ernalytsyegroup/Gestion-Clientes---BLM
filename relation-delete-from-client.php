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
if(!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['client_id']) || empty($_GET['client_id'])) {
    header("Location: index.php");
    exit();
}

// Set relation ID
$relation->id_relacion = $_GET['id'];
$client_id = $_GET['client_id'];

// Delete relation
if($relation->delete()) {
    header("Location: client-view.php?id=" . $client_id);
} else {
    header("Location: client-view.php?id=" . $client_id);
}
exit();
?>
