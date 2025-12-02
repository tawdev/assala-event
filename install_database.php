<?php
// install_database.php - Script pour créer automatiquement la base de données et les tables

// Configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = ''; // Mot de passe vide par défaut pour XAMPP
$db_name = 'assala_events';

try {
    // Connexion sans spécifier la base de données
    $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Créer la base de données
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Base de données '$db_name' créée avec succès.<br>";
    
    // Utiliser la base de données
    $pdo->exec("USE $db_name");
    
    // Créer la table reservations
    $pdo->exec("CREATE TABLE IF NOT EXISTS reservations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        full_name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        event_type VARCHAR(100) NOT NULL,
        number_of_guests INT DEFAULT 0,
        message TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_event_type (event_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✅ Table 'reservations' créée avec succès.<br>";
    
    // Créer la table gallery
    $pdo->exec("CREATE TABLE IF NOT EXISTS gallery (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_path VARCHAR(500) NOT NULL,
        category_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_created_at (created_at),
        INDEX idx_category_id (category_id),
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✅ Table 'gallery' créée avec succès.<br>";
    
    // Créer la table categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_name (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✅ Table 'categories' créée avec succès.<br>";
    
    // Insérer les catégories par défaut
    $pdo->exec("INSERT INTO categories (name) VALUES
        ('Mariage'),
        ('Fiançailles'),
        ('Khotoba'),
        ('Aqiqah'),
        ('Anniversaire')
    ON DUPLICATE KEY UPDATE name=name");
    echo "✅ Catégories par défaut insérées.<br>";
    
    echo "<br><strong>🎉 Installation terminée avec succès !</strong><br><br>";
    echo "<a href='admin.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#0B2545;color:white;text-decoration:none;border-radius:5px;'>Accéder à l'admin</a> ";
    echo "<a href='index.php' style='display:inline-block;margin-top:15px;padding:10px 20px;background:#D4AF37;color:#0B2545;text-decoration:none;border-radius:5px;'>Retour à l'accueil</a>";
    
} catch (PDOException $e) {
    echo "❌ Erreur : " . $e->getMessage();
}
?>

