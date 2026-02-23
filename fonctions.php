<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Regroupe toutes les fonctions techniques du projet (DB, Telegram, PDF, nettoyage).
// Le but est d'avoir un TP tres simple, sans trop de fichiers.

function journaliser(array $config, string $niveau, string $message, array $contexte = []): void
{
    $date = date('Y-m-d H:i:s');
    $donnees = empty($contexte) ? '' : ' | ' . json_encode($contexte, JSON_UNESCAPED_UNICODE);
    $ligne = "[$date] [$niveau] $message$donnees" . PHP_EOL;
    file_put_contents($config['fichier_journal'], $ligne, FILE_APPEND);
}

function ouvrir_base_de_donnees(array $config): PDO
{
    $db = $config['base_de_donnees'];
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $db['host'],
        $db['port'],
        $db['nom_base_de_donnees'],
        $db['charset']
    );

    return new PDO($dsn, $db['utilisateur'], $db['mot_de_passe'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
}

function telegram_requete(array $config, string $methode, array $payload): array
{
    $url = 'https://api.telegram.org/bot' . $config['token_telegram'] . '/' . $methode;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $payload,
        CURLOPT_TIMEOUT => 20,
    ]);

    $reponse = curl_exec($ch);
    curl_close($ch);

    if ($reponse === false) {
        return ['ok' => false, 'description' => 'Erreur CURL'];
    }

    $json = json_decode($reponse, true);
    return is_array($json) ? $json : ['ok' => false, 'description' => 'Reponse Telegram invalide'];
}

function telegram_envoyer_message(array $config, int $chatId, string $texte, ?array $clavier = null): void
{
    $payload = [
        'chat_id' => $chatId,
        'text' => $texte,
    ];

    if ($clavier !== null) {
        // Si c'est une demande de suppression de clavier
        if (isset($clavier['remove_keyboard'])) {
            $payload['reply_markup'] = json_encode(['remove_keyboard' => true]);
        } 
        // Si c'est un clavier personnalisé
        else if (isset($clavier['keyboard'])) {
            $payload['reply_markup'] = json_encode([
                'keyboard' => $clavier['keyboard'],
                'resize_keyboard' => $clavier['resize_keyboard'] ?? true,
                'one_time_keyboard' => $clavier['one_time_keyboard'] ?? true,
                'input_field_placeholder' => $clavier['input_field_placeholder'] ?? '',
            ]);
        }
        // Ancien format (compatibilité)
        else {
            $payload['reply_markup'] = $clavier;
        }
    }

    telegram_requete($config, 'sendMessage', $payload);
}

function telegram_configurer_webhook(array $config, string $url): array
{
    return telegram_requete($config, 'setWebhook', [
        'url' => $url,
        'secret_token' => $config['secret_webhook_telegram'],
    ]);
}

function telegram_lire_webhook(array $config): array
{
    return telegram_requete($config, 'getWebhookInfo', []);
}

function citoyen_par_numero_piece(PDO $pdo, string $numeroPiece): ?array
{
    $sql = 'SELECT id, nom_complet, numero_piece, date_naissance, ville FROM citoyens WHERE numero_piece = :numero_piece LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['numero_piece' => trim($numeroPiece)]);
    $citoyen = $stmt->fetch();
    return $citoyen ?: null;
}

function lire_session_telegram(PDO $pdo, int $chatId): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM sessions_telegram WHERE chat_id = :chat_id LIMIT 1');
    $stmt->execute(['chat_id' => $chatId]);
    $session = $stmt->fetch();
    return $session ?: null;
}

function enregistrer_session_telegram(PDO $pdo, int $chatId, string $etat, ?int $citoyenId = null): void
{
    $sql = 'INSERT INTO sessions_telegram (chat_id, etat, citoyen_id, date_mise_a_jour)
            VALUES (:chat_id, :etat, :citoyen_id, NOW())
            ON DUPLICATE KEY UPDATE etat = VALUES(etat), citoyen_id = VALUES(citoyen_id), date_mise_a_jour = NOW()';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'chat_id' => $chatId,
        'etat' => $etat,
        'citoyen_id' => $citoyenId,
    ]);
}

