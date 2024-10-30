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

// Handle file deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_file'])) {
    $documentId = $_POST['document_id'];

    // Prepare the statement to fetch the file name
    $sql = "SELECT file_name FROM documents WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $documentId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();

    // Check if the document exists
    if ($document) {
        $filePath = "uploads/" . $document['file_name'];
        
        // Delete the file from the file system if it exists
        if (file_exists($filePath)) {
            if (!unlink($filePath)) {
                // Handle error in deleting file from filesystem
                $_SESSION['error'] = "Failed to delete file from the server.";
                header("Location: index.php");
                exit();
            }
        }

        // Prepare the statement to delete the document from the database
        $sql = "DELETE FROM documents WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $documentId);
        $stmt->execute();

        // Check if the delete was successful
        if ($stmt->affected_rows > 0) {
            // Set a success message in session and redirect
            $_SESSION['message'] = "Document deleted successfully.";
            header("Location: index.php");
            exit();
        } else {
            // Handle error in deleting from database
            $_SESSION['error'] = "Failed to delete the document from the database.";
            header("Location: index.php");
            exit();
        }

        $stmt->close();
    } else {
        // Handle the case where the document does not exist
        $_SESSION['error'] = "Document not found.";
        header("Location: index.php");
        exit();
    }
}
?>