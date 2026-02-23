<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Page web simple pour configurer, verifier et desactiver le webhook Telegram.

require_once __DIR__ . '/demarrage.php';

date_default_timezone_set($configApplication['fuseau_horaire']);

$messageSucces = '';
$messageErreur = '';

$token = $configApplication['token_telegram'] ?? '';
$secret = $configApplication['secret_webhook_telegram'] ?? '';
$host = $_SERVER['HTTP_HOST'] ?? '';
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

if ($host === '') {
    $urlBase = (string) ($configApplication['url_base'] ?? '');
    $host = (string) parse_url($urlBase, PHP_URL_HOST);
    $scheme = (string) (parse_url($urlBase, PHP_URL_SCHEME) ?: $scheme);
}

$urlProposee = $host !== ''
    ? $scheme . '://' . $host . '/reception_telegram.php'
    : rtrim((string) ($configApplication['url_base'] ?? ''), '/') . '/reception_telegram.php';

function telegram_requete_simple(string $token, string $methode, array $payload): array
{
    $url = 'https://api.telegram.org/bot' . $token . '/' . $methode;
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

$webhookInfo = [];
if ($token !== '') {
    $webhookInfo = telegram_requete_simple($token, 'getWebhookInfo', []);
}

$urlActuelle = $webhookInfo['result']['url'] ?? '';
$pendingCount = $webhookInfo['result']['pending_update_count'] ?? 0;
$lastError = $webhookInfo['result']['last_error_message'] ?? '';
$derniereErreur = $lastError !== '' ? $lastError : 'Aucune';
$etatConnexion = ($urlActuelle !== '' && $lastError === '') ? 'Connecté' : 'Probleme de connexion';
$messageEtat = $lastError !== '' ? $lastError : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $urlEnvoyee = trim((string) ($_POST['url_webhook'] ?? ''));

    if ($token === '') {
        $messageErreur = 'Token Telegram manquant dans la configuration.';
    } else if ($action === 'configurer') {
        if ($urlEnvoyee === '') {
            $messageErreur = 'Veuillez renseigner une URL valide.';
        } else {
            $result = telegram_requete_simple($token, 'setWebhook', [
                'url' => $urlEnvoyee,
                'secret_token' => $secret,
            ]);
            if (!empty($result['ok'])) {
                $messageSucces = 'Webhook configuré avec succès.';
            } else {
                $messageErreur = 'Echec de configuration du webhook.';
            }
        }
    } else if ($action === 'desactiver') {
        $result = telegram_requete_simple($token, 'setWebhook', [
            'url' => '',
        ]);
        if (!empty($result['ok'])) {
            $messageSucces = 'Webhook désactivé.';
        } else {
            $messageErreur = 'Echec de désactivation du webhook.';
        }
    }

    if ($token !== '') {
        $webhookInfo = telegram_requete_simple($token, 'getWebhookInfo', []);
        $urlActuelle = $webhookInfo['result']['url'] ?? '';
        $pendingCount = $webhookInfo['result']['pending_update_count'] ?? 0;
        $lastError = $webhookInfo['result']['last_error_message'] ?? '';
    }
}

$estConfigure = $urlActuelle !== '';
$boutonConfigurerDesactive = $urlActuelle === $urlProposee;
$urlIncoherente = $estConfigure && $urlActuelle !== $urlProposee;
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurer le webhook Telegram</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 900px; }
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="container py-5">
        <h3 class="mb-3">Webhook Telegram</h3>
        <p class="text-muted mb-4">
            Le webhook permet à Telegram d'envoyer automatiquement les messages du bot vers votre serveur.
            Sans webhook, le bot ne reçoit rien et ne peut pas répondre.
            En production, on configure le webhook une seule fois, sauf si l'URL change.
        </p>

        <?php if ($messageSucces !== ''): ?>
            <div class="alert alert-success"><?= htmlspecialchars($messageSucces, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <?php if ($messageErreur !== ''): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($messageErreur, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-body">
                <h6 class="mb-3">Etat actuel</h6>
                <div class="mb-2"><strong>Statut:</strong> <?= $estConfigure ? 'Configuré' : 'Non configuré' ?></div>
                <div class="mb-2"><strong>Connexion Telegram:</strong> <?= htmlspecialchars($etatConnexion, ENT_QUOTES, 'UTF-8') ?></div>
                <div class="mb-2"><strong>URL enregistrée:</strong> <?= $urlActuelle !== '' ? htmlspecialchars($urlActuelle, ENT_QUOTES, 'UTF-8') : 'Aucune' ?></div>
                <div class="mb-2"><strong>Messages en attente:</strong> <?= (int) $pendingCount ?></div>
                <div class="mb-0"><strong>Dernière erreur:</strong> <?= htmlspecialchars($derniereErreur, ENT_QUOTES, 'UTF-8') ?></div>
                <?php if ($urlIncoherente): ?>
                    <div class="alert alert-warning mt-3 mb-0">L'URL enregistrée ne correspond pas à l'URL proposée. Reconfigurez le webhook.</div>
                <?php endif; ?>
                <?php if ($messageEtat !== ''): ?>
                    <div class="alert alert-danger mt-3 mb-0"><?= htmlspecialchars($messageEtat, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">Configurer</h6>
                <form method="post" class="mb-3">
                    <input type="hidden" name="action" value="configurer">
                    <div class="row align-items-end">
                        <div class="col-md-9 mb-2">
                            <label class="form-label">URL du webhook</label>
                            <input type="url" name="url_webhook" class="form-control" value="<?= htmlspecialchars($urlProposee, ENT_QUOTES, 'UTF-8') ?>" required>
                            <div class="form-text">Cliquez si vous changez d'URL (ex: nouveau domaine).</div>
                        </div>
                        <div class="col-md-3 mb-2">
                            <button type="submit" class="btn btn-outline-primary w-100" <?= $boutonConfigurerDesactive ? 'disabled' : '' ?>>Configurer webhook</button>
                        </div>
                    </div>
                </form>

                <form method="post">
                    <input type="hidden" name="action" value="desactiver">
                    <button type="submit" class="btn btn-outline-danger" <?= !$estConfigure ? 'disabled' : '' ?>>Désactiver le webhook</button>
                </form>
            </div>
        </div>

        <div class="mt-4">
            <a class="btn btn-light" href="administrateur.php">Retour admin</a>
        </div>
    </div>
</body>
</html>
