<?php
$host = "localhost";
$port = "3307"; // Replace with your custom port (default is 3306)
$username = "root";
$password = "";
$database = "user_registration";

// Create connection with custom port
$conn = new mysqli($host, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
