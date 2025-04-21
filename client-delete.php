<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Client.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin(); // Solo administradores pueden eliminar clientes

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize client object
$client = new Client($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit();
}

// Set client ID
$client->id_cliente = $_GET['id'];

// Check if client exists and user has access
if(!$client->readOne(getCurrentUserId(), isAdmin())) {
    header("Location: index.php");
    exit();
}

// Delete client
if($client->delete()) {
    header("Location: index.php");
} else {
    header("Location: index.php");
}
exit();
?>
