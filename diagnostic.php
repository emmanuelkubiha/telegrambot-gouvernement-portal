<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Diagnostic complet du système pour vérifier pourquoi le bot ne fonctionne pas.

echo "=== DIAGNOSTIC DU SYSTÈME GUICHET ===\n\n";

// 1. Vérifier la configuration
echo "1. CONFIGURATION\n";
echo "----------------\n";

require_once __DIR__ . '/demarrage.php';

echo "✓ Configuration chargée\n";
echo "- Token Telegram: " . substr($configApplication['token_telegram'], 0, 20) . "...\n";
echo "- URL de base: {$configApplication['url_base']}\n";
echo "- Secret webhook: {$configApplication['secret_webhook_telegram']}\n\n";

// 2. Vérifier la base de données
echo "2. BASE DE DONNÉES\n";
echo "------------------\n";

try {
    require_once __DIR__ . '/fonctions.php';
    $pdo = ouvrir_base_de_donnees($configApplication);
    echo "✓ Connexion MySQL réussie\n";
    
    // Vérifier les tables
    $tables = ['citoyens', 'administrateurs', 'types_documents', 'sessions_telegram', 'documents_generes', 'demandes_documents'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $result = $stmt->fetch();
        echo "  - Table '$table': {$result['count']} enregistrements\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ ERREUR BASE DE DONNÉES: " . $e->getMessage() . "\n";
    echo "  → Vous devez exécuter le fichier SQL dans phpMyAdmin\n";
    echo "  → Voir EXECUTER_SQL.md\n\n";
}

// 3. Vérifier le webhook Telegram
echo "3. WEBHOOK TELEGRAM\n";
echo "-------------------\n";

try {
    $ch = curl_init("https://api.telegram.org/bot{$configApplication['token_telegram']}/getWebhookInfo");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $webhookInfo = json_decode($response, true);
    
    if ($webhookInfo['ok']) {
        $url = $webhookInfo['result']['url'] ?? 'NON CONFIGURÉ';
        $pendingCount = $webhookInfo['result']['pending_update_count'] ?? 0;
        $lastError = $webhookInfo['result']['last_error_message'] ?? 'Aucune';
        
        echo "Status: " . ($url !== '' && $url !== 'NON CONFIGURÉ' ? "✓ Configuré" : "✗ Non configuré") . "\n";
        echo "URL: $url\n";
        echo "Messages en attente: $pendingCount\n";
        echo "Dernière erreur: $lastError\n\n";
        
        if ($url === '' || $url === 'NON CONFIGURÉ') {
            echo "⚠ PROBLÈME: Le webhook n'est pas configuré\n";
            echo "  → Installez ngrok: brew install ngrok/ngrok/ngrok\n";
            echo "  → Lancez: ngrok http 8888\n";
            echo "  → Configurez le webhook (voir CONFIGURATION_BOT.md)\n\n";
        }
    } else {
        echo "✗ ERREUR: Impossible de vérifier le webhook\n";
        echo "  Token Telegram invalide?\n\n";
    }
} catch (Exception $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "\n\n";
}

// 4. Vérifier les dossiers
echo "4. DOSSIERS ET PERMISSIONS\n";
echo "--------------------------\n";

$dossiers = [
    'stockage/documents' => __DIR__ . '/stockage/documents',
    'journaux' => __DIR__ . '/journaux',
];

foreach ($dossiers as $nom => $chemin) {
    if (is_dir($chemin)) {
        if (is_writable($chemin)) {
            echo "✓ $nom: OK (écriture possible)\n";
        } else {
            echo "✗ $nom: Pas de permission d'écriture\n";
        }
    } else {
        echo "✗ $nom: Dossier inexistant\n";
        echo "  → Création...\n";
        @mkdir($chemin, 0755, true);
        if (is_dir($chemin)) {
            echo "  ✓ Créé avec succès\n";
        }
    }
}
echo "\n";

// 5. Tester l'API Telegram
echo "5. TEST API TELEGRAM\n";
echo "--------------------\n";

try {
    $ch = curl_init("https://api.telegram.org/bot{$configApplication['token_telegram']}/getMe");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $botInfo = json_decode($response, true);
    
    if ($botInfo['ok']) {
        echo "✓ Bot actif\n";
        echo "- Nom: {$botInfo['result']['first_name']}\n";
        echo "- Username: @{$botInfo['result']['username']}\n";
        echo "- ID: {$botInfo['result']['id']}\n\n";
    } else {
        echo "✗ Token invalide\n\n";
    }
} catch (Exception $e) {
    echo "✗ ERREUR: " . $e->getMessage() . "\n\n";
}

// 6. Logs récents
echo "6. LOGS RÉCENTS\n";
echo "---------------\n";

$logFile = __DIR__ . '/journaux/application.log';
if (file_exists($logFile)) {
    $logs = file($logFile);
    $recentLogs = array_slice($logs, -10);
    if (empty($recentLogs)) {
        echo "Aucun log récent\n";
    } else {
        echo "Dernières lignes du journal:\n";
        foreach ($recentLogs as $log) {
            echo "  " . trim($log) . "\n";
        }
    }
} else {
    echo "Aucun fichier de log trouvé\n";
}
echo "\n";

// 7. Résumé
echo "=== RÉSUMÉ ===\n";
echo "--------------\n";

$problems = [];

try {
    $pdo = ouvrir_base_de_donnees($configApplication);
} catch (Exception $e) {
    $problems[] = "Base de données non créée (voir EXECUTER_SQL.md)";
}

if (!isset($webhookInfo) || empty($webhookInfo['result']['url'])) {
    $problems[] = "Webhook Telegram non configuré (voir CONFIGURATION_BOT.md)";
}

if (empty($problems)) {
    echo "✓ Système prêt!\n";
    echo "\nPour tester:\n";
    echo "1. Ouvrir Telegram\n";
    echo "2. Rechercher: @guichet_sk_bot\n";
    echo "3. Envoyer: /start\n";
} else {
    echo "✗ Problèmes détectés:\n\n";
    foreach ($problems as $i => $problem) {
        echo ($i + 1) . ". $problem\n";
    }
}

echo "\n";
