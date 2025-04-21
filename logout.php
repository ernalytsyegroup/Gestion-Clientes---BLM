<?php
include_once 'utils/session.php';

// Clear session and redirect to login
clearUserSession();
header("Location: login.php");
exit();
?>
