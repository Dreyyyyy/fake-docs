<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
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
        <p>Dashboard contendo todos os documentos.</p>
        <!-- Here you can list all the user’s documents -->
    </main>
</body>

</html>
