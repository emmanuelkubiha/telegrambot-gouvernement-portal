<?php
// Fichier de test pour diagnostiquer l'erreur 500
// À supprimer après résolution du problème

error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Test de diagnostic</h1>";

echo "<h2>1. Informations serveur</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Filename: " . __FILE__ . "<br>";
echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "<br>";

echo "<h2>2. Test détection environnement</h2>";
$estProduction = false;
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    if (strpos($host, 'etskushinganine.com') !== false) {
        $estProduction = true;
    }
}
echo "Est en production: " . ($estProduction ? 'OUI' : 'NON') . "<br>";

echo "<h2>3. Test chargement configuration</h2>";
$configFile = $estProduction ? 'configuration/configuration_production.php' : 'configuration/configuration.php';
echo "Fichier config à charger: " . $configFile . "<br>";
echo "Chemin complet: " . __DIR__ . '/' . $configFile . "<br>";

if (file_exists(__DIR__ . '/' . $configFile)) {
    echo "✓ Le fichier existe<br>";
    
    try {
        $configApplication = require __DIR__ . '/' . $configFile;
        echo "✓ Configuration chargée avec succès<br>";
        echo "Clés de config: " . implode(', ', array_keys($configApplication)) . "<br>";
    } catch (Throwable $e) {
        echo "✗ Erreur lors du chargement: " . $e->getMessage() . "<br>";
        echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "✗ Le fichier n'existe PAS<br>";
    echo "Fichiers dans le dossier configuration/ :<br>";
    if (is_dir(__DIR__ . '/configuration')) {
        $files = scandir(__DIR__ . '/configuration');
        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                echo "  - " . $file . "<br>";
            }
        }
    } else {
        echo "Le dossier configuration/ n'existe pas !<br>";
    }
}

echo "<h2>4. Test connexion base de données</h2>";
if (isset($configApplication)) {
    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $configApplication['hote_base'],
            $configApplication['port_base'],
            $configApplication['nom_base']
        );
        
        echo "DSN: " . $dsn . "<br>";
        echo "User: " . $configApplication['utilisateur_base'] . "<br>";
        
        $pdo = new PDO(
            $dsn,
            $configApplication['utilisateur_base'],
            $configApplication['mot_passe_base'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        echo "✓ Connexion base de données réussie<br>";
        
        // Test de requête
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Tables dans la base: " . implode(', ', $tables) . "<br>";
        
    } catch (Throwable $e) {
        echo "✗ Erreur connexion base: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>5. Test chargement fonctions.php</h2>";
if (file_exists(__DIR__ . '/fonctions.php')) {
    echo "✓ fonctions.php existe<br>";
    try {
        require_once __DIR__ . '/fonctions.php';
        echo "✓ fonctions.php chargé avec succès<br>";
    } catch (Throwable $e) {
        echo "✗ Erreur: " . $e->getMessage() . "<br>";
        echo "Trace: <pre>" . $e->getTraceAsString() . "</pre>";
    }
} else {
    echo "✗ fonctions.php n'existe pas<br>";
}

echo "<h2>6. Permissions des dossiers</h2>";
$dossiers = ['documents_pdf', 'journaux', 'configuration'];
foreach ($dossiers as $dossier) {
    $path = __DIR__ . '/' . $dossier;
    if (is_dir($path)) {
        $perms = substr(sprintf('%o', fileperms($path)), -4);
        $writable = is_writable($path) ? 'OUI' : 'NON';
        echo "$dossier: permissions $perms, écriture: $writable<br>";
    } else {
        echo "$dossier: N'EXISTE PAS<br>";
    }
}

echo "<h2>✓ Diagnostic terminé</h2>";
echo "<p style='color: green;'>Si vous voyez ce message, PHP fonctionne correctement.</p>";
echo "<p>Vérifiez les erreurs ci-dessus pour identifier le problème.</p>";
?>
