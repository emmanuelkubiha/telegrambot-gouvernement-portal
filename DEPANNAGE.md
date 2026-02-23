# GUIDE DE DÉPANNAGE - BOT TELEGRAM

## DIAGNOSTIC RAPIDE

Exécutez cette commande pour vérifier l'état du système :

```bash
php diagnostic.php
```

Ou ouvrez dans le navigateur :
```
http://localhost:8888/guichet-admin/diagnostic.php
```

---

## PROBLÈME 1 : Le bot ne répond pas

### Causes possibles

#### A. Base de données non créée
**Symptôme :** "Table 'administrateurs' doesn't exist"

**Solution :**
1. Ouvrir phpMyAdmin : http://localhost:8888/phpMyAdmin/
2. Onglet SQL
3. Copier le contenu de `base_de_donnees_sql/base_de_donnees_guichet.sql`
4. Coller et exécuter

Voir : [EXECUTER_SQL.md](EXECUTER_SQL.md)

#### B. Webhook non configuré
**Symptôme :** Le bot ne reçoit pas les messages

**Solution :**
1. Installer ngrok :
   ```bash
   brew install ngrok/ngrok/ngrok
   ```

2. Lancer ngrok :
   ```bash
   ngrok http 8888
   ```

3. Copier l'URL HTTPS (ex: https://abc123.ngrok-free.app)

4. Configurer le webhook :
   ```bash
   curl "http://localhost:8888/guichet-admin/configurer_webhook.php?url=https://abc123.ngrok-free.app/guichet-admin/reception_telegram.php"
   ```

5. Vérifier : vous devez voir `"ok": true`

Voir : [CONFIGURATION_BOT.md](CONFIGURATION_BOT.md)

#### C. MAMP non démarré
**Solution :** Ouvrir MAMP et cliquer sur "Start Servers"

#### D. Token Telegram invalide
**Solution :** Vérifier dans `configuration/configuration.php` que le token est correct

---

## PROBLÈME 2 : Erreur "403 Forbidden" dans ngrok

**Cause :** Le secret webhook ne correspond pas

**Solution :**
1. Ouvrir `configuration/configuration.php`
2. Vérifier : `'secret_webhook_telegram' => 'sud-kivu-2026'`
3. Reconfigurer le webhook

---

## PROBLÈME 3 : L'utilisateur peut taper au clavier

**C'EST NORMAL** pour l'envoi du numéro de pièce d'identité.

Le système fonctionne ainsi :
1. `/start` - Clavier libre (taper le numéro de pièce)
2. Après validation - Clavier avec boutons uniquement
3. Après sélection du document - Clavier supprimé

Si vous voulez forcer l'utilisation de boutons même pour le numéro de pièce, il faudrait :
- Créer une liste de citoyens dans un clavier
- Mais ça ne sera pas pratique avec beaucoup de citoyens

---

## PROBLÈME 4 : "Document non reconnu"

**Cause :** L'utilisateur tape au lieu d'utiliser les boutons

**Solution automatique :**
Le système renvoie maintenant automatiquement le clavier avec boutons si le texte n'est pas reconnu.

**Message affiché :**
```
Document non reconnu.

Veuillez choisir un document dans la liste proposée.
```

Puis le clavier réapparaît avec la mention "Utilisez les boutons ci-dessous".

---

## PROBLÈME 5 : ngrok change d'URL à chaque redémarrage

**C'EST NORMAL** avec la version gratuite.

**Solutions :**

### Option A : Créer un compte ngrok gratuit
1. Aller sur : https://ngrok.com/
2. S'inscrire (gratuit)
3. Obtenir un authtoken
4. Configurer :
   ```bash
   ngrok config add-authtoken VOTRE_TOKEN
   ```
5. Lancer avec un domaine stable (gratuit) :
   ```bash
   ngrok http 8888 --domain=votre-domaine.ngrok-free.app
   ```

### Option B : Reconfigurer à chaque fois
Chaque fois que vous redémarrez ngrok :
1. Copier la nouvelle URL
2. Reconfigurer le webhook

---

## VÉRIFIER L'ÉTAT DU WEBHOOK

```bash
curl "https://api.telegram.org/bot8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw/getWebhookInfo"
```

Remplacez le token par le vôtre.

**Réponse normale :**
```json
{
  "ok": true,
  "result": {
    "url": "https://abc123.ngrok-free.app/guichet-admin/reception_telegram.php",
    "has_custom_certificate": false,
    "pending_update_count": 0
  }
}
```

**Problème :**
- `"url": ""` → Webhook non configuré
- `"pending_update_count": 50` → Messages en attente (problème de connexion)
- `"last_error_message"` → Erreur lors de l'appel du webhook

---

## VÉRIFIER LES LOGS

```bash
tail -f journaux/application.log
```

Ou ouvrir le fichier : `journaux/application.log`

---

## TESTER MANUELLEMENT LE WEBHOOK

Simuler un message Telegram :

```bash
curl -X POST http://localhost:8888/guichet-admin/reception_telegram.php \
  -H "Content-Type: application/json" \
  -H "X-Telegram-Bot-Api-Secret-Token: sud-kivu-2026" \
  -d '{
    "message": {
      "chat": {"id": 123456789},
      "text": "/start"
    }
  }'
```

**Réponse attendue :** `OK`

---

## ORDRE DE DÉPANNAGE RECOMMANDÉ

1. ✓ Exécuter `php diagnostic.php`
2. ✓ Créer la base de données (si nécessaire)
3. ✓ Installer ngrok
4. ✓ Démarrer ngrok
5. ✓ Configurer le webhook
6. ✓ Tester avec Telegram

---

## COMMANDES UTILES

### Vérifier PHP
```bash
php -v
```

### Vérifier MySQL (MAMP)
```bash
/Applications/MAMP/Library/bin/mysql --version
```

### Supprimer le webhook
```bash
curl "http://localhost:8888/guichet-admin/configurer_webhook.php?url="
```

### Voir les processus ngrok
```bash
ps aux | grep ngrok
```

---

## BESOIN D'AIDE ?

1. Vérifier les logs : `journaux/application.log`
2. Exécuter le diagnostic : `php diagnostic.php`
3. Consulter la documentation :
   - [README.md](README.md) - Vue d'ensemble
   - [EXECUTER_SQL.md](EXECUTER_SQL.md) - Base de données
   - [CONFIGURATION_BOT.md](CONFIGURATION_BOT.md) - Configuration bot
   - [GUIDE.md](GUIDE.md) - Documentation complète
