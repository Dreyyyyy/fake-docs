<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include the database connection file
include 'db.php';

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle file deletion from the database
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $documentId = $_POST['document_id'];

    // Prepare the statement to fetch the user ID for the document
    $sql = "SELECT user_id FROM documents WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $documentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();

    // Check if the document exists and belongs to the logged-in user
    if ($document && $document['user_id'] === $_SESSION['user_id']) {
        // Prepare the statement to delete the document from the database
        $sql = "DELETE FROM documents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $documentId);
        $stmt->execute();

        // Check if the delete was successful
        if ($stmt->affected_rows > 0) {
            // Set a success message in session and redirect
            $_SESSION['message'] = "Document deleted successfully.";
        } else {
            // Handle error in deleting from database
            $_SESSION['error'] = "Failed to delete the document from the database.";
        }

        $stmt->close();
    } else {
        // Handle the case where the document does not exist or does not belong to the user
        $_SESSION['error'] = "Document not found or you do not have permission to delete it.";
    }

    header("Location: dashboard.php");
    exit();
}
?>