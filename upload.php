<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Define the directory to store uploaded files
$uploadDir = 'uploads/';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['document'])) {
    $documentName = $_FILES['document']['name'];
    $documentPath = $uploadDir . basename($documentName);

    // Move the uploaded file to the desired directory
    if (move_uploaded_file($_FILES['document']['tmp_name'], $documentPath)) {
        echo "Documento enviado com sucesso!";
    } else {
        echo "Erro ao enviar documento.";
    }

    // Redirect back to the dashboard
    header("Location: index.php");
    exit();
}
?>
