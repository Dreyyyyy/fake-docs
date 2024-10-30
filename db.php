<?php

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "fake-docs";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Conexão falhou!: " . $conn->connect_error);
}
?>
