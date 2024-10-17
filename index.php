<?php
if (!isset($_SESSION['user_id'])) {
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
    </main>
</body>

</html>