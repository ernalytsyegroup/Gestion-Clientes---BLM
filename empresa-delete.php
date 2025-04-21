<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/Empresa.php';
include_once 'utils/session.php';

// Require login and admin
requireLogin();
requireAdmin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize empresa object
$empresa = new Empresa($db);

// Check if ID is set
if(!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: empresas.php");
    exit();
}

// Set empresa ID
$empresa->id_empresa = $_GET['id'];

// Delete empresa
if($empresa->delete()) {
    header("Location: empresas.php");
} else {
    header("Location: empresas.php");
}
exit();
?>
