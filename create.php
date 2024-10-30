<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'db.php'; // Include the database connection file

// Handle file creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_file'])) {
    $fileName = $_POST['file_name'];
    $fileContent = $_POST['file_content']; // This will contain HTML now

    // Path where the file will be created (make sure this path is writable)
    $filePath = "uploads/" . basename($fileName); // Ensure you have a folder named 'uploads'

    // Create the file with the specified content
    if (file_put_contents($filePath, $fileContent) !== false) {
        // Insert file info into the database
        $sql = "INSERT INTO documents (user_id, file_name, file_data, upload_date) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $_SESSION['user_id'], $fileName, $fileContent);
        $stmt->execute();
        $stmt->close();

        // Redirect back to the dashboard with a success message
        $_SESSION['message'] = "Documento criado com sucesso.";
        header("Location: index.php");
        exit();
    } else {
        // Handle error in file creation
        $_SESSION['error'] = "Falha ao criar o documento.";
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Documento</title>
    <link rel="stylesheet" href="styles.css">
    <!-- Include TinyMCE -->
    <script src="https://cdn.tiny.cloud/1/64nc1k1n7fcumkg38hgn8zs1wv9h2iskw5bfwgi25tsslabw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea', // Targeting the textarea to replace with TinyMCE
            plugins: 'lists link image table code',
            toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist | link image | code',
            height: 300,
            setup: function (editor) {
                // Ensure the content is saved back to the textarea on change
                editor.on('change', function () {
                    editor.save(); // This will update the underlying textarea
                });
            }
        });
        
        // Validate form before submission
        function validateForm() {
            const content = tinymce.get('file_content').getContent(); // Get content from TinyMCE
            if (!content.trim()) { // Check if content is empty
                alert('Por favor, preencha o conteúdo do documento.');
                return false; // Prevent form submission
            }
            return true; // Allow form submission
        }
    </script>
</head>
<body>
    <header>
        <h1>Criar Novo Documento</h1>
        <nav>
            <a href="logout.php">Deslogar</a>
        </nav>
    </header>
    <main>
        <form action="" method="post" onsubmit="return validateForm();">
            <div class="input-group">
                <label for="file_name">Nome do Documento:</label>
                <input type="text" id="file_name" name="file_name" required>
            </div>
            <div class="input-group">
                <label for="file_content">Conteúdo do Documento:</label>
                <textarea id="file_content" name="file_content" rows="4" required></textarea>
            </div>
            <button type="submit" name="create_file">Criar</button>
        </form>
        <p><a href="index.php">Voltar para o painel</a></p>
    </main>
</body>
</html>