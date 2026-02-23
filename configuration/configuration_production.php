<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Configuration pour l'environnement de PRODUCTION (hébergeur).

return [
    // === BASE DE DONNÉES PRODUCTION ===
    'hote_base' => 'localhost',
    'port_base' => 3306,
    'nom_base' => 'u783961849_guichet_unique',
    'utilisateur_base' => 'u783961849_guichet_unique',
    'mot_passe_base' => 'Hallelujah18@',

    // === TELEGRAM ===
    'token_bot_telegram' => '8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw',
    'secret_webhook_telegram' => 'sud-kivu-2026',

    // === CHEMINS PRODUCTION ===
    'dossier_documents_pdf' => __DIR__ . '/../documents_pdf',
    'dossier_journaux' => __DIR__ . '/../journaux',

    // === URL PUBLIQUE ===
    'url_base_application' => 'https://asnath.etskushinganine.com/guichet-admin',

    // === PARAMÈTRES GÉNÉRAUX ===
    'duree_document_minutes' => 15,
    'fuseau_horaire' => 'Africa/Maputo',
    
    // === ENVIRONNEMENT ===
    'environnement' => 'production',
    'debug' => false,
];