function lister_types_documents(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, code, libelle FROM types_documents ORDER BY id ASC');
    return $stmt->fetchAll();
}

function type_document_par_libelle(PDO $pdo, string $libelle): ?array
{
    $stmt = $pdo->prepare('SELECT id, code, libelle FROM types_documents WHERE libelle = :libelle LIMIT 1');
    $stmt->execute(['libelle' => trim($libelle)]);
    $type = $stmt->fetch();
    return $type ?: null;
}

function echapper_texte_pdf(string $texte): string
{
    return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $texte);
}

function creer_pdf_simple(string $cheminSortie, array $lignes): void
{
    $taillePolice = 12;
    $interligne = 16;
    $x = 50;
    $y = 780;

    $commandesTexte = "BT\n/F1 {$taillePolice} Tf\n";
    foreach ($lignes as $ligne) {
        $ligneSafe = echapper_texte_pdf($ligne);
        $commandesTexte .= sprintf("1 0 0 1 %d %d Tm (%s) Tj\n", $x, $y, $ligneSafe);
        $y -= $interligne;
    }
    $commandesTexte .= "ET";

    $objets = [];
    $objets[] = "<< /Type /Catalog /Pages 2 0 R >>";
    $objets[] = "<< /Type /Pages /Kids [3 0 R] /Count 1 >>";
    $objets[] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>";
    $objets[] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>";
    $objets[] = "<< /Length " . strlen($commandesTexte) . " >>\nstream\n{$commandesTexte}\nendstream";

    $pdf = "%PDF-1.4\n";
    $decalages = [0];
    foreach ($objets as $index => $objet) {
        $numero = $index + 1;
        $decalages[] = strlen($pdf);
        $pdf .= "{$numero} 0 obj\n{$objet}\nendobj\n";
    }

    $xref = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objets) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";
    for ($i = 1; $i <= count($objets); $i++) {
        $pdf .= sprintf("%010d 00000 n \n", $decalages[$i]);
    }

    $pdf .= "trailer\n<< /Size " . (count($objets) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n{$xref}\n%%EOF";
    file_put_contents($cheminSortie, $pdf);
}

function construire_lignes_modele_document(array $citoyen, array $typeDocument): array
{
    $entete = [
        'GUICHET ADMINISTRATIF SUD-KIVU',
        '------------------------------',
        'Type document: ' . $typeDocument['libelle'],
    ];

    $pied = [
        'Nom: ' . $citoyen['nom_complet'],
        'Numero piece: ' . $citoyen['numero_piece'],
        'Date naissance: ' . $citoyen['date_naissance'],
        'Ville: ' . $citoyen['ville'],
        'Date generation: ' . date('Y-m-d H:i:s'),
    ];

    switch ($typeDocument['code']) {
        case 'attestation_residence':
            $corps = [
                'ATTESTATION DE RESIDENCE',
                'Le present document certifie que le citoyen',
                'reside dans la ville mentionnee ci-dessous.',
            ];
            break;

        case 'certificat_scolarite':
            $corps = [
                'CERTIFICAT DE SCOLARITE',
                'Le present document certifie que le citoyen',
                'est inscrit dans un etablissement scolaire.',
            ];
            break;

        case 'attestation_naissance':
            $corps = [
                'ATTESTATION DE NAISSANCE',
                'Le present document confirme les informations',
                'de naissance du citoyen.',
            ];
            break;

        case 'attestation_bonne_vie':
            $corps = [
                'ATTESTATION DE BONNE VIE ET MOEURS',
                'Le present document atteste la bonne conduite',
                'du citoyen selon les informations disponibles.',
            ];
            break;

        case 'certificat_celibat':
            $corps = [
                'CERTIFICAT DE CELIBAT',
                'Le present document atteste le statut matrimonial',
                'declare du citoyen pour usage administratif.',
            ];
            break;

        default:
            $corps = [
                'DOCUMENT ADMINISTRATIF',
                'Document genere automatiquement par le guichet.',
            ];
            break;
    }

    return array_merge($entete, $corps, $pied);
}

