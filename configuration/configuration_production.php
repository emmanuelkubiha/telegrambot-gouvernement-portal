<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Configuration pour l'environnement de PRODUCTION (hébergeur).

return [
    'nom_application' => 'Guichet Administratif Sud-Kivu',
    'fuseau_horaire' => 'Africa/Maputo',
    'url_base' => 'https://asnath.etskushinganine.com',

    // Telegram
    // 'token_telegram' => '8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw', // Ancien token
    'token_telegram' => '7114289996:AAELhYYXzRZqZEEGgsX8S7JTylOrMR-fO0s',
    'secret_webhook_telegram' => 'sud-kivu-2026',

    // Duree de validite d'un PDF (en minutes)
    'duree_document_minutes' => 15,

    // Base de donnees MySQL (Production)
    'base_de_donnees' => [
        'host' => 'localhost',
        'port' => 3306,
        'nom_base_de_donnees' => 'u783961849_guichet_unique',
        'utilisateur' => 'u783961849_guichet_unique',
        'mot_de_passe' => 'Hallelujah18@',
        'charset' => 'utf8mb4',
    ],

    // Dossiers techniques
    'fichier_journal' => __DIR__ . '/../journaux/application.log',
    'dossier_documents' => __DIR__ . '/../documents_pdf',

    // Metadonnees environnement
    'environnement' => 'production',
    'debug' => false,
];
