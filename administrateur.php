<?php

declare(strict_types=1);

// RÔLE DU FICHIER:
// Interface administrateur complète avec authentification.
// CRUD citoyens, CRUD administrateurs, liste des demandes.

require_once __DIR__ . '/demarrage.php';
require_once __DIR__ . '/fonctions.php';

session_start();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['admin_id'])) {
    header('Location: connexion_admin.php');
    exit;
}

date_default_timezone_set($configApplication['fuseau_horaire']);

$messageSucces = '';
$messageErreur = '';
$action = $_GET['action'] ?? 'tableau_bord';
$section = $_GET['section'] ?? 'citoyens';

try {
    $pdo = ouvrir_base_de_donnees($configApplication);

    // GESTION DES ACTIONS
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $postAction = $_POST['action'] ?? '';
        
        // === CITOYENS ===
        if ($postAction === 'ajouter_citoyen') {
            $nomComplet = trim((string) ($_POST['nom_complet'] ?? ''));
            $numeroPiece = trim((string) ($_POST['numero_piece'] ?? ''));
            $dateNaissance = trim((string) ($_POST['date_naissance'] ?? ''));
            $ville = trim((string) ($_POST['ville'] ?? ''));
            
            if ($nomComplet && $numeroPiece && $dateNaissance && $ville) {
                ajouter_citoyen($pdo, $nomComplet, $numeroPiece, $dateNaissance, $ville);
                $messageSucces = 'Citoyen ajouté avec succès.';
            } else {
                $messageErreur = 'Tous les champs sont obligatoires.';
            }
        }
        
        if ($postAction === 'modifier_citoyen') {
            $id = (int) ($_POST['id'] ?? 0);
            $nomComplet = trim((string) ($_POST['nom_complet'] ?? ''));
            $numeroPiece = trim((string) ($_POST['numero_piece'] ?? ''));
            $dateNaissance = trim((string) ($_POST['date_naissance'] ?? ''));
            $ville = trim((string) ($_POST['ville'] ?? ''));
            
            if ($id > 0 && $nomComplet && $numeroPiece && $dateNaissance && $ville) {
                modifier_citoyen($pdo, $id, $nomComplet, $numeroPiece, $dateNaissance, $ville);
                $messageSucces = 'Citoyen modifié avec succès.';
                $action = 'tableau_bord';
            } else {
                $messageErreur = 'Tous les champs sont obligatoires.';
            }
        }
        
        if ($postAction === 'supprimer_citoyen') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0) {
                supprimer_citoyen($pdo, $id);
                $messageSucces = 'Citoyen supprimé avec succès.';
            }
        }
        
        // === ADMINISTRATEURS ===
        if ($postAction === 'ajouter_administrateur') {
            $nomComplet = trim((string) ($_POST['nom_complet'] ?? ''));
            $identifiant = trim((string) ($_POST['identifiant'] ?? ''));
            $motDePasse = trim((string) ($_POST['mot_de_passe'] ?? ''));
            
            if ($nomComplet && $identifiant && $motDePasse) {
                ajouter_administrateur($pdo, $nomComplet, $identifiant, $motDePasse);
                $messageSucces = 'Administrateur ajouté avec succès.';
            } else {
                $messageErreur = 'Tous les champs sont obligatoires.';
            }
        }
        
        if ($postAction === 'modifier_administrateur') {
            $id = (int) ($_POST['id'] ?? 0);
            $nomComplet = trim((string) ($_POST['nom_complet'] ?? ''));
            $identifiant = trim((string) ($_POST['identifiant'] ?? ''));
            $motDePasse = trim((string) ($_POST['mot_de_passe'] ?? ''));
            
            if ($id > 0 && $nomComplet && $identifiant) {
                modifier_administrateur($pdo, $id, $nomComplet, $identifiant, $motDePasse !== '' ? $motDePasse : null);
                $messageSucces = 'Administrateur modifié avec succès.';
                $action = 'tableau_bord';
            } else {
                $messageErreur = 'Le nom et l\'identifiant sont obligatoires.';
            }
        }
        
        if ($postAction === 'supprimer_administrateur') {
            $id = (int) ($_POST['id'] ?? 0);
            if ($id > 0 && $id !== $_SESSION['admin_id']) {
                supprimer_administrateur($pdo, $id);
                $messageSucces = 'Administrateur supprimé avec succès.';
            } else if ($id === $_SESSION['admin_id']) {
                $messageErreur = 'Vous ne pouvez pas supprimer votre propre compte.';
            }
        }

        // === WEBHOOK TELEGRAM ===
        // Configuration via la page dédiée configurer_webhook.php
    }

    // CHARGEMENT DES DONNÉES
    $citoyens = lister_citoyens($pdo, 100);
    $administrateurs = lister_administrateurs($pdo);
    $demandes = lister_demandes_documents($pdo, 100);
    // Webhook géré sur la page dédiée
    
    // Pour l'édition
    $citoyenAEditer = null;
    $adminAEditer = null;
    if ($action === 'editer_citoyen' && isset($_GET['id'])) {
        $citoyenAEditer = citoyen_par_id($pdo, (int) $_GET['id']);
    }
    if ($action === 'editer_administrateur' && isset($_GET['id'])) {
        $stmt = $pdo->prepare('SELECT id, nom_complet, identifiant FROM administrateurs WHERE id = :id');
        $stmt->execute(['id' => (int) $_GET['id']]);
        $adminAEditer = $stmt->fetch();
    }

} catch (Throwable $e) {
    $messageErreur = 'Erreur: ' . $e->getMessage();
    journaliser($configApplication, 'ERREUR', 'Erreur page administrateur', ['message' => $e->getMessage()]);
    $citoyens = [];
    $administrateurs = [];
    $demandes = [];
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - Guichet Sud-Kivu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { 
            min-height: 100vh; 
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%); 
            color: white; 
        }
        .sidebar .nav-link { 
            color: rgba(255,255,255,0.8); 
            border-radius: 5px; 
            margin: 5px 0; 
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { 
            background-color: rgba(255,255,255,0.1); 
            color: white; 
        }
        .card { 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
            margin-bottom: 30px; 
            border: none; 
        }
        .table { background-color: white; }
        .btn-action { padding: 2px 8px; font-size: 0.85rem; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-2 sidebar p-4">
                <h5 class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-building me-2" viewBox="0 0 16 16">
                        <path d="M4 2.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3 0a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1ZM4 5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM7.5 5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1ZM4.5 8a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Zm2.5.5a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-1a.5.5 0 0 1-.5-.5v-1Zm3.5-.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5h-1Z"/>
                        <path d="M2 1a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V1Zm11 0H3v14h3v-2.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 .5.5V15h3V1Z"/>
                    </svg>
                    Guichet Admin
                </h5>
                <hr class="bg-white">
                <nav class="nav flex-column">
                    <a class="nav-link <?= $section === 'citoyens' ? 'active' : '' ?>" href="?section=citoyens">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-people me-2" viewBox="0 0 16 16">
                            <path d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1h8zm-7.978-1A.261.261 0 0 1 7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002a.274.274 0 0 1-.014.002H7.022zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4zm3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM6.936 9.28a5.88 5.88 0 0 0-1.23-.247A7.35 7.35 0 0 0 5 9c-4 0-5 3-5 4 0 .667.333 1 1 1h4.216A2.238 2.238 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816zM4.92 10A5.493 5.493 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275zM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0zm3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4z"/>
                        </svg>
                        Citoyens
                    </a>
                    <a class="nav-link <?= $section === 'administrateurs' ? 'active' : '' ?>" href="?section=administrateurs">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-shield-lock me-2" viewBox="0 0 16 16">
                            <path d="M5.338 1.59a61.44 61.44 0 0 0-2.837.856.481.481 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.725 10.725 0 0 0 2.287 2.233c.346.244.652.42.893.533.12.057.218.095.293.118a.55.55 0 0 0 .101.025.615.615 0 0 0 .1-.025c.076-.023.174-.061.294-.118.24-.113.547-.29.893-.533a10.726 10.726 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.54 1.54 0 0 1 1.044 1.262c.596 4.477-.787 7.795-2.465 9.99a11.775 11.775 0 0 1-2.517 2.453 7.159 7.159 0 0 1-1.048.625c-.28.132-.581.24-.829.24s-.548-.108-.829-.24a7.158 7.158 0 0 1-1.048-.625 11.777 11.777 0 0 1-2.517-2.453C1.928 10.487.545 7.169 1.141 2.692A1.54 1.54 0 0 1 2.185 1.43 62.456 62.456 0 0 1 5.072.56z"/>
                            <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99a1.5 1.5 0 1 1 2-1.415z"/>
                        </svg>
                        Administrateurs
                    </a>
                    <a class="nav-link <?= $section === 'demandes' ? 'active' : '' ?>" href="?section=demandes">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-text me-2" viewBox="0 0 16 16">
                            <path d="M5.5 7a.5.5 0 0 0 0 1h5a.5.5 0 0 0 0-1h-5zM5 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z"/>
                            <path d="M9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.5L9.5 0zm0 1v2A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z"/>
                        </svg>
                        Demandes
                    </a>
                </nav>
                <hr class="bg-white mt-4">
                <div class="mb-3">
                    <small>Connecté: <?= htmlspecialchars($_SESSION['admin_nom'], ENT_QUOTES, 'UTF-8') ?></small>
                </div>
                <a href="deconnexion_admin.php" class="btn btn-outline-light btn-sm w-100">Déconnexion</a>
                <a href="index.php" class="btn btn-outline-light btn-sm w-100 mt-2">Accueil</a>
            </div>

            <!-- CONTENU PRINCIPAL -->
            <div class="col-md-10 p-4">
                <h3 class="mb-4">Tableau de bord</h3>

                <?php if ($messageSucces !== ''): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Succès</strong> <?= htmlspecialchars($messageSucces, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($messageErreur !== ''): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Erreur</strong> <?= htmlspecialchars($messageErreur, ENT_QUOTES, 'UTF-8') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Webhook Telegram</h6>
                    </div>
                    <div class="card-body d-flex flex-wrap gap-2 align-items-center">
                        <div class="text-muted">Configurer et verifier l'URL de webhook.</div>
                        <a href="configurer_webhook.php" class="btn btn-outline-primary btn-sm">Ouvrir la page webhook</a>
                    </div>
                </div>

                <!-- SECTION CITOYENS -->
                <?php if ($section === 'citoyens'): ?>
                    <?php if ($action === 'editer_citoyen' && $citoyenAEditer): ?>
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Modifier un citoyen</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="administrateur.php?section=citoyens">
                                    <input type="hidden" name="action" value="modifier_citoyen">
                                    <input type="hidden" name="id" value="<?= (int) $citoyenAEditer['id'] ?>">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom complet</label>
                                            <input type="text" name="nom_complet" class="form-control" value="<?= htmlspecialchars($citoyenAEditer['nom_complet'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro de pièce</label>
                                            <input type="text" name="numero_piece" class="form-control" value="<?= htmlspecialchars($citoyenAEditer['numero_piece'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de naissance</label>
                                            <input type="date" name="date_naissance" class="form-control" value="<?= htmlspecialchars($citoyenAEditer['date_naissance'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ville</label>
                                            <input type="text" name="ville" class="form-control" value="<?= htmlspecialchars($citoyenAEditer['ville'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                                    <a href="administrateur.php?section=citoyens" class="btn btn-secondary">Annuler</a>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Ajouter un citoyen</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="administrateur.php?section=citoyens">
                                    <input type="hidden" name="action" value="ajouter_citoyen">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Nom complet</label>
                                            <input type="text" name="nom_complet" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Numéro de pièce</label>
                                            <input type="text" name="numero_piece" class="form-control" placeholder="Ex: OP-14862992" required>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Date de naissance</label>
                                            <input type="date" name="date_naissance" class="form-control" required>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Ville</label>
                                            <input type="text" name="ville" class="form-control" placeholder="Ex: Bukavu" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter le citoyen</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Liste des citoyens</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom</th>
                                                <th>Numéro pièce</th>
                                                <th>Date naissance</th>
                                                <th>Ville</th>
                                                <th>Date création</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (empty($citoyens)): ?>
                                            <tr><td colspan="7" class="text-center text-muted py-4">Aucun citoyen enregistré</td></tr>
                                        <?php endif; ?>
                                        <?php foreach ($citoyens as $citoyen): ?>
                                            <tr>
                                                <td><span class="badge bg-secondary"><?= (int) $citoyen['id'] ?></span></td>
                                                <td><strong><?= htmlspecialchars((string) $citoyen['nom_complet'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                                <td><code><?= htmlspecialchars((string) $citoyen['numero_piece'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                                <td><?= htmlspecialchars((string) $citoyen['date_naissance'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars((string) $citoyen['ville'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><small class="text-muted"><?= htmlspecialchars((string) $citoyen['date_creation'], ENT_QUOTES, 'UTF-8') ?></small></td>
                                                <td>
                                                    <a href="?section=citoyens&action=editer_citoyen&id=<?= (int) $citoyen['id'] ?>" class="btn btn-sm btn-warning btn-action">Modifier</a>
                                                    <form method="post" action="administrateur.php?section=citoyens" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?');">
                                                        <input type="hidden" name="action" value="supprimer_citoyen">
                                                        <input type="hidden" name="id" value="<?= (int) $citoyen['id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger btn-action">Supprimer</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- SECTION ADMINISTRATEURS -->
                <?php if ($section === 'administrateurs'): ?>
                    <?php if ($action === 'editer_administrateur' && $adminAEditer): ?>
                        <div class="card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0">Modifier un administrateur</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="administrateur.php?section=administrateurs">
                                    <input type="hidden" name="action" value="modifier_administrateur">
                                    <input type="hidden" name="id" value="<?= (int) $adminAEditer['id'] ?>">
                                    <div class="mb-3">
                                        <label class="form-label">Nom complet</label>
                                        <input type="text" name="nom_complet" class="form-control" value="<?= htmlspecialchars($adminAEditer['nom_complet'], ENT_QUOTES, 'UTF-8') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Identifiant</label>
                                        <input type="text" name="identifiant" class="form-control" value="<?= htmlspecialchars($adminAEditer['identifiant'], ENT_QUOTES, 'UTF-8') ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Nouveau mot de passe (laisser vide pour ne pas changer)</label>
                                        <input type="password" name="mot_de_passe" class="form-control">
                                    </div>
                                    <button type="submit" class="btn btn-warning">Enregistrer les modifications</button>
                                    <a href="administrateur.php?section=administrateurs" class="btn btn-secondary">Annuler</a>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0">Ajouter un administrateur</h5>
                            </div>
                            <div class="card-body">
                                <form method="post" action="administrateur.php?section=administrateurs">
                                    <input type="hidden" name="action" value="ajouter_administrateur">
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Nom complet</label>
                                            <input type="text" name="nom_complet" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Identifiant</label>
                                            <input type="text" name="identifiant" class="form-control" required>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label">Mot de passe</label>
                                            <input type="password" name="mot_de_passe" class="form-control" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Ajouter l'administrateur</button>
                                </form>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0">Liste des administrateurs</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nom</th>
                                                <th>Identifiant</th>
                                                <th>Date création</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php foreach ($administrateurs as $admin): ?>
                                            <tr>
                                                <td><span class="badge bg-secondary"><?= (int) $admin['id'] ?></span></td>
                                                <td><strong><?= htmlspecialchars((string) $admin['nom_complet'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                                <td><code><?= htmlspecialchars((string) $admin['identifiant'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                                <td><small class="text-muted"><?= htmlspecialchars((string) $admin['date_creation'], ENT_QUOTES, 'UTF-8') ?></small></td>
                                                <td>
                                                    <a href="?section=administrateurs&action=editer_administrateur&id=<?= (int) $admin['id'] ?>" class="btn btn-sm btn-warning btn-action">Modifier</a>
                                                    <?php if ((int) $admin['id'] !== $_SESSION['admin_id']): ?>
                                                        <form method="post" action="administrateur.php?section=administrateurs" style="display:inline;" onsubmit="return confirm('Confirmer la suppression ?');">
                                                            <input type="hidden" name="action" value="supprimer_administrateur">
                                                            <input type="hidden" name="id" value="<?= (int) $admin['id'] ?>">
                                                            <button type="submit" class="btn btn-sm btn-danger btn-action">Supprimer</button>
                                                        </form>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- SECTION DEMANDES -->
                <?php if ($section === 'demandes'): ?>
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0">Historique des demandes de documents</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Utilisateur Telegram</th>
                                            <th>Citoyen</th>
                                            <th>Numéro pièce</th>
                                            <th>Document demandé</th>
                                            <th>Statut</th>
                                            <th>Date / Heure</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php if (empty($demandes)): ?>
                                        <tr><td colspan="7" class="text-center text-muted py-4">Aucune demande enregistrée</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($demandes as $demande): 
                                        // Construire l'affichage de l'utilisateur Telegram
                                        $displayNom = [];
                                        if (!empty($demande['prenom_telegram'])) $displayNom[] = htmlspecialchars((string) $demande['prenom_telegram'], ENT_QUOTES, 'UTF-8');
                                        if (!empty($demande['nom_telegram'])) $displayNom[] = htmlspecialchars((string) $demande['nom_telegram'], ENT_QUOTES, 'UTF-8');
                                        $nomComplet = !empty($displayNom) ? implode(' ', $displayNom) : '<em class="text-muted">Inconnu</em>';
                                        $username = !empty($demande['username_telegram']) ? '@' . htmlspecialchars((string) $demande['username_telegram'], ENT_QUOTES, 'UTF-8') : '';
                                    ?>
                                        <tr>
                                            <td><span class="badge bg-secondary"><?= (int) $demande['id'] ?></span></td>
                                            <td>
                                                <strong><?= $nomComplet ?></strong>
                                                <?php if ($username): ?><br><small class="text-primary"><?= $username ?></small><?php endif; ?>
                                                <br><small class="text-muted">Chat: <?= htmlspecialchars((string) $demande['chat_id'], ENT_QUOTES, 'UTF-8') ?></small>
                                            </td>
                                            <td><strong><?= htmlspecialchars((string) $demande['nom_citoyen'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                            <td><code><?= htmlspecialchars((string) $demande['numero_piece'], ENT_QUOTES, 'UTF-8') ?></code></td>
                                            <td><?= htmlspecialchars((string) $demande['document_demande'], ENT_QUOTES, 'UTF-8') ?></td>
                                            <td><span class="badge bg-success"><?= htmlspecialchars((string) $demande['statut'], ENT_QUOTES, 'UTF-8') ?></span></td>
                                            <td><small class="text-muted"><?= htmlspecialchars((string) $demande['date_heure_demande'], ENT_QUOTES, 'UTF-8') ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
