<?php
session_start();

// Database connection
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "fake-docs";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou!: " . $conn->connect_error);
}

$error = ""; // Initialize an error message variable

// Check if the form was submitted (login attempt)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare statement to get the plain password
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $storedPassword = $row['password']; // Get the plain text password from DB

        // Compare the entered password with the stored one
        if ($password == $storedPassword) {
            $_SESSION['username'] = $username; // Start session on successful login
            header("Location: index.php"); // Redirect to dashboard
            exit();
        } else {
            $error = "Nome de usuário ou senha inválida!"; // Set error message
        }
    } else {
        $error = "Nome de usuário ou senha inválida!"; // Set error message
    }
    $stmt->close();
}

// If the user is already logged in, redirect to index.php
if (isset($_SESSION['username'])) {
    header("Location: index.php");
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
        <form action="login.php" method="post">
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
    </div>
    <script src="script.js"></script>
</body>
</html>
