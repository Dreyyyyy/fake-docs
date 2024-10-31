<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

include 'db.php'; // Include the database connection file

// Fetch documents from the database
$documents = [];
$sql = "SELECT id, file_name, file_data FROM documents WHERE user_id = ?"; // Include file_data in the SELECT statement
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $documents[] = $row; // Store each document in the array
}

$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FakeDocs - Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="dashboard-header">
        <h1>Bem-vindo ao FakeDocs, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <nav>
            <div class="nav-links">
                <a href="user.php" class="nav-link">Editar Perfil</a> <!-- Link to user.php -->
                <a href="create.php" class="nav-link create">Criar Novo Documento</a>
                <a href="logout.php" class="nav-link logout">Deslogar</a>
            </div>
        </nav>
    </header>

    <main>
        <h2>Seus Documentos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome do Documento</th>
                    <th>Conteúdo</th>
                    <th>Ação</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($documents)): ?>
                    <tr>
                        <td colspan="4">Nenhum documento encontrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($documents as $document): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($document['id']); ?></td>
                            <td><?php echo htmlspecialchars($document['file_name']); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars_decode($document['file_data']); ?></div>
                            </td>
                            <td>
                                <a href="view.php?id=<?php echo htmlspecialchars($document['id']); ?>" class="edit-button">Editar</a>
                                <form action="delete.php" method="post" style="display:inline;">
                                    <input type="hidden" name="document_id" value="<?php echo htmlspecialchars($document['id']); ?>">
                                    <button type="submit" name="delete_file" class="delete-button" onclick="return confirm('Tem certeza que deseja deletar este documento?');">Deletar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['message'])): ?>
            <p class="success"><?php echo htmlspecialchars($_SESSION['message']); ?></p>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error"><?php echo htmlspecialchars($_SESSION['error']); ?></p>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </main>
</body>
</html>
