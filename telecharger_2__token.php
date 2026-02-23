<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Copier ce fichier à la racine de public_html/ en tant que telecharger.php
// Permet de télécharger le PDF par token.
// Supprime le document après téléchargement (ou s'il est expiré).

require_once __DIR__ . '/demarrage.php';
require_once __DIR__ . '/fonctions.php';

date_default_timezone_set($configApplication['fuseau_horaire']);

try {
    $token = isset($_GET['token']) ? trim((string) $_GET['token']) : '';
    if ($token === '') {
        http_response_code(400);
        echo 'Token manquant. Utilise: telecharger.php?token=...';
        exit;
    }

    $pdo = ouvrir_base_de_donnees($configApplication);

    nettoyer_documents_expires($pdo);

    $document = document_par_token($pdo, $token);

    if ($document === null) {
        http_response_code(404);
        echo 'Document introuvable ou expiré.';
        exit;
    }

    if (strtotime((string) $document['date_expiration']) <= time()) {
        supprimer_document($pdo, $document);
        http_response_code(410);
        echo 'Document expiré.';
        exit;
    }

    if (!is_file((string) $document['chemin_fichier'])) {
        supprimer_document($pdo, $document);
        http_response_code(404);
        echo 'Fichier introuvable.';
        exit;
    }

    $nomTelechargement = preg_replace('/[^a-zA-Z0-9_-]/', '_', (string) $document['libelle_document']) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $nomTelechargement . '"');
    header('Content-Length: ' . filesize((string) $document['chemin_fichier']));

    readfile((string) $document['chemin_fichier']);

    supprimer_document($pdo, $document);
    exit;
} catch (Throwable $e) {
    journaliser($configApplication, 'ERREUR', 'Erreur telechargement', ['message' => $e->getMessage()]);
    http_response_code(500);
    echo 'Erreur serveur';
}
