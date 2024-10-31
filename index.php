<?php
session_start();

// Database connection
include 'db.php'; // Include the database connection file

$error = ""; // Initialize an error message variable

// Check if the form was submitted (login attempt)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to get the password and user ID
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password']; // Get the stored password
        $userId = $row['id']; // Get the user ID

        // Use password_verify to check the password
        if (password_verify($password, $storedPassword)) {
            $_SESSION['username'] = $username; // Start session on successful login
            $_SESSION['user_id'] = $userId; // Store user ID in session
            header("Location: dashboard.php"); // Redirect to dashboard
            exit();
        } else {
            $error = "Nome de usuário ou senha inválida!"; // Set error message
        }
    } else {
        $error = "Nome de usuário ou senha inválida!"; // Set error message
    }
    $stmt->close();
}

// If the user is already logged in, redirect to dashboard.php
if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css"> <!-- Link to your external CSS -->
    <title>FakeDocs - Página de login</title>
</head>
<body>
    <div class="login-container">
        <h2>Logar em FakeDocs</h2>
        <form action="index.php" method="post">
            <div class="input-group">
                <label for="username">Usuário:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group">
                <label for="password">Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Logar</button>
        </form>
        
        <!-- Error message display -->
        <?php if (!empty($error)): ?>
            <p id="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        
        <!-- Register button -->
        <div class="register-container">
            <p>Ainda não tem uma conta? <a href="register.php">Registre-se aqui</a></p>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>