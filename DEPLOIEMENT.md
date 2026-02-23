# DÉPLOIEMENT RAPIDE EN PRODUCTION

## 📋 RÉSUMÉ : 5 étapes pour mettre en ligne

### ✅ 1. Uploader les fichiers (via FTP/cPanel)
Transférer tout le dossier `guichet-admin` vers `/public_html/guichet-admin/`

### ✅ 2. Configurer les permissions (via cPanel)
```
documents_pdf/  → 777
journaux/       → 777
Autres          → 644/755
```

### ✅ 3. Créer les tables (via phpMyAdmin)
Importer le fichier : `base_de_donnees_sql/schema_production.sql`

### ✅ 4. Configurer le webhook (depuis votre navigateur)
```
https://asnath.etskushinganine.com/guichet-admin/configurer_webhook.php?url=https://asnath.etskushinganine.com/guichet-admin/reception_telegram.php
```

### ✅ 5. Tester le bot (sur Telegram)
Envoyer `/start` à @guichet_sk_bot

---

## 🔑 Configuration automatique

Le système détecte automatiquement l'environnement :

**En LOCAL (MAMP) :**
- Utilise `configuration.php`
- Base : `base_de_donnees_guichet_sud_kivu`
- Port : 8889

**En PRODUCTION (asnath.etskushinganine.com) :**
- Utilise `configuration_production.php` ✅
- Base : `u783961849_guichet_unique` ✅
- Port : 3306 ✅

Pas besoin de modifier le code ! Le fichier `demarrage.php` charge automatiquement la bonne configuration selon le domaine.

---

## 📂 Fichiers créés pour la production

1. **configuration/configuration_production.php** - Configuration hébergeur
2. **base_de_donnees_sql/schema_production.sql** - Script SQL adapté
3. **.htaccess** - Sécurité (bloquer accès aux fichiers sensibles)
4. **INSTALLATION_PRODUCTION.md** - Guide complet
5. **demarrage.php** (modifié) - Détection automatique environnement

---

## 🚀 Commandes webhook pratiques

**Configurer le webhook :**
```bash
curl "https://asnath.etskushinganine.com/guichet-admin/configurer_webhook.php?url=https://asnath.etskushinganine.com/guichet-admin/reception_telegram.php"
```

**Vérifier le webhook :**
```bash
curl "https://api.telegram.org/bot8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw/getWebhookInfo"
```

**Supprimer le webhook (si besoin) :**
```bash
curl "https://asnath.etskushinganine.com/guichet-admin/configurer_webhook.php?url="
```

---

## 🔍 Diagnostic

**Vérifier l'état du système :**
```
https://asnath.etskushinganine.com/guichet-admin/diagnostic.php
```

---

## 📚 Documentation complète

Voir [INSTALLATION_PRODUCTION.md](INSTALLATION_PRODUCTION.md) pour le guide détaillé.

---

## ⚠️ Important à savoir

1. **Pas besoin de ngrok** en production
2. **Le webhook est permanent** (pas besoin de le reconfigurer)
3. **Les deux environnements** (local + production) fonctionnent en même temps
4. **Changer le mot de passe admin** après la mise en ligne
5. **Activer SSL/HTTPS** pour plus de sécurité
