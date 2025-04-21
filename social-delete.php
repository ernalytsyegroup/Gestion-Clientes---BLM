<?php
// Include database and required files
include_once 'config/database.php';
include_once 'models/SocialNetwork.php';
include_once 'utils/session.php';

// Require login
requireLogin();

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Initialize social network object
$social_network = new SocialNetwork($db);

// Check if parameters are set
if(!isset($_GET['type']) || !isset($_GET['id']) || !isset($_GET['client_id'])) {
    header("Location: index.php");
    exit();
}

$type = $_GET['type'];
$id = $_GET['id'];
$client_id = $_GET['client_id'];

// Delete social network account
$success = false;

if($type === 'instagram') {
    $success = $social_network->deleteInstagram($id);
} else if($type === 'facebook') {
    $success = $social_network->deleteFacebook($id);
} else if($type === 'youtube') {
    $success = $social_network->deleteYoutube($id);
}

// Redirect back to client view
if($success) {
    header("Location: client-view.php?id=" . $client_id);
} else {
    header("Location: index.php");
}
exit();
?>
