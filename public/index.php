<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    $stmt = $pdo->query("SELECT contenu, created_at FROM messages ORDER BY id DESC LIMIT 10");
    $messages = $stmt->fetchAll();

} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Erreur serveur</h1>";
    echo "<p>Impossible de charger les messages.</p>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon site sécurisé</title>
</head>
<body>
    <h1>Mon site fonctionne</h1>

    <h2>Messages</h2>

    <?php foreach ($messages as $message): ?>
        <p>
            <?= htmlspecialchars($message['contenu'], ENT_QUOTES, 'UTF-8') ?>
            <br>
            <small><?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?></small>
        </p>
    <?php endforeach; ?>
</body>
</html>
