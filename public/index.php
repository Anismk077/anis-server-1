<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/config.php';

$errors = [];
$success = false;

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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = trim($_POST['nom'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        if ($nom === '' || mb_strlen($nom) > 100) {
            $errors[] = "Le nom est obligatoire et doit faire moins de 100 caractères.";
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 190) {
            $errors[] = "L'adresse email est invalide.";
        }

        if ($contenu === '' || mb_strlen($contenu) > 255) {
            $errors[] = "Le message est obligatoire et doit faire moins de 255 caractères.";
        }

        if ($errors === []) {
            $stmt = $pdo->prepare(
                "INSERT INTO messages (nom, email, contenu) VALUES (:nom, :email, :contenu)"
            );

            $stmt->execute([
                ':nom' => $nom,
                ':email' => $email,
                ':contenu' => $contenu,
            ]);

            $success = true;
        }
    }

    $stmt = $pdo->query(
        "SELECT nom, email, contenu, created_at FROM messages ORDER BY id DESC LIMIT 10"
    );
    $messages = $stmt->fetchAll();

} catch (PDOException $e) {
    http_response_code(500);
    echo "<h1>Erreur serveur</h1>";
    echo "<p>Impossible de charger le site.</p>";
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

    <h2>Contact</h2>

    <?php if ($success): ?>
        <p>Message envoyé avec succès.</p>
    <?php endif; ?>

    <?php if ($errors !== []): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="post">
        <p>
            <label for="nom">Nom</label><br>
            <input type="text" id="nom" name="nom" maxlength="100" required>
        </p>

        <p>
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" maxlength="190" required>
        </p>

        <p>
            <label for="contenu">Message</label><br>
            <textarea id="contenu" name="contenu" maxlength="255" required></textarea>
        </p>

        <button type="submit">Envoyer</button>
    </form>

    <h2>Messages</h2>

    <?php foreach ($messages as $message): ?>
        <article>
            <strong>
                <?= htmlspecialchars($message['nom'], ENT_QUOTES, 'UTF-8') ?>
            </strong>
            <br>

            <small>
                <?= htmlspecialchars($message['email'], ENT_QUOTES, 'UTF-8') ?>
                —
                <?= htmlspecialchars($message['created_at'], ENT_QUOTES, 'UTF-8') ?>
            </small>

            <p>
                <?= nl2br(htmlspecialchars($message['contenu'], ENT_QUOTES, 'UTF-8')) ?>
            </p>
        </article>
        <hr>
    <?php endforeach; ?>
</body>
</html>
