<?php
declare(strict_types=1);

// Test détaillé de connexion_admin.php étape par étape

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Test détaillé de connexion_admin.php</h1>";

echo "<h2>Étape 1 : Déclaration strict types</h2>";
echo "✓ OK<br><br>";

echo "<h2>Étape 2 : Chargement demarrage.php</h2>";
try {
    require_once __DIR__ . '/demarrage.php';
    echo "✓ demarrage.php chargé<br>";
    echo "Config chargée: " . ($configApplication ? 'OUI' : 'NON') . "<br><br>";
} catch (Throwable $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<h2>Étape 3 : Chargement fonctions.php</h2>";
try {
    require_once __DIR__ . '/fonctions.php';
    echo "✓ fonctions.php chargé<br><br>";
} catch (Throwable $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<h2>Étape 4 : Démarrage de session</h2>";
try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
        echo "✓ Session démarrée<br>";
    } else {
        echo "✓ Session déjà active<br>";
    }
    echo "Session ID: " . session_id() . "<br><br>";
} catch (Throwable $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<h2>Étape 5 : Test connexion base de données</h2>";
try {
    $pdo = ouvrir_base_de_donnees($configApplication);
    echo "✓ Connexion base OK<br><br>";
} catch (Throwable $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<h2>Étape 6 : Test fonction verifier_administrateur</h2>";
try {
    $admin = verifier_administrateur($pdo, 'asnath', '1234');
    if ($admin) {
        echo "✓ Administrateur trouvé: " . htmlspecialchars($admin['nom_complet']) . "<br>";
        echo "ID: " . $admin['id'] . "<br>";
    } else {
        echo "✗ Administrateur non trouvé (vérifier les identifiants)<br>";
    }
    echo "<br>";
} catch (Throwable $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<h2>Étape 7 : Test création session admin</h2>";
try {
    if ($admin) {
        $_SESSION['admin_id'] = (int) $admin['id'];
        $_SESSION['admin_nom'] = (string) $admin['nom_complet'];
        $_SESSION['admin_identifiant'] = (string) $admin['identifiant'];
        echo "✓ Variables de session créées<br>";
        echo "admin_id: " . $_SESSION['admin_id'] . "<br>";
        echo "admin_nom: " . $_SESSION['admin_nom'] . "<br>";
        echo "admin_identifiant: " . $_SESSION['admin_identifiant'] . "<br><br>";
    }
} catch (Throwable $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<hr>";
echo "<h2 style='color: green;'>✓ Tous les tests réussis !</h2>";
echo "<p>Le problème ne vient PAS du code PHP.</p>";
echo "<p>Il peut venir de :</p>";
echo "<ul>";
echo "<li>Un problème de redirection (header Location)</li>";
echo "<li>Un problème avec le serveur web</li>";
echo "<li>Un cache ou cookie corrompu</li>";
echo "</ul>";

echo "<hr>";
echo "<h2>Test de redirection</h2>";
echo "<p>Cliquez sur ce lien pour simuler une connexion :</p>";
echo "<a href='administrateur.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Aller à administrateur.php</a>";

echo "<hr>";
echo "<h2>Test avec formulaire réel</h2>";
?>
<form method="POST" action="connexion_admin.php" style="border: 1px solid #ccc; padding: 20px; max-width: 400px;">
    <h3>Formulaire de test</h3>
    <div style="margin-bottom: 10px;">
        <label>Identifiant :</label><br>
        <input type="text" name="identifiant" value="asnath" style="width: 100%; padding: 5px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label>Mot de passe :</label><br>
        <input type="password" name="mot_de_passe" value="1234" style="width: 100%; padding: 5px;">
    </div>
    <button type="submit" style="background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Se connecter</button>
</form>

<p style="color: #666;"><em>Note : Ce formulaire envoie vers connexion_admin.php. Si vous obtenez une erreur 500, le problème vient de connexion_admin.php lui-même.</em></p>
<?php
echo "<hr>";
echo "<h2>Informations supplémentaires</h2>";
echo "Version PHP: " . phpversion() . "<br>";
echo "Taille mémoire: " . ini_get('memory_limit') . "<br>";
echo "Max execution time: " . ini_get('max_execution_time') . "<br>";
echo "Display errors: " . ini_get('display_errors') . "<br>";
echo "Error reporting: " . ini_get('error_reporting') . "<br>";
?>
