# GUIDE D'INSTALLATION EN PRODUCTION

## Informations de l'hébergement

- **Domaine :** https://asnath.etskushinganine.com
- **Base de données :** u783961849_guichet_unique
- **Utilisateur MySQL :** u783961849_guichet_unique
- **Configuration :** configuration_production.php (chargée automatiquement)

---

## ✅ ÉTAPE 1 : Uploader les fichiers

### Via FTP/cPanel File Manager

Uploader **tous les fichiers** du dossier `guichet-admin` vers :
```
/public_html/guichet-admin/
```

ou

```
/home/u783961849/public_html/guichet-admin/
```

**Fichiers à uploader :**
- Tous les fichiers .php
- Dossier `configuration/`
- Dossier `base_de_donnees_sql/`
- Dossier `documents_pdf/` (vide)
- Dossier `journaux/` (vide)
- Fichier `.htaccess`

---

## ✅ ÉTAPE 2 : Configurer les permissions

Via FTP ou cPanel, définir les permissions :

```
chmod 755 /public_html/guichet-admin/
chmod 755 configuration/
chmod 644 configuration/*.php
chmod 777 documents_pdf/
chmod 777 journaux/
chmod 644 .htaccess
```

**Permissions importantes :**
- `documents_pdf/` doit être **777** (écriture pour générer les PDF)
- `journaux/` doit être **777** (écriture pour les logs)
- Les autres fichiers : **644**
- Les dossiers : **755**

---

## ✅ ÉTAPE 3 : Créer la base de données

### Via phpMyAdmin de cPanel

1. Se connecter à cPanel
2. Ouvrir **phpMyAdmin**
3. Sélectionner la base : `u783961849_guichet_unique`
4. Cliquer sur l'onglet **"SQL"**
5. Ouvrir le fichier `base_de_donnees_sql/base_de_donnees_guichet.sql` dans un éditeur
6. **IMPORTANT :** Modifier la première ligne :
   ```sql
   -- Remplacer
   CREATE DATABASE IF NOT EXISTS base_de_donnees_guichet_sud_kivu...
   USE base_de_donnees_guichet_sud_kivu;
   
   -- Par
   -- La base existe déjà, on l'utilise directement
   USE u783961849_guichet_unique;
   ```
7. Copier tout le contenu (sauf la ligne CREATE DATABASE)
8. Coller dans phpMyAdmin
9. Cliquer sur **"Exécuter"**

✓ Vous devez voir : "6 tables créées, X lignes insérées"

---

## ✅ ÉTAPE 4 : Configurer le webhook Telegram

### Depuis votre ordinateur local

Ouvrir le terminal et exécuter :

```bash
curl "https://asnath.etskushinganine.com/guichet-admin/configurer_webhook.php?url=https://asnath.etskushinganine.com/guichet-admin/reception_telegram.php"
```

**Réponse attendue :**
```json
{
  "ok": true,
  "result": true,
  "description": "Webhook was set"
}
```

### Ou via le navigateur

Ouvrir cette URL dans votre navigateur :
```
https://asnath.etskushinganine.com/guichet-admin/configurer_webhook.php?url=https://asnath.etskushinganine.com/guichet-admin/reception_telegram.php
```

✓ Si vous voyez `"ok": true`, c'est bon !

---

## ✅ ÉTAPE 5 : Vérifier l'installation

### Test 1 : Accès à l'interface admin

1. Ouvrir : https://asnath.etskushinganine.com/guichet-admin/
2. Vous devez voir la page d'accueil avec le lien vers le bot
3. Cliquer sur "Vous êtes admin ? Connectez-vous"
4. Se connecter avec :
   - **Identifiant :** asnath
   - **Mot de passe :** 1234

✓ Vous devez accéder au tableau de bord

### Test 2 : Tester le bot Telegram

1. Ouvrir Telegram
2. Rechercher : **@guichet_sk_bot**
3. Envoyer : `/start`
4. Le bot doit répondre immédiatement
5. Envoyer : `OP-14862992`
6. Le bot doit reconnaître l'identité
7. Choisir un document
8. Le bot doit générer et envoyer le PDF

✓ Si tout fonctionne, l'installation est réussie !

---

## ✅ ÉTAPE 6 : Sécurité supplémentaire (recommandé)

### A. Changer le mot de passe admin

1. Se connecter à l'interface admin
2. Aller dans "Administrateurs"
3. Cliquer sur "Modifier" pour l'admin "Asnath"
4. Entrer un nouveau mot de passe fort
5. Sauvegarder

### B. Installer un certificat SSL (si pas déjà fait)

Via cPanel :
1. **SSL/TLS Status**
2. Cliquer sur **"Run AutoSSL"**
3. Attendre quelques minutes

✓ Le site passera en HTTPS automatiquement

### C. Configurer les sauvegardes automatiques

Via cPanel :
1. **Backup**
2. Configurer des sauvegardes quotidiennes de :
   - La base de données
   - Le dossier `/guichet-admin/`

---

## 🔍 Vérifier l'état du système

Via le navigateur :
```
https://asnath.etskushinganine.com/guichet-admin/diagnostic.php
```

Ce fichier vous indiquera :
- ✓ Base de données connectée
- ✓ Tables créées
- ✓ Webhook configuré
- ✓ Permissions correctes

---

## ⚠️ Dépannage

### Erreur : "Can't connect to database"

**Solution :** Vérifier que `configuration_production.php` a les bons identifiants MySQL.

### Erreur : "Failed to write file"

**Solution :** Vérifier les permissions des dossiers `documents_pdf/` et `journaux/` (doivent être 777).

### Le bot ne répond pas

**Solutions :**
1. Vérifier que le webhook est configuré :
   ```bash
   curl "https://api.telegram.org/bot8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw/getWebhookInfo"
   ```
2. Vérifier les logs : `journaux/application.log`
3. Reconfigurer le webhook (Étape 4)

### Erreur 500 Internal Server Error

**Solutions :**
1. Vérifier les permissions des fichiers
2. Vérifier le fichier `.htaccess`
3. Consulter les logs d'erreur de cPanel

---

## 📞 Support

En cas de problème :
1. Consulter `journaux/application.log` pour les erreurs
2. Exécuter `diagnostic.php` pour identifier le problème
3. Consulter [DEPANNAGE.md](DEPANNAGE.md)

---

## 🎉 Félicitations !

Votre bot est maintenant en production et fonctionne 24/7 !

Les citoyens peuvent maintenant demander leurs documents via **@guichet_sk_bot** et vous pouvez gérer tout depuis l'interface admin.

---

## 📝 Différences Local vs Production

| Aspect | Local (MAMP) | Production (Hébergeur) |
|--------|--------------|------------------------|
| Configuration | `configuration.php` | `configuration_production.php` (auto) |
| Base de données | `base_de_donnees_guichet_sud_kivu` | `u783961849_guichet_unique` |
| Port MySQL | 8889 | 3306 |
| URL | `localhost:8888` | `asnath.etskushinganine.com` |
| ngrok | ✅ Requis | ❌ Pas besoin |
| Webhook | Change à chaque démarrage | Permanent |
| SSL/HTTPS | Optionnel | Recommandé |

Le système détecte automatiquement l'environnement grâce au nom de domaine dans `demarrage.php`.
