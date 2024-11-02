<?php
session_start();

// Destroy all session variables and the session itself
session_unset();
session_destroy();

// Redirect to the login page or output a message if needed
header("Location: LoginPage.html");
exit();
?>
