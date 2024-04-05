<?php
session_start();
$_SESSION["email"] = "";
session_destroy(); // Destroy all sessions
header("Location: login.html"); // Redirect to login page
exit();
?>
