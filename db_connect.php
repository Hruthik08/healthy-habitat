<?php
$servername = "localhost";
$username = "root";         // Default username for XAMPP/WAMP
$password = "";             // Default password (usually empty)
$dbname = "healthy_habitat";

// Create connection using MySQLi Object-Oriented
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Optional: Set charset to UTF-8
$conn->set_charset("utf8");
?>

