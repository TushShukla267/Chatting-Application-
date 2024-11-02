<?php
// Database connection parameters
$_servername  = 'localhost';
$_username = 'root';
$_password = 'Tushar';
$dbname = "ChatBotDb";

// Connect to MySQL server and database
$conn = new mysqli($_servername, $_username, $_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure that the database exists (this block is optional, but added to create the database if needed)
if (!mysqli_select_db($conn, $dbname)) {
    $sql = "CREATE DATABASE $dbname";
    if ($conn->query($sql) === TRUE) {
        // If the database is created, select it
        mysqli_select_db($conn, $dbname);
    } else {
        die("Error creating database: " . $conn->error);
    }
}
?>