function generer_document_pdf(PDO $pdo, array $config, array $citoyen, array $typeDocument): array
{
    if (!is_dir($config['dossier_documents'])) {
        mkdir($config['dossier_documents'], 0777, true);
    }

    $token = bin2hex(random_bytes(16));
    $nomFichier = $typeDocument['code'] . '_' . $token . '.pdf';
    $cheminFichier = rtrim($config['dossier_documents'], '/') . '/' . $nomFichier;

    $lignes = construire_lignes_modele_document($citoyen, $typeDocument);

    creer_pdf_simple($cheminFichier, $lignes);

    $dateExpiration = date('Y-m-d H:i:s', time() + ((int) $config['duree_document_minutes'] * 60));

    $stmt = $pdo->prepare('INSERT INTO documents_generes (token, citoyen_id, type_document_id, chemin_fichier, date_expiration, date_creation)
                           VALUES (:token, :citoyen_id, :type_document_id, :chemin_fichier, :date_expiration, NOW())');
    $stmt->execute([
        'token' => $token,
        'citoyen_id' => $citoyen['id'],
        'type_document_id' => $typeDocument['id'],
        'chemin_fichier' => $cheminFichier,
        'date_expiration' => $dateExpiration,
    ]);

    return [
        'token' => $token,
        'lien' => rtrim($config['url_base'], '/') . '/guichet-admin/telecharger.php?token=' . urlencode($token),
        'date_expiration' => $dateExpiration,
    ];
}

function document_par_token(PDO $pdo, string $token): ?array
{
    $sql = 'SELECT dg.*, td.libelle AS libelle_document
            FROM documents_generes dg
            INNER JOIN types_documents td ON td.id = dg.type_document_id
            WHERE dg.token = :token
            LIMIT 1';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['token' => $token]);
    $doc = $stmt->fetch();
    return $doc ?: null;
}

function supprimer_document(PDO $pdo, array $document): void
{
    if (!empty($document['chemin_fichier']) && is_file($document['chemin_fichier'])) {
        @unlink($document['chemin_fichier']);
    }

    $stmt = $pdo->prepare('DELETE FROM documents_generes WHERE id = :id');
    $stmt->execute(['id' => $document['id']]);
}

function nettoyer_documents_expires(PDO $pdo): int
{
    $stmt = $pdo->query('SELECT id, chemin_fichier FROM documents_generes WHERE date_expiration <= NOW()');
    $lignes = $stmt->fetchAll();
    foreach ($lignes as $ligne) {
        if (!empty($ligne['chemin_fichier']) && is_file($ligne['chemin_fichier'])) {
            @unlink($ligne['chemin_fichier']);
        }
    }

    $supprimes = $pdo->exec('DELETE FROM documents_generes WHERE date_expiration <= NOW()');
    return is_int($supprimes) ? $supprimes : 0;
}

function ajouter_citoyen(PDO $pdo, string $nomComplet, string $numeroPiece, string $dateNaissance, string $ville): void
{
    $sql = 'INSERT INTO citoyens (nom_complet, numero_piece, date_naissance, ville)
            VALUES (:nom_complet, :numero_piece, :date_naissance, :ville)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'nom_complet' => trim($nomComplet),
        'numero_piece' => trim($numeroPiece),
        'date_naissance' => trim($dateNaissance),
        'ville' => trim($ville),
    ]);
}

