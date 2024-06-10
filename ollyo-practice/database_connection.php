<?php

$servername = "localhost";
$username = "root";
$password = "";
$database = "inventory";

// Establish a connection to the database
$connect = mysqli_connect($servername, $username, $password, $database);

// Check the connection
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

session_start();

// Register session variables
// $_SESSION['type'] = null;  
// $_SESSION['user_id'] = null;

?>
