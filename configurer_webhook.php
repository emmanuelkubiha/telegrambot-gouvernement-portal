<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Permet d'enregistrer facilement l'URL webhook auprès de Telegram.
// Usage:
// http://localhost:8888/guichet-admin/configurer_webhook.php?url=https://xxxx.ngrok-free.app/reception_telegram.php

require_once __DIR__ . '/demarrage.php';
require_once __DIR__ . '/fonctions.php';

$url = isset($_GET['url']) ? trim((string) $_GET['url']) : '';

if ($url === '') {
    echo 'Ajoute ?url=https://.../reception_telegram.php';
    exit;
}

$resultat = telegram_configurer_webhook($configApplication, $url);

header('Content-Type: application/json; charset=utf-8');
echo json_encode($resultat, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
