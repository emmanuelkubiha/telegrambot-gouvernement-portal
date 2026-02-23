<?php
// Version simplifiée de connexion_admin avec affichage des erreurs
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

echo "<!-- Début du script -->\n";

try {
    declare(strict_types=1);
    
    echo "<!-- Chargement demarrage.php -->\n";
    require_once __DIR__ . '/demarrage.php';
    
    echo "<!-- Chargement fonctions.php -->\n";
    require_once __DIR__ . '/fonctions.php';
    
    echo "<!-- Démarrage session -->\n";
    session_start();
    
    echo "<!-- Vérification session existante -->\n";
    if (isset($_SESSION['admin_id'])) {
        header('Location: administrateur.php');
        exit;
    }
    
    $messageErreur = '';
    
    echo "<!-- Traitement POST -->\n";
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $identifiant = trim((string) ($_POST['identifiant'] ?? ''));
        $motDePasse = trim((string) ($_POST['mot_de_passe'] ?? ''));
        
        if ($identifiant === '' || $motDePasse === '') {
            $messageErreur = 'Tous les champs sont obligatoires.';
        } else {
            $pdo = ouvrir_base_de_donnees($configApplication);
            $admin = verifier_administrateur($pdo, $identifiant, $motDePasse);
            
            if ($admin !== null) {
                $_SESSION['admin_id'] = (int) $admin['id'];
                $_SESSION['admin_nom'] = (string) $admin['nom_complet'];
                $_SESSION['admin_identifiant'] = (string) $admin['identifiant'];
                
                // Debug: afficher au lieu de rediriger
                echo "<!DOCTYPE html><html><body>";
                echo "<h1 style='color: green;'>✓ Connexion réussie !</h1>";
                echo "<p>ID: " . $_SESSION['admin_id'] . "</p>";
                echo "<p>Nom: " . $_SESSION['admin_nom'] . "</p>";
                echo "<p><a href='administrateur.php'>Aller à l'administration</a></p>";
                echo "</body></html>";
                exit;
            } else {
                $messageErreur = 'Identifiant ou mot de passe incorrect.';
            }
        }
    }
    
    echo "<!-- Affichage formulaire -->\n";
    
} catch (Throwable $e) {
    echo "<!DOCTYPE html><html><body>";
    echo "<h1 style='color: red;'>ERREUR DÉTECTÉE</h1>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><strong>Fichier:</strong> " . htmlspecialchars($e->getFile()) . "</p>";
    echo "<p><strong>Ligne:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</body></html>";
    exit;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - TEST</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 500px; margin: 50px auto; padding: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; }
        input { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Connexion Admin (VERSION TEST)</h1>
    
    <?php if ($messageErreur): ?>
        <div class="error"><?= htmlspecialchars($messageErreur) ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <label>Identifiant :</label>
        <input type="text" name="identifiant" required>
        
        <label>Mot de passe :</label>
        <input type="password" name="mot_de_passe" required>
        
        <button type="submit">Se connecter</button>
    </form>
    
    <p style="color: #666; font-size: 12px; margin-top: 20px;">
        Identifiant par défaut : asnath<br>
        Mot de passe par défaut : 1234
    </p>
</body>
</html>
