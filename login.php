<?php
session_start();

$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "fake-docs-user";

$conn = new mysqli($host, $dbUsername,
$dbPassword, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou!: " .
    $conn->connect_error);
}