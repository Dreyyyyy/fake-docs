<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Define the directory to store uploaded files
$uploadDir = 'uploads/';
$documents = [];

// Scan the uploads directory for .txt files
if (is_dir($uploadDir)) {
    $files = scandir($uploadDir);
    foreach ($files as $file) {
        if (pathinfo($file, PATHINFO_EXTENSION) === 'txt') {
            $documents[] = $file;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FakeDocs - Dashboard</title>
    <link rel="stylesheet" href="styles.css"> <!-- Linking the external stylesheet -->
</head>

<body>
    <header>
        <h1>Bem vindo ao FakeDocs, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
            <a href="logout.php">Deslogar</a>
        </nav>
    </header>
    <main>
        <h2>Seus Documentos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Documento</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="3">Nenhum documento encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documents as $index => $document): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($document); ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($uploadDir . $document); ?>" target="_blank">Visualizar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <h3>Upload de Documentos</h3>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <div class="input-group">
                <label for="document">Selecionar Documento:</label>
                <input type="file" id="document" name="document" accept=".txt" required>
            </div>
            <button type="submit">Upload</button>
        </form>
    </main>
</body>

</html>