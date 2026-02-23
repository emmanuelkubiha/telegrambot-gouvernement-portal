# Guide simple du projet

## Objectif
Ce mini projet permet:
1. Un utilisateur parle au bot Telegram.
2. Il envoie son numéro de pièce.
3. Le système vérifie la base de données.
4. Il choisit un document.
5. Le système génère un PDF.
6. Le bot envoie un lien.
7. Le document est supprimé après téléchargement ou expiration.

---

## Structure minimale (très peu de fichiers)

```text
guichet-admin/
├── configuration/
│   └── configuration.php
├── base_de_donnees_sql/
│   └── base_de_donnees_guichet.sql
├── stockage/
│   └── documents/
├── journaux/
├── demarrage.php
├── fonctions.php
├── index.php
├── connexion_admin.php
├── deconnexion_admin.php
├── administrateur.php
├── reception_telegram.php
├── telecharger.php
├── configurer_webhook.php
├── .gitignore
├── INSTALLATION.md
└── GUIDE.md
```

---

## Rôle de chaque fichier

- `configuration/configuration.php`
  - Fichier unique de configuration.
  - Contient token Telegram, infos MySQL, URL locale, durée des documents.

- `base_de_donnees_sql/base_de_donnees_guichet.sql`
  - Script SQL pour créer la base + tables + données de test.

- `demarrage.php`
  - Charge la configuration commune.

- `fonctions.php`
  - Contient toutes les fonctions utiles :
    - connexion base de données,
    - envoi Telegram,
    - gestion session,
    - génération PDF,
    - nettoyage des documents expirés,
    - authentification administrateurs,
    - CRUD citoyens (ajouter, modifier, supprimer),
    - CRUD administrateurs (ajouter, modifier, supprimer).

- `index.php`
  - Page d'accueil publique.
  - Explique le fonctionnement du bot Telegram.
  - Lien direct vers le bot : @guichet_sk_bot.
  - Accès à la page de connexion administrateur.

- `connexion_admin.php`
  - Page de connexion pour les administrateurs.
  - Vérifie identifiant et mot de passe.
  - Crée une session sécurisée.

- `deconnexion_admin.php`
  - Déconnexion et destruction de session.

- `administrateur.php`
  - Interface complète avec sidebar et authentification obligatoire.
  - CRUD complet citoyens (ajouter, modifier, supprimer).
  - CRUD complet administrateurs (ajouter, modifier, supprimer).
  - Liste des demandes de documents reçues via Telegram.
  - Design Bootstrap 5 professionnel avec icônes SVG.

- `reception_telegram.php`
  - Webhook principal Telegram.
  - Reçoit les messages et pilote le scénario.

- `telecharger.php`
  - Télécharge le PDF via token puis supprime le fichier.

- `configurer_webhook.php`
  - Enregistre facilement l’URL webhook chez Telegram.

- `stockage/documents/`
  - Contient temporairement les PDF générés.

- `journaux/`
  - Contient les logs techniques.

---

## Mise en route locale (MAMP)

1. Démarre MAMP (Apache + MySQL).
2. Ouvre phpMyAdmin.
3. Exécute `base_de_donnees_sql/base_de_donnees_guichet.sql` (voir `INSTALLATION.md` pour instructions détaillées).
4. Vérifie dans `configuration/configuration.php`:
   - URL locale,
   - paramètres MySQL,
   - token Telegram.
5. Ouvre:
   - `http://localhost:8888/guichet-admin/index.php`

---

## Connecter Telegram avec ngrok

1. Lance ngrok:

```bash
ngrok http 8888
```

2. Copie l’URL HTTPS, exemple:

```text
https://abcd-1234.ngrok-free.app
```

3. Configure le webhook via navigateur:

```text
http://localhost:8888/guichet-admin/configurer_webhook.php?url=https://abcd-1234.ngrok-free.app/guichet-admin/reception_telegram.php
```

4. Si la réponse contient `"ok": true`, le webhook est actif.

---

## Test rapide

1. Dans Telegram, envoie `/start` au bot.
2. Envoie `OP-14862992`.
3. Choisis un document.
4. Clique le lien reçu.
5. Le PDF se télécharge puis est supprimé.

---

## Modèles de documents proposés dans le bot

Le bot propose actuellement ces modèles:
- Attestation de residence
- Certificat de scolarite
- Attestation de naissance
- Attestation de bonne vie et moeurs
- Certificat de celibat

Ces modèles sont définis dans la table `types_documents` via:
- `base_de_donnees_sql/base_de_donnees_guichet.sql`

---

## Connexion administrateur

Le système dispose d'une authentification sécurisée pour l'accès à l'administration.

### Compte par défaut

Après installation de la base de données, un compte administrateur est créé :
- **Identifiant :** `asnath`
- **Mot de passe :** `1234`

### Première connexion

1. Ouvre : `http://localhost:8888/guichet-admin/`
2. Clique sur "Se connecter à l'administration"
3. Saisis les identifiants par défaut
4. Tu accèdes au tableau de bord

### Sécurité

- Les mots de passe sont hashés avec `password_hash()` (bcrypt)
- Les sessions PHP protègent l'accès aux pages admin
- Un administrateur ne peut pas supprimer son propre compte

---

## Utiliser la partie administrateur

1. Connecte-toi avec tes identifiants
2. Interface avec sidebar de navigation :
  - **Citoyens :** gérer les citoyens (ajouter, modifier, supprimer)
  - **Administrateurs :** gérer les comptes admins (ajouter, modifier, supprimer)
  - **Demandes :** consulter l'historique des demandes Telegram avec :
    - Informations de l'utilisateur Telegram (prénom, nom, username @)
    - Chat ID pour identification technique
    - Citoyen associé (nom et numéro de pièce)
    - Document demandé
    - Statut de la demande
    - Date et heure de la demande
3. Toutes les opérations CRUD disponibles :
  - Ajouter un citoyen ou admin via formulaire
  - Modifier en cliquant sur "Modifier"
  - Supprimer avec confirmation
4. Design professionnel Bootstrap 5 :
  - Sidebar avec dégradé violet
  - Tableaux responsives
  - Icônes SVG professionnelles (pas d'emojis)
  - Alertes de succès/erreur
5. Déconnexion via le bouton en bas de sidebar

---

## Règle de maintenance du guide

À chaque modification de structure (nouveau fichier/dossier, suppression, renommage) ou de comportement important,
le fichier `GUIDE.md` doit être mis à jour dans la même intervention.
