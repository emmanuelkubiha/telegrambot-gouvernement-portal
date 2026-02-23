<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Reçoit les messages Telegram (webhook), vérifie l'identité,
// propose les documents et génère le PDF.

require_once __DIR__ . '/demarrage.php';
require_once __DIR__ . '/fonctions.php';

$messagesTelegram = require_once __DIR__ . '/configuration/messages_telegram.php';

date_default_timezone_set($configApplication['fuseau_horaire']);

// Si on ouvre ce fichier dans le navigateur, on affiche juste un message.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    echo 'Webhook Telegram actif (attend une requete POST).';
    exit;
}

$secretHeader = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? null;
if (!empty($configApplication['secret_webhook_telegram']) && $secretHeader !== $configApplication['secret_webhook_telegram']) {
    http_response_code(403);
    echo 'Acces refuse';
    exit;
}

try {
    $pdo = ouvrir_base_de_donnees($configApplication);

    // Nettoyer les documents expirés à chaque appel.
    nettoyer_documents_expires($pdo);

    $update = json_decode((string) file_get_contents('php://input'), true);

    if (!isset($update['message']['chat']['id'], $update['message']['text'])) {
        echo 'OK';
        exit;
    }

    $chatId = (int) $update['message']['chat']['id'];
    $texte = trim((string) $update['message']['text']);
    
    // Capturer les informations de l'utilisateur Telegram
    $username = $update['message']['from']['username'] ?? null;
    $prenom = $update['message']['from']['first_name'] ?? null;
    $nom = $update['message']['from']['last_name'] ?? null;

    if ($texte === '/start') {
        enregistrer_session_telegram($pdo, $chatId, 'attente_piece', null);
        telegram_envoyer_message(
            $configApplication, 
            $chatId, 
            $messagesTelegram['bienvenue'],
            ['remove_keyboard' => true]
        );
        echo 'OK';
        exit;
    }
    
    if ($texte === '/aide' || $texte === '/help') {
        telegram_envoyer_message(
            $configApplication, 
            $chatId, 
            $messagesTelegram['aide'],
            ['remove_keyboard' => true]
        );
        echo 'OK';
        exit;
    }

    $session = lire_session_telegram($pdo, $chatId);
    $etat = $session['etat'] ?? 'attente_piece';

    if ($etat === 'attente_piece') {
        $citoyen = citoyen_par_numero_piece($pdo, $texte);

        if ($citoyen === null) {
            telegram_envoyer_message(
                $configApplication, 
                $chatId, 
                $messagesTelegram['piece_introuvable'],
                ['remove_keyboard' => true]
            );
            echo 'OK';
            exit;
        }

        enregistrer_session_telegram($pdo, $chatId, 'attente_document', (int) $citoyen['id']);
        $types = lister_types_documents($pdo);

        $lignesClavier = [];
        foreach ($types as $type) {
            $lignesClavier[] = [['text' => $type['libelle']]];
        }

        $messageIdentite = sprintf(
            $messagesTelegram['identite_validee'],
            $citoyen['nom_complet'],
            $citoyen['numero_piece']
        );
        
        telegram_envoyer_message(
            $configApplication,
            $chatId,
            $messageIdentite,
            [
                'keyboard' => $lignesClavier,
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
                'input_field_placeholder' => 'Utilisez les boutons ci-dessous',
            ]
        );

        echo 'OK';
        exit;
    }

    if ($etat === 'attente_document') {
        if (empty($session['citoyen_id'])) {
            enregistrer_session_telegram($pdo, $chatId, 'attente_piece', null);
            telegram_envoyer_message($configApplication, $chatId, $messagesTelegram['session_invalide']);
            echo 'OK';
            exit;
        }

        $typeDocument = type_document_par_libelle($pdo, $texte);
        if ($typeDocument === null) {
            // Renvoyer le clavier si le document n'est pas reconnu
            $types = lister_types_documents($pdo);
            $lignesClavier = [];
            foreach ($types as $type) {
                $lignesClavier[] = [['text' => $type['libelle']]];
            }
            
            telegram_envoyer_message(
                $configApplication, 
                $chatId, 
                $messagesTelegram['document_non_reconnu'],
                [
                    'keyboard' => $lignesClavier,
                    'resize_keyboard' => true,
                    'one_time_keyboard' => true,
                    'input_field_placeholder' => 'Utilisez les boutons ci-dessous',
                ]
            );
            echo 'OK';
            exit;
        }

        $stmt = $pdo->prepare('SELECT id, nom_complet, numero_piece, date_naissance, ville FROM citoyens WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => (int) $session['citoyen_id']]);
        $citoyen = $stmt->fetch();

        if (!$citoyen) {
            enregistrer_session_telegram($pdo, $chatId, 'attente_piece', null);
            telegram_envoyer_message($configApplication, $chatId, $messagesTelegram['session_invalide']);
            echo 'OK';
            exit;
        }

        $documentGenere = generer_document_pdf($pdo, $configApplication, $citoyen, $typeDocument);

        // Si la table d'historique n'existe pas encore, on n'interrompt pas le service.
        try {
            enregistrer_demande_document($pdo, $chatId, $citoyen, $typeDocument, 'GENERE', $username, $prenom, $nom);
        } catch (Throwable $e) {
            journaliser($configApplication, 'INFO', 'Historique non enregistre', ['message' => $e->getMessage()]);
        }

        enregistrer_session_telegram($pdo, $chatId, 'attente_piece', null);

        $messageDocument = sprintf(
            $messagesTelegram['document_genere'],
            $typeDocument['libelle'],
            $documentGenere['lien'],
            $configApplication['duree_document_minutes'],
            $documentGenere['date_expiration']
        );
        
        telegram_envoyer_message(
            $configApplication, 
            $chatId, 
            $messageDocument,
            ['remove_keyboard' => true]
        );

        echo 'OK';
        exit;
    }

    enregistrer_session_telegram($pdo, $chatId, 'attente_piece', null);
    telegram_envoyer_message($configApplication, $chatId, $messagesTelegram['commande_inconnue']);
    echo 'OK';
} catch (Throwable $e) {
    journaliser($configApplication, 'ERREUR', 'Erreur reception Telegram', ['message' => $e->getMessage()]);
    http_response_code(500);
    echo 'Erreur serveur';
}
