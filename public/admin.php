<?php
declare(strict_types=1);

session_start();

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin</title>
</head>
<body>
    <h1>Espace admin</h1>
    <p>Bienvenue, <?= htmlspecialchars($_SESSION['admin_username'], ENT_QUOTES, 'UTF-8') ?>.</p>

    <p><a href="logout.php">Déconnexion</a></p>
</body>
</html>
