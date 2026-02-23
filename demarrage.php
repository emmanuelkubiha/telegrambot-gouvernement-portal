<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Point de chargement commun pour toute l'application.
// Il charge la configuration selon l'environnement (local ou production).

// Détection automatique de l'environnement
$estProduction = false;

// Vérifier si on est sur le serveur de production
if (isset($_SERVER['HTTP_HOST'])) {
    $host = $_SERVER['HTTP_HOST'];
    // Si le domaine contient "etskushinganine.com", c'est la production
    if (strpos($host, 'etskushinganine.com') !== false) {
        $estProduction = true;
    }
}

// Charger la configuration appropriée
if ($estProduction) {
    $configApplication = require __DIR__ . '/configuration/configuration_production.php';
} else {
    $configApplication = require __DIR__ . '/configuration/configuration.php';
}
