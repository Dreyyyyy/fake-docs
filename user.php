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

// Fetch user data
$sql = "SELECT id, username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Update user data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = trim($_POST['username']);
    $new_password = trim($_POST['password']);

    // Validate input
    if (empty($new_username)) {
        $_SESSION['error'] = "O nome de usuário não pode estar vazio.";
    } else {
        // Update the database
        $update_sql = "UPDATE users SET username = ?, password = ? WHERE id = ?";
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the password

        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $new_username, $hashed_password, $_SESSION['user_id']);

        if ($update_stmt->execute()) {
            $_SESSION['message'] = "Dados do usuário atualizados com sucesso!";
            $_SESSION['username'] = $new_username; // Update session username
        } else {
            $_SESSION['error'] = "Erro ao atualizar os dados do usuário.";
        }

        $update_stmt->close();
    }

    header("Location: user.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FakeDocs - Editar Usuário</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="dashboard-header">
        <h1>Editar Dados do Usuário</h1>
        <nav>
            <div class="nav-links">
                <a href="dashboard.php" class="nav-link">Voltar ao Dashboard</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="user-form-container">
            <h2>Editar Dados do Usuário</h2>
            <form action="user.php" method="post">
                <div class="input-group">
                    <label for="username">Nome de Usuário:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <div class="input-group">
                    <label for="password">Nova Senha:</label>
                    <input type="password" id="password" name="password" placeholder="Deixe vazio para não alterar">
                </div>
                <button type="submit">Atualizar</button>
            </form>

            <!-- Success/Error Messages -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="user-form-message success"><?php echo htmlspecialchars($_SESSION['message']); ?></div>
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="user-form-message error"><?php echo htmlspecialchars($_SESSION['error']); ?></div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
