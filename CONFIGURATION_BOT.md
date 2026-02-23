# CONFIGURATION DU BOT TELEGRAM

## Prérequis

Avant de commencer, assurez-vous que :
1. La base de données est créée (voir EXECUTER_SQL.md)
2. MAMP est démarré (Apache + MySQL)
3. Vous avez un bot Telegram configuré avec BotFather

---

## ÉTAPE 1 : Vérifier la configuration

Le bot est déjà configuré avec :
- **Token :** 8302001753:AAFcnw3AqHZ_Ix_rzopao2uEJCtBWoBDAAw
- **Nom du bot :** @guichet_sk_bot

Cette configuration se trouve dans : `configuration/configuration.php`

---

## ÉTAPE 2 : Installer ngrok (pour test local)

Ngrok permet d'exposer votre serveur local sur Internet pour que Telegram puisse envoyer les messages.

### Télécharger ngrok
1. Aller sur : https://ngrok.com/download
2. Télécharger la version pour macOS
3. Décompresser et placer ngrok dans /usr/local/bin

### Ou installer via Homebrew
```bash
brew install ngrok/ngrok/ngrok
```

---

## ÉTAPE 3 : Démarrer ngrok

Dans un terminal, exécuter :

```bash
ngrok http 8888
```

Vous verrez quelque chose comme :
```
Session Status    online
Forwarding        https://abc123.ngrok-free.app -> http://localhost:8888
```

**IMPORTANT :** Copiez l'URL HTTPS (exemple: https://abc123.ngrok-free.app)

---

## ÉTAPE 4 : Configurer le webhook Telegram

### Méthode 1 : Via le navigateur

1. Ouvrir votre navigateur
2. Aller à cette URL (remplacez YOUR_NGROK_URL) :

```
http://localhost:8888/guichet-admin/configurer_webhook.php?url=YOUR_NGROK_URL/guichet-admin/reception_telegram.php
```

**Exemple concret :**
```
http://localhost:8888/guichet-admin/configurer_webhook.php?url=https://abc123.ngrok-free.app/guichet-admin/reception_telegram.php
```

3. Si vous voyez `"ok": true`, c'est bon !

### Méthode 2 : Via terminal (avec curl)

```bash
curl "http://localhost:8888/guichet-admin/configurer_webhook.php?url=https://abc123.ngrok-free.app/guichet-admin/reception_telegram.php"
```

---

## ÉTAPE 5 : Tester le bot

1. Ouvrir Telegram
2. Rechercher : @guichet_sk_bot
3. Démarrer une conversation
4. Envoyer : `/start`
5. Le bot doit répondre avec un message de bienvenue

### Test complet

1. Tapez `/start`
2. Envoyez un numéro de pièce : `OP-14862992` (exemple de test)
3. Choisissez un document dans la liste
4. Recevez le lien de téléchargement

---

## MESSAGES DU BOT

Les messages sont configurables dans : `configuration/messages_telegram.php`

Vous pouvez modifier :
- Message de bienvenue
- Messages d'erreur
- Instructions
- Etc.

---

## COMMANDES DISPONIBLES

- `/start` - Démarrer une nouvelle demande de document
- `/aide` ou `/help` - Afficher l'aide

---

## DÉPANNAGE

### Erreur "Webhook not set"
> Vérifiez que ngrok est bien démarré et que l'URL est correcte

### Le bot ne répond pas
> Vérifiez les logs dans : `journaux/application.log`

### Erreur "Base table or view not found"
> Vous devez créer la base de données (voir EXECUTER_SQL.md)

### Ngrok affiche "403 Forbidden"
> Vérifiez le secret webhook dans `configuration/configuration.php`

### L'URL ngrok change à chaque redémarrage
> C'est normal avec la version gratuite. Vous devrez reconfigurer le webhook à chaque fois.
> Solution : Créer un compte ngrok gratuit pour avoir une URL stable.

---

## POUR LA PRODUCTION

Pour un déploiement en production :

1. Ne pas utiliser ngrok
2. Héberger sur un serveur avec HTTPS (obligatoire pour Telegram)
3. Configurer le webhook avec l'URL publique

Exemple :
```
https://votredomaine.com/guichet-admin/reception_telegram.php
```

---

## SÉCURITÉ

Le système utilise :
- Token secret pour le webhook : `sud-kivu-2026`
- Vérification des numéros de pièce dans la base de données
- Sessions Telegram uniques par utilisateur
- Documents temporaires avec expiration automatique (15 minutes)
- Suppression automatique après téléchargement

---

## RÉSUMÉ DES FICHIERS

| Fichier | Rôle |
|---------|------|
| `configuration/configuration.php` | Configuration générale |
| `configuration/messages_telegram.php` | Messages du bot |
| `reception_telegram.php` | Traitement des messages |
| `configurer_webhook.php` | Configuration du webhook |
| `telecharger.php` | Téléchargement des documents |

---

## BESOIN D'AIDE ?

Consultez le fichier `GUIDE.md` pour plus d'informations sur l'architecture du système.
