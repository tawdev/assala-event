# Installation de la page Admin

## 1. Créer la base de données

1. Ouvrez **phpMyAdmin** (http://localhost/phpmyadmin)
2. Créez une nouvelle base de données nommée `assala_events`
3. Importez le fichier `database.sql` dans cette base de données

**OU** exécutez directement dans phpMyAdmin :

```sql
CREATE DATABASE IF NOT EXISTS assala_events CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE assala_events;
```

Puis copiez-collez le contenu de `database.sql` dans l'onglet SQL.

## 2. Configurer la connexion

Modifiez `config.php` si nécessaire :
- `DB_HOST` : généralement `localhost`
- `DB_NAME` : `assala_events`
- `DB_USER` : généralement `root` pour XAMPP
- `DB_PASS` : généralement vide `''` pour XAMPP

## 3. Permissions du dossier uploads

Le dossier `uploads/` doit être accessible en écriture. Si vous avez des erreurs d'upload, vérifiez les permissions.

## 4. Accéder à l'admin

Une fois la base de données créée, accédez à :
**http://localhost/assala/admin.php**

## Fonctionnalités

### 📋 Réservations
- Affiche toutes les réservations depuis la table `reservations`
- Colonnes : ID, Nom complet, Téléphone, Email, Type d'événement, Message, Date
- Bouton de suppression pour chaque réservation

### 🖼️ Galerie
- Affiche toutes les images depuis la table `gallery`
- Formulaire d'upload avec validation :
  - Types autorisés : JPG, PNG, WEBP
  - Taille max : 3MB
- Bouton de suppression pour chaque image

### 📁 Catégories
- Affiche toutes les catégories depuis la table `categories`
- Formulaire pour ajouter une nouvelle catégorie
- Bouton de suppression pour chaque catégorie

## Sécurité

- Toutes les requêtes utilisent des **prepared statements** (protection contre SQL injection)
- Validation des types de fichiers uploadés
- Validation de la taille des fichiers
- Noms de fichiers uniques pour éviter les collisions

