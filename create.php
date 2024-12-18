<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db.php'; // Include the database connection file

// Handle file creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_file'])) {
    $fileName = $_POST['file_name'];
    $fileContent = $_POST['file_content']; // This will contain HTML now

    // Debug: Log the file name and content
    error_log("File Name: " . $fileName);
    error_log("File Content: " . $fileContent);

    // Insert file info into the database
    $sql = "INSERT INTO documents (user_id, file_name, file_data, upload_date) VALUES (?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("SQL Prepare Error: " . $conn->error);
        $_SESSION['error'] = "Falha ao preparar a consulta.";
        header("Location: dashboard.php");
        exit();
    }

    // Bind parameters: user_id, file_name, file_content
    $stmt->bind_param("iss", $_SESSION['user_id'], $fileName, $fileContent);
    if ($stmt->execute() === false) {
        error_log("SQL Execute Error: " . $stmt->error);
        $_SESSION['error'] = "Falha ao inserir o documento no banco de dados.";
        $stmt->close();
        header("Location: dashboard.php");
        exit();
    }

    $stmt->close();

    // Redirect back to the dashboard with a success message
    $_SESSION['message'] = "Documento criado com sucesso.";
    header("Location: dashboard.php");
    exit();
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
        <nav class="nav-links"> <!-- Add nav-links class -->
            <a href="dashboard.php" class="dashboard">Voltar ao Dashboard</a>
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

        <!-- Display error message if it exists -->
        <?php if (isset($_SESSION['error'])): ?>
            <p style="color: red;"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        <?php endif; ?>
        <!-- Display success message if it exists -->
        <?php if (isset($_SESSION['message'])): ?>
            <p style="color: green;"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>
    </main>
</body>
</html>