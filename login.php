<?php
session_start();

$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "fake-docs";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou!: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare the statement to fetch the hashed password for the given username
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    echo "Número de linhas retornadas: " . $result->num_rows . "<br>";

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Fetch the row here
        $hashedPassword = $row['password']; // This should be the hashed password
        
        // Debug output
        echo "Input password: " . htmlspecialchars($password) . "<br>";
        echo "Hashed password from DB: " . htmlspecialchars($hashedPassword) . "<br>";

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['username'] = $username; // Set session variable
            header("Location: dashboard.php");
            exit(); // Always call exit after a header redirect
        } else {
            echo "<p style='color: red; text-align: center;'>Nome de usuário ou senha inválida!</p>";
            echo "<p>Verification failed. Password does not match.</p>";
        }
    } else {
        echo "<p style='color: red; text-align: center;'>Nome de usuário ou senha inválida!</p>";
    }

    $stmt->close();
}

$conn->close();
?>