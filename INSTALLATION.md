# INSTALLATION DU SYSTÈME GUICHET

## Problème : Base de données manquante

Si vous voyez cette erreur : `Unknown database 'base_de_donnees_guichet_sud_kivu'`

> **Solution :** Vous devez créer la base de données en suivant les étapes ci-dessous.

---

## TRÈS IMPORTANT : Créer la base de données

Le système ne peut pas fonctionner tant que la base de données n'est pas créée.

### Étapes d'installation :

#### 1. Ouvrir phpMyAdmin
- Démarrer MAMP
- Ouvrir votre navigateur
- Aller à : `http://localhost:8888/phpMyAdmin/` (ou port 8889 selon votre config)

#### 2. Créer la base de données
- Cliquer sur l'onglet **"SQL"** en haut de la page
- Ouvrir le fichier `base_de_donnees_sql/base_de_donnees_guichet.sql` avec un éditeur de texte
- Copier **tout le contenu** du fichier
- Coller dans la zone de texte SQL de phpMyAdmin
- Cliquer sur le bouton **"Exécuter"** en bas à droite

#### 3. Vérifier l'installation
- Dans la colonne de gauche de phpMyAdmin, vous devriez voir : `base_de_donnees_guichet_sud_kivu`
- Cliquer dessus pour voir les 5 tables créées :
  - citoyens
  - types_documents
  - sessions_telegram
  - documents_generes
  - demandes_documents

#### 4. Accéder à l'administration
- Aller à : `http://localhost:8888/guichet-admin/`
- Cliquer sur "Se connecter à l'administration"
- Utiliser les identifiants par défaut :
  - **Identifiant :** asnath
  - **Mot de passe :** 1234
- Vous pouvez maintenant gérer les citoyens, les admins et consulter les demandes

## Configuration du bot Telegram (optionnel)

Pour activer le bot Telegram :
1. Aller à : `http://localhost:8888/guichet-admin/configurer_webhook.php?secret=sud-kivu-2026`
2. Le webhook sera configuré automatiquement
3. Tester en envoyant `/start` à votre bot sur Telegram

## Résolution des problèmes

### Erreur "Unknown database 'base_de_donnees_guichet_sud_kivu'"
> Vous n'avez pas exécuté le fichier SQL dans phpMyAdmin (voir étape 2 ci-dessus)

### Erreur "Table 'demandes_documents' doesn't exist"
> Vous avez exécuté une ancienne version de la base de données. Supprimer la base et recommencer l'étape 2

### Le design ne s'affiche pas correctement
> Vérifier votre connexion internet (Bootstrap est chargé depuis un CDN)
> Vider le cache de votre navigateur (Ctrl+Shift+R ou Cmd+Shift+R)

## Prêt à utiliser

Une fois la base de données créée, le système est opérationnel.
