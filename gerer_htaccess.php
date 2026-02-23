<?php
// Script pour désactiver temporairement .htaccess et tester
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h1>Gestion du fichier .htaccess</h1>";

$htaccess = __DIR__ . '/.htaccess';
$htaccess_bak = __DIR__ . '/.htaccess.backup';

if (isset($_GET['action'])) {
    $action = $_GET['action'];
    
    if ($action === 'desactiver') {
        if (file_exists($htaccess)) {
            if (rename($htaccess, $htaccess_bak)) {
                echo "<p style='color: green;'>✓ Le fichier .htaccess a été renommé en .htaccess.backup</p>";
                echo "<p>Le fichier .htaccess est maintenant <strong>DÉSACTIVÉ</strong></p>";
            } else {
                echo "<p style='color: red;'>✗ Erreur: Impossible de renommer le fichier</p>";
            }
        } else {
            echo "<p style='color: orange;'>Le fichier .htaccess n'existe pas ou est déjà désactivé</p>";
        }
    }
    
    if ($action === 'reactiver') {
        if (file_exists($htaccess_bak)) {
            if (rename($htaccess_bak, $htaccess)) {
                echo "<p style='color: green;'>✓ Le fichier .htaccess a été réactivé</p>";
            } else {
                echo "<p style='color: red;'>✗ Erreur: Impossible de réactiver le fichier</p>";
            }
        } else {
            echo "<p style='color: orange;'>Pas de fichier .htaccess.backup trouvé</p>";
        }
    }
}

echo "<hr>";
echo "<h2>État actuel</h2>";

if (file_exists($htaccess)) {
    echo "<p>✓ <strong>.htaccess</strong> existe (ACTIF)</p>";
} else {
    echo "<p>✗ <strong>.htaccess</strong> n'existe pas (DÉSACTIVÉ)</p>";
}

if (file_exists($htaccess_bak)) {
    echo "<p>✓ <strong>.htaccess.backup</strong> existe</p>";
} else {
    echo "<p>✗ <strong>.htaccess.backup</strong> n'existe pas</p>";
}

echo "<hr>";
echo "<h2>Actions</h2>";

if (file_exists($htaccess)) {
    echo "<p><a href='?action=desactiver' style='background: #dc3545; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Désactiver .htaccess</a></p>";
    echo "<p><em>Cela va renommer .htaccess en .htaccess.backup</em></p>";
}

if (file_exists($htaccess_bak)) {
    echo "<p><a href='?action=reactiver' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Réactiver .htaccess</a></p>";
}

echo "<hr>";
echo "<h2>Test de connexion</h2>";
echo "<p><a href='connexion_admin.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Tester la connexion admin</a></p>";

echo "<hr>";
echo "<p style='color: #666;'><em>Une fois que la connexion fonctionne, vous pouvez supprimer ce fichier.</em></p>";
?>
