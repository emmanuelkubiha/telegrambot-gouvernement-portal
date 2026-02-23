# MIGRATION : AJOUT DES INFORMATIONS UTILISATEUR TELEGRAM

## Contexte

Cette migration ajoute la capture des informations de l'utilisateur Telegram (username, prénom, nom) dans l'historique des demandes.

## Quand appliquer cette migration ?

**Si vous avez DÉJÀ créé la base de données** avant le 23 février 2026, vous devez appliquer cette migration.

**Si vous créez la base de données pour la première fois**, utilisez simplement le fichier `base_de_donnees_guichet.sql` - la migration n'est pas nécessaire car les colonnes sont déjà incluses.

---

## Comment vérifier si vous avez besoin de la migration

1. Ouvrir phpMyAdmin : http://localhost:8889/phpMyAdmin/
2. Sélectionner la base : `base_de_donnees_guichet_sud_kivu`
3. Cliquer sur la table : `demandes_documents`
4. Onglet "Structure"
5. Vérifier si ces colonnes existent :
   - `username_telegram`
   - `prenom_telegram`
   - `nom_telegram`

**Si ces colonnes n'existent pas**, vous devez appliquer la migration.

---

## Appliquer la migration

### Méthode 1 : Via phpMyAdmin (recommandé)

1. Ouvrir phpMyAdmin : http://localhost:8889/phpMyAdmin/
2. Sélectionner la base : `base_de_donnees_guichet_sud_kivu`
3. Cliquer sur l'onglet "SQL" en haut
4. Copier le contenu du fichier : `base_de_donnees_sql/migration_ajouter_infos_telegram.sql`
5. Coller dans la zone de texte
6. Cliquer sur "Exécuter"

**Résultat attendu :**
```
3 colonnes ont été ajoutées à la table demandes_documents
```

### Méthode 2 : Via Terminal

```bash
cd /Applications/MAMP/htdocs/guichet-admin

/Applications/MAMP/Library/bin/mysql -u root -proot -P 8889 base_de_donnees_guichet_sud_kivu < base_de_donnees_sql/migration_ajouter_infos_telegram.sql
```

---

## Vérifier que la migration a réussi

### Dans phpMyAdmin

1. Rafraîchir la page
2. Sélectionner la table `demandes_documents`
3. Onglet "Structure"
4. Vous devez voir :

| Champ | Type | Null |
|-------|------|------|
| ... | ... | ... |
| chat_id | bigint | Non |
| **username_telegram** | **varchar(100)** | **Oui** |
| **prenom_telegram** | **varchar(100)** | **Oui** |
| **nom_telegram** | **varchar(100)** | **Oui** |
| citoyen_id | int | Non |
| ... | ... | ... |

### Via le bot

1. Faites une nouvelle demande via le bot Telegram
2. Connectez-vous à l'interface admin
3. Allez dans "Demandes"
4. La dernière demande doit afficher :
   - Votre prénom et nom Telegram
   - Votre username @
   - Le Chat ID

---

## En cas d'erreur

### Erreur : "Table 'demandes_documents' doesn't exist"

**Cause :** La base de données n'a pas été créée.

**Solution :** Créer d'abord la base de données complète avec `base_de_donnees_guichet.sql` (voir [EXECUTER_SQL.md](EXECUTER_SQL.md))

### Erreur : "Duplicate column name 'username_telegram'"

**Cause :** Les colonnes existent déjà.

**Solution :** Rien à faire, la migration a déjà été appliquée.

### Erreur : "Access denied"

**Cause :** Identifiants MySQL incorrects.

**Solution :** Vérifier les identifiants dans phpMyAdmin ou utiliser les bons identifiants MAMP (généralement root/root).

---

## Rollback (annuler la migration)

Si vous devez annuler la migration :

```sql
USE base_de_donnees_guichet_sud_kivu;

ALTER TABLE demandes_documents 
DROP COLUMN username_telegram,
DROP COLUMN prenom_telegram,
DROP COLUMN nom_telegram;
```

**ATTENTION :** Cela supprimera toutes les données de ces colonnes.

---

## Impact sur les demandes existantes

Les demandes créées **avant** la migration auront ces champs à `NULL`.

Les demandes créées **après** la migration contiendront les informations de l'utilisateur Telegram (si disponibles).

---

## Questions fréquentes

### Q: Pourquoi certains utilisateurs n'ont pas de username ?

**R:** Sur Telegram, le username est optionnel. Certains utilisateurs n'en ont pas configuré. Dans ce cas, seuls le prénom et le nom seront affichés.

### Q: Le numéro de téléphone n'est pas capturé ?

**R:** L'API Telegram ne permet pas aux bots d'accéder au numéro de téléphone des utilisateurs par défaut. Il faudrait demander une permission spéciale (contact sharing), ce qui compliquerait l'expérience utilisateur.

### Q: Les informations sont-elles capturées rétroactivement ?

**R:** Non. Seules les **nouvelles demandes** créées après l'application de la migration contiendront ces informations.

---

## Support

Si vous rencontrez des problèmes :
1. Exécuter `php diagnostic.php` pour vérifier l'état du système
2. Consulter [DEPANNAGE.md](DEPANNAGE.md)
3. Vérifier les logs : `journaux/application.log`
