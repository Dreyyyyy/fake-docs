<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include the database connection file

// Fetch document content based on the document ID
if (isset($_GET['id'])) {
    $documentId = $_GET['id'];

    $sql = "SELECT file_name, file_data FROM documents WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $documentId, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $document = $result->fetch_assoc();

    $stmt->close();

    if (!$document) {
        // Handle document not found
        $_SESSION['error'] = "Documento não encontrado.";
        header("Location: dashboard.php");
        exit();
    }
} else {
    // Redirect if no ID is provided
    header("Location: dashboard.php");
    exit();
}

// Handle the saving of the document
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_file'])) {
    $updatedContent = $_POST['document_content'];

    // Update the document in the database
    $updateSql = "UPDATE documents SET file_data = ? WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->bind_param("si", $updatedContent, $documentId);
    
    if ($updateStmt->execute()) {
        // Save the content to the file
        $filePath = "uploads/" . $document['file_name'];
        file_put_contents($filePath, $updatedContent);

        $_SESSION['message'] = "Documento atualizado com sucesso.";
    } else {
        $_SESSION['error'] = "Erro ao atualizar o documento.";
    }

    $updateStmt->close();
    header("Location: view.php?id=" . $documentId);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Documento</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/64nc1k1n7fcumkg38hgn8zs1wv9h2iskw5bfwgi25tsslabw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#document_content', // Targeting the textarea to replace with TinyMCE
                plugins: 'lists link image table code',
                toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code',
                height: 600 // Set height for viewing
            });
        });
    </script>
</head>
<body>
    <header>
        <h1>Visualizar Documento</h1>
        <nav>
            <a href="dashboard.php">Voltar para o painel</a>
        </nav>
    </header>
    <main>
        <h2><?php echo htmlspecialchars($document['file_name']); ?></h2>
        <form method="post">
            <textarea id="document_content" name="document_content"><?php echo htmlspecialchars($document['file_data']); ?></textarea>
            <button type="submit" name="save_file">Salvar Alterações</button>
        </form>
    </main>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <p class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <p class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
</body>
</html>