function lister_citoyens(PDO $pdo, int $limite = 20): array
{
    $stmt = $pdo->prepare('SELECT id, nom_complet, numero_piece, date_naissance, ville, date_creation
                           FROM citoyens
                           ORDER BY id DESC
                           LIMIT :limite');
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function enregistrer_demande_document(PDO $pdo, int $chatId, array $citoyen, array $typeDocument, string $statut, ?string $username = null, ?string $prenom = null, ?string $nom = null): void
{
    $sql = 'INSERT INTO demandes_documents
            (chat_id, username_telegram, prenom_telegram, nom_telegram, citoyen_id, nom_citoyen, numero_piece, document_demande, statut, date_heure_demande)
            VALUES
            (:chat_id, :username_telegram, :prenom_telegram, :nom_telegram, :citoyen_id, :nom_citoyen, :numero_piece, :document_demande, :statut, NOW())';

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        'chat_id' => $chatId,
        'username_telegram' => $username,
        'prenom_telegram' => $prenom,
        'nom_telegram' => $nom,
        'citoyen_id' => $citoyen['id'],
        'nom_citoyen' => $citoyen['nom_complet'],
        'numero_piece' => $citoyen['numero_piece'],
        'document_demande' => $typeDocument['libelle'],
        'statut' => $statut,
    ]);
}

function lister_demandes_documents(PDO $pdo, int $limite = 50): array
{
    $stmt = $pdo->prepare('SELECT id, chat_id, username_telegram, prenom_telegram, nom_telegram, nom_citoyen, numero_piece, document_demande, statut, date_heure_demande
                           FROM demandes_documents
                           ORDER BY id DESC
                           LIMIT :limite');
    $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// === GESTION ADMINISTRATEURS ===

function verifier_administrateur(PDO $pdo, string $identifiant, string $motDePasse): ?array
{
    $stmt = $pdo->prepare('SELECT id, nom_complet, identifiant, mot_de_passe FROM administrateurs WHERE identifiant = :identifiant');
    $stmt->execute(['identifiant' => $identifiant]);
    $admin = $stmt->fetch();
    
    if ($admin === false) {
        return null;
    }
    
    if (password_verify($motDePasse, (string) $admin['mot_de_passe'])) {
        return $admin;
    }
    
    return null;
}

function ajouter_administrateur(PDO $pdo, string $nomComplet, string $identifiant, string $motDePasse): void
{
    $stmt = $pdo->prepare('INSERT INTO administrateurs (nom_complet, identifiant, mot_de_passe) VALUES (:nom, :identifiant, :mdp)');
    $stmt->execute([
        'nom' => $nomComplet,
        'identifiant' => $identifiant,
        'mdp' => password_hash($motDePasse, PASSWORD_DEFAULT),
    ]);
}

function modifier_administrateur(PDO $pdo, int $id, string $nomComplet, string $identifiant, ?string $motDePasse = null): void
{
    if ($motDePasse !== null && $motDePasse !== '') {
        $stmt = $pdo->prepare('UPDATE administrateurs SET nom_complet = :nom, identifiant = :identifiant, mot_de_passe = :mdp WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'nom' => $nomComplet,
            'identifiant' => $identifiant,
            'mdp' => password_hash($motDePasse, PASSWORD_DEFAULT),
        ]);
    } else {
        $stmt = $pdo->prepare('UPDATE administrateurs SET nom_complet = :nom, identifiant = :identifiant WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'nom' => $nomComplet,
            'identifiant' => $identifiant,
        ]);
    }
}

function supprimer_administrateur(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM administrateurs WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function lister_administrateurs(PDO $pdo): array
{
    $stmt = $pdo->query('SELECT id, nom_complet, identifiant, date_creation FROM administrateurs ORDER BY id ASC');
    return $stmt->fetchAll();
}

// === GESTION CITOYENS ETENDUE ===

function modifier_citoyen(PDO $pdo, int $id, string $nomComplet, string $numeroPiece, string $dateNaissance, string $ville): void
{
    $stmt = $pdo->prepare('UPDATE citoyens SET nom_complet = :nom, numero_piece = :numero, date_naissance = :date, ville = :ville WHERE id = :id');
    $stmt->execute([
        'id' => $id,
        'nom' => $nomComplet,
        'numero' => $numeroPiece,
        'date' => $dateNaissance,
        'ville' => $ville,
    ]);
}

function supprimer_citoyen(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM citoyens WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function citoyen_par_id(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT id, nom_complet, numero_piece, date_naissance, ville, date_creation FROM citoyens WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch();
    return $result === false ? null : $result;
}

