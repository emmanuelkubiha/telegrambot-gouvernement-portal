# GUICHET ADMINISTRATIF SUD-KIVU

Système de gestion de documents administratifs via bot Telegram.

## DÉMARRAGE RAPIDE

### 1. Créer la base de données
Voir le fichier : **[EXECUTER_SQL.md](EXECUTER_SQL.md)**

**Résumé :**
- Ouvrir phpMyAdmin
- Exécuter le fichier `base_de_donnees_sql/base_de_donnees_guichet.sql`

### 2. Se connecter à l'administration
```
http://localhost:8888/guichet-admin/
```

**Identifiants par défaut :**
- Identifiant : `asnath`
- Mot de passe : `1234`

### 3. Configurer le bot Telegram
Voir le fichier : **[CONFIGURATION_BOT.md](CONFIGURATION_BOT.md)**

**Résumé :**
- Installer ngrok
- Lancer : `ngrok http 8888`
- Configurer le webhook avec l'URL ngrok

---

## FONCTIONNALITÉS

### Pour les citoyens (via Telegram)
- Demande de documents officiels via @guichet_sk_bot
- Vérification automatique de l'identité
- Génération de PDF en temps réel
- Téléchargement sécurisé avec expiration

### Pour les administrateurs (via interface web)
- Gestion des citoyens (CRUD complet)
- Gestion des administrateurs (CRUD complet)
- Consultation de l'historique des demandes avec :
  - Identité de l'utilisateur Telegram (prénom, nom, username)
  - Informations du citoyen
  - Document demandé et statut
  - Date et heure de chaque demande
- Interface moderne Bootstrap 5

---

## DOCUMENTS DISPONIBLES

- Attestation de résidence
- Certificat de scolarité
- Attestation de naissance
- Attestation de bonne vie et moeurs
- Certificat de célibat

---

## DÉPLOIEMENT EN PRODUCTION

Le système est prêt pour la production sur **asnath.etskushinganine.com**.

### Configuration automatique

Le système détecte automatiquement l'environnement :
- **Local (MAMP)** : Utilise `configuration.php`
- **Production (etskushinganine.com)** : Utilise `configuration_production.php`

### Mise en ligne rapide (5 étapes)

1. Uploader les fichiers via FTP/cPanel
2. Configurer les permissions (777 pour documents_pdf/ et journaux/)
3. Importer `base_de_donnees_sql/schema_production.sql` dans phpMyAdmin
4. Configurer le webhook : https://asnath.etskushinganine.com/guichet-admin/configurer_webhook.php
5. Tester le bot sur Telegram

**Guide complet :** Voir [DEPLOIEMENT.md](DEPLOIEMENT.md) et [INSTALLATION_PRODUCTION.md](INSTALLATION_PRODUCTION.md)

---

## ARCHITECTURE

```
guichet-admin/
├── configuration/
│   ├── configuration.php              # Configuration locale (MAMP)
│   ├── configuration_production.php   # Configuration production (hébergeur)
│   └── messages_telegram.php          # Messages du bot
├── base_de_donnees_sql/
│   ├── base_de_donnees_guichet.sql    # Script SQL local
│   └── schema_production.sql          # Script SQL production
├── index.php                          # Page d'accueil publique
├── connexion_admin.php                # Connexion administrateur
├── administrateur.php                 # Interface d'administration
├── reception_telegram.php             # Webhook Telegram
├── telecharger.php                    # Téléchargement PDF
└── .htaccess                          # Sécurité production

Documentation:
├── README.md                          # Ce fichier
├── DEPLOIEMENT.md                     # Guide rapide déploiement production
├── INSTALLATION_PRODUCTION.md         # Guide complet production
├── EXECUTER_SQL.md                    # Guide création base de données
├── CONFIGURATION_BOT.md               # Guide configuration Telegram
├── DEPANNAGE.md                       # Guide dépannage
├── MIGRATION_TELEGRAM.md              # Migration infos utilisateur
├── INSTALLATION.md                    # Guide d'installation détaillé
└── GUIDE.md                           # Documentation technique complète
```

---

## CONFIGURATION

Tout se configure dans : `configuration/configuration.php`

```php
'token_telegram' => 'VOTRE_TOKEN',
'duree_document_minutes' => 15,
'base_de_donnees' => [
    'host' => 'localhost',
    'port' => 8889,
    'nom_base_de_donnees' => 'base_de_donnees_guichet_sud_kivu',
    // ...
]
```

---

## TECHNOLOGIES

- PHP 8+ (strict types)
- MySQL (via MAMP)
- Telegram Bot API
- Bootstrap 5
- Génération PDF native (sans librairie externe)

---

## SÉCURITÉ

- Authentification administrateur avec sessions PHP
- Mots de passe hashés (bcrypt)
- Token secret pour webhook Telegram
- Documents temporaires auto-supprimés
- Vérification des pièces d'identité dans la base

---

## GUIDES

| Guide | Description |
|-------|-------------|
| [EXECUTER_SQL.md](EXECUTER_SQL.md) | Créer la base de données (OBLIGATOIRE) |
| [CONFIGURATION_BOT.md](CONFIGURATION_BOT.md) | Configurer le bot Telegram avec ngrok |
| [INSTALLATION.md](INSTALLATION.md) | Installation et résolution de problèmes |
| [GUIDE.md](GUIDE.md) | Documentation technique complète |

---

## SUPPORT

Pour toute question :
1. Consultez les fichiers de documentation ci-dessus
2. Vérifiez les logs dans `journaux/application.log`
3. Assurez-vous que la base de données est créée

---

## LICENCE

Système développé pour le Guichet Administratif Sud-Kivu.
