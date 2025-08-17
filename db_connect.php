<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "esports_tournament";

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>