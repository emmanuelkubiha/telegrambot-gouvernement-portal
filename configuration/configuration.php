<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Fichier UNIQUE de configuration du projet.
// Tu peux tout modifier ici (bot, base de donnees, url, delai, etc.).

return [
    'nom_application' => 'Guichet Administratif Sud-Kivu',
    'fuseau_horaire' => 'Africa/Maputo',
    'url_base' => 'http://localhost:8888/guichet-admin',

    // Telegram
    'token_telegram' => '8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw',
    'secret_webhook_telegram' => 'sud-kivu-2026',

    // Duree de validite d'un PDF (en minutes)
    'duree_document_minutes' => 15,

    // Base de donnees MySQL (MAMP)
    'base_de_donnees' => [
        'host' => 'localhost',
        'port' => 8889,
        'nom_base_de_donnees' => 'base_de_donnees_guichet_sud_kivu',
        'utilisateur' => 'root',
        'mot_de_passe' => 'root',
        'charset' => 'utf8mb4',
    ],

    // Dossiers techniques
    'fichier_journal' => __DIR__ . '/../journaux/application.log',
    'dossier_documents' => __DIR__ . '/../stockage/documents',
];
